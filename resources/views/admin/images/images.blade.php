@extends('admin.master')
@include('includes.treetable')
@include('includes.cropper')
@section('subtitle' , '图片管理')

@section('right-container')
    <form class="form-horizontal ajax-form" action="{{ url('admin/images') }}" method="post"
          data-help-class="col-sm-push-2 col-sm-10" data-done-url="{{ url('admin/images') }}">
        <div id="container">
            <div class="form-group">
                <div class="row col-lg-12">
                    <label class="control-label col-sm-1"></label>

                    <div class="col-sm-2">
                        <select name="cate_level_1" class="form-control">

                        </select>
                    </div>
                    <div class="col-sm-2" id="level2">
                        <select name="cate_level_2" class="form-control">

                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select name="cate_level_3" class="form-control">
                            <option selected="selected" value="0">请选择</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-1 control-label"></label>

                <div class="attr col-sm-10">

                </div>
            </div>


        </div>
        <div class="form-group">
            <label class="col-sm-1 control-label" for="username"></label>

            <div class="col-sm-10 col-md-6">
                <div class="progress collapse">
                    <div class="progress-bar progress-bar-striped active"></div>
                </div>
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
        <div class="col-sm-8 text-center save">
            <button class="btn btn-bg btn-primary" type="submit"><i class="fa fa-save"></i> 保存</button>
            <button class="btn btn-bg btn-warning" type="button" onclick="javascript:history.go(-1)"><i
                        class="fa fa-reply"></i> 取消
            </button>
        </div>
        </div>
    </form>
@stop

@section('js')
    @parent
    <script>
        $(function () {
            $('#attr').treetable({expandable: true});
            getCategory(site.api('categories'));
            getAllCategory(site.api('categories'), '{{ $search }}');
            getAttr();
        });
    </script>
@stop