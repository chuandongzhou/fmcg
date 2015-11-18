@extends('admin.master')
@include('includes.treetable')
@section('subtitle' , '用户管理')

@section('right-container')
    <form class="form-horizontal ajax-form" method="post"
          action="{{ url('admin/attr/save') }}" data-help-class="col-sm-push-2 col-sm-10" >

        <div class="form-group">
            <label for="username" class="col-sm-2 control-label">所属分类</label>

            <div class="col-sm-4">
                <select class="form-control" id="category_id" name="category_id">
                    @foreach($categories as $id => $name)
                        <option value="{{ $id }}"{{ $categories->data('level') == 1 ? ' disabled' : '' }} {{ $id == $cateId ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="username" class="col-sm-2 control-label">标签</label>

            <div class="col-sm-8 first-attr" id="first-attr">
                <table id="attr" class="table">
                    <tr>
                        <td colspan="3">
                            <a class="btn btn-default add-attr-btn text-right" data-pid="0"
                               data-cate-id="{{ $cateId }}"
                               href="javascript:void(0)">
                                <i class="fa fa-plus"></i> 添加一级标签
                            </a>
                        </td>
                    </tr>
                    @foreach( $allAttr as $id => $attr )
                        <tr data-tt-id="{{ $id  }}" data-tt-parent-id="{{  $allAttr->data('pid')  }}">
                            <td>
                                @if (in_array($id , $thisAttr) && $allAttr->data('status'))
                                    <input type="checkbox" name="attr_id[]" value="{{ $allAttr->data('attr_id')}}"
                                           checked/>
                                @elseif(in_array($id , $thisAttr))
                                    <input type="checkbox" name="new_exist_attr_id[]" value="{{ $allAttr->data('attr_id')}}"/>
                                @else
                                    <input type="checkbox" name="new_attr_id[{{  $allAttr->data('pid')  }}]"
                                           value="{{ $allAttr->data('attr_id')}}"/>
                                @endif
                            </td>
                            <td>{{  $attr  }}</td>
                            <td class=" btn-group-xs">
                                @if($allAttr->data('pid') == 0 )
                                    <a class="btn btn-default add-attr-btn" data-pid="{{ $id }}"
                                       data-cate-id="{{ $cateId }}"
                                       href="javascript:void(0)">
                                        <i class="fa fa-plus"></i> 添加子标签
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button class="btn btn-bg btn-primary" type="submit">保存</button>
            </div>
        </div>
    </form>
@stop
@section('js')
    @parent
    <script>
        $(function () {
            var table = $('#attr');
            table.treetable({expandable: true});
            $('#category_id').change(function () {
                var cateId = $(this).val();
                window.location.href = "{{ url('admin/attr/create') }}" + '/' + cateId;
            });
            $('.add-attr-btn').on('click', function () {
                var obj = $(this), pid = obj.data('pid'), cateId = obj.data('cateId');
                var str = '';
                str += '<tr data-tt-id="" data-tt-parent-id="' + pid;
                str += '" class="branch collapsed" style="display: table-row;">';
                str += '      <td>';
                if (pid > 0) {
                    str += '<span class="indenter" style="padding-left: 19px;"></span>';
                } else {
                    str += '<span class="indenter" style="padding-left: 0px;"></span>';
                }
                str += '<input type="checkbox" name="new_exist_attr_id[]">';
                str += '   </td>';
                str += '    <td>&nbsp;│&nbsp;└&nbsp;<input type="text" class="attr-name"/>';
                str += '<button type="button" data-pid="' + pid;
                str += '" data-cate-id="' + cateId;
                str += '" class="btn btn-sm btn-default add-attr" onclick="addAttr(this)"><span class="fa fa-check text-success">确定</span></button>';
                str += '<button type="button" class="btn btn-sm btn-default js-close" onclick="deleteAttr(this)"><span class="fa fa-remove text-danger"></span></button></td>';
                str += '<td class=" btn-group-xs">';
                str += '   </td>';
                str += '   </tr>';
                obj.closest('tr').after(str);
            });

            table.on('click', 'input[type="checkbox"]', function () {
                var obj = $(this), pid = obj.closest('tr').attr('data-tt-parent-id'), id = obj.closest('tr').attr('data-tt-id');
                if (pid > 0) {
                    var isChecked = $(this).is(':checked');
                    isChecked ? obj.closest('tr').siblings('tr[data-tt-id="' + pid + '" ]').find('input[type="checkbox"]').prop('checked', true) : ''
                } else {
                    var isChecked = $(this).is(':checked');

                    isChecked ? '' : obj.closest('tr').siblings('tr[data-tt-parent-id="' + id + '" ]').find('input[type="checkbox"]').prop('checked', false);
                }
            })

        });
        function addAttr(obj) {
            var obj = $(obj),
                    data = {
                        pid: obj.data('pid'),
                        category_id: obj.data('cateId'),
                        name: obj.closest('tr').find('.attr-name').val()
                    },
                    saveUrl = '{{ url("admin/attr") }}';
            if (!data.name) {
                alert('标签名不能为空');
                return false;
            }
            $.post(saveUrl, data, function (result) {
                if (result.status) {
                    obj.closest('tr').find('input[type="checkbox"]').val(result.content);
                    obj.closest('td').html('&nbsp;│&nbsp;└&nbsp;' + data.name);

                } else {
                    alert(obj.content);
                }
            }, 'json')
        }
        function deleteAttr(obj) {
            $(obj).closest('tr').remove();
        }
    </script>
@stop