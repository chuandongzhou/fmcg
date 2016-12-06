@extends('index.menu-master')
@section('subtitle', '商品')
@section('top-title')
    <a href="{{ url('my-goods') }}">商品管理</a> >
    <span class="second-level"> 批量导入</span>
@stop
@include('includes.uploader')

@section('right')
    @include('includes.goods-import')
@stop
