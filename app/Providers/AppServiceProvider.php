<?php

namespace App\Providers;

use App\Models\Charge;
use App\Models\LegalEntity;
use App\Models\NaturalPerson;
use App\Models\Resolucion;
use App\Models\Setting;
use App\Models\Signature;
use App\Models\User;
use App\Observers\ActivityObserver;
use App\Services\Charge\ChargeService;
use App\Services\Charge\Contracts\ChargeServiceInterface;
use App\Services\Resolucion\Contracts\ResolucionServiceInterface;
use App\Services\Resolucion\ResolucionService;
use App\Services\User\Contracts\UserServiceInterface;
use App\Services\User\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(ResolucionServiceInterface::class, ResolucionService::class);
        $this->app->bind(ChargeServiceInterface::class, ChargeService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Charge::observe(ActivityObserver::class);
        NaturalPerson::observe(ActivityObserver::class);
        Resolucion::observe(ActivityObserver::class);
        LegalEntity::observe(ActivityObserver::class);
        Setting::observe(ActivityObserver::class);
        User::observe(ActivityObserver::class);
        Signature::observe(ActivityObserver::class);
    }
}
