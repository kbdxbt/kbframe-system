<?php

namespace Modules\System\Services;

use Modules\Core\Services\BaseService;
use Modules\Core\Support\Traits\ActionServiceTrait;
use Modules\System\Enums\QueueEnum;
use Modules\System\Jobs\HttpLogJob;
use Modules\System\Repositories\HttpLogRepository;

class HttpLogService extends BaseService
{
    use ActionServiceTrait;

    protected $repository;

    public function __construct(HttpLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveHttpLog($driver, $data): void
    {
        dispatch((new HttpLogJob($driver, $data))->onQueue(QueueEnum::SYSTEM_HTTP_LOG_JOB->value));
    }

    public function dealQueue($driver, $data): void
    {
        if ($driver === 'mysql') {
            HttpLogRepository::make()->create($data);
        } else {
            write_log('http_log', $data);
        }
    }

    protected function formatList($data)
    {
        foreach ($data['data'] as &$v) {
            $v['request_params'] = json_decode($v['request_params'], true);
            $v['request_header'] = json_decode($v['request_header'], true);
            $v['response_header'] = json_decode($v['response_header'], true);
            $v['response_body'] = json_decode($v['response_body'], true);
        }

        return $data;
    }
}
