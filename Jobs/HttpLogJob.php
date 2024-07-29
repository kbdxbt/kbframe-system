<?php

declare(strict_types=1);

namespace Modules\System\Jobs;

use Modules\Core\Jobs\BaseJob;
use Modules\System\Services\HttpLogService;

class HttpLogJob extends BaseJob
{
    protected $driver;

    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct($driver, $data)
    {
        $this->driver = $driver;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        app(HttpLogService::class)->dealQueue($this->driver, $this->data);
    }
}
