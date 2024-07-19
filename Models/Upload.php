<?php

namespace Modules\System\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Upload extends BaseModel
{
    use SoftDeletes;

    public $operators = true;

    protected $casts = [
        'ext' => 'json',
    ];
}
