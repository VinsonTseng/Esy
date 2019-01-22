<?php

namespace Esy;
use Illuminate\Database\Capsule\Manager as Capsule;
class Bootstrap {

    
    private static $_config = array();
    
    public function __construct($config=null) {
        self::$_config = $config;
    }
    
    
    function run(){
        
        // Config Loader
        $config = new Config(self::$_config);
        if ( self::$_config['time_zone'] ) {
            date_default_timezone_set(self::$_config['time_zone']);
        }
        
        if(self::$_config['errorReporting']){
            error_reporting(self::$_config['errorReporting']);
        }
        ini_set('session.use_trans_sid',0);
        ini_set('session.gc_probability',1);
        ini_set('session.gc_divisor',1000);
        ini_set('session.gc_maxlifetime',self::$_config['maxlifetime']);
        ini_set('session.use_cookies', 1);
        ini_set('session.cookie_path', '/');
        if ( self::$_config['url'] ) {
            $config->setUrl(self::$_config['url']);
        }
        if ( self::$_config['cookie_domain'] ) {
            $host = $_SERVER['HTTP_HOST'];
            $host_pos = strpos($host, '.'); //子域名不允许.
            $domain = substr($host,$host_pos);
            ini_set('session.cookie_domain',$domain);
        }
        if ( self::$_config['session'] ) {
            $sessions = new MemSession();
            $sessions->init(self::$_config['session']);
        }
        
        session_start();

        if ( self::$_config['database'] ) {
            
            $database_inc = BASE_PATH.'/'.self::$_config['database'];
            // Eloquent ORM
            $capsule = new Capsule;

            $capsule->addConnection(require $database_inc);
            $capsule->setAsGlobal();
            $capsule->bootEloquent();
        }
        if ( self::$_config['memcache'] ) {
            $memcache_file = BASE_PATH.'/'.self::$_config['memcache'];
            $memcache_inc = require $memcache_file;
            MyCache::init($memcache_inc);
            MyCache::opend();
        }
        $request = new Requests();
        $request->capture();
        $routes = new Routes();
        $routes->run();


    }
    
    
}
