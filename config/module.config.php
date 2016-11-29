<?php
$path=  realpath((__DIR__).'/../').'/src/';

return [
    'adteam_core_checkout'=>[
        'test'=>$path
    ],
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => \Doctrine\DBAL\Driver\PDOMySql\Driver::class,
                'params' => [
                    'charset' => 'utf8',
                ],
            ],
        ],
        'driver' => [
            'Doctrine_driver_checkout' => [
                'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    0 => $path.'/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'Adteam\\Core\\Checkout' => 'Doctrine_driver_checkout',
                ],
            ],
        ],
    ],  
    'configuration' => [
        'orm_default' => [
            'datetime_functions' => [
                'DATE_FORMAT' => 'Adteam\Core\Checkout\Functionsmysql\DateFormat'
            ],
        ],
    ],    
];
