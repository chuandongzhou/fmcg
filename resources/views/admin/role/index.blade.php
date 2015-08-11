@extends('index.master')

@section('subtitle', '首页')

@section('container')
    @include('admin.left-nav')
    <div class="right-content table-responsive">
        <form method="post" action="{{url('admin/admin')}}">
            <table class="table table-hover table-striped">
                <tr>
                    <th></th>
                    <th>角色名</th>
                    <th>操作</th>
                </tr>
                @foreach($roles as $key=>$role)
                    <tr>
                        <td><input type="checkbox" name="status"/> </td>
                        <input type="hidden" name="id[]" value="{{$key}}"/>

                        <td>{{$role}}</td>

                        <td><a href="{{url('admin/admin/'.$key.'/edit')}}">修改</a>|
                            <a class="ajax" data-method="delete" href="{{url('admin/admin/'.$key)}}">删除</a></td>
                    </tr>
                @endforeach
            </table>
            <input type="checkbox" name="check_all" />
            <button type="submit" name="delete" >删除</button>
        </form>
    </div>
@stop