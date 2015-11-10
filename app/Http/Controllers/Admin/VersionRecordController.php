<?php

namespace App\Http\Controllers\Admin;


use App\Http\Requests\Admin\CreateVersionRecordRequest;
use App\Models\VersionRecord;
use Illuminate\Support\Facades\Redis;

class VersionRecordController extends Controller
{
    /**
     * get list of all
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $redis = Redis::connection();

        return view('admin.operation.version', [
            'records' => VersionRecord::orderBy('created_at', 'DESC')->paginate(),
            'androidUrl' => $redis->get('android_url'),
            'iosUrl' => $redis->get('ios_url')
        ]);
    }

    /**
     * create
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.operation.version-record', [
            'type' => array_flip(cons('push_device'))
        ]);
    }

    /**
     * save
     *
     * @param \App\Http\Requests\Admin\CreateVersionRecordRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function store(CreateVersionRecordRequest $request)
    {
        $data = $request->all();
        $data['user_name'] = auth()->user()->user_name;

        return VersionRecord::create($data)->exists ? $this->success('添加成功') : $this->error('添加失败');
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
