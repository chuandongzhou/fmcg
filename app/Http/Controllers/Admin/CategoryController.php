<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CreateCategoryRequest;
use App\Models\Attr;
use App\Models\Category;
use Illuminate\Http\Request;

use WeiHeng\Foundation\Tree;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $categories = Category::select(['id', 'name', 'pid'])->with('icon')->get()->toArray();
        $categories = new Tree($categories);
        return view('admin.category.index', ['categories' => $categories]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $categories = Category::where('level', '<', 3)->with('icon')->get([
            'id',
            'name',
            'pid',
            'level'
        ])->toArray();
        $categories = new Tree($categories);
        return view('admin.category.category', ['categories' => $categories, 'category' => new Category]);
    }

    /**
     * 保存分类
     *
     * @param \App\Http\Requests\Admin\CreateCategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(CreateCategoryRequest $request)
    {
        $attributes = $request->all();

        $pid = $attributes['pid'];
        $attributes['level'] = 1;
        if ($pid) {
            $parentCategory = Category::find($pid);
            $attributes['level'] = $parentCategory->level + 1;
        }

        $category = Category::create($attributes);
        if ($category->exists) {
            if ($category->pid == 0) {
                $this->_addDefaultAttr($category);
            }
            return $this->success('添加分类成功');
        }
        return $this->error('添加分类出现问题');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * how the form for editing the specified resource.
     *
     * @param $category
     * @return \Illuminate\View\View
     */
    public function edit($category)
    {
        $categories = Category::where('level', '<', 3)->with('icon')->get([
            'id',
            'name',
            'pid',
            'level'
        ])->toArray();
        $categories = new Tree($categories);
        return view('admin.category.category', ['categories' => $categories, 'category' => $category]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $category
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, $category)
    {
        if ($category->fill($request->all())->save()) {
            return $this->success('更新分类成功');
        }
        return $this->success('更新分类失败');
    }

    /**
     * @param $category
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy($category)
    {
        $childNode = Category::where('pid', $category->id)->first();
        if (!empty($childNode)) {
            return $this->error('该分类存在子分类，不能删除');
        }
        if ($category->delete()) {
            return $this->success('删除分类成功');
        }
        return $this->error('删除分类时遇到错误');
    }

    private function _addDefaultAttr($model)
    {
        $attr = [];
        foreach (cons('attr.default') as $key => $val) {
            $attr[] = new Attr([
                'attr_id' => $val,
                'name' => cons()->valueLang('attr.default', $val),
                'category_id' => $model->id,
                'pid' => 0
            ]);
        }
        return $model->attrs()->saveMany($attr);
    }
}
