<?php

namespace App\Http\Controllers\Api\V1\Personal;


use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Api\v1\CreateAdvertRequest;

class ModelController extends Controller
{

    protected $shop;

    public function __construct()
    {
        $this->shop = auth()->user()->shop;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Api\v1\CreateAdvertRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response|\WeiHeng\Responses\Apiv1Response
     */
    public function postAdvert(CreateAdvertRequest $request)
    {
        $goodsId = $request->input('goods_id');
        $promoteinfo = $request->input('promoteinfo');
        $attributes = $request->except(['identity','goods_id','promoteinfo']);
        $attributes['type'] = cons('advert.type.'.$request->input('identity'));
        $attributes['end_at'] = $attributes['end_at'] ?: null;
        $attributes['url'] = $request->input('identity')=='shop'?url('goods/'.$goodsId):$promoteinfo;
        if ($this->shop->adverts()->create($attributes)->exists) {
            return $this->success('添加广告成功');
        }

        return $this->error('添加广告时遇到错误');
    }

    /**
     * 广告修改
     *
     * @param \App\Http\Requests\Api\v1\CreateAdvertRequest $request
     * @param $advertId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function putAdvert(CreateAdvertRequest $request, $advertId)
    {
        $advert = $this->shop->adverts()->find($advertId);
        if (is_null($advert)) {
            return $this->error('广告不存在');
        }

        $goodsId = $request->input('goods_id');
        $promoteinfo = $request->input('promoteinfo');
        $attributes = $request->except(['identity','goods_id','promoteinfo']);
        $attributes['type'] = cons('advert.type.'.$request->input('identity'));
        $attributes['url'] = $request->input('identity')=='shop'?url('goods/'.$goodsId):$promoteinfo;
        if ($advert->fill($attributes)->save()) {
            return $this->success('修改广告成功');
        }

        return $this->error('修改广告时遇到错误');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $advertId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function deleteAdvert($advertId)
    {
        $advert = $this->shop->adverts()->find($advertId);
        if (is_null($advert)) {
            return $this->error('广告不存在');
        }
        if ($advert->delete()) {
            return $this->success('删除成功');
        }

        return $this->error('删除失败');
    }
}
