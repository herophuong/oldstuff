<?php
namespace Payment;
return array(
    'service_manager' => array(
        'factories' => array(
            'payment' => 'Payment\Service\PaymentServiceManager',
        ),
        'invokables' => array(
            'Payment\Service\Cash' => 'Payment\Service\Cash',
            'Payment\Service\Credit' => 'Payment\Service\Credit',
        ),
    ),	
);
