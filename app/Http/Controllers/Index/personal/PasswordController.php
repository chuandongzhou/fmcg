<?php

namespace App\Http\Controllers\Index\personal;

use App\Http\Controllers\Index\Controller;

use App\Http\Requests;

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