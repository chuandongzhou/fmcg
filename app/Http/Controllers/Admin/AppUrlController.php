<?php

namespace App\Http\Controllers\Admin;

use App\Services\RedisService;
use Illuminate\Http\Request;

class AppUrlController extends Controller
{
    /**
     * 保存下载地址
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function postAppUrl(Request $request)
    {
        $redis = new RedisService;
        $android = $request->input('android_url');
        $ios = $request->input('ios_url');
        $android ? $redis->setRedis('android_url', $android) : '';
        $ios ? $redis->setRedis('ios_url', $ios) : '';

        return $this->success('保存成功');
    }
}
