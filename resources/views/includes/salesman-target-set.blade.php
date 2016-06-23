@section('body')
    @parent
    <div class="modal fade in" id="salesmanTargetSet" tabindex="-1" role="dialog" aria-labelledby="salesmanTargetSetModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="form-horizontal ajax-form" action="{{ url('api/v1/business/salesman/target-set') }}"
                      method="post"
                      data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
                    <input type="hidden" name="_method" value="put">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="cropperModalLabel">业务员目标设置<span class="extra-text"></span></h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="price">月份:</label>

                            <div class="col-sm-10 col-md-5">
                                <input type="text" name="date" class="form-control datetimepicker" value="{{ $date }}" data-format="YYYY-MM"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="salesman_id">业务员:</label>

                            <div class="col-sm-10 col-md-5">
                                <select name="salesman_id" class="inline-control">
                                    <option value="">请选择业务员</option>
                                    @foreach($salesmen as $salesman)
                                        <option value="{{ $salesman->id }}">
                                            {{ $salesman->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label " for="amount">目标:</label>

                            <div class="col-sm-10 col-md-5">
                                <input type="text" name="target" class="form-control" placeholder="请输入业务员目标"/>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary btn-sm btn-add" data-text="确定">
                            确定
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop