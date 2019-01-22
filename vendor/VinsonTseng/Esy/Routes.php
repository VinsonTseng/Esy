<?php

namespace Esy;

class Routes {

    private static $_config = array();
    private static $_applications = array();
    private static $_application = 'index';
    private static $_module = '';
    private static $_controller_path = '';
    private static $_controller = 'index';
    private static $_action = 'index';
    private static $_prefix = 'show';
    private static $_opend = true;
    private static $_segments = array();
    private static $_host_path = array();
    
    public function __construct() {
        self::_setPath();
        self::_setApplication();
        self::_loadConfig();
        self::_setRouters();
    }

    public function getControllerPath() {
        return self::$_controller_path;
    }
    
    
    public function getController($is_tolower=true) {
        if (  $is_tolower ) {
            return strtolower(self::$_controller);
        }
        return self::$_controller;
    }

    public function getApplications() {
        return self::$_applications;
    }
    
    public function getApplication($is_tolower=true) {
        if (  $is_tolower ) {
            return strtolower(self::$_application);
        }
        return self::$_application;
    }
    
    
    public function getModule($is_tolower=true) {
        if (  $is_tolower ) {
            return strtolower(self::$_module);
        }
        return self::$_module;
    }

    public function getAction($is_tolower=true) {
        if (  $is_tolower ) {
            return strtolower(self::$_action);
        }
        return self::$_action;
    }

    public function getPrefix() {
        return self::$_prefix;
    }

    public function getSegments() {
        return self::$_segments;
    }

    public function getConfig() {
        return self::$_config;
    }

    public function run() {
        $application = self::getApplication();
        $controller_path = self::getControllerPath();
        $controller = self::getController();
        $action = self::getAction();
        $prefix = self::getPrefix();
        $segments = self::getSegments();
        $new_controller = "App\\" . ucfirst($application) . "\\Controller";
        if ( $controller_path ) {
            $new_controller .= "\\".ucfirst($controller_path);
        }
        $new_controller .= "\\".ucfirst($controller) . "Controller";
        if (class_exists($new_controller)) {
            $class_controller = new $new_controller();
        }

        if (method_exists($class_controller, '__before')) {
            call_user_func_array(array(&$class_controller, '__before'), $segments);
        }
        $action_full = $prefix . $action;
        if (method_exists($class_controller, $action_full)) {
            
            $action_full = self::getClassMethod($class_controller,$action_full);
            call_user_func_array(array(&$class_controller, $action_full), $segments);
        }
        if (method_exists($class_controller, '__after')) {
            call_user_func_array(array(&$class_controller, '__after'), $segments);
        }
    }
    
    private function getClassMethod($class_controller,$action_full){
        $methods = get_class_methods($class_controller);
        if ( empty($methods) ) {
            return false;
        }
        foreach($methods as $method) {
            if ( strtolower($method) == strtolower($action_full) ) {
                self::$_action = str_replace(array('show','do'), '', $method);
                $result = $method;
            }
        }
        return $result;
        
    }
    
    private function _setPath(){
        $req_uri = Requests::instance()->path();
        self::$_host_path = self::_getSegments($req_uri);
        return self::$_host_path;
    }

    private function _setApplication() {
        if ( self::$_opend == false ) {
            return false;
        }
        $application_config_file = BASE_PATH . '/config/application.php';
        if (is_file($application_config_file)) {
            $application_config = require $application_config_file;
            self::$_applications = $application_config;
        }
        $application = self::$_host_path[0];
        $application_lower = strtolower($application);
        if ( self::$_applications[$application_lower] ) {
            self::$_application = self::$_applications[$application_lower];
            unset(self::$_host_path[0]);
            self::$_host_path = self::_getSegments(self::$_host_path);
            self::$_module = $application_lower;
            return self::$_application;
        }
        return self::$_application;
    }

    private function _loadConfig() {
        $application = self::getApplication();
        $config_file = BASE_PATH . '/config/routes/' . $application . '.php';
        if (is_file($config_file)) {
            $config = require $config_file;
            self::$_config = $config;

        }
    }
    

    private function _setRouters() {
        $req_uris = self::$_host_path;
        $req_uri = @implode('/', $req_uris);
        $routers = self::getConfig();
        $req_uri = empty($req_uri) ? '' : $req_uri;
        if ( array_key_exists($req_uri, $routers) ) {
            return self::_setRequest(explode('/', $routers[$req_uri]));
        }
        while (list($key, $val) = @each($routers)) {
            $pattern = '#^' . $key . '$#i';
            if (@preg_match($pattern, $req_uri)) {
                $val = preg_replace($pattern, $val, $req_uri);
                return self::_setRequest(explode('/', $val));
            }
        }
        return self::_setRequest($req_uris);
    }

    private function _getSegments($req_uri) {
        $segments = array();
        if (is_array($req_uri)) {
             $req_uris = $req_uri;
        } else {
            $req_uris = explode('/', $req_uri);
        }
        while (list($key, $val) = @each($req_uris)) {
            if (empty($val)) {
                continue;
            }
            $segments[] = $val;
            
        }
        return $segments;
    }

    private function _setSegments($segments = null) {
        self::$_segments = self::_getSegments($segments);
    }

    private function _setRequest($segments = array()) {
        if (isset($segments[0])) {
            if ( strpos($segments[0],'[') !== false && strpos($segments[0],']') !== false ) {
                self::$_controller_path = str_replace(array('[',']'), '', $segments[0]);
            } else {
                self::$_controller = $segments[0];
            }
            unset($segments[0]);
        }
        if (isset($segments[1]) && !empty($segments[1])) {
            if (  self::$_controller_path ) {
                self::$_controller = $segments[1];
            } else {
                self::$_action = $segments[1];
            }
            unset($segments[1]);
        }
        if (isset($segments[2]) &&  self::$_controller_path && !empty($segments[2])) {
            self::$_action = $segments[2];
            unset($segments[2]);
        }
        self::$_prefix = ($_SERVER['REQUEST_METHOD'] == 'POST') ? "do" : "show";
        self::_setSegments($segments);
        
    }

}
