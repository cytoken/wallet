<?php
// +----------------------------------------------------------------------
// | 基础业务CRUD方法范例
// +----------------------------------------------------------------------
// | Author: James.Yu <zhenzhouyu@jiechengkeji.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Admin\Service\UserService;
use Think\Controller;
use Think\Model;

class UserController extends AuthController{

    private static $userService;

    public function _initialize() {
        parent::_initialize();
        self::$userService = new UserService();
    }

    public function doLogin($id=0){

        $username = I('username', '', 'strip_tags');
        $password = I('password', '', 'strip_tags');

        $this->loger("username", $username);
        $this->loger("password", $password);
        $password = md5($password);

        $data = M()->query("SELECT admin.id, admin.username, admin.provinceId, admin.cityId, urr.roleId
                            FROM admin_user AS admin 
                            LEFT JOIN admin_auth_user_role_rel AS urr 
                            ON admin.id = urr.userId 
                            WHERE admin.username = '$username' AND admin.password = '$password' AND urr.status=1");
        $this->loger("data", $data);

        $admin = $data[0];
        $this->loger("admin", $admin);

        if(!$admin) {
            $this->ajaxReturn(json_fail());
        }

        $data = M()->query("SELECT r.id, r.pId, r.name, r.url, r.type, r.weight, r.display,r.icon
                            FROM admin_auth_resource AS r
                            LEFT JOIN admin_auth_role_resource_rel AS urr
                            ON r.id = urr.resourceId 
                            WHERE urr.roleId =" .$admin['roleid']." AND urr.status = 1 ORDER BY r.pid, r.weight");
        $this->loger("data", $data);

        $admin['accessSource'] = $data;
        $roleMap = array();
        foreach ($data as $item) {
            array_push($roleMap, $item['url']);
        }
        $admin['accessMap'] = $roleMap;
        $this->loger("admin", $admin);
        
        session("admin", $admin);
        $this->loger("session", session("admin"));

        $this->ajaxReturn(json_success($admin, "信息已更新"));
    }

    public function logout(){
        $_SESSION = array(); //清除SESSION值.
        if(isset($_COOKIE[session_name()])){  //判断客户端的cookie文件是否存在,存在的话将其设置为过期.
            setcookie(session_name(),'',time()-1,'/');
        }
        session_destroy();  //清除服务器的sesion文件
        $this->redirect('/');
    }

    public function all(){
        //1. 对输入数据进行验证
        $draw = I('draw', 0, 'intval'); //绘制计数器，回传给datatables
        $offset = I('start', 1, 'intval'); //数据起始位置
        $pageSize = I('length', 10, 'intval'); //每页显示条数

        //2. 对输入数据进行log记录
        $this->loger("draw", $draw);
        $this->loger("offset", $offset);
        $this->loger("pageSize", $pageSize);

        $args = array();
        $args['offset'] = $offset;
        $args['pageSize'] = $pageSize;
        $args['admin'] = $this->getAdmin();

        //3. 返回数据
        $result = self::$userService->queryByStatus($args);
        $result['draw'] = $draw;

        $this->ajaxReturn($result);
    }

    public function searchByConditions(){
        $provinceId = I("provinceId");
        $cityId = I("cityId");
        $schoolId = I("schoolId");
        $startTime = I("startTime");
        $endTime = I("endTime");
        $status = I("status");
        $draw = I('draw', 0, 'intval'); //绘制计数器，回传给datatables
        $offset = I('start', 1, 'intval'); //数据起始位置
        $pageSize = I('length', 10, 'intval'); //每页显示条数


        $this->loger("provinceId", $provinceId);
        $this->loger("cityId", $cityId);
        $this->loger("schoolId", $schoolId);
        $this->loger("startTime", $startTime);
        $this->loger("endTime", $endTime);
        $this->loger("status", $status);
        $this->loger("draw", $draw);
        $this->loger("offset", $offset);
        $this->loger("pageSize", $pageSize);

        $args = array();
        $args['provinceId'] = $provinceId;
        $args['cityId'] = $cityId;
        $args['schoolId'] = $schoolId;
        $args['startTime'] = $startTime;
        $args['endTime'] = $endTime;
        $args['status'] = $status;
        $args['offset'] = $offset;
        $args['pageSize'] = $pageSize;

        //3. 返回数据
        $result = self::$userService->searchByConditions($args);
        $result['draw'] = $draw;

        $this->ajaxReturn($result);
    }

    public function export(){
        $this->loger("export", "export()");
        $provinceId = I("provinceId");
        $cityId = I("cityId");
        $schoolId = I("schoolId");
        $startTime = I("startTime");
        $endTime = I("endTime");
        $status = I("status");
        $draw = I('draw', 0, 'intval'); //绘制计数器，回传给datatables
        $offset = I('start', 0, 'intval'); //数据起始位置
        $pageSize = I('length', 10, 'intval'); //每页显示条数
        
        $this->loger("provinceId", $provinceId);
        $this->loger("cityId", $cityId);
        $this->loger("schoolId", $schoolId);
        $this->loger("startTime", $startTime);
        $this->loger("endTime", $endTime);
        $this->loger("status", $status);
        $this->loger("draw", $draw);
        $this->loger("offset", $offset);
        $this->loger("pageSize", $pageSize);

        $args = array();
        $args['provinceId'] = $provinceId;
        $args['cityId'] = $cityId;
        $args['schoolId'] = $schoolId;
        $args['startTime'] = $startTime;
        $args['endTime'] = $endTime;
        $args['status'] = $status;
        $args['offset'] = $offset;
        $args['pageSize'] = $pageSize;

        self::$userService->export($args);
        
    }

    //启用
    public function activeUser(){
        $id = I('id', 0, 'intval');
        $this->loger("id", $id);
        $args['id'] = $id;

        $result = self::$userService->activeUser($args);
        $this->ajaxReturn($result);
    }

    //禁用
    public function freezeUser(){
        $id = I('id', 0, 'intval');
        $this->loger("id", $id);
        $args['id'] = $id;

        $result = self::$userService->freezeUser($args);
        $this->ajaxReturn($result);
    }
    //查看详情
    public function getUserDetail(){
        $id = I("userId");

        $result = self::$userService->getUserDetail($id);

        $this->ajaxReturn($result);
    }

    public function getFreezeUserData(){
        //1. 对输入数据进行验证
        $draw = I('draw', 0, 'intval'); //绘制计数器，回传给datatables
        $offset = I('start', 1, 'intval'); //数据起始位置
        $pageSize = I('length', 10, 'intval'); //每页显示条数

        //2. 对输入数据进行log记录
        $this->loger("draw", $draw);
        $this->loger("offset", $offset);
        $this->loger("pageSize", $pageSize);

        $args = array();
        $args['offset'] = $offset;
        $args['pageSize'] = $pageSize;
        $args['status'] = 2;
        $args['admin'] = $this->getAdmin();

        //3. 返回数据
        $result = self::$userService->getFreezeUserData($args);
        $result['draw'] = $draw;

        $this->ajaxReturn($result);
    }

    public function getCheckedUserData(){
        //1. 对输入数据进行验证
        $draw = I('draw', 0, 'intval'); //绘制计数器，回传给datatables
        $offset = I('start', 1, 'intval'); //数据起始位置
        $pageSize = I('length', 10, 'intval'); //每页显示条数

        //2. 对输入数据进行log记录
        $this->loger("draw", $draw);
        $this->loger("offset", $offset);
        $this->loger("pageSize", $pageSize);

        $args = array();
        $args['offset'] = $offset;
        $args['pageSize'] = $pageSize;
        $args['status'] = 1;
        $args['admin'] = $this->getAdmin();

        //3. 返回数据
        $result = self::$userService->getFreezeUserData($args);
        $result['draw'] = $draw;

        $this->ajaxReturn($result);
    }

 }