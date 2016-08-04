@section('body')
    <div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="form-horizontal ">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="cropperModalLabel">选择要添加的配送区域<span class="extra-text"></span></h4>
                    </div>
                    <div class="modal-body address-select">
                        <div class="address-group">
                            <div class="form-group row">
                                <label class="col-sm-2 control-label" for="num">配送区域:</label>
                                <div class="col-sm-10">
                                    <select class="address-province inline-control add-province">
                                        <option selected="selected" value="">请选择省市/其他...</option>
                                    </select>

                                    <select class="address-city inline-control add-city">
                                        <option selected="selected" value="">请选择城市...</option>
                                    </select>

                                    <select class="address-district inline-control add-district">
                                        <option selected="selected" value="">请选择区/县...</option>
                                    </select>

                                    <select class="address-street inline-control add-street useless-control">
                                        <option selected="selected" value="">请选择街道...</option>
                                    </select>
                                </div>
                            </div>

                            @if(isset($model) && $model == 'shop')
                                <div class="form-group row">
                                    <label class="col-sm-2 control-label" for="num">最低配送额:</label>

                                    <div class="col-sm-10 col-md-5">
                                        <input type="text" name="min_money" class="form-control min-money"
                                               placeholder="请输入最低配送额"/>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group row address-detail">
                                <label class="col-sm-2 control-label" for="num">备注:</label>

                                <div class="col-sm-10 col-md-5">
                                    <input type="text" name="num" class="form-control detail-address"
                                           placeholder="请输入备注"/>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm btn-add  pull-right" data-text="保存">保存
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @parent
@stop
@section('js-lib')
    @parent
    <script type="text/javascript" src="{{ asset('js/address.js') }}"></script>
@stop
@section('js')
    @parent
    <script>
        $(function () {
            addAddFunc();
        });
    </script>
@stop