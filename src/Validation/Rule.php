<?php

namespace markhuot\attrdeps\Validation;

#[\Attribute(\Attribute::TARGET_PARAMETER|\Attribute::IS_REPEATABLE)]
class Rule implements Validator
{
    public function __construct(
        public string $rule,
    ) { }

    public function toRules(): array
    {
        return [$this->rule];
    }
}
