<?php

namespace App\Http\Controllers\Index\Personal;

use App\Http\Controllers\Index\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\Index\CodeRequest;
use Illuminate\Session\Store as Session;

class SecurityController extends Controller
{
    /**
     * 修改密码
     *
     * @return \Illuminate\View\View
     */
    public function getIndex(Session $session)
    {
        $session->forget(['edit.backup-phone', 'edit.password']);
        return view('index.personal.security.security-index');
    }

    /**
     * 获取原密保手机验证码页面
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Session\Store $session
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getValidatePhone(Request $request, Session $session)
    {

        $type = $request->input('type', 'password');

        $session->put('validate.for', $type);

        $backupPhone = auth()->user()->backup_mobile;


        return view('index.personal.security.validate-phone',
            ['backupPhone' => obfuscate_string($backupPhone, 4), 'type' => $type]);
    }

    /**
     * 重新设置登录密码
     *
     * @return \Illuminate\View\View
     */
    public function getPassword(Session $session)
    {
        if (!$session->get('edit.password')) {
            return redirect(url('personal/security'));
        }
        return view('index.personal.security.password');
    }

    /**
     * 重新设置密保手机
     *
     * @return \Illuminate\View\View
     */
    public function getBackupPhone(Session $session)
    {
        if (!$session->get('edit.backup-phone')) {
            return redirect(url('personal/security'));
        }
        return view('index.personal.security.backup-phone');
    }

    /**
     * 根据原密码设置登录密码
     *
     * @return \Illuminate\View\View
     */
    public function getByOldPassword()
    {
        return view('index.personal.security.by-old-password');
    }
}
