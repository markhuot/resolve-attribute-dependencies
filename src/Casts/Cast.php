<?php

namespace markhuot\attrdeps\Casts;

interface Cast
{
    public function __invoke(mixed $value);
}
