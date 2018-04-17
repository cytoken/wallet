<?php
/**
 * Created by PhpStorm.
 * User: Freeman
 * Date: 2017/8/10
 * Time: 11:37
 */

namespace Home\Service;


use Enum\SysEnum;
use Home\Dao\StarDao;
use Home\Dao\SysDao;
use Home\Service\BaseService;
use Think\Model;

class IndexService extends BaseService
{
    private $db;
    private $sysDao;
    private $starDao;

    public function __construct() {
        $this->db = new Model();
        $this->sysDao = new SysDao($this->db);

    }

    public function getBanner() {
        return success_json("获取数据成功！",$this->sysDao->getBanner()) ;
    }


    public function getDealTime () {
        $data = $this->sysDao->getSysConfigByPid(SysEnum::SYS_DEAL_TIME_ID);
        if (empty($data)) {
            return fail_json("获取数据失败！");
        }
        $time = array();
        $time['openTime'] = $data[SysEnum::OPEN_TIME];
        $time['closingTime'] = $data[SysEnum::CLOSING_TIME];
        $result = $this->sysDao->getSysConfig(SysEnum::WITHDRAW_INVESTOR_RATE);
        $time['rate'] = $result[0]['value'];
        $this->loger('rate',$result[0]['value']);
        return success_json("获取数据成功！", $time);
    }

}