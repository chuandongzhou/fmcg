@extends('index.manage-master')

@section('subtitle', '财务管理-保证金缴纳')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('personal/finance/balance') }}">财务管理</a> >
                    <span class="second-level"> 保证金缴纳</span>
                </div>
            </div>
            <div class="row delivery">
                <div class="col-sm-12 caption">
                    <h3>申请开店说明</h3>

                    <div>1. 正常买家用户可免费使用订百达平台。</div>
                    <div>2. 申请开店用户将缴费一定服务费用，成功开店后可获得订百达外勤以、司机等其他服务使用权限:</div>
                    <p>1)需缴纳 <span class="red">￥1,000 </span>保证金，该保证金可在申请关店通过后退还到申请资料中银行账号中。</p>

                    <p>2)需支付平台使用费用 <span class="red">￥100/月</span> ，根据现优惠政策成功缴纳保证金后将赠送 <span class="red">3个月</span>免费使用期限
                        ，同时一次性购买<span class="red">10个月</span>使用期限将自动赠送<span class="red">2个月使用期限</span>。</p>

                    <p>3)成功开店后可获得订百达平台提供其他服务使用权限，可开通业务员、司机账号共计<span class="red"> 10</span> 个。如需更多账号个数，超出部分账号个数需支付额外服务费用，<span
                                class="red">每个账号每月将收取 ￥10 </span>服务费用。
                    </p>

                    <p>4)保证金后到款1个工作日之内开通，到时会短信通知到账号所绑定密保手机，请保证账号所绑定密保手机准确无误。</p>

                    <p>5)在使用期限到期之前 10天 和5天，会以短信通知到账号所绑定密保手机，请保证账号所绑定密保手机准确无误。</p>

                    <div>3. 保证金缴纳请往以下银行账号转账，:</div>
                    <p class="red">注意:</p>

                    <p class="red">必须在备注(附言)中注明 缴纳保证金-平台账号-账号身份(批发商/供应商)-店铺名称,例如: 缴纳保证金-test01-批发商-百货批发部。</p>

                    <p>收款银行: xx银行</p>

                    <p>开户行: xx省xx市xxx支行</p>

                    <p>银行账号: xxxxxxxxxxxxxxxxxxx</p>

                    <p>收款姓名: xxxx</p>

                    <div>4. 购买使用期限请往以下银行账号转账:</div>

                    <p class="red">注意:</p>

                    <p class="red">必须在备注(附言)中注明 购买使用期限-平台账号-账号身份(批发商/供应商)-店铺名称,例如: 购买10个月使用期限-test01-批发商-百货批发部。</p>

                    <p>收款银行: xx银行</p>

                    <p>开户行: xx省xx市xxx支行</p>

                    <p>银行账号: xxxxxxxxxxxxxxxxxxx</p>

                    <p>收款姓名: xxxx</p>

                    <div>5.申请过程中有任何问题可联系当地业务人员或致电 028 - 8323 3316。</div>
                </div>
            </div>
        </div>
    </div>
@stop
