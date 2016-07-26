<?php
namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use App\Models\Shop;
use Carbon\carbon;
use App\Models\Coupon;
class CouponController extends Controller
{
    /**我的优惠券
     *  @return \WeiHeng\Responses\Apiv1Response
     */
    public function index(Request $request){
        $coupons = auth()->user()->load(['coupon' => function ($query) {
            $query ->time()->whereNull('used_at')->orderBy('end_at', 'desc');
        },'coupon.shop']);
        return $this->success($coupons);
    }
    /**店铺可领取优惠券
     *  @return \WeiHeng\Responses\Apiv1Response
     */
    public function recevieCoupons(Request $request){
        $shopid = $request['shopid'];
        $ids = array();
        $receivedCoupons = auth()->user()->load(['coupon' => function($query) use ($shopid){
            $query->where('shop_id',$shopid);
        }]);
        foreach($receivedCoupons->coupon as $coupon){
            $ids[] = $coupon->id;
        }
        $canReceiveCoupons = Shop::where('id',$shopid)->with(['coupons' => function ($query) use($ids) {
            $query->whereNotIn('id',$ids)
               ->time()
                ->where('stock','>',0)
                ->orderBy('end_at', 'desc');
        },'coupons.shop'])->get();
        return $this->success($canReceiveCoupons);
    }
    /**
     * 领取优惠券
     * @return \WeiHeng\Responses\Apiv1Response
     */
    public function store(Request $request){
        $coupon_id = $request->input('coupon_id');
        auth()->user()->coupon()->attach($coupon_id, ['received_at' => Carbon::now()]);
        return $this->success('领取成功');
    }
}