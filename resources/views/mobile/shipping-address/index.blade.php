@extends('mobile.master')

@section('subtitle', '收货地址')

@section('header')
    <div class="fixed-header fixed-item white-bg orders-details-header">
        <div class="row nav-top">
            <div class="col-xs-12 color-black">收货地址</div>
        </div>
    </div>
@stop

@section('body')
    @parent
    <div class="container-fluid m60 p65">
        <div class="row">
            @foreach($shippingAddress as $item)
                <div class="col-xs-12 white-bg address-list-item">
                    <div class="contact-person">{{ $item->consigner }} - {{ $item->phone }}</div>
                    <div class="contact-address">{{ $item->address_name }}</div>
                    <div class="clearfix opera-wrap">
                        <div class="pull-left">
                            <label>
                                <input class="mobile-ajax set-default"
                                       data-url="{{ url('api/v1/personal/shipping-address/default/' . $item->id) }}"
                                       data-method="put"
                                       data-done-then="none"
                                       type="radio" name="default"
                                        {{ $item->is_default ? 'checked' : '' }} />
                                设为默认地址
                            </label>
                        </div>
                        <div class="pull-right">
                            <a class="edit" href="{{ url('shipping-address/' . $item->id . '/edit') }}"><i
                                        class="iconfont icon-xiugai"></i>修改</a>
                            <a class="mobile-ajax remove"
                               data-url="{{ url('api/v1/personal/shipping-address/'.$item->id) }}"
                               data-method="delete"><i class="iconfont icon-shanchu"></i>删除</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@stop
@section('footer')
    <div class="fixed-footer fixed-item white-bg address-footer">
        <a href="{{ url('shipping-address/create') }}" class="btn btn-primary">新增收货地址</a>
    </div>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $('.set-default').on('done.hct.ajax', function () {
            $(this).prop('checked', true);
        })
    </script>
@stop