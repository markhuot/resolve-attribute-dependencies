<?php

namespace markhuot\attrdeps\Validation;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class BaseRule implements Validator
{
    protected array $args;

    public function __construct(...$args) {
        $this->args = $args;
    }

    public function toRules(): array
    {
        $reflect = new \ReflectionClass($this);
        $rule = strtolower($reflect->getShortName());

        if (! empty($this->args)) {
            $rule .= ':' . implode(',', $this->args);
        }

        return [$rule];
    }
}
