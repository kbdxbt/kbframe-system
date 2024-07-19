<?php

namespace Modules\System\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Repositories\BaseRepository;
use Modules\System\Models\Message;

class MessageRepository extends BaseRepository
{
    public function searchable(Builder $query, $params)
    {
        if (!empty($params['type'])) {
            $query->where('type', $params['type']);
        } elseif (!empty($params['channel'])) {
            $query->where('channel', $params['channel']);
        } elseif (isset($params['status'])) {
            $query->where('status', $params['status']);
        } elseif (isset($params['read_status'])) {
            $query->where('read_status', $params['read_status']);
        } elseif (!empty($params['created_at'])) {
            $query->whereBetween('created_at', $params['created_at']);
        } elseif (!empty($params['readed_at'])) {
            $query->whereBetween('readed_at', $params['readed_at']);
        }

        $query->orderBy('priority', 'desc');
    }

    public function model(): string
    {
        return Message::class;
    }
}
