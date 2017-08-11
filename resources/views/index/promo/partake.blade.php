@extends('index.manage-master')
@section('subtitle', '参与详情')
{{--@include('includes.timepicker')--}}
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('promo/setting') }}">促销管理</a> >
                    <span class="second-level">参与情况查看</span>
                </div>
            </div>
            <div class="row delivery promotion">
                <div class="col-sm-3 details-item">
                    <label>促销编号：</label>
                    <div class="content">{{$promo->id}}</div>
                </div>
                <div class="col-sm-3 details-item">
                    <label>促销名称：</label>
                    <div class="content">{{$promo->name}}</div>
                </div>
                <div class="col-sm-3 details-item">
                    <label>有效时间：</label>
                    <div class="content">{{$promo->start_at}}</div>
                </div>
                <div class="col-sm-3 details-item">
                    <label>参与数量：</label>
                    <div class="content">{{$promo->partake->count()}}</div>
                </div>
                <div class="col-sm-12 table-responsive table-wrap">
                    <table class="table-bordered table table-center public-table">
                        <thead>
                        <tr>
                            <th>订单编号</th>
                            <th>参与商家名称</th>
                            <th>参与时间</th>
                            <th>业务员</th>
                            <th>供应商</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($promo->apply as $apply)
                            @if(count($apply->order))
                                <tr>
                                    <td>{{$apply->order->order->id ?? ''}}</td>
                                    <td>{{$apply->client_name ?? ''}}</td>
                                    <td>{{$apply->order->created_at}}</td>
                                    <td>{{$apply->salesman_name}}</td>
                                    <td>成华玉林供应</td>
                                    <td><a class="color-blue" data-order_id="{{$apply->id ?? ''}}"
                                           data-toggle="modal" data-target="#orderDetail">查看详情</a></td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('index.promo.order_detail')
@stop
@section('js')
    @parent
    <script type="text/javascript">

    </script>
@stop
