<?php

namespace App\Models;

use App\Services\GoodsService;
use App\Services\InventoryService;
use Carbon\Carbon;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $fillable = [
        'inventory_number',  //库存编号
        'user_id',           //操作人
        'operate',
        'source',            //库存来源(订单,赠品,促销,陈列...)
        'goods_id',          //商品
        'shop_id',           //所属店铺
        'order_number',      //订单
        'inventory_type',    //库存类型(系统\手动)
        'action_type',       //动作 (出库\入库)
        'production_date',   //商品生产日期
        'pieces',            //规格单位
        'cost',              //入库成本/出库价格
        'quantity',          //记录本次操作数量
        'surplus',           //剩余库存
        'remark',            //备注
        'in_id'              //入库ID(具体是用那条记录出的库便于计算成本)
    ];

    protected $appends = [];

    protected $hidden = [];

    /**
     * 商品关联
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     */
    public function goods()
    {
        return $this->belongsTo('App\Models\Goods');
    }

    /**
     * 起源
     *
     * @return mixed
     */
    public function parent()
    {
        return $this->hasOne(Inventory::class, 'id', 'in_id')->where('action_type', cons('inventory.action_type.in'));
    }

    /**
     * 订单关联
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_number');
    }

    /**
     * 用户关联
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * 仓管员关联
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouseKeeper()
    {
        return $this->belongsTo('App\Models\WarehouseKeeper', 'user_id');
    }

    /**
     * 验证店铺
     *
     * @param $query
     * @return mixed
     */
    public function scopeVerifyShop($query)
    {
        return $query->where('shop_id', auth()->user()->shop_id);
    }

    /**
     * 筛选入库
     *
     * @param $query
     * @return mixed
     */
    public function scopeOfIn($query)
    {
        return $query->where('action_type', cons('inventory.action_type.in'));
    }

    /**
     * 筛选出库
     *
     * @param $query
     * @return mixed
     */
    public function scopeOfOut($query)
    {
        return $query->where('action_type', cons('inventory.action_type.out'));
    }

    /**
     * 以编号搜索
     *
     * @param $query
     * @return mixed
     */
    public function scopeOfNumber($query, $number)
    {
        return $query->where(function ($query) use ($number) {
            $query->where('inventory_number', 'LIKE', '%' . $number . '%')
                ->orWhere('order_number', 'LIKE', '%' . $number . '%');
        });
    }

    /**
     * 以当月检索
     *
     * @param $query
     * @return mixed
     */
    public function scopeOfNowMonth($query)
    {
        return $query->where('created_at', '>=', (new Carbon)->startOfMonth()->toDateTimeString())
            ->where('created_at', '<=', (new Carbon)->endOfDay()->toDateTimeString());
    }

    /**
     * 获得转换后的出入库数量
     *
     * @return int|string
     */
    public function getTransformationQuantityAttribute()
    {
        return InventoryService::calculateQuantity($this->goods, $this->quantity);
    }

    /**
     * 获得入库成本
     *
     * @return mixed
     */
    public function getInCostAttribute()
    {
        return $this->parent->cost ?? 0;
    }


    /**
     * 获得入库实际成本
     *
     * @return mixed
     */
    private function getInActualCost()
    {
        $inv = $this->parent;
        //进制
        if ($inv) {
            $system = GoodsService::getPiecesSystem($inv->goods, $inv->pieces);
            return ($inv->cost / $system) ?? 0;
        }
    }


    /**
     * 获得入库单位
     *
     * @return mixed
     */
    public function getInPiecesAttribute()
    {
        return $this->parent->pieces ?? 0;
    }

    /*
     * 获取商品名
     */
    public function getGoodsNameAttribute()
    {
        return $this->goods->name;
    }

    /*
     * 获取商品条形码
     */
    public function getGoodsBarcodeAttribute()
    {
        return $this->goods->bar_code;
    }

    /**
     * 获得盈利额
     *
     * @return string
     */
    public function getProfitAttribute()
    {
        $system = GoodsService::getPiecesSystem($this->goods, $this->pieces);
        return sprintf("%.2f", $this->quantity * (($this->cost / $system) - $this->getInActualCost()));
    }

    /**
     * 获取买家名
     *
     * @return mixed
     */
    public function getBuyerNameAttribute()
    {
        if ($this->order_number) {
            return $this->order->user_shop_name;
        }
    }

    /**
     * 获取卖家名
     *
     * @return mixed
     */
    public function getSellerNameAttribute()
    {
        if ($this->order_number) {
            return $this->order->shop_name;
        }
    }

    /**
     * 获取配送人员名
     *
     * @return mixed
     */
    public function getDeliveryNameAttribute()
    {
        if ($this->order_number) {
            $name = [];
            if ($this->order->dispatchTruck) {
                $deliveryMans = $this->order->dispatchTruck->deliveryMans;
            } else {
                $deliveryMans = $this->order->deliveryMan;
            }
            $deliveryMans->each(function ($deliveryMan) use (&$name) {
                $name[] = $deliveryMan->name;
            });
            return implode(',', $name);
        }
    }

    /**
     * 获取操作人员名
     *
     * @return mixed
     */
    public function getOperatorNameAttribute()
    {
        if (!$this->user_id || is_null($this->operate)) {
            return '系统';
        }
        return ($this->operate == 'warehouseKeeper' ? '(仓管)' : '') . $this->{$this->operate}->{$this->operate == 'user' ? 'user_name' : 'name'};
    }
}
