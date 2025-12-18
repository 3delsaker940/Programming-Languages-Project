<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationsRequest;
use App\Http\Requests\UpdteaReservationsRequest;
use App\Models\Apartment;
use App\Models\Reservations;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReservationsController extends Controller
{
    public function StoreReservatins(ReservationsRequest $request)
    {
        $DataReservation=$request->validated();
        $StartDate=new Carbon($DataReservation['start_date']);
        $EndDate=new Carbon($DataReservation['end_date']);
        $apartmentReservationId=$DataReservation['apartment_id'];
        $UserId=$request->user()->id;
        return DB::transaction(function() use($StartDate,$EndDate,$apartmentReservationId,$UserId)
        {
            $apartment=Apartment::where('id',$apartmentReservationId)->lockForUpdate()->first();
            if(!$apartment)
            {
                return response()->json(['message'=>'Apartment not found'], 404);
            }
            $this->authorize('rent', $apartment);
            $OverlapCount=Reservations::overlapping($apartmentReservationId,$StartDate,$EndDate)->count();
            if($OverlapCount>0)
            {
                return response()->json(['message'=>'The dates are conflicting with an existing reservation '], 422,);
            }
            $Reservation=Reservations::create([
                'apartment_id'=>$apartmentReservationId,
                'user_id'=>$UserId,
                'start_date'=>$StartDate,
                'end_date'=>$EndDate,
                'status'=>'confirmed'
            ]);
            return response()->json($Reservation, 201);
        });
    }
    public function UpdateReservation(Reservations $reservation, Request $request)
    {
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not allowed'], 403);}
            if ($reservation->status === 'cancelled') {
                return response()->json([
                    'message' => 'Cannot modify a cancelled reservation'
                ], 400);
            }
            if ($reservation->end_date < now()) {
                return response()->json([
                    'message' => 'An expired reservation cannot be modified'
                ], 400);
            }
            if ($reservation->start_date <= now() && $reservation->end_date >= now()) {
                return response()->json([
                    'message' => 'Cannot modify reservation during stay'
                ], 400);
            }
            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date|after_or_equal:today',
                'end_date'   => 'required|date|after:start_date',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            $data = $validator->validated();
            $startAt = Carbon::parse($data['start_date']);
            $endAt = Carbon::parse($data['end_date']);
            $overlap = Reservations::OverlappingExceptReservation($reservation->apartment_id,$startAt,$endAt,$reservation->id)->count();
            if ($overlap > 0) {
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
    public function CancelReservation(Reservations $reservation,ReservationsRequest $request)
    {
        if($reservation->user_id !== $request->user()->id)
        {
            return response()->json(['message'=>'It is not allowed to cancel this reservation'], 403);
        }
           if($reservation->status === 'cancelled')
        {
            return response()->json(['message'=>'Reservation is already cancelled'], 400);
        }
        $cancellationDeadline = $reservation->start_date->subDay();
        if(now()>= $cancellationDeadline){
         return response()->json([
            'message' => 'The reservation must be canceled one day before the start day',
            'last_cancellation_date' => $cancellationDeadline->format('Y-m-d'),
            'checkin_date' => $reservation->start_date->format('Y-m-d'),
            'today' => now()->format('Y-m-d')], 400);
        }
        $reservation->update(['status'=>'cancelled']);
        return response()->json(['message'=>'The reservation has been canceled successfully',
         'cancelled_at' => now()->format('Y-m-d H:i:s')], 200);
    }
    public function myReservations(Request $request)
    {
        $userID = $request->user()->id;
        $statusFilter = $request->query('status');
        $query = Reservations::where('user_id', $userID);
            if ($statusFilter === 'cancelled') {
                    $query->where('status', 'cancelled')
                    ->orderBy('start_date', 'desc');}
            else if ($statusFilter === 'current') {
                    $query->where('status', 'confirmed')
                    ->where('end_date', '>', now())
                    ->orderBy('start_date', 'asc');}
            else if ($statusFilter === 'past') {$query->where('status', 'confirmed')
                    ->where('end_date', '<', now())
                    ->orderBy('start_date', 'desc');
            }
            else {$query->orderBy('start_date', 'asc');}
            $reservations = $query->get()->map(function ($res) {
                        if ($res->status === 'confirmed' && $res->end_date < now())
                             {$res->status = 'past';}
                        return $res;
                    });
                    return response()->json($reservations, 200);
    }
    public function allReservations(Request $request)
    {
        if ($request->user()->type !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $reservations = Reservations::orderBy('start_date', 'asc')->get()->map(function ($res)
        {
            if ($res->status === 'confirmed' && $res->end_date < now()) {
                $res->status = 'past';
            }
            return $res;
        });
        return response()->json($reservations, 200);
    }

}
