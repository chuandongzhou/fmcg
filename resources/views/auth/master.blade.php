@extends('master')

@section('js-lib')
    <script src="{{ asset('js/index.js') }}"></script>
@stop

@section('css')
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
@stop


@section('footer')
    <footer class="panel-footer footer login-footer">
        <div class="container text-center text-muted">
            <div class="row text-center">
                <div class="col-sm-6">
                    <ul class="list-inline">
                        <li><a href="{{ url('about') }}" class="icon about">关于我们</a></li>
                        <li>
                            <div class="contact-panel">
                                <a href="javascript:;" class="icon contact-information">联系方式</a>
                            </div>
                            <div class="contact-content content hidden">
                                <div>{{ cons('system.company_tel') . ' ' . cons('system.company_mobile') }}</div>
                                <div>{{ cons('system.company_addr') }}</div>
                            </div>
                        </li>
                        <li>
                            <div class="feedback-panel">
                                <a class="feedback icon" href="javascript:;">意见反馈</a>
                            </div>
                            <div class="content hidden">
                                <form class="ajax-form" method="post" action="{{ url('api/v1/feedback') }}"
                                      accept-charset="UTF-8" data-help-class="error-msg text-center"
                                >
                                    <div>
                                        <textarea placeholder="请填写您的反馈意见" name="content"></textarea>
                                    </div>
                                    <div>
                                        <div class="input-group">
                                            <span class="input-group-addon" id="feedback-contact"><i
                                                        class="fa fa-envelope-o"></i></span>
                                            <input type="text" class="form-control" placeholder="留个邮箱或者别的联系方式呗"
                                                   aria-describedby="feedback-contact" name="contact">
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary btn-submit" type="submit"
                                                        data-done-then="none" data-done-text="反馈提交成功">提交
                                                </button>
                                            </span>
                                        </div>
                                        <!-- /input-group -->
                                    </div>
                                </form>
                            </div>
                        </li>
                        <li>
                            <div id="qr-content-panel">
                                <a href="javascript:;" class="app-down icon">APP下载</a>
                            </div>
                            <div class="content hidden">
                                <div class="qr-code"></div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-6">
                    <p>Copyright {!! cons('system.company_name') !!}</p>
                </div>
            </div>
        </div>
    </footer>
@stop

@section('js')
    <script type="text/javascript">
        $(".qr-code-panel").hover(function () {
            $(".qr-code-img").css("display", "inline-block");
        }, function () {
            $(".qr-code-img").css("display", "none");
        });
        $(function () {
            //意见反馈
            $('.feedback-panel > a').popover({
                container: '.feedback-panel',
                placement: 'top',
                html: true,
                content: function () {
                    return $(this).parent().siblings('.content').html();
                }
            })

            //扫二维码下载app
            tooltipFunc('#qr-content-panel > a', '#qr-content-panel');
            //联系方式
            tooltipFunc('.contact-panel > a', '.contact-panel');


            //调用tooltip插件
            function tooltipFunc(item, container) {
                $(item).tooltip({
                    container: container,
                    placement: 'top',
                    html: true,
                    title: function () {
                        return $(this).parent().siblings('.content').html();
                    }
                })
            }

        });
    </script>
@stop