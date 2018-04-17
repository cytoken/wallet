<?php
/**
 * Created by PhpStorm.
 * User: Freeman
 * Date: 2016/10/19
 * Time: 14:42
 */

namespace Admin\Service;


use Enum\RoleEnum;
use Admin\Service\BaseService;
use Admin\Dao\GpDAO;
use Think\Model;

class GpService extends BaseService
{
    private static $GpDao;

    public function __construct(){
        self::$GpDao = new GpDAO();
    }

    public function getCities($params){
        $db = new Model();
        if($params['admin']['roleid']==RoleEnum::ROLE_CITY_ADMIN) {
            $data = self::$GpDao->getCities($db,$params);
            $AllProvince = array();
            $p = array();
            $ProvinceCity = array();
            $city = array();
            $currentProvinceId = -1; //当前的pid,为了触发第一次新建省份初始化为-1
            foreach ($data as $gc){

                if($gc['pid'] != $currentProvinceId){

                    //重新设置当前pid
                    $currentProvinceId = $gc['pid'];
                    //新建立省
                    $p1 = array();

                    //更换当前省城市列表
                    $ProvinceCity = array();
                    $city = array();

                    //每个省的第一个城市是“全部”
//                array_push($proCities,$ProvinceCity);
                    foreach ($data as $c){
                        if ($c['pid'] == $gc['pid']){
                            //把当前循环的市加到当前省的城市列表里
                            array_push($ProvinceCity,$c);
//                        self::loger("ProvinceCity",$ProvinceCity);
                            $p1['cities'] = $ProvinceCity;
                        }
                    }

                    $p1['name'] = $gc['pname'];
                    $p1['id'] = $gc['pid'];
                    $p1['cities'] = array();
                    $p1['cities'] = $ProvinceCity;
                    //把新建的省添加到省列表里
                    array_push($AllProvince,$p1);
                }
//            //把当前循环的市加到当前省的城市列表里
//            array_push($ProvinceCity,$gc);
//            self::loger("ProvinceCity",$ProvinceCity);
//            $p1['cities'] = $ProvinceCity;

            }

            return $AllProvince;
        }else{
            $data = self::$GpDao->getCities($db,$params);
            $AllProvince = array();
            $p = array();
            $p['id'] = 0;
            $p['name']= "全部";
            $ProvinceCity = array();
            $city = array();
            $city['id'] = 0;
            $city['name'] = "全部";
            array_push($ProvinceCity,$city);
            $p['cities'] = $ProvinceCity;
            array_push($AllProvince,$p);
//        $AllProvince['cities'] = $ProvinceCity;
//        array_push($AllProvince,$ProvinceCity);

            $currentProvinceId = -1; //当前的pid,为了触发第一次新建省份初始化为-1

            foreach ($data as $gc){

                if($gc['pid'] != $currentProvinceId){

                    //重新设置当前pid
                    $currentProvinceId = $gc['pid'];
                    //新建立省
                    $p1 = array();

                    //更换当前省城市列表
                    $ProvinceCity = array();
                    $city = array();

                    //每个省的第一个城市是“全部”
//                array_push($proCities,$ProvinceCity);
                    $city['id'] = 0;
                    $city['name'] = "全部";
                    array_push($ProvinceCity,$city);
                    foreach ($data as $c){
                        if ($c['pid'] == $gc['pid']){
                            //把当前循环的市加到当前省的城市列表里
                            array_push($ProvinceCity,$c);
//                        self::loger("ProvinceCity",$ProvinceCity);
                            $p1['cities'] = $ProvinceCity;
                        }
                    }

                    $p1['name'] = $gc['pname'];
                    $p1['id'] = $gc['pid'];
                    $p1['cities'] = array();
                    $p1['cities'] = $ProvinceCity;
                    //把新建的省添加到省列表里
                    array_push($AllProvince,$p1);
                }
//            //把当前循环的市加到当前省的城市列表里
//            array_push($ProvinceCity,$gc);
//            self::loger("ProvinceCity",$ProvinceCity);
//            $p1['cities'] = $ProvinceCity;

            }

            return $AllProvince;
        }

    }

    public function getAllRegions($cid) {
        $db = new Model();
        $data = self::$GpDao->getAllRegions($db,$cid);

        $first = array();
        $first['id'] = 0;
        $first['name'] = "全部";

        array_unshift($data,$first);

        return $data;
    }

    public function getAllSchool($cid){
        $db = new Model();
        $data = self::$GpDao->getAllSchool($db,$cid);

        $first = array();
        $first['id'] = 0;
        $first['name'] = "全部";

        array_unshift($data,$first);

        return $data;
    }

    public function getAllCities(){
        $db = new Model();
        $data = self::$GpDao->getAllCities($db);

        $first = array();
        $first['id'] = 0;
        $first['name'] = "全部";

        array_unshift($data,$first);

        return $data;
    }
}