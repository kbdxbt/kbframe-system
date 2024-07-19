<?php

namespace Modules\System\Enums\Message;

use Modules\Core\Support\Traits\EnumConcern;

enum TypeEnum: int
{
    use EnumConcern;

    case VERIFY_CODE = 1;

    public function map(): string
    {
        return match ($this) {
            self::VERIFY_CODE => '验证码',
        };
    }
}
