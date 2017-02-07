<?php
return [
    'top' => [
        'app_key' => '23582902',
        'app_secret' => '77d3c2a6295d2599014cb72c23ee5773',
        'signature_id' => '1600',
        'templates' => [
            'register' => 'SMS_baichun_2941',             //验证码
            'code' => 'SMS_baichun_2954',        //密码重置
            'audit_passed' => 'SMS_baichun_2943',
            'audit_not_passed' => 'SMS_baichun_2944',
            'withdraw' => 'SMS_baichun_2946',
            'order' => 'SMS_baichun_3329'
        ],

    ],
    'im' => [
        'app_key' => '23318997',
        'app_secret' => '3aecbfce77c9b040ce33a03123e65b91',
        // 消息通用密码
        'message_password' => substr(md5('dingbaida2016315'), 10, 8)
    ]
];