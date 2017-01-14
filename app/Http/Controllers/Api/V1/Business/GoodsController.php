<?php

namespace App\Http\Controllers\Api\V1\Business;

use App\Http\Controllers\Api\V1\Controller;
use App\Models\Salesman;
use App\Services\BusinessService;
use App\Services\CategoryService;
use App\Services\GoodsService;
use Carbon\Carbon;
use App\Http\Requests;
use App\Services\SalesmanTargetService;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use Gate;

class GoodsController extends Controller
{
    protected $shop;

    public function __construct()
    {
        $this->middleware('salesman.auth');
        $this->shop = salesman_auth()->user()->shop;
    }

    /**
     * 获取店铺商品分类
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function category()
    {
        $categories = CategoryService::formatShopGoodsCate($this->shop);
        return $this->success(['categories' => $categories]);
    }

    /**
     * 获取店铺商品
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function goods(Request $request)
    {
        $data = $request->all();

        $result = GoodsService::getShopGoods($this->shop, $data, ['images']);
        $goods = $result['goods']->active()->withGoodsPieces()->orderBy('id', 'DESC')->select([
            'id',
            'name',
            'bar_code',
            'price_retailer',
            'price_wholesaler',
            'pieces_retailer',
            'pieces_wholesaler',
        ])->paginate();
        $goods->each(function ($item) {
            $item->setAppends(['image_url']);
        });
        return $this->success(['goods' => $goods->toArray()]);
    }
}
