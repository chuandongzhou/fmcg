@extends('child-user.manage-master')

@section('subtitle', '财务管理-续费记录')

@section('container')
    @include('includes.child-menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('child-user/coupon') }}">财务管理</a> >
                    <span class="second-level"> 续费记录</span>
                </div>
            </div>
            <div class="row store personal-store">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">签约管理</h3>
                        </div>
                        <div class="panel-container clearfix">
                            @include('includes.sign', compact('user'))
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="notice-bar clearfix">
                                        <a class="active" href="javascript:">续费记录</a>
                                    </div>
                                </div>
                                <div class="col-sm-12 table-responsive">
                                    <table class="table table-bordered table-center public-table">
                                        <thead>
                                        <tr>
                                            <th>单号</th>
                                            <th>续期</th>
                                            <th>详情</th>
                                            <th>费用</th>
                                            <th>原到期时间</th>
                                            <th>时间</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($renews as $renew)
                                            <tr>
                                                <td>{{ $renew->id }}</td>
                                                <td>{{ $renew->renew_type }}</td>
                                                <td>{{ $renew->detail }}</td>
                                                <td>{{ $renew->cost }}</td>
                                                <td>{{ $renew->old_expire_at->toDateString() }}</td>
                                                <td>{{ $renew->created_at }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="right page">
                                {{ $renews->render() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop