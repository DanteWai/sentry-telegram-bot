<?php

namespace Modules\User\Contracts;

use Modules\User\UserDto;

interface UserRepositoryInterface
{
    public function getUser(int $id):UserDto;

    /** @return UserDto[] */
    public function getUsers(): array;

    public function updateUser(int $id, array $attributes);

    public function deleteUser(int $id);
}