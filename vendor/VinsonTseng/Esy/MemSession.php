<?PHP
namespace Esy;

class MemSession {
    
    private static $_config;
       
    private static $_memcache;
    
    function __construct(){
        
    }
    
    private function connect($config){
        if (!$config || self::$_memcache) {
            return false;
        }
        self::$_memcache = new \Memcache;
        foreach($config as $key => $val){
            $added = self::$_memcache->addServer($val['host'], $val['port']);
        }
        return true;
    }

    public function open() {
        return true;
    }

    public function close() {
        return true;
    }

    public function gc() {
        return true;
    }

    public function destroy($key) {
        if ( empty($key) ) {
            return;
        }
        return self::$_memcache->delete($key);
    }

    public function read($key) {
        if ( empty($key) ) {
            return;
        }

        return self::$_memcache->get($key);
    }

    public function write($key,$val) {
        if ( empty($key) ) {
            return;
        }
        $maxlifetime = empty(Config::get('maxlifetime')) ? 10800 : Config::get('maxlifetime');
        $writed = self::$_memcache->set($key,$val,0,$maxlifetime);

        return $writed;
    }
    
    public function init($config_file) {
        if (!is_file($config_file)) {
            return false;
        }
        $config = require $config_file;
        self::connect($config);
        if ( !self::$_memcache ) {
            return false;
        }
        session_module_name('user');
        session_set_save_handler(
            array($this,'open'),
            array($this,'close'),
            array($this,'read'),
            array($this,'write'),
            array($this,'destroy'),
            array($this,'gc')
        );
        
    }
}


?>