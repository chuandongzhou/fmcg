@extends('index.manage-master')
@section('subtitle', '业务管理-业务员目标设置')
@include('includes.timepicker')

@section('container')
    @include('includes.menu')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-sm-12 path-title">
                    <a href="{{ url('business/salesman') }}">业务管理</a> >
                    <a href="{{ url('business/salesman/target') }}">业务目标</a> >
                    <span class="second-level">设置目标</span>
                </div>
            </div>

            <div class="row salesman">
                <div class=" col-sm-push-1 col-sm-11"><b>业务员目标设置</b><br/><br/><br/></div>
                <div class="col-sm-12 create">
                    <form class="form-horizontal ajax-form"
                          action="{{ $actionUrl or url('api/v1/business/salesman/target-set') }}"
                          method="post"
                          data-help-class="col-sm-push-2 col-sm-10"
                          data-done-url="{{ url('business/salesman/target') }}"
                          autocomplete="off">
                        <input type="hidden" name="_method" value="put">
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="username"><span class="red">*</span>月份:</label>

                            <div class="col-sm-10 col-md-6">
                                <input type="text" name="date" class="form-control datetimepicker"
                                       value="{{ \Carbon\Carbon::now()->format('Y-m') }}"
                                       data-format="YYYY-MM"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="salesman_id"><span
                                        class="red">*</span>业务员:</label>

                            <div class="col-sm-10 col-md-6">
                                <select name="salesman_id" class="inline-control">
                                    <option value="">请选择业务员</option>
                                    @foreach($salesmen as $salesman)
                                            <option value="{{ $salesman->id }}">
                                                {{ $salesman->name }}
                                            </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="contact"><span class="red">*</span>金额目标:</label>

                            <div class="col-sm-6 col-md-4">
                                <input type="text" name="target" class="form-control" placeholder="请输入业务员金额目标"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="contact">单品目标:</label>

                            <div class="col-sm-10 set-target">
                                <div class="select-commodity">
                                    <a class="btn btn-blue-lighter" data-target="#chooseGoods"
                                       data-toggle="modal">选择商品</a> <span class="prompt">已选择商品<span class="num">0</span>个</span>
                                </div>
                                <div>
                                    <table class="table table-selected-goods table-bordered table-center public-table">
                                        <thead>
                                        <tr>
                                            <th width="50%">商品名称</th>
                                            <th width="20%">单位</th>
                                            <th width="20%">数量</th>
                                            <th width="10%">操作</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-push-2 col-sm-10 save">
                                <button class="btn btn-success" type="submit">提交</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('includes.my-goods-list')

@stop
@section('js')
    @parent
    <script type="text/javascript">
        var chooseGoods = $('#chooseGoods');
        chooseGoods.find('.btn-submit').on('click', function () {
            var goodsChecked = $('#chooseGoods').find($('tbody.goods-list')).find($(".goods:checked"));
            goodsChecked.each(function () {
                var self = $(this),
                    goodsName = self.data('name'),
                    pieces_1 = self.data('pieces_1'),
                    pieces_2 = self.data('pieces_2'),
                    pieces_3 = self.data('pieces_3'),
                    pieces_1_lang = self.data('pieces_1_lang'),
                    pieces_2_lang = self.data('pieces_2_lang'),
                    pieces_3_lang = self.data('pieces_3_lang');
                // goods_detail_url = window.location.protocol + '//' + window.location.host + '/goods/' + $(this).val();
                var html = '', option = '<option value="">请选择</option>';
                html += '<tr><td>';
                html += '<div>' + goodsName + '</div>';
                html += '</td><td><select class="form-control" name="goods[' + self.val() + '][pieces]">';
                if (pieces_1 != null) {
                    option += '<option value=' + pieces_1 + '>' + pieces_1_lang + '</option>';
                }
                if (pieces_2 != null) {
                    option += '<option value=' + pieces_2 + '>' + pieces_2_lang + '</option>';
                }
                if (pieces_3 != null) {
                    option += '<option value=' + pieces_3 + '>' + pieces_3_lang + '</option>';
                }
                html += option;
                html += ' </select></td><td>';
                html += '<input  class="form-control" type="text" name="goods[' + self.val() + '][num]" class= "num" placeholder="输入数量"/>';
                html += '<input  type="hidden" class="ids" value="' + self.val() + '"/>';
                html += '</td><td><i onclick="deleteChoose(this)" class="iconfont red icon-shanchu2"></i></td></tr>';
                $('.table-selected-goods').find('tbody').append(html);
            });
            setGoodsNum();
        });

        $('.ajax-form [type="submit"]').on('click', function () {
            var target = true;
            $(this).closest('form').find('.table-selected-goods').find('tbody').find('select,input:visible').each(function () {
                var obj = $(this);
                if(obj.val() == '') {
                    tips(obj, '此选项不能为空');
                    target = false;
                    return false;
                }

            });
            return target;

        });

        //删除选择的商品
        function deleteChoose(obj) {
            if (confirm('确定删除？')) {
                $(obj).parents('tr').remove()
                setGoodsNum();
            }
        }

        function setGoodsNum() {
            var goodsNum = $('.table-selected-goods tbody').children('tr').length;
            $('.set-target').find('.num').html(goodsNum);
        }
    </script>
@stop
