<?php

namespace Modules\System\Services;

use Illuminate\Support\Arr;
use Modules\Core\Exceptions\BadRequestException;
use Modules\Core\Services\BaseService;
use Modules\System\Repositories\NoticeRepository;

class NoticeService extends BaseService
{
    protected NoticeRepository $repository;

    public function __construct(NoticeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getList($params): array
    {
        $query = $this->repository->query();

        $this->repository->searchable($query, $params);

        return $query->pageList($params)->toArray();
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

    public function getDetail($id): array
    {
        $data = $this->repository->query()->find($id)?->toArray();
        if (!$data) {
            throw new BadRequestException('没有找到符合条件的记录');
        }

        return $data;
    }

    public function deleteData($params): void
    {
        $this->repository->delete($params['ids'], $params['is_force'] ?? false);
    }
}
