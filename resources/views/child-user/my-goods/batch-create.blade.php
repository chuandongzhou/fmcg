@extends('child-user.manage-master')
@section('subtitle', '商品')

@include('includes.uploader')

@section('container')
    @include('includes.child-menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('my-goods') }}">商品管理</a> >
                    <span class="second-level"> 批量导入</span>
                </div>
            </div>
            @include('includes.goods-import', ['downloadUrl' => url('child-user/my-goods/download-template'), 'uploadUrl' =>url('api/v1/child-user/my-goods/import')])
        </div>
    </div>
@stop
