@extends('index.manage-master')
@section('subtitle', '资产管理')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('asset/unused') }}">资产管理</a> >
                    <a href="{{ url('asset/review') }}">资产申请审核</a> >
                    <span class="second-level">查看详情</span>
                </div>
            </div>
            <div class="row order-detail business-detail">
                <div class="col-sm-12 go-history">
                    <a class="go-back btn btn-border-blue" href="javascript:history.back()"><i
                                class="iconfont icon-fanhui"></i>
                        返回</a>
                    @if(!$assetApply->status >= cons('asset_apply.status.approved'))
                        <a data-url="{{ url('api/v1/asset/apply/review/'.$assetApply->id) }}"
                           data-method="put"
                           data-data='{"status" : "{{cons('asset_apply.status.approved')}}"}'
                           class="ajax btn btn-blue-lighter">
                            通过
                        </a>
                    @endif
                </div>
                <div class="col-sm-12">
                    <div class="row order-receipt">
                        <div class="col-sm-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">资产申请信息</h3>
                                </div>
                                <div class="panel-container table-responsive">
                                    <table class="table table-bordered table-center public-table ">
                                        <thead>
                                        <tr>
                                            <td>申请编号</td>
                                            <td>资产名称</td>
                                            <td>申请条件</td>
                                            <td>业务员</td>
                                            <td>
                                                资产备注
                                            </td>
                                            <td>
                                                申请数量
                                                @if(!$assetApply->status >= cons('asset_apply.status.approved'))
                                                    <a class="edit display-fee-quantity"
                                                       onclick="editText('display-fee-quantity')"><i
                                                                class="iconfont icon-xiugai"></i> 编辑</a>
                                                @endif
                                            </td>
                                            <td>
                                                申请备注
                                                @if(!$assetApply->status >= cons('asset_apply.status.approved'))
                                                    <a class="edit display-fee-notes"
                                                       onclick="editText('display-fee-notes')"><i
                                                                class="iconfont icon-xiugai"></i> 编辑</a>
                                                @endif
                                            </td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>{{$assetApply->id ?? ''}}</td>
                                            <td>{{$assetApply->asset->name ?? ''}}</td>
                                            <td width="20%">{{$assetApply->asset->condition ?? ''}}</td>
                                            <td>{{$assetApply->salesman->name ?? ''}}</td>
                                            <td width="20%">
                                                {{$assetApply->asset->remark ?? ''}}
                                            </td>
                                            <td width="20%">
                                                <div class="commodity-num"
                                                     id="display-fee-quantity">{{$assetApply->quantity ?? ''}}</div>

                                                <div class="enter-num-panel pull-left">
                                                    <input data-id="{{$assetApply->id}}"
                                                           data-name="quantity"
                                                           class="edit-text" autofocus
                                                           value="{{$assetApply->quantity ?? ''}}"/>
                                                </div>
                                                <span class="pull-right">{{$assetApply->asset->unit}}</span>
                                            </td>
                                            <td width="20%">
                                                <div id="display-fee-notes">{{$assetApply->apply_remark ?? ''}}</div>
                                                <div class="enter-num-panel ">
                                            <textarea class="edit-text" autofocus maxlength="50"
                                                      data-id="{{$assetApply->id}}"
                                                      data-name="apply_remark">{{ $assetApply->apply_remark ?? '' }}</textarea>
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
                                <div class="panel-container">
                                    <table class="table table-bordered table-center public-table">
                                        <thead>
                                        <tr>
                                            <th>客户名称</th>
                                            <th>联系人</th>
                                            <th>联系电话</th>
                                            <th>营业地址</th>
                                            <th>
                                                开始使用时间
                                                @if(!$assetApply->status >= cons('asset_apply.status.approved'))
                                                    <a class="edit display-use-date"
                                                       onclick="editText('display-use-date')"><i
                                                                class="iconfont icon-xiugai use-date"></i> 编辑</a>
                                                @endif
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>{{$assetApply->client->name ?? ''}}</td>
                                            <td>{{$assetApply->client->contact ?? ''}}</td>
                                            <td>{{$assetApply->client->contact_information ?? ''}}</td>
                                            <td>
                                                <p>{{$assetApply->client->business_address_name}}</p>
                                                <p class="prop-item">
                                                    <a href="javascript:" data-target="#shopAddressMapModal"
                                                       data-toggle="modal"
                                                       data-x-lng="{{ isset($assetApply->client)? $assetApply->client->business_address_lng : 0 }}"
                                                       data-y-lat="{{ isset($assetApply->client)? $assetApply->client->business_address_lat : 0 }}"
                                                       data-address="{{ isset($assetApply->client) ? $assetApply->client->business_address_name : '' }}"
                                                       data-contact_person="{{ $assetApply->client->contact}}"
                                                       data-phone= {{$assetApply->client->contact_information ?? ''}}>
                                                        <i class="iconfont icon-chakanditu"></i> 查看地图
                                                    </a>
                                                </p>
                                            </td>
                                            <td width="20%">
                                                <div id="display-use-date">{{$assetApply->use_date ?? '---'}}</div>
                                                <div class="enter-num-panel ">
                                                    <input data-format="YYYY-MM-DD" placeholder="2000-10-10"
                                                           class="edit-text datetimepicker" autofocus
                                                           maxlength="50" data-id="{{$assetApply->id}}"
                                                           data-name="use_date"
                                                           value="{{ $assetApply->use_date ?? '' }}">
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">申请记录</h3>
                                </div>
                                <div class="panel-container table-responsive">
                                    <table class="table table-bordered table-center">
                                        <thead>
                                        <tr>
                                            <th>申请操作</th>
                                            <th>操作时间</th>
                                            <th>操作人</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(isset($assetApply->log))
                                            @foreach($assetApply->log as $value)
                                                <tr>
                                                    <td>{{cons()->valueLang('asset_apply_log.action',$value->action)}}</td>
                                                    <td>{{$value->created_at ?? ''}}</td>
                                                    <td>{{$value->opera->name ?? ''}} @if($value->opera_type == 'App\Models\Salesman')
                                                            （业务员）@endif</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('includes.shop-address-map-modal')
    @include('includes.asset-modal')
    @include('includes.timepicker')
@stop
@section('js')
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
                        assetId = "{{$assetApply->id}}" ,
                        data = {};
                if (oldValue != newValue) {

                    var load = '<div class="loading"> <img src="' + site.url("images/new-loading.gif") + '" /> </div>';
                    $('body').append(load);
                    data[name] = newValue;
                    self.html('<i class="fa fa-spinner fa-pulse"></i> 操作中');
                    $.ajax({
                        url: site.api('asset/apply/modify/' + assetId),
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
