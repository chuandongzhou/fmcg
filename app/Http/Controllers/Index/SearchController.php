<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/10
 * Time: 10:56
 */
namespace App\Http\Controllers\Index;

use App\Models\Category;
use App\Models\Goods;
use App\Services\AttrService;
use App\Services\CategoryService;
use App\Services\GoodsService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $gets = $request->all();
        $data = array_filter($this->_formatGet($gets));

        $result = GoodsService::getGoodsBySearch($data, '>');

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
        return view('index.search.index',
            [
                'goods' => $result['goods']->paginate(),
                'categories' =>  $result['categories'],
                'attrs' => $attrs,
                'searched' => $searched,
                'moreAttr' => $moreAttr,
                'get' => $gets,
                'data' => $data
            ]);
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