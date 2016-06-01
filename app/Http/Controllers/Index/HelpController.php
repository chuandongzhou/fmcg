<?php
/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/9/6
 * Time: 15:54
 */
namespace App\Http\Controllers\Index;

use Illuminate\Http\Request;

class HelpController extends Controller
{

    public function index(Request $request)
    {
        $id = $request->input('id', 1);
        $id = ($id < 1 || $id > 14) ? 1 : $id;

        return view('index.help.help-' . str_pad($id, 2, "0", STR_PAD_LEFT), ['id' => $id]);
    }
}