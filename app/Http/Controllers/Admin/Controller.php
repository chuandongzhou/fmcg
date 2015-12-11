<?php

namespace App\Http\Controllers\Admin;

use App\Models\Node;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use WeiHeng\Responses\AdminResponse;

abstract class Controller extends BaseController
{

    /*public function __construct(Request $request)
    {
        $requestUrl = substr(request()->getRequestUri(), 1);

        echo $requestUrl;

        $nodeId = Node::where('url', $requestUrl)->orWhere('manage_url', $requestUrl)->pluck('id');

        if ($request->method() == 'GET' && !admin_auth()->user()->node_ids->has($nodeId)) {

        }
    }*/

    /**
     * 返回成功提示
     *
     * @param $content
     * @param null $to
     * @param array $data
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function success($content, $to = null, $data = [])
    {
        return (new AdminResponse)->success($content, $to, $data);
    }

    /**
     * 返回错误提示
     *
     * @param $content
     * @param null $to
     * @param array $data
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function error($content, $to = null, $data = [])
    {
        return (new AdminResponse)->error($content, $to, $data);
    }

}
