@extends('index.menu-master')
@include('includes.templet-model')
@section('subtitle', '模版管理- 模板选择')
@section('top-title')
    <a href="{{ url('personal/model/model-edit') }}">模版管理</a> >
    <span class="second-level"> 模板选择</span>
@stop
@section('right')
    <div class="row setup-template">
        <div class="col-sm-3 item text-center">
            <div>
                <img src="http://placehold.it/200">
                <a href="javascript:;" class="templet-modal" data-target="#templetModal" data-toggle="modal">点击预览</a>
            </div>
            <label><input type="radio" name="template" checked> 默认模板</label>
        </div>
        <div class="col-sm-3 item text-center">
            <div>
                <img src="http://placehold.it/200">
                <a href="javascript" clas="templet-modal" data-target="#templetModal" data-toggle="modal">点击预览</a>
            </div>
            <label><input type="radio" name="template" checked> 模板01</label>
        </div>

        <div class="col-sm-12 btns">
            <button class="btn btn-submit">提交</button>
            <a class="btn check-index" href="{{ url('/') }}">查看店铺首页</a>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function(){
            $('.templet-modal').click(function () {
                $('.templet-dialog').css('width','200px');
                $('.templet-content').css({'width':'200px','height':'200px'});
                $('.templet-img').attr('src',$(this).siblings("img").attr('src'));
            });
        });
    </script>
@stop