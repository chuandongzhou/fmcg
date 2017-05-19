@extends('index.manage-master')
@section('subtitle', '个人中心-提现账号')
@include('includes.bank')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('personal/finance/balance') }}">财务管理</a> >
                    <span class="second-level">提现账号</span>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 ">
                    <a class="add-bank-account btn btn-blue-lighter update-modal " href="javascript:"
                       data-toggle="modal"
                       data-target="#bankModal">
                        <span class="fa fa-plus"></span>
                        添加提现账号
                    </a>
                </div>
                <div class="col-sm-12">
                    <div class="row bank-list-wrap">
                        @foreach($userBanks as $bank)
                            <div class="col-sm-4 item {{ $bank['is_default']==1?'active':'' }}">
                                <div class="panel">
                                    <p>{{ cons()->valueLang('bank.type' , $bank['card_type']) }}</p>
                                    <p class="clearfix account-number">
                                        <b class="pull-left">{{ $bank['card_number'] }}</b>
                                        @if($bank['is_default']==1)
                                            <span class="pull-right"><i class="iconfont icon-qiyong"></i>默认</span>
                                        @else
                                            <a class="pull-right ajax"
                                               data-url="{{ url('api/v1/personal/bank-default/'.$bank['id']) }}"
                                               data-method="put">设为默认</a>
                                        @endif

                                    </p>
                                    <p class="clearfix">
                                        <span class="pull-left">{{ $bank['card_holder'] }}</span>
                                        <a class="pull-right edit update-modal operate" data-toggle="modal"
                                           data-target="#bankModal" data-id="{{ $bank['id'] }}">
                                            <i class="iconfont icon-xiugai"></i>编辑</a>
                                    </p>
                                    <p class="clearfix">
                                        <span class="pull-left">{{ $bank['card_address'] }}</span>
                                        <a class="pull-right red ajax operate"
                                           data-url="{{ url('api/v1/personal/bank/'.$bank['id']) }}"
                                           data-method="delete"><i class="iconfont icon-shanchu"></i>删除</a>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @parent
@stop
