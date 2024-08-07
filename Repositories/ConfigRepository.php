<?php

namespace Modules\System\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Repositories\BaseRepository;
use Modules\System\Models\Config;

class ConfigRepository extends BaseRepository
{
    public function searchFields(): array
    {
        return [
            'title'       => '配置标题',
            'name'        => '配置名称',
            'type'        => '配置类型',
            'tip'         => '配置说明',
            'sort'        => '排序',
            'status_text' => '状态',
        ];
    }

    public function searchable(Builder $query, $params = [])
    {
        if (!empty($params['title'])) {
            $query->where('title', $params['title']);
        } elseif (!empty($params['name'])) {
            $query->where('name', $params['name']);
        } elseif (!empty($params['group'])) {
            $query->where('group', $params['group']);
        } elseif (isset($params['status'])) {
            $query->where('status', $params['status']);
        } elseif (!empty($params['created_at'])) {
            $query->whereBetween('created_at', $params['created_at']);
        }

        $query->orderBy('sort', 'desc');
    }

    public function model(): string
    {
        return Config::class;
    }
}
