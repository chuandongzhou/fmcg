<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;

/**
 * 公共接口
 *
 * @package App\Http\Controllers\Api\v1
 */
class PublicController extends Controller
{

    /**
     * 记录JS错误
     *
     * @param \Illuminate\Http\Request $request
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function jsErrorStore(Request $request)
    {
        $content = (array)$request->all();
        $content['referer'] = $request->server('HTTP_REFERER');
        $content['ua'] = $request->server('HTTP_USER_AGENT');
        info('JS错误', $content);

        return $this->created();
    }

}