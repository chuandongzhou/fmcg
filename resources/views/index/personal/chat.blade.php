@extends('index.menu-master')
@section('subtitle', '个人中心-消息管理')
@section('top-title')
    <a href="{{ url('personal/chat') }}">消息列表</a> &rarr;
    消息列表
@stop
@section('right')
    <div class="row user-list-wrap">
        <div class="col-sm-3 user-list-panel padding-clear">
            <ul>

            </ul>
        </div>
        <div class="col-sm-9 padding-clear">
            <div id="msgWrap"></div>
        </div>
    </div>
    @parent
@stop


