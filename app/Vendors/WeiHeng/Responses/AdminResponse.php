<?php

namespace WeiHeng\Responses;

class AdminResponse extends Response
{

    /**
     * 成功返回
     *
     * @param string $content
     * @param string|null $to
     * @param array $data
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function success($content, $to = null, $data = [])
    {
        return $this->response(true, $content, $to, $data);
    }

    /**
     * 失败返回
     *
     * @param string $content
     * @param string|null $to
     * @param array $data
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function error($content, $to = null, $data = [])
    {
        return $this->response(false, $content, $to, $data);
    }

    /**
     * 信息返回
     *
     * @param bool $status
     * @param string|array $content
     * @param string|null $to
     * @param array $data
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function response($status, $content, $to = null, $data = [])
    {
        if ($this->request->ajax()) {
            $token = csrf_token();

            return $this->response->json(compact('status', 'to', 'token', 'content', 'data'));
        }

        if (is_null($to)) {
            $to = url($this->request->headers->get('referer'));
        }

        return redirect($to)->with('notification', compact('status', 'content', 'data'));
    }

}