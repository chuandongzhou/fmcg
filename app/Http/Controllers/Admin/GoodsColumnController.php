<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Models\GoodsColumn;
use App\Services\CategoryService;
use App\Services\HomeColumnService;

class GoodsColumnController extends Controller
{
    protected $type = 'goods';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        $cates = array_where(CategoryService::getCategories(), function ($id, $cate) {
            return $cate['pid'] == 0;
        });
        return view('admin.goods-column.index', ['cates' => $cates]);
    }


    /**
     * 保存商品栏目
     *
     * @param \App\Http\Requests\Admin\UpdateGoodsColumnRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function postIndex(Requests\Admin\UpdateGoodsColumnRequest $request)
    {
        $attributes = $request->only('province_id', 'city_id', 'cate_level_1');
        $goodsColumn = GoodsColumn::firstOrCreate($attributes);
        $idList = $request->input('id_list');
        if (!(new HomeColumnService())->validateIdLists($idList)) {
            return $this->error('id格式不正确');
        }

        if ($goodsColumn->fill(['id_list' => $request->input('id_list')])->save()) {
            return $this->success('保存成功');
        }
        return $this->error('保存失败');
    }
}
