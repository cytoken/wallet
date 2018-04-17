<?php
/**
 * Created by PhpStorm.
 * User: Freeman
 * Date: 2016/10/27
 * Time: 14:51
 */

namespace Admin\DAO;
use Admin\Dao\BaseDAO;

class IndexDAO extends BaseDAO{
    public function getPVandUV($db,$args){
        $start = $args['start'];
        $end = $args['end'];

        $Sql = "SELECT * FROM bi_stat_request WHERE createTime BETWEEN '$start' and '$end'";

        $data = $db->query($Sql);

        $this->loger("Sql",$Sql);
        $this->loger("data",$data);

        return $data;
    }

    public function getUserChart($db){
        $Sql = "select * from bi_dist_province_user";

        $data = $db->query($Sql);

        $this->loger("Sql",$Sql);
        $this->loger("data",$data);

        return $data;
    }

    public function todayIncreaseUser($db,$args){
        $start = $args['start'];
        $end = $args['end'];

        // 取得当前用户的城市ID
        $cityId = $args['admin']['cityid'];

        if ($cityId != 0 || !empty($cityId)){
            $Sql = "select count(*) as total from user where cityId = $cityId AND createTime between '$start' and '$end'";
        }else{
            $Sql = "select count(*) as total from user where createTime between '$start' and '$end'";
        }

        $data = $db->query($Sql);

        $this->loger("Sql",$Sql);
        $this->loger("data",$data);

        if(empty($data)){
            $data[0]['total'] = 0;
        }

        return $data[0]['total'];
    }

    public function todayIncreaseJob($db,$args){
        $start = $args['start'];
        $end = $args['end'];

        // 取得当前用户的城市ID
        $cityId = $args['admin']['cityid'];

        if ($cityId != 0 || !empty($cityId)){
            $Sql = "select count(*) as total from job where cityId = $cityId AND createTime between '$start' and '$end'";
        }else{
            $Sql = "select count(*) as total from job where createTime between '$start' and '$end'";
        }

        $data = $db->query($Sql);

        $this->loger("Sql",$Sql);
        $this->loger("data",$data);

        if(empty($data)){
            $data[0]['total'] = 0;
        }

        return $data[0]['total'];
    }

    public function todayIncreaseApply($db,$args){
        $start = $args['start'];
        $end = $args['end'];
        $cityId = $args['admin']['cityid'];

        if ($cityId != 0 || !empty($cityId)){
            $Sql = "select count(*) as total from job_apply a LEFT JOIN job j ON j.id = a.jobId where j.cityId = $cityId AND a.createTime between '$start' and '$end'";
        }else{
            $Sql = "select count(*) as total from job_apply where createTime between '$start' and '$end'";
        }

        $data = $db->query($Sql);

        $this->loger("Sql",$Sql);
        $this->loger("data",$data);

        if(empty($data)){
            $data[0]['total'] = 0;
        }

        return $data[0]['total'];
    }

    public function todayIncreaseComp($db,$args){
        $start = $args['start'];
        $end = $args['end'];

        // 取得当前用户的城市ID
        $cityId = $args['admin']['cityid'];

        if ($cityId != 0 || !empty($cityId)){
            $Sql = "select count(*) as total from company where cityId = $cityId AND createTime between '$start' and '$end'";
        }else{
            $Sql = "select count(*) as total from company where createTime between '$start' and '$end'";
        }

        $data = $db->query($Sql);

        $this->loger("Sql",$Sql);
        $this->loger("data",$data);

        if(empty($data)){
            $data[0]['total'] = 0;
        }

        return $data[0]['total'];
    }

    public function getUVCount($db,$args){
        $start = $args['start'];
        $end = $args['end'];

        $Sql = "SELECT COUNT(DISTINCT(userId)) as total FROM bi_log where createTime between '$start' and '$end' and userId IS NOT NULL;";

        $data = $db->query($Sql);

        $this->loger("Sql",$Sql);
        $this->loger("data",$data);

        if(empty($data)){
            $data[0]['total'] = 0;
        }

        return $data[0]['total'];
    }

    public function getPVCount($db,$args){
        $start = $args['start'];
        $end = $args['end'];

        $Sql = "SELECT COUNT(*) as total FROM bi_log where createTime between '$start' and '$end'";

        $data = $db->query($Sql);

        $this->loger("Sql",$Sql);
        $this->loger("data",$data);

        if(empty($data)){
            $data[0]['total'] = 0;
        }

        return $data[0]['total'];
    }

    public function getWeChatCount($db){
        $Sql = "SELECT COUNT(*) as total  FROM bi_log where source='wechat.luobojianzhi.com'";

        $data = $db->query($Sql);

        $this->loger("Sql",$Sql);
        $this->loger("data",$data);

        if(empty($data)){
            $data[0]['total'] = 0;
        }

        return $data[0]['total'];
    }
    /**
     * 获取城市日增用户
     */
    public function getDailyUserCount($db,$args){
        $start = $args['start'];
        $end = $args['end'];
        $cityId = $args['admin']['cityid'];
        $role = $args['admin']['roleid'];

        if ($role == 1){
            $Sql = "SELECT  DATE_FORMAT(createTime,'%Y-%m-%d') AS createTime,SUM(count) as count FROM bi_user_daily WHERE createTime BETWEEN '$start' AND '$end' GROUP BY createTime";
        } else {
            $Sql = "SELECT DATE_FORMAT(createTime,'%Y-%m-%d') AS createTime,count FROM bi_user_daily WHERE cityId = $cityId AND createTime BETWEEN '$start' AND '$end'";
        }
        $data = $db->query($Sql);

        $this->loger("Sql",$Sql);

        return $data;
    }

    /**
     * 获取城市职位日申请量
     */
    public function getdailyApplyCount($db,$args){
        $start = $args['start'];
        $end = $args['end'];
        $cityId = $args['admin']['cityid'];
        $role = $args['admin']['roleid'];

        if ($role == 1){
            $Sql = "SELECT  DATE_FORMAT(createTime,'%Y-%m-%d') AS createTime,SUM(count) as count FROM bi_apply_daily WHERE createTime BETWEEN '$start' AND '$end' GROUP BY createTime";
        } else{
            $Sql = "SELECT DATE_FORMAT(createTime,'%Y-%m-%d') AS createTime,count FROM bi_apply_daily WHERE cityId = $cityId AND createTime BETWEEN '$start' AND '$end'";
        }

        $data = $db->query($Sql);

        $this->loger("Sql",$Sql);

        return $data;
    }

    /**
     * 短信发送量
     */
    public function getSmsRequestCount($db,$args){
        $start = $args['start'];
        $end = $args['end'];

        $Sql = "SELECT meisheng,chenglixin,createTime FROM bi_sms_request WHERE source = 'luobojianzhi.com' and createTime BETWEEN '$start' AND '$end'";

        $data = $db->query($Sql);

        $this->loger("Sql",$Sql);

        return $data;
    }

    /**
     * 各种短信发说说比重
     */
    public function getSmsTypeChart($db){
        $Sql = "SELECT * FROM bi_dist_type_sms WHERE source = 'luobojianzhi.com'";

        $data = $db->query($Sql);

        $this->loger("Sql",$Sql);

        return $data;
    }
}