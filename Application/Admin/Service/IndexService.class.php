<?php
/**
 * Created by PhpStorm.
 * User: Freeman
 * Date: 2016/10/27
 * Time: 14:53
 */

namespace Admin\Service;


use Admin\Dao\IndexDAO;
use Think\Model;

class IndexService extends BaseService{

    private static $indexDAO;

    public function __construct() {
        self::$indexDAO = new IndexDAO();
    }

    public function canvasPVUV($args){
        $db = new Model();
        $data = self::$indexDAO->getPVandUV($db,$args);

        for($i = 0 ; $i < sizeof($data); $i++){
            $data[$i]['uv'] = $data[$i]['uv']*10 . "";
            $data[$i]['date'] =substr($data[$i]['createtime'],0,11);
        }
        return $data;
    }

    public function getUserChart(){
        $db = new Model();
        return self::$indexDAO->getUserChart($db);
    }

    public function todayIncreaseUser($args){
        $db = new Model();
        return self::$indexDAO->todayIncreaseUser($db,$args);
    }

    public function todayIncreaseJob($args){
        $db = new Model();
        return self::$indexDAO->todayIncreaseJob($db,$args);
    }

    public function todayIncreaseApply($args){
        $db = new Model();
        return self::$indexDAO->todayIncreaseApply($db,$args);
    }

    public function todayIncreaseComp($args){
        $db = new Model();
        return self::$indexDAO->todayIncreaseComp($db,$args);
    }

    public function getUVCount($args){
        $db = new Model();
        return self::$indexDAO->getUVCount($db,$args);
    }

    public function getPVCount($args){
        $db = new Model();
        return self::$indexDAO->getPVCount($db,$args);
    }

    public function getWeChatCount(){
        $db = new Model();
        return self::$indexDAO->getWeChatCount($db);
    }

    public function getDailyUserCount($args){
        $db = new Model();
        return self::$indexDAO->getDailyUserCount($db,$args);
    }

    public function getdailyApplyCount($args){
        $db = new Model();
        return self::$indexDAO->getdailyApplyCount($db,$args);
    }

    public function getSmsRequestCount($args){
        $db = new Model();
        return self::$indexDAO->getSmsRequestCount($db,$args);
    }

    public function getSmsTypeChart(){
        $db = new Model();
        return self::$indexDAO->getSmsTypeChart($db);
    }
}