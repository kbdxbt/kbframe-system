<?php

namespace Modules\System\Enums;

use Modules\Core\Support\Traits\EnumConcern;

enum TableTaskStatusEnum: int
{
    use EnumConcern;

    case PENDING = 0;
    case EXEXUTING = 1;
    case SUCCESS = 2;
    case FAILED = 3;
    case CANCELLED = 4;

    public function map(): string
    {
        return match ($this) {
            self::PENDING => '待处理',
            self::EXEXUTING => '执行中',
            self::SUCCESS => '执行成功',
            self::FAILED => '执行失败',
            self::CANCELLED => '已取消',
        };
    }
}
