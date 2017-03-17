<div class="row goods-editor">
    <form class="form-horizontal ajax-form" method="post" action="" autocomplete="off">
        <div class="col-sm-12 editor-panel content-wrap">
            @if(request()->is('admin/goods/import'))
                <div class="form-group">
                    <label class="col-sm-2 control-label">店铺ID:</label>

                    <div class="col-xs-4 padding-clear">
                        <input type="text" name="shop_id" class="form-control" placeholder="店铺ID"/>
                    </div>
                </div>
            @endif
            <div class="form-group">
                <label class="col-sm-2 control-label">分类 :</label>

                <div class="col-sm-2 padding-clear">
                    <select name="cate_level_1" class="categories form-control control"></select>
                </div>
                <div class="col-sm-2">
                    <select name="cate_level_2" class="categories  form-control control">
                        <option value="">请选择</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <select name="cate_level_3" class="categories  form-control control">
                        <option value="">请选择</option>
                    </select>
                </div>

            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">标签 :</label>

                <div class=" col-sm-10  attr attr-labels" style="border: 1px solid rgb(255, 255, 255);">

                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">选择文件 :</label>

                <div class="col-sm-6 padding-clear">

                    <div class="progress collapse">
                        <div class="progress-bar progress-bar-striped active"></div>
                    </div>
                               <span class="btn btn-blue-lighter import-goods-btn">
                                    请选择Excel文件
                                    <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                           data-url="{{ url('api/v1/my-goods/import') }}" id="upload_file"
                                           name="file">
                                </span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">模板下载 : </label>
                <div class="col-sm-10 padding-clear">
                    <a class="control-label btn-blue-lighter btn-sm templet-down"
                       href="{{ url('my-goods/download-template') }}"><i class="fa fa-download"></i>
                        模板下载</a>
                    <span class="prompt">请严格按照模板格式批量导入商品</span>
                </div>
            </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">商品单位 : </label>
                    <div class="col-sm-10 unit">
                        0-盒  1-瓶  2-箱  3-听  4-条  5-袋  6-罐  7-包  8-桶  9-杯  10-支
                        11-个 12-筒 13-排 14-件 15-对 16-碗 17-壶 18-挂 19-提
                    </div>
                </div>
        </div>
        <div class="col-sm-push-2 col-sm-10   padding-clear">
            <button class="btn btn-success save-btn">提交</button>
        </div>
    </form>
</div>
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