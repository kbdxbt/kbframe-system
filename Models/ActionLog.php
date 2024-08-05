<?php

namespace Modules\System\Models;

class ActionLog extends BaseModel
{
    public $operators = true;

    protected $casts = [
        'options' => 'json',
    ];
}
