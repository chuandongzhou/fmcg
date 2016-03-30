<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/11/6
 * Time: 19:07
 */
namespace App\Services;

use Predis\Client;
use Predis\ClientInterface;

class RedisService
{

    /**
     * @var ClientInterface|Client
     */
    protected $redis;

    /**
     * 缓存名称
     *
     * @var string
     */
    protected $name = 'fmcg';

    /**
     * 命名空间
     *
     * @var string
     */
    private $namespace;

    public function __construct()
    {

        $this->redis = \Redis::connection();
        $this->namespace = $this->name . ':';
    }

    /**
     * 设置Redis
     *
     * @param $key
     * @param $redisValue
     * @param $expire
     */
    public function setRedis($key, $redisValue, $expire = 0)
    {
        $redisKey = $this->getKey($key);
        $expire = $expire ? $expire : cons('push_time.msg_life');
        if (!$this->redis->exists($redisKey)) {
            $this->redis->set($redisKey, $redisValue);
            $this->redis->expire($redisKey, $expire);
        }

    }

    /**
     *  判断redis是否含有
     *
     * @param $key
     * @return mixed
     */
    public function has($key)
    {
        return $this->redis->exists($this->getKey($key));
    }

    /**
     *  获取redis
     *
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->redis->get($this->getKey($key));
    }

    /**
     *  获取redis
     *
     * @param $key
     * @return mixed
     */
    public function del($key)
    {
        return $this->redis->del($this->getKey($key));
    }

    /**
     * 增加值
     *
     * @param $key
     * @param int $num
     * @param int $userId
     * @return mixed
     */
    public function increment($key, $num = 1, $userId = 0)
    {
        return $this->redis->zincrby($this->getKey($key), $num, $userId);
    }

    /**
     * 获取redisKey
     *
     * @param $key
     * @return string
     */
    protected function getKey($key)
    {
        return $this->namespace . $key;
    }

}