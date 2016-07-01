@extends('index.menu-master')
@section('subtitle', '个人中心-消息管理')
@section('top-title', '消息列表->消息列表')
@section('right')
    <div class="row user-list-wrap">
        <div class="col-sm-3 user-list-panel padding-clear">
            <ul>

            </ul>
        </div>
        <div class="col-sm-9">
            <div id="msgWrap"></div>
        </div>
    </div>
    @parent
@stop


