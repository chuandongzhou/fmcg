/**
 * 提现订单操作事件
 */
function withdrawOperateEvents() {
    $('.btn-add').prop('disabled', true);
    $('table').on('click', '.rollback', function () {
        var withdraw_id = $(this).attr('data-id');
        var reason = '';
        $('#rollback').on('keyup', 'textarea[name="reason"]', function () {
            reason = $(this).val();
            if (reason) {
                $('#rollback').find('.btn-add').prop('disabled', false).attr('data-data', '{"withdraw_id":' + withdraw_id + ',"reason":"' + reason + '"}');
            }
        });
    })
        .on('click', '.payment', function () {
            var withdraw_id = $(this).attr('data-id');
            var tradeNo = '';
            $('#payment').on('keyup', 'input[name="tradeNo"]', function () {
                tradeNo = $(this).val();
                if (tradeNo) {
                    $('#payment').find('.btn-add').prop('disabled', false).attr('data-data', '{"withdraw_id":' + withdraw_id + ',"trade_no":"' + tradeNo + '"}');
                }
            });
        });
}