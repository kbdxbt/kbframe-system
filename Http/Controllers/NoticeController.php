<?php

namespace Modules\System\Http\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Modules\System\Http\Requests\NoticeRequest;
use Modules\System\Services\NoticeService;

class NoticeController extends BaseController
{
    protected NoticeService $service;

    public function __construct(NoticeService $service)
    {
        $this->service = $service;
    }

    public function list(NoticeRequest $request)
    {
        return $this->success($this->service->getList($request->validateInput()));
    }

    public function save(NoticeRequest $request)
    {
        $this->service->saveData($request->validateInput());

        return $this->ok();
    }

    public function detail(NoticeRequest $request)
    {
        return $this->success($this->service->getDetail($request->id));
    }

    public function delete(NoticeRequest $request)
    {
        $this->service->deleteData($request->validateInput());

        return $this->ok();
    }
}
