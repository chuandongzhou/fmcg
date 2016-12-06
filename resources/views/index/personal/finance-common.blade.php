
<div class="table-responsive">
    <table class="table table-bordered table-center public-table">
        <thead>
        <tr>
            <td>账户余额</td>
            <td>结算保护金额
                <a class="iconfont icon-tixing pull-right" title=""
                   data-container="body" data-toggle="popover" data-placement="bottom"
                   data-content="（当天完成支付的订单金额，不可提现）">
                </a>
            </td>
            <td>可提现金额</td>
            <td>操作</td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><b class="red">¥{{ $balance }}</b></td>
            <td>¥{{ sprintf("%.2f", $protectedBalance) }}</td>
            <td><b class="red">¥{{ sprintf('%.2f' , $balance - $protectedBalance) }}</b></td>
            <td class="operating">
                <a data-target="#withdraw" data-toggle="modal">
                    <i class="iconfont icon-tixian"></i> 提现
                </a>
                <a class="iconfont icon-tixing" title=""
                   data-container="body" data-toggle="popover" data-placement="bottom"
                   data-content="（提现申请处理时间：每天10 : 00 、16 : 00）">
                </a>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div class="notice-bar clearfix padding-clear">
    <a href="{{ url('personal/finance/balance') }}"
       class="{{ path_active('personal/finance/balance') }}">流水账</a>
    <a href="{{ url('personal/finance/withdraw') }}"
       class="{{ path_active('personal/finance/withdraw') }}">提现记录</a>
</div>