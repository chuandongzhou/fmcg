<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/10
 * Time: 10:56
 */
namespace App\Http\Controllers\Index;

use App\Models\Goods;
use App\Services\AddressService;
use App\Services\GoodsService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $gets = $request->all();
        $data = array_filter($this->_formatGet($gets));

        //供应商暂时与批发商一致
        $type = auth()->user()->type;
        $type = $type <= cons('user.type.wholesaler') ? $type : cons('user.type.wholesaler');

        $goods = Goods::active()->with('images.image', 'shop')->select([
            'id',
            'name',
            'shop_id',
            'bar_code',
            'min_num_retailer',
            'min_num_wholesaler',
            'price_retailer',
            'price_wholesaler',
            'is_new',
            'is_promotion',
            'is_out',
            'cate_level_1',
            'cate_level_2'
        ])->where('user_type', '>', $type);

        $addressData = (new AddressService)->getAddressData();
        $data = array_merge($data, array_except($addressData, 'address_name'));
        $result = GoodsService::getGoodsBySearch($data, $goods);
        return view('index.search.index',
            [
                'goods' => $goods->paginate(),
                'categories' => $result['categories'],
                'attrs' => $result['attrs'],
                'searched' => $result['searched'],
                'moreAttr' => $result['moreAttr'],
                'get' => $gets,
                'keywords' => $this->_getKeywordsBySearch($result['categories'],
                    isset($data['name']) ? $data['name'] : null),
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
            if (starts_with($key, 'attr')) {
                if (isset(explode('_', $key)[1])) {
                    $pid = explode('_', $key)[1];
                    $data['attr'][$pid] = $val;
                }
            } else {
                $data[$key] = $val;
            }
        }
        return $data;
    }

    /**
     * 获取搜索关键词
     *
     * @param $categories
     * @param null $name
     * @return null|string
     */
    private function _getKeywordsBySearch($categories, $name = null)
    {
        if ($name) {
            return $name;
        }
        $keyword = '';
        foreach ($categories as $category) {
            if (isset($category['selected'])) {
                $keyword = $category['selected']['name'];
            }
        }
        return $keyword;
    }
}