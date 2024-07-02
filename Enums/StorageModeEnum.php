<?php

namespace Modules\System\Enums;

use Modules\Core\Support\Traits\EnumConcern;

enum StorageModeEnum: string
{
    use EnumConcern;

    case LOCAL = 'local';
    case OSS = 'oss';
    case QINIU = 'qiniu';
    case COS = 'cos';
    case FTP = 'ftp';
    case MEMORY = 'memory';
    case S3 = 's3';
    case MINIO = 'minio';

    public function map(): string
    {
        return match ($this) {
            self::LOCAL => 'local',
            self::OSS => 'oss',
            self::COS => 'cos',
            self::FTP => 'ftp',
            self::MEMORY => 'memory',
            self::S3 => 's3',
            self::MINIO => 'minio',
        };
    }
}
