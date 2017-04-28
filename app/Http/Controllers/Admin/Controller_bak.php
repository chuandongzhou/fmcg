<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use WeiHeng\Responses\AdminResponse;

abstract class ControllerBak extends BaseController
{
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

    /**
     * Paginate the given query.
     *
     * @param $collect
     * @param int $perPage
     * @param string $pageName
     * @param null $page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected function paginate(Collection $collect , $perPage = 15,  $pageName = 'page', $page = null)
    {
        $total = $collect->count();

        $collect = $collect->forPage(
            $page = $page ?: Paginator::resolveCurrentPage($pageName),
            $perPage
        );

        return new LengthAwarePaginator($collect, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

}
