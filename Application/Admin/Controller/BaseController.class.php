<?php
/**
 * Created by PhpStorm.
 * User: rainbow
 * Date: 16/11/18
 * Time: 上午9:55
 */

namespace Admin\Controller;

use Think\Controller;
use Tools\XLog;

class BaseController extends Controller {

    CONST SYSTEM_SECRET = "LUOBOJIANZHI"; 

    public function _initialize() {
        XLog::trackLog("_initialize", "BaseController");
    }

    protected function loger($title, $content) {
        XLog::trackLog($title, $content);
    }

}