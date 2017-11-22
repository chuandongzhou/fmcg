@extends('admin.master')
@include('includes.cropper')

@section('right-container')
    <form class="form-horizontal ajax-form" method="{{ $article->id ? 'put' : 'post' }}"
          action="{{ url('admin/weixin/' . $params['route'].'/' . $article->id) }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-then="referer" autocomplete="off">

        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">标题</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="title" name="title" placeholder="请输入标题"
                       value="{{ $article->title }}">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="username">图片</label>

            <div class="col-sm-10 col-md-6">
                <button class="btn btn-success btn-sm" data-height="{{ $params['size']['height'] }}" data-width="{{ $params['size']['width'] }}"
                        data-target="#cropperModal" data-toggle="modal" data-name="image"
                        type="button">
                    本地上传({{ $params['size']['width'] . 'x' . $params['size']['height'] }})
                </button>
                <div class="image-preview">
                    <img class="img-thumbnail" src="{{ $article->image_url }}">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="url" class="col-sm-2 control-label">链接地址</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="link_url" name="link_url" placeholder="请输入链接地址"
                       value="{{ $article->link_url or 'http://' }}">
            </div>
            <div class="col-sm-push-2 col-sm-10">
                <p class="help-block">必须以http://开头</p>
            </div>
            <input type="hidden" value="{{ cons('admin.weixin_article.type.' . $params['type']) }}" name="type">
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-bg btn-success">{{ $article->id ? '修改' : '添加' }}</button>
                {{--<a href="{{ url('admin/advert-' . $type) }}" class="btn btn-bg btn-primary">返回</a>--}}

            </div>
        </div>
    </form>
@stop