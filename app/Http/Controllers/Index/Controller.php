<?php

namespace App\Http\Controllers\Index;

use Illuminate\Routing\Controller as BaseController;
use WeiHeng\Responses\IndexResponse;

abstract class Controller extends BaseController
{

    /**
     * 返回成功提示
     *
     * @param mixed $content
     * @param null $to
     * @param array $data
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function success($content = '', $to = null, $data = [])
    {
        return (new IndexResponse)->success($content, $to, $data);
    }

    /**
     * 返回错误提示
     *
     * @param mixed $content
     * @param null $to
     * @param array $data
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function error($content = '', $to = null, $data = [])
    {
        return (new IndexResponse)->error($content, $to, $data);
    }

}
