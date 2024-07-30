<?php

namespace Modules\System\Enums;

use Modules\Core\Support\Traits\EnumConcern;

enum ConfigGroupEnum: int
{
    use EnumConcern;

    case BASE = 10;
    case SYSTEM = 20;
    case EXTEND = 30;

    public function map(): string
    {
        return match ($this) {
            self::BASE => '基本',
            self::SYSTEM => '系统',
            self::EXTEND => '扩展',
        };
    }
}
