<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/17
 * Time: 14:22
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\v1\BackupPasswordRequest;
use App\Http\Requests\Api\v1\BackupSendSmsRequest;
use App\Http\Requests\Api\v1\RegisterRequest;
use App\Http\Requests\Api\v1\RegisterUserRequest;
use App\Http\Requests\Api\v1\RegisterSetPasswordRequest;
use App\Http\Requests\Api\v1\RegisterUserShopRequest;
use App\Models\User;
use App\Services\RedisService;
use App\Services\CodeService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Hash;
use Illuminate\Support\Facades\Redis;

class AuthController extends Controller
{

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * 登录
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postLogin(Request $request)
    {
        $account = $request->input('account');
        $password = $request->input('password');
        if (!$account || !$password) {
            return $this->invalidParam('password', '账号或密码不能为空');
        }
        $type = $request->input('type');

        $user = User::where('user_name', $account)->first();

        if (!$user || !Hash::check($password, $user->password) || $user->type != $type) {
            return $this->invalidParam('password', '账号或密码错误');
        }

        if ($user->status != cons('status.on')) {
            return $this->invalidParam('password', '账号已锁定');
        }

        if ($user->audit_status != cons('user.audit_status.pass')) {
            return $this->invalidParam('password', '账户未审核或审核不通过');
        }
        $user->setVisible(['id', 'user_name', 'type', 'audit_status', 'backup_mobile', 'shop']);
        if (!is_null($user->shop)) {
            $user->shop->address_name = $user->shop->address;
        }

        $nowTime = Carbon::now();

        if ($user->fill(['last_login_at' => $nowTime, 'last_login_ip' => $request->ip()])->save()) {
            auth()->login($user, true);
            return $this->success(['user' => $user]);
        }
        return $this->invalidParam('password', '登录失败，请重试');

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

            if ($user->type == cons('user.type.retailer')) {
                unset($shopInput['area']);
            }

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

    /**
     * 注册用户
     *
     * @param \App\Http\Requests\Api\v1\RegisterUserRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     * @throws \Exception
     */
    public function postRegisterUser(RegisterUserRequest $request)
    {
        $data = $request->only('user_name', 'backup_mobile', 'type');
        //验证验证码
        $code = $request->input('code');
        $res = (new CodeService)->validateCode('register', $code, $data['user_name']);
        if (!$res['status']) {
            return $this->error($res['mes']);
        }
        session(['user' => $data]);
        return $this->success('验证成功');

    }

    /**
     * 设置密码
     *App\Http\Requests\Api\v1\RegisterSetPasswordRequest $request
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postSetPassword(RegisterSetPasswordRequest $request)
    {
        session(['user' => $request->all()]);
        return $this->success('设置密码成功');
    }

    /**
     * 添加商户信息
     *App\Http\Requests\Api\v1\RegisterUserShopRequest $request
     *
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postUserShop(RegisterUserShopRequest $request)
    {

        if (empty($request->input('user_name'))) {
            return $this->error('注册用户时遇到问题');
        }
        $user = User::where('user_name', $request->input('user_name'))->first();
        $shopInput = $request->except('username');
        $shopModel = $user->shop();

        if ($user->type == cons('user.type.retailer')) {
            unset($shopInput['area']);
        }

        if ($shopModel->create($shopInput)->exists) {

            return $this->success('注册成功');
        } else {
            $user->delete();
            return $this->error('注册用户时遇到问题');
        }
    }

    /**
     * 找回密码
     *
     * @param \App\Http\Requests\Api\v1\BackupPasswordRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
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
        //验证验证码
        $code = $request->input('code');
        $res = (new CodeService)->validateCode('backup', $code, $data['user_name']);
        if (!$res['status']) {
            return $this->error($res['mes']);
        }

        if ($user->fill(['password' => $data['password']])->save()) {
            return $this->success('修改密码成功');
        }
        return $this->success('修改密码时出现错误');
    }

    /**
     * 发送短信验证码
     *
     * @param \App\Http\Requests\Api\v1\BackupSendSmsRequest $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function postSendSms(BackupSendSmsRequest $request)
    {
        $data = $request->all();
        $user = User::with('shop')->where('user_name', $data['user_name'])->first();
        if ($user->status != cons('status.on')) {
            return $this->error('账号已锁定');
        }

        if ($user->audit_status != cons('user.audit_status.pass')) {
            return $this->error('账户未审核');
        }
        if (!$user || $user->backup_mobile != $data['backup_mobile']) {
            return $this->error('密保手机错误');
        }
        if ($user->shop->license_num != $data['license_num']) {
            return $this->error('营业执照错误');
        }
        //发送验证码
        $res = (new CodeService)->sendCode('code', 'backup', $request->input('backup_mobile'),
            $request->input('user_name'));
        if ($res['status']) {
            return $this->success($res['mes']);
        } else {
            return $this->error($res['mes']);
        }
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

    /**
     * 发送注册验证码
     *
     */
    public function postRegSendSms(Request $request)
    {
        if (empty($request->input('backup_mobile'))) {
            return $this->error('密保手机不能为空');
        }
        //发送验证码
        $res = (new CodeService)->sendCode('register', 'register', $request->input('backup_mobile'),
            $request->input('user_name'));
        if ($res['status']) {
            return $this->success($res['mes']);
        } else {
            return $this->error($res['mes']);
        }

    }

}