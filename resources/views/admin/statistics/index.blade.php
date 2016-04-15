@extends('admin.master')
@section('subtitle' , '运营数据统计')
@include('includes.timepicker')
@section('right-container')

    <form class="form-horizontal" action="{{ url('admin/data-statistics') }}" method="get" autocomplete="off">
        <label for="time">时间:</label> <input type="text" name="time" class="time inline-control datetimepicker"
                                             data-format="YYYY-MM-DD" value="{{ $time }}"/>
        <input type="submit" class="btn btn-default" value="查询"/>
    </form>
    <ul id="myTab" class="nav nav-tabs">
        <li class="active">
            <a href="#home" data-toggle="tab">活跃统计</a>
        </li>
        <li><a href="#data" data-toggle="tab">当日数据统计</a></li>
        <li><a href="#order" data-toggle="tab">当日订单统计</a></li>

    </ul>
    <div id="myTabContent" class="tab-content">
        <div class="tab-pane fade in active" id="home">
            <table class="table">
                <tr>
                    <th></th>
                    <th>供应商</th>
                    <th>批发商</th>
                    <th>终端商</th>
                    <th>总计</th>
                </tr>
                <tr>
                    <td>总用户数</td>
                    <td>{{ array_get($totalUser, cons('user.type.supplier'), 0) }}</td>
                    <td>{{ array_get($totalUser, cons('user.type.wholesaler'), 0) }}</td>
                    <td>{{ array_get($totalUser, cons('user.type.retailer'), 0) }}</td>
                    <td>{{ array_sum($totalUser->toArray()) }}</td>
                </tr>
                <tr>
                    <td>活跃用户数</td>
                    <td>{{ array_get($statistics['active_user'] , 0 ,0) }}</td>
                    <td>{{ array_get($statistics['active_user'] , 1 ,0) }}</td>
                    <td>{{ array_get($statistics['active_user'] , 2 ,0) }}</td>
                    <td>{{ isset($statistics['active_user']) ? array_sum($statistics['active_user']) : 0 }}</td>
                </tr>
            </table>
        </div>
        <div class="tab-pane fade" id="data">
            <table class="table">
                <tr>
                    <th></th>
                    <th>供应商</th>
                    <th>批发商</th>
                    <th>终端商</th>
                    <th>总计</th>
                </tr>
                <tr>
                    <td>当日注册数</td>
                    <td>{{ array_get($statistics,'supplier_reg_num' ,0) }}</td>
                    <td>{{ array_get($statistics,'wholesaler_reg_num' ,0) }}</td>
                    <td>{{ array_get($statistics,'retailer_reg_num' ,0) }}</td>
                    <td>{{ array_get($statistics,'supplier_reg_num' ,0) + array_get($statistics,'wholesaler_reg_num' ,0) + array_get($statistics,'retailer_reg_num' ,0) }}</td>
                </tr>
                <tr>
                    <td>历史最高注册数</td>
                    <td>{{ array_get($maxArray['max_supplier_reg_num'],'supplier_reg_num' ) }}
                        ({{ array_get($maxArray['max_supplier_reg_num'],'created_at') }})
                    </td>
                    <td>{{ array_get($maxArray['max_wholesaler_reg_num'],'wholesaler_reg_num' ) }}
                        ({{ array_get($maxArray['max_wholesaler_reg_num'],'created_at') }})
                    </td>
                    <td>{{ array_get($maxArray['max_retailer_reg_num'],'retailer_reg_num' ) }}
                        ({{ array_get($maxArray['max_retailer_reg_num'],'created_at') }})
                    </td>
                    <td>
                        {{ array_get($maxArray['max_supplier_reg_num'],'supplier_reg_num' ) +
                        array_get($maxArray['max_wholesaler_reg_num'],'wholesaler_reg_num' ) +
                        array_get($maxArray['max_retailer_reg_num'],'retailer_reg_num' ) }}
                    </td>
                </tr>
                <tr>
                    <td>当日登录数</td>
                    <td>{{ array_get($statistics,'supplier_login_num' ,0) }}</td>
                    <td>{{ array_get($statistics,'wholesaler_login_num' ,0) }}</td>
                    <td>{{ array_get($statistics,'retailer_login_num' ,0) }}</td>
                    <td>{{ array_get($statistics,'supplier_login_num' ,0) + array_get($statistics,'wholesaler_login_num' ,0) + array_get($statistics,'retailer_login_num' ,0) }}</td>

                </tr>
                <tr>
                    <td>历史最高登录数</td>
                    <td>{{ array_get($maxArray['max_supplier_login_num'],'supplier_login_num' ) }}
                        ({{ array_get($maxArray['max_supplier_login_num'],'created_at') }})
                    </td>
                    <td>{{ array_get($maxArray['max_wholesaler_login_num'],'wholesaler_login_num' ) }}
                        ({{ array_get($maxArray['max_wholesaler_login_num'],'created_at') }})
                    </td>
                    <td>{{ array_get($maxArray['max_retailer_login_num'],'retailer_login_num' ) }}
                        ({{ array_get($maxArray['max_retailer_login_num'],'created_at') }})
                    </td>
                    <td>
                        {{ array_get($maxArray['max_supplier_login_num'],'supplier_login_num' ) +
                        array_get($maxArray['max_wholesaler_login_num'],'wholesaler_login_num' ) +
                        array_get($maxArray['max_retailer_login_num'],'retailer_login_num' ) }}
                    </td>
                </tr>
            </table>
        </div>
        <div class="tab-pane fade" id="order">
            <div class="col-sm-10">
                <table class="table  table-bordered">
                    <tr>
                        <th></th>
                        <th>批发商</th>
                        <th>终端商</th>
                    </tr>
                    <tr>
                        <td>当日下单数</td>
                        <td>{!! $orderEveryday['wholesaler']['count'] or 0 !!}</td>
                        <td>{!! $orderEveryday['retailer']['count'] or 0 !!}</td>
                    </tr>
                    <tr>
                        <td>当日下单总金额</td>
                        <td>{!! $orderEveryday['wholesaler']['amount'] or 0 !!}</td>
                        <td>{!! $orderEveryday['retailer']['amount'] or 0 !!}</td>
                    </tr>
                    <tr>
                        <td>线上总金额</td>
                        <td>{!! $orderEveryday['wholesaler']['onlineAmount'] or 0 !!}</td>
                        <td>{!! $orderEveryday['retailer']['onlineAmount'] or 0 !!}</td>
                    </tr>
                    <tr>
                        <td>线下总金额</td>
                        <td>{!! $orderEveryday['wholesaler']['codAmount'] or 0 !!}</td>
                        <td>{!! $orderEveryday['retailer']['codAmount'] or 0 !!}</td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-10">
                <table class="table  table-bordered">
                    <tr>
                        <th></th>
                        <th>批发商</th>
                        <th colspan="2">供应商</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>对于终端商</th>
                        <th>对于终端商</th>
                        <th>对于批发商</th>
                    </tr>
                    <tr>
                        <td>成单数</td>
                        <td>{!! $orderSellerEveryday['wholesaler']['count'] or 0 !!}</td>
                        <td>{!! $orderSellerEveryday['supplier']['retailer']['count'] or 0 !!}</td>
                        <td>{!! $orderSellerEveryday['supplier']['wholesaler']['count'] or 0 !!}</td>
                    </tr>
                    <tr>
                        <td>当日成单总金额</td>
                        <td>{!! $orderSellerEveryday['wholesaler']['amount'] or 0 !!}</td>
                        <td>{!! $orderSellerEveryday['supplier']['retailer']['amount'] or 0 !!}</td>
                        <td>{!! $orderSellerEveryday['supplier']['wholesaler']['amount'] or 0 !!}</td>
                    </tr>
                    <tr>
                        <td>线上完成总金额</td>
                        <td>{!! $orderSellerEveryday['wholesaler']['onlineSuccessAmount'] or 0 !!}</td>
                        <td>{!! $orderSellerEveryday['supplier']['retailer']['onlineSuccessAmount'] or 0 !!}</td>
                        <td>{!! $orderSellerEveryday['supplier']['wholesaler']['onlineSuccessAmount'] or 0 !!}</td>
                    </tr>
                    <tr>
                        <td>线下完成总金额</td>
                        <td>{!! $orderSellerEveryday['wholesaler']['codSuccessAmount'] or 0 !!}</td>
                        <td>{!! $orderSellerEveryday['supplier']['retailer']['codSuccessAmount'] or 0 !!}</td>
                        </td>
                        <td>{!! $orderSellerEveryday['supplier']['wholesaler']['codSuccessAmount'] or 0 !!}</td>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@stop
