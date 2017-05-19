<?php

namespace App\Http\Controllers\Api\V1\ChildUser;


use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Api\v1\CreateAdvertRequest;
use Illuminate\Http\Request;
use App\Models\Goods;
use App\Models\ShopHomeAdvert;
use App\Services\ImageUploadService;
use DB;

class ModelController extends Controller
{

    protected $shop;

    public function __construct()
    {
        $this->shop = child_auth()->user()->shop;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Api\v1\CreateAdvertRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response|\WeiHeng\Responses\Apiv1Response
     */
    public function postAdvert(CreateAdvertRequest $request)
    {
        $goodsId = $request->input('goods_id');
        $promoteinfo = $request->input('promoteinfo');
        $attributes = $request->except(['identity', 'goods_id', 'promoteinfo']);
        $attributes['type'] = cons('advert.type.' . $request->input('identity'));
        $attributes['end_at'] = $attributes['end_at'] ?: null;
        $attributes['url'] = $request->input('identity') == 'shop' ? url('goods/' . $goodsId) : $promoteinfo;
        if ($this->shop->adverts()->create($attributes)->exists) {
            return $this->success('添加广告成功');
        }

        return $this->error('添加广告时遇到错误');
    }

    /**
     * 广告修改
     *
     * @param \App\Http\Requests\Api\v1\CreateAdvertRequest $request
     * @param $advertId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function putAdvert(CreateAdvertRequest $request, $advertId)
    {
        $advert = $this->shop->adverts()->find($advertId);
        if (is_null($advert)) {
            return $this->error('广告不存在');
        }

        $goodsId = $request->input('goods_id');
        $promoteinfo = $request->input('promoteinfo');
        $attributes = $request->except(['identity', 'goods_id', 'promoteinfo']);
        $attributes['type'] = cons('advert.type.' . $request->input('identity'));
        $attributes['url'] = $request->input('identity') == 'shop' ? url('goods/' . $goodsId) : $promoteinfo;
        if ($advert->fill($attributes)->save()) {
            return $this->success('修改广告成功');
        }

        return $this->error('修改广告时遇到错误');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $advertId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function deleteAdvert($advertId)
    {
        $advert = $this->shop->adverts()->find($advertId);
        if (is_null($advert)) {
            return $this->error('广告不存在');
        }
        if ($advert->delete()) {
            return $this->success('删除成功');
        }

        return $this->error('删除失败');
    }

    /**
     * 添加店招
     *
     * @param  \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */

    public function postSignature(Request $request)
    {
        $signature = $this->shop->ShopSignature()->find($request->input('id'));
        if (empty($request->input('signature')) && is_null($signature)) {
            return $this->success('未选择店招');
        }

        if (!is_null($signature)) {
            if ($signature->fill($request->all())->save()) {
                return $this->success('添加店招成功');
            }
        } else {
            $attributes = $request->except(['id']);
            if ($this->shop->ShopSignature()->create($attributes)->exists) {
                return $this->success('添加店招成功');
            }

        }
        return $this->error('添加店招失败');

    }

    /**
     *添加首页广告
     * * @param  \Illuminate\Http\Request $request
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postShopAdvert(Request $request)
    {
        $images = array_filter($request->input('images')['image']);
        if (empty($images)) {
            return $this->error('未选择');
        }
        $status = false;
        foreach ($images as $key => $value) {
            if ($request->input('images')['status'][$key] == 1) {
                $status = true;
                break;
            }
        }
        if (!$status) {
            return $this->error('没有显示的广告');
        }
        $advertsIds = $this->shop->shopHomeAdverts()->lists('id')->toArray();
        $flag = DB::transaction(function () use ($images, $request, $advertsIds) {
            foreach ($images as $key => $image) {
                $item['url'] = $request->input('images')['url'][$key];
                $item['status'] = $request->input('images')['status'][$key];
                if (substr($image, 0, 4) == 'http') {
                    $id = $request->input('images')['id'][$key];

                    unset($advertsIds[array_search($id, $advertsIds)]);
                    $advert = $this->shop->shopHomeAdverts()->find($id);
                    if (!$advert->fill($item)->save()) {
                        return false;
                    }

                } else {
                    $shophomeadvert = $this->shop->shopHomeAdverts()->create($item);
                    if ($shophomeadvert->exists) {
                        $shophomeadvert->associateFile($shophomeadvert->convertToFile($image), 'image');
                    } else {
                        return false;
                    }
                }

            }
            ShopHomeAdvert::destroy($advertsIds);
            return true;
        });


        return $flag ? $this->success('添加成功') : $this->error('添加失败');

    }

    /**
     * 添加店铺推荐商品
     *
     * @param  \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postRecommendGoods(Request $request)
    {
        $goodsId = $request->input('goodsId');
        if (count($goodsId) > 15) {
            return $this->error('最多选择15个推荐商品');
        }
        if (count($goodsId) < 1) {
            return $this->error('请选择推荐商品');
        }
        $this->shop->recommendGoods()->sync($goodsId);
        return $this->success('添加成功');

    }

    /**
     * 推荐商品选择分页查询
     *
     * @param  \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getGoodsPage(Request $request)
    {
        $currentPage = $request->input('currentPage') ? $request->input('currentPage') : 1;
        $pageNum = cons('shop.home_page_per_num');
        $skip = ($currentPage - 1) * $pageNum;
        $shop_id = $this->shop->id;

        $count = Goods::where('shop_id', $shop_id)->active()->count();
        $allPage = ceil($count / $pageNum);
        if ($currentPage > $allPage) {
            $currentPage = $allPage;
        }
        $goods = Goods::where('shop_id', $shop_id)->active()->skip($skip)->take($pageNum)->get()->each(function($item) {
            $item->setAppends(['pieces', 'image_url', 'price']);
        });

        return $this->success(['goods' => $goods, 'allPage' => $allPage, 'currentPage' => $currentPage]);

    }

}
