@section('body')
    @parent
    <div class="modal fade in" id="mortgageGoodsModal" tabindex="-1" role="dialog"
         aria-labelledby="mortgageGoodsModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="form-horizontal ajax-form"
                      method="post"
                      data-help-class="col-sm-push-2 col-sm-10" autocomplete="off">
                    <input type="hidden" name="_method" value="put">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="cropperModalLabel">抵陈列费商品修改<span class="extra-text"></span></h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="goods_name">商品名称:</label>

                            <div class="col-sm-10 col-md-8">
                                {{--<input type="text" name="goods_name" class="form-control" placeholder="请输入商品名称"/>--}}
                                <label class="goods-name control-label"></label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label" for="pieces">单位:</label>

                            <div class="col-sm-2 col-md-2">
                                <select name="pieces" class="form-control">
                                    <option value="">请选择单位</option>
                                    @foreach(cons()->valueLang('goods.pieces') as $id=> $pieces)
                                        <option value="{{ $id }}">{{ $pieces }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm btn-close" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary btn-sm" data-text="确定">
                            确定
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            var mortgageGoods = $('#mortgageGoodsModal'),
                    nameControl = mortgageGoods.find('.goods-name'),
                    piecesControl = mortgageGoods.find('select[name="pieces"]'),
                    submitBtn = mortgageGoods.find('button[type="submit"]');
            mortgageGoods.on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget),
                        name = parent.data('name'),
                        url = parent.data('url'),
                        pieces = parent.data('pieces');
                nameControl.text(name);
                piecesControl.val(pieces);
                submitBtn.data('url', url);
            });
        })
    </script>
@stop