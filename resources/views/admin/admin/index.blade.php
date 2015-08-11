@extends('index.master')

@section('subtitle', '首页')

@section('container')
    @include('admin.left-nav')
    <div class="right-content table-responsive">
        <form method="post" action="{{url('admin/admin')}}">
       <table class="table table-hover table-striped">
           <tr>
               <th></th>
               <th>管理员ID</th>
               <th>管理员账号</th>
               <th>姓名</th>
               <th>所属角色</th>
               <th>状态</th>
               <th>操作</th>
           </tr>
           @foreach($admins as $admin)
           <tr>
               <td><input type="checkbox" name="status"/> </td>
               {{--<td><input type="hidden" name="id[]" value="{{$admin->id}}"/> </td>--}}
               <td>{{$admin->id}}</td>
               <td>{{$admin->user_name}}</td>
               <td>{{$admin->real_name}}</td>
               <td>{{$admin->role->name}}</td>
               <td>
                   @if($admin->status)
                       禁用
                   @else
                        启用
                   @endif
               </td>
               <td><a href="{{url('admin/admin/'.$admin->id.'/edit')}}">修改</a>|
                   <a class="ajax" data-method="delete" href="{{url('admin/admin/'.$admin->id)}}">删除</a></td>
           </tr>
          @endforeach
       </table>
        <input type="checkbox" name="check_all" />
        <button type="submit" name="delete" >删除</button>
        <button type="submit" name="allow" >启用</button>
        <button type="submit" name="deny" >禁用</button>
        </form>
    </div>
@stop