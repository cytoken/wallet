<?php
/**
 * Created by PhpStorm.
 * User: jiechengkeji
 * Date: 2017/8/9
 * Time: 14:13
 */

namespace Home\Dao;


class GpDao extends BaseDao
{
    public function __construct($db = null)
    {
        parent::__construct($db);
    }

    /**
     * @return mixed获取省的列表
     */
    public function getProvince()
    {
        $Sql = "select * from gp_province where status=1 ORDER BY weight DESC";
        $data = $this->db->query($Sql);
        $this->logger("data", $data);
        return $data;
    }

    /**
     * 获取市的列表
     */

    public function getCity($pid)
    {
        $Sql = "select * from gp_city where status=1 and pId=$pid ORDER BY weight DESC";
        $data = $this->db->query($Sql);
        $this->logger("data", $data);
        return $data;
    }

    /**
     * 获取区的列表
     */

    public function getRegions($cid)
    {
        $Sql = "select * from gp_region where status=1 and cId=$cid ORDER BY id ASC";
        $data = $this->db->query($Sql);
        $this->logger("data", $data);
        return $data;
    }

    /**
     * 获取小区的列表
     */
    public function getVillage($rid)
    {
        $Sql = "select * from gp_village where status=1 and rId=$rid ORDER BY id ASC";
        $data = $this->db->query($Sql);
        $this->logger("data", $data);
        return $data;
    }
}