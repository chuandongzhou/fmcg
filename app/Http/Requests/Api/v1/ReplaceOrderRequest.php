<?php

namespace App\Http\Requests\Api\v1;

use WeiHeng\Responses\ApiResponse;

class ReplaceOrderRequest extends Request
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
            $displayFee = $this->input('display_fee');
            $displayGoods = $this->input('display_goods');
            $gifts = $this->input('gifts');
            try {
                foreach ($this->input('goods') as $goods_id => $goods) {
                    if ($goods['pieces'] == '请选择' || $goods['pieces'] == '') {
                        $validator->errors()->add('goods[' . $goods_id . '][pieces]', '请选择单位');
                    }
                    if ($goods['price'] == '' || $goods['price'] < 1) {
                        $validator->errors()->add('goods[' . $goods_id . '][price]', '请填写价格');
                    }
                    if ($goods['num'] == '' || $goods['num'] < 1) {
                        $validator->errors()->add('goods[' . $goods_id . '][num]', '请填写数量');
                    }
                }
                if (count($displayFee)) {
                    foreach ($displayFee as $date => $item) {
                        if ($item < 1) {
                            $validator->errors()->add('display_fee[' . $date . ']', '请填写数量');
                        }
                    }
                }
                if (count($displayGoods)) {
                    foreach ($displayGoods as $goods_id => $item) {
                        if ($item['num'] == '' || $item['num'] < 1) {
                            $validator->errors()->add('display_goods[' . $goods_id . '][num]', '请填写数量');
                        }
                    }
                }

                if (count($gifts)) {
                    foreach ($gifts as $goods_id => $gift) {
                        if ($gift['pieces'] == '请选择' || $gift['pieces'] == '') {
                            $validator->errors()->add('gifts[' . $goods_id . '][pieces]', '请选择单位');
                        }
                        if ($gift['num'] == '' || $gift['num'] < 1) {
                            $validator->errors()->add('gifts[' . $goods_id . '][num]', '请填写数量');
                        }
                    }
                }

            } catch (\Exception $e) {
                info($e->getMessage());
                return new ApiResponse('not_acceptable', '添加失败!');
            }

        });
    }

}
