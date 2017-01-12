<?php

namespace App\Http\Requests\Api\v1;
use DB;
use Cache;


abstract class UserRequest extends Request
{

    public function authorize()
    {
        return auth()->check() || admin_auth()->check();
    }

    /**
     * 查询下级地址
     */
    public function lowerLevelAddress($pid){
        $cacheConf = cons('address.districts.cache');
        $streetId = $pid;
        $cacheKey = $cacheConf['pre_name'] . $streetId;
        if (Cache::has($cacheKey)) {
            $addressList = Cache::get($cacheKey);
        } else {
            $addressList = DB::table('address')->where('pid', $streetId)->lists('name', 'id');
            Cache::forever($cacheKey, $addressList);
        }

        return $addressList;

    }

}