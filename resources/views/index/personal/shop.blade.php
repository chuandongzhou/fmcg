@extends('index.menu-master')
@include('includes.cropper')
@include('includes.address')
@section('subtitle', '个人中心-商家信息')

@section('right')
    <div class="col-sm-12 personal-center">
        <form class="form-horizontal ajax-form" method="put"
              action="{{ url('api/v1/personal/shop/'.$shop->id) }}" data-help-class="col-sm-push-2 col-sm-10"
              autocomplete="off">
            @include('includes.shop')
        </form>
    </div>
@stop

