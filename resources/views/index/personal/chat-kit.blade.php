@extends('master')

@section('body')

    <div class="message-container" id="J_demo"></div>
    @stop

@section('js-lib')
@parent
<!--[if lt IE 9]>
    <script src="https://g.alicdn.com/aliww/ww/json/json.js" charset="utf-8"></script>
    <![endif]-->
    <!-- 自动适配移动端与pc端 -->
    <script src="https://g.alicdn.com/aliww/??h5.imsdk/2.1.0/scripts/yw/wsdk.js,h5.openim.kit/0.3.7/scripts/kit.js"
            charset="utf-8"></script>
@stop


@section('js')
    @parent
    <script type="text/javascript">
        var d = document.getElementById('J_demo');
        d.parentNode.removeChild(d);
        window.onload = function () {
            WKIT.init({
                container: '{{ $fullScreen }}' ? null : document.getElementById('J_demo'),
                uid: '{{ $thisShopId }}',
                appkey: '{{ $chatConf['key'] }}',
                credential: '{{ $chatConf['pwd'] }}',
                touid: '{{ $remoteUid }}',
                theme: 'red',
                title: '{{ $shop->name }}',
                toAvatar: '{{ $shop->logo_url }}',
                //autoMsg: '',
                autoMsgType: 1,
                pluginUrl: '{{ url('personal/chat/detail?id=' . $remoteUid) }}',
                onLoginSuccess: function () {
                    var sdk = WKIT.Conn.sdk, Event = sdk.Event;
                    Event.on('CHAT.MSG_RECEIVED', function (data) {
                        setReadState(sdk, data.data.touid.substring(8));
                    });
                }
            });
            //设置消息已读
            var setReadState = function (sdk, touid) {
                sdk.Chat.setReadState({
                    touid: touid,
                    timestamp: Math.floor((new Date()) / 1000),
                    success: function (data) {
                        console.log('设置已读成功', data);
                    },
                    error: function (error) {
                        console.log('设置已读失败', error);
                    }
                });
            };
        }
    </script>
@stop