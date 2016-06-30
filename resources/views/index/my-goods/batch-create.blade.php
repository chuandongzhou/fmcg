@extends('index.menu-master')
@section('subtitle', '商品')
@section('top-title', '商品管理-批量导入')
@include('includes.uploader')

@section('right')
    @include('includes.goods-import')
@stop
