<?php

namespace Modules\System\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\BaseModel;

class HttpLog extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'ext' => 'json',
    ];
}
