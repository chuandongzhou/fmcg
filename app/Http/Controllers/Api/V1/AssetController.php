<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Asset;
use App\Models\AssetApplyLog;
use App\Models\Salesman;
use App\Models\Shop;
use App\Services\AssetService;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Gate;

class AssetController extends Controller
{
    protected $user;
    protected $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }
    /**
     * 添加资产
     *
     * @param \App\Http\Requests\Api\v1\CreateAssetRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postAdd(Requests\Api\v1\CreateAssetRequest $request)
    {
        $data = $request->all();
        if (auth()->user()->shop->asset()->create($data)) {
            return $this->success('添加成功');
        }
        return $this->error('添加失败');
    }


    /**
     * 资产状态切换
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function putStatusChange($asset)
    {
        $asset = Asset::find($asset);
        if (Gate::denies('validate-shop-asset', $asset)) {
            return $this->forbidden('权限不足');
        }
        $asset->status = ($asset->status == 0) ? 1 : 0;
        if ($asset->save()) {
            return $this->success('操作成功');
        }
        return $this->error('操作失败');
    }

    /**
     * 资产修改
     *
     * @param \App\Http\Requests\Api\v1\CreateAssetRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postModify(Requests\Api\v1\CreateAssetRequest $request)
    {
        $id = $request->input('id');
        $asset = Asset::find($id);
        if (Gate::denies('validate-shop-asset', $asset)) {
            return $this->forbidden('权限不足');
        }
        $data = $request->except('id');
        if ($asset->fill($data)->save()) {
            return $this->success('操作成功');
        }
        return $this->error('操作失败');
    }

    /**
     * 同意申请
     *
     * @param \Illuminate\Http\Request $request
     * @param $assetReview
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function review(Request $request, $assetApply)
    {
        if (Gate::denies('validate-shop-assetApply', $assetApply)) {
            return $this->forbidden('权限不足');
        }
        if ($assetApply->status) {
            return $this->success('已审核');
        }
        if ($assetApply->asset->quantity < $assetApply->quantity) {
            return $this->error('资产不足');
        }
        if ($assetApply->update($request->only('status'))) {
            $assetApply->asset->quantity -= $assetApply->quantity;
            $assetApply->asset->save();
            $this->assetService->createLog($assetApply, 'review');
            return $this->success('操作成功');
        }
        return $this->error('操作失败');
    }

    /**
     * 拒绝申请
     *
     * @param $assetReview
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function delete($assetApply)
    {
        if (Gate::denies('validate-shop-assetApply', $assetApply)) {
            return $this->forbidden('权限不足');
        }
        if ($assetApply->delete()) {
            return $this->success('操作成功');
        }
        return $this->error('操作失败');
    }

    /**
     * 修改<申请数量、备注、使用日期>
     *
     * @param \Illuminate\Http\Request $request
     * @param $assetReview
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function modify(Requests\Api\v1\UpdateAssetApplyRequest $request, $assetApply)
    {
        if (Gate::denies('validate-shop-assetApply', $assetApply)) {
            return $this->forbidden('权限不足');
        }
        $data = array_filter($request->only('quantity', 'apply_remark', 'use_date'));
        $quantity = array_get($data, 'quantity');
        if($quantity && $quantity > $assetApply->asset->quantity){
            return $this->error('数量不足');
        }
        if ($assetApply->update($data)) {
            if (array_get($data, 'use_date')) {
                $this->assetService->createLog($assetApply, 'use');
            }
            return $this->success('操作成功');
        }
        return $this->error('操作失败');
    }
}
