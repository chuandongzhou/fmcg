<?php

namespace App\Http\Controllers\Index\personal;

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
        //dd(Hash::check('654321', '$2y$10$cZy0YHMYelOKXzhCdslPDeqbCGRnupt29TVnobMU4PBHmMwRVcnd2'));
        return view('index.personal.password');
    }
}
