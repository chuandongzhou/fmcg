<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Images;
use Illuminate\Http\Request;

use App\Http\Requests;

class ImagesController extends Controller
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
        $cate = array_filter($request->only('cate_level_1', 'cate_level_2', 'cate_level_3'));
        $attrs = $request->input('attrs');

        if ($cate) {
            $goodsImage = Images::where($cate);
            $attrs = array_filter($attrs);
            if($attrs){
                $goodsImage = $goodsImage->ofAttr($attrs);
            }
            $goodsImage = $goodsImage->paginate();
        }
        else {
            $cate['cate_level_1'] = Category::orderBy('id', 'ASC')->with('icon')->pluck('id');
            $goodsImage =  Images::where('cate_level_1' , $cate['cate_level_1'])->paginate();
        }

        return view('admin.images.index', ['search' => $cate, 'goodsImage' => $goodsImage]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $firstCategory = Category::where('pid', 0)->with('icon')->pluck('id');
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
        $attributes = $request->except('attrs');

        $images = Images::create($attributes);
        if ($images->exists) {
            $attrs = $request->input('attrs');
            $images->attrs()->detach();
            $images->attrs()->sync($attrs);
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
        $goodsImage = Images::find($id);
        if ($goodsImage->delete()) {
            return $this->success('删除图片成功');
        }
        return $this->error('删除图片时遇到错误');
    }
}
