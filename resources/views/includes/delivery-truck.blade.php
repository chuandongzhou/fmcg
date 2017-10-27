@section('body')
    <div class="modal fade" id="deliveryTruckModal" tabindex="-1" role="dialog" aria-labelledby="deliveryTruckModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="form-horizontal ajax-form" action="{{ url('api/v1/personal/delivery-truck') }}"
                      method="post" data-help-class="col-sm-push-2 col-sm-10" data-no-loading="true" autocomplete="off">
                    <div class="modal-header choice-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                        <div class="modal-title forgot-modal-title" id="deliveryModalLabel">
                            <span>添加配送车辆</span>
                        </div>
                    </div>
                    <div class="modal-body address-select">

                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="name">车辆名称:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="name" name="name" placeholder="请输入车辆名称"
                                       value=""
                                       type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="license_plate">车牌号码:</label>

                            <div class="col-sm-10 col-md-6">
                                <input class="form-control" id="license_plate" name="license_plate" placeholder="请输入车牌号码"
                                       value=""
                                       type="text">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer middle-footer">
                        <button type="submit" class="btn btn-success btn-sm btn-add pull-right" data-text="提交">提交
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @parent
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var deliveryTruckModal = $('#deliveryTruckModal'),
                form = deliveryTruckModal.find('form');
            deliveryTruckModal.on('shown.bs.modal', function (e) {
                var obj = $(e.relatedTarget);
                $('#deliveryTruckModalLabel span').html(obj.hasClass('add') ? '添加配送车辆' : '编辑配送车辆');
                var id = obj.data('id') || '',
                    name = obj.data('name') || '',
                    licensePlate = obj.data('license') || '';

                $('input[name="name"]').val(name);
                $('input[name="license_plate"]').val(licensePlate);
                form.attr('action', site.api(obj.hasClass('add') ? 'personal/delivery-truck' : 'personal/delivery-truck/' + id));
                form.attr('method', obj.hasClass('add') ? 'post' : 'put');

            }).on('hide.bs.modal', function (e) {
                form.formValidate('reset');
            });

        });
    </script>
@stop
