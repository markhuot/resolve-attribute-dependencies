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
    }
}
