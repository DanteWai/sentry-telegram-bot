<?php

namespace Modules\Users\Repositories;

use Database\Database;
use Modules\Users\Contracts\UserWithKeysRepository;
use Modules\Users\UserDto;

class SqlLiteUsersWithKeysRepository implements UserWithKeysRepository
{
    private Database $client;
    protected string $table;
    protected string $pivot_project_table = 'project_user_with_keys';

    public function __construct(Database $database)
    {
        $this->client = $database;
        $this->table = 'users_with_key';
    }

    /**
     * @return array|UserDto[]
     */
    public function getUsersWithKey(): array
    {
        return $this->client->select(['*'], $this->table);
    }

    public function getUsersIdByProject(string $project_id): array
    {
        $data = $this->client->select(['*'], $this->pivot_project_table, 'where project_id = ' . $project_id);
        return array_map(fn($item) => $item['user_with_key_id'], $data);
    }


    public function createUser(int $telegram_id, array $project_ids, string $name): int
    {
        $this->client->insert($this->table, compact('telegram_id', 'name'));

        foreach ($project_ids as $project_id) {
            $this->client->insert($this->pivot_project_table, [
                'project_id' => $project_id,
                'user_with_key_id' => $telegram_id
            ]);
        }

        return $telegram_id;
    }

    public function isUserExistByTelegramId(int $telegram_id): bool
    {
        [$user] = $this->client->select(['*'], $this->table, "where telegram_id = '{$telegram_id}' limit 1");
        return (bool)$user;
    }

    public function deleteUser(int $telegram_id)
    {
        $this->client->delete($this->table, 'where telegram_id = ' . $telegram_id);
        $this->client->delete($this->pivot_project_table, 'where user_with_key_id = ' . $telegram_id);
    }


}