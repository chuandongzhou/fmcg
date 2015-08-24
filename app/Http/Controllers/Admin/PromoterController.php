<?php

namespace App\Http\Controllers\Admin;

use App\Models\Promoter;
use Illuminate\Http\Request;

use App\Http\Requests;

class PromoterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $name = $request->input('name') ? $request->input('name') : '';
        if ($name) {
            $promoters = Promoter::where('name', 'like', $name . '%')->paginate();
        } else {
            $promoters = Promoter::paginate();
        }
        return view('admin.promoter.index', ['promoters' => $promoters, 'name' => $name]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.promoter.promoter', ['promoter' => new Promoter]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\CreatePromoterRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(Requests\Admin\CreatePromoterRequest $request)
    {
        $attributes = $request->all();
        $attributes['spreading_code'] = strtoupper(uniqid());
        if (Promoter::create($attributes)->exists) {
            return $this->success('添加推广人员成功');
        }
        return $this->error('添加推广人员时遇到错误');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param $promoter
     * @return \Illuminate\View\View
     */
    public function edit($promoter)
    {
        return view('admin.promoter.promoter', ['promoter' => $promoter]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\UpdatePromoterRequest $request
     * @param $promoter
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Requests\Admin\UpdatePromoterRequest $request, $promoter)
    {
        if ($promoter->fill($request->all())->save()) {
            return $this->success('修改推广人员成功');
        }
        return $this->error('修改推广人员时遇到错误');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $promoter
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy($promoter)
    {
        return $promoter->delete() ? $this->success('删除推广人员成功') : $this->error('删除推广人员时遇到错误');
    }

    /**
     * 批量删除用户
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteBatch(Request $request)
    {
        $promoterIds = (array)$request->input('ids');
        if (empty($promoterIds)) {
            return $this->error('推广人员未选择');
        }
        return Promoter::destroy($promoterIds) ? $this->success('删除推广人员成功') : $this->error('推广人员删除时遇到错误');
    }
}
