<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Charge::class => \App\Policies\ChargePolicy::class,
        \App\Models\Resolucion::class => \App\Policies\ResolucionPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\NaturalPerson::class => \App\Policies\NaturalPersonPolicy::class,
        \App\Models\LegalEntity::class => \App\Policies\LegalEntityPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
