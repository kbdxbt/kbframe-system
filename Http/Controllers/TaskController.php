<?php

namespace Modules\System\Http\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Modules\System\Enums\Task\TypeEnum;
use Modules\System\Http\Requests\TaskRequest;
use Modules\System\Services\TaskService;

class TaskController extends BaseController
{
    public function __construct(TaskRequest $request, TaskService $service)
    {
        $this->request = $request;
        $this->service = $service;
    }

    public function export()
    {
        $this->service->createExportTask($this->request->validateInput());

        return $this->ok();
    }

    public function import()
    {
        $this->service->createImportTask(
            $this->request->file('file'),
            $this->request->validateInput()
        );

        return $this->ok();
    }

    public function importTemplate()
    {
        return $this->service->importTemplate($this->request->validateInput());
    }

    public function downloadFile()
    {
        return $this->success(['url' => $this->service->downloadFile(
            $this->request->validateInput()
        )]);
    }
}
