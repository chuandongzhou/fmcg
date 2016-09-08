<?php

namespace WeiHeng\OrderDownload;

class OrderDownload
{
    /**
     * @var null|\Predis\ClientInterface
     */
    protected $redis;

    /**
     * 缓存前缀
     *
     * @var string
     */
    protected $prefix = 'shop:order-export-model:';

    protected $field = 'model_id';

    /**
     * 初始化函数
     *
     * @param \Illuminate\Redis\Database $redis
     */
    public function __construct($redis)
    {
        $this->redis = $redis->connection();
    }

    /**
     * 设置模型
     *
     * @param $shopId
     * @param $modelId
     * @return int
     */
    public function setTemplete($shopId, $modelId)
    {
        $key = $this->prefix . $shopId;
        return $this->redis->hset($key, $this->field, $modelId);
    }


    /**
     * 获取模型
     *
     * @param $shopId
     * @return int|string
     */
    public function getTemplete($shopId)
    {
        $key = $this->prefix . $shopId;

        return $this->redis->exists($key) ? $this->redis->hget($key, $this->field) : 1;
    }

}
