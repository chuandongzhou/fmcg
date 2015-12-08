@extends('admin.master')

@section('subtitle' , '条形码')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/barcode-without-images') }}" data-help-class="col-sm-push-2 col-sm-10"
          autocomplete="off">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>条形码id</th>
                <th>条形码</th>
                <th>上传时间</th>
                <th class="text-nowrap">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($barcode as $code)
                <tr>
                    <td><input type="checkbox" class="child" name="ids[]" value="{{$code->id}}"/></td>
                    <td>{{$code->id}}</td>
                    <td>{{$code->barcode}}</td>
                    <td>{{$code->created_at}}</td>
                    <td>
                        <div class="btn-group btn-group-xs" role="group">
                            <button type="button" class="btn btn-danger ajax" data-method="delete"
                                    data-url="{{ url('admin/barcode-without-images/'.$code->id) }}">
                                <i class="fa fa-trash-o"></i> 删除
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="btn-group btn-group-xs" role="group">
            <input type="checkbox" id="parent" class="checkbox-inline"/>
        </div>
        <div class="btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-danger ajax" data-method="delete"
                    data-url="{{ url('admin/barcode-without-images/batch') }}">
                <i class="fa fa-trash-o"></i> 批量删除
            </button>
        </div>

        <div class="btn-group btn-group-xs" role="group">
            <a class="btn btn-bg btn-warning" href="{{ url('admin/barcode-without-images/export') }}">
                <i class="fa"></i> 导出
            </a>
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