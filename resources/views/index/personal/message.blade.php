@extends('index.menu-master')
@include('includes.message' ,[
      'password' => $password,
      'appKey' => $appKey
])
@section('subtitle', '个人中心-消息管理')

@section('right')
    <div class="row user-list-wrap">
        <div class="col-sm-3 user-list-panel padding-clear">
            <ul>
                {{--<li class="user-msg" data-touid="1"><img class="avatar" src="http://placehold.it/40"><span--}}
                            {{--class="user-name">我是第一个</span><span--}}
                            {{--class="pull-right badge">1</span></li>--}}
                {{--<li class="user-msg" data-touid="2"><img class="avatar" src="http://placehold.it/40"><span--}}
                            {{--class="user-name">我是第二个</span>--}}
                {{--</li>--}}
                {{--<li class="user-msg"><img class="avatar" src="http://placehold.it/40"><span class="user-name">用户名</span>--}}
                {{--</li>--}}
                {{--<li class="user-msg"><img class="avatar" src="http://placehold.it/40"><span class="user-name">用户名</span>--}}
                {{--</li>--}}
                {{--<li class="user-msg"><img class="avatar" src="http://placehold.it/40"><span class="user-name">用户名</span>--}}
                {{--</li>--}}
                {{--<li class="user-msg"><img class="avatar" src="http://placehold.it/40"><span class="user-name">用户名</span>--}}
                {{--</li>--}}
                {{--<li class="user-msg"><img class="avatar" src="http://placehold.it/40"><span class="user-name">用户名</span>--}}
                {{--</li>--}}
            </ul>
        </div>
        <div class="col-sm-9">
            <div id="msgWrap"></div>
        </div>
    </div>
    @parent
@stop


