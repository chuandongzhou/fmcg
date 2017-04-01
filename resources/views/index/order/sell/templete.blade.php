@extends('index.menu-master')
@section('subtitle' , '订单下载模版')
@include('includes.shipping-address-map')
@include('includes.templet-model')

@section('top-title')
    <a href="{{ url('order-sell') }}">订单管理</a> >
    <span class="second-level">订单打印模版</span>
@stop
@section('right')
    <div class="row print-templet margin-clear">
        <div class="col-sm-12 templet-title">
            <span class="prompt">已选订单打印默认模板:</span>
            <b class="print-default">{{ cons()->valueLang('order.templete', $defaultTempleteId) }}</b>
            <a href="javascript:" class="btn btn-blue-lighter" data-target="#templetModal" data-toggle="modal"
               data-src="{{ asset('images/order-templetes/templete_' . $defaultTempleteId . '.png') }}">预览
            </a>
            <a class="btn go-back" href="javascript:history.back()">返回</a>
            <span class="prompt prompt-last">您可以在下列中选择订单打印模板，点击模板图片可预览大图</span>
        </div>
        <div class="col-sm-12 templet-list">
            <div class="row">

                @foreach(cons()->valueLang('order.templete') as $templeteId => $templeteName)
                    <div class="col-sm-4 item">
                        <div class="mask-panel">
                            <img src="{{ asset('images/order-templetes/templete_' . $templeteId . '.png') }}">
                            <a href="javascript:" class="templet-modal" data-target="#templetModal" data-toggle="modal"
                               data-src="{{ asset('images/order-templetes/templete_' . $templeteId . '_s.png') }}">点击预览</a>
                        </div>
                        <div class="choice-item">
                            <label>
                                <input data-url="{{ url('api/v1/order/templete/' . $templeteId)  }}" data-method="post"
                                       class="select-templet ajax" type="radio"
                                       name="templet" {{ $templeteId == $defaultTempleteId ? 'checked' : '' }} />{{ $templeteName }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@stop