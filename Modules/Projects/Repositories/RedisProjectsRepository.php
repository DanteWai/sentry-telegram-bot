<?php

namespace Modules\Projects\Repositories;

use Modules\Projects\Contracts\SentryProjectRepository;
use Modules\Projects\SentryProjectDto;
use System\RedisRepository;

class RedisProjectsRepository extends RedisRepository implements SentryProjectRepository
{
    protected string $prefix = 'projects:';

    public function getProjects(): ?array
    {
        $value = $this->redis->get($this->prefix);

        if($value) {
            $value = json_decode($value, true);
        }

        return $value;
    }

    public function getProjectBySentryId($project_id): ?SentryProjectDto
    {
        $value = $this->redis->get($this->prefix.$project_id);

        if($value){
            return new SentryProjectDto(json_decode($value, true));
        }

        $value = $this->getProjects();

        if($value){
            $found_key = array_search($project_id, array_column($value, 'id'));
            $value = $value[$found_key] ?? null;

            if(!is_null($value)){
                $this->redis->setex($this->prefix.$project_id, $this->cache_seconds, json_encode($value));
            }
        }


        return $value ? new SentryProjectDto($value) : null;
    }

    public function addProjects(array $projects)
    {
        $this->redis->setex($this->prefix, $this->cache_seconds, json_encode($projects));
    }
}