@extends('index.manage-master')
@include('includes.timepicker')
@section('subtitle', '入库信息填写')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('inventory') }}">库存管理</a> >
                    <span class="second-level">{{is_null($outRecord) ? '入库信息填写' : '入库异常处理'}}</span>
                </div>
            </div>
            <form class="form-horizontal ajax-form" method="post"
                  action="{{ url('api/v1/inventory/in-save'/*.$goods->id*/) }}"
                  data-done-url="{{ url('inventory/in') }}"
                  data-help-class="col-sm-push-1 col-sm-10" data-done-then="referer"
                  autocomplete="off">
                <div class="row delivery">
                    <div class="col-sm-12">
                        <div class="row title-list-wrap">
                            <div class="col-sm-3 item">
                                <label>入库单号 :{{$inventory['inventory_number']}}</label>
                            </div>
                            <div class="col-sm-3 item">
                                <label>入库类型
                                    :{{$inventory['type'] == cons('inventory.inventory_type.system')?'系统入库':'手动入库'}}{{is_null($outRecord) ? '' : '(异常处理)'}}</label>
                            </div>

                            <div class="col-sm-6 control-search item">
                                @if(is_null($outRecord))
                                    <button type="button" class=" btn btn-blue-lighter control " data-target="#myModal"
                                            data-toggle="modal">选择商品
                                    </button>
                                @else
                                    <label>订单号: {{$outRecord[0]->order_number}}</label>
                                @endif
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-12 warehousing-table">
                        <table class="table-bordered table table-center table-title-blue">
                            <thead>
                            <tr>
                                <th>商品名称</th>
                                <th>生产日期 <span class="red">*</span></th>
                                <th>单位<span class="red">*</span></th>
                                <th>成本单价<span class="red">*</span></th>
                                <th>入库数量 <span class="red">*</span></th>
                                <th>备注</th>
                            </tr>
                            </thead>
                            <input type="hidden" name="inventory_number"
                                   value="{{$inventory['inventory_number'] ?? ''}}">
                            <input type="hidden" name="orderGoods" value="{{$orderGoods ?? ''}}">
                            <input type="hidden" name="inventory_type"
                                   value="{{cons('inventory.inventory_type.manual')}}">
                            <input type="hidden" name="action_type" value="{{cons('inventory.action_type.in')}}">
                            <tbody name="choosed_goods_list">
                            @if(isset($goods))
                                <tr class="inventory-tr">
                                    <td>
                                        @if(is_null($outRecord))
                                            <input type="checkbox" class="child" name="ids" value="{{$goods->id}}">
                                        @endif
                                        <img class="store-img lazy" data-original="{{ $goods->image_url }}"
                                             src="{{ $goods->image_url }}">
                                        <a class="product-name ellipsis"
                                           href="{{url('goods') .'/'. $goods->id}}"> {{$goods->name}}</a>
                                    </td>
                                    <td>
                                        @if(is_null($outRecord))
                                            <p>
                                                <input class="datetimepicker" type="text"
                                                       name="goods[{{$goods->id}}][production_date][0]"
                                                       placeholder="">
                                                <button onclick="inventory.addCol(this)" class="add-col">+</button>
                                            </p>
                                        @else
                                            @foreach($outRecord as $key => $record)
                                                <p>
                                                    <input onfocus="this.blur()" type="text"
                                                           name="goods[{{$goods->id}}][production_date][{{$key}}]"
                                                           placeholder=""
                                                           value="{{$record->production_date}}">
                                                </p>
                                                <p style="color: #bcb8c0">
                                                    (数量: {{$record->transformation_quantity }}
                                                    成本: {{$record->cost}}
                                                    / {{cons()->valueLang('goods.pieces',$record->pieces)}})

                                                </p>
                                            @endforeach
                                        @endif
                                        <p class="new-col hidden">
                                            <input class="production_date" type="text"
                                                   placeholder="">
                                            <button onclick="inventory.delCol(this)" class="remove-col">-</button>
                                        </p>

                                    </td>
                                    <td>
                                        @if(is_null($outRecord))
                                            <p class="new-col">
                                                <select class="pieces" name="goods[{{$goods->id}}][pieces][0]">
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
                                        @else
                                            @foreach($outRecord as $key => $record)
                                                <p class="new-col">
                                                    <select class="pieces"
                                                            name="goods[{{$goods->id}}][pieces][{{$key}}]">
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
                                                    {{--({{cons()->valueLang()}})--}}
                                                </p>
                                                <p>
                                                    &nbsp;
                                                </p>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if(is_null($outRecord))
                                            <p class="new-col">
                                                <input class="number cost" name="goods[{{$goods->id}}][cost][0]"
                                                       type="text">
                                            </p>
                                        @else
                                            @foreach($outRecord as $key => $record)
                                                <p class="new-col">
                                                    <input class="number cost"
                                                           name="goods[{{$goods->id}}][cost][{{$key}}]"
                                                           type="text">
                                                </p>
                                                <p>
                                                    &nbsp;
                                                </p>
                                            @endforeach
                                        @endif

                                    </td>
                                    <td>
                                        @if(is_null($outRecord))
                                            <p class="new-col">
                                                <input class="number quantity inventory"
                                                       name="goods[{{$goods->id}}][quantity][0]"
                                                       type="text"
                                                       placeholder="">
                                            </p>
                                        @else
                                            @foreach($outRecord as $key => $record)
                                                <p class="new-col">
                                                    <input class="number quantity inventory"
                                                           name="goods[{{$goods->id}}][quantity][{{$key}}]"
                                                           type="text"
                                                           placeholder="">
                                                </p>
                                                <p>
                                                    &nbsp;
                                                </p>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if(is_null($outRecord))
                                            <p class="margin-clear new-col">
                                                <textarea class="remark" name="goods[{{$goods->id}}][remark][0]"
                                                          rows="4" cols="20"></textarea>
                                            </p>
                                        @else
                                            @foreach($outRecord as $key => $record)
                                                <p class="margin-clear new-col">
                                                    <textarea class="remark"
                                                              name="goods[{{$goods->id}}][remark][{{$key}}]" rows="4"
                                                              cols="20"></textarea>
                                                </p>
                                                <p>
                                                    &nbsp;
                                                </p>
                                            @endforeach
                                        @endif
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
                                    <p>
                                        <input class="datetimepicker" type="text" name=""
                                               placeholder="2016-12-25  12:35:55">
                                        <button onclick="inventory.addCol(this)" class="add-col">+</button>
                                    </p>
                                    <p class="new-col hidden">
                                        <input class="production_date" type="text" placeholder="2016-12-25  12:35:55">
                                        <button onclick="inventory.delCol(this)" class="remove-col">-</button>
                                    </p>
                                </td>
                                <td>
                                    <p class="new-col">
                                        <select class="pieces" name="">

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
                                        <input class="number quantity inventory" name="" type="text"
                                               placeholder="">
                                    </p>
                                </td>
                                <td>
                                    <p class="margin-clear new-col">
                                        <textarea class="remark" rows="4" cols="20"></textarea>
                                    </p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-sm-12 wareh-operating">
                        @if(is_null($outRecord))
                            <label class="all-check">
                                <input type="checkbox" onclick="onCheckChange(this,'.child')" id="parent"> 全部勾选</label>
                            <button onclick="inventory.removeChecked()" class="btn btn-red " id="remove">删除已勾选</button>
                        @endif
                        <button type="submit" class="btn btn-warning pull-right">提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @if(is_null($outRecord))@include('includes.inventory-goods-list-modal')@endif
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            inventory = {
                //添加行
                addCol: function (obj) {
                    $(obj).parents("tr").children("td").each(function (e) {
                        if ($(this).children("p").hasClass("new-col")) {
                            var length = $(this).children("p").length
                            if ((length > 4 && e != 1) || length > 5) {
                                return
                            }
                            $(this).append($(this).children(".new-col:eq(0)").clone(true));
                            $newCol = $(this).children(".new-col:gt(0)");
                            $production_date = $(obj).prev('input.datetimepicker').prop('name')
                            $newCol.removeClass("hidden").children('input.production_date').each(makeDate());
                            if (e == 1) {
                                $(this).children("p").each(function (i) {
                                    if (!$(this).hasClass('hidden')) {
                                        $(this).children('input').attr('name', $production_date.slice(0, -3) + '[' + (i == 0 ? i : i - 1) + ']')
                                    }
                                });
                            } else {
                                $(this).children("p").each(function (i) {
                                    $(this).children().attr('name', $(this).children().prop('name').slice(0, -3) + '[' + (i) + ']')
                                });
                            }
                        }
                    })
                },
                //删除行
                delCol: function (obj) {
                    var num = $(obj).parents(".new-col").index() - 1;
                    $production_date = $(obj).prev().prop('name');
                    $(obj).parents("tr").children("td").each(function (e) {
                        $(this).children(".new-col").eq(num).remove();
                        if (e == 1) {
                            $(this).children("p").each(function (i) {
                                if (!$(this).hasClass('hidden')) {
                                    $(this).children('input').attr('name', $production_date.slice(0, -3) + '[' + (i == 0 ? i : i - 1) + ']')
                                }
                            });
                        } else {
                            $(this).children("p").each(function (i) {
                                $(this).children().attr('name', $(this).children().prop('name').slice(0, -3) + '[' + (i) + ']')
                            });
                        }
                    })
                },
                //删除已勾选
                removeChecked: function () {
                    $(".child:checked").parents("tr").remove();
                }

                //todo...
            };

        })
    </script>
@stop
