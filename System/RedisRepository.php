<?php

namespace System;

use Predis\ClientInterface;

abstract class RedisRepository
{
    protected $cache_seconds = 60 * 60;
    protected ClientInterface $redis;
    protected string $prefix = '';

    public function __construct(ClientInterface $client)
    {
        $this->redis = $client;
    }
}