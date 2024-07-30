<?php

namespace Modules\System\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Enums\StatusEnum;
use Modules\Core\Http\Requests\BaseRequest;

class ConfigRequest extends BaseRequest
{
    public function saveRules(): array
    {
        return [
            'title'  => ['required'],
            'name'   => ['required'],
            'value'  => ['required'],
            'group'  => ['required'],
            'type'   => ['required'],
            'status' => ['sometimes', Rule::in(StatusEnum::values())]
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
            'id.required'    => '请选择操作项',
            'ids.required'   => '请选择操作项',
            'ids.explode'    => '选择参数格式有误',
            'title.required' => '请填写配置标题',
            'name.required'  => '请填写配置名称',
            'type.required'  => '请选择类型',
            'value.required' => '请填写配置值',
            'group.required' => '请选择配置分组',
            'tip.required'   => '请填写配置说明',
            'status.in'      => '选择配置状态值有误'
        ];
    }
}
