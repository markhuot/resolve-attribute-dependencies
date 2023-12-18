<?php

namespace markhuot\attrdeps\Casts;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Carbon implements Cast
{
    protected string $classString = \Carbon\Carbon::class;

    public function __construct(
        protected ?string $format = null,
        protected ?string $tz = null,
    ) {}

    public function __invoke(mixed $value)
    {
        if ($value === null) {
            return null;
        }

        if ($this->format) {
            return $this->classString::createFromFormat($this->format, $value, $this->tz);
        }

        return new $this->classString($value, $this->tz);
    }
}
