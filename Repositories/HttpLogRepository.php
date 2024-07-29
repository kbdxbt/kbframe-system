<?php

namespace Modules\System\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Repositories\BaseRepository;
use Modules\System\Models\HttpLog;

class HttpLogRepository extends BaseRepository
{
    public function searchable(Builder $query, $params = [])
    {
        if (!empty($params['ip'])) {
            $query->where('ip', $params['ip']);
        } elseif (!empty($params['url'])) {
            $query->whereStartsWith('url', $params['url']);
        } elseif (!empty($params['method'])) {
            $query->where('method', $params['method']);
        }  elseif (!empty($params['request_id'])) {
            $query->where('request_id', $params['request_id']);
        } elseif (!empty($params['request_params'])) {
            $query->whereLike('request_params', $params['request_params']);
        } elseif (!empty($params['request_header'])) {
            $query->whereLike('request_header', $params['request_header']);
        } elseif (!empty($params['response_code'])) {
            $query->where('response_code', $params['response_code']);
        } elseif (!empty($params['response_body'])) {
            $query->whereLike('response_body', $params['response_body']);
        } elseif (!empty($params['request_time'])) {
            $query->whereBetween('request_time', $params['request_time']);
        } elseif (!empty($params['duration'])) {
            $query->whereBetween('duration', $params['duration']);
        }

        $query->orderBy('created_at', 'desc');
    }

    public function model(): string
    {
        return HttpLog::class;
    }
}
