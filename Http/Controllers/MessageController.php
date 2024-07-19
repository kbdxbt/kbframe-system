<?php

namespace Modules\System\Http\Controllers;

use Modules\Core\Http\Controllers\BaseController;
use Modules\System\Http\Requests\MessageRequest;
use Modules\System\Services\MessageService;

class MessageController extends BaseController
{
    public function __construct(MessageRequest $request, MessageService $service)
    {
        $this->request = $request;
        $this->service = $service;
    }

    public function unreadList()
    {
        return $this->success($this->service->getList($this->request->validateInput()));
    }

    public function markRead()
    {
        $this->service->markRead($this->request->validateInput());

        return $this->ok();
    }

    public function detail()
    {
        return $this->success($this->service->getDetail($this->request->message_id, 'message_id'));
    }
}
