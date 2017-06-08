@extends('index.manage-master')
@section('subtitle', '促销申请记录')
@include('includes.timepicker')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('asset/setting') }}">促销管理</a> >
                    <span class="second-level">申请记录</span>
                </div>
            </div>
            <div class="row delivery">
                <div class="col-sm-12 control-search assets">
                    <form action="" method="get" autocomplete="off">
                        <input class="enter control datetimepicker" name="start_at" placeholder="开始时间" type="text"
                               value="{{$data['start_at'] or ''}}">至
                        <input class="enter control datetimepicker" name="end_at" placeholder="结束时间" type="text"
                               value="{{$data['end_at'] or ''}}">
                        <input class="enter control" placeholder="促销名称/编号" name="number_name"
                               value="{{$data['number_name'] ?? ''}}" type="text">
                        <select name="salesman" class="control">
                            <option value="">选择业务员</option>
                            @foreach($salesmans as $salesman)
                                <option @if($data['salesman'] == $salesman->name) selected @endif value="{{$salesman->name}}">{{$salesman->name}}</option>
                            @endforeach
                        </select>

                        <select name="status" class="control">
                            <option value="">选择审核状态</option>
                            @foreach(cons('promo.review_status') as $status)
                                <option @if(!is_null($data['status']) && $data['status'] == $status) selected @endif value="{{$status}}">{{cons()->valueLang('promo.review_status',$status)}}</option>
                            @endforeach
                        </select>
                        <button type="button" class=" btn btn-blue-lighter search-by-get control ">查询</button>
                    </form>
                </div>
                <div class="col-sm-12 table-responsive wareh-details-table">
                    <table class="table-bordered table table-center public-table">
                        <thead>
                        <tr>
                            <th>促销申请编号</th>
                            <th>促销名称</th>
                            <th>促销内容</th>
                            <th>申请时间</th>
                            <th>客户名字</th>
                            <th>审核状态</th>
                            <th>申请人（业务员）</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($promoApply as $apply)
                            <tr>
                                <td>{{$apply->id}}</td>
                                <td>{{$apply->promo->name ?? ''}}</td>
                                <td width="20%">
                                    @if($apply->promo->type == cons('promo.type.custom'))
                                        {{$apply->promo->condition[0]->custom ?? ''}}
                                        送
                                        {{$apply->promo->rebate[0]->custom ?? ''}}
                                    @elseif($apply->promo->type == cons('promo.type.money-money'))
                                        任意下单总量达到
                                        ￥{{$apply->promo->condition[0]->money ?? ''}}
                                        送
                                        ￥{{$apply->promo->rebate[0]->money ?? ''}}
                                    @elseif($apply->promo->type == cons('promo.type.money-goods'))
                                        任意下单总量达到
                                        ￥{{$apply->promo->condition[0]->money ?? ''}}
                                        送
                                        商品 {{$apply->promo->rebate[0]->goods->name . (count($apply->promo->rebate) > 1 ? '...' : '')}}
                                    @elseif($apply->promo->type == cons('promo.type.goods-money'))
                                        {{$apply->promo->condition[0]->goods->name . (count($apply->promo->condition) > 1 ? '...' : '')}}
                                        任意下单总量达到 送
                                        ￥{{$apply->promo->rebate[0]->money ?? ''}}
                                    @elseif($apply->promo->type == cons('promo.type.goods-goods'))
                                        {{$apply->promo->condition[0]->goods->name . (count($apply->promo->condition) > 1 ? '...' : '')}}
                                        任意下单总量达到
                                        送
                                        {{$apply->promo->rebate[0]->goods->name . (count($apply->promo->rebate) > 1 ? '...' : '')}}
                                    @endif
                                </td>
                                <td>{{$apply->created_at}}</td>
                                <td>{{$apply->client->name ?? ''}}</td>
                                <td>{{cons()->valueLang('promo.review_status',$apply->status)}}</td>
                                <td>{{$apply->salesman->name ?? ''}}</td>
                                <td>
                                    <a href="{{url('promo/apply-log/'.$apply->id.'/detail')}}" class="color-blue"><i
                                                class="iconfont icon-chakan"></i>查看</a>
                                    <a href="javascript:"
                                       class="edit ajax @if($apply->status == cons('promo.review_status.pass')) hidden @endif"
                                       data-method="put"
                                       data-url="{{url('api/v1/promo/apply/pass/'.$apply->id)}}">
                                        <i class="iconfont icon-qiyong"></i>
                                        通过
                                    </a>
                                    <a href="javascript:"
                                       class="red ajax @if($apply->status == cons('promo.review_status.pass')) hidden @endif"
                                       data-method="put"
                                       data-url="{{url('api/v1/promo/apply/delete/'.$apply->id)}}">
                                        <i class="iconfont icon-shanchu"></i>
                                        删除
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12 text-right">
                    {!! $promoApply->render() !!}
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
