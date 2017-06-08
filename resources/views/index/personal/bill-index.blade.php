@extends('index.manage-master')
@section('subtitle', '月对账单')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('personal') }}">月对账单</a> >
                    <span class="second-level">商户列表</span>
                </div>
            </div>
            <div class="row delivery">
                <div class="col-sm-12 control-search assets">
                    <form action="" method="get" autocomplete="off">
                        <input  class="enter control"   type="text" placeholder="商户名称" name="name" value="{{$name ?? ''}}">
                        <button type="button" class=" btn btn-blue-lighter search-by-get  control ">查询</button>
                    </form>
                </div>
                <div class="col-sm-12 table-responsive wareh-details-table">
                    <table class="table-bordered table table-center public-table">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>商户名称</th>
                            <th>平台账号</th>
                            <th>联系人</th>
                            <th>联系方式</th>
                            <th>营业地址</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($shops as $shop)
                            <tr>
                                <td>{{$shop->id}}</td>
                                <td>{{$shop->name}}</td>
                                <td>{{$shop->user->user_name}}</td>
                                <td>{{$shop->contact_person}}</td>
                                <td>{{$shop->contact_info}}</td>
                                <td>{{$shop->shopAddress->area_name}}</td>
                                <td>
                                    <a href="{{url('personal/bill/'.$shop->id)}}" class="edit"><i class="iconfont icon-duizhangdan"></i>对账单</a>
                                    <a href="javascript:"
                                       onclick="window.open('{{ url('personal/chat/kit?remote_uid=' .$shop->id) }}&fullscreen', 'webcall',  'toolbar=no,title=no,status=no,scrollbars=0,resizable=0,menubar＝0,location=0,width=700,height=500');"
                                       class="contact"><span class="iconfont icon-kefu"></span> 联系商户</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script>
        formSubmitByGet();
    </script>
@stop
