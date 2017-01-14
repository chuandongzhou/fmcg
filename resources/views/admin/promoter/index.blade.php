@extends('admin.master')

@include('admin.promoter.promoter-modal')

@section('subtitle' , '销售推广')

@section('right-container')
    <div class="notice-bar clearfix ">
        <a href="{{ url('admin/promoter') }}" class="{{ path_active('admin/promoter') }}">销售推广</a>
        <a href="{{ url('admin/promoter/statistics') }}" class="{{ path_active('admin/promoter/statistics') }}">推广统计</a>
    </div>
    <div class="content-wrap">
        <input type="button" class="control btn btn-blue " data-target="#promoterModal" data-toggle="modal"
               value="添加推广员">
        <form class="form-horizontal ajax-form" method="post"
              action="{{ url('admin/promoter/') }}" data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
            <table class="table public-table table-bordered">
                <tr>
                    <th>推广员姓名</th>
                    <th>联系方式</th>
                    <th>推广码</th>
                    <th>生效时间</th>
                    <th>过期时间</th>
                    <th>操作</th>
                </tr>
                @foreach($promoters as $promoter)
                    <tr>
                        <td>{{ $promoter->name }}</td>
                        <td>{{ $promoter->contact }}</td>
                        <td>{{ $promoter->spreading_code }}</td>
                        <td>{{ $promoter->start_at }}</td>
                        <td>{{ $promoter->end_at ?: '永久' }}</td>
                        <td>
                            <a class="edit" href="javascript:;"
                               data-target="#promoterModal"
                               data-toggle="modal"
                               data-id="{{ $promoter->id }}"
                               data-name="{{ $promoter->name }}"
                               data-contact="{{ $promoter->contact }}"
                               data-start-at="{{ $promoter->start_at }}"
                               data-end-at="{{ $promoter->end_at }}"
                            >
                                <i class="iconfont icon-xiugai"></i>编辑
                            </a>

                            <a class="remove ajax" href="javascript:;" data-method="delete"  data-url="{{ url('admin/promoter/' . $promoter->id) }}">
                                <i class="iconfont icon-shanchu"></i>删除
                            </a>

                        </td>
                    </tr>
                @endforeach
            </table>
        </form>
    </div>
    <div class="text-right">
        {{ $promoters->render() }}
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            onCheckChange('#parent', '.child');
        })
    </script>
@stop