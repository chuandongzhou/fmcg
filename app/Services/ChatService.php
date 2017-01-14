<?php

namespace App\Services;

use App\Models\User;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class ChatService
{

    protected $appKey, $secretKey, $commonPassword;

    public function __construct()
    {
        $messageConf = config('push.im');

        $this->appKey = $messageConf['app_key'];
        $this->secretKey = $messageConf['app_secret'];
        $this->commonPassword = $messageConf['message_password'];
    }

    /**
     * 增加或修改用户到服务端
     *
     * @param $item
     * @param bool|false $isUpdate
     * @return mixed|\ResultSet|\SimpleXMLElement
     */
    public function usersHandle($item, $isUpdate = false)
    {
        $top = new \TopClient();
        $top->appkey = $this->appKey;
        $top->secretKey = $this->secretKey;
        $top->format = 'json';
        $req = $isUpdate ? new \OpenimUsersUpdateRequest() : new \OpenimUsersAddRequest();
        $userInfos = new \Userinfos();

        $userInfos->nick = $item instanceof User ? $item->shop->name : $item->name;
        $userInfos->icon_url = $item instanceof User ? $item->shop->logo_url : $item->logo_url;
        $userInfos->mobile = $item instanceof User ? $item->backup_mobile : $item->user->backup_mobile;
        $userInfos->userid = $item instanceof User ? $item->shop->id : $item->id;
        $userInfos->password = $this->commonPassword;
        $userInfos->remark = $item instanceof User ? $item->shop->introduction : $item->introduction;
        $userInfos->address = $item instanceof User ? $item->shop->address : $item->address;
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
    public function deleteUsers($userIds)
    {
        $top = new \TopClient();
        $top->appkey = $this->appKey;
        $top->secretKey = $this->secretKey;
        $req = new \OpenimUsersDeleteRequest();
        $req->setUserids($userIds);
        $resp = $top->execute($req);
        return $resp->openim_users_delete_response;
    }

}