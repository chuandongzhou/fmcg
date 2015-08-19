<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CreateAttrRequest;
use App\Models\Attr;
use App\Models\Category;
use Illuminate\Http\Request;
use WeiHeng\Foundation\Tree;

class AttrController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {

        $search = array_filter($request->all());
        $where = [];
        if ($search) {
            $where['category_id'] = max($search);
        } else {
            $firstCategory = Category::orderBy('id', 'ASC')->take(2)->lists('id');
            $search['level1'] = $firstCategory[0];
            $search['level2'] = $where['category_id'] = $firstCategory[1];
        }
        $attrs = Attr::where($where)->get(['id', 'name', 'pid'])->toArray();
        if ($attrs) {
            $attrs = new Tree($attrs);
        }
        return view('admin.attr.index', ['attrs' => $attrs, 'search' => $search]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($id = 0)
    {
        $categories = Category::all(['id', 'name', 'pid', 'level'])->toArray();
        $attr = new Attr();
        if ($id) {
            $categoryId = Attr::where('id', $id)->pluck('category_id');
            $attr->category_id = $categoryId;
            $attr->pid = $id;
        }
        $categories = new Tree($categories);
        return view('admin.attr.attr',
            [
                'categories' => $categories,
                'attr' => $attr,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(CreateAttrRequest $request)
    {
        if (Attr::create($request->all())->exists) {
            return $this->success('添加标签成功');
        }
        return $this->error('添加标签时遇到错误');
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
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $attr = Attr::find($id);

        $categories = Category::all(['id', 'name', 'pid', 'level'])->toArray();

        $firstCategoryOfAttr = Attr::where(['category_id' => $attr['category_id'], 'pid' => 0])->lists('name', 'id');
        $categories = new Tree($categories);
        return view('admin.attr.attr',
            [
                'categories' => $categories,
                'attr' => $attr,
                'first_category_of_attr' => $firstCategoryOfAttr
            ]
        );

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int $id
     * @return Response
     */
    public function update(CreateAttrRequest $request, $id)
    {
        $attr = Attr::find($id);
        if ($attr->fill($request->all())->save()) {
            return $this->success('保存标签成功');
        }
        return $this->success('保存标签失败');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $attr = Attr::find($id);
        //判断有没有子分类
        $childNode = Attr::where(['category_id' => $attr->category_id, 'pid' => $id])->first();
        if (!empty($childNode)) {
            return $this->error('该标签分类存在子标签，不能删除');
        }
        if ($attr->delete()) {
            return $this->success('删除标签成功');
        }
        return $this->error('删除标签时遇到问题');
    }

    /**
     * Get Attr by Search
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function getAttr(Request $request)
    {
        $post = $request->all();
        if (!isset($post['format'])) {
            $result = Attr::where($post)->lists('name', 'id');
            return json_encode($result);
        } else {
            unset($post['format']);
            $attrs = Attr::where($post)->get([
                'id',
                'name',
                'pid'
            ])->toArray();
            $attrList = Attr::formatAttr($attrs);
            return json_encode($attrList);
        }
    }
}
