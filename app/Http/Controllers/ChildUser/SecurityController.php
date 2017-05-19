<?php

namespace App\Http\Controllers\ChildUser;


class SecurityController extends Controller
{
    /**
     * 修改密码
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('child-user.security.index');
    }
}
