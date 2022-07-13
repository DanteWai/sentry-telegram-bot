<?php

namespace Modules\Users\Repositories;

use Database\Database;
use Modules\Users\Contracts\UserWithKeysRepository;
use Modules\Users\UserDto;

class SqlLiteUsersWithKeysRepository implements UserWithKeysRepository
{
    private Database $client;
    protected string $table;

    public function __construct(Database $database)
    {
        $this->client = $database;
        $this->table = 'users_with_keys';
    }

    /**
     * @return array|UserDto[]
     */
    public function getUsers($condition = ''): array
    {
        $users = [];
        $data = $this->client->select(['*'], $this->table, $condition);

        foreach ($data as $user) {
            $users[] = new UserDto($user);
        }

        return $users;
    }


    public function createUser(int $telegram_id, int $project_id, string $name): UserDto
    {
        $telegram_id = $this->client->insert(
            $this->table,
            compact('telegram_id', 'project_id', 'name')
        );

        return new UserDto(compact('telegram_id', 'project_id'));
    }

    public function getUsersByProjectId(int $project_id): array
    {
        return $this->getUsers("where project_id = {$project_id}");
    }
}