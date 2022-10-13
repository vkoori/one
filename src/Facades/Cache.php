<?php

namespace One\Facades;


use One\Cache\File;
use One\Cache\Redis;

/**
 * Class Cache
 * @package Facades
 * @mixin \One\Cache\Redis
 * @mixin \Redis
 * @method static string get($key, \Closure $closure = null, $ttl = 0, $tags = [])
 * @method static bool delRegex($key)
 * @method static bool del($key)
 * @method static bool flush($tag)
 * @method static bool set($key, $val, $ttl = 0, $tags = [])
 * @method static Redis setConnection($key)
 */
class Cache extends Facade
{
    protected static function getFacadeAccessor()
    {
        switch (config('cache.drive')) {
            case 'file':
                return File::class;
                break;
            case 'redis':
                return Redis::class;
                break;
            default:
                exit('no cache drive');
        }
    }
}
