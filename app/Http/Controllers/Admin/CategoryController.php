<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CreateCategoryRequest;
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
        $categories = Category::all(['id', 'name', 'pid'])->toArray();
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
        $categories = Category::where('level', '<', 3)->get([
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
        $post = $request->all();
        if (Category::create($post)->exists) {
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
        $categories = Category::where('level', '<', 3)->get([
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
}
