@extends('admin.master')
@include('includes.treetable')

@section('right-container')

    <form class="form-horizontal ajax-form" method="{{ $role->id ? 'put' : 'post' }}"
          action="{{url('admin/role/'.$role->id)}}" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">

        <div class="form-group">
            <label for="username" class="col-sm-2 control-label">角色名:</label>

            <div class="col-sm-4">
                <input type="text" class="form-control" id="name" value="{{ $role->name }}" name="name"
                       placeholder="请输入角色名">
            </div>
        </div>
        <div class="form-group">
            <label for="username" class="col-sm-2 control-label">权限分配:</label>

            <div class="col-sm-8">
                <div class="col-sm-8 first-attr" id="first-attr">
                    <table id="node" class="table">

                        @foreach( $nodes as $id => $node )
                            <tr data-tt-id="{{ $id  }}" data-tt-parent-id="{{  $nodes->data('pid')  }}">
                                <td>
                                    <input type="checkbox" name="node[]"
                                           value="{{ $nodes->data('id') }}" {{ in_array($nodes->data('id') , $role_node) ? 'checked' : '' }} />
                                </td>
                                <td>{{  $node  }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-bg btn-primary">{{ $role->id ? '修改' : '添加' }}</button>
            </div>
        </div>

    </form>
@stop

@section('js')
    @parent
    <script>
        $(function () {
            var table = $('#node');
            table.treetable({expandable: true});

            table.on('click', 'input[type="checkbox"]', function () {
                var obj = $(this), pid = obj.closest('tr').attr('data-tt-parent-id'), id = obj.closest('tr').attr('data-tt-id');
                if (pid > 0) {
                    var isChecked = $(this).is(':checked');
                    isChecked ? obj.closest('tr').siblings('tr[data-tt-id="' + pid + '" ]').find('input[type="checkbox"]').prop('checked', true) : ''
                } else {
                    var isChecked = $(this).is(':checked');

                    obj.closest('tr').siblings('tr[data-tt-parent-id="' + id + '" ]').find('input[type="checkbox"]').prop('checked', isChecked);
                }
            })

        });
    </script>
@stop