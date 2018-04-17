<?php
/**
 * Created by PhpStorm.
 * User: Freeman
 * Date: 2016/10/19
 * Time: 14:41
 */

namespace Admin\Controller;

use Tools\XLog;
use Home\Service\GpService;

class GpController extends AuthController
{
    private static $GpService;

    public function _initialize() {
        parent::_initialize();
        self::$GpService = new GpService();
    }

    public function getCities(){
        $args['admin'] = $this->getAdmin();
        $result = self::$GpService->getCities($args);
        $this->ajaxReturn($result);
    }

    public function getRegions(){
        $cid = I("cId");
        $result = self::$GpService->getAllRegions($cid);
        $this->ajaxReturn($result);
    }

    public function getAllSchool(){
        $cid = I("cId");
        $result = self::$GpService->getAllSchool($cid);
        $this->ajaxReturn($result);
    }

    public function getAllCities(){
        $result = self::$GpService->getAllCities();
        $this->ajaxReturn($result);
    }
}