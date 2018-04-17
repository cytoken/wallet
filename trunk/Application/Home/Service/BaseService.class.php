<?php
// +----------------------------------------------------------------------
// | 基础业务CRUD方法范例
// +----------------------------------------------------------------------
// | Author: James.Yu <zhenzhouyu@jiechengkeji.cn>
// +----------------------------------------------------------------------

namespace Home\Service;

use Tools\XLog;
use Think\Think;

class BaseService {

    public static $xlog;

    public function __construct() {

    }

    protected function loger($title, $content) {
        self::$xlog = new XLog();
        self::$xlog->trackLog($title, $content);
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

 }