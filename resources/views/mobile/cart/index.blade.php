@extends('mobile.master')

@section('subtitle', '店铺')

@include('includes.jquery-lazeload')

@section('header')
    <div class="fixed-header fixed-item shopping-nav">
        <div class="row nav-top margin-clear">
            <div class="col-xs-10  pd-right-clear">
                购物车(<span>10</span>)
            </div>
            <div class="col-xs-2 edit-btn pd-clear">
                <a class="edit">编辑</a>
                <input type="button" class="submit-btn hidden" value="完成">
            </div>
        </div>
    </div>
@stop

@section('body')
    @parent
    <div class="container-fluid  m60 p105">
        <div class="row cart-commodity">
            <div class="col-xs-12 shop-name-panel row-panel">
                <div class="checkbox-item item pull-left">
                    <input type="checkbox" id="checkbox1" class="check parent-children"><label for="checkbox1"></label>
                </div>
                <div class="item pull-left">
                    <i class="iconfont icon-shangpu"></i>金锣彩<span class="small">(批发商)</span>
                </div>
                <div class="item pull-right small">
                    最低配送额￥4.00
                </div>
            </div>
            <div class="col-xs-12 row-panel commodity-wrap">
                <div class="item pull-left middle-item check-item">
                    <input type="checkbox" id="checkbox2" class="check children"><label for="checkbox2"></label>
                </div>
                <div class="item pull-left middle-item">
                    <img class="commodity-img" src="http://placehold.it/100"/>
                </div>
                <div class="item pull-right commodity-panel">
                    <div class="first">
                        <div class="commodity-name">商品名称商品名称商品名称商品名称商品名称</div>
                        <div class="num-panel">
                            <div class="price red pull-left">￥2030.00/盒</div>
                            <div class="num pull-right">x1</div>
                        </div>
                    </div>
                    <div class="new hidden">
                        <div class="pull-left enter-panel">
                            <i class="less iconfont icon-jian"></i>
                            <div class="enter-num">
                                <input type="text"/>
                                <span class="min-num">最低购买数5</span>
                            </div>
                            <i class="plus iconfont icon-jia"></i>
                        </div>
                        <div class="pull-right remove">
                            <a class="red">删除</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 row-panel commodity-wrap">
                <div class="item pull-left middle-item check-item">
                    <input type="checkbox" id="checkbox3" class="check children"><label for="checkbox3"></label>
                </div>
                <div class="item pull-left middle-item">
                    <img class="commodity-img" src="http://placehold.it/100"/>
                </div>
                <div class="item pull-right commodity-panel">
                    <div class="first">
                        <div class="commodity-name">商品名称商品名称商品名称商品名称商品名称</div>
                        <div class="num-panel">
                            <div class="price red pull-left">￥2030.00/盒</div>
                            <div class="num pull-right">x1</div>
                        </div>
                    </div>
                    <div class="new hidden">
                        <div class="pull-left enter-panel">
                            <i class="less iconfont icon-jian"></i>
                            <div class="enter-num">
                                <input type="text"/>
                                <span class="min-num">最低购买数5</span>
                            </div>
                            <i class="plus iconfont icon-jia"></i>
                        </div>
                        <div class="pull-right remove">
                            <a class="red">删除</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="fixed-cart">
        <div class="item all-check pull-left">
            <label><input type="checkbox" id="checkbox7" class="check parent"><label for="checkbox7"></label>全选</label>
        </div>
        <div class="item pull-right">
            <div class="total">总额：<span class="red">￥2000.00</span></div>
            <input type="button" class="submit" value="确认结算"/>
        </div>
    </div>
@stop

@include('mobile.includes.footer')

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            onCheckChange('.parent-children', '.children');
            onCheckChange('.parent', '.parent-children, .children');

            //编辑操作
            $(".edit-btn .edit").click(function () {
                $(this).addClass("hidden").siblings(".submit-btn").removeClass("hidden");
                $(".commodity-panel .first").addClass("hidden").siblings(".new").removeClass("hidden");
                $(".total").addClass("hidden").siblings(".submit").val("删除");
            })

            //编辑完成操作
            $(".edit-btn .submit-btn").click(function () {
                $(this).addClass("hidden").siblings(".edit").removeClass("hidden");
                $(".commodity-panel .new").addClass("hidden").siblings(".first").removeClass("hidden");
                $(".total").removeClass("hidden").siblings(".submit").val("确认结账");
            })
        })
    </script>
@stop