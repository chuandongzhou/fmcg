@extends('index.index-master')

@section('subtitle' , '购物车')

@section('container')

    <form class="form-horizontal" action="{{ url('order/confirm-order') }}" method="post" autocomplete="off">
        {{ csrf_field() }}
        <div class="container dealer-index index shopping-cart">
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
            <div class="container dealer-index index shopping-cart">
                <div class="row table-list-row">
                    @foreach($shops as $shop)
                        <div class="col-xs-12 table-responsive shopping-table-list">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>
                                        <div class="check-item">
                                            <span class="span-checkbox  shop-checkbox"><i
                                                        class="fa fa-check"></i></span>
                                            <input class="inp-checkbox parent-checkbox" type="checkbox" checked>
                                            <a class="shop-name" href="{{ url('shop',['id'=> $shop->id]) }}">
                                                {{ $shop->name }}
                                                ({{ cons()->valueLang('user.type' ,$shop->user->type)  }})
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
                                        <td>
                                            <div class="check-item">
                                            <span class="span-checkbox  goods-checkbox"><i
                                                        class="fa fa-check"></i></span>
                                                <input class="inp-checkbox" checked name="goods_id[]" rel='reason'
                                                       value="{{ $cartGoods->goods_id }}" id="{{ $cartGoods->id }}"
                                                       type="checkbox">
                                            </div>
                                            <img class="avatar" src="{{ $cartGoods->image }}">

                                            <div class="product-panel">
                                                <a class="product-name ellipsis"
                                                   href="{{ url('goods', ['id' => $cartGoods->goods->id]) }}"
                                                   target="_blank">{{ $cartGoods->goods->name }}</a>
                                                {!! $cartGoods->goods->is_promotion ? '<p class="promotions">(<span class="ellipsis"> ' . $cartGoods->goods->promotion_info . '</span>)</p>' : '' !!}
                                            </div>

                                        </td>
                                        <td class="text-center">￥<span
                                                    class="goods-price">{{ $cartGoods->goods->price . '/' . $cartGoods->goods->pieces}}</span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="modified desc-num">-</button>
                                            <input class="num" data-min-num="{{ $cartGoods->goods->min_num }}"
                                                   data-price="{{ $cartGoods->goods->price }}"
                                                   name="num[{{ $cartGoods->goods_id }}]" type="text"
                                                   value="{{ $cartGoods->num }}">
                                            <button type="button" class="modified inc-num">+</button>
                                        </td>
                                        <td class="text-center red">￥<span
                                                    class="goods-all-money">{{ $cartGoods->goods->price * $cartGoods->num }}</span>
                                        </td>
                                        <td class="text-right">
                                            <a href="javascript:void(0)" data-type="goods" data-method="post"
                                               class="btn btn-xs btn-like" data-id="{{ $cartGoods->goods_id }}">
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
                                        <p class="lowest-money">最低配送额 <b class="red money">￥<span
                                                        class="min-money">{{ $shop->min_money }}</span></b>
                                        </p>

                                        <p class="total-money">合计金额
                                            <b class="red money">￥<span class="shop-sum-price"></span></b>
                                            <b class="red not-enough">(金额不足)</b>
                                        </p>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endforeach
                </div>
                    <div class="row clearing text-right clearing-container fixed-bottom">
                        <div class="col-xs-6 text-left left-operation">
                            <div class="check-item">
                                <span class="span-checkbox  check-all"><i class="fa fa-check"></i></span>
                            </div>
                            <a href="javascript:" class="batch-deletion ajax btn padding-clear" data-url="{{ url('api/v1/cart/batch-delete') }}" data-method="delete">删除选中</a>
                        </div>
                        <div class="col-xs-6 padding-clear">
                            <span class="money">总金额<b class="red">￥<span class="cart-sum-price"></span></b></span>

                            <input id="cartInput" type="submit" value="提交" class="btn submit btn-primary"/>
                        </div>

                    </div>
            </div>
        @endif
    </form>

@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {
            fixedBottom();
            cartFunc();
            $(window).scroll(function () {
                fixedBottom();
            });
            likeFunc();
            @if (session('message'))
               alert('{{ session('message') }}');
            @endif
        })
    </script>
@stop

