<?php

namespace App\Http\Controllers\Admin;

use App\Models\HomeColumn;
use Illuminate\Http\Request;

use App\Http\Requests;

class HomeColumnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $request
     * @return Response
     */
    public function index(Request $request)
    {
        $types = cons('home_column.type');
        $type = (string)$request->input('type');
        $typeId = array_get($types, $type);
        $homeColumns = HomeColumn::where('type', array_get($types, $type, head($types)))->get();

        return view('admin.home-column.index', [
            'homeColumns' => $homeColumns,
            'typeId' => $typeId,
            'type' => $type
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $type = (string)$request->input('type');
        $types = cons('home_column.type');
        $typeId = array_get($types, $type);
        if (empty($typeId)) {
            return $this->error('无法添加栏目');
        }

        return view('admin.home-column.home-column', [
            'homeColumn' => new HomeColumn,
            'type' => $type,
            'typeId' => $typeId
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\CreateHomeColumnRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(Requests\Admin\CreateHomeColumnRequest $request)
    {
        $attributes = $request->all();
        if (!$this->_validateIdLists($attributes['id_list'])) {
            return $this->error('添加的id不合法或大于10个');
        }

        $homeColumn = HomeColumn::Create($attributes);
        if ($homeColumn->exists) {
            return $this->success('添加栏目成功');
        }

        return $this->error('添加栏目时遇到错误');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $homeColumn
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit($homeColumn)
    {
        $types = cons('home_column.type');

        return view('admin.home-column.home-column', [
            'homeColumn' => $homeColumn,
            'typeId' => $homeColumn->type,
            'type' => array_search($homeColumn->type, $types)
        ]);
    }

    /**
     *
     *
     * @param \App\Http\Requests\Admin\UpdateHomeColumnRequest $request
     * @param $homeColumn
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Requests\Admin\UpdateHomeColumnRequest $request, $homeColumn)
    {

        $attributes = $request->all();
        if (!$this->_validateIdLists($attributes['id_list'])) {
            return $this->error('添加的id不合法或大于10个');
        }
        if ($homeColumn->fill($attributes)->save()) {
            return $this->success('更新栏目成功');
        }
        $this->error('更新时遇到错误');
    }

    /**
     *
     * @param $homeColumn
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy($homeColumn)
    {
        return $homeColumn->delete() ? $this->success('删除栏目成功') : $this->error('删除栏目时遇到错误');
    }

    /**
     * 验证id是否合法
     *
     * @param $idLists
     * @return bool
     */
    private function _validateIdLists($idLists)
    {
        $idLists = explode('|', $idLists);
        $return = array_first($idLists, function ($key, $value) {
            return !is_numeric($value);
        });
        return count($idLists) <= 10 && is_null($return);
    }
}
