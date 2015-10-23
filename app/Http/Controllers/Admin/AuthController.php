<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/10/20
 * Time: 14:49
 */
namespace App\Http\Controllers\Admin;

class AuthController extends Controller
{
    public function login()
    {
        return view('admin.auth.login');
    }
}