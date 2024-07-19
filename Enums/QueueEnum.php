<?php

namespace Modules\System\Enums;

use Modules\Core\Support\Traits\EnumConcern;

enum QueueEnum: string
{
    use EnumConcern;

    case SYSTEM_TASK_JOB = 'system_task_job';
    case SYSTEM_MESSAGE_JOB = 'system_message_job';

    public function map(): string
    {
        return match ($this) {
            self::SYSTEM_TASK_JOB => '系统任务队列',
            self::SYSTEM_MESSAGE_JOB => '系统消息队列',
        };
    }
}
