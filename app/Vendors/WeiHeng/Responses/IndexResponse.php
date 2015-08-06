<?php

namespace WeiHeng\Responses;

class IndexResponse extends Response
{

    /**
     * 成功返回
     *
     * @param mixed $content
     * @param string|null $to
     * @param array $data
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function success($content = '', $to = null, $data = [])
    {
        return $this->response(true, $content, $to, $data);
    }

    /**
     * 失败返回
     *
     * @param mixed $content
     * @param string|null $to
     * @param array $data
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function error($content = '', $to = null, $data = [])
    {
        return $this->response(false, $content, $to, $data);
    }

    /**
     * 信息返回
     *
     * @param bool $status
     * @param mixed $content
     * @param string|null $to
     * @param array $data
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function response($status, $content = '', $to = null, $data = [])
    {
        if ($this->request->ajax()) {
            return $this->response->json([
                'status' => $status,
                'to' => $to,
                'content' => $content,
                'data' => $data
            ]);
        }

        if (is_null($to)) {
            $to = url($this->request->headers->get('referer'));
        }

        if ($status) {
            $data['content'] = $content;

            return redirect($to)->with($data);
        }

        return redirect($to)->with($data)->withErrors($content);
    }

}