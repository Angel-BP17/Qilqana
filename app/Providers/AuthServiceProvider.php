<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Charge;
use App\Models\LegalEntity;
use App\Models\NaturalPerson;
use App\Models\Resolucion;
use App\Models\User;
use App\Policies\ChargePolicy;
use App\Policies\LegalEntityPolicy;
use App\Policies\NaturalPersonPolicy;
use App\Policies\ResolucionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Charge::class => ChargePolicy::class,
        Resolucion::class => ResolucionPolicy::class,
        User::class => UserPolicy::class,
        NaturalPerson::class => NaturalPersonPolicy::class,
        LegalEntity::class => LegalEntityPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
