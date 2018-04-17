<?php
// +----------------------------------------------------------------------
// | 基础业务CRUD方法范例
// +----------------------------------------------------------------------
// | Author: James.Yu <zhenzhouyu@jiechengkeji.cn>
// +----------------------------------------------------------------------

namespace Admin\DAO;

use Think\Model;
use Admin\Dao\BaseDAO;
use Enum\CompanyEnum;
use Tools\XLog;

class UserDAO extends BaseDAO{

    public function queryByStatus($db, $args){

        $offset = $args['offset']; //数据起始位置
        $pageSize = $args['pageSize']; //每页显示条数
        $cityId = $args['admin']['cityid'];

        $querySql = "SELECT count(*) AS total FROM `user`";
        $whereSql = null;
        if ($cityId != 0 || !empty($cityId)){
            $whereSql = " WHERE cityId = $cityId";
        }
        $sql = $querySql.$whereSql;
        $this->loger("sql", $sql);

        $data = $db->query($sql);
        $this->loger("data", $data);
        $total = $data['0']['total'];
        $this->loger("total", $total);

        if ($total > 0) {
            $querySql = "SELECT
                        U. STATUS,
                        U.id,
                        U.username,
                        U.realname,
                        U.phone,
                        U.`gender`,
                        P. NAME AS province,
                        P.id AS provinceId,
                        C.id AS cityId,
                        C. NAME AS city,
                        S. NAME AS school,
                        U.headImgUrl,
                        U.idCardFrontUrl,
                        U.idCardBackUrl,
                        U.intro,
                        U.`grade`,
                        U.`address`,
                        U.`qq`,
                        U.createTime,
                        U.updateTime
                    FROM
                        USER U
                    LEFT JOIN gp_province P ON U.`provinceId` = P.`id`
                    LEFT JOIN gp_city C ON U.`cityId` = C.id
                    LEFT JOIN gp_school S ON U.`schoolId` = S.id";

            $sql = $querySql.$whereSql." ORDER BY U.id DESC LIMIT $offset,$pageSize";
            $this->loger("sql", $sql);

            $data = M()->query($sql);
//            $this->loger("data", $data);

            $result['recordsTotal'] = $total;
            $result['recordsFiltered'] = $total;
            $result['data'] = $data;

        } else {
            $result['recordsTotal'] = $total;
            $result['recordsFiltered'] = $total;
            $result['data'] = [];
            return $result;
        }

        return $result;
    }


    public function searchByConditions($db, $args){
        $provinceId = $args['provinceId'];
        $cityId = $args['cityId'];
        $schoolId = $args['schoolId'];
        $startTime = $args['startTime'];
        $endTime = $args['endTime'];
        $status = $args['status'];
        $offset = $args['offset'];
        $pageSize = $args['pageSize'];

        $Sql = "select count(*) AS total from  user U LEFT JOIN gp_province P ON U.`provinceId`=P.`id` 
    LEFT JOIN gp_city C ON U.`cityId`=C.id LEFT JOIN gp_school S ON U.`schoolId`=S.id";

        $whereSql = " where 1=1";

        if (!empty($provinceId) and $provinceId != 0){
            $whereSql = $whereSql." and U.provinceId=$provinceId";
        }

        if (!empty($cityId) and $cityId != 0){
            $whereSql = $whereSql." and U.cityId=$cityId";
        }

        if (!empty($schoolId) and $schoolId != 0){
            $whereSql = $whereSql." and U.schoolId=$schoolId";
        }

        if (!empty($startTime)){
            $whereSql = $whereSql." and U.createTime between $startTime";
        }

        if (!empty($endTime)){
            $whereSql = $whereSql." and $endTime";
        }

        if (!empty($status) and $status != 0){
            $whereSql = $whereSql." and U.status=$status";
        }
        $Sql = $Sql.$whereSql;

        $this->loger("Sql", $Sql);

        $data = $db->query($Sql);
//        $this->loger("data", $data);
        $total = $data['0']['total'];
        $this->loger("total", $total);

        if ($total > 0) {
            $Sql = "SELECT U.id,U.username,U.realname,U.phone ,U.`gender`,P.name AS province,C.name AS city,S.name AS school ,U.`grade`,U.`address`,U.`qq`,U.status,U.createTime,U.updateTime FROM USER u LEFT JOIN gp_province P ON U.`provinceId`=P.`id` 
                    LEFT JOIN gp_city C ON U.`cityId`=C.id LEFT JOIN gp_school S ON U.`schoolId`=S.id";
            $whereSql = $whereSql." order by U.createTime desc  limit $offset,$pageSize";
            $Sql = $Sql.$whereSql;

            $this->loger("Sql", $Sql);

            $data = M()->query($Sql);
//            $this->loger("data", $data);

            $result['recordsTotal'] = $total;
            $result['recordsFiltered'] = $total;
            $result['data'] = $data;
        } else {
            $result['recordsTotal'] = $total;
            $result['recordsFiltered'] = $total;
            $result['data'] = [];
            return $result;
        }

        return $result;
    }

    public function activeUser($db,$args){
        $this->loger("id", $args['id']);

        $executeSql = "update user set status=1 where id=".$args['id'];
        $this->loger("executeSql", $executeSql);

        $data = M()->execute($executeSql);
//        $this->loger("data", $data);

        if($data) {
            $data['code'] = 1;
            $data['msg'] = "信息已更新";
        } else {
            $data['code'] = 0;
            $data['msg'] = "信息更新失败";
        }
        return $data;
    }

    public function freezeUser($db,$args){
        $this->loger("id", $args['id']);

        $executeSql = "update user set status=2 where id=".$args['id'];
        $this->loger("executeSql", $executeSql);

        $data = M()->execute($executeSql);
//        $this->loger("data", $data);

        if($data) {
            $data['code'] = 1;
            $data['msg'] = "信息已更新";
        } else {
            $data['code'] = 0;
            $data['msg'] = "信息更新失败";
        }
        return $data;
    }

    public function getUserDetail($db,$id){
        $Sql = "SELECT U.status, 
	    	   U.id,
	    	   U.realname,
	    	   U.username,
	    	   U.phone ,
	    	   U.`gender`,
	    	   U.provinceId as provinceId,
	    	   P.name AS province,
	    	   U.cityId as cityId,
	    	   C.name AS city,
	    	   U.schoolId as schoolId,
	    	   S.name AS school ,
	    	   U.`grade`,
	    	   U.`address`,
	    	   U.`qq`,
	    	   U.credit,
	    	   U.birthday,
	    	   U.intro,
	    	   U.degree,
	    	   U.nation,
	    	   U.headImgUrl,
	    	   U.openid,
	    	   U.unionid,
	    	   U.source,
	    	   U.major,
	    	   U.email,
	    	   U.inviteCode,
	   	       U.createTime,
	    	   U.updateTime
		   	FROM USER U 
		   	LEFT JOIN gp_province P ON U.`provinceId`=P.`id` 
		    LEFT JOIN gp_city C ON U.`cityId`=C.id 
		    LEFT JOIN gp_school S ON U.`schoolId`=S.id   
		  	where U.id=$id";

        $this->loger("Sql", $Sql);

        $data = M()->query($Sql);
//        $this->loger("data", $data);
        $result['data'] = $data;
        return $result;
    }

    public function getFreezeUserData($db,$args){
        $offset = $args['offset']; //数据起始位置
        $pageSize = $args['pageSize']; //每页显示条数
        $status = $args['status'];
        $cityId = $args['admin']['cityid'];

        $querySql = "SELECT count(*) AS total FROM
                        USER U
                    LEFT JOIN gp_province P ON U.`provinceId` = P.`id`
                    LEFT JOIN gp_city C ON U.`cityId` = C.id
                    LEFT JOIN gp_school S ON U.`schoolId` = S.id
                    WHERE U.status=$status";

        $whereSql = null;
        if ($cityId != 0 || !empty($cityId)){
            $whereSql = " AND U.cityId = $cityId ";
        }

        $sql = $querySql.$whereSql;
        $this->loger("sql", $sql);

        $data = $db->query($sql);
        $total = $data['0']['total'];
        $this->loger("total", $total);

        if ($total > 0) {
            $querySql = "SELECT
                        U. STATUS,
                        U.id,
                        U.username,
                        U.realname,
                        U.phone,
                        U.`gender`,
                        P. NAME AS province,
                        P.id AS provinceId,
                        C.id AS cityId,
                        C. NAME AS city,
                        S. NAME AS school,
                        U.headImgUrl,
                        U.idCardFrontUrl,
                        U.idCardBackUrl,
                        U.intro,
                        U.`grade`,
                        U.`address`,
                        U.`qq`,
                        U.createTime,
                        U.updateTime
                    FROM
                        USER U
                    LEFT JOIN gp_province P ON U.`provinceId` = P.`id`
                    LEFT JOIN gp_city C ON U.`cityId` = C.id
                    LEFT JOIN gp_school S ON U.`schoolId` = S.id
                    WHERE U.status=$status";

            $sql = $querySql.$whereSql." ORDER BY U.id DESC LIMIT $offset,$pageSize";
            $this->loger("sql", $sql);

            $data = M()->query($sql);

            $result['recordsTotal'] = $total;
            $result['recordsFiltered'] = $total;
            $result['data'] = $data;
        } else {
            $result['recordsTotal'] = $total;
            $result['recordsFiltered'] = $total;
            $result['data'] = [];
            return $result;
        }
        return $result;
    }
 }