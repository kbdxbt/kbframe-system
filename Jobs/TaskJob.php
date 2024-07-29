<?php

declare(strict_types=1);

namespace Modules\System\Jobs;

use Modules\Core\Jobs\BaseJob;
use Modules\System\Services\TaskService;

class TaskJob extends BaseJob
{
    /** 参数 */
    protected $params;

    /**
     * Create a new job instance.
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        app(TaskService::class)->dealQueue($this->params);
    }
}
