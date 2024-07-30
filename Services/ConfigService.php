<?php

namespace Modules\System\Services;

use Modules\Core\Enums\StatusEnum;
use Modules\Core\Services\BaseService;
use Modules\Core\Support\Traits\ActionServiceTrait;
use Modules\Core\Support\Traits\Cacheable;
use Modules\System\Repositories\ConfigRepository;

class ConfigService extends BaseService
{
    use ActionServiceTrait, Cacheable {
        ActionServiceTrait::saveData as parentSaveData;
    }

    protected $repository;

    public function __construct(ConfigRepository $repository)
    {
        $this->repository  = $repository;
        $this->cachePrefix = 'system_config:';
    }

    public function saveData($params): void
    {
        $this->parentSaveData($params);

        $this->getData($params['key'], true);
    }

    public function getData($key, $isCache = false)
    {
        $this->key = $key;

        $config = json_decode(self::getCacheInstance()->get($this->getCacheKey()), true);

        if (empty($config) || $isCache) {
            $data = $this->getDetail($key, 'name');

            if ($data) {
                $config = $data['value'];
                self::getCacheInstance()->put($this->getCacheKey(), json_encode($config));
            }
        }

        return $config;
    }

    protected function formatList($data)
    {
        foreach ($data['data'] as &$v) {
            $v['status_text'] = StatusEnum::fromValue($v['status']);
        }

        $data['searchFields'] = $this->repository->searchFields();

        return $this->formatListData($data);
    }
}
