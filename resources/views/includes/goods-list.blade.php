@foreach($goods  as $item)
    @if ($item->price > 0)
        <div class="col-sm-3 commodity commodity-search-product">
            <div class="commodity-border">
                <div class="img-wrap">
                    <a href="{{ url('goods/' . $item->id) }}" target="_blank">
                        <img class="commodity-img lazy" data-original="{{ $item->image_url }}">
                        <span class="@if($item->is_out)prompt  lack  @elseif($item->is_promotion)prompt  promotions @elseif($item->is_new)prompt  new-listing @endif"></span>
                    </a>
                </div>
                <div class="content-panel">
                    <div class="commodity-name">
                        <a href="{{ url('goods/' . $item->id) }}" target="_blank">{{ $item->name }}</a></div>
                    <div class="sell-panel ">
                        <span class="money red">¥{{ $item->price . '/' . $item->pieces }}</span>
                        <span class="sales pull-right">最低购买量 : {{ $item->min_num }}</span>
                    </div>

                    <div class="shopping-store">
                        <button type="button" data-group="group{{ $item->id }}" class="count modified desc-num"
                                disabled>-
                        </button>
                        <input type="text" data-group="group{{ $item->id }}" class="amount num" name="num"
                               value="{{ $item->min_num }}" data-min-num="{{ $item->min_num }}">
                        <button type="button" data-group="group{{ $item->id }}" class="count modified inc-num">+
                        </button>
                        @if($item->is_out)
                            <a href="javascript:void(0)" class="btn btn-primary disabled join-cart" disabled="">缺货</a>
                        @else
                            <a href="javascript:void(0)"
                               data-url="{{ isset($shop)&&$user->id==$shop->user_id?'':url('api/v1/cart/add/'.$item->id) }}"
                               class="btn btn-primary join-cart {{ isset($shop)&&$user->id==$shop->user_id?'disabled':'' }}"
                               data-group="group{{ $item->id }}">加入购物车</a>
                        @endif
                        <div class="sales prompt">累积销量：{{ $item->sales_volume }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach