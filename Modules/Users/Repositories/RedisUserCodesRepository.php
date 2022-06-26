<?php


namespace Modules\Users\Repositories;

use Predis\Client;

class RedisUserCodesRepository
{
    const CACHE_TIME = 60 * 60;

    private Client $redis;
    private string $prefix;

    public function __construct()
    {
        $this->redis = new Client();
        $this->prefix = 'codes:';
    }
    
    public function setCode(int $user_id, string $code, string $email){
        $this->redis->setex($this->prefix.$user_id, self::CACHE_TIME , $code.' '.$email);
    }

    public function getCode($user_id){
        $value = $this->redis->get($this->prefix.$user_id);
        if(!$value) return $value;

        [$code, $email] = explode(' ', $value);

        return compact('code', 'email');
    }
}