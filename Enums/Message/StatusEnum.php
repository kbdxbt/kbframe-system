<?php

namespace Modules\System\Enums\Message;

use Modules\Core\Support\Traits\EnumConcern;

enum StatusEnum: int
{
    use EnumConcern;

    case NO = 0;
    case YES = 1;
    case FAIL = 2;

    public function map(): string
    {
        return match ($this) {
            self::NO => 0,
            self::YES => 1,
            self::FAIL => 1,
        };
    }
}
