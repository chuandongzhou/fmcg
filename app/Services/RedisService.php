<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/11/6
 * Time: 19:07
 */
namespace App\Services;

use DB;
use Illuminate\Support\Facades\Redis;

class RedisService
{

    /**
     * 设置Redis
     *
     * @param $redisKey
     * @param $redisValue
     * @param $expire
     */
    public static function setRedis($redisKey, $redisValue, $expire = 0)
    {
        static $redis;
        if (!$redis) {
            $redis = Redis::connection();
        }
        $expire = $expire ? $expire : cons('push_time.msg_life');
        if (!$redis->exists($redisKey)) {
            $redis->set($redisKey, $redisValue);
            $redis->expire($redisKey, $expire);
        }

    }

    /**
     *  判断redis是否含有
     *
     * @param $key
     * @return mixed
     */
    public static function has($key)
    {
        static $redis;
        if (!$redis) {
            $redis = Redis::connection();
        }
        return $redis->has($key);
    }

    /**
     * 增加值
     *
     * @param $key
     * @param int $num
     * @param int $userId
     * @return mixed
     */
    public static function increment($key, $num = 1, $userId = 0)
    {
        static $redis;
        if (!$redis) {
            $redis = Redis::connection();
        }
        return $redis->zincrby($key, $num, $userId);
    }

}