@extends('mobile.shop.master')

@section('subtitle', '店铺详情')

@section('body')
    @parent
    <div class="container-fluid  m185 p65">
        <div class="row shop-coupon-wrap">
            @foreach($coupons as $coupon)
                <div class="col-xs-6 coupon-item">
                    <div class="item-wrap">
                        <div class="left-panel">
                            <div class="price">¥{{ $coupon->discount }}</div>
                            <div class="claim">满{{ $coupon->full }}使用</div>
                            <div class="valid-time">{{ substr($coupon->end_at, 5) }} 前有效</div>
                        </div>
                        <div class="right-panel">
                            <a class="receive mobile-ajax"  href="javascript:"
                               data-url="{{ url('api/v1/coupon/receive/' . $coupon->id) }}" data-method="post"
                               data-done-text="领取成功" data-done-then="none">
                                <span class="txt">点击领取</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            $(".receive").on('done.hct.ajax', function () {
               $(this).closest('.coupon-item').fadeOut(function(){
                   $(this).remove();
               })
            });
            likeFunc();
        })
    </script>
@stop