<div class="modal modal1 fade" id="add-modify" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" style="width:800px;">
        <div class="modal-content" style="width:800px;margin:auto">
            <div class="modal-header choice-header prop-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                <div class="modal-title forgot-modal-title" id="myModalLabel">
                    <span>添加资产</span>
                </div>
            </div>
            <div class="modal-body">
                <div class="row assets-modal-wrap">
                    <form class="ajax-form form-horizontal add-modify" method="post" accept-charset="UTF-8">
                        <div class="form-group">

                            <label class="col-xs-2 control-label"><span class="red">*</span> 资产名称:</label>
                            <div class="col-xs-7 col-md-5">
                                <input name="id" class="form-control" type="hidden">
                                <input name="name" class="form-control" type="text">
                            </div>
                            <div class="col-sm-3 prompt">
                                例如：海尔冰柜 NJ-58631
                            </div>
                        </div>
                        <div class="form-group">

                            <label class="col-xs-2 control-label"><span class="red">*</span> 数量:</label>
                            <div class="col-xs-2 col-md-2">
                                <input name="quantity" class="form-control" type="text">
                            </div>

                            <label class="col-xs-2 control-label"><span class="red">*</span> 单位:</label>
                            <div class="col-xs-2 col-md-2">
                                <input name="unit" class="form-control" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">申请条件:</label>
                            <div class="col-xs-7 col-md-6">
                                <textarea name="condition" maxlength="100" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">资产备注:</label>
                            <div class="col-xs-7 col-md-6">
                                <textarea name="remark" maxlength="100" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-8 col-md-6 col-xs-offset-2 operating-panel">
                                <label><input type="radio" name="status" value="0"/>禁用</label>
                                <label><input type="radio" checked name="status" value="1"/>启用</label>
                                <p class="tip hidden">(温馨提示:资产禁用后才能修改)</p>
                            </div>

                        </div>
                        <div class="form-group">
                            <div class="col-xs-8 col-xs-offset-2">
                                <button type="submit" class="btn btn-blue-lighter btn-submit">提交资产
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal modal1 fade" id="view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" style="width:800px;">
        <div class="modal-content" style="width:800px;margin:auto">
            <div class="modal-header choice-header prop-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                <div class="modal-title forgot-modal-title" id="myModalLabel">
                    <span>查看</span>
                </div>
            </div>
            <div class="modal-body">
                <div class="row modal-list-wrap">
                    <div class="col-sm-7 item">
                        <label>资产名称 : </label>
                        <div name="asset_name" class="content"></div>
                    </div>
                    <div class="col-sm-5 item">
                        <label> 添加时间 : </label>
                        <div name='asset_created_at' class="content"></div>
                    </div>
                    <div class="col-sm-12 item">
                        <label>申请条件 : </label>
                        <div name="asset_condition" class="content"> </div>
                    </div>
                    <div class="col-sm-12 item">
                        <label> 资产备注 : </label>
                        <div name="asset_remark" class="content"></div>
                    </div>
                </div>
                <div class="row modal-list-wrap bg-gray">
                    <div class="col-sm-12 item title">
                        客户信息
                    </div>
                    <div class="col-sm-4 item">
                        <label>使用客户名称 : </label>
                        <div name="client_name" class="content"></div>
                    </div>
                    <div class="col-sm-4 item">
                        <label> 联系人 : </label>
                        <div name="client_contact_person" class="content"></div>
                    </div>
                    <div class="col-sm-4 item">
                        <label>联系方式 : </label>
                        <div name="client_contact_info" class="content"></div>
                    </div>
                    <div class="col-sm-12 item">
                        <label> 营业地址 : </label>
                        <div name='client_shopaddress' class="content"></div>
                    </div>
                    <div class="col-sm-12 item">
                        <label> 开始使用时间 : </label>
                        <div name="use_date" class="content"></div>
                    </div>
                </div>
                <div class="row modal-list-wrap">
                    <div class="col-sm-7 item">
                        <label>业务员 : </label>
                        <div name="salesman_name" class="content"></div>
                    </div>
                    <div class="col-sm-5 item">
                        <label> 申请时间 : </label>
                        <div name="apply_date" class="content"></div>
                    </div>
                    <div class="col-sm-12 item">
                        <label>审核通过时间 : </label>
                        <div name="pass_date" class="content"></div>
                    </div>
                    <div class="col-sm-12 item">
                        <label> 申请备注 : </label>
                        <div name='apply_remark' class="content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var add_modify = $('div#add-modify'),
                    form = $('form.add-modify');
            add_modify.on('shown.bs.modal', function (parent) {
                var obj = $(parent.relatedTarget);
                form.find('input,textarea').removeClass('white-bg');
                action = '添加资产';
                if (obj.hasClass('view')) {
                    action = '查看资产';
                    form.find('input,textarea').prop('disabled', true).addClass('white-bg');
                    $('p.tip').removeClass('hidden');
                    form.find('.btn-submit').addClass('hidden');
                } else {
                    form.find('input,textarea').removeAttr("disabled");
                    $('p.tip').addClass('hidden');
                    form.find('.btn-submit').removeClass('hidden');
                    if (obj.hasClass('edit')) {
                        action = '修改资产';
                    }
                }
                $(this).find('#myModalLabel>span').html(action);

                form.attr('action', site.api(obj.hasClass('edit') ? 'asset/modify' : 'asset/add'));

                var data = obj.parents('tr').find('input[name=data]');
                form.find('input[name = id]').val(data.data('id'));
                form.find('input[name = name]').val(data.data('name'));
                form.find('input[name = quantity]').val(data.data('quantity'));
                form.find('input[name = unit]').val(data.data('unit'));
                form.find('textarea[name = condition]').val(data.data('condition'));
                form.find('textarea[name = remark]').val(data.data('remark'));
                form.find('input[name = status]:eq(' + data.data('status') + ')').prop('checked', true);
            }).on('hidden.bs.modal', function () {
                $('input').css('border-color','#ccc');
                $('p.ajax-error').remove();
            });

            var viewModal = $('#view');
            viewModal.on('shown.bs.modal', function (e) {
                var obj = $(e.relatedTarget),
                        asset_name = obj.data('asset_name') || '',
                        asset_condition = obj.data('asset_condition') || '',
                        asset_remark = obj.data('asset_remark') || '',
                        asset_created_at = obj.data('asset_created_at') || '',
                        client_name = obj.data('client_name') || '',
                        client_contact_person = obj.data('client_contact_person') || '',
                        client_contact_info = obj.data('client_contact_info') || '',
                        client_shopaddress_address_name = obj.data('client_shopaddress_address_name') || '',
                        use_date = obj.data('use_date') || '',
                        salesman_name = obj.data('salesman_name') || '',
                        created_at = obj.data('created_at') || '',
                        pass_date = obj.data('pass_date') || '',
                        apply_remark = obj.data('apply_remark') || '';
                viewModal.find('div[name = asset_name]').html(asset_name)
                viewModal.find('div[name = asset_condition]').html(asset_condition)
                viewModal.find('div[name = asset_remark]').html(asset_remark)
                viewModal.find('div[name = asset_created_at]').html(asset_created_at)
                viewModal.find('div[name = client_name]').html(client_name)
                viewModal.find('div[name = client_contact_person]').html(client_contact_person)
                viewModal.find('div[name = client_contact_info]').html(client_contact_info)
                viewModal.find('div[name = client_shopaddress]').html(client_shopaddress_address_name);
                viewModal.find('div[name = use_date]').html(use_date)
                viewModal.find('div[name = salesman_name]').html(salesman_name)
                viewModal.find('div[name = apply_date]').html(created_at)
                viewModal.find('div[name = pass_date]').html(pass_date)
                viewModal.find('div[name = apply_remark]').html(apply_remark)
            });
        });
    </script>
@stop