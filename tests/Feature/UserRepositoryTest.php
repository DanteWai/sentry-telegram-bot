<?php

namespace tests\Feature;

use Database\DatabaseSQLLite;
use Modules\Users\Repositories\SqlLiteUserRepository;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    protected SqlLiteUserRepository $userRepository;
    public function setUp(): void
    {
        $this->userRepository = new SqlLiteUserRepository(new DatabaseSQLLite($_ENV['DATABASE_NAME']));
    }


    public function testAddUser(){
        $dto = $this->userRepository->createUser(5, 6, 'test7@mail.ru');

        $this->assertEquals(5, $dto->telegram_id);
        $this->assertEquals(6, $dto->sentry_id);
        $this->assertEquals('test7@mail.ru', $dto->email);
    }

    public function testGetUser(){
        $dto = $this->userRepository->getUser(5);
        $this->assertEquals(5, $dto->telegram_id);
        $this->assertEquals(6, $dto->sentry_id);
        $this->assertEquals('test7@mail.ru', $dto->email);
    }

    public function testGetUsers(){
        $users = $this->userRepository->getUsers();
        $this->assertGreaterThanOrEqual(1, count($users));
    }

    public function testUpdateUser(){
        $dto = $this->userRepository->updateUser(5, ['email' => 'mail@mail.ru']);
        $this->assertEquals('mail@mail.ru', $dto->email);
    }

    public function testDeleteUser(){
        $this->userRepository->deleteUser(5);
        $dto = $this->userRepository->getUser(5);
        $this->assertEquals(null, $dto);
    }
}