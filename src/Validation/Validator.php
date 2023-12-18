<?php

namespace markhuot\attrdeps\Validation;

interface Validator
{
    /** @return array<string> */
    public function toRules(): array;
}
