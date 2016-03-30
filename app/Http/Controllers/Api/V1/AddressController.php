<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/10
 * Time: 16:46
 */
namespace App\Http\Controllers\Api\V1;

use DB;
use Cache;
use Illuminate\Http\Request;

class AddressController extends Controller
{

    /**
     * 根据区id 获取街道
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function street(Request $request)
    {
        $addressList = [];
        $cacheConf = cons('address.districts.cache');
        $streetId = $request->input('pid');
        if (!intval($streetId)) {
            return $this->success($addressList);
        }
        $cacheKey = $cacheConf['pre_name'] . $streetId;
        if (Cache::has($cacheKey)) {
            $addressList = Cache::get($cacheKey);
        } else {
            $addressList = DB::table('address')->where('pid', $streetId)->lists('name', 'id');
            Cache::forever($cacheKey, $addressList);
        }
        return $this->success($addressList);
    }

    /**
     * 根据省名获取省id
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getProvinceIdByName(Request $request)
    {
        $provinceName = str_replace('省', '', $request->input('name'));
        $cacheConf = cons('address');

        if (!$provinceName) {
            $provinceId = $cacheConf['default_province'];
        } else {
            $provinces = [];
            $cacheKey = $cacheConf['provinces']['cache']['name'];
            if (Cache::has($cacheKey)) {
                $provinces = Cache::get($cacheKey);
            } else {
                $provinces = DB::table('address')->where('pid', 1)->lists('name', 'id');
                Cache::forever($cacheKey, $provinces);
            }

            $provinceId = $this->_getProvinceId($provinces, $provinceName, $cacheConf['default_province']);
        }
        return $this->success(['provinceId' => $provinceId]);
    }

    /**
     * 获取省id
     *
     * @param $provinces
     * @param $name
     * @param $default
     * @return int|string
     */
    private function _getProvinceId($provinces, $name, $default)
    {
        if (empty($provinces)) {
            return $default;
        }
        foreach ($provinces as $id => $pName) {
            if (strstr($pName, $name)) {
                return $id;
            }
        }
    }
}