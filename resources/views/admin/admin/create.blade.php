@extends('index.master')

@section('subtitle', '首页')

@section('container')
    @include('admin.left-nav')
    <div class="right-content">
       <form method="post" action="{{url('admin/admin')}}">
           {{--<input type="hidden" name="user_name"/>--}}
           {{csrf_field()}}
           <table>
               <tr>
                   <td>管理员账号：</td>
                   <td><input type="text" name="user_name"/></td>
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
                   <td><input type="text" name="real_name"/></td>
               </tr>
               <tr>
                   <td>所属角色：</td>
                   <td><select name="role_id">
                           @foreach($role as $key=>$item)
                           <option value="{{$key}}">{{$item}}</option>
                          @endforeach
                       </select></td>
               </tr>
               <tr>
                   <td colspan="2"><button type="submit">添加</button></td>
               </tr>
           </table>


       </form>
    </div>
@stop