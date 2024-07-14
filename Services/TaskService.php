<?php

namespace Modules\System\Services;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Core\Exceptions\BadRequestException;
use Modules\Core\Services\BaseService;
use Modules\Core\Support\Traits\ActionServiceTrait;
use Modules\Core\Support\Upload;
use Modules\System\Enums\QueueEnum;
use Modules\System\Enums\StorageModeEnum;
use Modules\System\Enums\Task\SourceEnum;
use Modules\System\Enums\Task\StatusEnum;
use Modules\System\Enums\Task\TypeEnum;
use Modules\System\Jobs\TaskJob;
use Modules\System\Repositories\TaskRepository;
use Modules\System\Repositories\UploadRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TaskService extends BaseService
{
    use ActionServiceTrait;

    protected $repository;

    protected UploadRepository $uploadFileRepository;

    protected Filesystem $filesystem;

    public function __construct(TaskRepository $repository, UploadRepository $uploadFileRepository, $disk = 'public')
    {
        $this->repository = $repository;
        $this->uploadFileRepository = $uploadFileRepository;
        $this->filesystem = Storage::disk($disk);
    }

    public function createExportTask($params): void
    {
        $scheduledAt = $params['scheduled_at'] ?? null;

        $this->repository->create([
            'member_id' => $params['member_id'] ?? request()->userId(),
            'type' => TypeEnum::EXPORT->value,
            'source' => $params['source'],
            'scheduled_at' => $scheduledAt,
            'ext' => ['request_params' => $params['request_params'] ?? [], 'download_count' => 0]
        ]);

        $this->addTaskQueue(['task_id' => $this->repository->getId()], $scheduledAt);
    }

    public function createImportTask(UploadedFile $file, $params): void
    {
        $scheduledAt = $params['scheduled_at'] ?? null;

        $uploadRes = (new Upload($this->filesystem))
            ->upload($file, sprintf('import/%s', $params['source']));

        $this->repository->create([
            'member_id' => $params['member_id'] ?? request()->userId(),
            'type' => TypeEnum::IMPORT->value,
            'source' => $params['source'],
            'upload_id' => $uploadRes['id'],
            'scheduled_at' => $scheduledAt,
            'ext' => ['request_params' => $params['request_params'] ?? []]
        ]);

        $this->addTaskQueue(['task_id' => $this->repository->getId()], $scheduledAt);
    }

    public function importTemplate($params): StreamedResponse
    {
        $callback = SourceEnum::fromValue($params['source'], 'importTemplate');
        $path = sprintf('%s_%s.xlsx', $params['source'], date('mdHis'));

        try {
            return response()->streamDownload(function () use ($callback, $params, $path) {
                return call_user_func($callback, $params);
            }, $path);
        } catch (\Exception) {
            throw new BadRequestException('生成导入模板失败');
        }
    }

    public function addTaskQueue($params, $scheduledAt = ''): void
    {
        dispatch((new TaskJob($params))->onQueue(QueueEnum::TABLE_TASK_JOB->value))->delay($scheduledAt);
    }

    public function dealQueue($params): void
    {
        $taskId = $params['task_id'] ?? '';

        $task = $this->getDetail($taskId);

        try {
            $callback = SourceEnum::fromValue($task['source'], 'callback');

            if ($task['type'] == TypeEnum::EXPORT->value) {
                $this->dealExportTask($task, $callback);
            } else if ($task['type'] == TypeEnum::IMPORT->value) {
                $this->dealImportTask($task, $callback);
            } else {
                throw new \Exception("任务类型格式不正确");
            }
        } catch (\Exception $e) {
            $this->repository->update([
                'fail_reason' => '任务执行异常, 请联系技术人员',
                'error_reason' => $e->getFile() . '.' . $e->getLine() . ':' . $e->getMessage(),
                'completed_at' => Carbon::now(),
                'status' => StatusEnum::FAILED->value,
            ], $taskId);
        }
    }

    public function dealExportTask($task, $callback): void
    {
        $params = $task['ext']['request_params'] ?? [];
        $suffix = $params['suffix'] ?? 'xlsx';
        $mode = $params['mode'] ?? 'c';
        list($filePath, $realPath) = $this->generateFormatFileName($task['source'], 'export', $suffix);

        $exportData = [];
        $total = $num = 0;
        $currentTime = Carbon::now();

        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        while (true) {
            if (Carbon::now()->diffInSeconds($currentTime) > 1800 || $params['page'] > 10000) {
                $this->repository->update([
                    'row_num' => $total,
                    'fail_reason' => '系统执行超时或超过执行分页限制，请联系技术人员',
                    'status' => StatusEnum::FAILED->value,
                ], $task['id']);
                break;
            }

            $result = call_user_func($callback, $params);
            $total = $total ? : $result['total'] ?? 0;

            if ($total === 0) {
                $this->repository->update([
                    'num_rows' => $total,
                    'fail_reason' => '数据查询为空',
                    'status' => StatusEnum::FAILED->value,
                ], $task['id']);
            } else {
                $this->repository->update([
                    'num_rows' => $total,
                    'progress' => round($num / $total * 100, 2),
                    'status' => StatusEnum::EXEXUTING->value,
                ], $task['id']);
            }

            if (! empty($list = $result['data'])) {
                $num += count($list);
                if ($suffix == 'csv' && $mode == 'a') {
                    if (File::exists($realPath)) {
                        export_csv($list, $realPath);
                    } else {
                        fastexcel($list)->export($realPath);
                    }
                } else {
                    $exportData = array_merge($exportData, $list);
                }
            }

            if (empty($result['data']) || Arr::existEmpty($result, 'next_cursor')) {
                if ($mode != 'a') {
                    fastexcel($exportData)->export($realPath);
                }
                break;
            }

            $params['page']++;
            !empty($result['next_cursor']) && $params['cursor'] = $result['next_cursor'];
        }

        if ($total) {
            $this->uploadFileRepository->create([
                'storage_mode' => StorageModeEnum::fromValue($this->filesystem->getConfig()['driver']),
                'origin_name' => File::basename($filePath),
                'object_name' => File::basename($filePath),
                'hash' => md5_file($filePath),
                'mime_type' => File::mimeType($filePath),
                'storage_path' => $realPath,
                'suffix' => File::extension($filePath),
                'size_byte' => File::size($filePath),
                'size_info' => format_bytes(File::size($filePath)),
                'url' => $this->filesystem->url($filePath),
                'created_by' => $task['member_id'],
            ]);

            $this->repository->update([
                'upload_id' => $this->uploadFileRepository->getId(),
                'progress' => 100,
                'completed_at' => Carbon::now(),
                'status' => StatusEnum::SUCCESS->value,
            ], $task['id']);
        }
    }

    public function dealImportTask($task, $callback): void
    {
        $uploadFileModel = $this->uploadFileRepository->query()->find($task['upload_id']);
        if (empty($uploadFileModel)) {
            throw new \Exception('获取上传文件失败');
        }
        $data = fastexcel()->import($this->filesystem->path($uploadFileModel['storage_path']));

        $total = $data->count();
        $failData = [];
        $data->chunk(500)->each(function ($list) use ($total, $task, $callback, &$failData) {
            $num = 0;

            $result = call_user_func($callback, $list);
            if ($result) $failData = array_merge($failData, $result);

            $num += $list->count();
            $this->repository->update([
                'num_rows' => $total,
                'progress' => round($num / $total * 100, 2),
                'status' => StatusEnum::EXEXUTING->value,
            ], $task['id']);
        });

        list($filePath, $realPath) = $this->generateFormatFileName($task['source'], 'import_fail');
        fastexcel($failData)->export($realPath);

        $this->repository->update([
            'ext->fail_rows' => count($failData),
            'ext->fail_file_path' => $realPath,
            'progress' => 100,
            'completed_at' => Carbon::now(),
            'status' => StatusEnum::SUCCESS->value,
        ], $task['id']);
    }

    public function generateFormatFileName($source, $path, $suffix = 'xlsx'): array
    {
        $format = '{path}/{date}/{filename}_{datetime}{.suffix}';

        $filename = SourceEnum::fromValue($source);
        if ($path == 'import_fail') {
            $filename = '错误提示';
        }

        $filePath = Upload::generateFormatFileName(
            sprintf('%s/%s', $path, $source),
            '',
            $filename,
            $suffix,
            $format
        );

        $this->filesystem->makeDirectory(pathinfo($filePath, PATHINFO_DIRNAME));
        $realPath = $this->filesystem->path($filePath);

        return [$filePath, $realPath];
    }

    public function downloadFile($params): string
    {
        $task = $this->getDetail($params['task_id']);

        if ($task['status'] != StatusEnum::SUCCESS->value || empty($task['file_path']) || $task['member_id'] != request()->userId()) {
            throw new BadRequestException('获取任务下载文件失败');
        }

        $this->repository->query()->where('id', $task['id'])->increment('ext->download_count');

        return Storage::disk('public')->url($task['file_path']);
    }
}
