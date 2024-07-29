<?php

namespace Modules\System\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Repositories\BaseRepository;
use Modules\System\Models\Notice;

class NoticeRepository extends BaseRepository
{
    public function searchable(Builder $query, $params = [])
    {
        if (!empty($params['title'])) {
            $query->whereStartsWith('title', $params['title']);
        } elseif (isset($params['status'])) {
            $query->where('status', $params['status']);
        } elseif (!empty($params['created_at'])) {
            $query->whereBetween('created_at', $params['created_at']);
        }

        $query->orderBy('sort', 'desc');
    }

    public function model(): string
    {
        return Notice::class;
    }
}
