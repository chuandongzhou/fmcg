<?php

return array(

    'IOS'     => array(
        'environment' =>'development',
        'certificate' =>storage_path('iospem/fmcg_dev.pem'),
        'passPhrase'  =>'mgpzWrymkf',
        //tQ0txh1Q
        'service'     =>'apns'
    ),
    'appNameAndroid' => array(
        'environment' =>'production',
        'apiKey'      =>'yourAPIKey',
        'service'     =>'gcm'
    )

);