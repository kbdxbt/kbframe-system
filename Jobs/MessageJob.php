<?php

declare(strict_types=1);

namespace Modules\System\Jobs;

use Modules\Core\Jobs\BaseJob;
use Modules\System\Services\MessageService;

class MessageJob extends BaseJob
{
    protected $messageId;

    /**
     * @param $params
     * Create a new job instance.
     */
    public function __construct($messageId)
    {
        $this->messageId = $messageId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        app(MessageService::class)->dealQueue($this->messageId);
    }
}
