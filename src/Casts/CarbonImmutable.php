<?php

namespace markhuot\attrdeps\Casts;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class CarbonImmutable extends Carbon
{
    protected string $classString = \Carbon\CarbonImmutable::class;
}
