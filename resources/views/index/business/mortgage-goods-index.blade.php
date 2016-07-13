@extends('index.menu-master')
@section('subtitle', '业务管理-抵陈列费商品')
@include('includes.mortgage-goods')

@section('right')
    <form class="form-horizontal ajax-form" method="put" data-help-class="col-sm-push-2 col-sm-10"
          data-done-then="refresh" autocomplete="off">
        <div class="row">
            <div class="col-sm-12 table-responsive">
                <div class="col-sm-12 form-group">
                    <table class="table table-bordered table-center">
                        <thead>
                        <tr>
                            <th></th>
                            <th>商品名称</th>
                            <th>商品单位</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($mortgageGoods as $goods)
                            <tr>
                                <td>
                                    <input type="checkbox" class="child" name="goods_id[]" value="{{ $goods->id }}">
                                </td>
                                <td>
                                    {{ $goods->goods_name }}
                                </td>
                                <td>
                                    {{ $goods->pieces_name }}
                                </td>
                                <td>
                                    {{ $goods->status_name }}
                                </td>
                                <td>

                                    <div role="group" class="btn-group btn-group-xs">
                                        <a class="btn btn-primary" href="javascript:void(0)" type="button"
                                           data-target="#mortgageGoodsModal"
                                           data-toggle="modal"
                                           data-url="{{ url('api/v1/business/mortgage-goods/' . $goods->id ) }}"
                                           data-name="{{ $goods->goods_name }}"
                                           data-pieces="{{ $goods->pieces }}"><i class="fa fa-edit"></i> 编辑
                                        </a>
                                        <a href="javascript:" data-method="put"
                                           data-url="{{ url('api/v1/business/mortgage-goods/' . $goods->id . '/status') }}"
                                           data-status="{{ $goods->status }}"
                                           data-on='<i class="fa  fa-check"></i> 启用'
                                           data-off='<i class="fa  fa-minus-circle"></i> 禁用'
                                           class="no-form btn btn-default ajax-no-form">

                                            <i class="fa {{ $goods->status ? 'fa-minus-circle' : 'fa-check' }}"></i>
                                            {{ cons()->valueLang('status' , !$goods->status) }}
                                        </a>

                                        <a data-url="{{ url('api/v1/business/mortgage-goods/'. $goods->id) }}"
                                           data-method="delete" class="btn btn-danger delete-no-form">
                                            <i class="fa fa-trash-o"></i> 删除
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="col-sm-12 form-group">
                    <span class="item control-item">
                       <input type="checkbox" class="parent"> 全选
                    </span>
                    <span class="item control-item">
                        <a data-url="{{ url('api/v1/business/mortgage-goods/batch-delete') }}" data-method="delete"
                           class="btn btn-danger ajax" type="button">
                            <i class="fa fa-trash-o"></i> 批量移除
                        </a>
                        <a data-url="{{ url('api/v1/business/mortgage-goods/batch-status') }}" data-method="put"
                           class="btn btn-primary ajax" data-data='{ "status": "1" }'>
                            <i class="fa  fa-check"></i> 批量启用
                        </a>
                        <a data-url="{{ url('api/v1/business/mortgage-goods/batch-status') }}" data-method="put"
                           class="btn btn-default ajax" data-data='{ "status": "0" }'>
                            <i class="fa fa-minus-circle"></i> 批量禁用
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </form>
    @parent
@stop
@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
        onCheckChange('.parent', '.child');
        deleteNoForm();
        ajaxNoForm();
    </script>
@stop
