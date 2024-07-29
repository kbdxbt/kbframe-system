<?php

namespace Modules\System\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\System\Models\HttpLog;

class HttpLogRepository extends BaseRepository
{
    public function model(): string
    {
        return HttpLog::class;
    }
}
