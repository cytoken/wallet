<?php
/**
 * Created by PhpStorm.
 * User: Freeman
 * Date: 2016/10/24
 * Time: 13:37
 */

namespace Admin\Service;

use Enum\RoleEnum;
use Think\Model;
use Admin\Dao\UserDAO;

class UserService extends BaseService
{
    private static $userDAO;

    public function __construct() {
        self::$userDAO = new UserDAO();
    }

    public function queryByStatus($params){
        $db = new Model();
        return self::$userDAO->queryByStatus($db, $params);
    }

    public function searchByConditions($args){
        $db = new Model();
        return self::$userDAO->searchByConditions($db, $args);
    }

    public function activeUser($args){
        $db = new Model();
        return self::$userDAO->activeUser($db,$args);
    }

    public function freezeUser($args){
        $db = new Model();
        return self::$userDAO->freezeUser($db,$args);
    }

    public function getUserDetail($id){
        $db = new Model();
        $result = self::$userDAO->getUserDetail($db,$id);

        if(!empty($result)){
            $result['code'] = 1;
            $result['msg'] = "查询成功";
        }else{
            $result['code'] = 0;
            $result['msg'] = "查询失败";
        }

        return $result;
    }

    public function getFreezeUserData($args){
        $db = new Model();
        return self::$userDAO->getFreezeUserData($db, $args);
    }

    public function export($args){
        $db = new Model();
        $title = array("ID","用户名","姓名","电话","性别","省","城市","学校","年级","地址","qq","状态","创建时间","更新时间");
        $cellNames = array("id","usernaem","realname","phone","gender","province","city","school","grade","address","qq","status","createtime","updatetime");
        $fileName = "用户清单.xlsx";

        $excelService = new ExcelService();
        $excelService->setTitle($title);
        $excelService->setCellNames($cellNames);

        $result = self::$userDAO->searchByConditions($db, $args);
        $totalCount = $result['recordsTotal'];
        $pageSize = 10000;
        $pageCount = $totalCount / $pageSize;
        if($totalCount % $pageSize > 0){
            $pageCount += 1;
        }

        for($i = 0; $i < $pageCount; $i++){
            $args['offset'] = $i * $pageSize;
            $args['pageSize'] = $pageSize;
            $result = self::$userDAO->searchByConditions($db, $args);
            if(empty($result)){
                continue;
            }
            $excelService->setCeils($result['data']);
            unset($result['data']);
        }
        $excelService->downloadFile($fileName);
    }
}