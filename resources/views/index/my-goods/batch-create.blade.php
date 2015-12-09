@extends('index.menu-master')
@section('subtitle', '商品')

@include('includes.uploader')

@section('right')
    <div class="col-sm-12 goods-editor">
        <form class="form-horizontal ajax-form" method="post" action="{{ url('api/v1/my-goods') }}" autocomplete="off">
            <div class="row editor-panel content-wrap">
                <div class="col-sm-10 editor-wrap">
                    <div class="form-group editor-item">
                        <p class="items-item">
                            <label class="control-label">分类 :</label>
                            <select name="cate_level_1" class="categories"></select>
                            <select name="cate_level_2" class="categories"> </select>
                            <select name="cate_level_3" class="categories"></select>
                        </p>
                    </div>
                    <div class="form-group  editor-item">
                        <label class="control-label">标签 :</label>

                        <p class="items-item attr">

                        </p>
                    </div>
                    <div class="form-group  editor-item">
                        <label class="control-label">选择文件 :</label>

                        <p class="items-item">

                        <div class="progress collapse">
                            <div class="progress-bar progress-bar-striped active"></div>
                        </div>
                           <span class="btn btn-primary btn-sm import-goods-btn">
                                请选择Excel文件
                                <input type="file" accept="excel/*"
                                       data-url="{{ url('api/v1/my-goods/import') }}" id="upload_file"
                                       name="file">
                            </span>
                        <a class="control-label" href="{{ url('my-goods/download-template') }}">下载模板</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 text-center save">
                <a class="btn btn-bg btn-success save-btn" data-data='{"status":"1"}'>
                    <i class="fa fa-level-up"></i> 立即上架</a>
                <a class="btn btn-bg btn-primary save-btn" href="javascript:;"><i class="fa fa-save"></i> 保存</a>
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