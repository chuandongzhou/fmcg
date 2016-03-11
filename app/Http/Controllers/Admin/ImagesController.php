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
        $barCode = $request->input('bar_code');

        $goodsImage = Images::with('image');
        if ($barCode) {
            $goodsImage->where('bar_code', 'LIKE', '%' . $barCode . '%');
        }

        return view('admin.images.index',
            [
                'barCode' => $barCode,
                'goodsImage' => $goodsImage->paginate(),
            ]);
    }

    /**
     * 添加商品时上传的图片审核
     */
    public function check()
    {
        $goodsImages = Images::with('image', 'goods')->where('status', 0)->paginate(30);
        return view('admin.images.check', [
            'goodsImages' => $goodsImages
        ]);
    }

    /**
     * 批量审核图片
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function checkHandle(Request $request)
    {
        $imageIds = (array)$request->input('ids');
        if (is_null($imageIds)) {
            return $this->error('图片不能为空');
        }
        if (Images::whereIn('id', $imageIds)->update(['status' => 1])) {
            return $this->success('图片审核成功');
        }
        return $this->error('图片审核时出现问题');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.images.images');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request)
    {

        $images = $request->input('images');

        $images = (new ImageUploadService($images))->formatImagePost();


        foreach ($images as $item) {
            $image = Images::create(['bar_code' => $item['name'], 'status' => 1]);
            if ($image->exists) {
                $image->associateFile($image->convertToFile($item['path'], $item['name']), 'image');
            } else {
                return $this->error('添加图片时遇到错误');
            }
        }
        return $this->success('添加图片成功');

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

    /**
     * 批量删除图片
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function batchDelete(Request $request)
    {
        $imageIds = $request->input('ids');
        if (is_null($imageIds)) {
            return $this->error('选择的图片不能为空');
        }
        if (Images::whereIn('id', $imageIds)->where('status', 0)->delete()) {
            return $this->success('图片删除成功');
        }
        return $this->error('图片删除时出现问题');
    }
}
