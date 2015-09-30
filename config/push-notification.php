<?php

return array(

    'IOS'     => array(
        'environment' =>'development',
        'certificate' =>storage_path('iospem/fmcg_dev.pem'),
        'passPhrase'  =>'mgpzWrymkf',
        'service'     =>'apns'
    ),
    'appNameAndroid' => array(
        'environment' =>'production',
        'apiKey'      =>'yourAPIKey',
        'service'     =>'gcm'
    )

);