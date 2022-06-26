<?php


namespace Modules\Users\Repositories;

use Modules\Users\Contracts\UserCodesRepository;
use Predis\Client;

class RedisUserCodesRepository implements UserCodesRepository
{
    const CACHE_TIME = 60 * 60;

    private Client $redis;
    private string $prefix;

    public function __construct()
    {
        $this->redis = new Client();
        $this->prefix = 'codes:';
    }

    public function setCode(int $user_id, string $code, string $email, string $sentry_id): void
    {
        $this->redis->setex($this->prefix . $user_id, self::CACHE_TIME, $code . ' ' . $email . ' ' . $sentry_id);
    }

    public function getCode($user_id): ?array
    {
        $value = $this->redis->get($this->prefix . $user_id);

        if (is_null($value)) return null;

        [$code, $email, $sentry_id] = explode(' ', $value);

        return compact('code', 'email', 'sentry_id');
    }
}