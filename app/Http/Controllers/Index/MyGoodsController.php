<?php

namespace App\Http\Controllers\Index;

use App\Models\Attr;
use App\Models\Category;
use App\Models\Goods;
use App\Services\GoodsService;
use App\Http\Requests;
use App\Services\AttrService;
use Illuminate\Http\Request;
use DB;
use Gate;
use Illuminate\Support\Facades\Response;


class MyGoodsController extends Controller
{
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
        $data = array_filter($this->_formatGet($gets));
        $goods = auth()->user()->shop->goods()->with('images')->select([
            'id',
            'name',
            'bar_code',
            'sales_volume',
            'price_retailer',
            'price_wholesaler',
            'min_num_retailer',
            'min_num_wholesaler',
            'user_type',
            'is_new',
            'status',
            'is_promotion',
            'cate_level_1',
            'cate_level_2',
            'cate_level_3',
            'updated_at'
        ]);
        $result = GoodsService::getGoodsBySearch($data, $goods);
        $myGoods = $result['goods']->paginate();
        $cateIds = [];
        foreach ($myGoods as $item) {
            $cateIds[$item->id] = $item->category_id;
        }
        $cateName = Category::whereIn('id', $cateIds)->lists('name', 'id');

        return view('index.my-goods.index', [
            'goods' => $myGoods,
            'goodsCateName' => $cateName,
            'categories' => $result['categories'],
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
     * @return Response
     */
    public function create()
    {
        //默认加入店铺配送地址
        $shop = auth()->user()->shop()->with(['deliveryArea.coordinate'])->first();
        $shopDelivery = $shop->deliveryArea->each(function ($area) {
            $area->id = '';
            $area->coordinate;
        });
        $goods = new Goods;
        $goods->deliveryArea = $shopDelivery;
        return view('index.my-goods.goods', [
            'goods' => $goods,
            'attrs' => [],
            'coordinates' => $shopDelivery
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
        $attrs = (new AttrService())->getAttrByGoods($goods, true);
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

        $attrResults = Attr::select(['attr_id', 'pid', 'name'])->where('category_id',
            $goods->category_id)->get()->toArray();

        $attrResults = (new AttrService($attrResults))->format();

        $coordinates = $goods->deliveryArea->each(function ($area) {
            $area->coordinate;
        });
        return view('index.my-goods.goods', [
            'goods' => $goods,
            'attrs' => $attrResults,
            'attrGoods' => $attrGoods,
            'coordinates' => $coordinates
        ]);
    }

    /**
     * 模板下载
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function downloadTemplate()
    {
        $userType = auth()->user()->type;
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
