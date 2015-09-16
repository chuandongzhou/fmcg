<?php

namespace App\Http\Controllers\Index;

use App\Models\Attr;
use App\Models\Category;
use App\Models\Goods;
use DB;


use App\Http\Requests;
use App\Services\AttrService;
use Illuminate\Http\Request;

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
     * @return Response
     */
    public function index(Request $request)
    {
        $gets = $request->all();
        $data = array_filter($this->_formatGet($gets));
        $attrs = [];

        $categories = Category::orderBy('level', 'asc')->select('name', 'level', 'id')->get();
        $goods = Goods::with('images')->where('shop_id', 1);
        //排序
        if (isset($data['sort']) && in_array($data['sort'], $this->sort)) {
            $goods = $goods->{'Order' . ucfirst($data['sort'])}();
        }
        // 省市县
        if (isset($data['province_id'])) {
            $goods = $goods->OfDeliveryArea($data);
        }
        // 名称
        if (isset($data['name'])) {
            $goods = $goods->where('name', 'like', '%' . $data['name'] . '%');
        }
        if (isset($data['category_id'])) {
            //分类最高位为层级 后面为categoryId
            $level = substr($data['category_id'], 0, 1);
            $categoryId = substr($data['category_id'], 1);
            $attrs = (new AttrService([]))->getAttrByCategoryId($categoryId);
            $goods = $goods->OfCategory($categoryId , $level);
        }

        // 标签
        if (isset($data['attr'])) {
            $goods = $goods->OfAttr($data['attr']);
        }
        return view('index.my-goods.index',
            [
                'goods' => $goods->paginate(),
                'categories' => $categories,
                'attrs' => $attrs,
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
        $firstCategory = Category::where('pid', 0)->pluck('id');
        $goods = new Goods;
        $goods->cate_level_1 = $firstCategory;
        return view('index.my-goods.goods', ['goods' => $goods, 'attrs' => []]);
    }


    /**
     * Display the specified resource.
     *
     * @param $goods
     * @return \Illuminate\View\View
     */
    public function show($goods)
    {
        if (!Gate::denies('validate-goods', $goods)) {
            abort(403);
        }
        return view('index.my-goods.detail', ['goods' => $goods]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $goods
     * @return Response
     */
    public function edit($goods)
    {
        //获取所有标签
        $attrGoods = [];
        foreach ($goods->attr as $attr) {
            $attrGoods[] = $attr->pivot->toArray();
        }
        $attrIds = array_pluck($attrGoods, 'attr_pid');
        $attrResults = Attr::select(['id', 'pid', 'name'])
            ->whereIn('id', $attrIds)
            ->orWhere(function ($query) use ($attrIds) {
                $query->whereIn('pid', $attrIds);
            })->get()->toArray();

        $attrResults = (new AttrService($attrResults))->format();
        return view('index.my-goods.goods', ['goods' => $goods, 'attrs' => $attrResults]);
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
