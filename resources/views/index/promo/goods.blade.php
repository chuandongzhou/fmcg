@extends('index.manage-master')
@section('subtitle', '促销商品')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('asset/setting') }}">促销管理</a> >
                    <span class="second-level">促销商品</span>
                </div>
            </div>
            <div class="row ">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">促销商品</h3>
                        </div>
                        <div class="panel-container clearfix salesman">
                            <form action="">
                                <div class="col-sm-12">
                                    <table class="table table-bordered public-table table-center">
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
                                        @foreach($promoGoods as $goods)
                                            <tr>
                                                <td class="check">
                                                    <input type="checkbox" class="child" name="ids[]"
                                                           value="{{$goods->id}}">
                                                </td>
                                                <td>
                                                    {{$goods->goods->name ?? ''}}
                                                </td>
                                                <td>
                                                    {{cons()->valueLang('goods.pieces',$goods->goods->goodsPieces->pieces_level_1)}}
                                                    /
                                                    @if($goods->goods->goodsPieces->pieces_level_2)
                                                        {{cons()->valueLang('goods.pieces',$goods->goods->goodsPieces->pieces_level_2)}}
                                                    @endif
                                                    @if($goods->goods->goodsPieces->pieces_level_3)
                                                        / {{cons()->valueLang('goods.pieces',$goods->goods->goodsPieces->pieces_level_3)}}
                                                    @endif
                                                </td>
                                                <td>已{{cons()->valueLang('status',$goods->status)}}</td>
                                                <td>
                                                    @if(!$goods->status)
                                                        <a data-data='{"status":"{{cons('status.on')}}"}'
                                                           data-method="post"
                                                           data-no-loading="1"
                                                           data-url="{{url('api/v1/promo/goods/'.$goods->id.'/status')}}"
                                                           class="gray ajax no-prompt"><i
                                                                    class="iconfont icon-qiyong"></i>启用</a>
                                                        @else
                                                        <a data-data='{"status":"{{cons('status.off')}}"}'
                                                           data-method="post"
                                                           data-no-loading="1"
                                                           data-url="{{url('api/v1/promo/goods/'.$goods->id.'/status')}}"
                                                           class="gray ajax no-prompt"><i
                                                                    class="iconfont icon-jinyong"></i><span
                                                                    class="red">禁用</span></a>
                                                    @endif

                                                    <a data-url="{{url('api/v1/promo/goods/'.$goods->id.'/destroy')}}"
                                                       data-method="post"
                                                       data-no-loading="1"
                                                       class="red  ajax no-prompt">
                                                        <i class="fa fa-trash-o"></i> 删除
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label><input type="checkbox" onclick="onCheckChange(this,'.child')" class="parent">
                                        全选</label>
                                    <button class="btn btn-red ajax no-prompt"
                                            data-no-loading="1"
                                            data-url="{{ url('api/v1/promo/goods/batch-destroy') }}"
                                            data-method="put">批量删除
                                    </button>
                                    <button class="btn btn-blue ajax no-prompt"
                                            data-no-loading="1"
                                            data-url="{{url('api/v1/promo/goods/batch-status')}}"
                                            data-data='{"status":"{{cons('status.on')}}"}'
                                            data-method="put">
                                        批量启用
                                    </button>
                                    <button class="btn btn-cancel ajax no-prompt"
                                            data-no-loading="1"
                                            data-url="{{url('api/v1/promo/goods/batch-status')}}"
                                            data-data='{"status":"{{cons('status.off')}}"}'
                                            data-method="put">批量禁用
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')

@stop
