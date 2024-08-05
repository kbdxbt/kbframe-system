<?php

namespace Modules\System\Enums;

use Modules\Core\Support\Traits\EnumConcern;

enum ActionLogEnum: string
{
    use EnumConcern;

    case NOTICE = 'notice';

    public function map(): string
    {
        return match ($this) {
            self::NOTICE => '公告',
        };
    }
}
