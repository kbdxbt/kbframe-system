<?php

namespace Modules\System\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Upload extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'ext' => 'json',
    ];
}
