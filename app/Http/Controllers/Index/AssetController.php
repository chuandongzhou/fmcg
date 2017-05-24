<?php

namespace App\Http\Controllers\Index;

use App\Models\AssetApply;
use App\Services\AssetService;
use Illuminate\Http\Request;

use App\Http\Requests;

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


    public function __construct()
    {
        $this->shop = auth()->user()->shop;
        $this->assetService = new AssetService();
    }

    /**
     * 未使用资产
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getUnused(Request $request)
    {
        $data = $request->only('start_at', 'end_at', 'name');
        $assets = $this->shop->asset();
        $assetName = [];
        $this->assetService->getShopAsset()->each(function ($asset) use (&$assetName) {
            $assetName[] = $asset->name;
        });
        $result = $this->assetService->getDataByCondition($assets, $data);
        return view('index.asset.unused', [
            'assets' => $result->paginate(),
            'data' => $data,
            'assetName' => $assetName
        ]);
    }

    /**
     * 已使用资产
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getUsed(Request $request)
    {
        $data = $request->only('use_start_at','use_end_at','name','condition');
        $assetApply = $this->shop->assetApply()->where('asset_apply.status','>',0)->where(function ($query){
            $query->whereNotNull('use_date');
        });
        $assets = $this->assetService->getShopAsset();
        $result = $this->assetService->getDataByCondition($assetApply, $data);
        return view('index.asset.used', [
           'used' => $result->paginate(),
           'assets' => $assets,
           'data' => $data,
        ]);
    }
    
    public function getApply(Request $request)
    {
        $data = $request->only('start_at','end_at','asset','salesmen','status');
        $salesmens = $this->shop->salesmen;
        $assetApply = $this->shop->assetApply();
        $result = $this->assetService->getDataByCondition($assetApply, $data);
        return view('index.asset.apply', [
            'assetApply' => $result->paginate(),
            'salesmens' => $salesmens,
            'data' => $data
        ]);
    }

    public function getApplyDetail($applyId = 0)
    {
        if (!$applyId) return '';
        $assetApply = AssetApply::with(['asset', 'client'])->find($applyId);
        return view('index.asset.apply-detail', [
            'assetApply' => $assetApply
        ]);
    }
}
