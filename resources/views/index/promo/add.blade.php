@extends('index.manage-master')
@section('subtitle', request()->is('promo/*/edit') ? '促销编辑' : (request()->is('promo/*/view') ? '促销查看' : '促销添加'))

@include('includes.timepicker')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('promo/setting') }}">促销管理</a> >
                    <span class="second-level">{{request()->is('promo/*/edit') ? '促销编辑' : (request()->is('promo/*/view') ? '促销查看' : '促销添加')}}</span>
                </div>
            </div>
            <div class="row salesman">
                <div class="col-sm-12 create">
                    <form method="post" action="{{request()->is('promo/*/edit') ? url('api/v1/promo/edit/'.$promo->id) : (request()->is('promo/*/view') ? '' : url('api/v1/promo/add'))}}" class="form-horizontal ajax-form"
                          data-help-class="col-sm-push-2 col-sm-10 " data-done-url="{{url('promo/setting')}}"
                          autocomplete="off">
                        <div class="form-group row">
                            <label class="col-sm-2 control-label"> 促销名称 :</label>

                            <div class="col-sm-5 ">
                                <input name="name" {{request()->is('promo/*/view')?'disable':''}} class="form-control"
                                       placeholder="活动名称" type="text"
                                       value="{{$promo ->name ?? ''}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label"> 有效时间 :</label>

                            <div class="col-sm-2  pd-right-clear">
                                <input name="start_at" class="form-control datetimepicker" placeholder="开始时间"
                                       type="text" value="{{$promo->start_at ?? ''}}">
                            </div>
                            <div class="col-sm-1 company padding-clear text-center">至</div>
                            <div class="col-sm-2 pd-left-clear">
                                <input name="end_at" class="form-control datetimepicker" placeholder="结束时间" type="text"
                                       value="{{$promo->end_at ?? ''}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label"> 选择类型 :</label>

                            <div class="col-sm-3 ">
                                <select name="type" class="form-control" id="opt-control">
                                    <option value="">请选择促销类型</option>
                                    @foreach(cons('promo.type') as $key => $type)
                                        <option @if(isset($promo) && $promo->type == $type) selected
                                                @endif class="{{$key}}"
                                                value="{{$type}}">{{cons()->valueLang('promo.type',$type)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-10 col-sm-offset-2 promo-add select-wrap">
                                <!--自定义-->
                                <div class="row option-panel @if(isset($promo) && $promo->type == cons('promo.type.custom')) active @endif custom">
                                    <div class="col-sm-12 item">
                                        <ul>
                                            <li><label>设置条件 :</label></li>
                                            <li>
                                                <textarea name="condition[custom]" placeholder="填写内容" rows="5"
                                                          cols="30">
                                                    @if(isset($promo) && $promo->type == cons('promo.type.custom')) {{$promo->condition[0]->custom}} @endif
                                                </textarea>
                                            </li>
                                            <li><span class="fan">返</span></li>
                                            <li>
                                                <textarea name="rebate[custom]" placeholder="填写内容" rows="5"
                                                          cols="30">
                                                    @if(isset($promo) && $promo->type == cons('promo.type.custom')) {{$promo->rebate[0]->custom}} @endif
                                                </textarea>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!--下单商品总量达到返商品-->
                                <div class="row option-panel @if(isset($promo) && $promo->type == cons('promo.type.goods-goods')) active @endif goods-goods">
                                    <div class="col-sm-5">
                                        <div class="select-commodity">本次参与促销商品 <a class="btn btn-border-blue"
                                                                                  data-target="#chooseGoods"
                                                                                  data-toggle="modal">选择商品</a></div>
                                        <div>
                                            <table class="table table-bordered table-center public-table">
                                                <thead>
                                                <tr>
                                                    <th>商品名称</th>
                                                    <th>单位</th>
                                                    <th>数量</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if(isset($promo) && $promo->type == cons('promo.type.goods-goods'))
                                                    @foreach($promo->condition as $condition)
                                                        <tr>
                                                            <td>
                                                                <div>{{$condition->goods->name ?? ''}}</div>
                                                            </td>
                                                            <td>
                                                                <select name="condition[unit][]">';
                                                                    <option value="">请选择</option>
                                                                    <option @if($condition->unit == $condition->goods->goodsPieces->pieces_level_1) selected
                                                                            @endif value={{$condition->goods->goodsPieces->pieces_level_1}}>{{cons()->valueLang('goods.pieces',$condition->goods->goodsPieces->pieces_level_1)}}</option>

                                                                    @if(!is_null($condition->goods->goodsPieces->pieces_level_2))
                                                                        <option @if($condition->unit == $condition->goods->goodsPieces->pieces_level_2) selected
                                                                                @endif value={{$condition->goods->goodsPieces->pieces_level_2}}>{{cons()->valueLang('goods.pieces',$condition->goods->goodsPieces->pieces_level_2)}}</option>
                                                                    @endif
                                                                    @if(!is_null($condition->goods->goodsPieces->pieces_level_3))
                                                                        <option @if($condition->unit == $condition->goods->goodsPieces->pieces_level_3) selected
                                                                                @endif value={{$condition->goods->goodsPieces->pieces_level_3}}>{{cons()->valueLang('goods.pieces',$condition->goods->goodsPieces->pieces_level_3)}}</option>
                                                                    @endif
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="condition[quantity][]"
                                                                       class="num" placeholder="输入数量"
                                                                       value="{{$condition->quantity ?? ''}}"/>
                                                                <input type="hidden" disabled name="ids"
                                                                       value='{{$condition->goods_id}}'/>
                                                                <input type="hidden" name="condition[goods_id][]"
                                                                       value='{{$condition->goods_id}}'/>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 padding-clear prompt">
                                        任意下单总量达到<span class="fan">返</span>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="select-commodity">本次参与促销商品
                                            <a class="btn btn-border-blue rebate" data-target="#chooseGoods"
                                               data-toggle="modal">选择商品</a>
                                        </div>
                                        <div>
                                            <table class="table table-bordered table-center public-table">
                                                <thead>
                                                <tr>
                                                    <th>商品名称</th>
                                                    <th>单位</th>
                                                    <th>数量</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if(isset($promo) && $promo->type == cons('promo.type.goods-goods'))
                                                    @foreach($promo->rebate as $rebate)
                                                        <tr>
                                                            <td>
                                                                <div>{{$rebate->goods->name ?? ''}}</div>
                                                            </td>
                                                            <td>
                                                                <select name="rebate[unit][]">';
                                                                    <option value="">请选择</option>
                                                                    <option @if($rebate->unit == $rebate->goods->goodsPieces->pieces_level_1) selected
                                                                            @endif value={{$rebate->goods->goodsPieces->pieces_level_1}}>{{cons()->valueLang('goods.pieces',$rebate->goods->goodsPieces->pieces_level_1)}}</option>

                                                                    @if(!is_null($rebate->goods->goodsPieces->pieces_level_2))
                                                                        <option @if($rebate->unit == $rebate->goods->goodsPieces->pieces_level_2) selected
                                                                                @endif value={{$rebate->goods->goodsPieces->pieces_level_2}}>{{cons()->valueLang('goods.pieces',$rebate->goods->goodsPieces->pieces_level_2)}}</option>
                                                                    @endif
                                                                    @if(!is_null($rebate->goods->goodsPieces->pieces_level_3))
                                                                        <option @if($rebate->unit == $rebate->goods->goodsPieces->pieces_level_3) selected
                                                                                @endif value={{$rebate->goods->goodsPieces->pieces_level_3}}>{{cons()->valueLang('goods.pieces',$rebate->goods->goodsPieces->pieces_level_3)}}</option>
                                                                    @endif
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="rebate[quantity][]"
                                                                       class="num" placeholder="输入数量"
                                                                       value="{{$rebate->quantity ?? ''}}"/>
                                                                <input type="hidden" disabled name="ids"
                                                                       value='{{$rebate->goods_id}}'/>
                                                                <input type="hidden" name="rebate[goods_id][]"
                                                                       value='{{$rebate->goods_id}}'/>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--下单商品总量达到返利-->
                                <div class="row option-panel @if(isset($promo) && $promo->type == cons('promo.type.goods-money')) active @endif goods-money">
                                    <div class="col-sm-6">
                                        <div class="select-commodity">本次参与促销商品 <a class="btn btn-border-blue"
                                                                                  data-target="#chooseGoods"
                                                                                  data-toggle="modal">选择商品</a></div>
                                        <div>
                                            <table class="table table-bordered table-center public-table">
                                                <thead>
                                                <tr>
                                                    <th>商品名称</th>
                                                    <th>单位</th>
                                                    <th>数量</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if(isset($promo) && $promo->type == cons('promo.type.goods-money'))
                                                    @foreach($promo->condition as $condition)
                                                        <tr>
                                                            <td>
                                                                <div>{{$condition->goods->name ?? ''}}</div>
                                                            </td>
                                                            <td>
                                                                <select name="condition[unit][]">';
                                                                    <option value="">请选择</option>
                                                                    <option @if($condition->unit == $condition->goods->goodsPieces->pieces_level_1) selected
                                                                            @endif value={{$condition->goods->goodsPieces->pieces_level_1}}>{{cons()->valueLang('goods.pieces',$condition->goods->goodsPieces->pieces_level_1)}}</option>

                                                                    @if(!is_null($condition->goods->goodsPieces->pieces_level_2))
                                                                        <option @if($condition->unit == $condition->goods->goodsPieces->pieces_level_2) selected
                                                                                @endif value={{$condition->goods->goodsPieces->pieces_level_2}}>{{cons()->valueLang('goods.pieces',$condition->goods->goodsPieces->pieces_level_2)}}</option>
                                                                    @endif
                                                                    @if(!is_null($condition->goods->goodsPieces->pieces_level_3))
                                                                        <option @if($condition->unit == $condition->goods->goodsPieces->pieces_level_3) selected
                                                                                @endif value={{$condition->goods->goodsPieces->pieces_level_3}}>{{cons()->valueLang('goods.pieces',$condition->goods->goodsPieces->pieces_level_3)}}</option>
                                                                    @endif
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="condition[quantity][]"
                                                                       class="num" placeholder="输入数量"
                                                                       value="{{$condition->quantity ?? ''}}"/>
                                                                <input type="hidden" disabled name="ids"
                                                                       value='{{$condition->goods_id}}'/>
                                                                <input type="hidden" name="condition[goods_id][]"
                                                                       value='{{$condition->goods_id}}'/>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 item-panel">
                                        任意下单总量达到<span class="fan">返</span>
                                        ￥<input type="text" name="rebate[money]" class="money"
                                                value="@if(isset($promo) && $promo->type == cons('promo.type.goods-money')) {{$promo->rebate[0]->money}} @endif"/>
                                    </div>
                                </div>
                                <!--下单总金额达到返商品-->
                                <div class="row option-panel @if(isset($promo) && $promo->type == cons('promo.type.money-goods')) active @endif money-goods">
                                    <div class="col-sm-5 item-panel">
                                        任意下单总量达到 ￥ <input type="text" name="condition[money]" class="money"
                                                          value="@if(isset($promo) && $promo->type == cons('promo.type.money-goods')) {{$promo->condition[0]->money}} @endif"/>
                                        <span class="fan pull-right">返</span>
                                    </div>

                                    <div class="col-sm-7">
                                        <div class="select-commodity">
                                            <a class="btn btn-border-blue rebate"
                                               data-target="#chooseGoods"
                                               data-toggle="modal">选择商品</a></div>
                                        <div>
                                            <table class="table table-bordered table-center public-table">
                                                <thead>
                                                <tr>
                                                    <th>商品名称</th>
                                                    <th>单位</th>
                                                    <th>数量</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if(isset($promo) && $promo->type == cons('promo.type.money-goods'))
                                                    @foreach($promo->rebate as $rebate)
                                                        <tr>
                                                            <td>
                                                                <div>{{$rebate->goods->name ?? ''}}</div>
                                                            </td>
                                                            <td>
                                                                <select name="rebate[unit][]">';
                                                                    <option value="">请选择</option>
                                                                    <option @if($rebate->unit == $rebate->goods->goodsPieces->pieces_level_1) selected
                                                                            @endif value={{$rebate->goods->goodsPieces->pieces_level_1}}>{{cons()->valueLang('goods.pieces',$rebate->goods->goodsPieces->pieces_level_1)}}</option>

                                                                    @if(!is_null($rebate->goods->goodsPieces->pieces_level_2))
                                                                        <option @if($rebate->unit == $rebate->goods->goodsPieces->pieces_level_2) selected
                                                                                @endif value={{$rebate->goods->goodsPieces->pieces_level_2}}>{{cons()->valueLang('goods.pieces',$rebate->goods->goodsPieces->pieces_level_2)}}</option>
                                                                    @endif
                                                                    @if(!is_null($rebate->goods->goodsPieces->pieces_level_3))
                                                                        <option @if($rebate->unit == $rebate->goods->goodsPieces->pieces_level_3) selected
                                                                                @endif value={{$rebate->goods->goodsPieces->pieces_level_3}}>{{cons()->valueLang('goods.pieces',$rebate->goods->goodsPieces->pieces_level_3)}}</option>
                                                                    @endif
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="rebate[quantity][]"
                                                                       class="num" placeholder="输入数量"
                                                                       value="{{$rebate->quantity ?? ''}}"/>
                                                                <input type="hidden" disabled name="ids"
                                                                       value='{{$rebate->goods_id}}'/>
                                                                <input type="hidden" name="rebate[goods_id][]"
                                                                       value='{{$rebate->goods_id}}'/>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--下单总金额达到返利-->
                                <div class="row option-panel @if(isset($promo) && $promo->type == cons('promo.type.money-money')) active @endif money-money">
                                    <div class="col-sm-12 item-panel other">
                                        任意下单总量达到 ￥ <input type="text" name="condition[money]" class="money"
                                                          value="@if(isset($promo) && $promo->type == cons('promo.type.money-money')) {{$promo->condition[0]->money}} @endif"/>
                                        <span class="fan">返</span>
                                        ￥ <input type="text" name="rebate[money]" class="money"
                                                 value="@if(isset($promo) && $promo->type == cons('promo.type.money-money')) {{$promo->rebate[0]->money}} @endif"/>
                                    </div>

                                </div>
                                <input type="hidden" name="error">
                            </div>

                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label"> 促销备注 :</label>
                            <div class="col-sm-5 ">
                                <input name="remark" class="form-control" type="text" value="{{$promo->remark ?? ''}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-10 col-sm-offset-2 operating-panel">

                                <label><input @if(request()->is('promo/*/view')) disabled @endif  @if(isset($promo) && $promo->status == cons('status.on')) checked
                                              @endif name="status" type="radio"
                                              value="{{cons('status.on')}}"/>启用</label>
                                <label><input @if(request()->is('promo/*/view')) disabled @endif  @if(isset($promo) && $promo->status == cons('status.off')) checked
                                              @endif name="status" type="radio"
                                              value="{{cons('status.off')}}"/>禁用</label>
                            </div>
                        </div>

                        <div class="form-group @if(request()->is('promo/*/view')) hidden @endif row">
                            <div class="col-sm-10 col-sm-offset-2">
                                <button type="submit" class="btn btn-blue-lighter promotion-submit">提交</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('includes.promo-add-goods-modal')
@stop
@section('js')
    @parent
    <script type="text/javascript">
        @if(request()->is('promo/*/view'))
        $('div.row').find('a.btn').attr('data-toggle','');
        $('div.row').find('input,textarea,select,button,a').attr('disabled', true).addClass('disable');
        @endif
        $("#opt-control").change(function () {
            optionClass = $(this).find("option:selected").prop('class');
            var div = $("." + optionClass);
            div.addClass("active").siblings().removeClass("active");
            $('div.promo-add').find('input,textarea,select').attr('disabled', true);
            div.find('input,textarea,select').attr('disabled', false)
        })
    </script>
@stop
