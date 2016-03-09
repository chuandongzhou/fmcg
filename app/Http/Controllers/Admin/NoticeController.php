<?php

namespace App\Http\Controllers\Admin;

use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notices = Notice::all();
        return view('admin.notice.index', ['notices' => $notices]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.notice.notice', ['notice' => new Notice]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attributes = $request->all();
        if (Notice::create(['title' => $attributes['title'], 'content' => $attributes['content']])->exists) {
            return $this->success('添加公告成功');
        }
        return $this->error('添加公告时出现问题');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Notice $notice
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Notice $notice)
    {
        return view('admin.notice.notice', ['notice' => $notice]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $notice
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, $notice)
    {
        $attributes = $request->all();
        if ($notice->fill(['title' => $attributes['title'], 'content' => $attributes['content']])->save()) {
            return $this->success('更新公告成功');
        }
        return $this->error('更新公告时出现问题');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $notice
     * @return \Illuminate\Http\Response
     */
    public function destroy($notice)
    {
        return $notice->delete() ? $this->success('添加公告成功') : $this->error('删除公告时出现问题');
    }
}
