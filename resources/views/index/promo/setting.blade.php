@extends('index.manage-master')
@section('subtitle', '促销设置')
@include('includes.timepicker')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('asset/setting') }}">促销管理</a> >
                    <span class="second-level">促销设置</span>
                </div>
            </div>
            <div class="row delivery">
                <div class="col-sm-12 control-search assets">
                    <form action="" method="get" autocomplete="off">
                        <input class="enter control datetimepicker" name="start_at"
                               placeholder="开始时间" type="text"
                               value="{{$data['start_at'] ?? ''}}">至
                        <input class="enter control datetimepicker" name="end_at"
                               placeholder="结束时间" type="text"
                               value="{{$data['end_at'] ?? ''}}">
                        <input name="number_name" class="enter control" placeholder="促销名称/编号" type="text" value="{{$data['number_name'] ?? ''}}">
                        <button type="button" class=" btn btn-blue-lighter search-by-get control ">查询</button>
                        <a href="{{url('promo/add')}}" class=" btn btn-blue-lighter  control ">添加促销</a>
                    </form>
                </div>
                <div class="col-sm-12 table-responsive wareh-details-table">
                    <table class="table-bordered table table-center public-table">
                        <thead>
                        <tr>
                            <th>促销编号</th>
                            <th>促销信息名称</th>
                            <th>添加/修改时间</th>
                            <th>有效时间</th>
                            <th>促销内容</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($promos as $promo)
                            <tr>
                                <td>{{$promo->id}}</td>
                                <td>{{$promo->name}}</td>
                                <td>{{$promo->updated_at}}</td>
                                <td>{{$promo->start_at}} 至 {{$promo->end_at}}</td>
                                <td width="30%">
                                    @if($promo->type == cons('promo.type.custom'))
                                        {{$promo->condition[0]->custom ?? ''}}
                                        送
                                        {{$promo->rebate[0]->custom ?? ''}}
                                    @elseif($promo->type == cons('promo.type.money-money'))
                                        任意下单总量达到
                                        ￥{{$promo->condition[0]->money ?? ''}}
                                        送
                                        ￥{{$promo->rebate[0]->money ?? ''}}
                                    @elseif($promo->type == cons('promo.type.money-goods'))
                                        任意下单总量达到
                                        ￥{{$promo->condition[0]->money ?? ''}}
                                        送
                                        商品 {{$promo->rebate[0]->goods->name . (count($promo->rebate) > 1 ? '...' : '')}}
                                    @elseif($promo->type == cons('promo.type.goods-money'))
                                        {{$promo->condition[0]->goods->name . (count($promo->condition) > 1 ? '...' : '')}}
                                        任意下单总量达到 送
                                        ￥{{$promo->rebate[0]->money ?? ''}}
                                    @elseif($promo->type == cons('promo.type.goods-goods'))
                                        {{$promo->condition[0]->goods->name . (count($promo->condition) > 1 ? '...' : '')}}
                                        任意下单总量达到
                                        送
                                        {{$promo->rebate[0]->goods->name . (count($promo->rebate) > 1 ? '...' : '')}}
                                    @endif
                                </td>
                                <td>
                                    @if(strtotime($promo->end_at) > time())
                                        @if($promo->status == cons('status.off'))
                                            <a href="{{url('promo/'.$promo->id.'/edit')}}" class="edit">
                                                <i class="iconfont icon-xiugai"></i>
                                                修改
                                            </a>
                                        @endif
                                        <a href="javascript:" data-method="put"
                                           data-url="{{ url('api/v1/promo/status/' . $promo->id) }}"
                                           data-data='{"status":"{{$promo->status ? cons('status.off') : cons('status.on')}}"}'
                                           class="no-form  ajax">
                                            <i class="fa {{ $promo->status ? 'fa-minus-circle' : 'fa-check' }}"></i>
                                            {{ cons()->valueLang('status' , !$promo->status) }}
                                        </a>
                                    @endif
                                    <a href="{{url('promo/'.$promo->id.'/view')}}" class="color-blue"><i
                                                class="iconfont icon-chakan"></i>查看</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12 text-right">
                    {!! $promos->render() !!}
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
    </script>
@stop
