@section('body')
    @parent
    <div class="modal modal1 fade" id="expireModal" tabindex="-1" role="dialog" aria-labelledby="expireModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="width:800px;">
            <div class="modal-content" style="width:800px;height:200px;margin:auto">
                <div class="modal-header choice-header prop-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="myModalLabel">
                        <span class="expire">产品续费</span>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="signed-xuqi expire">
                        <form class="form-horizontal "
                              action="{{ url('personal/sign/expire-pay') }}" method="post">
                            {{ csrf_field() }}
                            <div class="item">
                                <label>购买期限 :</label>
                                <ul class="month">
                                    @foreach(($amount = cons()->valueLang('sign.worker_expire_amount')) as $cost=>$month)
                                        <li data-cost="{{ $cost }}" data-month="{{ $month }}"
                                            data-pieces="{{ strstr($month, '年') ? '' : '个月' }}">{{ $month }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="item">
                                <input type="hidden" name="month" value="">
                                <label class="text-right">费用 :</label><span class="signed-num xuqi-num">￥100</span>
                            </div>
                            <div class="item last">
                                <button class="signed-submit">购买</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        signManage();
    </script>
@stop
