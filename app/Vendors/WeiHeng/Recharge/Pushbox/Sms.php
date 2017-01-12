<?php

namespace WeiHeng\Recharge\Pushbox;

use Illuminate\Contracts\Queue\Queue;
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
     * 构造函数
     *
     * @param \Tinpont\Pushbox\Adapter $adapter
     * @param \Illuminate\Contracts\Queue\Queue $queue
     */
    public function __construct(Adapter $adapter, Queue $queue)
    {
        $this->adapter = $adapter;
        $this->queue = $queue;
    }

    /**
     * 短信发送
     *
     * @param string $template
     * @param array|string $mobiles
     * @param mixed $text
     * @return false
     */
    public function send($template, $mobiles, $text)
    {
        return $this->adapter->to($mobiles)->{'push' . ucfirst($template)}($this->parseText($text))->success();
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