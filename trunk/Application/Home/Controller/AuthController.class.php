<?php
/**
 * Created by PhpStorm.
 * User: Freeman
 * Date: 2017/8/9
 * Time: 14:15
 */

namespace Home\Controller;

use Enum\SessionEnum;

class AuthController extends BaseController
{
    public function _initialize() {
        parent::_initialize();
        $this->loger("_initialized", "AuthController");
//        $this->Auth();
        $this->getStatus();
    }
    private $whitelist = array("", "Home/User/signUp", "Home/User/logIn", "Home/User/forgetPassword",
                                "Home/Tools/sendVerifyCode", "Home/Tools/checkVerifyCode", "Home/Index/getBanner", "Home/Index/getStarList");

    private function Auth() {
        $this->loger("execute", "Auth()");
        if ($_SERVER['HTTP_HOST'] == "192.168.1.119") {
            if (!in_array_case(__INFO__, $this->whitelist)) {
                $AccessToken = I('AccessToken', '', 'trim');
                $this->loger("AccessToken", $AccessToken);
                if ($AccessToken != session (SessionEnum::USER_ACCESS_TOKEN)) {
                    $this->ajaxReturn(fail_json("token不合法"));
                }
            }
        } else {
            $this->ajaxReturn(fail_json("非法请求"));
        }
    }

    /**
     * ToRe
     */
    public function getStatus(){
        $phone = session("userPhone");
        $result = M()->query("SELECT state FROM user WHERE phone ='$phone'");
        $this->loger('$result',$result);
        if ($result[0]['state']==1){
            $this->ajaxReturn(fail_json("账号被禁用，请联系客服~"));
        }
    }

}