<?php

namespace Modules\System\Enums;

use Modules\Core\Support\Traits\EnumConcern;

enum TableTaskSourceEnum: int
{
    use EnumConcern;

    case EXPORT = 1;
    case IMPORT = 2;

    public function map(): string
    {
        return match ($this) {
            self::EXPORT => '导出',
            self::IMPORT => '导入',
        };
    }
}
