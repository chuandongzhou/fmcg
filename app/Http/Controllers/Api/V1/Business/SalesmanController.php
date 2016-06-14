<?php

namespace App\Http\Controllers\Api\V1\Business;

use App\Http\Controllers\Api\V1\Controller;
use App\Models\Salesman;

use App\Http\Requests;

class SalesmanController extends Controller
{
    protected $shop;

    public function __construct()
    {
        $this->shop = auth()->user()->shop;
    }

    public function store(Requests\Api\v1\CreateSalesManRequest $request)
    {
        $attributes = $request->all();
        if ($this->shop->salesmen()->create($attributes)->exists) {
            return $this->success('添加业务员成功');
        }
        return $this->error('添加业务员是出现错误');
    }

    /**
     * @param $salesman
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function show($salesman)
    {
        return $this->success(['salesman' => $salesman]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Api\V1\UpdateSalesmanRequest $request
     * @param $salesman
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function update(Requests\Api\V1\UpdateSalesmanRequest $request, $salesman)
    {
        $attributes = $request->all();
        if ($salesman->fill($attributes)->save()) {
            return $this->success('保存业务员成功');
        }
        return $this->error('保存业务员是出现错误');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Salesman $salesman
     * @return \Illuminate\Http\Response
     */
    public function destroy($salesman)
    {
        return $salesman->delete() ? $this->success('删除业务员成功') : $this->error('删除业务员失败');
    }
}
