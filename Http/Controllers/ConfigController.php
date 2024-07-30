<?php

namespace Modules\System\Http\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Modules\System\Http\Requests\ConfigRequest;
use Modules\System\Services\ConfigService;

class ConfigController extends BaseController
{
    public function __construct(ConfigRequest $request, ConfigService $service)
    {
        $this->request = $request;
        $this->service = $service;
    }
}
