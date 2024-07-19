<?php

namespace Modules\System\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\System\Enums\Message\ChannelEnum;
use Modules\System\Enums\Message\TypeEnum;

class MessageRequest extends BaseRequest
{
    public function sendRules(): array
    {
        return [
            'recipient_id' => ['required'],
            'type' => ['required', Rule::in(TypeEnum::values())],
            'channel' => ['required', Rule::in(ChannelEnum::values())],
            'subject' => ['required'],
            'content' => ['required'],
        ];
    }

    public function unreadListRules(): array
    {
        return [
            'read_status' => ['default:0'],
        ];
    }

    public function markReadRules(): array
    {
        return [
            'message_ids' => ['required', 'explode:string'],
        ];
    }

    public function detailRules(): array
    {
        return [
            'message_id' => ['required'],
        ];
    }

    public function deleteRules(): array
    {
        return [
            'message_ids' => ['required', 'explode:string'],
        ];
    }

    public function messages(): array
    {
        return [
            'message_id.required' => '请选择操作项',
            'message_ids.required' => '请选择操作项',
            'message_ids.explode' => '选择参数格式有误',
            'recipient_id.required' => '请选择接收人',
            'type.required' => '请选择发送类型',
            'type.in' => '选择参数类型有误',
            'channel.required' => '请选择发送渠道',
            'channel.in' => '选择渠道类型有误',
            'subject.required' => '请选择发送主题',
            'content.required' => '请选择发送内容',
        ];
    }
}
