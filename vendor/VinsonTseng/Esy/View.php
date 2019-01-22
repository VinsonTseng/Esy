<?php

namespace Esy;

class View {

    private static $_data = array();
    
    private static $_make = null;
    
    private static $_mobile = false;
    

    public static function newd() {
        if ( self::$_make == null ) {
            self::$_make = new View();
        }
        return self::$_make;
    }

    function getWith($key){
        return self::$_data[$key];
    }
    
    public function with($key, $value = null) {
        self::$_data[$key] = $value;
        return $this;
    }
    
    public function isMobile(){
        $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
        $uachar = "/(nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|mobile)/i";
        if(($ua == '' || preg_match($uachar, $ua)) && !strpos(strtolower($_SERVER['REQUEST_URI']),'wap')) {
            return true;
        }
        return false;
    }
    
    public function mobile(){
        if ( self::isMobile() ) {

            self::$_mobile = true;

        }
        return $this;
    }
    
    public function i18n($path=null){
        if ( $path ) {
            self::$_i18n = $path;
        }
        return $this;
    }

    public function display($file = null) {
        $file = self::_getFile($file);
        if (!is_file($file)) {

            return false;
        }
        self::setExtract();
        extract(self::$_data, EXTR_OVERWRITE);
        self::_setHeader('html');
        $inculde_file = @include($file);
    }

    public function fetch($file = null) {
   
        $file = self::_getFile($file);
         
        self::setExtract();
        extract(self::$_data, EXTR_OVERWRITE);
        self::_setHeader('html');
        ob_start();
        $inculde_file = @include($file);

        $results = ob_get_contents();
        ob_end_clean();

        return $results;
    }
    
    private function setExtract(){
        $webSite = Config::get('Url');
        if ( is_array($webSite) ) {
            self::$_data = array_merge(self::$_data,$webSite);
        }
    }

    /**
     * array('status'=>200,'msg'=>'','data'=>'');
     * 40* 错误信息 (400 – 请求无效, 401 – 参数不全, 402 – 未授权, 403 – 禁止访问,404- 无法找到文件)
     * 20* 成功信息 
     * 50* 内部服务器错误 (500 – 网站关闭, 501 - 网站维护, 502 - 功能开发中, 503 - 服务器忙碌, 504 - controller无效, 505 - action无效, 506 - view无效)
     *
     * */
    public function json($param = null, $ending = true) {
        self::_setHeader('json');
        $result = self::_json($param);
        echo str_replace(':null',':""',json_encode($result));
        if ($ending)
            exit();
    }

    /**
     * array('status'=>200,'msg'=>'','data'=>'');
     * 40* 错误信息 (400 – 请求无效, 401 – 参数不全, 402 – 未授权, 403 – 禁止访问,404- 无法找到文件)
     * 20* 成功信息 
     * 50* 内部服务器错误 (500 – 网站关闭, 501 - 网站维护, 502 - 功能开发中, 503 - 服务器忙碌, 504 - controller无效, 505 - action无效, 506 - view无效)
     *
     * */
    public function jsonp($param = null, $callback = 'callback', $ending = true) {
        $result = self::_json($param);
        $jsonp = $_REQUEST[$callback];
        echo $jsonp . '(' . json_encode($result) . ')';
        if ($ending)
            exit();
    }

    public function javascript($param = null) {
        self::_setHeader('html');
        echo '<script type="text/javascript">';
        echo $param;
        echo '</script>';
    }

    private function _json($param = null) {
        if (is_numeric($param)) {
            $result['status'] = $param;
        } else {
            if (empty($param['status'])) {
                $result['status'] = 200;
                $result['data'] = $param;
            } else {
                $result = $param;
            }
        }
        return $result;
    }

    private function _getFile($file = null) {
        $application = Routes::getApplication();
        $prefix = APP_PATH . '/' . ucfirst($application) . '/View';
        if (empty($file)) {
            $controller_path = Routes::getControllerPath();
            $controller = Routes::getController();
            $action = Routes::getAction();
            $file = $prefix;

            if ( $controller_path ) {
                $file .= "/".$controller_path;
            }
            if ( self::$_mobile ) {
                $file .= "/Mb";
            }
            $file .= '/' . ucfirst($controller);
            
            $file .= '/' . ucfirst($action) . '.php';
        } else {
            if ( self::$_mobile ) {
                if($file[0] == '/'){
                    $file = "Mb".$file;
                }else{
                    $file = "Mb/".$file;
                }
            }
            $file = $prefix . '/' . $file;
        }
        self::$_data['Public'] = $prefix . '/Public';
        return $file;
    }

    private function _setHeader($type = null) {
        if (headers_sent() || empty($type)) {
            return false;
        }
        switch ($type) {
            case 'html':
                header("Content-type: text/html; charset=utf-8");
                break;
            case 'json':
                header("Content-type: application/json; charset=utf-8");
                break;
            case 'javascript':
                header("Content-type: text/javascript; charset=utf-8");
                break;
        }
    }

}
