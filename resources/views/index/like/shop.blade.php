@extends('index.manage-master')

@section('subtitle', '店铺收藏')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('like/shops') }}">我的收藏</a> >
                    <span class="second-level">店铺收藏</span>
                </div>
            </div>
            <div class="row collection index ">
                <div class="col-sm-12 collect">
                    <div class="row">
                        <div class="col-sm-12 salesman-controls">
                            <form action="{{ url('like/shops') }}" method="get" autocomplete="off">
                                <select data-id="{{ $data['province_id'] or 0 }}" class="control address-province"
                                        name="province_id"></select>
                                <select data-id="{{ $data['city_id'] or 0 }}" class="control address-city"
                                        name="city_id"> </select>
                                <select data-id="{{ $data['district_id'] or 0 }}" class="control address-district"
                                        name="district_id"> </select>
                                <select data-id="{{ $data['street_id'] or 0 }}" class="control address-street"
                                        name="street_id"> </select>
                                <input type="text" placeholder="请输入店铺名称" class="control" name="name"
                                       value="{{$data['name'] or '' }}">
                                <button class=" btn btn-blue-lighter  search-by-get">搜索</button>
                            </form>
                        </div>
                    </div>
                    @if(isset($shops))
                        <div class="row list-penal dealer-commodity-wrap">
                            <form>
                                @foreach($shops  as $shop)

                                    <div class="col-sm-6 pd-right-clear">
                                        <div class="thumbnail clearfix">
                                            <div class="img-wrap pull-left">
                                                <a href="{{ url('shop/' . $shop->id) }}" target="_blank">
                                                    <img class="commodity-img" src="{{ $shop->logo_url }}">
                                                </a>
                                            </div>
                                            <div class="operation">
                                                <div class="text-right">
                                                    <input type="checkbox" name="id[]" value="{{ $shop->id }}"
                                                           class="child"/>
                                                </div>
                                                <div class="remove">
                                                    <a data-url="{{ url('V1') }}" data-method="put"
                                                       data-data='{"id":{{ $shop->id }}, "type" : "shops","status":0}'
                                                       class="red ajax">
                                                        <i class="iconfont icon-shanchu"></i>
                                                        删除
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="content-panel store-content">
                                                <div class="commodity-name item">
                                                    <a href="{{ url('shop/' . $shop->id) }}" target="_blank">
                                                        <b>{{ $shop->name }}</b>
                                                    </a>
                                                </div>
                                                <div class="item address-panel">
                                                    <span class="prompt">店家地址 : </span>
                                                    <span class="address">{{ $shop->address }}</span>
                                                </div>
                                                <div class="store-presentation item">
                                                    <span class="prompt">店家介绍 : </span>
                                                    <span title="{{ $shop->introduction }}">{{ $shop->introduction }}</span>
                                                </div>
                                                <div class="sell-panel item">
                                                    <span class="sales prompt">最低配送额 : </span>
                                                    <span class="money">¥{{ $shop->min_money }}</span>
                                                </div>
                                                <div class="order-count item">
                                                    <div class="item-child">
                                                        <span class="prompt">店铺销量 : </span>
                                                        <span>{{ $shop->sales_volume }}</span>
                                                    </div>
                                                    <div class="item-child">
                                                        <span class="prompt">共</span>{{ $shop->goods_count }}
                                                        <span class="prompt">件商品</span>
                                                    </div>
                                                    <div class="item-child">
                                                        <a href="javascript:"
                                                           onclick="window.open('{{ url('personal/chat/kit?remote_uid=' .$shop->id) }}&fullscreen', 'webcall',  'toolbar=no,title=no,status=no,scrollbars=0,resizable=0,menubar＝0,location=0,width=700,height=500');"
                                                           class="contact"><span class="iconfont icon-kefu"></span> 联系客服</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-sm-12 batch-delete">
                                    <label><input type="checkbox" class="parent"/>全选 </label>
                                    <input type="hidden" name="type" value="shops"/>
                                    <button data-method="put" data-url="{{ url('api/v1/like/batch') }}"
                                            class="btn btn-red batch ajax">批量删除
                                    </button>
                                </div>
                            </form>
                            <div class="text-right col-sm-12">
                                {!! $shops->appends(array_filter($data))->render() !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
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
