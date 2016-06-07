@include('includes.uploader')
@include('includes.timepicker')

@section('body')
    <div class="modal fade" id="shopAdvertModal" tabindex="-1" role="dialog" aria-labelledby="shopAdvertModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="form-horizontal ajax-form" method="post"
                      action="{{ url('api/v1/personal/model/advert') }}"
                      data-help-class="col-sm-push-2 col-sm-10"
                      data-done-then="refresh" autocomplete="off">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="cropperModalLabel">添加店铺广告<span class="extra-text"></span></h4>
                    </div>
                    <div class="modal-body address-select">

                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">广告名称</label>

                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="name" name="name" placeholder="请输入广告名">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="upload-file" class="col-sm-2 control-label">广告图片</label>

                            <div class="col-sm-4">
                <span data-name="image" class="btn btn-primary btn-sm fileinput-button">
                                请选择图片文件
                                <input type="file" accept="image/*" data-url="{{ url('api/v1/file/upload-temp') }}"
                                       name="file">
                            </span>

                                <div class="image-preview w160">
                                    <img src="" class="img-thumbnail">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="url" class="col-sm-2 control-label">商品id</label>

                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="goods_id" name="goods_id"
                                       placeholder="请输入商品id">
                            </div>
                        </div>

                        <div class="form-group" id="date-time">
                            <label class="col-sm-2 control-label">起止时间</label>

                            <div class="col-sm-3 time-limit">
                                <input type="text" class="form-control datetimepicker" name="start_at"
                                       placeholder="起始时间"/>
                            </div>

                            <div class="col-sm-3 time-limit">
                                <input type="text" class="form-control datetimepicker" name="end_at" placeholder="结束时间"/>
                            </div>
                            <div class="col-sm-push-2 col-sm-10">
                                <p class="help-block">结束时间为空时，表示广告永久有效。</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-bg btn-success"> 添加</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @parent
@stop
