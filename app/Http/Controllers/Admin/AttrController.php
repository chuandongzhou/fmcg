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
            $search['cate_level_1'] = $firstCategory[0];
            $search['cate_level_2'] = $where['category_id'] = $firstCategory[1];
        }
        $attrs = Attr::where($where)->get(['id', 'attr_id', 'name', 'pid'])->toArray();
        if ($attrs) {
            $attrs = new Tree($attrs, 'attr_id');
        }
        return view('admin.attr.index', ['attrs' => $attrs, 'search' => $search]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($categoryId = 0)
    {
        $categories = Category::with('icon')->select(['id', 'name', 'pid', 'level'])->get()->toArray();
        /*      if ($id) {
                  $categoryId = Attr::where('id', $id)->pluck('category_id');
                  $attr->category_id = $categoryId;
                  $attr->pid = $id;
              }*/
        $categories = new Tree($categories);
        $firstCateId = $categoryId > 0 ? $categoryId : array_first($categories->data(), function ($key, $data) {
            return $data['pid'] > 0;
        })['id'];

        $categoryPid = Category::where('id', $firstCateId)->with('icon')->pluck('pid');

        // 当前分类所包含标签
        $attr = Attr::where('category_id', $firstCateId)->select([
            'status',
            'name',
            'attr_id',
            'pid'
        ])->get()->toArray();


        // 父级分类所包含标签
        $parentCateOfAttr = Attr::where('category_id', $categoryPid)->select([
            'status',
            'name',
            'attr_id',
            'pid'
        ])->get()->toArray();

        // 组合当前和父级

        $allAttrs = $attr;
        $attrNames = array_pluck($attr, 'name');
        foreach ($parentCateOfAttr as $key => $parentAttr) {
            if (!in_array($parentAttr['name'], $attrNames)) {
                array_push($allAttrs, $parentAttr);
            }
        }

        //dd(array_pluck($attr, 'attr_id'));
        return view('admin.attr.attr',
            [
                'categories' => $categories,
                'allAttr' => new Tree($allAttrs, 'attr_id'),
                'thisAttr' => array_pluck($attr, 'attr_id'),
                'cateId' => $firstCateId
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateAttrRequest $request
     * @return Response
     */
    public function store(CreateAttrRequest $request)
    {
        $maxAttrId = Attr::max('attr_id');

        $attributes = $request->all();
        $attributes['attr_id'] = $maxAttrId + 1;

        if (Attr::create($attributes)->exists) {
            return $this->success($attributes['attr_id']);
        }
        return $this->error('添加标签时遇到错误');
    }

    public function save(Request $request)
    {
        $categoryId = $request->input('category_id');
        $checkedAttrIds = $request->input('attr_id');
        $status = cons('status');
        //找出取消的

        $cancel = Attr::where('category_id', $categoryId)->whereNotIn('attr_id',
            $checkedAttrIds)->update(['status' => $status['off']]);

        //新增的并且数据库已有的

        $newExistsAttrIds = $request->input('new_exist_attr_id');

        if ($newExistsAttrIds) {
            Attr::where('category_id', $categoryId)->whereIn('attr_id',
                $newExistsAttrIds)->update(['status' => $status['on']]);
        }

        //新增并且数据库里没有的
        $newAttrIds = $request->input('new_attr_id');
        if ($newAttrIds) {
            foreach ($newAttrIds as $pid => $attr) {
                $result = Attr::where('attr_id', $attr)->where('pid',
                    $pid)->first();
                $newAttrs =
                    [
                        'attr_id' => $result->attr_id,
                        'name' => $result->name,
                        'category_id' => $categoryId,
                        'pid' => $pid,
                        'status' => $status['on'],
                    ];
                Attr::create($newAttrs);
            }

        }

        return $this->success('保存成功');
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

        return view('admin.attr.edit',
            [
                'attr' => $attr,
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
        if ($attr->fill(['name' => $request->input('name')])->save()) {
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
