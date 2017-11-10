@extends('index.index-master')

@section('subtitle' , '购物车')

@section('container')
    @if($shops->isEmpty())
        <div class="container table-list-row">
            <div class="row">
                <div class="col-xs-4 col-xs-offset-4 cart-empty">
                    <span class="fa fa-shopping-cart car-icon"></span>
                    <ul>
                        <li>你还没有添加商品到购物车哦，赶紧去看看吧~~</li>
                        <li><a href="{{ url('/') }}">去购物></a></li>
                    </ul>
                </div>
            </div>
        </div>
    @else
        <div class="container-wrap">
            <div class="container ">
                <div class="row audit-step-outs">
                    <div class="col-xs-3 step step-active">
                        1.查看购物车
                        <span></span>
                        <span class="triangle-right first"></span>
                        <span class="triangle-right last"></span>
                    </div>
                    <div class="col-xs-3 step">
                        2.确认订单消息
                        <span class="triangle-right first"></span>
                        <span class="triangle-right last"></span>
                    </div>
                    <div class="col-xs-3 step">
                        3.成功提交订单
                        <span class="triangle-right first"></span>
                        <span class="triangle-right last"></span>
                    </div>
                    <div class="col-xs-3 step">
                        4.等待确认
                    </div>
                </div>
            </div>
        </div>
        <form class="form-horizontal" action="{{ url('order/confirm-order') }}" method="post" autocomplete="off">
            {{ csrf_field() }}

            <div class="container dealer-index index shopping-cart">
                <div class="row table-list-row ">
                    <div class="col-xs-12 table-responsive shopping-table-list">
                        @foreach($shops as $shop)
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>
                                        <div class="check-item">
                                    <span class="span-checkbox shop-checkbox">
                                        <i class="fa fa-check"></i>
                                    </span>
                                            <input class="inp-checkbox parent-checkbox" type="checkbox" checked>
                                            <a class="shop-name" href="{{ url('shop',['id'=> $shop->id]) }}">
                                                {{ $shop->name }}
                                                ({{ cons()->valueLang('user.type' ,$shop->user_type)  }})
                                            </a>
                                        </div>
                                    </th>
                                    <th class="text-center">商品单价</th>
                                    <th class="text-center">商品数量</th>
                                    <th class="text-center">金额</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($shop['cart_goods'] as $cartGoods)
                                    <tr class="goods-list">
                                        <td width="50%">
                                            <div class="check-item">
                                                <span class="span-checkbox  goods-checkbox">
                                                    <i class="fa fa-check"></i>
                                                </span>
                                                <input class="inp-checkbox" checked name="ids[]" rel='reason'
                                                       value="{{ $cartGoods->goods_id }}" id="{{ $cartGoods->id }}"
                                                       type="checkbox">
                                                <input type="hidden" name="type" value="pc">
                                            </div>
                                            <img class="avatar" src="{{ $cartGoods->image }}">
                                            <div class="product-panel" style="width:70%!important;">
                                                <a class="product-name ellipsis"
                                                   href="{{ url('goods/' . $cartGoods->goods_id) }}" target="_blank">
                                                    {{ $cartGoods->goods->name }}
                                                </a>
                                                {!! $cartGoods->goods->is_promotion ? '<p class="promotions">(<span class="ellipsis" title="'.$cartGoods->goods->promotion_info.'"> ' . $cartGoods->goods->promotion_info . '</span>)</p>' : '' !!}
                                            </div>
                                        </td>
                                        <td class="text-center">¥
                                            <span class="goods-price">{{ $cartGoods->goods->price . '/' . $cartGoods->goods->pieces}}</span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="modified desc-num">-</button>
                                            <input class="num" data-min-num="{{ $cartGoods->goods->min_num }}"
                                                   data-max-num="{{ $cartGoods->goods->max_num }}"
                                                   data-price="{{ $cartGoods->goods->price }}"
                                                   name="num[{{ $cartGoods->goods_id }}]" type="text"
                                                   value="{{ $cartGoods->num }}">
                                            <button type="button" class="modified inc-num">+</button>
                                        </td>
                                        <td class="text-center red">¥
                                            <span class="goods-all-money">{{ $cartGoods->goods->price * $cartGoods->num }}</span>
                                        </td>
                                        <td class="text-right">
                                            <a href="javascript:void(0)" data-type="goods" data-method="post"
                                               class="btn btn-xs btn-like " data-id="{{ $cartGoods->goods_id }}">
                                                @if($cartGoods->is_like) <i class="fa fa-star"></i> 已收藏@else<i
                                                        class="fa fa-star-o"></i> 加入收藏夹 @endif
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:void(0)"
                                               data-url="{{ url('api/v1/cart/delete/'.$cartGoods->id ) }}"
                                               class="btn btn-xs ajax" data-method="delete">删除</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="6" class="text-right">
                                        <p class="lowest-money">
                                            (<span class="prompt"><span class="check-min-money">满足最低配送额¥</span>
                                        <span class="min-money">{{ $shop->min_money }}</span></span>)
                                        </p>
                                        <p class="total-money">合计金额 : <span class="red money">¥<span
                                                        class="shop-sum-price"></span></span></p>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        @endforeach
                    </div>
                </div>

                <div class="row clearing text-right  ">
                    <div class="col-xs-6 text-left left-operation">
                        <div class="check-item">
                            <span class="span-checkbox check-all"><i class="fa"></i></span>
                            <input class="inp-checkbox" type="checkbox">
                            全选
                        </div>
                        <a href="javascript:" class="batch-deletion ajax"
                           data-url="{{ url('api/v1/cart/batch-delete') }}" data-method="delete">删除选中</a>
                    </div>
                    <div class="col-xs-6 padding-clear">
                        <span class="money">合计总金额<b class="red">¥<span class="cart-sum-price"></span></b></span>
                    </div>
                    <div class="col-xs-12 text-right padding-clear">
                        <button class="btn submit btn-primary" id="cartInput" type="submit">提交</button>
                    </div>
                </div>
            </div>
        </form>
    @endif
    {{--错误提示--}}
    <div class="mask-outer" id="mask-outer">
        <div class="pop-general text-center maker-pop h200">
            <div class="pop-content">
                <a class="pull-right close-btn" href="javascript:"><i class="fa fa-remove"></i></a>
                <div class="pop-tips maker-wrap">
                    <span class="title-name"></span> <span class="maker"></span>
                </div>
                <input type="hidden" class="hidden-input" name="shopIds" value="">
                <div class="maker-msg">提交申请绑定您的平台账号信息</div>
                <div class="maker-msg"></div>
            </div>
            <div class="pop-footer-btn">
                <button class="btn btn-primary">提交申请</button>
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {
            cartFunc();
            likeFunc();
            @if (session('message'))
               alert('{{ session('message') }}');
            @endif

            @if(session('notRelation'))
                bindTips('{{implode(',',session('notRelation'))}}', '{{implode(',',array_keys(session('notRelation')))}}', 'notRelation');
            @elseif(session('applyed'))
                bindTips('{{implode(',',session('applyed'))}}', '{{implode(',',array_keys(session('applyed')))}}', 'applyed');
            @endif
            //绑定业务关系提示弹框
            function bindTips(makerName, inputVal, type) {
                var type = type || 'applyed';
                var div = $('#mask-outer');
                var titleName = '', msg = '', btn = '';
                if (type == 'applyed') {
                    titleName = '已向厂家';
                    msg = '请您耐心等待...';
                    btn = '查看';
                } else {
                    titleName = '向厂家';
                    msg = '申请通过后才能进行购买';
                    btn = '提交申请';
                }
                div.find('span.title-name').html(titleName);
                div.find('span.maker').html(makerName);
                div.find('input.hidden-input').val(inputVal);
                div.find('div.maker-msg:eq(1)').html(msg);
                div.find('button.btn').html(btn);
                type == 'applyed' ? div.find('button.btn').addClass('applyed').removeClass('submit-apply') : div.find('button.btn').addClass('submit-apply').removeClass('applyed');
                div.show();
            }

            //关闭弹窗
            $('a.close-btn').on('click', function () {
                $(".mask-outer").css("display", "none");
                /*window.location.reload();*/
            });

            $('button.submit-apply').click(function () {
                var shopIds = $('input[name=shopIds]').val();
                if (shopIds != '') {
                    $.post(site.api('business/salesman-customer/apply-bind-relation'), {'shopIds': shopIds}, function (data) {
                        bindTips('{{implode(',',array_merge(session('notRelation') ?? [],session('applyed') ?? []))}}', '');
                    })
                }
            });

            $('.pop-footer-btn').on('click','.applyed', (function () {
                window.open('{{asset('business/trade-request')}}')
                $(".mask-outer").css("display", "none");
            }));
        })
    </script>
@stop