@extends('index.menu-master')
@include('includes.cropper')
@include('includes.address')
@section('subtitle', '个人中心-商家信息')
@section('top-title', '个人中心-店铺信息')
@section('right')

    <div class="row">
        <div class="col-sm-12 personal-center">
            <form class="form-horizontal ajax-form" method="put"
                  action="{{ url('api/v1/personal/shop/'.$shop->id) }}" data-help-class="col-sm-push-2 col-sm-10"
                  autocomplete="off">
                <div class="col-sm-12 user-show">
                    @include('includes.shop')
                    <div class="col-sm-12 text-center save">
                        <button class="btn btn-bg btn-primary" type="submit"><i class="fa fa-save"></i> 保存</button>
                        <button class="btn btn-bg btn-warning" type="button" onclick="javascript:history.go(-1)"><i
                                    class="fa fa-reply"></i> 取消
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

