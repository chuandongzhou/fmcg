@extends('index.menu-master')
@include('includes.uploader')
@include('includes.timepicker')
@section('subtitle', '个人中心-首页广告')
@section('top-title')
    <a href="{{ url('personal/model/advert') }}">模版管理</a> &rarr;
    <a href="{{ url('personal/model/advert') }}">首页广告</a> &rarr;
    首页广告{{ $advert->id ? '编辑' : '添加' }}
@stop
@include('includes.tinymce',['full' => true])
@include('includes.cropper')

@section('right')
    <form class="form-horizontal ajax-form" method="{{ $advert->id ? 'put' : 'post' }}"
          action="{{ url('api/v1/personal/model/advert/' . $advert->id) }}" data-help-class="col-sm-push-2 col-sm-10"
          data-done-url="{{ url('personal/model/advert') }}" autocomplete="off">
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">广告名称</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="name" name="name" placeholder="请输入广告名"
                       value="{{ $advert->name }}">
            </div>
        </div>
        <div class="form-group">
            <label for="upload-file" class="col-sm-2 control-label">广告图片</label>

            <div class="col-sm-4">
                <button data-height="200" data-width="800" data-target="#cropperModal" data-toggle="modal"
                        data-loading-text="图片已达到最大数量" class="btn btn-primary btn-sm" type="button"
                        id="pic-upload">
                    请选择图片文件(裁剪)
                </button>

                <div class="progress collapse">
                    <div class="progress-bar progress-bar-striped active"></div>
                </div>

                <div class="row pictures">

                </div>

                <div class="image-preview w160">
                    <img src="{{ $advert->image_url }}" class="img-thumbnail">
                </div>

                {{--<div class="image-preview w160">--}}
                {{--<img src="{{ $advert->image_url }}" class="img-thumbnail">--}}
                {{--</div>--}}
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-2 col-xs-offset-2">
                @if($advert->type==6)
                    <input class="goodsIdRadio" type="radio" name="identity" value="shop" />商品id

                @else
                    <input class="goodsIdRadio" type="radio" name="identity"  checked="checked" value="shop" />商品id
                @endif
            </div>
            <div>
                @if($advert->type==6)
                    <input class="promoteRadio" type="radio" name="identity" value="promote" checked="checked" >促销信息

                @else
                    <input class="promoteRadio" type="radio" name="identity" value="promote" >促销信息
                @endif
            </div>
        </div>
        <div class="form-group">
            @if($advert->type==6)
                <label for="url" class="col-sm-2 control-label goodsId">促销信息</label>
                <div class="col-sm-8 promoteDiv">
                    <textarea class="introduce tinymce-editor form-control promotInfo" name="promoteinfo">{{ $advert->url }}</textarea>
                </div>
                <div class="col-xs-4 goodsidDiv" style="display:none">
                    <input type="text" class="form-control" id="goods_id" name="goods_id" placeholder="请输入商品id" />
                </div>
            @else
                <label for="url" class="col-sm-2 control-label goodsId">商品id</label>
                <div class="col-sm-4 goodsidDiv">
                    <input type="text" class="form-control" id="goods_id" name="goods_id" placeholder="请输入商品id"
                           value="{{ $advert->goods_id }}">
                </div>
                <div class="col-xs-8 promoteDiv" style="display:none">
                    <textarea name="promoteinfo"  class="introduce tinymce-editor form-control promotInfo"></textarea>
                </div>
            @endif

        </div>

        <div class="form-group" id="date-time">
            <label class="col-sm-2 control-label">起止时间</label>

            <div class="col-sm-2 time-limit">
                <input type="text" class="form-control datetimepicker" name="start_at" placeholder="起始时间"
                       value="{{ $advert->start_at }}"/>
            </div>

            <div class="col-sm-2 time-limit">
                <input type="text" class="form-control datetimepicker" name="end_at" placeholder="结束时间"
                       value="{{ $advert->end_at }}"/>
            </div>
            <div class="col-sm-push-2 col-sm-10">
                <p class="help-block">结束时间为空时，表示广告永久有效。</p>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-bg btn-success">{{ $advert->id ? '修改' : '添加' }}</button>
                <a href="javascript:history.go(-1)" class="btn btn-cancel">返回</a>
            </div>
        </div>
    </form>
    @parent
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {
            radioCheck();
        })
    </script>
@stop
