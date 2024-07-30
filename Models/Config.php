<?php

namespace Modules\System\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Config extends BaseModel
{
    use SoftDeletes;

    public $operators = true;

    protected $casts = [
        'options' => 'json',
    ];
}
