@section('js-lib')
    @parent
    <!--[if lt IE 9]>
<script src="https://g.alicdn.com/aliww/ww/json/json.js" charset="utf-8"></script>
<![endif]-->
<script src="https://g.alicdn.com/aliww/??h5.imsdk/2.1.0/scripts/yw/wsdk.js,h5.openim.kit/0.3.7/scripts/kit.js?pc=1"
        charset="utf-8"></script>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var userListPanel = $('.user-list-panel ul'), totalMsg = $('.total-message-count'), chatListPanel = $('.page-quick-sidebar-chat-users');
            //获取未读消息数
            var getUnreadMsgCount = function (sdk) {
                sdk.Base.getUnreadMsgCount({
                    count: 10,
                    success: function (data) {
                        var list = data.data, totalCount = 0;
                        list.forEach(function (item) {
                            if (item.contact.substring(0, 8) === 'chntribe') {

                            } else {
                                totalCount += item.msgCount;
                                var touid = item.contact.substring(8);
                                userListPanel.find('.user-msg[data-touid="' + touid + '"]').find('.badge').removeClass('hide').html(item.msgCount);
                                if (item.msgCount > 0) {
                                    chatListPanel.find('.user-msg[data-touid="' + touid + '"]').find('.badge').removeClass('hide').html(item.msgCount);
                                }

                            }
                        });
                        if (totalCount > 0) {
                            totalMsg.removeClass('hide').html(totalCount);
                        }

                    },
                    error: function (error) {
                        console.log('获取未读消息的条数失败', error);
                    }
                });

            };
            //获取最近联系人
            var allRecentContact = function (sdk, Event) {
                sdk.Base.getRecentContact({
                    count: 30,
                    success: function (data) {
                        data = data.data;
                        var list = data.cnts, firstId = getToUid(list[0].uid) || 0, userList = [];
                        list.forEach(function (item) {
                            var lastMsg = '';
                            if (item.type == 2) {
                                lastMsg = '[语音]';
                            } else if (item.type == 1) {
                                lastMsg = '[图片]';
                            }
                            else {
                                lastMsg = item.msg && item.msg[0][1];
                            }
                            userList[item.uid.substring(8)] = [getLocalTime(item.time), lastMsg];
                        });

                        if (firstId) {
                            setUserList(userList, sdk);
                            //  setReadState(sdk, firstId);
                            Event.on('CHAT.MSG_RECEIVED', function (data) {
                                addMessageNum(sdk, getToUid(data.data.touid));
                            });
                            sdk.Base.startListenAllMsg();
                        }
                    },
                    error: function (error) {
                        console.log('获取最近联系人及最后一条消息内容失败', error);
                    }
                });
            }
            //聊天界面联系人点击事件,聊天记录加载
            userListPanel.on('click', '.user-msg', function () {
                var obj = $(this), touid = obj.data('touid').toString();
                var sdk = WKIT.Conn.sdk;
                obj.prop('disabled', true).css('background-color', '#646464').siblings().prop('disabled', false).css('background-color', '#555555');
                WKIT.switchTouid({
                    touid: touid,
                    toAvatar: obj.find('img').attr('src')
                });
                setReadState(sdk, touid);
            });

            //获取所有历史聊天用户html
            var setUserList = function (userList, sdk) {
                var keys = Object.keys(userList), firstId = keys[0], firstLogoUrl = '', userHtml = '', usersHtml = '';

                $.get(site.api('shop/get-shops-by-ids'), {data: keys}, function (shops) {
                    var shops = shops.shops;
                    for (var i in shops) {
                        @if(!empty(request()->input('uid')) && request()->is('personal/chat'))
                            var uid = parseInt({!!  request()->input('uid') !!});
                            if(uid == i){
                                firstLogoUrl = shops[i].logo_url;
                                firstId = {!!  request()->input('uid') !!}+'';
                            }
                         @else
                            if (!firstLogoUrl) {
                                firstLogoUrl = shops[i].logo_url;
                            }
                         @endif

                        userHtml += '<li class="user-msg" data-touid="' + i + '">' +
                                '   <img class="avatar" src=" ' + shops[i].logo_url + ' "> ' +
                                '   <div class="user-item"> ' +
                                '       <span class="user-name">' + shops[i].name + '</span> ' +
//                                '       <span class="pull-right last-msg-time prompt">' + userList[i][0] + '</span> ' +
                                '   </div> ' +
                                '   <div class="user-item"> ' +
                                 '       <span class=" last-msg-time prompt">' + userList[i][0] + '</span> ' +
                                '       <span class="pull-right badge hide">0</span> ' +
                                '   </div> ' +
                                '</li>';
                        usersHtml += '<a href="/personal/chat?uid=' + i + '">' +
                                '<ul class="media-list list-items user-msg" data-touid="' + i + '">' +
                                ' <li class="media">' +
                                '<div class="media-status">' +
                                ' <span class="badge badge-success pull-right badge hide">0</span>' +
                                '</div>' +
                                ' <img class="media-object" src="' + shops[i].logo_url + '" alt="...">' +
                                '<div class="media-body">' +
                                '<h4 class="media-heading">' + shops[i].name + '</h4>' +
                                '<div class="media-heading-sub">' + userList[i][1] + '</div>' +
                                '<div class="media-heading-small">' + userList[i][0] + '</div>' +
                                '</div>' +
                                ' </li>' +
                                ' </ul>' +
                                '</a>';
                    }
                    userListPanel.append(userHtml);
                    chatListPanel.append(usersHtml);
                    getUnreadMsgCount(sdk);
                    WKIT.switchTouid({
                        touid: firstId,
                        toAvatar: firstLogoUrl
                    });
                    @if(request()->is('personal/chat') && !empty(request()->input('uid')) )
                        var id = parseInt({!!  request()->input('uid') !!});
                        userListPanel.find('.user-msg[data-touid="' + id + '"]').prop('disabled', true).css('background-color', '#646464');
                        setReadState(sdk, id + "");
                     @elseif(request()->is('personal/chat'))
                             userListPanel.find('.user-msg[data-touid="' + firstId + '"]').prop('disabled', true).css('background-color', '#646464');
                        setReadState(sdk, firstId);
                    @endif

                }, 'json')

            };
            //设置消息已读
            var setReadState = function (sdk, touid) {
                sdk.Chat.setReadState({
                    touid: touid,
                    timestamp: Math.floor((new Date()) / 1000),
                    success: function (data) {
                        var readNum = userListPanel.find('.user-msg[data-touid="' + touid + '"]').find('.badge').addClass('hide').html();
                        userListPanel.find('.user-msg[data-touid="' + touid + '"]').find('.badge').addClass('hide').html(0);
                        chatListPanel.find('.user-msg[data-touid="' + touid + '"]').find('.badge').addClass('hide').html(0);
                        if ((totalMsg.html() - readNum) > 0) {
                            totalMsg.html(totalMsg.html() - readNum);
                        } else {
                            totalMsg.addClass('hide').html(0);
                        }

                    },
                    error: function (error) {
                        console.log('设置已读失败', error);
                    }
                });
            };
            //消息条数加1
            var addMessageNum = function (sdk, touid) {
                var userMsg = userListPanel.find('.user-msg[data-touid="' + touid + '"]'), badgeItem = userMsg.find('.badge'), unreadMsg = chatListPanel.find('.user-msg[data-touid="' + touid + '"]').find('.badge');

                if (!userMsg.prop('disabled')) {
                    badgeItem.removeClass('hide').html(parseInt(badgeItem.html()) + 1);
                    unreadMsg.removeClass('hide').html(parseInt(unreadMsg.html()) + 1);
                    @if(request()->is('personal/chat'))
                        totalMsg.removeClass('hide').html(parseInt(totalMsg.html()) + 1);
                    @endif
                } else {
                    setReadState(sdk, touid);
                }

            };
            // 获取时间
            var getLocalTime = function (nS) {
                var now = new Date(nS * 1000);
                var year = now.getFullYear();
                var month = now.getMonth() + 1;
                var date = now.getDate();

                return year + "-" + month + "-" + date;
            };
            //聊天对方id处理
            var getToUid = function (toUid) {
                return toUid.substring(8);
            };
            @if(request()->is('personal/chat'))
                WKIT.init({
                container: document.getElementById('msgWrap'),
                width: 700,
                height: 500,
                uid: '{{ $chatConf['shop_id'] }}',
                appkey: '{{ $chatConf['key'] }}',
                credential: '{{ $chatConf['pwd'] }}',
                touid: '0',
                theme: 'green',
                pluginUrl: '',
                onLoginSuccess: function () {
                    var sdk = WKIT.Conn.sdk, Event = sdk.Event;
                    allRecentContact(sdk, Event);
                },
                onAudioReceived: function (content) {
                    // content为图片的地址
                    // 返回一个要显示语音的html字符串
                    // 点击播放等操作请自己实现
                    return '<span class="glyphicon glyphicon-volume-up"></span> 暂不支持播放，请下载最新版app';
                }


            });
            @else

            if (!SITE.USER.id) {
                return false;
            }
            if(typeof WSDK == 'undefined'){
                return ;
            }
            var sdk = new WSDK(), Event = sdk.Event;
                sdk.Base.login({
                uid: '{{ $chatConf['shop_id'] }}',
                appkey: '{{ $chatConf['key'] }}',
                credential: '{{ $chatConf['pwd'] }}',
                timeout: 5000,
                success: function (data) {
                    //获取联系人列表
                   @if(request()->is('personal/*', 'order*', 'my-goods*', 'business*', 'like*'))
                        allRecentContact(sdk, Event);
                   @endif
//                    getUnreadMsgCount(sdk, true);

                    Event.on('CHAT.MSG_RECEIVED', function (data) {
                        totalMsg.html(parseInt(totalMsg.html()) + 1);
                    });
                    sdk.Base.startListenAllMsg();
                },
                error: function (error) {
                    // {code: 1002, resultText: 'TIMEOUT'}
                    console.log('login fail', error);
                }
            });
            @endif

        });
    </script>
@stop
