@extends('index.manage-master')
@section('subtitle' , '订单下载模版')
@include('includes.shipping-address-map')
@include('includes.templet-model')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('order-sell') }}">订单管理</a> >
                    <span class="second-level">订单打印模版</span>
                </div>
            </div>
            <div class="row print-templet margin-clear">
                <div class="col-sm-12 templet-title">
                    <span class="prompt">已选订单打印默认模板:</span>
                    <b class="print-default">{{ cons()->valueLang('order.templete', $defaultTempleteId) }}</b>
                    <a href="javascript:" class="btn btn-blue-lighter" data-target="#templetModal" data-toggle="modal"
                       data-src="{{ asset('images/order-templetes/templete_' . $defaultTempleteId . '.png') }}">预览
                    </a>
                    <a class="btn go-back" href="javascript:history.back()">返回</a>
                    <span class="prompt prompt-last">您可以在下列中选择订单打印模板，点击模板图片可预览大图</span>
                </div>
                <div class="col-sm-12 templet-list">
                    <div class="row">

                        @foreach(cons()->valueLang('order.templete') as $templeteId => $templeteName)
                            <div class="col-sm-4 item">
                                <div class="mask-panel">
                                    <img src="{{ asset('images/order-templetes/templete_' . $templeteId . '.png') }}">
                                    <a href="javascript:" class="templet-modal" data-target="#templetModal"
                                       data-toggle="modal"
                                       data-src="{{ asset('images/order-templetes/templete_' . $templeteId . '_s.png') }}">点击预览</a>
                                </div>
                                <div class="choice-item">
                                    <label>
                                        <input data-url="{{ url('api/v1/order/templete/' . $templeteId)  }}"
                                               data-method="post"
                                               class="select-templet ajax" type="radio"
                                               name="templet" {{ $templeteId == $defaultTempleteId ? 'checked' : '' }} />{{ $templeteName }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="row  margin-clear">
                <div class="col-sm-12">
                    <div class="row bank-list-wrap">
                        <div class="col-sm-12 templet-title">
                            <a class="add-bank-account btn btn-blue-lighter update-modal " href="javascript:"
                               data-toggle="modal"
                               data-target="#templeteModal">
                                <span class="fa fa-plus"></span>
                                添加店铺模版
                            </a>
                        </div>
                        @foreach($tempHeaders as $tempHeader)
                            <div class="col-sm-4 item {{ $tempHeader->is_default == 1 ? 'active' : '' }}">
                                <div class="panel">
                                    <p>{{ $tempHeader->name }}
                                        <span class="pull-left"></span>
                                        <a class="pull-right red ajax operate"
                                           data-url="{{ url('api/v1/templete/'.$tempHeader->id) }}"
                                           data-method="delete"><i class="iconfont icon-shanchu"></i>删除</a>
                                    </p>
                                    <p class="clearfix account-number">
                                        <b class="pull-left">{{ $tempHeader->contact_person . ' - ' . $tempHeader->contact_info }}</b>
                                        @if($tempHeader->is_default == 1)
                                            <span class="pull-right"><i class="iconfont icon-qiyong"></i>默认</span>
                                        @else
                                            <a class="pull-right ajax"
                                               data-url="{{ url('api/v1/templete/default/'.$tempHeader->id) }}"
                                               data-method="put">设为默认</a>
                                        @endif

                                    </p>
                                    <p class="clearfix">
                                        <span class="pull-left">{{ $tempHeader->address }}</span>
                                        <a class="pull-right edit update-modal operate" data-toggle="modal"
                                           data-target="#templeteModal" data-id="{{ $tempHeader->id }}">
                                            <i class="iconfont icon-xiugai"></i>编辑</a>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('body')
    <div class="modal fade" id="templeteModal" tabindex="-1" role="dialog" aria-labelledby="templeteModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="templeteModalLabel">
                        <span>添加店铺模版</span>
                    </div>
                </div>
                <div class="modal-body address-select">
                    <form class="form-horizontal ajax-form" action="{{ url('api/v1/templete') }}"
                          method="post" data-help-class="col-sm-push-2 col-sm-10" data-no-loading="true"
                          autocomplete="off">
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="name">店铺名:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="name" name="name" placeholder="请输入店铺名"
                                       value=""
                                       type="text">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="contact_person">联系人:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="contact_person" name="contact_person"
                                       placeholder="请输入联系人"
                                       value=""
                                       type="text">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="contact_info">联系方式:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="contact_info" name="contact_info" placeholder="请输入联系方式">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="address">地址:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="address" name="address" placeholder="请输入地址">
                            </div>
                        </div>
                        <div class="form-group row ">
                            <div class="modal-footer middle-footer">
                                <button type="submit" class="btn btn-success btn-sm btn-add" data-text="添加">添加</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @parent
@stop


@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var templeteModal = $('#templeteModal'), form = templeteModal.find('form');
            templeteModal.on('show.bs.modal', function (e) {
                var obj = $(e.relatedTarget);
                $('#templeteModal span').html(obj.data('id') ? '编辑店铺模版' : '添加店铺模版');
                $('.btn-add').html(obj.data('id') ? '提交' : '添加');
                var id = obj.data('id') || 0,
                    name = $('input[name="name"]'),
                    contactPerson = $('input[name="contact_person"]'),
                    contactInfo = $('input[name="contact_info"]'),
                    address = $('input[name="address"]');

                if (id) {
                    $.ajax({
                        url: site.api('templete/' + id),
                        method: 'get'
                    }).done(function (data) {
                        var templete = data.templete;
                        name.val(templete.name);
                        contactPerson.val(templete.contact_person);
                        contactInfo.val(templete.contact_info);
                        address.val(templete.address);
                    });
                }
                form.attr('action', site.api(id ? 'templete/' + id : 'templete'));
                form.attr('method', id ? 'put' : 'post');
            }).on('hide.bs.modal', function (e) {
                form.formValidate('reset');
                form.trigger('reset');
            })
        });
    </script>
@stop