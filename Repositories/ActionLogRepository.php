<?php

namespace Modules\System\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Repositories\BaseRepository;
use Modules\System\Models\ActionLog;

class ActionLogRepository extends BaseRepository
{
    public function searchable(Builder $query, $params = [])
    {
        if (!empty($params['log_id'])) {
            $query->where('log_id', $params['log_id']);
        } elseif (!empty($params['model_id'])) {
            $query->where('model_id', $params['model_id']);
        } elseif (!empty($params['key'])) {
            $query->where('key', $params['key']);
        } elseif (!empty($params['subject'])) {
            $query->whereLike('subject', $params['subject']);
        }  elseif (!empty($params['created_by'])) {
            $query->where('created_by', $params['created_by']);
        }

        $query->orderBy('created_at', 'desc');
    }

    public function model(): string
    {
        return ActionLog::class;
    }
}
