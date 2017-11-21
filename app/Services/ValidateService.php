<?php

namespace App\Services;

use Germey\Geetest\Geetest;
use Illuminate\Http\Request;

class ValidateService
{

    /**
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function validateGeetest(Request $request)
    {
        list($geetest_challenge, $geetest_validate, $geetest_seccode) = array_values($request->only('geetest_challenge',
            'geetest_validate', 'geetest_seccode'));
        if (session()->get('gtserver') == 1) {
            if (Geetest::successValidate($geetest_challenge, $geetest_validate, $geetest_seccode,
                session()->get('user_id'))
            ) {
                return true;
            }
            return false;
        } else {
            if (Geetest::failValidate($geetest_challenge, $geetest_validate, $geetest_seccode)
            ) {
                return true;
            }
            return false;
        }

    }
}