<?php

namespace Esy;

class Config {

    private static $_data = array();

    public function __construct($config = null) {
        self::$_data = $config;
    }
    
    public function getAll(){
        return self::$_data;
    }

    public function get($option) {
        if (!$option) {
            return false;
        }
        $config = self::$_data;
        if ($config[$option]) {
            return $config[$option];
        }
        return false;
    }
    
    public function debug(){
        $config = self::$_data;
        if ($config['debug']) {
            return true;
        }
        return false;
    }

    public function set($option, $val) {
        if (!$option) {
            return false;
        }
        self::$_data[$option] = $val;
        return self::$_data[$option];
    }
    
    public function setUrl($inc) {
        if (!$inc) {
            return false;
        }
        $inc_file = BASE_PATH.'/'.$inc;
        self::$_data['Url'] = require $inc_file;
        return self::$_data['Url'];
    }
    
    public function getUrl($key) {
        if (!$key) {
            return false;
        }
        $config = self::$_data['Url'];
        if ($config[$key]) {
            return $config[$key];
        }
        return false;
    }

    public function getHost() {
        
        return self::$_data['Url']['http'].$_SERVER['HTTP_HOST'];
    }
    
}
