<?php

namespace Esy;
use Illuminate\Http\Request;


class Requests  {
    
    private static $_request;
    
    
    function capture(){
        self::$_request = Request::capture();
        return self::$_request;
    }
    
    public function instance(){
        return self::$_request;
    }
    /**
     * 获取$_GET数据
     * @param type $key 主键
     * @param type $default 主键不存在时返回默认值
     * @return type
     */
    public function get($pamam,$default=null) {
        return self::$_request->input($pamam,$default);
    }
    
    public function getNum($pamam,$default=0) {
        $value = self::$_request->input($pamam,$default);
        if ( !is_numeric($value) ) {
            $value = 0;
        }
        return $value;
    }
    
    /**
     * 获取$_POST数据
     * @param type $key 主键
     * @param type $default 主键不存在时返回默认值
     * @return type
     */
    public function post($pamam,$default=null) {
        return self::$_request->input($pamam,$default);
    }
    
    public function postNum($pamam,$default=0) {
        if ( !is_numeric($pamam) ) {
            $pamam = 0;
        }
        return self::$_request->input($pamam,$default);
    }
    
    
    public function getSession($key){
        return $_SESSION[$key];
    }
    
    public function setSession($key,$val){
        $_SESSION[$key] = $val;
        return $val;
    }
    
    public function getCookie($key){
        return $_COOKIE[$key];
    }
    
    public function setCookie($key,$val,$expire){
        $life = time() + $expire;
        $domain = Config::getUrl('base_url');
        setcookie($key, $val, $life, '/', $domain);
        return $val;
    }
    
    
    
    function getCurl($url) {
        if ( empty($url) ) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        if ( strpos($url,'https') !== false ) {
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
        curl_setopt($ch,CURLOPT_TIMEOUT,60);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    
    function postCurl($url,$post_data) {
        if ( empty($url) || empty($post_data) ) {
            return false;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        if ( strpos($url,'https') !== false ) {
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch,CURLOPT_TIMEOUT,60);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    
    public function getUrlContents($url){
        if ( empty($url) ) {
            return false;
        }
        $url = trim($url);
        $result = file_get_contents($url);
        return $result;
    }
   
}