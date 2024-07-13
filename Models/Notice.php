<?php

namespace Modules\System\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Notice extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'ext' => 'json',
    ];
}
