<?php

namespace App\Services;

/**
 * Service基类
 *
 * @author     bant
 * @copyright  (C) 2011-2013 BPICMS
 * @license    http://www.chinabpi.com/license/
 * @version    1.0
 * @createTime 2013-4-26
 * @updateTime 2013-4-26
 */
class BaseService {

    protected static $error;

    public function __construct() {

    }

    /**
     * 返回错误信息
     *
     * @return mixed
     */
    public function getError() {
        return self::$error;
    }


    /**
     * 设置错误信息
     *
     * @param mixed $message
     */
    public function setError($message) {
        self::$error = $message;
    }

}