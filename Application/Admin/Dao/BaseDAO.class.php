<?php
// +----------------------------------------------------------------------
// | 基础业务CRUD方法范例
// +----------------------------------------------------------------------
// | Author: James.Yu <zhenzhouyu@jiechengkeji.cn>
// +----------------------------------------------------------------------

namespace Admin\DAO;

use Think\Model;
use Enum\CompanyEnum;
use Tools\XLog;

class BaseDAO {

    protected static $xlog;

    public function __construct() {
        self::$xlog = new XLog();
        self::$xlog->trackLog("__construct", "BaseDAO");  
    }

    protected function loger($title, $content) {
        self::$xlog->trackLog($title, $content);

    }

 }