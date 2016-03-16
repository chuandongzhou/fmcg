<?php

namespace App\Http\Controllers\Admin;


use App\Models\User;
use App\Services\MessageService;

class MessageController extends Controller
{
    public function index()
    {
//        $users = User::where('audit_status', 1)->where('user_name', 'supplier')->with('shop')->first();
//        $result = (new MessageService())->usersHandle($users);
//        dd($result);
//

        $userIds = User::where('audit_status', 1)->with('shop')->get()->implode('shop.id', ',');
        $remoteUsers = (new MessageService())->checkUsers($userIds);
        $userInfos = [];
        if (isset($remoteUsers->userinfos->userinfos) && count($remoteUsers->userinfos->userinfos) > 0) {
            $userInfos = $remoteUsers->userinfos->userinfos;
        }
        return view('admin.message.index', ['userInfos' => $userInfos]);
    }
}
