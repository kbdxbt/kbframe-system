<?php

namespace Modules\System\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\System\Models\Task;

class TaskRepository extends BaseRepository
{

    public function model(): string
    {
        return Task::class;
    }
}
