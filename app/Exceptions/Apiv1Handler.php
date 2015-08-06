<?php

namespace App\Exceptions;

use WeiHeng\Responses\Apiv1Response;

class Apiv1Handler
{

    /**
     * 返回错误
     *
     * @param \Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(\Exception $e)
    {
        if (is_a($e, 'Symfony\Component\HttpKernel\Exception\NotFoundHttpException')) {
            return new Apiv1Response('not_found');
        }

        return new Apiv1Response();
    }

}