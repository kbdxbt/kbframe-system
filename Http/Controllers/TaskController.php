<?php

namespace Modules\System\Http\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Modules\System\Enums\Task\TypeEnum;
use Modules\System\Http\Requests\TaskRequest;
use Modules\System\Services\TaskService;

class TaskController extends BaseController
{
    protected TaskService $service;

    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    public function export(TaskRequest $request)
    {
        $this->service->createExportTask($request->validateInput());

        return $this->ok();
    }

    public function import(TaskRequest $request)
    {
        $this->service->createImportTask($request->file('file'), $request->validateInput());

        return $this->ok();
    }

    public function importTemplate(TaskRequest $request)
    {
        return $this->service->importTemplate($request->validateInput());
    }

    public function downloadFile(TaskRequest $request)
    {
        return $this->success(['url' => $this->service->downloadFile($request->validateInput())]);
    }
}
