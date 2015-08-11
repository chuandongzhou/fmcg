@extends('index.master')

@section('subtitle', '首页')

@section('container')
    @include('admin.left-nav')
    <div class="right-content">
        <form method="post"
              action="{{ url( 'admin/user/destroy') }}">
            <table class="table">
                <tr>
                    <td><input type="checkbox" class="parent"/></td>
                    <td>账号名</td>
                    <td>名称</td>
                    <td>地址</td>
                    <td>状态</td>
                    <td>操作</td>
                </tr>
                @foreach($users as $user)
                    <tr>
                        <td><input type="checkbox" class="child" name="uid[]" value="{{ $user->id  }}"/></td>
                        <td>{{ $user->user_name  }}</td>
                        <td>{{ $user->nickname  }}</td>
                        <td>{{ $user->address  }}</td>
                        <td>{{ $user->status == 1 ? '启用' : '禁用' }}</td>
                        <td>
                            <a href="{{ url('admin/user/'.$user->id .'/edit')  }}">编辑</a>
                            <a href="{{ url('admin/user/'.$user->id )  }}">详情</a>
                            <a href="{{ url('admin/user/'.$user->id )  }}" data-method="delete" class="ajax">删除</a>
                        </td>
                    </tr>
                @endforeach
            </table>
        </form>
    </div>
@stop