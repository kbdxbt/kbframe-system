<?php

namespace Modules\System\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\System\Models\TableTask;

class TableTaskRepository extends BaseRepository
{

    public function model(): string
    {
        return TableTask::class;
    }
}
