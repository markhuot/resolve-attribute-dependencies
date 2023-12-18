<?php

namespace markhuot\attrdeps;

class CastManager
{
    protected $casts = [];

    public function register(string $concrete, string $caster)
    {
        $this->casts[$concrete] = $caster;
    }

    public function getCastFor(string $concrete)
    {
        return $this->casts[$concrete] ?? null;
    }
}
