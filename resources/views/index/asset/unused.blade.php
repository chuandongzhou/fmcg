@extends('index.manage-master')
@section('subtitle', '资产管理')
@include('includes.timepicker')
@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('asset/unused') }}">资产管理</a> >
                    <span class="second-level">未使用</span>
                </div>
            </div>
            <div class="row delivery">
                <div class="col-sm-12 control-search assets">
                    <form action="{{url('asset/unused')}}" method="get" autocomplete="off">
                        <input class="enter control datetimepicker" name="start_at" placeholder="开始时间" type="text"
                               value="{{$data['start_at'] ?? ''}}">至
                        <input class="enter control datetimepicker" name="end_at" placeholder="结束时间" type="text"
                               value="{{$data['end_at'] ?? ''}}">
                        <select name="name" class="control">
                            <option value="">请选择资产名称型号</option>
                            @if(isset($assetName))
                                @foreach($assetName as $name)
                                    @if($data['name'] == $name)
                                        <option selected value="{{$name}}">{{$name}}</option>
                                    @else
                                        <option value="{{$name}}">{{$name}}</option>
                                    @endif

                                @endforeach
                            @endif
                        </select>
                        <button type="button" class="btn btn-blue-lighter search-by-get control">查询</button>
                        <button type="button" data-target="#add-modify" data-toggle="modal"
                                class="add btn btn-blue-lighter  control ">添加资产
                        </button>
                    </form>
                </div>
                <div class="col-sm-12 table-responsive wareh-details-table">
                    <table class="table-bordered table table-center public-table">
                        <thead>
                        <tr>
                            <th>资产名称</th>
                            <th>数量</th>
                            <th>添加时间</th>
                            <th>申请条件</th>
                            <th>资产备注</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($assets))
                            @foreach($assets as $asset)
                                <tr>
                                    <td>{{$asset->name ?? ''}}</td>
                                    <td>{{$asset->quantity ?? ''}}{{$asset->unit ?? ''}}</td>
                                    <td>{{$asset->created_at ?? ''}}</td>
                                    <td>{{$asset->condition ?? ''}}</td>
                                    <td><a href="javascript:"
                                           class="gray status"
                                           data-data='{"status":"{{cons('status.on')}}"}'
                                           data-url='asset/status-change/{{$asset->id}}'
                                           data-method='put'
                                        >test</a> <a href="" class="hidden"><i class="iconfont  icon-xiugai"></i>修改</a></a> {{$asset->remark ?? ''}}</td>
                                    <td class="on {{$asset->status == cons('asset.status.off')? 'hidden' :''}}">
                                        已启用
                                    </td>
                                    <td class="off {{$asset->status == cons('asset.status.on')? 'hidden' :''}}">已停用</td>
                                    <td class="on {{$asset->status == cons('asset.status.off')? 'hidden' :''}}">
                                        <a href="javascript:" onclick="asset.statusChange('{{$asset->id}}',this)"
                                           class="gray"><i
                                                    class="iconfont icon-jinyong"></i><span class="red">禁用</span></a>
                                        <a class="color-blue view" data-target="#add-modify" data-toggle="modal"><i
                                                    class="iconfont icon-chakan"></i>查看</a>
                                    </td>
                                    <td class="off {{$asset->status == cons('asset.status.on')? 'hidden' :''}}">
                                        <input name="data" type="hidden" disabled
                                               data-id="{{$asset->id}}"
                                               data-name="{{$asset->name}}"
                                               data-quantity="{{$asset->quantity}}"
                                               data-unit="{{$asset->unit}}"
                                               data-condition="{{$asset->condition}}"
                                               data-remark="{{$asset->remark}}"
                                               data-status="{{$asset->status}}">
                                        <a href="javascript:" onclick="asset.statusChange('{{$asset->id}}',this)"
                                           class="gray"><i
                                                    class="iconfont icon-qiyong"></i>启用</a>
                                        <a href="javascript:" data-target="#add-modify" data-toggle="modal"
                                           class="edit">
                                            <i class="iconfont icon-xiugai"></i>修改</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12 text-right">
                    @if($assets)
                        {!! $assets->render() !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('includes.asset-modal')
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $('.status').on('click', function () {
            var obj = $(this),
                    url = obj.data('url'),
                    _method = obj.data('method') || 'post',
                    data = obj.data('data');
            $(obj).button({
                loadingText: '<i class="fa fa-spinner fa-pulse"></i>'
            });
            $(obj).button('loading');

            $.ajax({
                url: site.api(url),
                method: _method,
                dataType: 'json',
                data: {'name':1}

            }).done(function (data) {
                var tr = $(obj).parents('tr');
                if(data.status != false){
                    $(obj).html(data.message || '操作成功')
                    tr.find('.hide').removeClass('hidden hide');

                }else{
                    $(obj).html(data.message || '操作失败')
                }
            });
        });

        asset = {
            statusChange: function (id, obj) {
                var html = $(obj).html();
                $(obj).button({
                    loadingText: '<i class="fa fa-spinner fa-pulse"></i>'
                });
                $(obj).button('loading');
                $.post(site.api('asset/status-change/'+id), {'_method': 'put'}, function (data) {
                    var _class = $(obj).parents('td').hasClass('on');
                    var tr = $(obj).parents('tr');
                    $(obj).html(html);
                    if (_class) {
                        tr.find('td.on').addClass('hidden');
                        tr.find('td.off').removeClass('hidden').button('on');
                    } else {
                        tr.find('td.off').addClass('hidden');
                        tr.find('td.on').removeClass('hidden').button('off');
                    }

                });
            }
        };
        formSubmitByGet();

    </script>
@stop
