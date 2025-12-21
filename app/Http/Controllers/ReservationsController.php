<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationsRequest;
use App\Models\Apartment;
use App\Models\Reservations;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationsController extends Controller
{
    //=========================Store====================================================
    public function storeReservations(ReservationsRequest $request)
    {
        $data = $request->validated();
        $startDate = new Carbon($data['start_date']);
        $endDate = new Carbon($data['end_date']);
        $apartmentId = $data['apartment_id'];
        $userId = $request->user()->id;
        return DB::transaction(function () use ($startDate, $endDate, $apartmentId, $userId) {
            $apartment = Apartment::where('id', $apartmentId)->lockForUpdate()->first();
            if (!$apartment) {
                return response()->json(['message' => 'Apartment not found'], 404);
            }
            $this->authorize('rent', $apartment);
            $reservation = new Reservations([
                'apartment_id' => $apartmentId,
                'user_id' => $userId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'pending'
            ]);
            if ($reservation->isOverlapping($startDate, $endDate)) {
                return response()->json(['message' => 'The dates are conflicting with an existing reservation'], 422);
            }
            $reservation->save();
            return response()->json($reservation, 201);
        });
    }
    //=============================Update=======================================================

    public function updateReservation(Reservations $reservation, Request $request)
    {
        $this->authorize('update', $reservation);
        if ($reservation->isCancelled()) {
            return response()->json(['message' => 'the Reservation is canceled'], 400);
        }
        if ($reservation->isFinished()) {
            return response()->json(['message' => 'An expired reservation cannot be modified'], 400);
        }
        if ($reservation->isCurrent()) {
            return response()->json(['message' => 'No , it is possible to modify a reservation that started'], 400);
        }
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);
        $startAt = Carbon::parse($request->start_date);
        $endAt = Carbon::parse($request->end_date);
        if ($reservation->isOverlapping($startAt, $endAt)) {
            return response()->json(['message' => 'Dates are conflicting'], 422);
        }
        $reservation->update([
            'start_date' => $startAt,
            'end_date' => $endAt
        ]);
        return response()->json([
            'message' => 'Reservation updated successfully',
            'reservation' => $reservation->fresh()
        ], 200);
    }
    //===================================Cancel==============================================
    public function cancelReservation(Reservations $reservation)
    {
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

        if ($statusFilter) {
            $query = Reservations::forUser($userId);

            if ($statusFilter === 'finished') {
                $data = $query->status('finished')->get()
                    ->map(function ($reservation) {

                        $reservation->display_status = 'finished';
                        return $reservation;
                    });
            } else {
                $data = $query->status($statusFilter)->get();
            }

            return response()->json([
                'count' => $data->count(),
                'data' => $data
            ], 200);
        }
        return response()->json([
            'pending' => Reservations::forUser($userId)
                ->status('pending')
                ->get(),

            'confirmed' => Reservations::forUser($userId)
                ->status('confirmed')
                ->get(),

            'finished' => Reservations::forUser($userId)
                ->status('finished')
                ->get()
                ->map(function ($reservation) {
                    $reservation->display_status = 'finished';
                    return $reservation;
                }),

            'cancelled' => Reservations::forUser($userId)
                ->status('cancelled')
                ->get(),

            'rejected' => Reservations::forUser($userId)
                ->status('rejected')
                ->get(),
        ], 200);
    }
    //===========================================all reservaions in app=================================
    public function allReservations(Request $request)
    {
        $reservations = Reservations::orderBy('start_date', 'asc')->get()->map(function ($res) {
            if ($res->status === 'confirmed' && $res->end_date < now()) {
                $res->status = 'finished';
            }
            return $res;
        });
        return response()->json($reservations, 200);
    }
    //===================================accept the reservaiont before owner===================================
    public function approveReservation(Reservations $reservation, Request $request)
    {
        $this->authorize('approve', $reservation);
        if ($reservation->status !== 'pending') {
            return response()->json(['message' => 'the reservations is not pending'], 400);
        }
        $data = $request->validate([
            'action' => 'required|in:approve,reject'
        ]);
        if ($data['action'] === 'reject') {
            $reservation->update(['status' => 'rejected']);
            return response()->json([
                'message' => 'Reservation rejected successfully',
                'reservation' => $reservation
            ], 200);
        }
        if ($reservation->isOverlapping($reservation->start_date, $reservation->end_date)) {
            return response()->json(['message' => 'Dates are conflicting'], 422);
        }
        $reservation->update(['status' => 'confirmed']);
        return response()->json([
            'message' => 'Reservation approved successfully',
            'reservation' => $reservation
        ], 200);
    }
}
