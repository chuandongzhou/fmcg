<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/6
 * Time: 17:57
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\v1\CreateInterestsRequest;
use Illuminate\Http\Request;

class LikeController extends Controller
{

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
     * 获取收藏的店铺信息
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function postShops(Request $request)
    {
        $data = $request->all();
        $shops = auth()->user()->likeShops()->select(['id', 'name', 'min_money']);
        if (isset($data['name'])) {
            $shops = $shops->where('name', 'like', '%' . $data['name'] . '%');
        }
        if ($request->has('province_id')) {
            $shops = $shops->OfDeliveryArea($data);
        }
        dd($shops->get()->toArray());
        return $this->success(['shops' => $shops->simplePaginate()->toArray()]);
    }
}