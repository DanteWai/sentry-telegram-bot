<?php

namespace Modules\Users\Repositories;

use Modules\Users\Contracts\UserKeysRepository;
use System\RedisRepository;

class RedisUserKeysRepository extends RedisRepository implements UserKeysRepository
{
    protected string $prefix = 'keys:';

    public function addKey(string $key, string $project_ids)
    {
        $this->redis->setex($this->prefix . $key, $this->cache_seconds, $project_ids);
    }

    public function deleteKey(string $key)
    {
        $this->redis->del($this->prefix . $key);
    }

    public function getProjectIdsByKey(string $key): array
    {
        $projects = $this->redis->get($this->prefix . $key);

        if($projects){
            return explode(',', $projects);
        } else {
            return [];
        }
    }

    public function startCreatingNewKey(string $chat_id)
    {
        $this->redis->setex($this->prefix . 'creating'. $chat_id, $this->cache_seconds, '');
    }

    public function addProjectToCreatingKey(string $chat_id, string $project_id)
    {
        $project_ids = $this->redis->get($this->prefix . 'creating'. $chat_id);

        if($project_ids){
            $project_ids .= ",{$project_id}";
        } else {
            $project_ids = $project_id;
        }

        $this->redis->setex($this->prefix . 'creating'. $chat_id, $this->cache_seconds, $project_ids);
    }

    public function getProjectsForCreatingKey(string $chat_id): array
    {
        $projects = $this->redis->get($this->prefix . 'creating'. $chat_id);

        if($projects){
            return explode(',', $projects);
        } else {
            return [];
        }
    }

}