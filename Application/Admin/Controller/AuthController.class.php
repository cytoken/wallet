<?php
/**
 * Created by PhpStorm.
 * User: rainbow
 * Date: 16/11/18
 * Time: 上午9:55
 */

namespace Admin\Controller;
use Tools\XAuth;

class AuthController extends BaseController {

    public function _initialize() {
        parent::_initialize();
        $this->loger("_initialized", "AuthController");
        $this->authentication();

    }

    private $admin;
    private $whitelist = array("", "Home/User/doLogin", "Home/Wallet/notify");
    protected $errPage = "/Index/index";

    private function authentication() {
        if(C('X_AUTH')) {
            $this->auth14BySession();
        }
    }

    private function auth14BySession() {
        if(!in_array(__INFO__, $this->whitelist)) {
            $this->admin = session("admin");
            if(!isset($this->admin)) {
                $this->loger("auth14 failed!", __INFO__);
                $this->error('非常抱歉,您没有登录系统!', __APP__, 3);
            } else {
                if(!XAuth::auth13($this->admin['accessMap'])){
                    $this->error('非常抱歉,您没有相应的操作权限!', __APP__, 3);
                }
            }
        }
    }

    private function auth14Token() {
        if($_SERVER['HTTP_HOST'] == "wechat.luobojianzhi.com")
            return ture;

        $token = I('token', '', 'strip_tags');
        $this->loger("token", $token);
        $auth = explode(":", $token);
        $key = $auth[0];
        $secret = $auth[1];
        $this->loger("key", $key);
        $this->loger("secret", $secret);

        $xToken = md5($key . SELF::SYSTEM_SECRET);
        $this->loger("xToken", $xToken);

        if(substr($xToken, 6, 6) != $secret) {
            $this->logger("auth14 failed!", __INFO__);
            $this->error('非常抱歉,您没有登录系统!', $this->errPage, 3);
        }
    }
    
    protected function getAdmin() {
        return $this->admin;
    }
}