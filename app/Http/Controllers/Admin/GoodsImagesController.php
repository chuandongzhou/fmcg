<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\GoodsImages;
use Illuminate\Http\Request;

use App\Http\Requests;

class GoodsImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        // TODO: 查询是需要返回标签
        /*$attrs = $goods->attrs;
        $attrIds = array_keys($attrs);
        $attrResults = Attr::select(['id', 'pid', 'name'])
            ->whereIn('id', $attrIds)
            ->orWhere(function ($query) use ($attrIds) {
                $query->whereIn('pid', $attrIds);
            })->get()->toArray();
        $attrResults = (new AttrService($attrResults))->format();*/
        // TODO 以上代码可能会用到
        $attributes = array_filter($request->all());
        if ($attributes) {
            if (isset($attributes['attrs'])) {
                $attributes['attrs'] = json_encode($attributes['attrs']);
            }

        } else {
            $attributes['cate_level_1'] = Category::orderBy('id', 'ASC')->pluck('id');
        }

        $goodsImage = GoodsImages::where($attributes)->with('image')->get();
        return view('admin.images.index', ['search' => $attributes, 'goodsImage' => $goodsImage]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $firstCategory = Category::where('pid', 0)->pluck('id');
        return view('admin.images.images', ['search' => $firstCategory]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\CreateGoodsImagesRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(Requests\Admin\CreateGoodsImagesRequest $request)
    {
        $post = $request->all();

        if (GoodsImages::create($post)->exists) {
            return $this->success('添加图片成功');
        }

        return $this->error('添加图片时遇到错误');
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $goodsImage = GoodsImages::find($id);
        if ($goodsImage->delete()) {
            return $this->success('删除图片成功');
        }
        return $this->error('删除图片时遇到错误');
    }
}
