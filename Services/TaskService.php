<?php

namespace Modules\System\Services;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Exceptions\BadRequestException;
use Modules\Core\Services\BaseService;
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
    protected TaskRepository $repository;

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

        $uploadRes = (new Upload($this->filesystem->getConfig()['driver']))
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

    public function getTask($id): array
    {
        $data = $this->repository->query()->find($id)?->toArray();
        if (!$data) {
            throw new BadRequestException('获取数据失败');
        }

        return $data;
    }

    public function addTaskQueue($params, $scheduledAt = ''): void
    {
        dispatch((new TaskJob($params))->onQueue(QueueEnum::TABLE_TASK_JOB->value))->delay($scheduledAt);
    }

    public function dealQueue($params): void
    {
        $taskId = $params['task_id'] ?? '';

        $task = $this->getTask($taskId);

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
        list($filePath, $realPath) = $this->generateFormatFileName($task['source'], $task['type']);

        $requestParams = $task['ext']['request_params'] ?? [];
        $exportData = [];
        $total = $num = 0;
        $currentTime = Carbon::now();

        while (true) {
            if (Carbon::now()->diffInSeconds($currentTime) > 1800 || $requestParams['page'] > 10000) {
                $this->repository->update([
                    'row_num' => $total,
                    'fail_reason' => '系统执行超时或超过执行分页限制，请联系技术人员',
                    'status' => StatusEnum::FAILED->value,
                ], $task['id']);
                break;
            }

            $result = call_user_func($callback, $requestParams);
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

            if (! empty($result['data'])) {
                $num += count($exportData);
                $exportData = array_merge($exportData, $result['data']);
            } else {
                fastexcel($exportData)->export($realPath);
                break;
            }

            $requestParams['page']++;
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
        $fail_data = [];
        $data->chunk(500)->each(function ($list) use ($total, $task, $callback, &$fail_data) {
            $num = 0;

            foreach ($list as $v) {
                $result = call_user_func($callback, $v);
                if ($result && $result['status'] == 0) {
                    $fail_data[] = $result;
                }
            }

            $num += $list->count();
            $this->repository->update([
                'num_rows' => $total,
                'progress' => round($num / $total * 100, 2),
                'status' => StatusEnum::EXEXUTING->value,
            ], $task['id']);
        });

        list($filePath, $realPath) = $this->generateFormatFileName($task['source'], 'import_fail');
        fastexcel($fail_data)->export($realPath);

        $this->repository->update([
            'ext->fail_rows' => count($fail_data),
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
            $filename .= '_失败';
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
        $task = $this->getTask($params['task_id']);

        if ($task['status'] != StatusEnum::SUCCESS->value || empty($task['file_path']) || $task['member_id'] != request()->userId()) {
            throw new BadRequestException('获取任务下载文件失败');
        }

        $this->repository->query()->where('id', $task['id'])->increment('ext->download_count');

        return Storage::disk('public')->url($task['file_path']);
    }
}
