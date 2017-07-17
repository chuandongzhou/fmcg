<?php

namespace App\Http\Controllers\Api\v1\Business;

use App\Services\AssetService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Api\V1\Controller;
use Gate;

class AssetController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->middleware('maker_salesman');
        $this->user = salesman_auth()->user();
    }

    /**
     * 厂家业务员获取厂家资产列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //获取启用且数量大于零的资产
        $assets = $this->user->maker->asset()->where('status', '1')->where('quantity', '>', 0)->get()->each(function (
            $item
        ) {
            $item->setHidden(['shop_id', 'updated_at', 'status', 'created_at']);
        });
        return $this->success(['assets' => $assets]);
    }

    /**
     * 资产申请列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function applyList(Request $request)
    {
        $data = $request->all();
        $applyAssetModel = $this->user->applyAsset()->with(['client', 'client.businessAddress', 'asset']);
        $applyLists = (new AssetService())->getDataByCondition($applyAssetModel, $data)->orderBy('created_at',
            'DESC')->get()->each(function ($item) {
            $item->setAppends(['pass_date']);
        });
        return $this->success(compact('applyLists'));
    }

    /**
     * 资产申请
     *
     * @param \App\Http\Requests\Api\v1\AssetApplyRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function applyCreate(Requests\Api\v1\AssetApplyRequest $request)
    {
        $data = $request->all();
        $customer = $this->user->customers->pluck('id')->toArray();
        if (!in_array($data['client_id'], $customer)) {
            return $this->error('未知的客户!');
        }
        if ($assetApply = $this->user->applyAsset()->create($data)) {
            (new AssetService)->createLog($assetApply, 'apply');
            return $this->success('操作成功,请等待审核!');
        }
        return $this->error('操作失败');
    }

    /**
     * 添加使用时间
     *
     * @param $assetApply
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function addUseDate($assetApply, Request $request)
    {
        $date = $request->input('date');
        $is_date = strtotime($date) ? strtotime($date) : false;
        if ($is_date == false) {
            return $this->error('不是正确的日期格式');
        }
        if ($assetApply->status != cons('asset_apply.status.approved')) {
            return $this->error('审核未通过!');
        }
        if ($assetApply->use_date) {
            return $this->error('已经开始使用!');
        }
        if ($assetApply->fill(['use_date' => $date])->save()) {
            (new AssetService())->createLog($assetApply, 'use');
            return $this->success('操作成功');
        }
        return $this->error('操作失败');
    }

    /**
     * 删除
     *
     * @param $apply
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function applyDelete($apply)
    {
        if (Gate::forUser(salesman_auth()->user())->denies('validate-salesman-assetApply', $apply)) {
            return $this->error('没有权限!');
        }
        return $apply->delete() ? $this->success('删除成功') : $this->error('删除失败');
    }

}
