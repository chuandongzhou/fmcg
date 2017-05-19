@extends('child-user.manage-master')
@section('subtitle', '业务管理-抵陈列费商品')

@include('includes.mortgage-goods')

@section('container')
    @include('includes.child-menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('child-user/salesman') }}">业务管理</a> >
                    <span class="second-level">抵陈列费商品</span>
                </div>
            </div>

            <form class="form-horizontal ajax-form" method="put" data-help-class="col-sm-push-2 col-sm-10"
                  data-done-then="refresh" autocomplete="off">
                <div class="row ">
                    <div class="col-sm-12 table-responsive">

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">陈列费商品</h3>
                            </div>
                            <div class="panel-container clearfix">
                                <table class="table table-bordered table-center public-table">
                                    <thead>
                                    <tr>
                                        <th>选择</th>
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
                                                <input type="checkbox" class="child" name="goods_id[]"
                                                       value="{{ $goods->id }}">
                                            </td>
                                            <td>
                                                {{ $goods->goods_name }}
                                            </td>
                                            <td>
                                                {{ $goods->pieces_name }}
                                            </td>
                                            <td>
                                                已<span class="status-name">{{ $goods->status_name }}</span>
                                            </td>
                                            <td>

                                                <div role="group" class="btn-group btn-group-xs">
                                                    <a class="edit" href="javascript:void(0)" type="button"
                                                       data-target="#mortgageGoodsModal"
                                                       data-toggle="modal"
                                                       data-url="{{ url('api/v1/child-user/mortgage-goods/' . $goods->id ) }}"
                                                       data-name="{{ $goods->goods_name }}"
                                                       data-goods-id="{{ $goods->goods_id }}"
                                                       data-pieces="{{ $goods->pieces }}"><i class="fa fa-edit"></i> 编辑
                                                    </a>
                                                    <a href="javascript:" data-method="put"
                                                       data-url="{{ url('api/v1/child-user/mortgage-goods/' . $goods->id . '/status') }}"
                                                       data-status="{{ $goods->status }}"
                                                       data-on='<i class="fa  fa-check"></i> 启用'
                                                       data-off='<i class="fa  fa-minus-circle"></i> 禁用'
                                                       class="no-form  ajax-no-form">

                                                        <i class="fa {{ $goods->status ? 'fa-minus-circle' : 'fa-check' }}"></i>
                                                        {{ cons()->valueLang('status' , !$goods->status) }}
                                                    </a>

                                                    <a data-url="{{ url('api/v1/child-user/mortgage-goods/'. $goods->id) }}"
                                                       data-method="delete" class="red delete-no-form">
                                                        <i class="fa fa-trash-o"></i> 删除
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="text-right">
                                    {!! $mortgageGoods->render() !!}
                                </div>
                                <div class="col-sm-12 form-group">
                            <span class="item control-item">
                               <input type="checkbox" class="parent"> 全选
                            </span>
                                    <span class="item control-item">
                                <a data-url="{{ url('api/v1/child-user/mortgage-goods/batch-delete') }}"
                                   data-method="delete"
                                   class="btn btn-danger ajax" type="button">
                                    <i class="fa fa-trash-o"></i> 批量移除
                                </a>
                                <a data-url="{{ url('api/v1/child-user/mortgage-goods/batch-status') }}" data-method="put"
                                   class="btn btn-primary ajax" data-data='{ "status": "1" }'>
                                    <i class="fa  fa-check"></i> 批量启用
                                </a>
                                <a data-url="{{ url('api/v1/child-user/mortgage-goods/batch-status') }}" data-method="put"
                                   class="btn btn-default ajax" data-data='{ "status": "0" }'>
                                    <i class="fa fa-minus-circle"></i> 批量禁用
                                </a>
                            </span>
                                </div>

                            </div>
                        </div>


                    </div>
                </div>

            </form>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        formSubmitByGet();
        onCheckChange('.parent', '.child');
        deleteNoForm();
        ajaxNoForm(true);
    </script>
@stop
