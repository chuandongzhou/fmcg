<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CreateWeixinArticleRequest;
use App\Models\WeixinArticle;

class WeixinArticleController extends Controller
{
    protected $params = [
        'type' => 'article',
        'size' => ['width' => '60', 'height' => '60'],
        'route' => 'article'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = WeixinArticle::where('type', cons('admin.weixin_article.type.' . $this->params['type']))->get();
        return view('admin.weixin.article-index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $article = new WeixinArticle();
        return view('admin.weixin.article', ['article' => $article, 'params' => $this->params]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\CreateWeixinArticleRequest $request
     * @return \Illuminate\Http\RedirectResponse|\WeiHeng\Responses\AdminResponse
     */
    public function store(CreateWeixinArticleRequest $request)
    {
        $attributes = $request->all();
        return WeixinArticle::create($attributes) ? $this->success('添加成功') : $this->error('添加时出现问题');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * @param \App\Models\WeixinArticle $article
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(WeixinArticle $article)
    {
        return view('admin.weixin.article', ['article' => $article, 'params' => $this->params]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\CreateWeixinArticleRequest $request
     * @param \App\Models\WeixinArticle $article
     * @return \Illuminate\Http\RedirectResponse|\WeiHeng\Responses\AdminResponse
     */
    public function update(CreateWeixinArticleRequest $request, WeixinArticle $article)
    {
        return $article->fill($request->all())->save() ? $this->success('修改成功') : $this->error('删除时出现问题');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\WeixinArticle $article
     * @return \Illuminate\Http\RedirectResponse|\WeiHeng\Responses\AdminResponse
     */
    public function destroy(WeixinArticle $article)
    {
        return $article->delete() ? $this->success('删除成功') : $this->error('删除时出现问题');
    }
}
