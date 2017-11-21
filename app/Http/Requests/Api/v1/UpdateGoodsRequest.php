<?php

namespace App\Http\Requests\Api\v1;


class UpdateGoodsRequest extends UserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $retailer = [
            'price_retailer' => 'required|numeric|min:0',
            'price_retailer_pick_up' => 'numeric|min:0',
            'min_num_retailer' => 'required|numeric|min:0|max:20000',
        ];
        $rules = [
            'name' => 'required',
            'price_wholesaler' => 'sometimes|required|numeric|min:0',
            'price_wholesaler_pick_up' => 'numeric|min:0',
            'min_num_wholesaler' => 'sometimes|required|numeric|min:0|max:20000',
            'bar_code' => 'required|digits_between:7,18',
            'cate_level_1' => 'required|numeric|min:0',
            'cate_level_2' => 'required|numeric|min:1',
            /* 'is_new' => 'required|boolean',
             'is_out' => 'required|boolean',
             'is_change' => 'sometimes|required',
             'is_back' => 'sometimes|required',
             'is_expire' => 'required|boolean',
             'is_promotion' => 'required|boolean',*/
            'promotion_info' => 'sometimes|required_if:is_promotion,1',
            'images' => 'sometimes|array',
            'area' => 'sometimes|array',
            'pieces_level_1' => 'required',
            'specification' => 'required'
        ];
        if ($this->user()->type != cons('user.type.maker')) {
            return array_merge($rules, $retailer);
        }
        return $rules;
    }

    /**
     * 自定义验证
     *
     * @param \Illuminate\Contracts\Validation\Factory $factory
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator($factory)
    {
        return $this->defaultValidator($factory)->after(function ($validator) {

            $system1 = $this->input('system_1');
            $system2 = $this->input('system_2');
            $piecesLevel2 = $this->input('pieces_level_2');
            $piecesLevel3 = $this->input('pieces_level_3');
            $maxNumRetailer = $this->input('max_num_retailer');
            $maxNumWholesaler = $this->input('max_num_wholesaler');

            if ($maxNumRetailer && $maxNumRetailer < $this->input('min_num_retailer')) {
                $validator->errors()->add('max_num_retailer', '终端商最高购买数 必须大于最低购买数');
            }

            if ($maxNumWholesaler && $maxNumWholesaler < $this->input('min_num_wholesaler')) {
                $validator->errors()->add('max_num_wholesaler', '批发商最高购买数 必须大于最低购买数');
            }

            if ($this->user()->type != cons('user.type.maker')) {
                if (!is_numeric($this->input('pieces_retailer'))) {
                    $validator->errors()->add('pieces_retailer', '终端商单位 不能为空');
                }
            }
            if (!empty($this->input('price_wholesaler')) && !is_numeric($this->input('pieces_wholesaler'))) {
                $validator->errors()->add('pieces_wholesaler',
                    ($this->user()->type == cons('user.type.maker') ? '供应商' : '批发商') . '单位 不能为空');
            }
            if ($piecesLevel2 != '' && (!is_numeric($system1) || $system1 <= 0)) {

                $validator->errors()->add('system_1', '一级单位进制 不合法');
            }
            if ($system1 != '' && $piecesLevel2 == '') {

                $validator->errors()->add('system_1', '二级单位 不能为空');
            }
            if ($piecesLevel3 != '' && (!is_numeric($system2) || $system2 <= 0)) {

                $validator->errors()->add('system_2', '二级单位进制 不合法');
            }
            if ($system2 != '' && $piecesLevel3 == '') {

                $validator->errors()->add('system_2', '三级单位 不能为空');
            }

            if ($piecesLevel3 != '' && $piecesLevel2 == '') {

                $validator->errors()->add('pieces_level_2', '二级单位 不能为空');
            }
        });
    }
}
