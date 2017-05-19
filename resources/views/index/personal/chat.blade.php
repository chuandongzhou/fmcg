@extends('index.manage-master')
@section('subtitle', '个人中心-消息管理')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('personal/chat') }}">消息列表</a> >
                    <span class="second-level"> 消息列表</span>
                </div>
            </div>
            <div class="row user-list-wrap">
                <div class="col-sm-3 user-list-panel padding-clear">
                    <ul>

                    </ul>
                </div>
                <div class="col-sm-9 padding-clear">
                    <div id="msgWrap"></div>
                </div>
            </div>
        </div>
    </div>
    @parent
@stop


