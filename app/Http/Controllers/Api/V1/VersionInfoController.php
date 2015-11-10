<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\VersionRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class VersionInfoController extends Controller
{
    /**
     * 获取最新版信息
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getIndex(Request $request)
    {
        $redis = Redis::connection();

        return $this->success([
            'version' => VersionRecord::where('type', $request->input('type'))->orderBy('created_at', 'DESC')->first(),
            'android_url' => $redis->get('android_url'),
            'ios_url' => $redis->get('ios_url')
        ]);
    }
}
