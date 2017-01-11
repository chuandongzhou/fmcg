@extends('admin.master')

@section('subtitle' , '运维管理')

@section('right-container')
    <div class="notice-bar clearfix ">
        <a href="javascript:" class="active">运维管理</a>
        <a href="{{ url('admin/operation') }}">更新记录</a>
        <a href="{{ url('admin/operation/notification') }}">操作记录</a>
    </div>
    <div class="content-wrap">
        <form class="form-horizontal ajax-form" method="post"
              action="{{ url('admin/operation') }}" data-help-class="col-sm-push-2 col-sm-10"
              data-done-url="{{ url('admin/operation') }}" autocomplete="off">
            <div class="form-group">
                <label class="col-xs-2 control-label">终端类型:</label>

                <div class="col-xs-5">
                    <select name="type" class="form-control">
                        @foreach($device as $key => $item)
                            <option value="{{ $key }}"
                                    data-url="{{ (new \App\Services\RedisService())->get('app-link:' . $key) }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-2 control-label">下载地址:</label>

                <div class="col-xs-5">
                    <input class="form-control control-radius download-url" name="download_url" placeholder="请输入下载地址" type="text">
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-2 control-label">版本号:</label>
                <div class="col-xs-5">
                    <input class="form-control control-radius" name="version_no" placeholder="请输入版本号" type="text">
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-2 control-label">版本名:</label>
                <div class="col-xs-5">
                    <input class="form-control control-radius" name="version_name" placeholder="请输入版本名" type="text">
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-2 control-label">更新内容:</label>
                <div class="col-xs-5">
                    <textarea class="form-control" rows="5" name="content"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-2 control-label"></label>
                <div class="col-xs-5">
                    <button type="submit" class="btn btn-blue control">提交</button>
                </div>
            </div>
        </form>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        var downloadUrlControl = $('.download-url'), typeControl = $('select[name="type"]');
        typeControl.on('change', function () {
            var url = $(this).children(':checked').data('url');
            downloadUrlControl.val(url);
        });
        downloadUrlControl.val(typeControl.children(':checked').data('url'))
    </script>
@stop