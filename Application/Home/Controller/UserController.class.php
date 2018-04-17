<?php
/**
 * Created by PhpStorm.
 * User: LiuJing
 * Date: 2017/8/9
 * Time: 14:40
 */

namespace Home\Controller;

use Enum\SessionEnum;
use Enum\UserEnum;
use Home\Service\UserService;

class UserController extends BaseController
{
    private $userService;

    public function __construct() {
        parent::_initialize();
        $this->userService = new UserService();
    }

    /**
     * 注册入口
     * @author wanfeng
     */
    public function signUp() {
        $t1 = microtime(true);
        $this->loger("execute", "signIn()");
        $phone = I("phone", "", "trim"); //手机号
        $passWord = md5(I("passWord", "", "trim")); //密码
        $inviteCode = I("inviteCode", "", "trim"); //邀请码
        $this->loger("phone", $phone);

        $result = $this->userService->signUp($phone, $passWord, $inviteCode);
        $this->timeLogger($t1, __ACTION__);
        $this->ajaxReturn($result);
    }

    /**
     * 通过扫码注册的需要这个弹出框来验证
     */
    public function getPhoneById() {
        $inviteCode = I('inviteCode', '', 'strip_tags');//邀请码
        $data = $this->userService->getPhoneById($inviteCode);
        $this->ajaxReturn($data);
    }

    /**
     * 登录入口
     * @author wanfeng
     */
    public function signIn() {
        $t1 = microtime(true);
        $this->loger("execute", "logIn()");
        $phone = I("phone", ""); //账户名
        $passWord = md5(I("passWord", "", "trim")); //密码

        $data = $this->userService->signIn($phone, $passWord);
        $this->timeLogger($t1, __ACTION__);
        $this->ajaxReturn($data);
    }

    /**
     * 忘记密码
     * @author wanfeng
     */
    public function updatePassword() {
        $phone = I("phone", "", "trim"); //账户名
        $passWord = md5(I("passWord", "", "trim")); //密码
        $this->ajaxReturn($this->userService->updatePassword($phone, $passWord));
    }

    /**
     * 设置交易密码
     * @author wanfeng
     */
    public function updateDealPassWord() {
        $userId = I("userId", 0, "intval");
        $dealPass = md5(I("dealPassWord", "", "trim"));
        $this->ajaxReturn($this->userService->updateDealPassWord($userId, $dealPass));
    }

    /**
     * 验证交易密码
     * @author wanfeng
     */
    public function checkDealPassword() {
        $t1 = microtime(true);
        $userId = I("userId", 0, "intval");
        $dealPass = md5(I("dealPass", "", "trim"));
        $data = $this->userService->checkDealPassword($userId, $dealPass);

        if (!$data) {
            $this->ajaxReturn(fail_json("交易密码错误"));
        }
        $this->timeLogger($t1, __ACTION__);
        $this->ajaxReturn(success_json("验证成功！"));
    }

    /**
     * 设置用户信息
     * @author  fealr
     */
    public function update() {
        $t1 = microtime(true);
        $id = I('userId', 0, 'intval');
        $realName = I('realName', '', 'trim');
        $nickname = I('nickname', '', 'trim');
        $bank = I('bank', '', 'trim');
        $openingBank = I('openingBank', '', 'trim');
        $bankCard = I('bankCard', '', 'trim');

        $this->timeLogger($t1, __ACTION__);
        $this->ajaxReturn($this->userService->update($realName, $nickname, $bank, $openingBank, $bankCard, $id));
    }

    /**
     * 获取用户部分信息
     * @author fealr
     */
    public function getUserData() {
        $t1 = microtime(true);
        $userId = I('userId', '', 'strip_tags');

        $this->timeLogger($t1, __ACTION__);
        $this->ajaxReturn($this->userService->getUserData($userId));
    }

    /**
     * 获取用户资料
     * @author fealr
     */
    public function getUserInfo() {
        $t1 = microtime(true);
        $id = I('userId', 0, 'intval');
        $this->loger("userId", $id);

        $this->timeLogger($t1, __ACTION__);
        $this->ajaxReturn($this->userService->get($id));
    }

    /**
     * 更换头像
     * @author fealr
     */
    public function updateHeadImg() {
        $t1 = microtime(true);
        $id = I('userId', 0, 'intval');
        $url = I('url', '', 'trim');

        $this->timeLogger($t1, __ACTION__);
        $this->ajaxReturn($this->userService->updateHeadImg($id, $url));
    }

    /**
     * 会员列表
     * @author wanfeng
     */
    public function memberList() {
        $t1 = microtime(true);
        $userId = I("userId", 0, "intval");//用户ID
        $offset = I('start', 1, 'intval'); //数据起始位置
        $pageSize = I('length', 10, 'intval'); //每页显示条数

        $offset = ($offset - 1) * $pageSize; //分页

        $this->timeLogger($t1, __ACTION__);
        $this->ajaxReturn($this->userService->memberList($userId, $offset, $pageSize));
    }

    /**
     * 更新用户加密钱包地址
     * @author fealr
     */
    public function updateWalletAddress() {
        $t1 = microtime(true);
        $id = I('userId', 0, 'intval');
        $walletAddress = I('walletAddress', '', 'trim');
        $this->loger("userId", $id);
        $this->loger("walletAddress", $walletAddress);

        $this->timeLogger($t1, __ACTION__);
        $this->ajaxReturn($this->userService->updateWalletAddress($id, $walletAddress));
    }
}