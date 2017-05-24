<?php

namespace App\Services;

class PromoService extends BaseService
{
    /**
     * 促销搜索
     * @param $promo
     * @param $data
     */
    public function search($promo,$data)
    {
        if ($numberName = array_get($data,'number_name')){
            $promo->OfNumberName($numberName);
        }
        if ($start_at = array_get($data,'start_at')){
            $promo->where('start_at','>=',$start_at);
        }
        if ($end_at = array_get($data,'end_at')){
            $promo->where('end_at','>=',$end_at);
        }
        return $promo;
    }


    public function applyLogSearch($applyLog,$data)
    {
        if($start_at = array_get($data,'start_at')){
            $applyLog->where('promo_apply.created_at','>=',$start_at);
        }
        if($end_at = array_get($data,'end_at')){
            $applyLog->where('promo_apply.created_at','<=',$end_at);
        }
        if($number_name = array_get($data,'number_name')){
            $applyLog->OfNumberName($number_name);
        }
        if($salesman = array_get($data,'salesman')){
            $applyLog->OfSalesman($salesman);
        }
        if($status = !empty($data['status'])){
            $applyLog->where('promo_apply.status',$status);
        }
        return $applyLog;
    }
    
    /**
     * 添加促销
     * @param $data
     */
    public function add($data)
    {
        $type = array_get($data,'type');
        $condition = array_get($data,'condition');
        $rebate = array_get($data,'rebate');
        $param = array_except($data,['condition','rebate']);
        $promo = auth()->user()->shop->promo()->create($param);
        $condition['type'] = cons('promo.content_type.condition');
        $rebate['type'] = cons('promo.content_type.rebate');
        $this->addContent($promo,$type,$condition,$rebate);
    }

    /**
     * 编辑促销
     * @param $promo
     * @param $data
     */
    public function edit($promo,$data)
    {
        $type = array_get($data,'type');
        $condition = array_get($data,'condition');
        $rebate = array_get($data,'rebate');
        $param = array_except($data,['condition','rebate']);
        $promo->fill($param)->save();
        $promo->condition()->delete();
        $promo->rebate()->delete();
        $condition['type'] = cons('promo.content_type.condition');
        $rebate['type'] = cons('promo.content_type.rebate');
        $this->addContent($promo,$type,$condition,$rebate);
    }

    /**
     * 添加内容 条件/返利
     * @param $promo
     * @param $type
     * @param $condition
     * @param $rebate
     */
    public function addContent($promo,$type,$condition,$rebate)
    {
        switch ($type){
            case cons('promo.type.custom') :      //自定义
                $promo->condition()->create($condition);
                $promo->rebate()->create($rebate);
                break;
            case cons('promo.type.money-money') : //钱返钱
                $promo->condition()->create($condition);
                $promo->rebate()->create($rebate);
                break;
            case cons('promo.type.money-goods') : //钱返商品
                $promo->condition()->create($condition);
                foreach ($rebate['goods_id'] as $key => $value){
                    $_data['type'] = $rebate['type'];
                    $_data['goods_id'] = $rebate['goods_id'][$key];
                    $_data['quantity'] = $rebate['quantity'][$key];
                    $_data['unit'] = $rebate['unit'][$key];
                    $promo->rebate()->create($_data);
                }
                break;
            case cons('promo.type.goods-money') : //商品返钱
                foreach ($condition['goods_id'] as $key => $value){
                    $_data['type'] = $condition['type'];
                    $_data['goods_id'] = $condition['goods_id'][$key];
                    $_data['quantity'] = $condition['quantity'][$key];
                    $_data['unit'] = $condition['unit'][$key];
                    $promo->condition()->create($_data);
                }
                $promo->rebate()->create($rebate);
                break;
            case cons('promo.type.goods-goods') : //商品返商品
                foreach ($condition['goods_id'] as $key => $value){
                    $_data['type'] = $condition['type'];
                    $_data['goods_id'] = $condition['goods_id'][$key];
                    $_data['quantity'] = $condition['quantity'][$key];
                    $_data['unit'] = $condition['unit'][$key];
                    $promo->condition()->create($_data);
                }
                foreach ($rebate['goods_id'] as $key => $value){
                    $_data['type'] = $rebate['type'];
                    $_data['goods_id'] = $rebate['goods_id'][$key];
                    $_data['quantity'] = $rebate['quantity'][$key];
                    $_data['unit'] = $rebate['unit'][$key];
                    $promo->condition()->create($_data);
                }
                break;
        }
    }
}