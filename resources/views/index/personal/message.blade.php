@extends('index.menu-master')
@section('subtitle', '个人中心-提现账号')

@section('right')
    <div class="row user-list-wrap">
        <div class="col-sm-3 user-list-panel padding-clear">
            <ul>
                <li class="user-msg"><img class="avatar" src="http://placehold.it/40"><span class="user-name">用户名</span><span class="pull-right badge">1</span></li>
                <li class="user-msg"><img class="avatar" src="http://placehold.it/40"><span class="user-name">用户名</span></li>
                <li class="user-msg"><img class="avatar" src="http://placehold.it/40"><span class="user-name">用户名</span></li>
                <li class="user-msg"><img class="avatar" src="http://placehold.it/40"><span class="user-name">用户名</span></li>
                <li class="user-msg"><img class="avatar" src="http://placehold.it/40"><span class="user-name">用户名</span></li>
                <li class="user-msg"><img class="avatar" src="http://placehold.it/40"><span class="user-name">用户名</span></li>
                <li class="user-msg"><img class="avatar" src="http://placehold.it/40"><span class="user-name">用户名</span></li>
            </ul>
        </div>
        <div class="col-sm-9">
            <div id="msgWrap"></div>
        </div>
    </div>
    @parent
@stop

@section('js-lib')
    @parent
    <!--[if lt IE 9]>
    <script src="https://g.alicdn.com/aliww/ww/json/json.js" charset="utf-8"></script>
    <![endif]-->
    <script src="https://g.alicdn.com/aliww/??h5.openim.sdk/1.0.6/scripts/wsdk.js,h5.openim.kit/0.3.3/scripts/kit.js?pc=1" charset="utf-8"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function(){
            $(".user-msg").click(function(){
//                window.open('kit.html?uid=test0&to=test1&appkey=23018936&pwd=123456&fullscreen',
//                        'webcall',
//                        'toolbar=no,title=no,status=no,scrollbars=0,resizable=0,menubar＝0,location=0,width=700,height=500');

            WKIT.init({
                container: document.getElementById('msgWrap'),
                width: 700,
                height: 500,
                uid: 'test5',
                appkey: 23018936,
                credential: '123456',
                touid: 'test6',
                theme: 'orange',
                pluginUrl: '',
            });
            })

        });
    </script>
@stop

