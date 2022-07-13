<?php


namespace Modules\Users\Repositories;

use Modules\Users\Contracts\UserCodesRepository;
use System\RedisRepository;

class RedisUserCodesRepository extends RedisRepository  implements UserCodesRepository
{
    protected string $prefix = 'codes:';

    public function setCode(int $user_id, string $code, string $email, string $sentry_id): void
    {
        $this->redis->setex($this->prefix . $user_id, $this->cache_seconds, $code . ' ' . $email . ' ' . $sentry_id);
    }

    public function getCode($user_id): ?array
    {
        $value = $this->redis->get($this->prefix . $user_id);

        if (is_null($value)) return null;

        [$code, $email, $sentry_id] = explode(' ', $value);

        return compact('code', 'email', 'sentry_id');
    }
}