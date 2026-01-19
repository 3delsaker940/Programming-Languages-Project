<?php

namespace App\Providers;

use App\Models\Apartment;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Property;
use App\Models\Reservations;
use App\Policies\PropertyPolicy;
use App\Models\User;
use App\Policies\ApartmentPolicy;
use App\Policies\ReservationsPolicy;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        Apartment::class => ApartmentPolicy::class,Reservations::class => ReservationsPolicy::class,
    ];
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
