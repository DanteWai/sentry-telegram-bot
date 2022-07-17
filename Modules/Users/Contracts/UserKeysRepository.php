<?php

namespace Modules\Users\Contracts;

interface UserKeysRepository
{
    public function addKey(string $key, string $project_ids);
    public function deleteKey(string $key);
    public function getProjectIdsByKey(string $key): array;
    public function startCreatingNewKey(string $chat_id);
    public function getProjectsForCreatingKey(string $chat_id);
    public function addProjectToCreatingKey(string $chat_id, string $project_id);
}