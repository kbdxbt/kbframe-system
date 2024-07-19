<?php

namespace Modules\System\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Message extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'options' => 'json',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('recipient', function (Builder $builder) {
            $builder->where('recipient_id', Auth::id());
        });
    }
}
