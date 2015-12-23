<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/10
 * Time: 16:46
 */
namespace App\Http\Controllers\Api\V1;

use DB;
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
        $addressList = DB::table('address')->where('pid', $request->input('pid'))->lists('name', 'id');
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
        if (!$provinceName) {
            return $this->error('获取省id错误');
        }
        $provinceId = DB::table('address')->where('name', 'LIKE', '%' . $provinceName . '%')->where('pid',
            1)->pluck('id');
        return $provinceId ? $this->success(['provinceId' => $provinceId]) : $this->error('获取省id错误');
    }
}