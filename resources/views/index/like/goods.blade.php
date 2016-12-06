@extends('index.menu-master')
@section('subtitle', '商品收藏')
@section('top-title')
    <a href="{{ url('like/shops') }}">我的收藏</a> >
    <span class="second-level">商品收藏</span>
@stop
@section('right')

    <div class="row collection index ">
        <div class="col-sm-12 collect">
            <div class="row">
                <form action="{{ url('like/goods') }}" method="get" autocomplete="off">
                    <div class="col-sm-12 salesman-controls">

                        <select data-id="{{ $data['province_id'] or 0 }}" class="control address-province"
                                name="province_id"></select>
                        <select data-id="{{ $data['city_id'] or 0 }}" class="control address-city"
                                name="city_id"></select>
                        <select data-id="{{ $data['district_id'] or 0 }}" class="control address-district"
                                name="district_id"></select>
                        <select data-id="{{ $data['street_id'] or 0 }}" class="control address-street"
                                name="street_id"></select>
                        <input type="text" placeholder="请输入店铺名称" class="control" name="name"
                               value="{{ $data['name'] or '' }}">
                        <button class=" btn btn-blue-lighter  search-by-get">搜索</button>
                    </div>
                </form>
            </div>
            <form>
                <div class="row list-penal">
                    @foreach($goods as $good)
                        <div class="col-sm-3 commodity">
                            <div class="img-wrap">
                                <img class="commodity-img" src="{{ $good->image_url }}">
                            </div>
                            <div class="operation">
                                <div class="text-right"><input type="checkbox" name="id[]" value="{{ $good->id }}"
                                                               class="child"/></div>
                                <div class="remove">
                                    <a data-url="{{ url('api/v1/like/interests') }}" data-method="put"
                                       data-data='{"id":{{ $good->id }}, "type" : "goods","status":0}'
                                       class="red ajax">
                                        <i class="iconfont icon-shanchu"></i>删除
                                    </a>
                                </div>
                            </div>
                            <div class="content-panel">
                                <div class="commodity-name">{{ $good->name }}</div>
                                <div class="sell-panel">
                                    <span class="money red"><b>￥{{ $good->price.'/'.$good->pieces }}</b></span>
                                    <span class="sales pull-right">最低购买量 : {{ $good->min_num }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-sm-12 batch-delete">
                        <label><input type="checkbox" class="parent"/>全选 </label>
                        <input type="hidden" name="type" value="goods"/>
                        <button data-method="put" data-url="{{ url('api/v1/like/batch') }}"
                                class="btn btn-red batch ajax">批量删除
                        </button>
                    </div>
                    <div class="text-right col-sm-12">
                        {!! $goods->appends(array_filter($data))->render() !!}
                    </div>
                </div>
            </form>

        </div>
    </div>
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
        onCheckChange('.parent', '.child');
    </script>
@stop