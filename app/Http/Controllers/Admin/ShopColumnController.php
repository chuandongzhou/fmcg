<?php

namespace App\Http\Controllers\Admin;


use App\Http\Requests;
use App\Models\ShopColumn;
use App\Services\HomeColumnService;

class ShopColumnController extends Controller
{
    protected $type = 'shop';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $shopColumn = ShopColumn::get();

        return view('admin.shop-column.index', [
            'shopColumns' => $shopColumn,
            'type' => $this->type
        ]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function create()
    {
        return view('admin.shop-column.shop-column', [
            'shopColumn' => new ShopColumn()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\CreateShopColumnRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(Requests\Admin\CreateShopColumnRequest $request)
    {
        $attributes = $request->all();
        if (!(new HomeColumnService)->validateIdLists($attributes['id_list'])) {
            return $this->error('添加的id不合法或大于10个');
        }

        $shopColumn = ShopColumn::Create($attributes);
        if ($shopColumn->exists) {
            return $this->success('添加栏目成功');
        }

        return $this->error('添加栏目时遇到错误');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $shopColumn
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit($shopColumn)
    {
        return view('admin.shop-column.shop-column', [
            'shopColumn' => $shopColumn,
        ]);
    }

    /**
     *
     *
     * @param \App\Http\Requests\Admin\UpdateShopColumnRequest $request
     * @param $shopColumn
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Requests\Admin\UpdateShopColumnRequest $request, $shopColumn)
    {

        $attributes = $request->all();
        if (!(new HomeColumnService)->validateIdLists($attributes['id_list'])) {
            return $this->error('添加的id不合法或大于10个');
        }
        if ($shopColumn->fill($attributes)->save()) {
            return $this->success('更新栏目成功');
        }
        $this->error('更新时遇到错误');
    }

    /**
     *
     * @param $shopColumn
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy($shopColumn)
    {
        return $shopColumn->delete() ? $this->success('删除栏目成功') : $this->error('删除栏目时遇到错误');
    }


}
