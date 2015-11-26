<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | such as the size rules. Feel free to tweak each of these messages.
    |
    */

    "accepted" => ":attribute 必须接受。",
    "active_url" => ":attribute 不是一个有效的网址。",
    "after" => ":attribute 必须是一个在 :date 之后的日期。",
    "alpha" => ":attribute 只能由字母组成。",
    "alpha_dash" => ":attribute 只能由字母、数字和斜杠组成。",
    "alpha_num" => ":attribute 只能由字母和数字组成。",
    "array" => ":attribute 必须是一个数组。",
    "before" => ":attribute 必须是一个在 :date 之前的日期。",
    "between" => [
        "numeric" => ":attribute 必须介于 :min - :max 之间。",
        "file" => ":attribute 必须介于 :min - :max kb 之间。",
        "string" => ":attribute 必须介于 :min - :max 个字符之间。",
        "array" => ":attribute 必须只有 :min - :max 个单元。",
    ],
    "boolean" => ":attribute 必须为布尔值。",
    "confirmed" => ":attribute 两次输入不一致。",
    "date" => ":attribute 不是一个有效的日期。",
    "date_format" => ":attribute 的格式必须为 :format。",
    "different" => ":attribute 和 :other 必须不同。",
    "digits" => ":attribute 必须是 :digits 位的数字。",
    "digits_between" => ":attribute 必须是介于 :min 和 :max 位的数字。",
    "email" => ":attribute 不是一个合法的邮箱。",
    "exists" => ":attribute 不存在。",
    "filled" => ":attribute 不能为空。",
    "image" => ":attribute 必须是图片。",
    "in" => "已选的属性 :attribute 非法。",
    "integer" => ":attribute 必须是整数。",
    "ip" => ":attribute 必须是有效的 IP 地址。",
    "max" => [
        "numeric" => ":attribute 不能大于 :max。",
        "file" => ":attribute 不能大于 :max kb。",
        "string" => ":attribute 不能大于 :max 个字符。",
        "array" => ":attribute 最多只有 :max 个单元。",
    ],
    "mimes" => ":attribute 必须是一个 :values 类型的文件。",
    "min" => [
        "numeric" => ":attribute 必须大于等于 :min。",
        "file" => ":attribute 大小不能小于 :min kb。",
        "string" => ":attribute 至少为 :min 个字符。",
        "array" => ":attribute 至少有 :min 个单元。",
    ],
    "not_in" => "已选的属性 :attribute 非法。",
    "numeric" => ":attribute 必须是一个数字。",
    "regex" => ":attribute 格式不正确。",
    "required" => ":attribute 不能为空。",
    "required_if" => "当 :other 为 :value 时 :attribute 不能为空。",
    "required_with" => "当 :values 存在时 :attribute 不能为空。",
    "required_with_all" => "当 :values 存在时 :attribute 不能为空。",
    "required_without" => "当 :values 不存在时 :attribute 不能为空。",
    "required_without_all" => "当 :values 都不存在时 :attribute 不能为空。",
    "same" => ":attribute 和 :other 必须相同。",
    "size" => [
        "numeric" => ":attribute 大小必须为 :size。",
        "file" => ":attribute 大小必须为 :size kb。",
        "string" => ":attribute 必须是 :size 个字符。",
        "array" => ":attribute 必须为 :size 个单元。",
    ],
    "string" => ":attribute 必须是一个字符串。",
    "timezone" => ":attribute 必须是一个合法的时区值。",
    "unique" => ":attribute 已经存在。",
    "url" => ":attribute 格式不正确。",

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'password' => [
            'confirmed' => '两次输入密码不一致',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        //api
        'name' => '名称',
        'phone' => '电话号码',
        'backup_mobile' => '密保手机',
        'price_retailer' => '终端商价格',
        'min_num_retailer' => '终端商最低购买数',
        'price_wholesaler' => '批发商价格',
        'min_num_wholesaler' => '批发商最低购买数',
        'cate_level_1' => '一级分类',
        'cate_level_2' => '二级分类',
        'cate_level_3' => '三级分类',
        'is_new' => '是否新品',
        'is_out' => '是否缺货',
        'is_change' => '是否可换货',
        'is_back' => '是否可退货',
        'is_expire' => '是否即将过期',
        'is_promotion' => '是否促销',
        'promotion_info' => '促销信息',
        'images' => '图片',
        'area' => '配送区域',
        'status' => '状态',
        'type' => '类型',
        'version' => '版本号',
        'account' => '账号',
        'password' => '密码',
        'user_name' => '用户名',
        'consigner' => '收货人',
        'province_id' => '省份',
        'city_id' => '市/区',
        'district_id' => '乡镇',
        'street_id' => '街道',
        'area_name' => '详细地址',
        'address' => '地址',
        'card_number' => '银行卡号',
        'card_type' => '银行卡卡类型',
        'card_holder' => '持卡人',
        'card_address' => '开户行地址',
        'logo' => '商标',
        'contact_person' => '联系人',
        'contact_info' => '联系方式',
        'spreading_code' => '推广码',
        'license' => '营业执照',
        'license_num' => '营业执照编号',
        'business_license' => '经营许可证',
        'agency_contract' => '代理合同',
        'old_password' => '原密码',
        'min_money' => '最低配送额',
        'introduction' => '介绍',
        //后台
        'real_name' => '真实名字',
        'role_id' => '角色分组ID',
        'image' => '图片',
        'url' => '链接地址',
        'start_at' => '开始时间',
        'end_at' => '结束时间',
        'pid' => '父级ID',
        'category_id' => '分类ID',
        'id_list' => '商家或商品id列表',
        'sort' => '排序',
        'reason' => '原因',
        'content' => '内容',
        'contact' => '联系方式',
        'version_name' => '版本名称',
        'version_no' => '版本号',
        'address[province_id]' => '省',
        'address[city_id]' => '市',
        'address[address]' => '地址'
    ],

];
