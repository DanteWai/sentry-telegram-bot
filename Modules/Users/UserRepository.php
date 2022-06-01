<?php

namespace Modules\User;

use Database\Database;
use Modules\User\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{

    private Database $client;
    protected string $table;

    public function __construct(Database $database)
    {
        $this->client = $database;
        $this->table = 'sentry_users';
    }

    public function getUser(int $id): UserDto
    {
        $data = $this->client->select(['*'], $this->table, "where id = ${id} limit 1");
        return new UserDto();
    }

    public function getUsers(): array
    {
        $data = $this->client->select(['*'], $this->table);
        return [];
    }

    public function createUser(array $attributes)
    {
        $data = $this->client->insert($this->table, []);
    }

    public function updateUser(int $id, array $attributes)
    {
        $data = $this->client->update($this->table, [], "where id = ${id}");
    }

    public function deleteUser(int $id)
    {
        $data = $this->client->delete($this->table, "where id = ${id}");
    }
}