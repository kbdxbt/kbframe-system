<?php

namespace Modules\System\Enums\Message;

use Modules\Core\Support\Traits\EnumConcern;

enum ReadStatusEnum: int
{
    use EnumConcern;

    case NO = 0;
    case YES = 1;

    public function map(): string
    {
        return match ($this) {
            self::NO => 0,
            self::YES => 1,
        };
    }
}
