<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Images;
use App\Services\AttrService;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

use App\Http\Requests;

class ImagesController extends Controller
{
    /**
     * 图片列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $cate = array_filter($request->only('cate_level_1', 'cate_level_2', 'cate_level_3'));
        $attrs = $request->input('attrs');
        $attrResults = [];
        if ($cate) {
            //获取标签
            $attrResults = (new AttrService())->getAttrByCategoryId(max($cate));

            $goodsImage = Images::with('image')->where($cate);
            if (array_filter((array)$attrs)) {
                $goodsImage = $goodsImage->ofAttr(array_filter($attrs));
            }
            $goodsImage = $goodsImage->paginate(2);
        } else {
            $cate['cate_level_1'] = Category::orderBy('id', 'ASC')->with('icon')->pluck('id');
            $goodsImage = Images::with('image')->where('cate_level_1', $cate['cate_level_1'])->paginate(2);
        }

        return view('admin.images.index',
            [
                'data' => array_filter($request->all()),
                'search' => $cate,
                'goodsImage' => $goodsImage,
                'attrs' => $attrs,
                'attrResult' => $attrResults,
                'categories' => Category::lists('name', 'id')
            ]);
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

        $images = $request->input('images');

        $images = (new ImageUploadService($images))->formatImagePost();


        $attributes = $request->only(['cate_level_1', 'cate_level_2', 'cate_level_3']);
        foreach ($images as $item) {
            $image = Images::create($attributes);
            if ($image->exists) {
                $attrs = $request->input('attrs');
                $image->attrs()->detach();
                if (!empty($attrs)) {
                    $image->attrs()->sync(array_filter($attrs));
                }

                $image->associateFile($image->convertToFile($item['path']), 'image', cons('shop.file_type.logo'));
            } else {
                return $this->error('添加图片时遇到错误');
            }
        }
        return $this->success('添加图片成功');

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
