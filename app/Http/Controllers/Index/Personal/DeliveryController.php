<?php
namespace App\Http\Controllers\Index\Personal;

use App\Http\Controllers\Index\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{

    /**
     * 配送历史查询．
     *
     * @param \Illuminate\Http\Request $request
     * $return \Illuminate\View\View
     */
    public function historyDelivery(Request $request)
    {
        $search = $request->all();
        $search['start_at'] = isset($search['start_at']) ? $search['start_at'] : '';
        $search['end_at'] = isset($search['end_at']) ? $search['end_at'] : '';
        $search['delivery_man_id'] = isset($search['delivery_man_id']) ? $search['delivery_man_id'] : '';

        $delivery = Order::where('shop_id',
            auth()->user()->shop->id)->whereNotNull('delivery_finished_at')->where(function ($query) use ($search) {
            if ($search['delivery_man_id'] && is_numeric($search['delivery_man_id'])) {
                $query->where('delivery_man_id', $search['delivery_man_id']);
            }
            if ($search['start_at']) {
                $query->where('delivery_finished_at', '>', $search['start_at']);
            }
            if ($search['end_at']) {
                $query->where('delivery_finished_at', '<', $search['end_at']);
            }
        })->with('user.shop', 'shippingAddress.address', 'deliveryMan')->orderBy('delivery_finished_at','DESC')->paginate();

        $deliveryMen = auth()->user()->shop->deliveryMans;
        return view('index.personal.delivery',
            ['deliveryMen' => $deliveryMen, 'deliveries' => $delivery, 'search' => $search]);
    }
}