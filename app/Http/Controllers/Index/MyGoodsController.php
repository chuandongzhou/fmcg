<?php

namespace App\Http\Controllers\Index;

use App\Models\Attr;
use App\Models\Category;
use App\Models\Goods;
use App\Services\GoodsService;
use DB;
use Gate;


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

        $result = GoodsService::getGoodsBySearch($data);

        $attrs = $result['attrs'];
        $defaultAttrName = cons()->valueLang('attr.default');

        $searched = []; //保存已搜索的标签
        $moreAttr = []; //保存扩展的标签

        // 已搜索的标签
        foreach ($attrs as $key => $attr) {
            if (!empty($data['attr']) && in_array($attr['attr_id'], array_keys($data['attr']))) {
                $searched[$attr['attr_id']] = array_get($attr['child'], $data['attr'][$attr['attr_id']])['name'];
                unset($attrs[$key]);
            } elseif (!in_array($attr['name'], $defaultAttrName)) {
                $moreAttr[$key] = $attr;
                unset($attrs[$key]);
            }
        }
        return view('index.my-goods.index',
            [
                'goods' => $result['goods']->paginate(),
                'categories' => $result['categories'],
                'attrs' => $attrs,
                'searched' => $searched,
                'moreAttr' => $moreAttr,
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

        $attrs = (new AttrService())->getAttrByGoods($goods);
        return view('index.my-goods.detail', ['goods' => $goods, 'attrs' => $attrs]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $goods
     * @return Response
     */
    public function edit($goods)
    {
        $goodsAttr = $goods->attr;
        //获取所有标签
        $attrGoods = [];
        foreach ($goodsAttr as $attr) {
            $attrGoods[$attr->pid] = $attr->pivot->toArray();
        }

        $attrIds = array_pluck($attrGoods, 'attr_pid');
        $attrResults = Attr::select(['attr_id', 'pid', 'name'])
            ->where('category_id', $goods->category_id)
            ->where(function ($query) use ($attrIds) {
                $query->whereIn('attr_id', $attrIds)
                    ->orWhere(function ($query) use ($attrIds) {
                        $query->whereIn('pid', $attrIds);
                    });
            })
            ->get()->toArray();
        $attrResults = (new AttrService($attrResults))->format();
        return view('index.my-goods.goods', [
                'goods' => $goods,
                'attrs' => $attrResults,
                'attrGoods' => $attrGoods
            ]
        );
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
