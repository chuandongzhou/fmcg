<?php

namespace App\Http\Controllers\Mobile;


use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function goods(Request $request)
    {
        $goods = auth()->user()->likeGoods()->select(['id', 'bar_code', 'name'])->active();
        if (isset($data['name'])) {
            $goods->where('name', 'like', '%' . $data['name'] . '%');
        }
        $goods = $goods->paginate();

        if ($request->ajax()) {
            $goods = $goods->each(function ($item) {
                $item->setAppends(['image_url']);
            })->toArray();
            return $this->success(compact('goods'));
        }

        return view('mobile.like.goods', compact('goods'));
    }

    public function shops(Request $request)
    {
        $shops = auth()->user()->likeShops()->with('logo')->select(['id', 'name', 'min_money']);
        if (isset($data['name'])) {
            $shops = $shops->where('name', 'like', '%' . $data['name'] . '%');
        }
        $shops = $shops->paginate();
        $shops->each(function ($shop) {
            $shop->setAppends(['logo_url', 'sales_volume', 'user_type']);
        });

        if ($request->ajax()) {
            return $this->success($this->_buildShopHtml($shops));
        }

        return view('mobile.like.shops', compact('shops'));
    }


    /**
     *  收藏店铺加载更多html
     *
     * @param $shops
     * @return array
     */
    public function _buildShopHtml($shops)
    {
        $html = '';
        foreach ($shops as $shop) {
            $html .= '<div class="col-xs-12 clearfix shop-list-item">' .
                '       <img src="' . $shop->logo_url . '" class="pull-left"/>' .
                '       <div class="pull-left opera-panel">' .
                '           <div class="shop-name">' .
                '               <b>' . $shop->name . '</b>' .
                '               <span class="prompt">（' . cons()->valueLang('user.type',
                    $shop->user_type) . '）</span>' .
                '           </div>' .
                '            <div class="amount">' .
                '                <span class="prompt">最低配送额 :</span>' .
                '                <span class="num">¥' . $shop->min_money . '</span>' .
                '            </div>' .
                '        </div>' .
                '    </div>';
        }

        return ['count' => $shops->count(), 'html' => $html];
    }
}
