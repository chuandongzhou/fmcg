@extends('admin.master')

{{--@include('admin.promoter.promoter-modal')--}}

@section('subtitle' , '支付渠道')

@section('right-container')
    <div class="content-wrap">
        <form class="form-horizontal ajax-form" method="post"
              action="{{ url('admin/payment-channel/') }}" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
            <table class="table public-table table-bordered">
                <tr>
                    <th>渠道名</th>
                    <th>渠道识别码</th>
                    <th>渠道类型</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                @foreach($paymentChannels as $paymentChannel)
                    <tr>
                        <td>{{ $paymentChannel->name }}</td>
                        <td>{{ $paymentChannel->identification_code }}</td>
                        <td>{{ cons()->valueLang('payment_channel.type' , $paymentChannel->type)}}</td>
                        <td>{{ $paymentChannel->status ? '启用': '关闭' }}</td>
                        <td>
                            <a class="edit" href="{{ url('admin/payment-channel/' . $paymentChannel->id . '/edit') }}">
                                <i class="iconfont icon-xiugai"></i>编辑
                            </a>

                            <a class="check-or-times edit ajax" href="javascript:"
                               data-url="{{  url('admin/payment-channel/' . $paymentChannel->id) }}"
                               data-method="put"
                               data-data='{"status" : "{{ $paymentChannel->status ? 0 : 1 }}"}'
                            >
                                @if($paymentChannel->status)
                                    <i class="fa fa-times-circle"></i>禁用
                                @else
                                    <i class="fa fa-check-circle"></i>启用
                                @endif
                            </a>

                            <a class="remove ajax" href="javascript:;" data-method="delete"
                               data-url="{{ url('admin/payment-channel/' . $paymentChannel->id) }}">
                                <i class="iconfont icon-shanchu"></i>删除
                            </a>

                        </td>
                    </tr>
                @endforeach
            </table>
        </form>
    </div>
    <div class="text-right">
        {{ $paymentChannels->render() }}
    </div>
@stop
