<?php
return [
    'top' => [
        'app_key' => '23318997',
        'app_secret' => '3aecbfce77c9b040ce33a03123e65b91',
        'signature_id' => '1600',
        'templates' => [
            'register' => '000',
            'code' => '2954',
            'audit_passed' => '2943',
            'audit_not_passed' => '2944',
            'withdraw' => '2946'
        ],
        // 消息通用密码
        'message_password' => substr(md5('dingbaida2016315'), 10, 8)
    ],
];