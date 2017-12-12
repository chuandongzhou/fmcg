<?php

namespace App\Http\Controllers\Index;

use App\Models\Attr;
use App\Models\Goods;
use App\Models\OrderGoods;
use App\Services\CategoryService;
use App\Services\GoodsService;
use App\Http\Requests;
use App\Http\Requests\Index\UpdateGoodsRequest;
use App\Services\AttrService;
use Illuminate\Http\Request;
use DB;
use Gate;
use Illuminate\Support\Facades\Response;


class MyGoodsController extends Controller
{

    public function __construct()
    {
        $this->middleware('forbid:retailer');
    }

    protected $sort = [
        'name',
        'price',
        'new'
    ];

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $gets = $request->all();
        $data = $this->_formatGet($gets);
        $shop = auth()->user()->shop;

        $result = GoodsService::getShopGoods($shop, $data, ['mortgageGoods','promoGoods','images','shop']);
        $myGoods = $result['goods']->orderBy('updated_at', 'DESC')->paginate();

        $cateName = [];

        foreach (CategoryService::getCategories() as $key => $category) {

            foreach ($myGoods as $goods) {
                if ($goods->cate_level_1 && $key == $goods->cate_level_1) {
                    $cateName[$goods->id]['cate_level_1'] = $category['name'];
                }
                if ($goods->cate_level_2 && $key == $goods->cate_level_2) {
                    $cateName[$goods->id]['cate_level_2'] = $category['name'];
                }
                if ($goods->cate_level_3 && $key == $goods->cate_level_3) {
                    $cateName[$goods->id]['cate_level_3'] = $category['name'];
                }

            }
        }
        $cateId = isset($data['category_id']) ? $data['category_id'] : -1;
        $categories = CategoryService::formatShopGoodsCate($shop, $cateId, false);

        return view('index.my-goods.index', [
            'goods' => $myGoods,
            'goodsCateName' => $cateName,
            'categories' => $categories,
            'attrs' => $result['attrs'],
            'searched' => $result['searched'],
            'moreAttr' => $result['moreAttr'],
            'get' => $gets,
            'data' => $data
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        //判断有没有交保证金
        /*if (!$user->deposit) {
             return $this->error('添加商品前请先缴纳保证金', url('personal/sign'));
         }*/
        $orderGoodsId = $request->input('orderGoods');
        //默认加入店铺配送地址
        $shop = $user->shop()->with(['deliveryArea'])->first();
        $shopDelivery = $shop->deliveryArea->each(function ($area) {
            $area->id = '';
        });
        $goods = new Goods;
        //店铺配送地址
        $goods->shopDeliveryArea = $shopDelivery;
        if (!empty($orderGoodsId)) {
            $orderGoods = OrderGoods::with('goods')->find($orderGoodsId);
            $goods->bar_code = $orderGoods->goods->bar_code;
            $goods->name = $orderGoods->goods->name;
            $goods->goodsPieces = $orderGoods->goods->goodsPieces;
            $goods->orderGoodsId = $orderGoodsId;
        }
        return view('index.my-goods.goods', [
            'goods' => $goods,
            'attrs' => [],
        ]);
    }

    /**
     * Show the form for creating some new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function batchCreate()
    {
        return view('index.my-goods.batch-create');
    }


    /**
     * Display the specified resource.
     *
     * @param $goods
     * @return \Illuminate\View\View
     */
    public function show($goods)
    {
        if (Gate::denies('validate-my-goods', $goods)) {
            return redirect(url('my-goods'));
        }
        $attrs = (new AttrService())->getAttrByGoods($goods);
        /* $coordinate = $goods->deliveryArea->each(function ($area) {
             $area->coordinate;
         });*/
        return view('index.my-goods.detail',
            ['goods' => $goods, 'attrs' => $attrs/*, 'coordinates' => $coordinate->toJson()*/]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $goods
     * @return Response
     */
    public function edit($goods)
    {
        if (Gate::denies('validate-my-goods', $goods)) {
            return redirect(url('my-goods'));
        }
        $goodsAttr = $goods->attr;
        //获取所有标签
        $attrGoods = [];
        foreach ($goodsAttr as $attr) {
            $attrGoods[$attr->pid] = $attr->pivot->toArray();
        }
        $attrService = new AttrService();
        $attrResults = $attrService->getAttrsByCategoryId($goods->category_id);

        //店铺配送地址
        $shop = auth()->user()->shop()->with(['deliveryArea'])->first();
        $shopDelivery = $shop->deliveryArea;
        $goods->shopDeliveryArea = $shopDelivery;

        $attrResults = $attrService->format($attrResults);

        return view('index.my-goods.goods', [
            'goods' => $goods,
            'attrs' => $attrResults,
            'attrGoods' => $attrGoods,
        ]);
    }

    /**
     * 模板下载
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function downloadTemplate()
    {
        $userType = auth()->user() ? auth()->user()->type : cons('user.type.wholesaler');
        $fileName = array_search($userType, cons('user.type'));
        $file = public_path('images/') . $fileName . '.xls';
        if (is_file($file)) {
            return Response::download($file);
        }
        return $this->error('文件不存在');
    }

    /**
     * 格式化查询每件
     *
     * @param $get
     * @return array
     */
    private function _formatGet($get)
    {
        $data = [];
        foreach ($get as $key => $val) {
            if (starts_with($key, 'attr_')) {
                $pid = explode('_', $key)[1];
                $data['attr'][$pid] = $val;
            } else {
                $data[$key] = $val;
            }
        }

        return $data;
    }

}
