<?php
return [
    'top' => [
        'app_key' => '23582902',
        'app_secret' => '77d3c2a6295d2599014cb72c23ee5773',
        'signature_id' => '1600',
        'templates' => [
            'register' => '2941',
            'code' => '2954',
            'audit_passed' => '2943',
            'audit_not_passed' => '2944',
            'withdraw' => '2946',
            'order' => 'SMS_baichun_3329'
        ],
        // 消息通用密码
        'message_password' => substr(md5('dingbaida2016315'), 10, 8)
    ],
];