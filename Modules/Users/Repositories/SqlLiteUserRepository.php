<?php

namespace Modules\Users\Repositories;

use Database\Database;
use Modules\Users\Contracts\UserRepository;
use Modules\Users\UserDto;

class SqlLiteUserRepository implements UserRepository
{

    private Database $client;
    protected string $table;

    public function __construct(Database $database)
    {
        $this->client = $database;
        $this->table = 'sentry_users';
    }

    public function getUser(int $telegram_id): ?UserDto
    {
        [$user] = $this->client->select(['*'], $this->table, "where telegram_id = {$telegram_id} limit 1");
        return $user ? new UserDto($user) : $user;
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

    /**
     * @return array|UserDto[]
     */
    public function getUsersBySentryIds(array $sentry_ids): array
    {
        $sentry_ids = implode(',', $sentry_ids);
        return $this->getUsers("where sentry_id in ({$sentry_ids})");
    }

    public function createUser(int $telegram_id, int $sentry_id, string $email): UserDto
    {
        $telegram_id = $this->client->insert(
            $this->table,
            compact('telegram_id', 'sentry_id', 'email')
        );

        return new UserDto(compact('telegram_id', 'sentry_id', 'email'));
    }

    public function updateUser(int $telegram_id, array $attributes): UserDto
    {
        $this->client->update($this->table, $attributes, "where telegram_id = {$telegram_id}");
        return $this->getUser($telegram_id);
    }

    public function deleteUser(int $telegram_id): void
    {
        $this->client->delete($this->table, "where telegram_id = {$telegram_id}");
    }
}