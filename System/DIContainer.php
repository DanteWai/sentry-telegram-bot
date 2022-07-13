<?php

namespace System;

use Database\DatabaseSQLLite;
use GuzzleHttp\Client;
use Modules\Bot\Contracts\TelegramMessageHandlerInterface;
use Modules\Bot\Contracts\WebhookHandlerInterface;
use Modules\Bot\Sentry\TelegramBot;
use Modules\Bot\Sentry\TelegramMessageHandler;
use Modules\Bot\Sentry\WebhookHandler;
use Modules\Projects\Contracts\SentryProjectRepository;
use Modules\Projects\Repositories\RedisProjectsRepository;
use Modules\Users\Contracts\UserCodesRepository;
use Modules\Users\Contracts\UserKeysRepository;
use Modules\Users\Contracts\UserRepository;
use Modules\Users\Contracts\UserWithKeysRepository;
use Modules\Users\Repositories\RedisUserCodesRepository;
use Modules\Users\Repositories\RedisUserKeysRepository;
use Modules\Users\Repositories\SqlLiteUserRepository;
use Modules\Users\Repositories\SqlLiteUsersWithKeysRepository;
use Predis\ClientInterface;
use ReflectionException;

class DIContainer
{
    protected array $binds = [];

    public function __construct()
    {
        $this->binds[TelegramMessageHandlerInterface::class] = TelegramMessageHandler::class;
        $this->binds[WebhookHandlerInterface::class] = WebhookHandler::class;
        $this->binds[TelegramBot::class] = fn() => new TelegramBot(new Client(), $_ENV['TELEGRAM_SENTRY_BOT_TOKEN']);
        $this->binds[UserRepository::class] = fn() => new SqlLiteUserRepository(new DatabaseSQLLite($_ENV['DATABASE_NAME']));
        $this->binds[UserWithKeysRepository::class] = fn() => new SqlLiteUsersWithKeysRepository(new DatabaseSQLLite($_ENV['DATABASE_NAME']));
        $this->binds[UserKeysRepository::class] = RedisUserKeysRepository::class;
        $this->binds[UserCodesRepository::class] = RedisUserCodesRepository::class;
        $this->binds[SentryProjectRepository::class] = RedisProjectsRepository::class;
        $this->binds[ClientInterface::class] = \Predis\Client::class;
    }

    public function bind(string $type, string $subtype){
        $this->binds[$type] = $subtype;
    }

    /**
     * @throws ReflectionException
     */
    public function resolveClass($className): object{
        $ref = new \ReflectionClass($className);
        $constructor = $ref->getConstructor();
        $deps = [];

        if($constructor !== null){
            $attrs = $constructor->getParameters();

            foreach ($attrs as $attr){
                $type = $attr->getType();
                $name = is_null($type) ? $attr->getName() : $type->getName();

                if(isset($this->binds[$name])){
                    $name = $this->binds[$name];
                }

                if(is_callable($name)){
                    $deps[] = $name();
                } else if(class_exists($name)){
                    $deps[] = $this->resolveClass($name);
                }  else if($attr->isOptional()){
                    $deps[] = $attr->getDefaultValue();
                } else {
                    $deps[] = $name;
                }

            }
        }

        return new $className(...$deps);
    }

}