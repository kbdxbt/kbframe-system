<?php

namespace Modules\System\Models;

class HttpLog extends BaseModel
{
    protected $casts = [
        'ext' => 'json',
    ];
}
