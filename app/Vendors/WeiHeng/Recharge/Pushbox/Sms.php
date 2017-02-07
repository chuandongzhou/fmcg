<?php

namespace WeiHeng\Recharge\Pushbox;

use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Config\Repository as Config;
use Tinpont\Pushbox\Adapter;

/**
 * 通短信推送类
 *
 * @package Recharge\Pushbox
 */
class Sms
{

    /**
     * 短信适配类
     *
     * @var \Tinpont\Pushbox\Adapter
     */
    protected $adapter;

    /**
     * 队列
     *
     * @var \Illuminate\Contracts\Queue\Queue
     */
    protected $queue;

    /**
     * 缓存
     *
     * @var \Illuminate\Contracts\Queue\Queue
     */
    protected $cache;

    /**
     * 缓存前缀
     *
     * @var string
     */
    protected $cachePrefix = 'services:sms:';

    /**
     * 模版
     *
     * @var
     */
    protected $templates;

    /**
     * 构造函数
     *
     * @param \Tinpont\Pushbox\Adapter $adapter
     * @param \Illuminate\Contracts\Queue\Queue $queue
     * @param \Illuminate\Contracts\Cache\Repository $cache
     * @param \Illuminate\Contracts\Config\Repository $config
     */
    public function __construct(Adapter $adapter, Queue $queue, Cache $cache, Config $config)
    {
        $this->adapter = $adapter;
        $this->queue = $queue;
        $this->cache = $cache;
        $this->templates = $config->get('push.top');
    }

    /**
     * 短信发送
     *
     * @param string $template
     * @param array|string $mobiles
     * @param mixed $text
     * @param int $minutes
     * @param bool $force
     * @return false
     */
    public function send($template, $mobiles, $text, $minutes = 1, $force = false)
    {
        $mobileCachePrefix = $this->getKey('mobile:');
        // 限制同一个号码发送
        $allows = [];
        foreach ((array)$mobiles as $mobile) {
            if ($force || !$this->cache->get($mobileCachePrefix . $mobile)) {
                $allows[] = $mobile;
            }
        }

        if (empty($allows)) {
            return false;
        }

        $success = $this->adapter->to($allows)->{'push' . ucfirst($template)}($this->parseText($text))->success();


        if ($minutes) {
            foreach ($success as $mobile) {
                $this->cache->put($mobileCachePrefix . $mobile, 1, $minutes);
            }
        }

        return $success ? true : false;
    }

    /**
     * 队列发送短信
     *
     * @param string $template
     * @param array|string $mobiles
     * @param mixed $text
     * @param null $queue
     * @return mixed
     */
    public function queue($template, $mobiles, $text, $queue = null)
    {
        $data = [
            'template' => $template,
            'mobiles' => $mobiles,
            'text' => $text,
        ];

        return $this->queue->push('pushbox.sms@handleQueue', $data, $queue);
    }

    /**
     * 处理队列短信发送
     *
     * @param \Illuminate\Contracts\Queue\Job $job
     * @param array $data
     */
    public function handleQueue($job, $data)
    {
        $this->send($data['template'], $data['mobiles'], $data['text']);
        $job->delete();
    }

    /**
     * 设置适配类
     *
     * @param \Tinpont\Pushbox\Adapter $adapter
     * @return $this
     */
    public function setAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * Set the queue manager instance.
     *
     * @param  \Illuminate\Contracts\Queue\Queue $queue
     * @return $this
     */
    public function setQueue(Queue $queue)
    {
        $this->queue = $queue;

        return $this;
    }


    /**
     * 发送验证码
     *
     * @param string $type
     * @param string $mobile
     * @param int $length
     * @return bool
     */
    public function sendCode($type, $mobile, $length = 4)
    {
        $job = array_get($this->templates, 'templates.' . $type);

        if (empty($job)) {
            return false;
        }

        $code = sprintf("%0{$length}u", mt_rand(0, pow(10, $length) - 1));

        if ($this->send($type, $mobile, $code)) {
            $this->cache->put($this->getKey("code:{$type}:{$mobile}"), $code, 30);

            return true;
        }

        return false;
    }

    /**
     * 验证验证码是否存在
     *
     * @param string $type
     * @param string $mobile
     * @param string $code
     * @param bool $delete
     * @return bool
     */
    public function verifyCode($type, $mobile, $code, $delete = true)
    {
        $key = $this->getKey("code:{$type}:{$mobile}");
        $cacheCode = $this->cache->get($key);

        if ($code && $code === $cacheCode) {
            $delete && $this->cache->forget($key);

            return true;
        }

        return false;
    }

    /**
     * 格式化参数
     *
     * @param $text
     * @return array|string
     */
    public function parseText($text)
    {
        if (!is_array($text)) {
            return (string)$text;
        }
        foreach ($text as $key => $value) {
            !is_string($value) && ($text[$key] = (string)$value);
        }
        return $text;

    }

    /**
     * 获取缓存key
     *
     * @param string $key
     * @return string
     */
    protected function getKey($key = '')
    {
        return $this->cachePrefix . $key;
    }

    /**
     * 魔术方法
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        if (starts_with($name, 'send')) {
            $template = substr($name, 4);
            array_unshift($arguments, $template);

            return call_user_func_array([$this, 'queue'], $arguments);
        }

        throw new \BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $name . '()');
    }
}