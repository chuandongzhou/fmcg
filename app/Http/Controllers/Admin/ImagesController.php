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
            $image = Images::create(['bar_code' => $item['name']]);
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
}
