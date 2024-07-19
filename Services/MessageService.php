<?php

namespace Modules\System\Services;

use Carbon\Carbon;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Core\Exceptions\BadRequestException;
use Modules\Core\Services\BaseService;
use Modules\Core\Support\Traits\ActionServiceTrait;
use Modules\System\Enums\Message\ReadStatusEnum;
use Modules\System\Enums\QueueEnum;
use Modules\System\Http\Requests\MessageRequest;
use Modules\System\Jobs\MessageJob;
use Modules\System\Models\Message;
use Modules\System\Notifications\Message as MessageNotify;
use Modules\System\Repositories\MessageRepository;

class MessageService extends BaseService
{
    use ActionServiceTrait;

    protected $repository;

    public function __construct(MessageRepository $repository)
    {
        $this->repository = $repository;
    }


    public function send($params): void
    {
        $validator = Validator::make($params, (new MessageRequest())->sendRules());
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors()->first());
        }

        $messageId = $params['message_id'] = Str::random();

        $this->repository->create($params);

        dispatch((new MessageJob($messageId))->onQueue(QueueEnum::SYSTEM_MESSAGE_JOB->value));
    }

    public function dealQueue($messageId): void
    {
        $message = Message::query()->withoutGlobalScopes()->where('message_id', $messageId)->first();

        if (empty($message)) {
            throw new \Exception('获取系统消息记录异常');
        }

        app(AnonymousNotifiable::class)->notify(new MessageNotify($message));
    }

    public function markRead($params): void
    {
        $this->repository->batchUpdateByKeyName($params['message_ids'], [
            'read_status' => ReadStatusEnum::YES->value,
            'readed_at' => Carbon::now()
        ], 'message_id');
    }

    public function deleteData($params): void
    {
        $this->repository->delete($params['message_ids'], $params['is_force'] ?? false, 'message_id');
    }
}
