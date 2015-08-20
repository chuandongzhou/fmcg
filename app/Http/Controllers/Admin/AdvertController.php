<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\Admin\CreateAdvertRequest;
use App\Models\Advert;


class AdvertController extends Controller
{
    /**
     * 获取广告列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Advert $user
     * @return \Illuminate\View\View
     */
    public function index(Request $request, Advert $user)
    {
        $records =  $user->formatDuration($request->input('type'));
        foreach($records as &$value){
            $value->show_str = $this->showTip($value);
        }
        return view('admin.advert.index', [
            'records' => $records,
            'type' => $request->input('type'),
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {

        $adType = cons()->lang('advert.ad_type');
        $timeType = cons()->lang('advert.time_type');
        $appType = cons()->lang('advert.app_type');
        $type = $request->input('type');

        return view('admin.advert.create', [
            'type' => $type,
            'ad_type' => $adType,
            'time_type' => $timeType,
            'app_type' => $appType,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(CreateAdvertRequest $request)
    {
        if(Advert::create($request->all())->exists){
            return $this->success('添加广告成功',url('admin/advert?type='.$request->type));
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
     * @param \Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $adType = cons()->lang('advert.ad_type');
        $timeType = cons()->lang('advert.time_type');
        $appType = cons()->lang('advert.app_type');
        $record = Advert::find($id);

        return view('admin.advert.edit', [
            'type' => $request->input('type'),
            'ad' => $record,
            'pic' => $record->file,
            'ad_type' => $adType,
            'time_type' => $timeType,
            'app_type' => $appType,
        ]);

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
        $advert = Advert::find($id);
        if(!$advert){
            return $this->error('该广告信息不存在');
        }
        if($advert->fill($request->all())->save()){
            return $this->success('修改成功', url('admin/advert?type='.$request->input('type')));
        }
        return $this->error('修改失败');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        return Advert::destroy($id) ? $this->success('删除成功', url('admin/advert')) : $this->error('删除失败');
    }

    /**
     * 处理获取信息时时长的显示信息
     *
     * @param $value
     * @return string
     */
    public function showTip($value)
    {
        if (empty($value)) {
            return $value;
        }
        $now = date('Y-m-d H:i:s', time());
        if ($value->time_type == cons('advert.time_type.forever')) {
            $str = '永久';
        } else {

            if ($value->started_at > $now) {
                $str = '未开始';
            } elseif ($value->end_at < $now) {
                $str = '已结束';
            } else {
                $str = '展示中';
            }
        }

        return $str;
    }
}
