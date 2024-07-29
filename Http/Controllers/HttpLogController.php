<?php

namespace Modules\System\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\BaseController;
use Modules\System\Services\HttpLogService;

class HttpLogController extends BaseController
{
    public function __construct(Request $request, HttpLogService $service)
    {
        $this->request = $request;
        $this->service = $service;
    }
}
