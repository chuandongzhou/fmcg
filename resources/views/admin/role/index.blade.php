@extends('admin.master')

@section('right-container')
        <form class="form-horizontal ajax-form" method="post" action="{{url('admin/role')}}" data-help-class="col-sm-push-2 col-sm-10"  autocomplete="off">
            <table class="table table-hover table-striped">
                <tr>
                    <th>#</th>
                    <th>角色名</th>
                    <th>操作</th>
                </tr>
                @foreach($roles as $key=>$role)
                    <tr>
                        <td><input type="checkbox" name="status"/> </td>
                        <input type="hidden" name="id[]" value="{{$key}}"/>

                        <td>{{$role}}</td>
                        <td><div class="btn-group btn-group-xs" role="group">


                        <a class="btn btn-primary"  href="{{url('admin/role/'.$key.'/edit')}}">修改</a>
                            <a class="btn btn-danger ajax" data-method="delete" href="{{url('admin/role/'.$key)}}">删除</a>
                            </div>
                        </td>

                    </tr>
                @endforeach
            </table>
            <div class="btn-group btn-group-xs" role="group">
                <input type="checkbox" id="parent" class="checkbox-inline" name="check_all"/>
                <label for="parent">全选</label>
            </div>
            <div class="btn-group btn-group-xs" role="group">
                <button type="button" class="btn btn-danger ajax" data-method="delete"
                        data-url="{{ url('admin/role') }}">
                    <i class="fa fa-trash-o"></i> 删除
                </button>
            </div>

        </form>

@stop