<?php
/**
 * Created by PhpStorm.
 * User: rainbow
 * Date: 16/11/18
 * Time: 上午9:55
 */

namespace Admin\Controller;
use Think\Controller;
use Admin\Service\IndexService;


class IndexController extends AuthController {

    private static $indexService;

    public function _initialize() {
        parent::_initialize();
        self::$indexService = new IndexService();
    }

    /**
     * url:domain/index.php/Home/Index/menu
     * 根据管理员角色获取菜单列表
     */
    public function menu() {
        $admin = session("admin");
        $menus = array();
        if(isset($admin)) {
            $accessSource = $admin['accessSource'];
            $menusMap = $this->filterMenus($accessSource);
            $menus = $this->menus($menusMap, 0);
            $this->loger("menus", $menus);
        }
        $data['code'] = 1;
        $data['data'] = $menus;
        $this->ajaxReturn($data);
    }

    // 菜单迭代器
    private function menus($data, $pid){
        $menus = array();
        foreach($data as $menu) {
            if($menu['pid'] == $pid) {
                $menu['menus'] = $this->menus($data, $menu['id']);
                array_push($menus, $menu);
            }
        }
        return $menus;
    }

    private function filterMenus($data) {
        $menus = array();
        foreach($data as $menu) {
            if($menu['display']==1) {
                array_push($menus, $menu);
            }
        }
        return $menus;
    }
}