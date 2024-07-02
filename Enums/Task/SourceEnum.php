<?php

namespace Modules\System\Enums\Task;

use Modules\Core\Support\Traits\EnumConcern;
use Modules\PlatformGateway\Services\PlatformAccountService;

enum SourceEnum: string
{
    use EnumConcern;

    case ACCOUNT_EXPORT = 'account_export';
    case ACCOUNT_IMPORT = 'account_import';

    public function map(): string
    {
        return match ($this) {
            self::ACCOUNT_EXPORT => '导出账号列表',
            self::ACCOUNT_IMPORT => '导入账号列表',
        };
    }

    public function callback(): array
    {
        return match ($this) {
            self::ACCOUNT_EXPORT => [app(PlatformAccountService::class), 'export'],
            self::ACCOUNT_IMPORT => [app(PlatformAccountService::class), 'import'],
        };
    }

    public function importTemplate(): array
    {
        return match ($this) {
            self::ACCOUNT_IMPORT => [app(PlatformAccountService::class), 'importTemplate'],
        };
    }
}
