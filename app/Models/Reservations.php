<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder;

class Reservations extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function apartmint()
    {
        return $this->belongsTo(Apartment::class);
    }
       public function scopeOverlapping(Builder $query, $apartmentId, $startAt, $endAt)
    {
        return $query->where('apartment_id', $apartmentId)
                 ->where('status', 'confirmed')
                 ->where(function($sub) use ($startAt, $endAt) {
                     $sub->where('start_date', '<', $endAt)
                         ->where('end_date', '>', $startAt);
                 });
    }
}
