<?php

namespace Modules\System\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\System\Models\Upload;

class UploadRepository extends BaseRepository
{
    public function model(): string
    {
        return Upload::class;
    }
}
