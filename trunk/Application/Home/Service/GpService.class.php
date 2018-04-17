<?php
/**
 * Created by PhpStorm.
 * User: jiechengkeji
 * Date: 2017/8/9
 * Time: 14:13
 */

namespace Home\Service;


use Home\Dao\GpDao;
use Think\Model;

class GpService extends BaseService
{
    private $db;
    private $gpDao;

    public function __construct() {
        parent::__construct();
        $this->db = new Model();
        $this->gpDao = new GpDao($this->db);
    }

    /**
     * 获取省的列表
     */
    public function getProvince() {
        $data = $this->gpDao->getProvince();
        return success_json('请求成功', $data);
    }

    /**
     * 通过省的id获取市的列表
     */
    public function getCity($pid) {
        $data = $this->gpDao->getCity($pid);
        return success_json('请求成功', $data);
    }

    /**
     * 通过市的id获取市的列表
     */
    public function getRegions($cid) {
        $data = $this->gpDao->getRegions($cid);
        return success_json('请求成功', $data);
    }

    /**
     * 通过区获取小区的列表
     */
    public function getVillage($rid) {
        $data = $this->gpDao->getVillage($rid);
        $first = array();
        $first['id'] = 0;
        $first['name'] = "全部";
        array_unshift($data, $first);
        return $data;

    }
}