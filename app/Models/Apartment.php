<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;

class Apartment extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function images()
    {
        return $this->hasMany(ApartmentImages::class);
    }
    public function Reservations()
    {
        return $this->hasMany(Reservations::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
    public function ratings()
    {
        return $this->belongsToMany(User::class, 'ratings');
    }
}
