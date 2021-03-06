<?php

namespace Modules\Users\Contracts;

use Modules\Users\UserDto;

interface UserRepository
{
    public function getUser(int $telegram_id): ?UserDto;

    public function getUserByEmail(string $email): ?UserDto;

    /** @return UserDto[] */
    public function getUsers(): array;

    public function getUsersBySentryIds(array $sentry_ids): array;

    public function createUser(int $telegram_id, int $sentry_id, string $email): UserDto;

    public function updateUser(int $telegram_id, array $attributes):UserDto;

    public function deleteUser(int $telegram_id): void;
}