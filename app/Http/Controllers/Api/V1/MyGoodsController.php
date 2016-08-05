<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\AddressData;
use App\Models\BarcodeWithoutImages;
use App\Models\Goods;
use App\Models\Shop;
use App\Http\Requests;
use App\Models\Images;
use App\Services\AddressService;
use App\Services\AttrService;
use App\Services\CategoryService;
use App\Services\GoodsService;
use App\Services\ImageUploadService;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Gate;

class MyGoodsController extends Controller
{

    public function index(Request $request)
    {
        $data = $request->all();
        $shop = auth()->user()->shop;

        $result = GoodsService::getShopGoods($shop, $data);

        return $this->success([
            'goods' => $result['goods']->orderBy('id', 'DESC')->paginate()->toArray(),
            'categories' => CategoryService::formatShopGoodsCate($shop)
        ]);
    }

    /**
     * store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Api\v1\CreateGoodsRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(Requests\Api\v1\CreateGoodsRequest $request)
    {
        $attributes = $request->except('images');
        $attributes['user_type'] = auth()->user()->type;
        $goods = auth()->user()->shop->goods()->create($attributes);
        if ($goods->exists) {
            // 更新配送地址
            $this->updateDeliveryArea($goods, $request->input('area'));

            $images = $request->hasFile('images') ? $request->file('images') : $request->input('images');

            if (!is_null($images)) {
                $this->_setImages($images, $goods->bar_code);
            }

            // 更新标签
            isset($attributes['attrs']) && $this->updateAttrs($goods, $attributes['attrs']);
            //保存没有图片的条形码
            $this->saveWithoutImageOfBarCode($goods);
            return $this->created('添加商品成功');
        }
        return $this->error('添加商品出现错误');
    }

    /**
     *
     *
     * @param $goods
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function show($goods)
    {
        if (Gate::denies('validate-my-goods', $goods)) {
            return $this->forbidden('权限不足');
        }
        $goods->load(['images', 'deliveryArea']);
        $goods->setAppends(['images_url', 'image_url', 'pieces', 'price']);

        $attrs = (new AttrService())->getAttrByGoods($goods, true);
        $goods->shop_name = $goods->shop()->pluck('name');
        $goods->attrs = $attrs;

        return $this->success(['goods' => $goods]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Api\v1\UpdateGoodsRequest $request
     * @param $goods
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Requests\Api\v1\UpdateGoodsRequest $request, $goods)
    {
        if (Gate::denies('validate-my-goods', $goods)) {
            return $this->forbidden('权限不足');
        }
        $attributes = $request->except('images');

        //是否退换货补充
        $attributes['is_back'] = isset($attributes['is_back']) ? $attributes['is_back'] : 0;
        $attributes['is_change'] = isset($attributes['is_change']) ? $attributes['is_change'] : 0;
        $attributes['is_new'] = isset($attributes['is_new']) ? $attributes['is_new'] : 0;
        $attributes['is_out'] = isset($attributes['is_out']) ? $attributes['is_out'] : 0;
        $attributes['is_expire'] = isset($attributes['is_expire']) ? $attributes['is_expire'] : 0;

        if (!isset($attributes['is_promotion'])) {
            $attributes['is_promotion'] = 0;
            $attributes['promotion_info'] = '';
        }

        if ($goods->fill($attributes)->save()) {
            // 更新配送地址
            $this->updateDeliveryArea($goods, $request->input('area'));

            $images = $request->hasFile('images') ? $request->file('images') : $request->input('images');
            if (!is_null($images)) {
                $this->_setImages($images, $goods->bar_code);
            }

            // 更新标签
            isset($attributes['attrs']) && $this->updateAttrs($goods, $attributes['attrs']);

            $this->saveWithoutImageOfBarCode($goods);
            return $this->success('更新商品成功');
        }
        return $this->error('更新商品时遇到问题');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $goods
     * @return Response
     */
    public function destroy($goods)
    {
        if (Gate::denies('validate-my-goods', $goods)) {
            return $this->forbidden('权限不足');
        }
        if ($goods->delete()) {
            return $this->success('删除商品成功');
        }
        return $this->error('删除商品时遇到问题');
    }

    /**
     * 商品上下架
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function shelve(Request $request)
    {
        $goodsId = $request->input('id');
        $goods = Goods::find($goodsId);
        if (Gate::denies('validate-my-goods', $goods)) {
            return $this->forbidden('权限不足');
        }
        $status = intval($request->input('status'));
        $goods->status = $status;
        $statusVal = cons()->valueLang('goods.status', $status);
        if ($goods->save()) {
            if ($status) {
                return $this->success('商品' . $statusVal . '成功');
            } else {
                return $this->success(null);
            }
        }
        return $this->error('商品' . $statusVal . '失败');
    }

    /**
     * 设置为抵费商品
     *
     * @param \App\Models\Goods $goods
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function mortgage(Goods $goods)
    {
        if (Gate::denies('validate-my-goods', $goods)) {
            return $this->forbidden('权限不足');
        }
        $shopId = auth()->user()->shop_id;

        if ($mortgageGoods = $goods->mortgageGoods()->withTrashed()->where('shop_id', $shopId)->first()) {
            return $mortgageGoods->fill(['deleted_at' => null])->save() ? $this->success('设置成功') : $this->error('设置失败，请重试');
        }

        $attributes = [
            'goods_name' => $goods->name,
            'pieces' => $goods->pieces_retailer,
            'shop_id' => $shopId
        ];

        return $goods->mortgageGoods()->create($attributes)->exists ? $this->success('设置成功') : $this->error('设置失败，请重试');
    }

    /**
     * 商品批量上下架
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function batchShelve(Request $request)
    {
        $goodsIds = $request->input('ids');
        $goods = Goods::where('shop_id', auth()->user()->shop()->pluck('id'))->whereIn('id', $goodsIds)->get();

        if ($goods->isEmpty()) {
            return $this->error('请选择商品');
        }
        $status = intval($request->input('status'));

        $goods->each(function ($item) use ($status) {
            $item->fill(['status' => $status])->save();
        });

        $statusVal = cons()->valueLang('goods.status', $status);
        return $this->success('商品' . $statusVal . '成功');
    }

    /**
     * 获取商品图片
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getImages(Request $request)
    {
        $barCode = $request->input('bar_code');
        if (!$barCode) {
            return $this->error('暂无商品图片');
        }
        $goodsImage = Images::with('image')->where('bar_code', $barCode)->paginate()->toArray();

        return $this->success(['goodsImage' => $goodsImage]);
    }


    /**
     * 商品批量导入
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function import(Request $request)
    {
        $file = $request->file('file');

        $postAttr = $request->only(['cate_level_1', 'cate_level_2', 'cate_level_3', 'status', 'shop_id']);
        $attrs = $request->input('attrs');
        $importResult = $this->importGoods($file, $postAttr, $attrs);

        return $importResult['type'] ? $this->success($importResult['info'],
            ['Content-Type' => 'text/html']) : $this->error($importResult['info']);
    }


    /**
     * 导入商品
     *
     * @param $file
     * @param $postAttr
     * @param $attrs
     * @return bool
     */
    public function importGoods($file, $postAttr, $attrs)
    {
        $filePath = $this->uploadExcel($file);
        if (!$filePath['type']) {
            return $filePath;
        }
        $results = Excel::selectSheetsByIndex(0)->load($filePath['info'], function ($reader) {
        })->skip(1)->toArray();
        $shopId = isset($postAttr['shop_id']) && $postAttr['shop_id'] > 0 ? $postAttr['shop_id'] : null;
        if ($shopId) {
            $shop = Shop::with(['deliveryArea', 'user'])->find($shopId);
        } else {
            $shop = auth()->user()->shop->load(['deliveryArea']);
        }
        if (is_null($shop)) {
            return $this->setImportResult('店铺不存在');
        }

        foreach ($results as $goods) {
            if (is_null($goods[0])) {
                break;
            }
            $goodsAttr = $this->_getGoodsAttrForImport($goods, $postAttr, $shop->user->type);
            $goodsModel = $shop->goods()->create($goodsAttr);
            if ($goodsModel->exists) {
                $this->saveWithoutImageOfBarCode($goodsModel);
                $this->_copyShopDeliveryAreaForImport($goodsModel, $shop);
                !is_null($attrs) && $this->updateAttrs($goodsModel, $attrs);
            } else {
                return $this->setImportResult('文件格式不正确');
            }
        }

        return $this->setImportResult('上传成功', true);

    }


    /**
     * upload excel for goods
     *
     * @param $file
     * @return bool|string
     */
    protected function uploadExcel($file)
    {
        $tempPath = config('path.upload_temp');

        // 判断文件是否有效
        if (!is_object($file) || !$file->isValid()) {
            return $this->setImportResult('无效的文件');
        }
        $ext = $file->getClientOriginalExtension();

        if (!in_array($ext, cons('goods.import_allow_ext'))) {
            return $this->setImportResult('文件类型错误');
        }

        $path = date('Y/m/d/');
        $name = uniqid() . '.' . $ext;

        try {
            // 移动到目录
            $file->move($tempPath . $path, $name);
        } catch (FileException $e) {
            info($e);

            return $this->setImportResult('上传失败');
        }
        return $this->setImportResult($tempPath . $path . $name, true);
    }

    /**
     * 更新配送地址处理
     *
     * @param $model
     * @param $area
     * @return bool
     */
    private function updateDeliveryArea($model, $area)
    {
//        if (is_null($area) || $model->deliveryArea->count() == count(array_filter($area['province_id']))) {
//            return true;
//        }
        //配送区域添加
        $areaArr = (new AddressService($area))->formatAddressPost();
        //删除原有配送区域信息
        $model->deliveryArea->each(function ($address) {
            $address->delete();
        });
        if (!empty($areaArr)) {
            $areas = [];
            foreach ($areaArr as $data) {
                /* if (isset($data['coordinate'])) {
                     $coordinate = $data['coordinate'];
                     unset($data['coordinate']);
                 }*/
                unset($data['coordinate']);
                $areasModal = new AddressData($data);
                if (!in_array($areasModal, $areas)) {
                    $areas[] = $areasModal;
                }


                /* if (isset($coordinate)) {
                     $areaModel->coordinate()->save(new Coordinate($coordinate));
                 }*/
            }
            $model->deliveryArea()->saveMany($areas);
        }
        return true;
    }

    /**
     * 复制商店配送区域至商品配送区域
     *
     * @param $goodsModel
     * @return bool
     */
    private function _copyShopDeliveryAreaForImport($goodsModel, $shop)
    {
        if (!$goodsModel instanceof Goods) {
            return false;
        }

        $shop->deliveryArea->each(function ($area) use ($goodsModel) {
            $areaModel = $goodsModel->deliveryArea()->save(new AddressData($area->toArray()));
            unset($areaModel);
        });
        return true;
    }

    /**
     * 获取商品字段
     *
     * @param $goodsArr
     * @param $postAttr
     * @param $shopType
     * @return array
     */
    private function _getGoodsAttrForImport($goodsArr, $postAttr, $shopType)
    {
        $goods = [
            'name' => $goodsArr[0],
            'bar_code' => $goodsArr[1],
            'price_retailer' => $goodsArr[2],
            'price_retailer_pick_up' => isset($goodsArr[3]) && $goodsArr[3] ? $goodsArr[3] : $goodsArr[2],
            'min_num_retailer' => $goodsArr[4],
            'pieces_retailer' => $goodsArr[5],
            'specification_retailer' => $goodsArr[6],
            'user_type' => $shopType
        ];
        if ($shopType == cons('user.type.supplier')) {
            $goods['price_wholesaler'] = isset($goodsArr[7]) ? $goodsArr[7] : $goodsArr[2];
            $goods['price_wholesaler_pick_up'] = isset($goodsArr[8]) && $goodsArr[8] ? $goodsArr[8] : $goods['price_wholesaler'];
            $goods['min_num_wholesaler'] = isset($goodsArr[9]) ? $goodsArr[9] : $goodsArr[4];
            $goods['pieces_wholesaler'] = isset($goodsArr[10]) ? $goodsArr[10] : $goodsArr[5];
            $goods['specification_wholesaler'] = isset($goodsArr[11]) ? $goodsArr[11] : $goodsArr[6];
        }
        return array_merge($goods, $postAttr);
    }

    /**
     * 更新标签
     *
     * @param $model
     * @param $attrs
     */
    private function updateAttrs($model, $attrs)
    {
        //删除所有标签
        $model->attr()->detach();

        $attrArr = [];
        foreach ($attrs as $pid => $id) {
            $attrArr[$id] = ['attr_pid' => $pid];
        }
        if (!empty($attrArr)) {
            $model->attr()->sync($attrArr);
        }
    }

    /**
     * 保存没有图片的条形码
     *
     * @param \App\Models\Goods $goods
     * @return bool
     */
    private function saveWithoutImageOfBarCode(Goods $goods)
    {
        $barCode = $goods->bar_code;
        $imagesCount = Images::where('bar_code', $barCode)->count();
        if (!$imagesCount) {
            BarcodeWithoutImages::create(['barcode' => $barCode, 'goods_name' => $goods->name]);
        }
        return true;
    }

    /**
     * 设置导入错误信息
     *
     * @param $msg
     * @param $type
     * @return array
     */
    private function setImportResult($msg, $type = false)
    {
        return [
            'info' => $msg,
            'type' => $type
        ];
    }

    /**
     * 设置图片
     *
     * @param $images
     * @param $name
     * @return bool
     */
    private function _setImages($images, $name)
    {
        $images = (new ImageUploadService($images))->formatImagePost();

        foreach ($images as $item) {
            $image = Images::create(['bar_code' => $name]);
            if ($image->exists) {
                $file = is_array($item) ? $item['path'] : $item;
                $fileName = is_array($item) ? $item['name'] : null;
                $image->associateFile($image->convertToFile($file, $fileName), 'image');
            }
        }
        return true;
    }
}