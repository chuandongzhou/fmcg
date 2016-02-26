@section('css')
    @parent
    <style type="text/css">
        .modal .modal .address-select {
            width: 30%;
            display: inline;
            margin-bottom: 5px;
        }

        .modal .modal-body .address-detail {
            margin: 10px 0;
        }

        .modal .address-select .address {
            width: 90%;
            display: inline;
            margin-bottom: 5px;
        }
    </style>
@stop

@section('body')
    <div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="cropperModalLabel">选择要添加的地址<span class="extra-text"></span></h4>
                </div>
                <div class="modal-body address-select">
                    <div>

                        <label class="control-label">&nbsp;&nbsp;&nbsp;配送区域 : </label>
                        <select class="address-province inline-control add-province">
                            <option selected="selected" value="">请选择省市/其他...</option>
                        </select>

                        <select class="address-city inline-control add-city">
                            <option selected="selected" value="">请选择城市...</option>
                        </select>

                        <select class="address-district inline-control add-district">
                            <option selected="selected" value="">请选择区/县...</option>
                        </select>

                        <select class="address-street inline-control add-street">
                            <option selected="selected" value="">请选择街道...</option>
                        </select>
                        <button type="button" class="btn btn-primary btn-sm btn-add  pull-right" data-text="添加">添加</button>

                        <div class="address-detail">
                            <label class="control-label">&nbsp;&nbsp;&nbsp;详细区域 : </label>
                            <input type="text" placeholder="请输入详细区域" class="form-control detail-address">
                        </div>

                    </div>
                    {{--区域经纬度--}}
                    <input type="hidden" name="coordinate_blx" value=""/>
                    <input type="hidden" name="coordinate_bly" value=""/>
                    <input type="hidden" name="coordinate_slx" value=""/>
                    <input type="hidden" name="coordinate_sly" value=""/>
                    {{--<div class="modal-footer">--}}
                        {{--<button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">关闭</button>--}}
                        {{--<button type="button" class="btn btn-primary btn-sm btn-add" data-text="添加">添加</button>--}}
                    {{--</div>--}}
                    <div class="modal-footer">
                        <button class="btn btn-primary btn-sm " onclick="polygon_modal.enableEditing();">开启编辑功能</button>
                        <button class="btn btn-primary btn-sm " onclick="polygon_modal.disableEditing();">关闭编辑功能</button>
                    </div>
                    <div id="map-modal"></div>

                </div>
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
            baiDuMap();
        });
    </script>
@stop