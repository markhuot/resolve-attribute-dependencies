<?php

namespace markhuot\attrdeps;

class CallableDispatcher extends \Illuminate\Routing\CallableDispatcher
{
    use ResolvesRouteDependencies;
}
