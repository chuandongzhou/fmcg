@section('body')
    @parent
    <div class="modal modal1 fade" id="customersModal" tabindex="-1" role="dialog" aria-labelledby="customersModal"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="width:820px;height:700px">
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="myModalLabel">
                        <span>选择客户</span>
                    </div>
                </div>
                <div class="modal-body instead-order-modal ">
                    <div class="warehousing-control-search">
                        <input type="text" name="customer_name" class="control" placeholder="客户名称"/>
                        <input type="button" onclick="customerSearch()" class="control  btn btn-blue-lighter"
                               value="搜索"/>
                    </div>
                    <table class="table table-center salesman-goods-table table-bordered public-table  margin-clear">
                        <thead>
                        <tr>
                            <th>客户名称</th>
                            <th>收货地址</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                    </table>
                    <div class="salesman-table-wrap warehousing-table-wrap">
                        <table class="table table-center salesman-goods-table table-bordered public-table  margin-clear">
                            <tbody id="customerTable">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="text-center page">
                    <ul class="customerModal pagination">
                    </ul>
                </div>
                <div class="modal-footer middle-footer text-right">
                    <button data-dismiss="modal" onclick="chooseCustomer()" type="button" class="btn btn-success">提交
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal1 fade" id="goodsModal" tabindex="-1" role="dialog" aria-labelledby="goodsModal"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="width:820px;">
                <div class="modal-header choice-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <div class="modal-title forgot-modal-title" id="myModalLabel2">
                        <span id="modal-name"></span>
                    </div>
                </div>
                <div class="modal-body">
                    <div id="warehousing-control-search" class="warehousing-control-search" style="display: none">
                        <input type="text" name="searchValue" class="control" placeholder="商品名称/商品条形码"/>
                        <input type="button" name="goods-modal-search" data-type=""
                               class="control  btn btn-blue-lighter" value="搜索"/>
                    </div>
                    <div class="text-center display-date" id="display-date">
                        <input value="{{\Carbon\Carbon::now()->toDateString()}}" data-format="YYYY-MM"
                               placeholder="开始时间" name="displayDate" class="enter control datetimepicker" type="text">
                    </div>
                    <table class="table table-center salesman-goods-table table-bordered public-table  margin-clear">
                        <thead>
                        <tr id="goods-table-title">
                            <th>选择</th>
                            <th>商品名称</th>
                            <th>单位</th>
                            <th>数量</th>
                        </tr>
                        </thead>
                    </table>
                    <div class="salesman-table-wrap warehousing-table">
                        <table class="table table-center salesman-goods-table table-bordered public-table  margin-clear">
                            <tbody id="goodsTable">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="text-center page">
                    <ul class="goodsModal pagination">
                    </ul>
                </div>
                <div class="modal-footer middle-footer text-right">
                    <button id="goods-submit" type="button" class="btn btn-success">提交
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(function () {
            body = $('div.instead-order');
            var customersModal = $('#customersModal'),
                    customerTable = $('#customerTable');

            var goodsModal = $('#goodsModal'),
                    goodsTable = $('#goodsTable'),
                    goodsListContainer = $('.goods-list-container'),
                    displayGoodsParentsContainer = $('.display-goods-parents-container'),
                    displayFeeContainer = $('.display-fee-container'),
                    displayContainer = $('div.display'),
                    giftsListContainer = $('.gifts-list-container'),
                    searchDiv = $('#warehousing-control-search');

            customersModal.on('shown.bs.modal', function (e) {
                var customerName = customersModal.find('input[name = customer_name]').val() || '';
                data = {'name': customerName};
                customerList('customers', data)
            }).on('hide.bs.modal', function (e) {
                //addBtn.prop('disabled', true).unbind('click');
            });

            /**
             * 选择客户
             */
            chooseCustomer = function () {
                choosed = customerTable.find('input[name=choose]:checked ');
                body.find('input[name=client_id]').val(choosed.data('id'));
                body.find('input[name=display_type]').val(choosed.data('display_type'));
                body.find('#customer_name').html(choosed.data('name'));
                body.find('#customer_contact').html(choosed.data('contact'));
                body.find('#customer_contact_info').html(choosed.data('contact_info'));
                body.find('#customer_shipping_address').html(choosed.data('shipping_address'))

                $('div.customer').removeClass('hidden');
                displayContainer.addClass('hidden');
                displayFeeContainer.find('tbody').html('');
                displayGoodsParentsContainer.find('.display-goods-container').find('tbody').html('');
                statistics()
            };

            /**
             *分页
             */
            function customerList(url, data, page, method) {
                customerTable.html('<div class="loading-img" style="text-align:center;" ><img src="' + "{{ asset('images/loading.gif') }}" + '"/></div>');
                var _method = method || 'get',
                        _page = page || 1,
                        _data = data || {};
                $('.page ul li').prop('disabled', true);
                $.ajax({
                    url: site.api(url + '?page=' + _page),
                    method: _method,
                    data: _data
                }).done(function (result) {
                    var pageHtml = makePageHtml(result.last_page, result.current_page);
                    var customerHtml = customersHtml(result.data);
                    customerTable.html(customerHtml);
                    $('.page ul').html(pageHtml);
                    goodsTable.html('');
                });
            }

            /**
             *客户列表Html
             */
            function customersHtml(data) {
                var html = '';
                var chooseCustomerId = body.find('input[name=client_id]').val() || 0;
                for (var i in data) {
                    html += '<tr>';
                    html += '<td>' + data[i].name + '</td>';
                    html += '<td>' + data[i].shipping_address_name + '</td>';
                    html += '<td>' +
                            '<input ' + ((chooseCustomerId == data[i].id) ? 'checked' : '') + ' data-id="' + data[i].id + '" data-display_type="' + data[i].display_type + '" data-name="' + data[i].name + '" data-contact="' + data[i].contact + '" data-contact_info="' + data[i].contact_information + '" data-shipping_address="' + data[i].shipping_address_name + '" type="radio" name="choose">' +
                            '</td>';
                    html += '</tr>'
                }
                return html;
            }

            customerSearch = function () {
                customerName = customersModal.find('input[name = customer_name]').val() || '',
                        data = {'name': customerName};
                customerList('customers', data, 1)
            };

            /**
             * 生成分页按钮
             * @param allPage
             * @param currentPage
             * @returns {string|*|string|string}
             */
            function makePageHtml(allPage, currentPage) {
                //添加分页html
                //前一页
                pageHtml = '';
                if (currentPage == 1) {
                    pageHtml += '<li class="disabled"> <span>«</span></li>';
                } else {
                    pageHtml += '<li data-page="' + (currentPage - 1) + '" > <a>«</a></li>';
                }
                //数字分页信息
                if (allPage < 12) {
                    for (var i = 0; i < allPage; i++) {
                        if ((i + 1) == currentPage) {
                            pageHtml += '<li data-page="' + (i + 1) + '" class="active" ><span>' + (i + 1) + '</span></li>';
                        } else {
                            pageHtml += '<li data-page="' + (i + 1) + '" ><a>' + (i + 1) + '</a></li>';
                        }
                    }
                } else {
                    if (currentPage < 7) {
                        for (var i = 0; i < 8; i++) {
                            if ((i + 1) == currentPage) {
                                pageHtml += '<li data-page="' + (i + 1) + '" class="active" ><span>' + (i + 1) + '</span></li>';
                            } else {
                                pageHtml += '<li data-page="' + (i + 1) + '" ><a>' + (i + 1) + '</a></li>';
                            }
                        }
                        pageHtml += '<li class="disabled"  ><span>...</span></li>' +
                                '<li data-page="' + ( allPage - 1) + '" ><a>' + (allPage - 1) + '</a></li>' +
                                '<li data-page="' + allPage + '" ><a>' + allPage + '</a></li>';

                    } else if (currentPage >= 7 && currentPage <= (allPage - 5)) {
                        pageHtml += '<li data-page="1"><a>1</a></li>' +
                                '<li data-page="2"><a>2</a></li>' +
                                '<li class="disabled"  ><span>...</span></li>' +
                                '<li data-page="' + ( currentPage - 3) + '" ><a>' + (currentPage - 3) + '</a></li>' +
                                '<li data-page="' + ( currentPage - 2) + '" ><a>' + (currentPage - 2) + '</a></li>' +
                                '<li data-page="' + ( currentPage - 1) + '" ><a>' + (currentPage - 1) + '</a></li>' +
                                '<li data-page="' + currentPage + '" class="active" ><span>' + currentPage + '</span></li>' +
                                '<li data-page="' + ( currentPage + 1) + '" ><a>' + (currentPage + 1) + '</a></li>' +
                                '<li data-page="' + ( currentPage + 2) + '" ><a>' + (currentPage + 2) + '</a></li>' +
                                '<li data-page="' + ( currentPage + 3) + '" ><a>' + (currentPage + 3) + '</a></li>' +
                                '<li class="disabled"  ><span>...</span></li>' +
                                '<li data-page="' + ( allPage - 1) + '" ><a>' + (allPage - 1) + '</a></li>' +
                                '<li data-page="' + allPage + '" ><a>' + allPage + '</a></li>';
                    } else {
                        pageHtml += '<li data-page="1"><a>1</a></li>' +
                                '<li data-page="2"><a>2</a></li>' +
                                '<li class="disabled"  ><span>...</span></li>';
                        for (var i = 8; i >= 0; i--) {
                            if ((allPage - i) == currentPage) {
                                pageHtml += '<li data-page="' + (allPage - i) + '" class="active" ><span>' + (allPage - i) + '</span></li>';
                            } else {
                                pageHtml += '<li data-page="' + (allPage - i) + '" ><a>' + (allPage - i) + '</a></li>';
                            }
                        }

                    }

                }

                //后一页
                if (currentPage == allPage) {
                    pageHtml += '<li class="disabled"> <span>»</span></li>';
                } else {
                    pageHtml += '<li data-page="' + (currentPage + 1) + '" ><a>»</a></li>';
                }
                return pageHtml;
            }

            $('.page ul.customerModal').on('click', 'li', function () {
                var obj = $(this);
                var page = obj.data('page');
                customerName = customersModal.find('input[name = customer_name]').val() || '';
                if (page) {
                    data = {'name': customerName};
                    customerList('customers', data, page)
                }
            });

            $('.page ul.goodsModal').on('click', 'li', function () {
                var obj = $(this);
                var page = obj.data('page');
                if (page) {
                    var nameOrCode = $('input[name=nameOrCode]').val() || ''
                    data = {'nameOrCode': nameOrCode};
                    goodsList('my-goods', data, page)
                }
            });


            //-----------------------------------------------------------------------------

            /**
             * 商品modal弹窗处理
             */
            goodsModal.on('shown.bs.modal', function (e) {
                var modalType = $(e.relatedTarget).data('type'),
                        nameOrCode = $('input[name=nameOrCode]').val() || '',
                        chooseCustomerId = body.find('input[name=client_id]').val() || 0,
                        modalNameCtr = $('#modal-name'),
                        goodsTableTitleCtr = $('#goods-table-title'),
                        displayDate = $('input[name = displayDate]'),
                        displayType = $('input[name = display_type]');

                switch (modalType) {
                    case 'goods' :
                        modalName = '添加商品';
                        titleHtml = '<th>选择</th><th>商品名称</th><th>商品条形码</th>'
                        modalNameCtr.html(modalName);
                        searchDiv.show();
                        displayDate.hide();
                        goodsModal.find('#goods-submit').data('type', 'goods');
                        searchDiv.find('input[type=button]').attr('data-type', 'goods');
                        goodsTableTitleCtr.html(titleHtml);
                        goodsList('my-goods', {'nameOrCode': nameOrCode});
                        break;
                    case 'display' :
                        modalName = '选择陈列';
                        modalNameCtr.html(modalName);
                        searchDiv.hide();
                        displayDate.show();
                        if (displayType.val() == '2') {
                            titleHtml = '<th>选择</th><th>商品名称</th><th>单位</th><th>数量</th>'
                            goodsTableTitleCtr.html(titleHtml);
                            goodsModal.find('#goods-submit').data('type', 'displayGoods');
                            searchDiv.find('input[type=button]').attr('data-type', 'displayGoods');
                            displayGoodsList('business/visit/surplus-mortgage-goods', {
                                "customer_id": chooseCustomerId,
                                "month": displayDate.val()
                            });
                        } else if (displayType.val() == '1') {
                            titleHtml = '<th>现金</th>';
                            goodsTableTitleCtr.html(titleHtml);
                            goodsModal.find('#goods-submit').data('type', 'displayFee');
                            searchDiv.find('input[type=button]').attr('data-type', 'displayFee');
                            displayFeeList('business/visit/surplus-display-fee', {
                                "customer_id": chooseCustomerId,
                                "month": displayDate.val()
                            });
                        } else {
                            goodsTable.html('')
                        }
                        break;
                    case 'gifts' :
                        modalName = '选择赠品';
                        titleHtml = '<th>选择</th><th>商品名称</th><th>商品条形码</th>'
                        modalNameCtr.html(modalName);
                        searchDiv.show();
                        displayDate.hide();
                        goodsModal.find('#goods-submit').data('type', 'gifts');
                        searchDiv.find('input[type=button]').attr('data-type', 'gifts');
                        goodsTableTitleCtr.html(titleHtml);
                        giftsList('my-goods/gifts', {'nameOrCode': nameOrCode});
                        break;
                }

            }).on('hide.bs.modal', function (e) {
                goodsTable.html('');
                searchDiv.hide();
            });
            /**
             * 商品列表ajax
             */
            function goodsList(url, data, page, method) {
                goodsTable.html('<div class="loading-img" style="text-align:center;" ><img src="' + "{{ asset('images/loading.gif') }}" + '"/></div>');
                var _method = method || 'get',
                        _page = page || 1,
                        _data = data || {};
                $('.page ul li').prop('disabled', true);
                $.ajax({
                    url: site.api(url + '?page=' + _page),
                    method: _method,
                    data: _data
                }).done(function (result) {
                    var pageHtml = makePageHtml(result.goods.last_page, result.goods.current_page);
                    var goods_html = goodsHtml(result.goods.data);
                    goodsTable.html(goods_html);
                    $('.page ul.goodsModal').html(pageHtml);
                });
            }

            /**
             * 陈列费列表ajax
             */
            function displayFeeList(url, data, page, method) {
                goodsTable.html('<div class="loading-img" style="text-align:center;" ><img src="' + "{{ asset('images/loading.gif') }}" + '"/></div>');
                var _method = method || 'get',
                        _page = page || 1,
                        _data = data || {};
                $('.page ul li').prop('disabled', true);


                $.ajax({
                    url: site.api(url + '?page=' + _page),
                    method: _method,
                    data: _data
                }).done(function (result) {
                    var display_fee_html = displayFeeHtml(result);
                    goodsTable.html(display_fee_html);
                    $('.page ul.goodsModal').html('');
                }).fail(function (jqXHR) {
                    goodsTable.html('')
                    var json = jqXHR['responseJSON'];
                    if (json) {
                        setTimeout(function () {
                            goodsTable.html(json['message']);
                        }, 0);
                    }
                });


            }

            /**
             * 陈列费商品ajax
             */
            function displayGoodsList(url, data, page, method) {
                goodsTable.html('<div class="loading-img" style="text-align:center;" ><img src="' + "{{ asset('images/loading.gif') }}" + '"/></div>');
                var _method = method || 'get',
                        _page = page || 1,
                        _data = data || {};
                $('.page ul li').prop('disabled', true);
                $.ajax({
                    url: site.api(url + '?page=' + _page),
                    method: _method,
                    data: _data
                }).done(function (result) {
                    var display_goods_html = displayGoodsHtml(result.surplus);
                    goodsTable.html(display_goods_html);
                    $('.page ul.goodsModal').html('');
                }).fail(function (jqXHR) {
                    goodsTable.html('')
                    var json = jqXHR['responseJSON'];
                    if (json) {
                        setTimeout(function () {
                            goodsTable.html(json['message']);
                        }, 0);
                    }
                });
            }

            /**
             * 赠品ajax
             */
            function giftsList(url, data, page, method) {
                goodsTable.html('<div class="loading-img" style="text-align:center;" ><img src="' + "{{ asset('images/loading.gif') }}" + '"/></div>');
                var _method = method || 'get',
                        _page = page || 1,
                        _data = data || {};
                $('.page ul li').prop('disabled', true);
                $.ajax({
                    url: site.api(url + '?page=' + _page),
                    method: _method,
                    data: _data
                }).done(function (result) {
                    var gifts_html = giftsHtml(result);
                    goodsTable.html(gifts_html);
                    $('.page ul.goodsModal').html('');
                });
            }

            /**
             * 赠品HTML
             */
            function giftsHtml(data) {
                var html = '';
                for (var i in data) {
                    html += '<tr>';
                    html += '<td><input ' +
                            'data-id="' + data[i].id + '" ' +
                            'data-img="' + data[i].image_url + '" ' +
                            'data-name="' + data[i].name + '" ' +
                            'data-pieces="' + data[i].pieces_list + '" ' +
                            'data-pieces_lang="' + data[i].pieces_lang_list + '" ' +
                            'data-surplus_inventory="' + data[i].surplus_inventory + '" ' +
                            'data-cost_tips="' + data[i].cost_tips + '" ' +
                            'name="gifts" type="checkbox"></td>';
                    html += '<td>' + data[i].name + '</td>';
                    html += '<td>' + data[i].bar_code + '</td>'
                    html += '</tr>'
                }
                return html;
            }

            /**
             * 陈列费商品HTML
             */
            function displayGoodsHtml(data) {
                var html = '';
                for (var i in data) {
                    html += '<tr>';
                    html += '<td><input data-goods_id="' + data[i].goods_id + '" data-id="' + data[i].id + '" data-img_url="' + data[i].img_url + '" data-name="' + data[i].name + '" data-pieces="' + data[i].pieces + '" data-pieces_lang="' + data[i].pieces_name + '" data-surplus="' + data[i].surplus + '" name="display_goods" type="checkbox"></td>';
                    html += '<td>' + data[i].name + '</td>';
                    html += '<td>' + data[i].pieces_name + '</td>';
                    html += ' <td><p class="new-col">';
                    html += '<input class="number" name="display-goods-num" type="text" placeholder="请输入数量"> ';
                    html += '<span class="tips">剩余总量:' + data[i].surplus + '</span> ';
                    html += '</p></td></tr>'
                }
                return html;
            }

            /**
             * 陈列费HTML
             */
            function displayFeeHtml(data) {
                var html = '';
                html += '<tr>';
                html += ' <td><p class="new-col">';
                html += '<input class="number" data-surplus="' + data.surplus + '" data-no_confirm_sum="' + data.noConfirmSum + '" name="display_fee" type="text" placeholder="请输入数量"> ';
                html += '<span class="tips">剩余陈列费:' + data.surplus + ' 未审核陈列:' + data.noConfirmSum + '</span> ';
                html += '</p></td></tr>';
                return html;
            }

            /**
             * 商品列表HTML
             */
            function goodsHtml(data) {
                var html = '';
                //var chooseGoodsId = body.find('input[name=customer_id]').val() || 0;
                for (var i in data) {
                    html += '<tr>';
                    html += '<td><input ' +
                            'data-id="' + data[i].id + '" ' +
                            'data-img="' + data[i].image_url + '" ' +
                            'data-name="' + data[i].name + '" ' +
                            'data-pieces="' + data[i].pieces_list + '" ' +
                            'data-pieces_lang="' + data[i].pieces_lang_list + '" ' +
                            'data-surplus_inventory="' + data[i].surplus_inventory + '" ' +
                            'data-cost_tips="' + data[i].cost_tips + '" ' +
                            'data-price_retailer="' + data[i].price_retailer + '" ' +
                            'name="goods" type="checkbox"></td>';
                    html += '<td>' + data[i].name + '</td>';
                    html += '<td>' + data[i].bar_code + '</td>'
                    html += '</tr>'
                }
                return html;
            }

            /**
             *搜索事件处理
             */
            $(searchDiv).on('click', 'input[name=goods-modal-search]', function () {
                var type = $(this).data('type'),
                        searchValue = $('input[name=searchValue]').val() || ''
                switch (type) {
                    case 'goods' :
                        goodsList('my-goods', {'nameOrCode': searchValue});
                        break;
                    case 'display':
                        break;
                    case 'gifts':
                        giftsList('my-goods/gifts', {'nameOrCode': searchValue});
                        break;
                }
            });


            /**
             *选择商品处理
             */
            $(goodsModal).on('click', 'button#goods-submit', function () {
                var type = $('button#goods-submit').data('type');
                switch (type) {
                    case 'goods' :
                        /**
                         * 选择商品
                         */
                        html = '';
                        goodsTable.find('input[name=goods]:checked').each(function () {
                            var self = $(this),
                                    id = self.data('id'),
                                    img = self.data('img'),
                                    name = self.data('name'),
                                    pieces = String(self.data('pieces')).split(','),

                                    pieces_lang = String(self.data('pieces_lang')).split(','),
                                    surplus_inventory = self.data('surplus_inventory'),
                                    cost_tips = self.data('cost_tips'),
                                    price_retailer = self.data('price_retailer');

                            html += '<tr class="goods"><td class="goods_id">' + id + '</td>'
                            html += '<td><img class="store-img" src=' + img + '> <a class="product-name outhousing-product-name" href="javascript:;">' + name + '</a></td>'
                            html += '<td><select name="goods[' + id + '][pieces]">';
                            html += '<option>请选择</option>';
                            for (i in pieces_lang) {
                                html += '<option value="' + pieces[i] + '">' + pieces_lang[i] + '</option>'
                            }
                            html += '</select> </td> ';
                            html += '<td> <p class="new-col">';
                            html += '<input name="goods[' + id + '][price]" class="number goods_price" type="text" value="' + price_retailer + '" placeholder=""> '
                            html += '<span class="tips">' + cost_tips + '</span> '
                            html += ' </p></td> <td>';
                            html += ' <p class="new-col">';
                            html += '<input name="goods[' + id + '][num]" class="number goods_num" type="text" value="1" placeholder=""> '
                            html += '<span class="tips">剩余库存:' + surplus_inventory + '</span> '
                            html += '</p></td><td><a class="red goods-delete">删除</a></td> </tr>'
                        });
                        goodsListContainer.append(html);
                        goodsListContainer.parents('div.goods').removeClass('hidden');
                        break;
                    case 'displayGoods':
                        /**
                         * 选择陈列费商品
                         */

                        html = '';
                        goodsTable.find('input[name=display_goods]:checked').each(function () {
                            var self = $(this),
                                    goods_id = self.data('goods_id'),
                                    id = self.data('id'),
                                    img_url = self.data('img_url'),
                                    name = self.data('name'),
                                    pieces = self.data('pieces'),

                                    pieces_lang = self.data('pieces_lang'),
                                    surplus = self.data('surplus'),
                                    num = self.parents('tr').find('input[name=display-goods-num]').val();
                            displayDate = $('input[name = displayDate]').val();
                            html += '<tr><td>' + displayDate + '</td>';
                            html += '<td>' + goods_id + '</td>';
                            html += '<td><img class="store-img" src="' + img_url + '"> <a class="product-name outhousing-product-name" href="javascript:;">' + name + '</a> </td>';
                            html += '<td> <input type="hidden" name="display_goods[' + displayDate + '][' + goods_id + '][id]" value="' + id + '">' + pieces_lang + '</td>';
                            html += '<td><p class="new-col">';
                            html += '<input name="display_goods[' + displayDate + '][' + goods_id + '][num]" class="number" type="text" value="' + num + '">';
                            html += '<span class="tips">剩余总量:' + surplus + '</span></p></td>';
                            html += '<td><a class="red goods-delete">删除</a></td></tr>';
                        });

                        displayGoodsParentsContainer.find('.display-goods-container').append(html);
                        displayGoodsParentsContainer.find('.display-goods-container').show();
                        displayContainer.removeClass('hidden');
                        displayFeeContainer.hide();

                        break;
                    case 'displayFee':
                        var html = '';
                        var displayFee = goodsTable.find('input[name=display_fee]'),
                                displayDate = $('input[name = displayDate]').val(),
                                flag = 1;
                        if (displayFee.val() > (displayFee.data('surplus') - displayFee.data('no_confirm_sum'))) {
                            alert('陈列费不能大于剩余陈列费');
                            return false;
                        }
                        displayFeeContainer.find('input[name=display_date]').each(function () {
                            if ($(this).val() == displayDate) {
                                $(this).parents('tr').find('input.display-fee').val(displayFee.val());
                                flag = 0;
                            }
                        });
                        if (flag == 1) {
                            html += '<tr class="display_fee"><td><input name="display_date" type="text" data-type="display" data-target="#goodsModal" data-toggle="modal" placeholder="选择时间" value="' + displayDate + '"/></td>';
                            html += '<td>';
                            html += '<p class="new-col">';
                            html += '<input name="display_fee[' + displayDate + ']" class="number display-fee" type="text" value="' + displayFee.val() + '" placeholder="' + (displayFee.data('surplus') - displayFee.data('no_confirm_sum')) + '">';
                            html += '<span class="tips">剩余陈列费:' + displayFee.data('surplus') + ' 未审核陈列:' + displayFee.data('no_confirm_sum') + '</span>';
                            html += '</p>';
                            html += ' </td>';
                            html += '<td><a class="red goods-delete">删除</a></td></tr>';
                            displayContainer.removeClass('hidden');
                            displayGoodsParentsContainer.hide();
                            displayFeeContainer.show();
                            displayFeeContainer.append(html);
                        }
                        break;
                    case 'gifts':
                        html = '';
                        goodsTable.find('input[name=gifts]:checked').each(function () {
                            var self = $(this),
                                    id = self.data('id'),
                                    img = self.data('img'),
                                    name = self.data('name'),
                                    pieces = self.data('pieces').split(','),
                                    pieces_lang = self.data('pieces_lang').split(','),
                                    surplus_inventory = self.data('surplus_inventory'),
                                    cost_tips = self.data('cost_tips');

                            html += '<tr><td>' + id + '</td>'
                            html += '<td><img class="store-img" src=' + img + '> <a class="product-name outhousing-product-name" href="javascript:;">' + name + '</a></td>'
                            html += '<td><select name="gifts[' + id + '][pieces]">';
                            html += '<option >请选择</option>';
                            for (i in pieces_lang) {
                                html += '<option value="' + pieces[i] + '">' + pieces_lang[i] + '</option>'
                            }
                            html += '</select> </td> ';
                            html += '<td> <p class="new-col">';
                            html += '<input name="gifts[' + id + '][num]" class="number" type="text" placeholder=""> '
                            html += '<span class="tips">剩余库存:' + surplus_inventory + '</span> '
                            html += '</p></td><td><a class="red goods-delete">删除</a></td> </tr>'
                        });
                        giftsListContainer.append(html);
                        giftsListContainer.parents('div.gifts').removeClass('hidden');
                        break;
                }
                //**金额计算
                goodsModal.modal('hide');
                statistics()
            });
            /**
             * 价格计算
             */
            function  statistics() {
                var goodsTotal = 0,
                        displayFeeTotal = 0;
                $('tr.goods').each(function () {
                    var goods_price = parseInt($(this).find('input.goods_price').val()) || 1,
                            goods_num = parseInt($(this).find('input.goods_num').val()) || 1;
                    goodsTotal += (goods_price * goods_num);
                });

                $('tr.display_fee').each(function () {
                    displayFeeTotal += parseInt($(this).find('input.display-fee').val());
                });
                var statisticsContainer = $('div.statistics');

                statisticsContainer.find('b.goods-total').html('￥' + goodsTotal);
                statisticsContainer.find('input[name=amount]').val(goodsTotal);
                statisticsContainer.find('b.display-fee').html('-￥' + displayFeeTotal);
                statisticsContainer.find('b.total_amount').html('￥' + (goodsTotal - displayFeeTotal))
            }


            $('.instead-order').on('change', 'input[type=text]', function () {
                statistics()
            });

            /**
             * 页面通用删除
             */
            $(body).on('click', 'a.goods-delete', function () {
                var div = $(this).parents('div.panel-default');
                if (confirm('确定删除?')) {
                    if (div.find('tr').length == 2) {
                        div.addClass('hidden');
                    }
                    $(this).parents('tr').remove();
                }
                statistics()
            });

            $(goodsModal).on('blur', 'input[name=displayDate]', function () {
                var displayDate = $('input[name = displayDate]'),
                        displayType = $('input[name = display_type]'),
                        chooseCustomerId = body.find('input[name=client_id]').val() || 0;
                if (displayType.val() == '2') {
                    displayGoodsList('business/visit/surplus-mortgage-goods', {
                        "customer_id": chooseCustomerId,
                        "month": displayDate.val()
                    });
                } else if (displayType.val() == '1') {
                    displayFeeList('business/visit/surplus-display-fee', {
                        "customer_id": chooseCustomerId,
                        "month": displayDate.val()
                    });
                } else {
                    goodsTable.html('')
                }
            });

        })
    </script>
@stop