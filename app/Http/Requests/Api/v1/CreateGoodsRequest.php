<?php

namespace App\Http\Requests\Api\v1;


class CreateGoodsRequest extends UserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'price_retailer' => 'required|numeric|min:0',
            'price_retailer_pick_up' => 'numeric|min:0',
            'min_num_retailer' => 'required|numeric|min:0',
            'price_wholesaler' => 'sometimes|required|numeric|min:0',
            'price_wholesaler_pick_up' => 'numeric|min:0',
            'min_num_wholesaler' => 'sometimes|required|numeric|min:0',
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
            if(!is_numeric($this->input('pieces_retailer'))){
                $validator->errors()->add('pieces_retailer', '终端商单位 不能为空');
            }
            if(!empty($this->input('price_wholesaler')) && !is_numeric($this->input('pieces_wholesaler'))){
                $validator->errors()->add('pieces_wholesaler', '批发商单位 不能为空');
            }
            if ($this->input('pieces_level_2')!='' && $this->input('system_1')=='') {

                $validator->errors()->add('system_1', '二级单位进制 不能为空');
            }
            if($this->input('system_1') !='' && $this->input('pieces_level_2')=='') {

                $validator->errors()->add('system_1', '二级单位 不能为空');
            }
            if ($this->input('pieces_level_3') !='' && $this->input('system_2')=='') {

                $validator->errors()->add('system_2', '三级单位进制 不能为空');
            }
            if ($this->input('system_2') !='' && $this->input('pieces_level_3')=='') {

                $validator->errors()->add('system_2', '三级单位 不能为空');
            }

            if ($this->input('pieces_level_3') !='' && $this->input('pieces_level_2')=='') {

                $validator->errors()->add('pieces_level_2', '二级单位 不能为空');
            }
        });
    }
}
