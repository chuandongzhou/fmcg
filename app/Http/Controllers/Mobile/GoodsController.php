<?php

namespace App\Http\Controllers\Mobile;

use App\Models\Goods;
use App\Services\AddressService;
use App\Services\CategoryService;
use Cache;
use Gate;
use Illuminate\Http\Request;

class GoodsController extends Controller
{
    /**
     * 商品列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\WeiHeng\Responses\IndexResponse
     */
    public function index(Request $request)
    {
        $data = $request->all();

        //供应商暂时与批发商一致
        $type = auth()->user()->type;
        $type = $type <= cons('user.type.wholesaler') ? $type : cons('user.type.wholesaler');

        $goods = Goods::active()->shopUser()->with('shop')->where('user_type', '>', $type);

        $addressData = (new AddressService)->getAddressData();
        $address = array_except($addressData, 'address_name');

        // 名称
        if ($name = array_get($data, 'name')) {
            $cachePre = cons('goods.cache.keywords_pre');

            $cacheKey = $cachePre . 'sort';
            $keywords = Cache::get($cacheKey);
            $keywords[(string)$name] = isset($keywords[$name]) ? $keywords[$name] + 1 : 1;
            Cache::forever($cacheKey, $keywords);

            $goods->where('name', 'like', '%' . $name . '%')->get();
        }
        $cateName = '';

        if ($categoryId = array_get($data, 'category_id')) {
            //分类最高位为层级 后面为categoryId
            $level = substr($categoryId, 0, 1);
            $cateId = substr($categoryId, 1);
            $categories = CategoryService::getCategories();
            $cateName = array_get($categories, $cateId) ? array_get($categories, $cateId)['name'] : '';
            $goods->ofCategory($cateId, $level);
        }

        // 省市
        if (array_get($address, 'province_id')) {
            $goods->ofDeliveryArea(array_filter($address));
        }
        $goods = $goods->hasPrice()->ofCommonSort()->paginate();

        if ($request->ajax()) {
            $goods = $goods->each(function ($item) {
                $item->setAppends(['image_url', 'pieces', 'price']);
            })->toArray();

            return $this->success(compact('goods'));
        }


        return view('mobile.goods.index', compact('name', 'goods', 'cateName'));
    }

    public function detail($goods)
    {
        if (Gate::denies('validate-goods', $goods)) {
            return $this->forbidden('权限不足');
        }

        return view('mobile.goods.detail', compact('goods'));
    }

}
