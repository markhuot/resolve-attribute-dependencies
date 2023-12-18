<?php

namespace markhuot\attrdeps\Resolvers;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class FromRequest
{
    public function __construct(
        public ?string $key=null,
    ) { }
}
