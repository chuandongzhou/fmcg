<footer class="panel-footer footer {{ $class or '' }}">
    <div class="container  text-muted">
        <div class="row ">
            <div class="col-xs-6 pd-left-clear">
                <ul class="list-inline">
                    <li><a href="{{ url('about') }}" class="icon about">关于我们</a></li>
                    <li>
                        <div class="contact-panel">
                            <a href="javascript:;" class="icon contact-information">联系方式</a>
                        </div>
                        <div class="contact-content content hidden">
                            <div>{{ cons('system.company_tel') . '&nbsp;&nbsp;&nbsp;&nbsp;' . cons('system.company_mobile') }}</div>
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
                                </div>
                            </form>
                        </div>
                    </li>
                    <li>
                        <div id="qr-content-panel">
                            <a href="javascript:;" class="app-down icon">APP下载</a>
                        </div>
                        <div class="content hidden">
                            <div class="qr-panel">
                                <div class="dbd item">
                                    <div class="qr-code dbd-qr-code"></div>
                                    <div class="text text-center">订百达</div>
                                </div>
                                <div class="driver-helper item">
                                    <div class="qr-code helper"></div>
                                    <div class="text text-center">司机助手</div>
                                </div>
                                <div class="driver-helper item">
                                    <div class="qr-code field"></div>
                                    <div class="text text-center">外勤</div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col-xs-6">
                <div>
                    Copyright
                    &copy; {!! cons('system.company_name') . '&nbsp;&nbsp;&nbsp;&nbsp;' . cons('system.company_record') !!} </div>
            </div>
        </div>
    </div>
</footer>