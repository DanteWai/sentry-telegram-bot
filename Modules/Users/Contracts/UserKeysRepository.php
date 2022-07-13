<?php

namespace Modules\Users\Contracts;

interface UserKeysRepository
{
    public function addKey(string $key, $project_id);
    public function deleteKey(string $key);
    public function getProjectIdByKey(string $key);
}