<?php

namespace Modules\System\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\PlatformGateway\Models\PlatformAccount;
use Modules\System\Enums\Task\SourceEnum;
use Modules\System\Models\Notice;

class NoticeRequest extends BaseRequest
{
    public function listRules(): array
    {
        return [
        ];
    }

    public function saveRules(): array
    {
        return [
            'category' => ['required'],
            'title' => ['required', 'max:255'],
            'content' => ['required'],
        ];
    }

    public function detailRules(): array
    {
        return [
            'id' => ['required'],
        ];
    }

    public function deleteRules(): array
    {
        return [
            'ids' => ['required', 'explode:number'],
        ];
    }

    public function messages(): array
    {
        return [
            'category.required' => '请选择类别',
            'title.required' => '请填写标题',
            'title.max' => '标题最多输入255个字符',
            'content.required' => '请填写内容',
            'id.required' => '请选择操作项',
            'ids.required' => '请选择操作项',
            'ids.explode' => '选择参数格式有误'
        ];
    }
}
