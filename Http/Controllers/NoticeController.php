<?php

namespace Modules\System\Http\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Modules\System\Http\Requests\NoticeRequest;
use Modules\System\Services\NoticeService;

class NoticeController extends BaseController
{
    public function __construct(NoticeRequest $request, NoticeService $service)
    {
        $this->request = $request;
        $this->service = $service;
    }
}
