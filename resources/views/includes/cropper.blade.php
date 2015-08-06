@include('includes.uploader')

@section('css')
    @parent
    <link href="{{ asset('js/lib/jquery/cropper/cropper.min.css') }}" rel="stylesheet">
@stop

@section('body')
    <div class="modal fade" id="cropperModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="cropperModalLabel">上传图片并进行裁剪<span class="extra-text"></span></h4>
                </div>

                <div class="modal-body">
                    <div class="progress collapse">
                        <div class="progress-bar progress-bar-striped active"></div>
                    </div>
                    <span class="btn btn-primary fileinput-button">
                        请选择图片文件
                        <input type="file" name="file" data-url="{{ url('api/v2/file/upload-temp') }}" accept="image/*">
                    </span>

                    <div id="cropper-container">
                        <img class="img-responsive" src="">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary btn-sm btn-crop" data-error-text="裁剪失败">裁剪并提交
                    </button>
                </div>
            </div>
        </div>
    </div>
    @parent
@stop

@section('js-lib')
    @parent
    <script src="{{ asset('js/lib/jquery/cropper/cropper.min.js') }}"></script>
@stop

@section('js')
    @parent
    <script>
        $(function () {
            var cropper = $('#cropperModal')
                    , cropperTitleExtra = cropper.find('#cropperModalLabel > .extra-text')
                    , cropperImage = cropper.find('#cropper-container > img')
                    , cropperUploadButton = cropper.find('.fileinput-button > input[type="file"]')
                    , cropButton = cropper.find('.btn-crop')
                    , cropWidth = 0
                    , cropHeight = 0
                    , cropperParent = null;

            cropper.on('show.bs.modal', function (e) {
                cropperParent = $(e.relatedTarget);

                cropWidth = cropperParent.data('width');
                cropHeight = cropperParent.data('height');
                // 添加附加信息
                cropperTitleExtra.html(cropWidth && cropHeight ? '（' + cropWidth + 'x' + cropHeight + '）' : '');

                // 初始化裁剪
                cropperImage.cropper({
                    aspectRatio: cropWidth && cropHeight ? cropWidth / cropHeight : NaN,
                    autoCropArea: 1,
                    dragCrop: true,
                    zoomable: false
                });
            }).on('hidden.bs.modal', function () {
                cropperImage.cropper('destroy').removeAttr('src');
                cropButton.button('reset');

                cropperParent = null;
            });


            // 开始提交裁剪参数
            cropButton.on('click', '', function () {
                var fileData = cropperUploadButton.data('data');
                var data = $.extend(cropperImage.cropper('getData'), {
                    org_name: fileData.org_name,
                    path: fileData.path,
                    destWidth: cropWidth,
                    destHeight: cropHeight
                });

                cropButton.button('loading');
                $.ajax({
                    url: site.api('file/image-crop'),
                    method: 'POST',
                    dataType: 'json',
                    data: data
                }).done(function (data, textStatus, jqXHR) {
                    if (cropperParent) {
                        cropperParent.trigger('cropped.hct.cropper', [data]);
                    }
                    cropper.modal('hide');
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    cropButton.button('error');
                });
            });

            // 文件上传替换
            cropperUploadButton.on('fileuploaddone', '', function (e, data) {
                var result = data.result;

                // 设置返回信息
                cropperUploadButton.data('data', result);
                cropperImage.attr('src', result.url).cropper('replace', result.url);
            });
        });
    </script>
@stop