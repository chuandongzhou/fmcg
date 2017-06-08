@extends('mobile.shop.master')

@section('subtitle', '店铺详情')

@section('body')
    @parent
    <div class="container-fluid  m185 p65">
        <div class="row shop-address-wrap">
            <div class="col-xs-12 ">
                <div class="row">
                    <div class="col-xs-8 th-title">配送地区</div>
                    <div class="col-xs-4 th-title amount">配送额</div>
                </div>
                <div class="row">
                    @foreach($area as $item)
                    <div class="col-xs-8 td">
                        {{ $item->address_name }}
                    </div>
                    <div class="col-xs-4 td amount"> {{ $item->extra_common_param or 0 }}</div>
                   @endforeach
                </div>

            </div>
        </div>
    </div>
@stop