<?php

namespace App\Http\Controllers\Admin;


use App\Http\Requests\Admin\CreateVersionRecordRequest;
use App\Models\VersionRecord;
use App\Services\RedisService;

class VersionRecordController extends Controller
{
    /**
     * get list of all
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $redis = new RedisService();

        return view('admin.operation.version', [
            'records' => VersionRecord::orderBy('created_at', 'DESC')->paginate(),
            'redis' => $redis
        ]);
    }

    /**
     * create
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {

        return view('admin.operation.version-record');
    }

    /**
     * save
     *
     * @param \App\Http\Requests\Admin\CreateVersionRecordRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(CreateVersionRecordRequest $request)
    {
        $attributes = $request->all();
        $attributes['user_name'] = admin_auth()->user()->name;

        $exist = VersionRecord::where([
            'type' => $attributes['type'],
            'version_name' => $attributes['version_name']
        ])->pluck('id');

        if ($exist) {
            return $this->error('版本名已存在');
        }

        return VersionRecord::create($attributes)->exists ? $this->success('添加成功') : $this->error('添加失败');
    }

    /**
     * delete
     *
     * @param $record
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy($record)
    {
        return $record->delete() ? $this->success('删除成功') : $this->error('删除失败');
    }


}
