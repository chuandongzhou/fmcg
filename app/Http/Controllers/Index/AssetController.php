<?php

namespace App\Http\Controllers\Index;

use App\Models\AssetApply;
use App\Models\Salesman;
use App\Services\AssetService;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Gate;

/**
 * 资产管理
 * Class AssetController
 *
 * @package App\Http\Controllers\Index
 */
class AssetController extends Controller
{
    protected $shop;
    protected $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->shop = auth()->user()->shop;
        $this->assetService = $assetService;
    }

    /**
     * 未使用资产
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getUnused(Request $request)
    {
        $data = $request->only('start_at', 'end_at', 'asset');
        $assets = $this->shop->asset();
        $result = $this->assetService->getDataByCondition($assets, $data);
        return view('index.asset.unused', [
            'assets' => $result->paginate(),
            'data' => $data,
            'assetNames' => $this->_getAssetName()
        ]);
    }

    /**
     * 已使用资产
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getUsed(Request $request)
    {
        $data = $request->only('use_start_at', 'use_end_at', 'asset', 'condition');
        $assetApply = $this->shop->assetApply()->with([
            'client.businessAddress',
            'log',
            'salesman',
            'asset'
        ])->where('asset_apply.status', '>', 0)->whereNotNull('use_date');
        $result = $this->assetService->getDataByCondition($assetApply, $data);
        return view('index.asset.used', [
            'used' => $result->paginate(),
            'assetNames' => $this->_getAssetName(),
            'data' => $data,
        ]);
    }

    /**
     * 申请列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getApply(Request $request)
    {
        $data = $request->only('start_at', 'end_at', 'asset', 'salesmen', 'status');
        $salesmens = $this->shop->salesmen;
        $assetApply = $this->shop->assetApply()->with(['salesman', 'client', 'asset']);
        $result = $this->assetService->getDataByCondition($assetApply, $data);
        return view('index.asset.apply', [
            'assetApply' => $result->paginate(),
            'salesmens' => $salesmens,
            'data' => $data
        ]);
    }

    /**
     * 申请详情
     *
     * @param int $applyId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getApplyDetail($applyId = 0)
    {
        $assetApply = AssetApply::with(['asset', 'client'])->find($applyId);
        if (is_null($assetApply) || Gate::denies('validate-shop-assetApply', $assetApply)) {
            return view('errors.404');
        };
        return view('index.asset.apply-detail', [
            'assetApply' => $assetApply
        ]);
    }

    /**
     * 获取所有资产名
     *
     * @return array
     */
    public function _getAssetName():array
    {
        return $this->shop->asset()->get(['id', 'name'])->toArray();
    }
}
