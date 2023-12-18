<?php

namespace markhuot\attrdeps;

class ControllerDispatcher extends \Illuminate\Routing\ControllerDispatcher
{
    use ResolvesRouteDependencies;
}
