<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationsRequest;
use App\Models\Apartment;
use App\Models\Reservations;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationsController extends Controller
{
    public function StoreReservatins(ReservationsRequest $request)
    {
        $DateReservation=$request->validated();
        $StartDate=new Carbon($DateReservation['start_date']);
        $EndDate=new Carbon($DateReservation['end_date']);
        $apartmentReservationId=$DateReservation['apartment_id'];
        $UserId=$request->user()->id;
        return DB::transaction(function() use($StartDate,$EndDate,$apartmentReservationId,$UserId)
        {
            $apartment=Apartment::where('id',$apartmentReservationId)->lockForUpdate()->first();
            if(!$apartment)
            {
                return response()->json(['message'=>'Apartment not found'], 404);
            }
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
}
