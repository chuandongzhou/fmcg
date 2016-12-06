<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Services\RedisService;


/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class CodeService
{
    /**
     * 发送短信验证码
     *
     */
    public function sendCode($templates,$field, $phone, $user_name)
    {
        $validateCodeConf = cons('validate_code');
        $redisKey = $validateCodeConf[$field]['pre_name'] . $user_name;
        $redis = Redis::connection();
        if ($redis->exists($redisKey)) {
            return array(
                'status' => false,
                'mes' => '短信发送过于频繁'
            );
        }
        $code = str_random($validateCodeConf['length']);

        $result = app('pushbox.sms')->send($templates, $phone, $code);
        if (empty($result)) {
            return array(
                'status' => false,
                'mes' => '发送失败,请重试'
            );
        } else {
            (new RedisService)->setRedis($redisKey, $code, $validateCodeConf[$field]['expire']);
            return array(
                'status' => true,
                'mes' => '发送成功'
            );
        }

    }

    /**
     * 验证验证码
     *
     */
    public function validateCode($field, $code, $user_name)
    {
        $validateCodeConf = cons('validate_code');
        $redisKey = $validateCodeConf[$field]['pre_name'] . $user_name;

        $redis = Redis::connection();
        if (!$redis->exists($redisKey) || $redis->get($redisKey) != $code) {
            return array(
                'status' => false,
                'mes' => '验证码错误'
            );
        }
        $redis->del($redisKey);
        return array(
            'status' => true,
            'mes' => '验证成功'
        );
    }
}