<?php

namespace Modules\System\Enums\Message;

use Modules\Core\Support\Traits\EnumConcern;

enum ChannelEnum: string
{
    use EnumConcern;

    case LOCAL = 'local';
    case MAIL = 'mail';
    case SMS = 'sms';
    case DINGTALK = 'dingTalk';

    public function map(): string
    {
        return match ($this) {
            self::LOCAL => 'local',
            self::MAIL => 'mail',
            self::SMS => 'sms',
            self::DINGTALK => 'dingTalk',
        };
    }
}
