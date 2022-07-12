<?php

namespace Modules\Projects\Contracts;

use Modules\Projects\SentryProjectDto;

interface SentryProjectRepository
{
    /**
     * @return SentryProjectDto[]
     */
    public function getProjects(): ?array;

    /**
     * @param $project_id
     * @return SentryProjectDto
     */
    public function getProjectBySentryId($project_id): ?SentryProjectDto;

    /**
     * @param SentryProjectDto[] $projects
     * @return mixed
     */
    public function addProjects(array $projects);
}