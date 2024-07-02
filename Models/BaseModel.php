<?php

namespace Modules\System\Models;

use Modules\Core\Models\BaseModel as Model;

class BaseModel extends Model
{
    protected $connection = 'system';
}
