@extends('index.menu-master')
@section('subtitle', '个人中心-优惠券')

@section('top-title')
    <a href="{{ url('personal/info') }}">个人中心</a> &rarr; 优惠券
@stop

@include('includes.coupon')
@section('right')
    <form action="#" method="post">
        <div class="row">
            <div class="col-sm-12 table-responsive">
                <div>
                    <a class="add" href="javascript:" data-toggle="modal" data-target="#couponModal">
                        <label>
                            <span class="fa fa-plus"></span>
                        </label>添加优惠券
                    </a>
                </div>
                <table class="table table-bordered table-center">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>满</th>
                        <th>减</th>
                        <th>库存</th>
                        <th>总量</th>
                        {{--<th>开始时间</th>--}}
                        <th>结束时间</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($coupons as $coupon)
                        <tr>
                            <td>
                                {{ $coupon->id }}
                            </td>
                            <td>
                                {{ $coupon->full }}
                            </td>
                            <td>
                                {{ $coupon->discount  }}
                            </td>
                            <td>
                                {{ $coupon->stock  }}
                            </td>
                            <td>
                                {{ $coupon->total  }}
                            </td>
                            {{--<td>--}}
                                {{--{{ $coupon->start_at  }}--}}
                            {{--</td>--}}
                            <td>
                                {{ $coupon->end_at  }}
                            </td>
                            <td>
                                {{ $coupon->status_name  }}
                            </td>
                            <td>

                                <div role="group" class="btn-group btn-group-xs">
                                    {{--<a href="javascript:" class="btn btn-primary" data-toggle="modal"--}}
                                       {{--data-target="#couponModal" data-id="{{ $coupon->id }}">--}}
                                        {{--<i class="fa fa-edit"></i> 编辑--}}
                                    {{--</a>--}}
                                    <a data-url="{{ url('api/v1/personal/coupon/'. $coupon->id) }}"
                                       data-method="delete" class="btn btn-danger ajax" type="button">
                                        <i class="fa fa-trash-o"></i> 删除
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
    @parent
@stop
