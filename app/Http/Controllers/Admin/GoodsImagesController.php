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
        $attributes = array_filter($request->all());
        if ($attributes) {
            if (isset($attributes['attrs'])) {
                $attributes['attrs'] = implode(',', $attributes['attrs']);
            }

        } else {
            $attributes['level1'] = Category::orderBy('id', 'ASC')->pluck('id');
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
        $firstCategory = Category::orderBy('id', 'ASC')->pluck('id');
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
