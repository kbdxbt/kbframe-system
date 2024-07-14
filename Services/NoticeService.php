<?php

namespace Modules\System\Services;

use Illuminate\Support\Arr;
use Modules\Core\Services\BaseService;
use Modules\Core\Support\Traits\ActionServiceTrait;
use Modules\System\Repositories\NoticeRepository;

class NoticeService extends BaseService
{
    use ActionServiceTrait;

    protected $repository;

    public function __construct(NoticeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveData($params): void
    {
        $params['created_by'] = $params['created_by'] ?? request()->userId();

        $this->repository->updateOrInsert([
            'id' => $params['id'] ?? 0
        ], Arr::only($params, [
            'category', 'tags', 'title', 'content', 'sort', 'start_date', 'end_date',
            'created_by', 'updated_by'
        ]));
    }
}
