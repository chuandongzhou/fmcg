@section('js-lib')
    @parent
    <!--[if lt IE 9]>
<script src="https://g.alicdn.com/aliww/ww/json/json.js" charset="utf-8"></script>
<![endif]-->
<script src="https://g.alicdn.com/aliww/??h5.openim.sdk/1.0.6/scripts/wsdk.js,h5.openim.kit/0.3.3/scripts/kit.js?pc=1"
        charset="utf-8"></script>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var sdk = new WSDK(), userListPanel = $('.user-list-panel ul');

            // 登录
            sdk.Base.login({
                uid: SITE.USER.id.toString(),
                appkey: '{{ $appKey }}',
                credential: '{{ $password }}',
                timeout: 5000,
                success: function (data) {
                    // {code: 1000, resultText: 'SUCCESS'}
                    console.log('login success', data);
                    getRecentContact();
                },
                error: function (error) {
                    // {code: 1002, resultText: 'TIMEOUT'}
                    console.log('login fail', error);
                }
            });

            var getRecentContact = function () {
                sdk.Base.getRecentContact({
                    count: 10,
                    success: function (data) {
                        data = data.data;
                        var list = data.cnts, firstId = list[0].uid.substring(8) || 0;
                        list.forEach(function (item) {
                            var userList = ' <li class="user-msg" data-touid="' +  item.uid.substring(8) + '">' +
                                    '<img class="avatar" src="http://placehold.it/40">' +
                                    '<span class="user-name">' + item.uid.substring(8) +  '</span>' +
                                    '</li>';
                            userListPanel.append(userList);

//                            console.log(item.uid.substring(8) + '在' + new Date(parseInt(item.time)*1000) + '联系了你');
//                            console.log('他说：' + item.type == 2 ? '[语音]' : item.type == 1 ? '[图片]' : (item.msg && item.msg[1]));
                        });
                        firstId && inits(firstId);
                    },
                    error: function (error) {
                        console.log('获取最近联系人及最后一条消息内容失败', error);
                    }
                });
            };

            var inits = function (firstId) {
                sdk.Base.destroy();
                sdk = null;
                WKIT.init({
                    container: document.getElementById('msgWrap'),
                    width: 700,
                    height: 500,
                    uid: SITE.USER.id.toString(),
                    appkey: '{{ $appKey }}',
                    credential: '{{ $password }}',
                    touid: firstId,
                    theme: 'orange',
                    pluginUrl: ''
                });
                WKIT
            };
        });
    </script>
@stop
