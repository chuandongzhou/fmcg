@extends('auth.master')

@section('title' , '订百达 - 订货首选')

@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
    <style>body{margin-bottom:100px}</style>
@stop
@section('body')
        <div class="container-fluid guide-container padding-clear">
            <div class="banner"><img src="{{ asset('images/banner.jpg') }}"></div>
            <div class="row content-panel">
                <div class="col-sm-8 col-sm-offset-2 col-lg-8 col-lg-offset-2">
                    <a class="tabs-item" href="{{ url('auth/login?type=supplier') }}">
                        <div class="item-icon">
                            <img src="{{ asset('images/guide-icons-1.png') }}">
                        </div>
                        <span class="item-name">供应商平台</span>
                    </a>
                    <a class="tabs-item" href="{{ url('auth/login?type=wholesaler') }}">
                        <div class="item-icon">
                            <img src="{{ asset('images/guide-icons-2.png') }}">
                        </div>
                        <span class="item-name">批发平台</span>
                    </a>
                    <a class="tabs-item" href="{{ url('auth/login?type=retailer') }}">
                        <div class="item-icon">
                            <img src="{{ asset('images/guide-icons-3.png') }}">
                        </div>
                        <span class="item-name ">终端平台</span>
                    </a>
                    <a class="tabs-item" href="{{ url('auth/login?type=retailer') }}">
                        <div class="item-icon">
                            <img src="{{ asset('images/guide-icons-4.png') }}">
                        </div>
                        <span class="item-name">零售商城</span>
                    </a>
                </div>
            </div>
        </div>
@stop
