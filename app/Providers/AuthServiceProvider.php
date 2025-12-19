<?php

namespace App\Providers;

use App\Models\Apartment;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Policies\ApartmentPolicy;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        Apartment::class => ApartmentPolicy::class,
    ];
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
