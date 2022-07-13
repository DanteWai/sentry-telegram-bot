<?php

namespace Modules\Projects;

use GuzzleHttp\Exception\GuzzleException;
use Modules\Bot\Sentry\SentryApi;
use Modules\Projects\Contracts\SentryProjectRepository;


class SentryProjectService
{
    private SentryApi $sentryApi;
    private SentryProjectRepository $projectRepository;

    public function __construct(
        SentryApi               $sentryApi,
        SentryProjectRepository $projectRepository)
    {
        $this->sentryApi = $sentryApi;
        $this->projectRepository = $projectRepository;
    }


    /**
     * @return SentryProjectDto[]
     * @throws GuzzleException
     */
    public function getProjects(): array
    {
        $projects = $this->projectRepository->getProjects();
        if (!$projects) {
            $projects = $this->sentryApi->getOrganizationProjects();

            $projects = array_map(function ($item) {
                return [
                    'id' => $item['id'],
                    'title' => $item['name'],
                    'slug' => $item['slug']
                ];
            }, $projects);

            $this->projectRepository->addProjects($projects);
        }

        return $projects;
    }

    /**
     * @param $project_id
     * @return SentryProjectDto|null
     * @throws GuzzleException
     */
    public function getProjectById($project_id): ?SentryProjectDto
    {
        $project = $this->projectRepository->getProjectBySentryId($project_id);

        if (!$project) {
            $projects = $this->getProjects();
            $found_key = array_search($project_id, array_column($projects, 'id'));
            $project = $projects[$found_key] ?? null;
        }

        if(is_array($project)){
            $project = new SentryProjectDto($project);
        }

        return $project;
    }
}