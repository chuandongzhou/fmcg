@extends('master')

@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
@stop
@section('body')
    <div class="guide-content">
        <div class="content-panel">
            <a class="tabs-item" href="{{ url('auth/login?type=supplier') }}">
                <span class="item-icon">
                    <i class="icon icon-car"></i>
                </span>
                <span class="item-name">供应商平台</span>
            </a>
            <a class="tabs-item" href="{{ url('auth/login?type=wholesaler') }}">
                <span class="item-icon">
                    <i class="icon icon-wholesalers"></i>
                </span>
                <span class="item-name">批发平台</span>
            </a>
            <a class="tabs-item" href="{{ url('auth/login?type=retailer') }}">
                <span class="item-icon">
                    <i class="icon icon-shopping-cart"></i>
                </span>
                <span class="item-name ">超市平台</span>
            </a>
            <a class="tabs-item" href="{{ url('auth/login?type=retailer') }}">
                <span class="item-icon">
                    <i class="icon icon-books"></i>
                </span>
                <span class="item-name">零售商城</span>
            </a>
        </div>
    </div>
@stop
@section('footer')
    <footer class="panel-footer login-footer guide-footer">
        <div class="container text-center content">
            <p>Copyright2015成都订百达科技有限公司</p>
        </div>
    </footer>
    @parent
@stop