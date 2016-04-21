@extends('admin.master')
@section('subtitle' , '批量导入商品')

@include('includes.uploader')
@section('right-container')
    @include('includes.goods-import')
@stop
