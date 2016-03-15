<?php

namespace App\Services;

use Psr\Http\Message\ResponseInterface;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class MessageService
{

    protected $appKey, $secretKey, $commonPassword;

    public function __construct()
    {
        $messageConf = config('push.top');

        $this->appKey = $messageConf['app_key'];
        $this->secretKey = $messageConf['app_secret'];
        $this->commonPassword = $messageConf['message_password'];
    }

    /**
     * 增加或修改用户到服务端
     *
     * @param $user
     * @param bool|false $isUpdate
     * @return mixed|\ResultSet|\SimpleXMLElement
     */
    public function usersHandle($user, $isUpdate = false)
    {
        $top = new \TopClient();
        $top->appkey = $this->appKey;
        $top->secretKey = $this->secretKey;
        $top->format = 'json';
        $req = $isUpdate ? new \OpenimUsersUpdateRequest() : new \OpenimUsersAddRequest();
        $userInfos = new \Userinfos();
        $userInfos->nick = $user->shop->name;
        $userInfos->icon_url = $user->shop->logo_url;
        //$userInfos->email='';
        $userInfos->mobile = $user->backup_mobile;
        //$userInfos->taobaoid="tbnick123";
        $userInfos->userid = $user->shop->id;
        $userInfos->password = $this->commonPassword;
        $userInfos->remark = $user->shop->introduction;
        //$userInfos->extra = "demo";
        //$userInfos->career = "demo";
        //$userInfos->vip = "demo";
        $userInfos->address = $user->shop->address;
        //$userInfos->name = "demo";
        //$userInfos->age = "123";
        //$userInfos->gender = "demo";
        //$userInfos->wechat = "demo";
        //$userInfos->qq = "demo";
        //$userInfos->weibo = "demo";
        $req->setUserinfos(json_encode($userInfos));
        $resp = $top->execute($req);

        return $resp;
    }

    /**
     * 查询用户
     *
     * @param $userIds  用户id序列,逗号隔开
     * @return mixed|\ResultSet|\SimpleXMLElement
     */
    public function checkUsers($userIds)
    {
        $top = new \TopClient();
        $top->appkey = $this->appKey;
        $top->secretKey = $this->secretKey;
        $top->format = 'json';
        $req = new \OpenimUsersGetRequest();
        $req->setUserids($userIds);
        $resp = $top->execute($req);
        return $resp;
    }

    /**
     * 删除用户
     *
     * @param $userIds 用户id序列，逗号隔开
     * @return \SimpleXMLElement[]
     */
    public function deleteUsers($userIds){
        $top = new \TopClient();
        $top->appkey =  $this->appKey;
        $top->secretKey =  $this->secretKey;
        $req = new \OpenimUsersDeleteRequest();
        $req->setUserids($userIds);
        $resp = $top->execute($req);
        return $resp->openim_users_delete_response;
    }

}