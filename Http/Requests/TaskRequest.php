<?php

namespace Modules\System\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;
use Modules\System\Enums\Task\SourceEnum;

class TaskRequest extends BaseRequest
{
    public function exportRules(): array
    {
        return [
            'source' => ['required', Rule::in(SourceEnum::values())],
            'request_params.page' => ['default:1'],
        ];
    }

    public function importRules(): array
    {
        return [
            'source' => ['required', Rule::in(SourceEnum::values())],
            'file' => ['required', 'mimes:xls,xlsx', 'max:10240'],
        ];
    }

    public function importTemplateRules(): array
    {
        return [
            'source' => ['required', Rule::in(SourceEnum::values())],
        ];
    }

    public function downloadFileRules(): array
    {
        return [
            'task_id' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'source.required' => '请选择来源',
            'source.in' => '来源选择有误，请检查',
            'file.required' => '请选择上传文件',
            'file.mimes' => '只能上传类型xls,xlsx的文件',
            'file.max' => '文件类型不能超过10M',
        ];
    }
}
