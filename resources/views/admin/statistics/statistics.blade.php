@extends('admin.master')
@section('subtitle' , '运营数据统计')
@include('includes.timepicker')
@section('right-container')
    <form class="form-horizontal" action="{{ url('admin/statistics') }}" method="get" autocomplete="off">
        <label for="time">时间:</label>
        <input type="text" name="start_time" class="time inline-control datetimepicker"
               data-format="YYYY-MM-DD" value="{{ $startTime }}"/>
        至
        <input type="text" name="end_time" class="time inline-control datetimepicker"
               data-format="YYYY-MM-DD" value="{{ $endTime }}"/>
        <input type="submit" class="btn btn-default" value="查询"/>
    </form>
    <ul id="myTab" class="nav nav-tabs">
        <li class="active"><a href="#data" data-toggle="tab">数据统计</a></li>
        <li><a href="#order" data-toggle="tab">订单统计</a></li>

    </ul>
    <div id="myTabContent" class="tab-content">
        <div class="tab-pane fade in active" id="data">
            <table class="table">
                <tr>
                    <th></th>
                    <th>供应商</th>
                    <th>批发商</th>
                    <th>终端商</th>
                    <th>总计</th>
                </tr>
                <tr>
                    <td>注册数</td>
                    <td>{{ array_get($statistics,'supplier_reg_num' ,0) }}</td>
                    <td>{{ array_get($statistics,'wholesaler_reg_num' ,0) }}</td>
                    <td>{{ array_get($statistics,'retailer_reg_num' ,0) }}</td>
                    <td>{{ array_get($statistics,'supplier_reg_num' ,0) + array_get($statistics,'wholesaler_reg_num' ,0) + array_get($statistics,'retailer_reg_num' ,0) }}</td>
                </tr>
                <tr>
                    <td>历史最高注册数(不包含今天)</td>
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
                    <td>登录数</td>
                    <td>{{ array_get($statistics,'supplier_login_num' ,0) }}</td>
                    <td>{{ array_get($statistics,'wholesaler_login_num' ,0) }}</td>
                    <td>{{ array_get($statistics,'retailer_login_num' ,0) }}</td>
                    <td>{{ array_get($statistics,'supplier_login_num' ,0) + array_get($statistics,'wholesaler_login_num' ,0) + array_get($statistics,'retailer_login_num' ,0) }}</td>

                </tr>
                <tr>
                    <td>历史最高登录数(不包含今天)</td>
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
                        <th>总计</th>
                    </tr>
                    <tr>
                        <td>下单数</td>
                        <td>{!! $orderEveryday['wholesaler']['count'] or 0 !!}</td>
                        <td>{!! $orderEveryday['retailer']['count'] or 0 !!}</td>
                        <td>{!! (isset($orderEveryday['wholesaler']['count'])?$orderEveryday['wholesaler']['count']:0)+(isset($orderEveryday['retailer']['count'])?$orderEveryday['retailer']['count']:0) !!}</td>
                    </tr>
                    <tr>
                        <td>下单总金额</td>
                        <td>{!! $orderEveryday['wholesaler']['amount'] or 0 !!}</td>
                        <td>{!! $orderEveryday['retailer']['amount'] or 0 !!}</td>
                        <td>{!! bcadd((isset( $orderEveryday['wholesaler']['amount'])? $orderEveryday['wholesaler']['amount']:0),(isset( $orderEveryday['retailer']['amount'])? $orderEveryday['retailer']['amount']:0),2) !!}</td>
                    </tr>
                    <tr>
                        <td>线上总金额</td>
                        <td>{!! $orderEveryday['wholesaler']['onlineAmount'] or 0 !!}</td>
                        <td>{!! $orderEveryday['retailer']['onlineAmount'] or 0 !!}</td>
                        <td>{!! bcadd((isset($orderEveryday['wholesaler']['onlineAmount'])?$orderEveryday['wholesaler']['onlineAmount']:0),(isset( $orderEveryday['retailer']['onlineAmount'])? $orderEveryday['retailer']['onlineAmount']:0),2) !!}</td>
                    </tr>
                    <tr>
                        <td>线下总金额</td>
                        <td>{!! $orderEveryday['wholesaler']['codAmount'] or 0 !!}</td>
                        <td>{!! $orderEveryday['retailer']['codAmount'] or 0 !!}</td>
                        <td>{!! bcadd((isset($orderEveryday['wholesaler']['codAmount'])?$orderEveryday['wholesaler']['codAmount']:0),(isset($orderEveryday['retailer']['codAmount'])?$orderEveryday['retailer']['codAmount']:0),2) !!}</td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-10">
                <table class="table  table-bordered">
                    <tr>
                        <th></th>
                        <th>批发商</th>
                        <th colspan="2">供应商</th>
                        <th>总计</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>对于终端商</th>
                        <th>对于终端商</th>
                        <th>对于批发商</th>
                        <th></th>
                    </tr>
                    <tr>
                        <td>成单数</td>
                        <td>{!! $orderSellerEveryday['wholesaler']['count'] or 0 !!}</td>
                        <td>{!! $orderSellerEveryday['supplier']['retailer']['count'] or 0 !!}</td>
                        <td>{!! $orderSellerEveryday['supplier']['wholesaler']['count'] or 0 !!}</td>
                        <td>{!! (isset($orderSellerEveryday['wholesaler']['count'])?$orderSellerEveryday['wholesaler']['count']:0)+(isset($orderSellerEveryday['supplier']['retailer']['count'])?$orderSellerEveryday['supplier']['retailer']['count']:0)+(isset($orderSellerEveryday['supplier']['wholesaler']['count'])?$orderSellerEveryday['supplier']['wholesaler']['count']:0) !!}</td>
                    </tr>
                    <tr>
                        <td>成单总金额</td>
                        <td>{!! $orderSellerEveryday['wholesaler']['amount'] or 0 !!}</td>
                        <td>{!! $orderSellerEveryday['supplier']['retailer']['amount'] or 0 !!}</td>
                        <td>{!! $orderSellerEveryday['supplier']['wholesaler']['amount'] or 0 !!}</td>
                        <td>{!! (isset($orderSellerEveryday['wholesaler']['amount'])?$orderSellerEveryday['wholesaler']['amount']:0)+(isset($orderSellerEveryday['supplier']['retailer']['amount'])?$orderSellerEveryday['supplier']['retailer']['amount']:0)+(isset($orderSellerEveryday['supplier']['wholesaler']['amount'])?$orderSellerEveryday['supplier']['wholesaler']['amount']:0) !!}</td>
                    </tr>
                    <tr>
                        <td>线上完成总金额</td>
                        <td>{!! isset($orderSellerEveryday['wholesaler']['onlineSuccessAmount'])?number_format($orderSellerEveryday['wholesaler']['onlineSuccessAmount'],2):'0.00' !!}
                            ({!! isset($orderSellerEveryday['wholesaler']['onlineSuccessAmountToday'])?number_format($orderSellerEveryday['wholesaler']['onlineSuccessAmount'],2):'0.00' !!}
                            +{!! bcsub((isset($orderSellerEveryday['wholesaler']['onlineSuccessAmount'])?$orderSellerEveryday['wholesaler']['onlineSuccessAmount']:0),(isset($orderSellerEveryday['wholesaler']['onlineSuccessAmountToday'])?$orderSellerEveryday['wholesaler']['onlineSuccessAmountToday']:0),2) !!}
                            )
                        </td>
                        <td>{!! isset($orderSellerEveryday['supplier']['retailer']['onlineSuccessAmount'])?number_format($orderSellerEveryday['supplier']['retailer']['onlineSuccessAmount'],2):'0.00' !!}
                            ({!! isset($orderSellerEveryday['supplier']['retailer']['onlineSuccessAmountToday'])?number_format($orderSellerEveryday['supplier']['retailer']['onlineSuccessAmountToday'],2):'0.00' !!}
                            +{!! bcsub((isset($orderSellerEveryday['supplier']['retailer']['onlineSuccessAmount'])?$orderSellerEveryday['supplier']['retailer']['onlineSuccessAmount']:0),(isset($orderSellerEveryday['supplier']['retailer']['onlineSuccessAmountToday'])?$orderSellerEveryday['supplier']['retailer']['onlineSuccessAmountToday']:0),2) !!}
                            )
                        </td>
                        <td>{!! isset($orderSellerEveryday['supplier']['wholesaler']['onlineSuccessAmount'])?number_format($orderSellerEveryday['supplier']['wholesaler']['onlineSuccessAmount'],2):'0.00' !!}
                            ({!!  isset($orderSellerEveryday['supplier']['wholesaler']['onlineSuccessAmountToday'])?number_format($orderSellerEveryday['supplier']['wholesaler']['onlineSuccessAmountToday'],2):'0.00' !!}
                            +{!! bcsub((isset($orderSellerEveryday['supplier']['wholesaler']['onlineSuccessAmount'])?$orderSellerEveryday['supplier']['wholesaler']['onlineSuccessAmount']:0),(isset($orderSellerEveryday['supplier']['wholesaler']['onlineSuccessAmountToday'])?$orderSellerEveryday['supplier']['wholesaler']['onlineSuccessAmountToday']:0),2) !!}
                            )
                        </td>
                        <td>{!! bcadd(bcadd((isset($orderSellerEveryday['wholesaler']['onlineSuccessAmount'])?$orderSellerEveryday['wholesaler']['onlineSuccessAmount']:0),(isset($orderSellerEveryday['supplier']['retailer']['onlineSuccessAmount'])?$orderSellerEveryday['supplier']['retailer']['onlineSuccessAmount']:0),2),(isset($orderSellerEveryday['supplier']['wholesaler']['onlineSuccessAmount'])?$orderSellerEveryday['supplier']['wholesaler']['onlineSuccessAmount']:0),2) !!}</td>
                    </tr>
                    <tr>
                        <td>线下完成总金额</td>
                        <td>{!! isset($orderSellerEveryday['wholesaler']['codSuccessAmount'])?number_format($orderSellerEveryday['wholesaler']['codSuccessAmount'],2):'0.00' !!}
                            ({!!  isset($orderSellerEveryday['wholesaler']['codSuccessAmountToday'])?number_format($orderSellerEveryday['wholesaler']['codSuccessAmountToday'],2):'0.00' !!}
                            +{!! bcsub((isset($orderSellerEveryday['wholesaler']['codSuccessAmount'])?$orderSellerEveryday['wholesaler']['codSuccessAmount']:0),(isset($orderSellerEveryday['wholesaler']['codSuccessAmountToday'])?$orderSellerEveryday['wholesaler']['codSuccessAmountToday']:0),2) !!}
                            )
                        </td>
                        <td>{!! isset($orderSellerEveryday['supplier']['retailer']['codSuccessAmount'])?number_format($orderSellerEveryday['supplier']['retailer']['codSuccessAmount'],2):'0.00' !!}
                            ({!!  isset($orderSellerEveryday['supplier']['retailer']['codSuccessAmountToday'])?number_format($orderSellerEveryday['supplier']['retailer']['codSuccessAmountToday'],2):'0.00' !!}
                            +{!! bcsub((isset($orderSellerEveryday['supplier']['retailer']['codSuccessAmount'])?$orderSellerEveryday['supplier']['retailer']['codSuccessAmount']:0),(isset($orderSellerEveryday['supplier']['retailer']['codSuccessAmountToday'])?$orderSellerEveryday['supplier']['retailer']['codSuccessAmountToday']:0),2) !!}
                            )
                        </td>
                        <td>{!! isset($orderSellerEveryday['supplier']['wholesaler']['codSuccessAmount'])?number_format($orderSellerEveryday['supplier']['wholesaler']['codSuccessAmount'],2):'0.00' !!}
                            ({!!  isset($orderSellerEveryday['supplier']['wholesaler']['codSuccessAmountToday'])?number_format($orderSellerEveryday['supplier']['wholesaler']['codSuccessAmountToday'],2):'0.00' !!}
                            +{!! bcsub((isset($orderSellerEveryday['supplier']['wholesaler']['codSuccessAmount'])?$orderSellerEveryday['supplier']['wholesaler']['codSuccessAmount']:0),(isset($orderSellerEveryday['supplier']['wholesaler']['codSuccessAmountToday'])?$orderSellerEveryday['supplier']['wholesaler']['codSuccessAmountToday']:0),2) !!}
                            )
                        </td>
                        <td>{!! bcadd(bcadd((isset($orderSellerEveryday['wholesaler']['codSuccessAmount'])?$orderSellerEveryday['wholesaler']['codSuccessAmount']:0),(isset($orderSellerEveryday['supplier']['retailer']['codSuccessAmount'])?$orderSellerEveryday['supplier']['retailer']['codSuccessAmount']:0),2),(isset($orderSellerEveryday['supplier']['wholesaler']['codSuccessAmount'])?$orderSellerEveryday['supplier']['wholesaler']['codSuccessAmount']:0),2) !!}</td>
                    </tr>
                    <tr>
                        <td>线下pos机完成总金额</td>
                        <td>{!! isset($orderSellerEveryday['wholesaler']['posSuccessAmount'])?number_format($orderSellerEveryday['wholesaler']['posSuccessAmount'],2):'0.00' !!}
                            ({!!  isset($orderSellerEveryday['wholesaler']['posSuccessAmountToday'])?number_format($orderSellerEveryday['wholesaler']['posSuccessAmountToday'],2):'0.00' !!}
                            +{!! bcsub((isset($orderSellerEveryday['wholesaler']['posSuccessAmount'])?$orderSellerEveryday['wholesaler']['posSuccessAmount']:0),(isset($orderSellerEveryday['wholesaler']['posSuccessAmountToday'])?$orderSellerEveryday['wholesaler']['posSuccessAmountToday']:0),2) !!}
                            )
                        </td>
                        <td>{!! isset($orderSellerEveryday['supplier']['retailer']['posSuccessAmount'])?number_format($orderSellerEveryday['supplier']['retailer']['posSuccessAmount'],2) : '0.00' !!}
                            ({!! isset($orderSellerEveryday['supplier']['retailer']['posSuccessAmountToday'])?number_format($orderSellerEveryday['supplier']['retailer']['posSuccessAmountToday'],2):'0.00' !!}
                            +{!! bcsub((isset($orderSellerEveryday['supplier']['retailer']['posSuccessAmount'])?$orderSellerEveryday['supplier']['retailer']['posSuccessAmount']:0),(isset($orderSellerEveryday['supplier']['retailer']['posSuccessAmountToday'])?$orderSellerEveryday['supplier']['retailer']['posSuccessAmountToday']:0),2) !!}
                            )
                        </td>
                        <td>{!! isset($orderSellerEveryday['supplier']['wholesaler']['posSuccessAmount'])?number_format($orderSellerEveryday['supplier']['wholesaler']['posSuccessAmount'],2):'0.00' !!}
                            ({!! isset($orderSellerEveryday['supplier']['wholesaler']['posSuccessAmountToday'])?number_format($orderSellerEveryday['supplier']['wholesaler']['posSuccessAmountToday'],2):'0.00' !!}
                            +{!! bcsub((isset($orderSellerEveryday['supplier']['wholesaler']['posSuccessAmount'])?$orderSellerEveryday['supplier']['wholesaler']['posSuccessAmount']:0),(isset($orderSellerEveryday['supplier']['wholesaler']['posSuccessAmountToday'])?$orderSellerEveryday['supplier']['wholesaler']['posSuccessAmountToday']:0),2) !!}
                            )
                        </td>
                        <td>{!! bcadd(bcadd((isset($orderSellerEveryday['wholesaler']['posSuccessAmount'])?$orderSellerEveryday['wholesaler']['posSuccessAmount']:0),(isset($orderSellerEveryday['supplier']['retailer']['posSuccessAmount'])?$orderSellerEveryday['supplier']['retailer']['posSuccessAmount']:0),2),(isset($orderSellerEveryday['supplier']['wholesaler']['posSuccessAmount'])?$orderSellerEveryday['supplier']['wholesaler']['posSuccessAmount']:0),2) !!}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@stop
