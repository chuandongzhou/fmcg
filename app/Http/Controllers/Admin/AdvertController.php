<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UpdateAdvertRequest;
use App\Http\Requests\Admin\CreateAdvertRequest;
use App\Models\Advert;
use App\Models\Category;
use Illuminate\Http\Request;


abstract class AdvertController extends Controller
{
    /**
     * 默认首页内容
     *
     * @var string
     */
    protected $type = 'index';

    /**
     * 获取广告列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = $request->all();

        $adverts = Advert::where('type',
            cons('advert.type.' . $this->type))->OfAddress(array_filter($data))->paginate();

        return view('admin.advert.index', [
            'type' => $this->type,
            'adverts' => $adverts,
            'data' => $data
        ]);
    }

    /**
     * 显示创建广告页面
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if ($this->type == 'category') {
            $categories = Category::where('pid', 0)->get();
        }
        return view('admin.advert.advert', [
            'type' => $this->type,
            'advert' => new Advert,
            'categories' => isset($categories) ? $categories : []
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\CreateAdvertRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(CreateAdvertRequest $request)
    {
        $attributes = $request->all();
        $attributes['type'] = cons('advert.type.' . $this->type);

        if (Advert::create(array_filter($attributes))->exists) {
            return $this->success('添加广告成功');
        }

        return $this->error('添加广告时遇到错误');
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
     * 显示修改界面
     *
     * @param \App\Models\Advert $advert
     * @return \Illuminate\View\View
     */
    public function edit($advert)
    {
        if ($this->type == 'category') {
            $categories = Category::where('pid', 0)->get();
        }
        return view('admin.advert.advert', [
            'type' => $this->type,
            'advert' => $advert,
            'categories' => isset($categories) ? $categories : []
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\UpdateAdvertRequest $request
     * @param \App\Models\Advert $advert
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(UpdateAdvertRequest $request, $advert)
    {

        $attributes = $request->all();
        unset($attributes['type']);

        if ($advert->fill(array_filter($attributes))->save()) {
            return $this->success('修改成功');
        }

        return $this->error('修改失败');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Advert $advert
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy($advert)
    {
        if ($advert->delete()) {
            return $this->success('删除成功');
        }

        return $this->error('删除失败');
    }
}
