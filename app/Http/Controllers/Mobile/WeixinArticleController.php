<?php

namespace App\Http\Controllers\Mobile;


use App\Models\WeixinArticle;

class WeixinArticleController extends Controller
{

    public function index()
    {
        $articles = WeixinArticle::with('image')->where('type',
            cons('admin.weixin_article.type.article'))->orderBy('id', 'DESC')->paginate();

        $banners = WeixinArticle::with('image')->where('type',
            cons('admin.weixin_article.type.banner'))->orderBy('id', 'DESC')->take(5)->get();


        return view('mobile.weixin-article.index', compact('articles', 'banners'));
    }

    /**
     * 异步获取文章
     *
     * @return \Illuminate\Http\RedirectResponse|\WeiHeng\Responses\IndexResponse
     */
    public function articles()
    {
        $articles = WeixinArticle::with('image')->where('type',
            cons('admin.weixin_article.type.article'))->orderBy('id', 'DESC')->paginate();

        $articles = $articles->each(function ($item) {
            $item->setAppends(['image_url']);
        })->toArray();

        return $this->success(compact('articles'));
    }

}
