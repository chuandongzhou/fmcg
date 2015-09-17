<?php

namespace App\Http\Controllers\Index\Personal;

use App\Http\Controllers\Index\Controller;

use App\Http\Requests;
use Hash;

class PasswordController extends Controller
{


    /**
     * 修改密码
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('index.personal.password');
    }
}
