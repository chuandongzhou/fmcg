@extends('index.manage-master')
@section('subtitle', '申请记录')
@section('container')
    @include('includes.shop-address-map-modal')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('promo/setting') }}">促销管理</a> >
                    <span class="second-level">促销申请详情</span>
                </div>
            </div>
            <div class="row order-detail business-detail">
                <div class="col-sm-12 go-history">
                    <a class="go-back btn btn-border-blue" href="javascript:history.back()"><i
                                class="iconfont icon-fanhui"></i> 返回</a>
                    @if($apply->status < cons('promo.review_status.pass'))
                        <a class="btn btn-blue-lighter"> 通过</a>
                    @endif
                </div>
                <div class="col-sm-12">
                    <div class="row order-receipt">
                        <div class="col-sm-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">促销申请信息</h3>
                                </div>
                                <div class="panel-container table-responsive">
                                    <table class="table table-bordered table-center public-table ">
                                        <thead>
                                        <tr>
                                            <td>促销申请编号</td>
                                            <td>促销名称</td>
                                            <td>有效时间</td>
                                            <td>业务员</td>
                                            <td>
                                                申请时间
                                            </td>
                                            <td>
                                                申请备注
                                                @if($apply->status < cons('promo.review_status.pass'))
                                                    <a class="edit display-fee-notes"
                                                       onclick="editText('display-fee-notes')"><i
                                                                class="iconfont icon-xiugai"></i> 编辑</a>
                                                @endif
                                            </td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>{{$apply->id}}</td>
                                            <td>{{$apply->promo->name ?? ''}}</td>
                                            <td>{{$apply->promo->start_at ?? ''}} 至 {{$apply->promo->end_at}}</td>
                                            <td>{{$apply->salesman->name ?? ''}}</td>
                                            <td>{{$apply->created_at}}</td>
                                            <td width="20%">
                                                <div id="display-fee-notes">{{$apply->apply_remark ?? ''}}</div>
                                                <div class="enter-num-panel ">
                                            <textarea class="edit-text" autofocus maxlength="50"
                                                      data-id="{{$apply->id}}"
                                                      data-name="apply_remark">{{ $apply->apply_remark ?? '' }}</textarea>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">客户信息</h3>
                                </div>
                                <div class="panel-container table-responsive">
                                    <table class="table table-bordered table-center public-table">
                                        <thead>
                                        <tr>
                                            <th>使用客户名称</th>
                                            <th>联系人</th>
                                            <th>联系电话</th>
                                            <th>营业地址</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>{{$apply->client->name ?? ''}}</td>
                                            <td>{{$apply->client->contact_person ?? ''}}</td>
                                            <td>{{$apply->client->contact_info ?? ''}}</td>
                                            <td>
                                                <p>{{$apply->client->shopAddress->area_name ?? ''}}</p>
                                                <p class="prop-item">
                                                    <a href="javascript:" data-target="#shopAddressMapModal"
                                                       data-toggle="modal"
                                                       data-x-lng="{{ isset($apply->client->shopAddress)? $apply->client->x_lng : 0 }}"
                                                       data-y-lat="{{ isset($apply->client->shopAddress)? $apply->client->y_lat : 0 }}"
                                                       data-address="{{ isset($apply->client->shopAddress) ? $apply->client->shopAddress->address_name : '' }}"
                                                       data-contact_person="{{ $apply->client->contact_person }}"
                                                       data-phone= {{$apply->client->contact_info ?? ''}}>
                                                        <i class="iconfont icon-chakanditu"></i> 查看地图
                                                    </a>
                                                </p>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">促销内容</h3>
                                </div>
                                <div class="panel-container table-responsive promotion-msg-wrap">
                                    @if($apply->promo->type == cons('promo.type.custom'))
                                        <div class="row custom">
                                            <div class="col-sm-5 ">
                                                {{$apply->promo->condition[0]->custom}}
                                            </div>
                                            <div class="col-sm-2 item-text  padding-clear">
                                                <span class="fan">返</span>
                                            </div>
                                            <div class="col-sm-5  ">
                                             <textarea name="rebate[custom]" placeholder="填写内容" rows="5"
                                                       cols="30">{{$apply->promo->rebate[0]->custom}}</textarea>
                                            </div>
                                        </div>
                                    @elseif($apply->promo->type == cons('promo.type.goods-goods'))
                                        <div class="row">
                                            <div class="col-sm-5">
                                                <table class="table table-bordered table-center public-table">
                                                    <thead>
                                                    <tr>
                                                        <th>商品名称</th>
                                                        <th>单位</th>
                                                        <th>数量</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($apply->promo->condition as $condition)
                                                        <tr>
                                                            <td>
                                                                <div>{{$condition->goods->name ?? ''}}</div>
                                                            </td>
                                                            <td>
                                                                <div>{{$condition->quantity}}</div>
                                                            </td>
                                                            <td>
                                                                <div>{{cons()->valueLang('goods.pieces',$condition->unit)}}</div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-sm-2 padding-clear item-txt prompt">
                                                任意下单总量达到 <span class="fan">返</span>
                                            </div>
                                            <div class="col-sm-5">
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
                                                        @foreach($apply->promo->rebate as $rebate)
                                                            <tr>
                                                                <td>
                                                                    <div>{{$rebate->goods->name ?? ''}}</div>
                                                                </td>
                                                                <td>
                                                                    <div>{{$rebate->quantity}}</div>
                                                                </td>
                                                                <td>
                                                                    <div>{{cons()->valueLang('goods.pieces',$rebate->unit)}}</div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($apply->promo->type == cons('promo.type.goods-money'))
                                        <div class="row ">
                                            <div class="col-sm-6">
                                                <table class="table table-bordered table-center public-table">
                                                    <thead>
                                                    <tr>
                                                        <th>商品名称</th>
                                                        <th>单位</th>
                                                        <th>数量</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($apply->promo->condition as $condition)
                                                        <tr>
                                                            <td>
                                                                <div>{{$condition->goods->name ?? ''}}</div>
                                                            </td>
                                                            <td>
                                                                <div>{{$condition->quantity}}</div>
                                                            </td>
                                                            <td>
                                                                <div>{{cons()->valueLang('goods.pieces',$condition->unit)}}</div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-sm-6 item-text">
                                                任意下单总量达到<span class="fan">返</span>
                                                ￥
                                                <span>{{$apply->promo->rebate[0]->money}}</span>
                                            </div>
                                        </div>
                                    @elseif($apply->promo->type == cons('promo.type.money-goods'))
                                        <div class="row  money-goods">
                                            <div class="col-sm-5 item-text">
                                                任意下单总量达到 ￥ <span>{{$apply->promo->condition[0]->money}}</span>
                                                <span class="fan pull-right">返</span>
                                            </div>
                                            <div class="col-sm-7">
                                                <table class="table table-bordered table-center public-table">
                                                    <thead>
                                                    <tr>
                                                        <th>商品名称</th>
                                                        <th>单位</th>
                                                        <th>数量</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($apply->promo->rebate as $rebate)
                                                        <tr>
                                                            <td>
                                                                <div>{{$rebate->goods->name ?? ''}}</div>
                                                            </td>
                                                            <td>
                                                                <div>{{$rebate->quantity}}</div>
                                                            </td>
                                                            <td>
                                                                <div>{{cons()->valueLang('goods.pieces',$rebate->unit)}}</div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @elseif($apply->promo->type == cons('promo.type.money-money'))
                                        <div class="row money-money">
                                            <div class="col-sm-12 item-text other">
                                                任意下单总量达到 ￥ <span>{{$apply->promo->condition[0]->money}}</span>
                                                <span class="fan">返</span>
                                                ￥ <span>{{$apply->promo->rebate[0]->money}}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        function editText(id) {
            var content = $("#" + id), self = $("." + id);
            if (content.is(":visible")) {
                $("." + id).html("<i class='iconfont icon-baocun'></i> 保存");
                content.hide().siblings(".enter-num-panel").show();
            } else {
                var oldValueControl = content,
                        oldValue = oldValueControl.html();
                newValueControl = content.siblings('.enter-num-panel').children('.edit-text'),
                        newValue = newValueControl.val(),
                        name = newValueControl.data('name'),
                        assetId = "{{$apply->id}}" ,
                        data = {};
                if (oldValue != newValue) {

                    var load = '<div class="loading"> <img src="' + site.url("images/new-loading.gif") + '" /> </div>';
                    $('body').append(load);
                    data[name] = newValue;
                    self.html('<i class="fa fa-spinner fa-pulse"></i> 操作中');
                    $.ajax({
                        url: site.api('promo/apply/modify/' + assetId),
                        method: 'put',
                        data: data
                    }).done(function (data, textStatus, jqXHR) {
                        self.html("<i class='iconfont icon-xiugai'></i> 编辑");
                        content.html(newValue);
                        content.next('.enter-num-panel').find('input').val(newValue);
                        content.show().siblings(".enter-num-panel").hide();
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        if (errorThrown == 'Unauthorized') {
                            site.redirect('auth/login');
                        } else {
                            tips(self, apiv1FirstError(jqXHR['responseJSON'], '操作失败'));
                            content.html(oldValue);
                            content.next('.enter-num-panel').find('input').val(oldValue);
                            content.show().siblings(".enter-num-panel").hide();
                            self.html("<i class='iconfont icon-xiugai'></i> 编辑");
                        }
                    }).always(function () {
                        self.parent().children('.prompt').remove();
                        $('body').find('.loading').remove();
                    });

                } else {
                    self.html("<i class='iconfont icon-xiugai'></i> 编辑");
                    content.show().siblings(".enter-num-panel").hide();
                    self.parent().children('.prompt').remove();
                    $('body').find('.loading').remove();
                }
            }
        }
    </script>
@stop
