<?php

namespace Modules\System\Services;

use Illuminate\Support\Arr;
use Modules\Core\Services\BaseService;
use Modules\Core\Support\Traits\ActionServiceTrait;
use Modules\System\Repositories\NoticeRepository;

class NoticeService extends BaseService
{
    use ActionServiceTrait {
        ActionServiceTrait::saveData as parentSaveData;
    }

    protected $repository;

    public function __construct(NoticeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveData($params): void
    {
        $this->parentSaveData(Arr::only($params, [
            'category', 'tags', 'title', 'content', 'sort', 'start_date', 'end_date'
        ]));
    }
}
