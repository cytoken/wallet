<?php
/**
 * Created by PhpStorm.
 * User: rainbow
 * Date: 16/11/18
 * Time: 上午9:55
 */

namespace Home\Controller;

use Enum\AppUserSessionEnum;
use Enum\NumEnum;
use Think\Controller;
use Tools\XLog;
class BaseController extends Controller {

    CONST SYSTEM_SECRET = "WEBSTAR";

    public function _initialize() {
        XLog::trackLog("_initialize", "BaseController");
    }

    protected function loger($title, $content) {
        XLog::trackLog($title, $content);
    }

    /**
     * 记录方法的时间消耗
     * @param $beginTime
     * @param $content
     */
    protected function timeLogger($beginTime, $method) {
        $endTime = microtime(true);
        $this->loger("c_t_".$method, round($endTime - $beginTime, 3)."秒");
    }
    /**
     * 获取系统操作日志
     */
    public function signLog($path){
        $userId = session(AppUserSessionEnum::USER_INFO)['id'];
        $this->loger('userId',$userId);
        $cip =$_SERVER['REMOTE_ADDR']; //客户端ip
        $user_agent = $_SERVER['HTTP_USER_AGENT'];//获取操作系统
        $this->loger('path', $path);
        $this->loger('cip', $cip);
        $this->loger('agent', $user_agent);
        $param = array($userId, $user_agent, $cip,$path, NumEnum::NUM_USER_LOG_TWO);
        $this->loger('data',$param);
        return $param;
    }
}