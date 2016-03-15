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
    <script src="https://g.alicdn.com/aliww/??h5.openim.sdk/0.1.4/scripts/wsdk.js,h5.openim.kit/0.0.5/scripts/kit.js"
            charset="utf-8"></script>


@stop


@section('js')
    <script>
        // 在url上加上fullscreen参数,即可变为全屏
//        if (result.fullscreen) {
//            var d = document.getElementById('J_demo');
//            d.parentNode.removeChild(d);
//        }
        var d = document.getElementById('J_demo');
        d.parentNode.removeChild(d);
        window.onload = function () {
            WKIT.init({
                container: '{{ $fullScreen }}' ? null : document.getElementById('J_demo'),
                uid: SITE.USER.id,
                appkey: '{{ $appKey }}',
                credential: '{{ $password }}',
                touid: '{{ $remoteUid }}',
                theme: 'red',
            title: '我是客服哟',
                logo: 'http://interface.im.taobao.com/mobileimweb/fileupload/downloadPriFile.do?type=1&fileId=876114ca44f4362f629f7d592014e057.jpg&suffix=jpg&width=1920&height=1200&wangxintype=1&client=ww',
                //autoMsg: '',
                autoMsgType: 1,
                pluginUrl: '{{ url('personal/message/goods-detail?id=34') }}'
            });
        }
    </script>
@stop