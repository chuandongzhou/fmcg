<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\BarcodeWithoutImages;
use App\Models\Coordinate;
use App\Models\DeliveryArea;
use App\Models\Goods;
use App\Http\Requests;
use App\Models\Images;
use App\Services\AddressService;
use App\Services\AttrService;
use App\Services\GoodsService;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Gate;

class MyGoodsController extends Controller
{

    public function index(Request $request)
    {
        $gets = $request->all();

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
            'is_promotion',
            'cate_level_1',
            'cate_level_2'
        ]);

        $result = GoodsService::getGoodsBySearch($gets, $goods, false);
        return $this->success([
            'goods' => $result['goods']->paginate()->toArray(),
            'categories' => $result['categories']
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
        $attributes = $request->all();
        $goods = auth()->user()->shop->goods()->create($attributes);
        if ($goods->exists) {
            // 更新配送地址
            $this->updateDeliveryArea($goods, $request->input('area'));

            // 更新标签
            $this->updateAttrs($goods, $attributes['attrs']);
            //保存没有图片的条形码
            $this->saveWithoutImageOfBarCode($goods->bar_code);
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
        $goods->load(['images', 'deliveryArea.coordinate']);

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
        $attributes = $request->all();
        //是否退换货补充
        $attributes['is_back'] = isset($attributes['is_back']) ? $attributes['is_back'] : 0;
        $attributes['is_change'] = isset($attributes['is_change']) ? $attributes['is_change'] : 0;
        if ($goods->fill($attributes)->save()) {
            // 更新配送地址
            $this->updateDeliveryArea($goods, $attributes['area']);
            // 更新标签
            $this->updateAttrs($goods, $attributes['attrs']);

            $this->saveWithoutImageOfBarCode($goods->bar_code);
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
     * @param $goodsId
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function shelve(Request $request, $goodsId)
    {
        $goods = Goods::find($goodsId);
        if (Gate::denies('validate-my-goods', $goods)) {
            return $this->forbidden('权限不足');
        }
        $status = intval($request->input('status'));
        $goods->status = $status;
        $statusVal = cons()->valueLang('goods.status', $status);
        if ($goods->save()) {
            return $this->success('商品' . $statusVal . '成功');
        }
        return $this->error('商品' . $statusVal . '失败');
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
        $goodsImage = Images::with('image')->where('bar_code', 'LIKE', '%' . $barCode . '%')->paginate()->toArray();

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

        $postAttr = $request->only(['cate_level_1', 'cate_level_2', 'cate_level_3', 'status']);
        $attrs = $request->input('attrs');
        $importResult = $this->importGoods($file, $postAttr, $attrs);

        return $importResult['type'] ? $this->success($importResult['info']) : $this->error($importResult['info']);
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

        $shop = auth()->user()->shop->load(['deliveryArea.coordinate']);
        foreach ($results as $goods) {
            $goodsAttr = $this->_getGoodsAttrForImport($goods, $postAttr);
            $goodsModel = $shop->goods()->create($goodsAttr);
            if ($goodsModel->exists) {
                $this->saveWithoutImageOfBarCode($goodsModel->bar_code);
                $this->_copyShopDeliveryAreaForImport($goodsModel, $shop);
                $this->updateAttrs($goodsModel, $attrs);
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
        //配送区域添加
        $areaArr = (new AddressService($area))->formatAddressPost();

        $nowArea = $model->deliveryArea;
        if (count($nowArea) == count(array_filter($area['province_id']))) {
            return true;
        }
        //删除原有配送区域信息
        $model->deliveryArea->each(function ($address) {
            $address->delete();
        });
        if (!empty($areaArr)) {
            foreach ($areaArr as $data) {
                if (isset($data['coordinate'])) {
                    $coordinate = $data['coordinate'];
                    unset($data['coordinate']);
                }
                $areas = new DeliveryArea($data);
                $areaModel = $model->deliveryArea()->save($areas);
                if (isset($coordinate)) {
                    $areaModel->coordinate()->save(new Coordinate($coordinate));
                }
            }
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
            $areaModel = $goodsModel->deliveryArea()->save(new DeliveryArea(array_except($area->toArray(),
                'coordinate')));
            if (isset($area->coordinate)) {
                $areaModel->coordinate()->save(new Coordinate($area->coordinate->toArray()));
            }
            unset($areaModel);
        });
        return true;
    }

    /**
     * 获取商品字段
     *
     * @param $goodsArr
     * @return array
     */
    private function _getGoodsAttrForImport($goodsArr, $postAttr)
    {

        $goods = [
            'name' => $goodsArr[0],
            'bar_code' => $goodsArr[1],
            'price_retailer' => $goodsArr[2],
            'min_num_retailer' => $goodsArr[3],
            'pieces_retailer' => $goodsArr[4],
            'specification_retailer' => $goodsArr[5]
        ];
        if (auth()->user()->type == cons('user.type.supplier')) {
            $goods['price_wholesaler'] = isset($goodsArr[6]) ? $goodsArr[6] : $goodsArr[2];
            $goods['min_num_wholesaler'] = isset($goodsArr[7]) ? $goodsArr[7] : $goodsArr[3];
            $goods['pieces_wholesaler'] = isset($goodsArr[8]) ? $goodsArr[8] : $goodsArr[4];
            $goods['specification_wholesaler'] = isset($goodsArr[9]) ? $goodsArr[9] : $goodsArr[5];
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
     *  保存没有图片的条形码
     *
     * @param $barCode
     * @return bool
     */
    private function saveWithoutImageOfBarCode($barCode)
    {
        $imagesCount = Images::where('bar_code', $barCode)->count();
        if (!$imagesCount) {
            BarcodeWithoutImages::create(['barcode' => $barCode]);
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
}