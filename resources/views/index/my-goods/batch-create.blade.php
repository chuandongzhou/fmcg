@extends('index.menu-master')
@section('subtitle', '商品')
@section('top-title')
    <a href="{{ url('my-goods') }}">商品管理</a> &rarr;
    批量导入
@stop
@include('includes.uploader')

@section('right')
    @include('includes.goods-import')
@stop
