@extends('index.menu-master')
@section('subtitle', '商品')

@include('includes.uploader')

@section('right')
    <div class="col-sm-12 goods-editor">
        <form class="form-horizontal ajax-form" method="post" action="" autocomplete="off">
            <div class="row editor-panel content-wrap">
                <div class="col-sm-12 editor-wrap">
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
                <label>
                    <input type="checkbox" name="status" value="1"> 立即上架<span
                            class="prompt">(勾选后保存商品会立即上架,可被购买者查看购买)</span></label>

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
        goodsBatchUpload();
    </script>
@stop