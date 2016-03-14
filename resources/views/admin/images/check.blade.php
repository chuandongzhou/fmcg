@extends('admin.master')

@section('subtitle' , '条形码')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/images') }}" data-help-class="col-sm-push-2 col-sm-10"
          autocomplete="off">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>图片</th>
                <th>条形码</th>
                <th>商品名</th>
                <th>上传时间</th>
                <th class="text-nowrap">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($goodsImages as $goodsImage)
                <tr>
                    <td><input type="checkbox" class="child" name="ids[]" value="{{$goodsImage->id}}"/></td>
                    <td><img src="{{$goodsImage->image_url}}" width="80px" height="80px"></td>
                    <td>{{$goodsImage->bar_code}}</td>
                    <td>{{$goodsImage->goods ? $goodsImage->goods->name : ''}}</td>
                    <td>{{$goodsImage->created_at}}</td>
                    <td>
                        <div class="btn-group btn-group-xs" role="group">
                            <a class="close btn ajax" type="button"
                               data-url="{{ url('admin/images/check-handle') }}"
                               data-data='{"ids" : "{{$goodsImage->id }}"}'
                               data-method="put">审核通过</a>

                            <a class="close btn ajax" type="button"
                               data-url="{{ url('admin/images',[$goodsImage->id]) }}"
                               data-method="delete">删除</a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pager">
            {!! $goodsImages->render() !!}
        </div>
        <div class="btn-group btn-group-xs" role="group">
            <input type="checkbox" id="parent" class="checkbox-inline"/>
        </div>
        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-danger ajax" data-method="delete"
                    data-url="{{ url('admin/images/batch-delete') }}">
                <i class="fa fa-trash-o"></i> 批量删除
            </button>
        </div>

        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-default ajax" data-method="put"
                    data-url="{{ url('admin/images/check-handle') }}">
                <i class="fa fa-ok"></i> 批量审核通过
            </button>
        </div>
    </form>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            onCheckChange('#parent', '.child');
        })
    </script>
@stop