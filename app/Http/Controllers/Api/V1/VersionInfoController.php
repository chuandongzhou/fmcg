<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\VersionRecord;
use App\Services\RedisService;
use Illuminate\Http\Request;

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
        $redis = new RedisService();

        $type = $request->input('type', 1);

        $deviceName = array_search($type, cons('push_device'));

        return $this->success([
            'record' => VersionRecord::where('type', $type)->orderBy('id', 'DESC')->first(),
            'download_url' => $redis->get('app-link:' . $deviceName),
        ]);
    }

    /**
     * 保存下载地址
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function postAppUrl(Request $request)
    {
        $redis = new RedisService;
        $data = $request->only('android', 'ios', 'delivery', 'business');

        $data = array_filter($data);

        foreach ($data as $key => $item) {
            $redisKey = 'app-link:' . $key;
            $redis->del($redisKey);
            $redis->setRedis($redisKey, $item);
        }

        return $this->success('保存成功');
    }
}
