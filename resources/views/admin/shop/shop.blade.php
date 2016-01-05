@extends('admin.master')
@include('includes.cropper')
@include('includes.loadMapJs')
@include('includes.address')
@section('subtitle' , '用户管理')

@section('right-container')
    <div class="col-sm-12 personal-center">
        <form class="form-horizontal ajax-form" method="put"
              action="{{ url('admin/shop/'.$shop->id) }}" data-help-class="col-sm-push-2 col-sm-10"
              autocomplete="off">
            @include('includes.shop')
        </form>
    </div>
@stop

