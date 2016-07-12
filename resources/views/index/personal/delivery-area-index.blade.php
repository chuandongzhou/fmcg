@extends('index.menu-master')
@section('subtitle', '个人中心-配送人员')

@section('top-title')
    <a href="{{ url('personal/info') }}">个人中心</a> &rarr; <a href="{{ url('personal/shop') }}">配送区域</a>
@stop
@section('right')
    <form action="#" method="post">
        <div class="row">
            <div class="col-sm-12 table-responsive">
                <div>
                    <a class="add" href="{{ url('personal/delivery-area/create') }}" ><label><span class="fa fa-plus"></span></label>添加配送区域
                    </a>
                </div>
                <table class="table table-bordered table-center">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>区域</th>
                        <th>配送额</th>
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
                                    <a href="{{ url('personal/delivery-area/'. $area->id . '/edit') }}"
                                       class="btn btn-primary">
                                        <i class="fa fa-edit"></i> 编辑
                                    </a>
                                    <a data-url="{{ url('api/v1/personal/delivery-area/'. $area->id) }}"
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
