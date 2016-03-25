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
use App\Http\Requests\Api\v1\LoginRequest;
use App\Http\Requests\Api\v1\RegisterRequest;
use App\Models\User;
use App\Services\RedisService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Hash;
use Illuminate\Support\Facades\Redis;

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

        if ($user->fill(['last_login_at' => $nowTime])->save()) {
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
        $validateCodeConf = cons('validate_code');
        $redisKey = $validateCodeConf['backup']['pre_name'] . $data['user_name'];

        $code = $request->input('code');
        $redis = Redis::connection();
        if (!$redis->exists($redisKey) || $redis->get($redisKey) != $code) {
            return $this->error('验证码错误');
        }
        $redis->del($redisKey);
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
        $validateCodeConf = cons('validate_code');
        $redisKey = $validateCodeConf['backup']['pre_name'] . $data['user_name'];
        $redis = Redis::connection();
        if ($redis->exists($redisKey)) {
            return $this->error('短信发送过于频繁');
        }
        $code = str_random($validateCodeConf['length']);

        $result = app('pushbox.sms')->send('code', $data['backup_mobile'], $code);
        if (empty($result)) {
            return $this->error('发送失败,请重试');
        } else {
            RedisService::setRedis($redisKey, $code, $validateCodeConf['backup']['expire']);
            return $this->success('发送成功');
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
}