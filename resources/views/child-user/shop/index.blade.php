@extends('child-user.manage-master')
@include('includes.cropper')
@include('includes.templet-model')
@section('subtitle', '个人中心-商家信息')
@section('container')
    @include('includes.child-menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('child-user/info') }}">个人中心</a> >
                    <span class="second-level"> 店铺信息</span>
                </div>
                <div class="row">
                    <div class="col-sm-12 personal-center">
                        <form class="form-horizontal ajax-form" method="put"
                              action="{{ url('api/v1/child-user/shop/'.$shop->id) }}"
                              data-help-class="col-sm-push-2 col-sm-10"
                              autocomplete="off">
                            <div class="col-sm-12 user-show">
                                @include('includes.shop')
                                <div class="col-sm-12 save">
                                    <button class="btn btn-success" type="submit">提交</button>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
@stop

