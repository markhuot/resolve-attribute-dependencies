<?php

namespace markhuot\attrdeps;

use Illuminate\Routing\Contracts\CallableDispatcher;
use Illuminate\Routing\Contracts\ControllerDispatcher;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        app()->bind(CallableDispatcher::class, \markhuot\attrdeps\CallableDispatcher::class);
        app()->bind(ControllerDispatcher::class, \markhuot\attrdeps\ControllerDispatcher::class);
        app()->singleton(CastManager::class, fn () => new CastManager());
    }

    public function boot(): void
    {
        app(CastManager::class)->register(\Illuminate\Support\Collection::class, \markhuot\attrdeps\Casts\Collection::class);
        app(CastManager::class)->register(\DateTimeInterface::class, \markhuot\attrdeps\Casts\CarbonImmutable::class);
        app(CastManager::class)->register(\Carbon\CarbonImmutable::class, \markhuot\attrdeps\Casts\CarbonImmutable::class);
        app(CastManager::class)->register(\Carbon\Carbon::class, \markhuot\attrdeps\Casts\Carbon::class);
    }
}
