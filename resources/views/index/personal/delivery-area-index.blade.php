@extends('index.manage-master')
@section('subtitle', '个人中心-配送区域')

@include('includes.address')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('api/v1/personal/delivery-area') }}">个人中心</a> ><span class="second-level"> 配送区域</span>
                </div>
            </div>
            <form action="#" method="post">
                <div class="row margin-clear">
                    <div class="col-sm-12 table-responsive">
                        <div class="delivery-area-wrap">
                            <a class="personal-add update-modal " data-target="#addressModal"
                               data-toggle="modal" data-url="{{ url('personal/delivery-area') }}"
                            ><label><span class="fa fa-plus"></span></label>添加配送区域
                            </a>
                            <table class="table table-bordered table-center">
                                <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>区域</th>
                                    <th>配送额(元)</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($areas as $area)
                                    <tr>
                                        <td>
                                            {{ $area->id }}
                                        </td>
                                        <td>
                                            {{ $area->address_name }}
                                        </td>
                                        <td>

                                            {{ $area->min_money  }}
                                        </td>
                                        <td>

                                            <div role="group" class="btn-group btn-group-xs">
                                                <a data-target="#addressModal" data-toggle="modal"
                                                   data-id="{{ $area->id }}"
                                                   data-address="{{ $area->address }}"
                                                   data-min-money="{{ $area->min_money }}"
                                                   data-province-id="{{ $area->province_id }}"
                                                   data-city-id="{{ $area->city_id }}"
                                                   data-district-id="{{ $area->district_id }}"
                                                   data-area-name="{{ $area->area_name }}"
                                                   data-url="{{ url('api/v1/personal/delivery-area/' . $area->id) }}"
                                                   class="edit update-modal">
                                                    <i class="iconfont icon-xiugai"></i> 编辑
                                                </a>
                                                <a data-url="{{ url('api/v1/personal/delivery-area/'. $area->id) }}"
                                                   data-method="delete" class="red delete-no-form" href="javascript:"
                                                   type="button">
                                                    <i class="iconfont icon-shanchu"></i> 删除
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @parent
@stop
@section('js')
    @parent
    <script type="text/javascript">
        deleteNoForm();
    </script>
@stop
