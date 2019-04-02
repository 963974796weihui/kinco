<?php

return [

    // 默认支付网关
    'default' => 'alipay',

    // 各个支付网关配置
    'gateways' => [
        'paypal' => [
            'driver' => 'PayPal_Express',
            'options' => [
                'solutionType' => '',
                'landingPage' => '',
                'headerImageUrl' => ''
            ]
        ],

        'alipay' => [
            'driver' => 'Alipay_Express',
            'options' => [
                'partner' => 'your pid here',
                'key' => 'your appid here',
                'sellerEmail' =>'your alipay account here',
                'returnUrl' => 'your returnUrl here',
                'notifyUrl' => 'your notifyUrl here'
            ]
        ]
    ]

];