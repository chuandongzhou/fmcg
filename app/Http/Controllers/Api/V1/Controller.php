<?php namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller as BaseController;
use WeiHeng\Responses\Apiv1Response;

abstract class Controller extends BaseController
{
    /**
     * 返回成功响应
     *
     * @param mixed $data
     * @param array $headers
     * @param int $options
     * @return \WeiHeng\Responses\Apiv1Response
     */
    protected function success($data = null, $headers = [], $options = 0)
    {
        return $this->response('success', $data, $headers, $options);
    }

    /**
     * 返回创建成功响应
     *
     * @param mixed $data
     * @param array $headers
     * @param int $options
     * @return \WeiHeng\Responses\Apiv1Response
     */
    protected function created($data = null, $headers = [], $options = 0)
    {
        return $this->response('created', $data, $headers, $options);
    }

    /**
     * 返回异步处理响应
     *
     * @param mixed $data
     * @param array $headers
     * @param int $options
     * @return \WeiHeng\Responses\Apiv1Response
     */
    protected function accepted($data = null, $headers = [], $options = 0)
    {
        return $this->response('accepted', $data, $headers, $options);
    }

    /**
     * 返回分页信息
     *
     * @param mixed $data
     * @param array $headers
     * @param int $options
     * @return \WeiHeng\Responses\Apiv1Response
     */
    protected function partial($data = null, $headers = [], $options = 0)
    {
        return $this->response('partial_content', $data, $headers, $options);
    }

    /**
     * 返回授权错误
     *
     * @param mixed $message
     * @param array $headers
     * @param int $options
     * @return \WeiHeng\Responses\Apiv1Response
     */
    protected function error($message = null, $headers = [], $options = 0)
    {
        return $this->response('bad_request', is_string($message) ? ['message' => $message] : $message, $headers,
            $options);
    }

    /**
     * 返回授权错误
     *
     * @param mixed $message
     * @param array $headers
     * @param int $options
     * @return \WeiHeng\Responses\Apiv1Response
     */
    protected function unauthorized($message = null, $headers = [], $options = 0)
    {
        return $this->response('unauthorized', is_string($message) ? ['message' => $message] : $message, $headers,
            $options);
    }

    /**
     * 返回权限不足
     *
     * @param mixed $message
     * @param array $headers
     * @param int $options
     * @return \WeiHeng\Responses\Apiv1Response
     */
    protected function forbidden($message = null, $headers = [], $options = 0)
    {
        return $this->response('forbidden', is_string($message) ? ['message' => $message] : $message, $headers,
            $options);
    }

    /**
     * 返回找不到资源
     *
     * @param mixed $message
     * @param array $headers
     * @param int $options
     * @return \WeiHeng\Responses\Apiv1Response
     */
    protected function notFound($message = null, $headers = [], $options = 0)
    {
        return $this->response('not_found', is_string($message) ? ['message' => $message] : $message, $headers,
            $options);
    }

    /**
     * 返回无效参数
     *
     * @param string $field
     * @param string $message
     * @param array $headers
     * @param int $options
     * @return \WeiHeng\Responses\Apiv1Response
     */
    protected function invalidParam($field = '', $message = '', $headers = [], $options = 0)
    {
        return $this->invalidParams([$field => [$message]], $headers, $options);
    }

    /**
     * 返回无效参数
     *
     * @param mixed $errors
     * @param array $headers
     * @param int $options
     * @return \WeiHeng\Responses\Apiv1Response
     */
    protected function invalidParams($errors = null, $headers = [], $options = 0)
    {
        if ($errors instanceof \Illuminate\Validation\Validator) {
            $errors = $errors->errors()->getMessages();
        }

        if (is_array($errors)) {
            $errors = ['errors' => $errors];
        }

        return $this->response('invalid_params', $errors, $headers, $options);
    }

    /**
     * 响应
     *
     * @param string $id
     * @param mixed $data
     * @param array $headers
     * @param int $options
     * @return \WeiHeng\Responses\Apiv1Response
     */
    protected function response($id = 'internal_server_error', $data = null, $headers = [], $options = 0)
    {
        return new Apiv1Response($id, $data, $headers, $options);
    }
}
