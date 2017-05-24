@extends('index.manage-master')
@include('includes.timepicker')
@include('includes.inventory-goods-list-modal')
@section('subtitle', '出库信息填写')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('inventory') }}">库存管理</a> >
                    <span class="second-level">出库信息填写</span>
                </div>
            </div>
            <form class="form-horizontal ajax-form" method="post"
                  action="{{ url('api/v1/inventory/out-save'/*.$goods->id*/) }}" data-done-url="{{ url('inventory') }}"
                  data-help-class="col-sm-push-1 col-sm-10" data-done-then="referer"
                  autocomplete="off">
                <div class="row delivery">
                    <div class="col-sm-12">
                        <div class="row title-list-wrap">
                            <div class="col-sm-3 item">
                                <label>出库单号 :</label>
                                {{session('inv')['inventory_number']}}
                            </div>
                            <div class="col-sm-3 item">
                                <label>出库类型 :</label>
                                {{session('inv')['type'] == cons('inventory.inventory_type.system')?'系统出库':'手动出库'}}
                            </div>
                            <div class="col-sm-6 control-search item">
                                <button type="button" class=" btn btn-blue-lighter control " data-target="#myModal"
                                        data-toggle="modal">选择商品
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 table-responsive warehousing-table">
                        <table class="table-bordered table table-center table-title-blue">
                            <thead>
                            <tr>
                                <th>商品名称</th>
                                <th>单位 <span class="red">*</span></th>
                                <th>出库价格 <span class="red">*</span></th>
                                <th>出库数量 <span class="red">*</span></th>
                                <th>备注</th>
                            </tr>
                            </thead>
                            <input type="hidden" name="inventory_number"
                                   value="{{session('inv')['inventory_number'] ?? ''}}">
                            <input type="hidden" name="inventory_type"
                                   value="{{cons('inventory.inventory_type.manual')}}">
                            <input type="hidden" name="action_type" value="{{cons('inventory.action_type.out')}}">
                            <tbody name="choosed_goods_list">
                            @if(isset($goods))
                                <tr class="inventory-tr">
                                    <td>
                                        <input type="checkbox" class="child" name="ids" value="{{$goods->id}}">
                                        <img class="store-img lazy" data-original="{{ $goods->image_url }}"
                                             src="{{ $goods->image_url }}">
                                        <a class="product-name ellipsis"
                                           href="{{url('goods') .'/'. $goods->id}}"> {{$goods->name}}</a>
                                    </td>
                                    <td>
                                        <p class="new-col">
                                            <select name="goods[{{$goods->id}}][pieces][]">
                                                <option>请选择</option>
                                                @if($goods->goodsPieces && is_numeric($goods->goodsPieces->pieces_level_1))
                                                    <option class="retailer_pieces_level_1"
                                                            value="{{ $goods->goodsPieces->pieces_level_1 }}">{{ cons()->valueLang('goods.pieces',$goods->goodsPieces->pieces_level_1) }}</option>
                                                @endif
                                                @if($goods->goodsPieces && is_numeric($goods->goodsPieces->pieces_level_2))
                                                    <option class="retailer_pieces_level_2"
                                                            value="{{ $goods->goodsPieces->pieces_level_2 }}">{{ cons()->valueLang('goods.pieces',$goods->goodsPieces->pieces_level_2) }}</option>
                                                @endif
                                                @if($goods->goodsPieces && is_numeric($goods->goodsPieces->pieces_level_3))
                                                    <option class="retailer_pieces_level_3"
                                                            value="{{ $goods->goodsPieces->pieces_level_3 }}">{{ cons()->valueLang('goods.pieces',$goods->goodsPieces->pieces_level_3) }}</option>
                                                @endif
                                            </select>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="new-col">
                                            <input class="number cost" name="goods[{{$goods->id}}][cost][]" type="text">
                                        </p>
                                    </td>
                                    <td>
                                        <p class="new-col">
                                            <input class="number inventory" name="goods[{{$goods->id}}][quantity][]"
                                                   type="text"
                                                   placeholder="">
                                        </p>
                                    </td>
                                    <td>
                                        <p class="margin-clear new-col">
                                    <textarea name="goods[{{$goods->id}}][remark][]" rows="4" cols="20">
                                    </textarea>
                                        </p>
                                    </td>
                                </tr>
                            @endif
                            <tr class="modal inventory-template">
                                <td>
                                    <input type="checkbox" class="" name="ids" value="">
                                    <img class="store-img lazy" data-original=""
                                         src="">
                                    <a class="product-name ellipsis"
                                       href=""> </a>
                                </td>
                                <td>
                                    <p class="new-col">
                                        <select name="">

                                        </select>
                                    </p>
                                </td>
                                <td>
                                    <p class="new-col">
                                        <input class="number cost" name="" type="text">
                                    </p>
                                </td>
                                <td>
                                    <p class="new-col">
                                        <input class="number inventory" name="" type="text"
                                               placeholder="">
                                    </p>
                                </td>
                                <td>
                                    <p class="margin-clear new-col">
                                    <textarea rows="4" cols="20">
                                    </textarea>
                                    </p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-12 wareh-operating">
                        <label class="all-check">
                            <input type="checkbox" onclick="onCheckChange(this,'.child')" id="parent"> 全部勾选</label>
                        <button onclick="inventory.removeChecked()" class="btn btn-red " id="remove">删除已勾选</button>
                        <button type="submit" class="btn btn-warning pull-right">提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            inventory = {
                //删除已勾选
                removeChecked: function () {
                    $(".child:checked").parents("tr").remove();
                }
                //todo...
            };
        })
    </script>
@stop
