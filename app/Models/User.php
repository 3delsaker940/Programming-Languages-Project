<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birthdate' => 'date:Y/m/d',
            'password' => 'hashed',
        ];
    }

    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'owner_id', 'id');
    }
    public function Reservation()
    {
        return $this->hasMany(Reservations::class);
    }
    public function favoritesApartment()
    {
        return $this->belongsToMany(Apartment::class, 'favorites');
    }

    //==============for delete user + his file ==============
    // protected static function boot()
    //     {
    //             parent::boot();

    //             static::deleting(function ($user) {

    //             $folder = "apartments/{$user->id}";

    //             if (\Storage::disk('public')->exists($folder)) {
    //                 \Storage::disk('public')->deleteDirectory($folder);
    //             }

    //         });
    //     }
    //========================================================

}
