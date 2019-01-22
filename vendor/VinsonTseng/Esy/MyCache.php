<?php
namespace Esy;


class MyCache {
    
    private static $_opend = false;

        /**
	 * 配置
	 *
	 * @access private
	 * @var string
	 */
	private static $_config = array();
	
	/**
	 * Memcache连结
	 *
	 * @access private
	 * @var integer 默認值 0
	 */
	private static $_mem = 0;
	
	function __construct() {
		
	}
    
    function init($config){
        if ( is_array($config) ) {
			self::$_config = $config;
		}
    }
	
	/**
	 * connect memcache
	 *
	 * @access private
	 */
    private function connect(){
        
        if ( empty(self::$_config) ) {
            return false;
        }
		if (self::$_mem ) {
			return true;
		}

		self::$_mem = new \Memcache;
		while(list($key,$val)=@each(self::$_config)) {
			self::$_mem->addServer($val['host'], $val['port']);
		}
		
        return true;
    }
    
    
    
    public function opend(){
        self::$_opend = true;
    }
    
    /**
     * 写入cache
     * @param string $key
     * @param Any $val
     * @param int $life 存活时间 秒
     * @return boolean
     **/
    public function set($key,$val,$life = 180) {
        if ( self::connect() == false || self::$_opend == false ) {
            return false;
        }
		$cache_key = md5($key);
        $cache_life = time() + $life;
        $val = empty($val) ? null : $val;
        $cache_data = array(
            'data'=>$val,
            'life'=>$cache_life,
        );
		$cache_val = json_encode($cache_data);
        if (self::$_mem->set($cache_key, $cache_val , 0, $life)) {
			return $key;
		}
        return false;
    }
    
    /**
     * &CLEAN_CACHE=X5e7AdQT6XlyCLjxmciz
     * 读取cache
     * @param string $key
     * @return any
     **/
    public function get($key,$echo_all=false) {

        if ( self::connect() == false || self::$_opend == false ) {
            return false;
        }
        if ( $_GET['CLEAN_CACHE'] == 'X5e7AdQT6XlyCLjxmciz' ) {
            return false;
        }
		$cache_key = md5($key);
        $cache_val = self::$_mem->get($cache_key);
        if ( empty($cache_val) ) {
            return false;
        }
		$cache_data = json_decode($cache_val,true);
        if ( $echo_all ) {
            return $cache_data;
        }
        return $cache_data['data'];
    }
	
	/**
	 * 删除cache
     * @param string $key
     * @return boolean
     **/
	public function del($key,$is_md5=true) {
		if ( self::connect() == false  ) {
            return false;
        }
        if ( $is_md5 ) {
            $key = md5($key);
        }
		if (self::$_mem->delete($key)) {
			return true;
		}
		return false;
	}

}
?>