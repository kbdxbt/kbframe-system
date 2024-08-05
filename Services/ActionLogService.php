<?php

namespace Modules\System\Services;

use Modules\Core\Services\BaseService;
use Modules\Core\Support\Traits\ActionServiceTrait;
use Modules\System\Enums\QueueEnum;
use Modules\System\Jobs\ActionLogJob;
use Modules\System\Repositories\ActionLogRepository;

class ActionLogService extends BaseService
{
    use ActionServiceTrait;

    protected $repository;

    public function __construct(ActionLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveData($params): void
    {
        $params['log_id'] = app('request_id');

        dispatch((new ActionLogJob($params))->onQueue(QueueEnum::SYSTEM_ACTION_LOG_JOB->value));
    }

    public function dealQueue($data): void
    {
        ActionLogRepository::make()->create($data);
    }
}
