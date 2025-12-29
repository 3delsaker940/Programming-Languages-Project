<?php

namespace App\Providers;

use App\Models\Apartment;
use App\Models\Reservations;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Policies\ApartmentPolicy;
use App\Policies\ReservationsPolicy;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        Apartment::class => ApartmentPolicy::class,Reservations::class=>ReservationsPolicy::class
    ];
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
