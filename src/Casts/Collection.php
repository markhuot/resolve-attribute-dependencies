<?php

namespace markhuot\attrdeps\Casts;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Collection implements Cast
{
    public function __invoke(mixed $items)
    {
        return collect($items);
    }
}
