@extends('index.menu-master')
@section('subtitle', '个人中心-客户列表')
@if(request()->is('personal/customer/wholesaler'))

    @section('top-title')
        <a href="{{ url('personal/customer/retailer') }}">客户管理</a> &rarr;
        批发客户
    @stop
@else
    @section('top-title')
        <a href="{{ url('personal/customer/retailer') }}">客户管理</a> &rarr;
        终端客户
    @stop
@endif

@section('right')
    <form class="form-horizontal" method="get" action="{{ url('personal/customer/'. $type) }}" autocomplete="off">

        <div class="col-sm-12 form-group">
            <span class="item control-item">
                配送区域 :
                <select name="province_id" data-id="{{ $data['province_id'] or 0 }}"
                        class="address-province address inline-control"></select>
                <select name="city_id" data-id="{{ $data['city_id'] or 0 }}"
                        class="address-city address inline-control"></select>
                <select name="district_id" data-id="{{ $data['district_id'] or 0 }}"
                        class="address-district address inline-control"> </select>
                <select name="street_id" data-id="{{ $data['street_id'] or 0 }}"
                        class="address-street address useless-control inline-control hide"> </select>
            </span>
            <span class="item control-item">
                店铺名称 : <input class="inline-control" type="text" name="name" value="{{ $data['name'] or '' }}">
            </span>
            <span class="item control-item">
                <button type="submit" class="btn btn-default search-by-get">查询</button>
            </span>
        </div>

        <div class="row">
            <div class="col-sm-12 ">
                <div class="table-responsive">

                    <table class="table table-bordered table-center">
                        <thead>
                        <tr>
                            <th>店铺名称</th>
                            <th>联系人</th>
                            <th>联系方式</th>
                            <th>店家地址</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->shop_name }}</td>
                                <td>{{ $user->shop->contact_person }}</td>
                                <td>{{ $user->shop->contact_info }}</td>
                                <td>{{ $user->shop->address }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
    <div class="text-right">
        {!!  $users->appends(array_filter($data))->render() !!}
    </div>
    @parent
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
    </script>
@stop

