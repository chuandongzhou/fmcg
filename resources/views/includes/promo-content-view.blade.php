<div class="panel-container {{--table-responsive--}} {{--promotion-msg-wrap--}}">
    @if($promo->type == cons('promo.type.custom'))
        <div class="row custom">
            <div class="col-sm-12 item-text other">
                <span>{{$promo->condition[0]->custom ?? ''}} &nbsp;&nbsp;&nbsp;&nbsp;</span>
                <span class="fan">返</span>&nbsp;&nbsp;&nbsp;&nbsp;
                <span>{{$promo->rebate[0]->custom ?? ''}}</span>
            </div>
        </div>
    @elseif($promo->type == cons('promo.type.goods-goods'))
        <div class="row">
            <div class="col-sm-5">
                <table class="table table-bordered table-center public-table">
                    <thead>
                    <tr>
                        <th>商品名称</th>
                        <th>数量</th>
                        <th>单位</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($promo->condition as $condition)
                        <tr>
                            <td>
                                <div>{{$condition->goods->name ?? ''}}</div>
                            </td>
                            <td>
                                <div>{{$condition->quantity}}</div>
                            </td>
                            <td>
                                <div>{{cons()->valueLang('goods.pieces',$condition->unit)}}</div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-sm-2 padding-clear item-txt prompt">
                下单总量达到&nbsp;&nbsp;&nbsp;&nbsp;<span class="fan">返</span>&nbsp;&nbsp;&nbsp;&nbsp;
            </div>
            <div class="col-sm-5">
                <div>
                    <table class="table table-bordered table-center public-table">
                        <thead>
                        <tr>
                            <th>商品名称</th>
                            <th>数量</th>
                            <th>单位</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($promo->rebate as $rebate)
                            <tr>
                                <td>
                                    <div>{{$rebate->goods->name ?? ''}}</div>
                                </td>
                                <td>
                                    <div>{{$rebate->quantity}}</div>
                                </td>
                                <td>
                                    <div>{{cons()->valueLang('goods.pieces',$rebate->unit)}}</div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @elseif($promo->type == cons('promo.type.goods-money'))
        <div class="row ">
            <div class="col-sm-6">
                <table class="table table-bordered table-center public-table">
                    <thead>
                    <tr>
                        <th>商品名称</th>
                        <th>数量</th>
                        <th>单位</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($promo->condition as $condition)
                        <tr>
                            <td>
                                <div>{{$condition->goods->name ?? ''}}</div>
                            </td>
                            <td>
                                <div>{{$condition->quantity}}</div>
                            </td>
                            <td>
                                <div>{{cons()->valueLang('goods.pieces',$condition->unit)}}</div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-sm-6 item-text">
                下单总量达到&nbsp;&nbsp;&nbsp;&nbsp;<span class="fan">返</span>&nbsp;&nbsp;&nbsp;&nbsp;
                ￥<span>{{$promo->rebate[0]->money}}</span>
            </div>
        </div>
    @elseif($promo->type == cons('promo.type.money-goods'))
        <div class="row  money-goods">
            <div class="col-sm-5 item-text">
                下单总量达到 ￥ <span>{{$promo->condition[0]->money}}</span>
                &nbsp;&nbsp;&nbsp;&nbsp;<span class="fan pull-right">返</span>&nbsp;&nbsp;&nbsp;&nbsp;
            </div>
            <div class="col-sm-7">
                <table class="table table-bordered table-center public-table">
                    <thead>
                    <tr>
                        <th>商品名称</th>
                        <th>数量</th>
                        <th>单位</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($promo->rebate as $rebate)
                        <tr>
                            <td>
                                <div>{{$rebate->goods->name ?? ''}}</div>
                            </td>
                            <td>
                                <div>{{$rebate->quantity}}</div>
                            </td>
                            <td>
                                <div>{{cons()->valueLang('goods.pieces',$rebate->unit)}}</div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($promo->type == cons('promo.type.money-money'))
        <div class="row money-money">
            <div class="col-sm-12 item-text other">
                下单总量达到 ￥ <span>{{$promo->condition[0]->money}}</span>
                &nbsp;&nbsp;&nbsp;&nbsp;<span class="fan">返</span>&nbsp;&nbsp;&nbsp;&nbsp;
                ￥ <span>{{$promo->rebate[0]->money}}</span>
            </div>
        </div>
    @endif
</div>