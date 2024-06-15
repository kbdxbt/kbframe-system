<?php

namespace Modules\System\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\BaseModel;

class TableTask extends BaseModel
{
    use SoftDeletes;

    protected $table = 'table_tasks';

    protected $guarded = [];

    protected $casts = [
        'ext' => 'json',
    ];
}
