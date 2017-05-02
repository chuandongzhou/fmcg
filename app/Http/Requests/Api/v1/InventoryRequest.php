<?php

namespace App\Http\Requests\Api\v1;

class InventoryRequest extends Request
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * 自定义验证
     *
     * @param $factory
     * @return mixed
     */
    public function validator($factory)
    {
        return $this->defaultValidator($factory)->after(function ($validator) {
            $action = $this->input('action_type');
            foreach ($this->only('goods') as $key => $value) {
                foreach ($value as $k => $v) {
                    if ($action === cons('inventory.action_type.in')) {
                        foreach ($v['production_date'] as $key => $value) {
                            if (!empty($value)) {
                                $is_date = strtotime($value) ? strtotime($value) : false;
                                if ($is_date === false) {
                                    $validator->errors()->add('product_date', '生产日期 不是一个正确的日期格式');
                                }
                            } else {
                                $validator->errors()->add('product_date', '生产日期 不能为空');
                            }
                        }
                    }
                    foreach ($v['cost'] as $key => $value) {
                        if (empty($value)) {
                            $validator->errors()->add('cost',
                                ($action == cons('inventory.action_type.in') ? '成本' : '出库') . '价 不能为空');
                        } elseif (!is_numeric($value)) {
                            $validator->errors()->add('cost',
                                ($action == cons('inventory.action_type.in') ? '成本' : '出库') . '价 必须是数字');
                        }
                    }
                    foreach ($v['quantity'] as $key => $value) {
                        if (empty($value)) {
                            $validator->errors()->add('quantity',
                                ($action == cons('inventory.action_type.in') ? '入库' : '出库') . '数量 不能为空');
                        } elseif (!is_numeric($value)) {
                            $validator->errors()->add('quantity',
                                ($action == cons('inventory.action_type.in') ? '入库' : '出库') . '数量 必须是数字');
                        }
                    }
                    foreach ($v['pieces'] as $key => $value) {
                        if (empty($value) || $value == '请选择') {
                            $validator->errors()->add('pieces', '单位 必需选择');
                        }
                    }
                }
            }

        });
    }
}
