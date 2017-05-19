@extends('master')
@section('css')
    <style type="text/css">
        .store .address-panel {
            border-top: 1px dashed #b4b4b4;
            margin-top: -1px;
        }
        .store .address-panel ul {
            padding: 5px 0;
            clear: both;
        }
    </style>
@stop
@section('body')
    <div class="store">
        <div class="address-panel">
            <ul>
                <li>
                    <div class="panel-name">联系方式：</div>
                    <p>{{ $shop->contact_info }}</p>
                </li>
            </ul>
            <ul>
                <li>
                    <div class="panel-name">店家地址：</div>
                    <p>{{ $shop->address }}</p>
                </li>
            </ul>
            <ul>
                <li>
                    <div class="panel-name">店家介绍：</div>
                    <p>{{ $shop->introduction }}</p>
                </li>
            </ul>
        </div>
    </div>
@stop