@extends('admin.master')
@include('includes.treetable')

@section('subtitle' , '用户管理')

@section('right-container')
    <div id="container" class="col-sm-8">
        <table id="category" class="">
            @foreach( $categories as $id => $category )
                <tr data-tt-id="{{ $id  }}" data-tt-parent-id="{{  $categories->data('pid')  }}">
                    <td>{{  $category  }}</td>
                    <td class="btn-group-xs" align="right">
                        <a class="btn btn-primary" href="{{ url('admin/category/' . $id . '/edit') }}">
                            <i class="fa fa-edit"></i> 编辑
                        </a>
                        <a type="button" class="btn btn-danger ajax" data-method="delete" data-url="{{  url('admin/category/' . $id)  }}">
                            <i class="fa fa-trash-o"></i> 删除
                        </a>

                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@stop

@section('js')
    @parent
    <script>
        $(function () {
            $('#category').treetable({expandable: true});
        });
    </script>
@stop