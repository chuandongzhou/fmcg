<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/6
 * Time: 17:57
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\v1\CreateInterestsRequest;
use App\Models\Category;
use App\Models\Shop;
use Illuminate\Http\Request;

class LikeController extends Controller
{

    public function __construct()
    {
        $this->middleware('forbid:maker');
    }
    /**
     * 收藏
     *
     * @param \App\Http\Requests\Api\v1\CreateInterestsRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function putInterests(CreateInterestsRequest $request)
    {
        $type = $request->input('type');
        $likeTypes = array_keys(cons('like.type'));
        $relate = 'like' . ucfirst(in_array($type, $likeTypes) ? $type : head($likeTypes));
        $likeModels = cons('like.model');
        $model = array_get($likeModels, $type, head($likeModels));

        $user = auth()->user();
        $id = $request->input('id');
        $status = $request->input('status');

        if ($status) {
            $shop = $user->$relate()->where('id', $id)->first();
            if (!$shop) {
                $shop = $model::find($id);
                $shop ? $user->$relate()->attach($id) : null;
            }

            return $this->success($shop);
        } else {
            $user->$relate()->detach($id);
            return $this->success(null);
        }
    }

    /**
     * 批量删除
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function putBatch(Request $request)
    {
        $type = $request->input('type');
        $likeTypes = array_keys(cons('like.type'));
        $relate = 'like' . ucfirst(in_array($type, $likeTypes) ? $type : head($likeTypes));
        if (empty($request->input('id'))) {
            return $this->error($type == 'goods' ? '请选择需要删除的商品' : '请选择需要删除的店铺');
        }
        auth()->user()->$relate()->detach($request->input('id'));
        return $this->success(null);


    }

    /**
     * 获取收藏的店铺信息
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function postShops(Request $request)
    {
        $data = $request->all();
        $shops = auth()->user()->likeShops()->with('images')->select(['id', 'name', 'min_money']);
        if (isset($data['name'])) {
            $shops = $shops->where('name', 'like', '%' . $data['name'] . '%');
        }
        if ($request->has('province_id')) {
            $shops = $shops->OfDeliveryArea($data);
        }
        $shops = $shops->paginate();
        $shops->each(function (Shop $shop) {
            $shop->setAppends(['logo_url', 'user_type']);
        });
        return $this->success(['shops' => $shops->toArray()]);
    }

    /**
     * 获取收藏的商品信息
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postGoods(Request $request)
    {
        $data = $request->all();
        $goods = auth()->user()->likeGoods();

        if (isset($data['name'])) {
            $goods->where('name', 'like', '%' . $data['name'] . '%');
        }
        if (!empty($data['province_id'])) {
            $goods->OfDeliveryArea($data);
        }

        return $this->success([
            'goods' => $goods->active()->paginate()->toArray(),
        ]);
    }
}