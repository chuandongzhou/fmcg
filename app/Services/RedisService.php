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
     * 设置推送Redis
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

}