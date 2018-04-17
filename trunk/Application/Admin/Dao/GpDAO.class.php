<?php
/**
 * Created by PhpStorm.
 * User: Freeman
 * Date: 2016/10/19
 * Time: 14:46
 */

namespace Admin\DAO;

use Think\Model;
use Admin\Dao\BaseDAO;
use Tools\XLog;
class GpDAO extends BaseDAO
{
    public function getCities($db,$params){
        $cityId = $params['admin']['cityid'];
        $Sql = "SELECT c.*,p.name AS pName FROM gp_city c LEFT JOIN gp_province p ON c.`pid`=p.`id` 
		        where c.status =1  ";
        $whereSql = null;
        if($cityId != 0 || !empty($cityId)){
            $whereSql = " and c.id = $cityId ";
        }

        $Sql = $Sql.$whereSql." ORDER BY p.name ASC, pid, id";
        $this->loger("Sql", $Sql);

        $data = $db->query($Sql);
        $this->loger("data", $data);
        return $data;
    }


    public function getAllRegions($db,$cid){
        $Sql = "select * from gp_region where status=1 and cId=$cid ORDER BY name ASC";

        $this->loger("Sql", $Sql);

        $data = $db->query($Sql);
        $this->loger("data", $data);
        return $data;
    }

    public function getAllSchool($db,$cid){
        $Sql = "select * from gp_school where status=1 and cId=$cid ORDER BY name ASC";

        $this->loger("Sql", $Sql);

        $data = $db->query($Sql);
        $this->loger("data", $data);
        return $data;
    }

    public function getAllCities($db){
        $Sql = "SELECT c.*,p.name AS pName FROM gp_city c LEFT JOIN gp_province p ON c.`pid`=p.`id` 
		    where c.status =1 ORDER BY p.name ASC, pid, id";

        $this->loger("Sql", $Sql);

        $data = $db->query($Sql);
        $this->loger("data", $data);
        return $data;
    }
}