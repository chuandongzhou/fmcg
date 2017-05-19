@extends('index.manage-master')
@section('subtitle', '个人中心-客户列表')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    @if(request()->is('personal/customer/wholesaler'))
                        <a href="{{ url('personal/customer/retailer') }}">客户管理</a> >
                        <span class="second-level"> 批发客户</span>
                    @else
                        <a href="{{ url('personal/customer/retailer') }}">客户管理</a> >
                        <span class="second-level">终端客户</span>
                    @endif
                </div>
            </div>
            <div class="row delivery">
                <div class="col-sm-12 control-search">
                    <form method="get" action="{{ url('personal/customer/'. $type) }}" class="form-horizontal"
                          autocomplete="off">
                        <select name="province_id" data-id="{{ $data['province_id'] or 0 }}"
                                class="address-province address  control"></select>
                        <select name="city_id" data-id="{{ $data['city_id'] or 0 }}"
                                class="address-city address  control"></select>
                        <select name="district_id" data-id="{{ $data['district_id'] or 0 }}"
                                class="address-district address control"> </select>
                        <select name="street_id" data-id="{{ $data['street_id'] or 0 }}"
                                class="address-street address useless-control  hide"> </select>
                        <input class="control" type="text" name="name" value="{{ $data['name'] or '' }}"
                               placeholder="请输入店铺名称">
                        <button type="submit" class=" btn btn-blue-lighter search control search-by-get">查询</button>
                    </form>
                </div>
                <div class="col-sm-12 table-responsive table-wrap">
                    <table class="table-bordered table table-center public-table">
                        <thead>
                        <tr align="center">
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
                    <div class="text-right">
                        {!!  $users->appends(array_filter($data))->render() !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
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

