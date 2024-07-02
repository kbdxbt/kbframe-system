<?php

namespace Modules\System\Services;

use Modules\Core\Services\BaseService;
use Modules\System\Repositories\UploadRepository;

class UploadFileService extends BaseService
{
    protected UploadRepository $repository;

    public function __construct(UploadRepository $repository)
    {
        $this->repository = $repository;
    }
}
