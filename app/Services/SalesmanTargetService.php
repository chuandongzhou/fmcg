<?php

namespace App\Services;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class SalesmanTargetService extends RedisService
{

    protected $field = 'target';

    protected $subName = 'target';

    /**
     * 获取业务员目标
     *
     * @param $salesmanId
     * @param $date
     * @return string
     */
    public function getTarget($salesmanId, $date)
    {
        $key = $this->getKey($this->subName . ':' . $date . ':' . $salesmanId);

        if (!$this->redis->exists($key)) {
            return 0;
        }
        return $this->redis->hget($key, $this->field);
    }

    /**
     * 设置业务员目标
     *
     * @param $salesmanId
     * @param $date
     * @param $value
     * @return int
     */
    public function setTarget($salesmanId, $date, $value)
    {
        $key = $this->getKey($this->subName . ':' . $date . ':' . $salesmanId);
        return $this->redis->hset($key, $this->field, $value);
    }

}