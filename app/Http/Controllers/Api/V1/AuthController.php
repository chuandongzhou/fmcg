<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/17
 * Time: 14:22
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\v1\BackupPasswordRequest;
use App\Http\Requests\Api\v1\LoginRequest;
use App\Http\Requests\Api\v1\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Hash;

class AuthController extends Controller
{
    /**
     * 登录
     *
     * @param \App\Http\Requests\Api\v1\LoginRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postLogin(LoginRequest $request)
    {
        //TODO: 判断account
        $account = $request->input('account');
        $password = $request->input('password');
        $type = $request->input('type');

        $user = User::where('user_name', $account)->first();

        if (!$user || !Hash::check($password, $user->password) || $user->type != $type) {
            return $this->error('账号或密码错误');
        }

        if ($user->status != cons('status.on')) {
            return $this->error('账号已锁定');
        }

        if ($user->audit_status != cons('user.audit_status.pass')) {
            return $this->error('账户未审核或审核失败');
        }
        $user->load('shop');
        if (!is_null($user->shop)) {
            $user->shop->address_name = $user->shop->address;
        }
        auth()->login($user, true);
        return $this->success(['user' => $user]);
    }

    /**
     * 注册
     *
     * @param \App\Http\Requests\Api\v1\RegisterRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     * @throws \Exception
     */
    public function postRegister(RegisterRequest $request)
    {
        $userInput = $request->only('user_name', 'password', 'backup_mobile', 'type');
        $user = User::Create($userInput);
        if ($user->exists) {
            //商店
            $shopInput = $request->except('username', 'password', 'backup_mobile', 'type');
            $shopModel = $user->shop();

            if ($shopModel->create($shopInput)->exists) {
                session(['shop.name' => $shopInput['name']]);
                return $this->success('注册成功');
            } else {
                $user->delete();
                return $this->error('注册用户时遇到问题');
            }
        }
        return $this->error('注册用户时遇到问题');
    }

    public function postBackup(BackupPasswordRequest $request)
    {
        $data = $request->all();
        $user = User::with('shop')->where('user_name', $data['user_name'])->first();
        if (!$user || $user->backup_mobile != $data['backup_mobile']) {
            return $this->error('用户不存在或密保手机不正确');
        }
        if ($user->shop->license_num != $data['license_num']) {
            return $this->error('营业执照编号不正确');
        }
        if ($user->fill(['password' => $data['password']])->save()) {
            return $this->success('修改密码成功');
        }
        return $this->success('修改密码时出现错误');
    }

    /**
     * 退出登录
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function getLogout()
    {
        auth()->logout();
        return $this->success();
    }

    /**
     *  打印上传数据
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function anyRequest(Request $request)
    {
        $result = $request->all();
        $result['has_file'] = count($request->file());
        return $result;
    }
}