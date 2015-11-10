<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

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
        $redis = Redis::connection();
        $android = $request->input('android_url');
        $ios = $request->input('ios_url');
        $android ? $redis->set('android_url', $android) : '';
        $ios ? $redis->set('ios_url', $ios) : '';

        return $this->success('保存成功');
    }
}
