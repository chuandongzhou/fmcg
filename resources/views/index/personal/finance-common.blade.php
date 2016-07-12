<div class="account-balance">
    <label>账户余额 :</label>
    <b class="balance red">¥{{ $balance }}</b>
</div>
<div class="protected-balance">
    <label>结算保护金额 :</label>
    <b class="balance red">¥{{ sprintf("%.2f", $protectedBalance) }}</b>
</div>
<div class="can-withdraw-balance">
    <label>可提现余额 :</label>
    <b class="balance red">¥{{ sprintf('%.2f' , $balance - $protectedBalance) }}</b>
    <a class="btn btn-primary" data-target="#withdraw" data-toggle="modal">提现</a>
</div>
<div class="font-size-10 red">(提现申请处理时间：每天10 : 00 、16 : 00)</div>
<div class="personal-center">
    <div class="switching">
        <a href="{{ url('personal/finance/balance') }}"
           class="btn {{ path_active('personal/finance/balance') }}">流水账</a>
        <a href="{{ url('personal/finance/withdraw') }}"
           class="btn {{ path_active('personal/finance/withdraw') }}">提现记录</a>
    </div>
</div>