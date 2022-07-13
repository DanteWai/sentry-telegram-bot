<?php

namespace Modules\Users\Repositories;

use Modules\Users\Contracts\UserKeysRepository;
use System\RedisRepository;

class RedisUserKeysRepository extends RedisRepository implements UserKeysRepository
{
    protected string $prefix = 'keys:';

    public function addKey(string $key, $project_id)
    {
        $this->redis->setex($this->prefix . $key, $this->cache_seconds, $project_id);
    }

    public function deleteKey(string $key)
    {
        $this->redis->del($key);
    }

    public function getProjectIdByKey(string $key): ?string
    {
        return $this->redis->get($this->prefix . $key);
    }
}