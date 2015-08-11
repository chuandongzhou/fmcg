@extends('index.master')

@section('subtitle', '首页')

@section('container')
    @include('admin.left-nav')
    <div class="right-content">
       <form method="post" action="{{url('admin/admin/'.$user->id)}}">
           <input type="hidden" name="_method" value="PUT"/>
           {{csrf_field()}}
           <table class="table text-left" style="float:right;width:75%">
               <tr>
                   <td>管理员账号：</td>
                   <td><input type="text" name="user_name" value="{{$user->user_name}}"/></td>
               </tr>
               <tr>
                   <td>管理员密码：</td>
                   <td><input type="password" name="password"/></td>
               </tr>
               <tr>
                   <td>确认密码：</td>
                   <td><input type="password" name="password_confirmation"/></td>
               </tr>
               <tr>
                   <td>管理员姓名：</td>
                   <td><input type="text" name="real_name" value="{{$user->real_name}}"/></td>
               </tr>
               <tr>
                   <td>所属角色：</td>
                   <td><select name="role_id">
                           @foreach($role as $key=>$item)
                           <option value="{{$key}}"
                               @if($key == $user->role->id)
                                   selected
                               @endif
                               >{{$item}}</option>
                          @endforeach
                       </select></td>
               </tr>
               <tr>
                   <td><button type="submit">修改</button></td>
                   <td><button >取消</button></td>
               </tr>
           </table>


       </form>
    </div>
@stop