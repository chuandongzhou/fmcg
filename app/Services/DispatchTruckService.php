<?php

namespace App\Services;

class DispatchTruckService
{
    /**
     * 订单商品统计逻辑
     *
     * @param $orders
     * @return array
     */
    static public function goodsStatistical($orders)
    {
        $tmpArray = [];
        $resultArray = [];
        if (count($orders)) {
            foreach ($orders as $order) {
                foreach ($order->orderGoods as $orderGoods) {
                    if (!array_key_exists($orderGoods->goods_id, $tmpArray)) {
                        $tmpArray[$orderGoods->goods_id] = [
                            'goods' => $orderGoods->goods,
                            'name' => $orderGoods->goods_name,
                            'quantity' => $orderGoods->num * GoodsService::getPiecesSystem($orderGoods->goods,
                                    $orderGoods->pieces),
                            'frequency' => [$order->id],
                        ];
                    } else {
                        $tmpArray[$orderGoods->goods_id]['quantity'] += $orderGoods->num * GoodsService::getPiecesSystem($orderGoods->goods,
                                $orderGoods->pieces);
                        if (!in_array($order->id, $tmpArray[$orderGoods->goods_id]['frequency'])) {
                            $tmpArray[$orderGoods->goods_id]['frequency'][] = $order->id;
                        };
                    }
                }
            }
            foreach ($tmpArray as $data) {
                $resultArray[] = [
                    'goods_id' => $data['goods']->id,
                    'name' => $data['name'],
                    'type' => 1,
                    'img_url' => $data['goods']->image_url,
                    'quantity' => InventoryService::calculateQuantity($data['goods'], $data['quantity']),
                    'frequency' => count($data['frequency'])
                ];
            }
        }
        return $resultArray;
    }

    /**
     * 退货订单商品统计逻辑
     *
     * @param $orders
     * @return array
     */
    static public function returnOrderGoodsStatistical($orders)
    {
        $tmpArray = [];
        $resultArray = [];
        if (count($orders)) {
            foreach ($orders as $order) {
                if (!array_key_exists($order->goods_id, $tmpArray)) {
                    $tmpArray[$order->goods_id] = [
                        'goods' => $order->goods,
                        'name' => $order->goods_name,
                        'quantity' => $order->num * GoodsService::getPiecesSystem($order->goods, $order->pieces),
                        'frequency' => [$order->order_id],
                    ];
                } else {
                    $tmpArray[$order->goods_id]['quantity'] += $order->num * GoodsService::getPiecesSystem($order->goods,
                            $order->pieces);
                    if (!in_array($order->order_id, $tmpArray[$order->goods_id]['frequency'])) {
                        $tmpArray[$order->goods_id]['frequency'][] = $order->order_id;
                    };
                }
            }
            foreach ($tmpArray as $data) {
                $resultArray[] = [
                    'goods_id' => $data['goods']->id,
                    'name' => $data['name'],
                    'type' => 2,
                    'img_url' => $data['goods']->image_url,
                    'quantity' => InventoryService::calculateQuantity($data['goods'], $data['quantity']),
                    'frequency' => count($data['frequency'])
                ];
            }
        }
        return $resultArray;
    }
}