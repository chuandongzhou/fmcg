<?php

namespace App\Http\Requests\Api\v1;


class CreatePromoRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:30',
            'start_at' => 'required|date',
            'end_at' => 'required|date',
            'type' => 'in:1,2,3,4,5',
            // 'remark' => 'required',
        ];
    }

    /**
     * 自定义验证
     *
     * @param \Illuminate\Contracts\Validation\Factory $factory
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator($factory)
    {
        $rebate = $this->input('rebate');
        $condition = $this->input('condition');
        return $this->defaultValidator($factory)->after(function ($validator) use ($rebate, $condition) {
            if (empty($this->input('type'))) {
                $validator->errors()->add('type', '请选择促销类型');
            }
            switch ($this->input('type')) {
                case cons('promo.type.custom'):
                    if (empty($condition['custom'])) {
                        $validator->errors()->add('condition[custom]', '条件 不能为空');
                    }
                    if (empty($rebate['custom'])) {
                        $validator->errors()->add('rebate[custom]', '返利 不能为空');
                    }
                    break;
                case cons('promo.type.money-money'):
                    $this->checkMoney($validator, $condition, false);
                    $this->checkMoney($validator, $rebate, true);
                    break;
                case cons('promo.type.money-goods'):
                    $this->checkMoney($validator, $condition, false);
                    $this->checkGoods($validator, $rebate, true);
                    break;
                case cons('promo.type.goods-money'):
                    $this->checkGoods($validator, $condition, false);
                    $this->checkMoney($validator, $rebate, true);
                    break;
                case cons('promo.type.goods-goods'):
                    $this->checkGoods($validator, $condition, false);
                    $this->checkGoods($validator, $rebate, true);
                    break;

            }
        });
    }

    private function checkGoods($validator, $products, $isRebate)
    {
        if (count($products) < 1) {
            return $validator->errors()->add('error', ($isRebate ? '返利' : '条件') . '商品 不能为空');
        } else {
            foreach ($products['goods_id'] as $key => $value) {
                if (strlen(trim($products['unit'][$key])) == 0) {
                    return $validator->errors()->add('error', ($isRebate ? '返利' : '条件') . '商品单位 不能为空');
                }
                if (!isset($products['quantity'][$key]) || empty($products['quantity'][$key])) {
                    return $validator->errors()->add('error', ($isRebate ? '返利' : '条件') . '商品数量 不能为空');
                }
            }
        }
    }

    private function checkMoney($validator, $money, $isRebate)
    {
        if (empty($money['money']) && $money['money'] != 0) {
            return $validator->errors()->add('condition[money]', ($isRebate ? '返利' : '条件') . '金额 不能为空');
        }
        if (!is_numeric(trim($money['money']))) {
            return $validator->errors()->add('condition[money]', ($isRebate ? '返利' : '条件') . '金额 请输入数字');
        }
    }
}
