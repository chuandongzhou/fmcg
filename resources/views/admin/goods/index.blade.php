@extends('admin.master')
@section('subtitle' , '批量导入商品')
@include('includes.uploader')
@section('css')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <link href="{{ asset('css/index.css?v=1.0.0') }}" rel="stylesheet">
    <script src="{{ asset('js/index.js')  }}" ></script>

@stop
@include('includes.uploader')
@section('right-container')
    <div class="col-sm-12 goods-editor">
        <form class="form-horizontal ajax-form" method="post" action="" autocomplete="off">
            <div class="row editor-panel content-wrap">
                <div class="col-sm-12 editor-wrap">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">店铺ID:</label>
                        <div class="col-xs-4 padding-clear">
                            <input type="text" id="shopId" name="shopId" class="form-control" placeholder="店铺ID"  />
                        </div>
                    </div>
                    <div class="form-group">

                        <label class="col-sm-2 control-label">分类 :</label>

                        <div class="col-sm-2 padding-clear">
                            <select name="cate_level_1" class="categories form-control"></select>
                        </div>
                        <div class="col-sm-2">
                            <select name="cate_level_2" class="categories  form-control"> </select>
                        </div>
                        <div class="col-sm-2">
                            <select name="cate_level_3" class="categories  form-control"></select>
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">标签 :</label>

                        <p class=" col-sm-10  attr">

                        </p>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">选择文件 :</label>

                        <div class="col-sm-10 padding-clear">

                            <div class="progress collapse">
                                <div class="progress-bar progress-bar-striped active"></div>
                            </div>
                               <span class="btn btn-primary btn-sm import-goods-btn">
                                    请选择Excel文件
                                    <input type="file" accept="excel/*"
                                           data-url="{{ url('api/v1/my-goods/import') }}" id="upload_file"
                                           name="file">
                                </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">模板下载 : </label>

                        <div class="col-sm-10 padding-clear">
                            <a class="control-label btn btn-warning btn-sm"
                               href="{{ url('my-goods/download-template') }}"><i class="fa fa-download"></i> 模板下载</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 text-center save padding-clear">
                {{--<label>--}}
                {{--<input type="checkbox" name="status" value="1"> 立即上架<span--}}
                {{--class="prompt">(勾选后保存商品会立即上架,可被购买者查看购买)</span></label>--}}

                <p class="save-btn">
                    <a class="btn btn-bg btn-primary"> 保存</a>
                </p>
            </div>
        </form>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        //获取下级分类
        getCategory(site.api('categories'));
        getAllCategory(site.api('categories'), '');
        //获取分类
        getAttr();
        //
        goodsUpload();
        function goodsUpload() {
            $('#upload_file').change(function () {
                var fileName = $(this).val();
                var arr = fileName.split('\\');
                fileName = arr[arr.length - 1];
                $(this).closest('span').next('span').remove().end().after('<span>&nbsp;&nbsp;&nbsp;' + fileName + '</span>');
            });
            $('#upload_file').fileupload({
                dataType: 'json',
                add: function (e, data) {
                    $(".save-btn").off('click').on('click', function () {
                        var obj = $('#upload_file');
                        obj.fileupload('disable');
                        var $this = $(this),
                                shopId = $('input[name="shopId"]').val();

                                cateLevel1 = $('select[name="cate_level_1"]').val(),
                                cateLevel2 = $('select[name="cate_level_2"]').val(),
                                cateLevel3 = $('select[name="cate_level_3"]').val() || 0,
                                status = /*$('input[name="status"]').is(':checked') ? 1 :*/ 0;
                        if (!cateLevel1 || !cateLevel2) {
                            alert('请把分类选择完整');
                            return false;
                        }
                        if(!shopId || shopId==""){
                            alert('请填写店铺ID');
                            return false;
                        }
                        obj.parent().addClass('disabled').siblings('.progress').show();
                        obj.parent().siblings('.fileinput-error').remove();
                        $(this).children('a').html('<i class="fa fa-spinner fa-pulse"></i> 操作中...');
                        var formData = {
                            'status': status,
                            'cate_level_1': cateLevel1,
                            'cate_level_2': cateLevel2,
                            'cate_level_3': cateLevel3,
                            'shopId':shopId,
                        };


                        $('.attrs').each(function () {
                            var obj = $(this);
                            if (obj.val()) {
                                formData[obj.attr('name')] = obj.val();
                            }
                        });
                        data.formData = formData;

                        data.submit();
                    });
                }, fail: function (e, data) {

                    var json = data.jqXHR['responseJSON'], text = '文件上传失败';

                    if (json && json['message']) {
                        text = json['message'];
                        alert(text);
                    }
                    $(this).parent().after('<span class="fileinput-error"> ' + text + '</span>');
                    alert(text);
                },
                done: function (e, data) {
                    $(this).parent().after('<span class="fileinput-error"> 上传成功</span>');
                    alert('上传成功');
                    location.reload();
                }, always: function (e, data) {
                    // 隐藏进度条并开放按钮
                    $(this).parent().removeClass('disabled').siblings('.progress').hide()
                            .children('.progress-bar').css('width', '0');
                    $(this).fileupload('enable');
                    $(".save-btn a").html('保存');
                },
                progressall: function (e, data) {
                    var progress = Math.round(data.loaded / data.total * 1000) / 10,
                            text = isNaN(progress) ? '100.0%' : (progress.toFixed(1) + '%');
                    $(this).parent().siblings('.progress')
                            .children('.progress-bar').css('width', text).html(text);
                }
            });
        }

    </script>
@stop