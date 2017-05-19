<?php

namespace App\Http\Controllers\Api\V1\ChildUser;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Api\v1\CreateTempleteHeaderRequest;
use App\Models\OrderTemplete;


class TempleteController extends Controller
{

    /**
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function index()
    {
        $tempHeaders = child_auth()->user()->shop->orderTempletes;

        return $this->success(compact('tempHeaders'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Api\v1\CreateTempleteHeaderRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function store(CreateTempleteHeaderRequest $request)
    {
        $attributes = $request->all();

        $shop = child_auth()->user()->shop;

        return $shop->orderTempletes()->create($attributes)->exists ? $this->success('添加店铺模版成功') : $this->error('添加店铺模版时出现问题');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  CreateTempleteHeaderRequest $request
     * @param  int $id
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function update(CreateTempleteHeaderRequest $request, $id)
    {
        $attributes = $request->all();

        $shop = child_auth()->user()->shop;

        $templete = $shop->orderTempletes()->find($id);

        if (is_null($templete)) {
            return $this->error('模板不存在');
        }

        return $templete->fill($attributes)->save() ? $this->success('修改店铺模版成功') : $this->error('修改店铺模版时出现问题');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function destroy($id)
    {
        if (OrderTemplete::where(['id' => $id, 'shop_id' => child_auth()->user()->shop_id])->delete()) {
            return $this->success('删除店铺模版成功');
        }
        return $this->error('删除店铺模版时出现问题');
    }

    /**
     * @param $id
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function show($id)
    {

        $templete = child_auth()->user()->shop->orderTempletes()->find($id);

        return $this->success(compact('templete'));
    }

    /**
     * 默认店铺模版
     *
     * @param $templeteId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function default($templeteId)
    {
        $shop = child_auth()->user()->shop;

        if (!($templete = $shop->orderTempletes()->find($templeteId))) {
            return $this->error('店铺模版不存在');
        }

        if ($templete->fill(['is_default' => 1])->save()) {
            $shop->orderTempletes()->where('id', '<>', $templeteId)->where('is_default',
                1)->update(['is_default' => 0]);
            return $this->success('选择店铺模版成功');
        }
        return $this->error('店铺模版不存在');
    }
}
