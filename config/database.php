<?php

return [

    'driver'    => 'mysql',

    'host'      =>'127.0.0.1',

    'database'  => 'test',

    'username'  => 'root',

    'password'  => '',

    'charset'   => 'utf8',

    'collation' => 'utf8_general_ci',

    'prefix'    => '',

    'memcached' => [
        'driver' => 'memcached',
        'servers' => [
            [
                'host' => '127.0.0.1',
                'port' => 11211,
                'weight' => 100,
            ],
        ],
    ],

];