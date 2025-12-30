<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Reservations extends Model
{
    protected $guarded = [];
    protected $casts = [
        'start_date' => 'date:Y/m/d',
        'end_date' => 'date:Y/m/d',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
    public function scopeOverlapping(Builder $query, $apartmentId, $startAt, $endAt)
    {
        return $query->where('apartment_id', $apartmentId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($sub) use ($startAt, $endAt) {
                $sub->where('start_date', '<', $endAt)
                    ->where('end_date', '>', $startAt);
            });
    }

    public function scopeOverlappingExceptReservation(Builder $query, $apartmentId, $startAt, $endAt, $reservationId)
    {
        return $query->where('apartment_id', $apartmentId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('id', '!=', $reservationId)
            ->where(function ($sub) use ($startAt, $endAt) {
                $sub->where('start_date', '<', $endAt)
                    ->where('end_date', '>', $startAt);
            });
    }
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isfinished()
    {
        return $this->status === 'confirmed' && $this->end_date < now();
    }
    public function isCurrent()
    {
        return $this->start_date <= now() && $this->end_date >= now();
    }
    public function isOverlapping($startAt, $endAt)
    {
        return self::where('apartment_id', $this->apartment_id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('id', '!=', $this->id ?? 0)
            ->where(function ($q) use ($startAt, $endAt) {
                $q->where('start_date', '<', $endAt)
                    ->where('end_date', '>', $startAt);
            })->exists();
    }

    public function cancellationDeadline()
    {
        return $this->start_date->copy()->subDay();
    }
    public function scopeStatus(Builder $query, $status)
    {
        switch ($status) {
            case 'pending':
                $query->where('status', 'pending')->orderBy('start_date', 'asc');
                break;
            case 'confirmed':
                $query->where('status', 'confirmed')->where('end_date', '>=', now())->orderBy('start_date', 'asc');
                break;
            case 'finished':
                $query->where('status', 'confirmed')->where('end_date', '<', now())->orderBy('start_date', 'desc');
                break;
            case 'cancelled':
                $query->where('status', 'cancelled')->orderBy('start_date', 'desc');
                break;
            case 'rejected':
                $query->where('status', 'rejected')->orderBy('start_date', 'desc');
                break;
            default:
                $query->whereRaw('1=0');
        }
        return $query;
    }
    public function scopeForUser(Builder $query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
