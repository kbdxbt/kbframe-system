<?php

namespace Modules\System\Enums;

use Modules\Core\Support\Traits\EnumConcern;

enum QueueEnum: string
{
    use EnumConcern;

    case TABLE_TASK_JOB = 'table_task_job';

    public function map(): string
    {
        return match ($this) {
            self::TABLE_TASK_JOB => '数据表任务',
        };
    }
}
