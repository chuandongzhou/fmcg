<?php

namespace App\Http\Controllers\Admin;


use App\Services\RedisService;
use Illuminate\Http\Request;

class CacheController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {

        return view('admin.cache.index');
    }

    public function postDelete(Request $request)
    {
        $key = $this->_getCacheKey($request->input('key'));
        $redisService = new RedisService;
        $keys = $redisService->keys($key);
        return $this->success($redisService->del($keys, false));

    }

    /**
     * 获取key
     *
     * @param $key
     * @return string
     */
    private function _getCacheKey($key)
    {
        return $key ? $key . ':*' : '*';
    }

}
