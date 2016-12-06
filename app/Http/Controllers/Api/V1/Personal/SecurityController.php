<?php

namespace App\Http\Controllers\Api\V1\Personal;

use App\Http\Controllers\Api\v1\Controller;

use App\Http\Requests\Api\v1\UpdatePasswordRequest;
use App\Http\Requests\Api\v1\CodeRequest;
use App\Http\Requests\Api\v1\UpdateBackupPhoneRequest;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Services\RedisService;
use App\Services\CodeService;

class SecurityController extends Controller
{

    public function password(UpdatePasswordRequest $request)
    {
        $attributes = $request->all();

        $user = auth()->user();

        if (Hash::check($attributes['old_password'], $user->password)) {
            if ($user->fill(['password' => $attributes['password']])->save()) {
                return $this->success('修改密码成功');
            }
            return $this->error('修改密码时遇到错误');

        }
        return $this->error('原密码错误');
    }

    /**
     * 安全设置发送原手机验证码
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function backupSms()
    {
        $user = auth()->user();;
        if ($user->status != cons('status.on')) {
            return $this->error('账号已锁定');
        }
        if ($user->audit_status != cons('user.audit_status.pass')) {
            return $this->error('账户未审核');
        }
        //发送验证码
        $res = (new CodeService)->sendCode('code','backup', $user->backup_mobile, $user->user_name);
        if ($res['status']) {
            return $this->success($res['mes']);
        } else {
            return $this->error($res['mes']);
        }

    }

    /**
     * 验证原手机验证码
     *
     * @param \App\Http\Requests\Api\v1\CodeRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */

    public function validateBackupSms(CodeRequest $request)
    {
        $code = $request->input('code');
        $user = auth()->user();
        //验证验证码
        $res = (new CodeService)->validateCode('backup', $code, $user->user_name);
        if ($res['status']) {
            return $this->success($res['mes']);
        } else {
            return $this->error($res['mes']);
        }

    }

    /**
     * 发送新密保手机验证码
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function sendNewBackupSms(Request $request)
    {
        $phone = $request->input('backup_mobile');
        if (empty($phone)) {
            return $this->error('密保手机不能为空');
        }
        $user = auth()->user();
        //发送验证码
        $res = (new CodeService)->sendCode('code','backup', $phone, $user->user_name);
        if ($res['status']) {
            return $this->success($res['mes']);
        } else {
            return $this->error($res['mes']);
        }
    }

    /**
     * 修改密保手机
     * @parm \App\Http\Requests\Api\v1\UpdateBackupPhoneRequest $request
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function editBackupPhone(UpdateBackupPhoneRequest $requst)
    {
        $code = $requst->input('code');
        $phone = $requst->input('backup_mobile');
        $user = auth()->user();
        //验证验证码
        $res = (new CodeService)->validateCode('backup', $code, $user->user_name);
        if (!$res['status']) {
            return $this->error($res['mes']);
        }
        if ($user->fill(['backup_mobile' => $phone])->save()) {
            return $this->success('修改密保手机成功');
        }
        return $this->error('修改密保手机时遇到错误');


    }

    /**
     * 验证原密码
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function validateOldPassword(Request $request)
    {
        $password = $request->input('old_password');

        $user = auth()->user();

        if (Hash::check($password, $user->password)) {
            return $this->success('验证成功');
        }
        return $this->error('原密码错误');

    }

    /**
     * 修改密码
     *
     * @param \App\Http\Requests\Api\v1\UpdatePasswordRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */

    public function editPassword(UpdatePasswordRequest $request)
    {
        $password = $request->input('password');

        $user = auth()->user();
        if ($user->fill(['password' => $password])->save()) {
            return $this->success('修改密码成功');
        }
        return $this->error('修改失败');
    }


}
