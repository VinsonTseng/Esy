<?php
return array(
    'url'=>'config/url.php',
    'database'=>'config/database.php',
    //'memcache'=>'config/memcache.php',
    'time_zone' => 'Asia/Shanghai',
    'errorReporting'=>E_ALL & ~E_NOTICE & ~E_DEPRECATED,
    'maxlifetime'=>10800,
    'debug'=>true,
);
