<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationsRequest;
use App\Models\Apartment;
use App\Models\Reservations;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\NewReservationNotification;
use Illuminate\Auth\Middleware\Authorize;

class ReservationsController extends Controller
{
    //=========================Store====================================================
    public function storeReservations(ReservationsRequest $request)
    {
        $data = $request->validated();
        $startDate = Carbon::parse($data['start_date']);
        $endDate   = Carbon::parse($data['end_date']);
        $apartmentId = $data['apartment_id'];
        $user = $request->user();
        $reservation = DB::transaction(function () use ($startDate, $endDate, $apartmentId, $user) {
            $apartment = Apartment::where('id', $apartmentId)
                ->lockForUpdate()
                ->first();
            if (!$apartment) {
                abort(404, 'Apartment not found');
            }
            $this->authorize('rent',$apartment);
            $overlapping = Reservations::overlapping(
                $apartmentId,
                $startDate,
                $endDate
            )->lockForUpdate()->exists();
            if ($overlapping) {
                abort(422, 'The dates are conflicting with an existing reservation');
            }

            $this->authorize('rent', $apartment);
            $days = $startDate->diffInDays($endDate);
            $total_price = $days * $apartment->price;
            $reservation = new Reservations([
                'apartment_id' => $apartmentId,
                'user_id' => $userId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_price' => $total_price,
                'status' => 'pending'
            ]);
            if ($reservation->isOverlapping($startDate, $endDate)) {
                return response()->json(['message' => 'The dates are conflicting with an existing reservation'], 422);
            }
            $reservation->save();
            return response()->json([
                'message' => "Booked Successfully",
                'Total Cost' => $total_price
            ], 201);
        });
        $owner = $reservation->apartment->user;

        $notification = new NewReservationNotification(
            $reservation->id,
            $user->name
        );
        $owner->notify($notification);
        $notification->sendFCM($owner);
        return response()->json([
            'message' => 'Booked Successfully',
            'reservation_id' => $reservation->id
        ], 201);
    }

    //=============================Update=======================================================

    public function updateReservation(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'reservation_id' => 'required|exists:reservations,id|integer'
        ]);
        $reservation = Reservations::findOrFail($request->reservation_id);
        $this->authorize('update', $reservation);

        if ($reservation->isCancelled()) {
            return response()->json(['message' => 'the Reservation is canceled'], 400);
        }
        if ($reservation->isfinished()) {
            return response()->json(['message' => 'An expired reservation cannot be modified'], 400);
        }
        if ($reservation->isCurrent()) {
            return response()->json(['message' => 'No , it is possible to modify a reservation that started'], 400);
        }
        $request->validate([
            'start_date' => 'required|date|after:yesterday',
            'end_date' => 'required|date|after:start_date',
        ]);
        $startAt = Carbon::parse($request->start_date);
        $endAt = Carbon::parse($request->end_date);
        $days = $startAt->diffInDays($endAt);
        $daily_price = $reservation->apartment->price;
        $total_price = $days * $daily_price;
        if ($reservation->isOverlapping($startAt, $endAt)) {
            return response()->json(['message' => 'Dates are conflicting'], 422);
        }
        $reservation->apartment->user
        ->notifications()
        ->where('data->reservation_id', $reservation->id)
        ->delete();
        $reservation->update([
            'start_date' => $startAt,
            'end_date' => $endAt,
            'total_price' => $total_price
        ]);

        return response()->json([
            'message' => 'The modification of the reservation has been accepted and wation for the lessors approval',
            'reservation' => $reservation->fresh()
        ], 200);
    }
    //===================================Cancel==============================================
    public function cancelReservation(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id'
        ]);
        $reservation = Reservations::findOrFail($request->reservation_id);
        $this->authorize('cancel', $reservation);
        if ($reservation->isCancelled()) {
            return response()->json(['message' => 'Reservation is already cancelled'], 200);
        }
        if (now() > $reservation->cancellationDeadline()) {
            return response()->json(['message' => 'the limited cancellation period has expired'], 400);
        }
        $reservation->update(['status' => 'cancelled']);
        return response()->json([
            'message' => 'The reservation has been canceled successfully',
            'cancelled_at' => now()->format('Y-m-d H:i:s')
        ], 200);
    }
    //=================================Reservations user=================================================
    public function myReservations(Request $request)
    {
        $userId = $request->user()->id;
        $statusFilter = $request->query('status');

        $query = Reservations::with(['apartment.images'])->forUser($userId);

        if ($statusFilter) {
            // $query = Reservations::forUser($userId);

            if ($statusFilter === 'finished') {
                $data = $query->status('finished')->get()
                    ->map(function ($reservation) {

                        $reservation->display_status = 'finished';

                        $reservation->apartment_details = [
                            'name' => $reservation->apartment->title ?? null,
                            'price' => $reservation->apartment->price ?? null,
                            'city' => $reservation->apartment->city ?? null,
                            'first_image' => $reservation->apartment->images->first()
                                ? asset('storage/' . $reservation->apartment->images->first()->apartment_image_path)
                                : null
                        ];
                        return $reservation;
                    });
            } else {
                $data = $query->status($statusFilter)->get()->map(function ($reservation) {
                    $reservation->apartment_details = [
                        'title' => $reservation->apartment->title ?? null,
                        'price' => $reservation->apartment->price ?? null,
                        'city' => $reservation->apartment->city ?? null,
                        'first_image' => $reservation->apartment->images->first()
                            ? asset('storage/' . $reservation->apartment->images->first()->apartment_image_path)
                            : null
                    ];
                    return $reservation;
                });
            }

            return response()->json([
                'count' => $data->count(),
                'data' => $data
            ], 200);
        }

        return response()->json([
            'pending' => Reservations::with(['apartment.images'])
                ->forUser($userId)
                ->status('pending')
                ->get()
                ->map(function ($reservation) {
                    $reservation->apartment_details = [
                        'title' => $reservation->apartment->title ?? null,
                        'price' => $reservation->apartment->price ?? null,
                        'city' => $reservation->apartment->city ?? null,
                        'first_image' => $reservation->apartment->images->first()
                            ? asset('storage/' . $reservation->apartment->images->first()->apartment_image_path)
                            : null
                    ];
                    unset($reservation->apartment);
                    return $reservation;
                }),

            'confirmed' => Reservations::with(['apartment.images'])
                ->forUser($userId)
                ->status('confirmed')
                ->get()
                ->map(function ($reservation) {
                    $reservation->apartment_details = [
                        'title' => $reservation->apartment->title ?? null,
                        'price' => $reservation->apartment->price ?? null,
                        'city' => $reservation->apartment->city ?? null,
                        'first_image' => $reservation->apartment->images->first()
                            ? asset('storage/' . $reservation->apartment->images->first()->apartment_image_path)
                            : null
                    ];
                    unset($reservation->apartment);

                    return $reservation;
                }),

            'finished' => Reservations::with(['apartment.images'])
                ->forUser($userId)
                ->status('finished')
                ->get()
                ->map(function ($reservation) {
                    $reservation->display_status = 'finished';
                    $reservation->apartment_details = [
                        'title' => $reservation->apartment->title ?? null,
                        'price' => $reservation->apartment->price ?? null,
                        'city' => $reservation->apartment->city ?? null,
                        'first_image' => $reservation->apartment->images->first()
                            ? asset('storage/' . $reservation->apartment->images->first()->apartment_image_path)
                            : null
                    ];
                    unset($reservation->apartment);

                    return $reservation;
                }),

            'cancelled' => Reservations::with(['apartment.images'])
                ->forUser($userId)
                ->status('cancelled')
                ->get()
                ->map(function ($reservation) {
                    $reservation->apartment_details = [
                        'title' => $reservation->apartment->title ?? null,
                        'price' => $reservation->apartment->price ?? null,
                        'city' => $reservation->apartment->city ?? null,
                        'first_image' => $reservation->apartment->images->first()
                            ? asset('storage/' . $reservation->apartment->images->first()->apartment_image_path)
                            : null
                    ];
                    unset($reservation->apartment);

                    return $reservation;
                }),

            'rejected' => Reservations::with(['apartment.images'])
                ->forUser($userId)
                ->status('rejected')
                ->get()
                ->map(function ($reservation) {
                    $reservation->apartment_details = [
                        'title' => $reservation->apartment->title ?? null,
                        'price' => $reservation->apartment->price ?? null,
                        'city' => $reservation->apartment->city ?? null,
                        'first_image' => $reservation->apartment->images->first()
                            ? asset('storage/' . $reservation->apartment->images->first()->apartment_image_path)
                            : null
                    ];
                    unset($reservation->apartment);

                    return $reservation;
                }),
        ], 200);
    }
    //===========================================all reservaions in app=================================
    public function allReservations()
    {
        $reservations = Reservations::orderBy('start_date', 'asc')->get()->map(function ($res) {
            if ($res->status === 'confirmed' && $res->end_date < now()) {
                $res->status = 'finished';
            }
            return $res;
        });
        $ans = $reservations->paginate(8);
        return response()->json($ans, 200);
    }
    //===================================approved by owner===================================
    public function approveReservation(Reservations $reservation, Request $request)
    {
        $this->authorize('approve',$reservation);
        if ($reservation->status !== 'pending') {
            return response()->json(['message' => 'the reservations is not pending'], 400);
        }
        $data = $request->validate([
            'action' => 'required|in:approve,reject'
        ]);
        if ($data['action'] === 'reject')
        {
            $reservation->update(['status' => 'rejected']);
            return response()->json([
                'message' => 'Reservation rejected successfully',
                'reservation' => $reservation
            ], 200);
        }
        $overlapping = Reservations::overlappingExceptReservation(
            $reservation->apartment_id,
            $reservation->start_date,
            $reservation->end_date,
            $reservation->id
        )->exists();
        if ($overlapping)
        {
            return response()->json(['message' => 'Dates are conflicting'], 422);
        }
        $reservation->update(['status' => 'confirmed']);
        return response()->json([
            'message' => 'Reservation approved successfully',
            'reservation' => $reservation
        ], 200);
    }
}
