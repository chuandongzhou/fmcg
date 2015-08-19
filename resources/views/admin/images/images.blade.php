@extends('admin.master')
@include('includes.treetable')
@include('includes.cropper')
@section('subtitle' , '图片管理')

@section('right-container')
    <form class="form-horizontal ajax-form" action="{{ url('admin/images') }}" method="post"
          data-help-class="col-sm-push-2 col-sm-10" data-done-url="{{ url('admin/images') }}">
        <div id="container">
            <div class="form-group">
                <div class="row col-lg-12">
                    <label class="control-label col-sm-1"></label>

                    <div class="col-sm-2">
                        <select name="level1" class="address-province form-control">

                        </select>
                    </div>
                    <div class="col-sm-2" id="level2" >
                        <select name="level2" class="address-city form-control">

                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select name="level3" class="address-district form-control">
                            <option selected="selected" value="0">请选择</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group attr">

            </div>
            <div class="form-group">
                <label class="col-sm-1 control-label" for="username"></label>

                <div class="col-sm-10 col-md-6">
                    <div class="progress collapse">
                        <div class="progress-bar progress-bar-striped active"></div>
                    </div>
                            <span data-name="image" class="btn btn-primary btn-sm fileinput-button">
                                请选择图片文件
                                <input type="file" accept="image/*" data-url="{{ url('api/v1/file/upload-temp') }}"
                                       name="file">
                            </span>

                    <div class="image-preview w160">
                        <img src="" class="img-thumbnail">
                    </div>
                </div>
            </div>
            <div class="col-sm-8 text-center save">
                <button class="btn btn-bg btn-primary" type="submit"><i class="fa fa-save"></i> 保存</button>
                <button class="btn btn-bg btn-warning" type="button" onclick="javascript:history.go(-1)"><i
                            class="fa fa-reply"></i> 取消
                </button>
            </div>
        </div>
    </form>
@stop

@section('js')
    @parent
    <script>
        $(function () {
            $('#attr').treetable({expandable: true});
            getCategory(site.api('categories'));
            getAllCategory(site.api('categories'), '{{ $search }}', 0, 0);
            $('select[name="level1"]').change(function () {
                $('div.attr').html('');
            });

            $('select[name="level2"] , select[name="level3"]').change(function () {
                var categoryId = $(this).val() || $('select[name="level2"]').val();

                $.get(site.api('categories/' + categoryId + '/attrs'), {category_id: categoryId, format: true}, function (data) {
                    var html = '';
                    for (var index in data) {
                        var options = '';
                        html += '<label class="control-label col-sm-1">' + data[index]['name'] + '</label>';
                        html += '<div class="col-sm-2">';
                        html += ' <select name="attrs[]" class="form-control">';
                        for (var i in data[index]['child']) {
                            options += ' <option value="' + data[index]['child'][i]['id'] + '">' + data[index]['child'][i]['name'] + '</option>'
                        }
                        html += options;
                        html += '</select>'
                        html += '</div>'
                    }
                    $('div.attr').html(html);
                }, 'json')
            })
        });
    </script>
@stop