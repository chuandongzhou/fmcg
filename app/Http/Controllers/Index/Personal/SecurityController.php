<?php

namespace App\Http\Controllers\Index\Personal;

use App\Http\Controllers\Index\Controller;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Requests\Index\CodeRequest;
use App\Services\RedisService;
use Illuminate\Support\Facades\Redis;
class SecurityController extends Controller
{


    /**
     * 修改密码
     *
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {

        return view('index.personal.security.security-index');
    }

    /**
     * 获取原密保手机验证码页面
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function getOldBackupPhone(Request $request){

        return view('index.personal.security.by-backup-phone',['type'=>$request->input('type'),'backupPhone'=>auth()->user()->backup_mobile]);
    }
    /**
     * 重新设置登录密码
      * @return \Illuminate\View\View
     */
    public function getEditPassword(){
        return view('index.personal.security.edit-password');
    }
    /**
     * 重新设置密保手机
     * @return \Illuminate\View\View
     */
    public function getEditBackupPhone(){
        return view('index.personal.security.edit-backup-phone');
    }
    /**
     * 根据原密码设置登录密码
     * @return \Illuminate\View\View
     */
    public function getByOldPassword(){
        return view('index.personal.security.by-old-password');
    }
}
