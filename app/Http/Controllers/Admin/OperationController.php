<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CreateVersionRecordRequest;
use App\Models\Notification;
use App\Models\VersionRecord;
use App\Services\RedisService;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;

class OperationController extends Controller
{
    /**
     * get list of all
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $dates = $request->all();
        $beginDay = array_get($dates, 'begin_day');
        $endDay = array_get($dates, 'end_day');

        $records = VersionRecord::ofDates($dates)->orderBy('created_at', 'DESC')->paginate();

        return view('admin.operation.index', [
            'records' => $records,
            'beginDay' => $beginDay,
            'endDay' => $endDay
        ]);
    }

    /**
     * 更新记录导出
     *
     * @param \Illuminate\Http\Request $request
     */
    public function export(Request $request){
        $dates = $request->all();
        $records = VersionRecord::ofDates($dates)->orderBy('created_at', 'DESC')->get();

        // Creating the new document...
        $phpWord = new PhpWord();
        $styleTable = array('borderSize' => 1, 'borderColor' => '999999');
        $cellAlignCenter = array('align' => 'center');
        $phpWord->setDefaultFontName('仿宋');
        $phpWord->setDefaultFontSize(12);
        $phpWord->addTableStyle('table', $styleTable);

        $style = [
            'marginTop' => 500,
            'marginRight' => 0,
            'marginLeft' => 500,
            'marginBottom' => 0,
            'orientation' => 'landscape'
        ];

        $section = $phpWord->addSection($style);
        $table = $section->addTable('table');

        $titles = [
            '终端类型' => 1200,
            '更新时间' => 2000,
            '版本号' => 1000,
            '版本名称' => 2000,
            '更新内容' => 4000,
        ];
        $table->addRow();
        foreach ($titles as $name => $width) {
            $table->addCell($width, ['fill' => '#f2f2f2'])->addText($name, ['bold' => true], $cellAlignCenter);
        }
        foreach($records as $record) {
            $table->addRow();
            $table->addCell()->addText(cons()->valueLang('push_device' , $record->type), null, $cellAlignCenter);
            $table->addCell()->addText($record->created_at, null, $cellAlignCenter);
            $table->addCell()->addText($record->version_no, null, $cellAlignCenter);
            $table->addCell()->addText($record->version_name, null, $cellAlignCenter);
            $table->addCell()->addText($record->content, null, $cellAlignCenter);
        }


        $name = '更新记录.docx';
        $phpWord->save($name, 'Word2007', true);
    }

    /**
     * create
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $device = cons()->lang('push_device');
        array_shift($device);
        return view('admin.operation.update', ['device' => $device]);
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
        $exist = VersionRecord::where([
            'version_no' => $attributes['version_no'],
            'version_name' => $attributes['version_name'],
            'type' => cons('push_device.' . $attributes['type']),
        ])->pluck('id');

        if ($exist) {
            return $this->error('版本名已存在');
        }
        if (VersionRecord::create($attributes)->exists) {
            $redis = new RedisService;
            $type = array_get($attributes, 'type');
            $url = array_get($attributes, 'download_url');
            $redisKey = 'app-link:' . $type;
            $redis->setRedis($redisKey, $url);
            return $this->success('添加成功');
        }

        return $this->error('添加失败');
    }

    /**
     * 操作记录
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function notification(Request $request)
    {
        $dates = $request->all();
        $beginDay = array_get($dates, 'begin_day');
        $endDay = array_get($dates, 'end_day');

        $notifications = Notification::ofDates($dates)->with('user')->paginate();
        return view('admin.operation.notification', [
            'notifications' => $notifications,
            'beginDay' => $beginDay,
            'endDay' => $endDay
        ]);
    }

    /**
     * 操作记录导出
     *
     * @param \Illuminate\Http\Request $request
     */
    public function notificationExport(Request $request){
        $dates = $request->all();
        $notifications = Notification::ofDates($dates)->with('user')->get();

        // Creating the new document...
        $phpWord = new PhpWord();
        $styleTable = array('borderSize' => 1, 'borderColor' => '999999');
        $cellAlignCenter = array('align' => 'center');
        $phpWord->setDefaultFontName('仿宋');
        $phpWord->setDefaultFontSize(12);
        $phpWord->addTableStyle('table', $styleTable);

        $style = [
            'marginTop' => 500,
            'marginRight' => 0,
            'marginLeft' => 500,
            'marginBottom' => 0,
            'orientation' => 'landscape'
        ];

        $section = $phpWord->addSection($style);
        $table = $section->addTable('table');

        $titles = [
            '操作姓名' => 2000,
            '账号' => 1000,
            'ID' => 800,
            '操作时间' => 3000,
            '操作内容' => 8000,
        ];
        $table->addRow();
        foreach ($titles as $name => $width) {
            $table->addCell($width, ['fill' => '#f2f2f2'])->addText($name, ['bold' => true], $cellAlignCenter);
        }

        foreach($notifications as $notification) {
            $table->addRow();
            $table->addCell()->addText($notification->user->real_name, null, $cellAlignCenter);
            $table->addCell()->addText($notification->user->name, null, $cellAlignCenter);
            $table->addCell()->addText($notification->user_id, null, $cellAlignCenter);
            $table->addCell()->addText($notification->created_at, null, $cellAlignCenter);
            $table->addCell()->addText(strip_tags($notification->content), null, $cellAlignCenter);
        }

        $name = '操作记录.docx';
        $phpWord->save($name, 'Word2007', true);

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
