<?php
/**
 * Created by PhpStorm.
 * User: Freeman
 * Date: 2017/8/9
 * Time: 14:50
 */

namespace Home\Service;

use Enum\NumEnum;
use Home\Dao\SysDao;
use Enum\AppUserSessionEnum;
use Enum\UserEnum;
use Home\Dao\UserDao;
use Home\Dao\WalletDao;
use Think\Exception;
use Think\Model;
use Enum\SessionEnum;

class UserService extends BaseService
{
    private $db;
    private $userDao;
    private $walletDao;
    private $sysDao;
    private $qrCodeService;

    public function __construct() {
        parent::__construct();
        $this->db = new Model();
        $this->userDao = new UserDao($this->db);
        $this->walletDao = new WalletDao($this->db);
        $this->sysDao = new SysDao($this->db);
        $this->qrCodeService = new QrCodeService();

    }

    /**
     * 用户注册
     * @param $phone
     * @param $passWord
     * @return mixed
     */
    public function signUp($phone, $passWord, $inviteCode) {
        $this->loger('inviteCode', $inviteCode);
        if (!empty($inviteCode)) {
            //通过邀请码获取用户Id
            $pId = $this->userDao->getIdByCode($inviteCode);
            if (empty($pId)) {
                return fail_json("没有匹配的邀请码!");
            }
        } else {
            $pId = -1;
        }

        do {
            $myCode = build_number_Code();
            //通过邀请码先查看是否有用户存在（去重）
            $result = $this->userDao->getIdByCode($myCode);
            $this->loger('$result', $result);
        } while (!empty($result));
         if (empty($phone)){
             return fail_json('抱歉，请输入手机号');
         }

        $phoneOnly = $this->userDao->getByPhone($phone);
        if (!empty($phoneOnly)) {
            return fail_json("该手机号已注册!");
        }
        try {
            $this->db->startTrans();
            $qrCodeUrL = $this->qrCodeService->createQrCode($myCode);
            $this->loger('$qrCodeUrL', $qrCodeUrL);
            $id = $this->userDao->signUp($phone, $passWord, $myCode, $pId, $qrCodeUrL); //用户表创建用户
            $wallet = $this->walletDao->add($id); //为用户创建钱包

            if ($id > 0  && $wallet > 0) {
                $this->db->commit();

                return success_json("注册成功！");
            } else {
                $this->db->rollback();
                return fail_json("注册失败！");
            }
        } catch (Exception $e) {
            $this->db->rollback();
            return fail_json("注册失败！");
        }

    }

    /**
     * 通过扫码注册的需要这个弹出框来验证
     */
    public function getPhoneById($inviteCode) {
        $data = $this->userDao->getPhoneById($inviteCode);
        if (empty($data)) {
            return fail_json('获取上级信息失败，请联系你的邀请人！');
        }
        $data[0]['realname'] = substr_cut($data[0]['realname']);
        $data[0]['phone'] = substr_cut($data[0]['phone']);

        if (empty($data)) {
            return fail_json('获取上级信息失败，请联系你的邀请人！');
        } else {
            $this->loger('$data', $data);
            return success_json('获取上级信息成功！', $data);
        }

    }

    /**
     * 登录
     * @param $phone
     * @param $passWord
     * @param $accessToken
     * @param $deviceToken
     * @param $OS
     * @return mixed
     */
    public function signIn($phone, $passWord) {
        $userInfo = $this->userDao->getByPhone($phone)[0];

        //验证密码
        if ($userInfo['password'] != $passWord) {
            return fail_json("账户名或密码不正确！");
        }

        $wallet = $this->walletDao->getWalletInfoByInvestorId($userInfo['id']);

        $data['id'] = $userInfo['id'];
        $data['walletId'] = $wallet['id'];
        $data['status'] = $userInfo['status'];
        $data['inviteCode'] = $userInfo['inviteCode'];
        $data['phone'] = $userInfo['phone'];
        return success_json("登录成功！", $data);
    }

    /**
     * 忘记密码
     * @param $phone
     * @param $passWord
     * @return mixed
     */
    public function updatePassword($phone, $passWord) {
        $userInfo = $this->userDao->getByPhone($phone)[0];

        if ($passWord == $userInfo['password']) {
            return fail_json("新密码和旧密码一致，请勿重复修改！");
        }

        $data = $this->userDao->updatePassWord($userInfo['id'], $passWord);

        if ($data != 1) {
            return fail_json("修改失败！");
        }
        return success_json("修改成功！");
    }

    /**
     * 获取用户手机号
     * @param $username
     * @return mixed
     */
    public function getUserPhone($username){
        $userInfo = $this->userDao->getByNickName($username)[0];
        if(!empty($userInfo)){
            $phone = $userInfo['phone'];
            return success_json("获取成功！",$phone);
        }else{
            return fail_json('该用户不存在！');
        }

    }

    /**
     * 设置交易密码
     * @param $id
     * @param $passWord
     * @return mixed
     */
    public function updateDealPassWord($id, $passWord) {
        $userInfo = $this->userDao->get($id);
        if ($userInfo[0]['password'] == $passWord) {
            return fail_json("设置失败！交易密码不能与登录密码一致！");
        }

        $data = $this->userDao->updateDealPassWord($id, $passWord);

        if ($data < 1) {
            return fail_json("设置失败！");
        }
        unset($_SESSION['phoneCaptcha']);//删除指定的session
        return success_json("设置成功！");
    }

    /**
     * 交易密码是否正确
     * @param $id
     * @param $dealPass
     * @return bool
     */
    public function checkDealPassword($id, $dealPass) {
        $data = $this->userDao->get($id);
        if ($data[0]['dealPassword'] != $dealPass) {
            return false;
        }
        return true;
    }

    /**
     * 是否设置了交易密码
     * @param $id
     * @return bool
     */
    public function checkDealPasswordIsSet($id) {
        $data = $this->userDao->get($id);
        if (empty($data[0]['dealPassword'])) {
            return false;
        }

        return true;
    }

    /**
     * 设置用户信息
     * @param $realName
     * @param $idCard
     * @param $idCardOnURL
     * @param $idCardOffURL
     * @param $bank
     * @param $openingBank
     * @param $bankCard
     * @param $id
     * @return mixed
     */
    public function update($realName, $nickname, $bank, $openingBank, $bankCard, $id) {
        $data = $this->userDao->update($realName, $nickname, $bank, $openingBank, $bankCard, $id);
        if ($data < 1) {
            return fail_json("设置失败！");
        }
        return success_json("设置成功！");

    }

    /**
     * 获取用户信息
     * @param $id
     * @return mixed
     */
    public function getUserData($userId) {
        $data = $this->userDao->getUserData($userId);

        if (empty($data)) {
            return fail_json("获取数据失败！");
        }

        return success_json("获取数据成功！", $data);
    }

    /**
     * 获取用户信息
     * @param $id
     * @return mixed
     */
    public function get($id) {
        $data = $this->userDao->get($id);

        if (empty($data)) {
            return fail_json("获取数据失败！");
        }

        return success_json("获取数据成功！", $data);
    }

    /**
     * 设置头像
     * @param $id
     * @param $url
     * @return mixed
     */
    public function updateHeadImg($id, $url) {
        $data = $this->userDao->updateHeadImg($id, $url);

        if ($data < 1) {
            return fail_json("设置失败！");
        }

        return success_json("设置成功！");
    }

    /**
     * 会员列表
     * @param $pid
     * @param $offset
     * @param $pageSize
     * @return mixed
     */
    public function memberList($pid, $offset, $pageSize) {
        $data = $this->userDao->memberList($pid, $offset, $pageSize);
        if (empty($data)) {
            return fail_json("获取数据失败！");
        }
        $total = $this->userDao->memberListCount($pid);

        return success_json("获取数据成功！", $data, $total);
    }

    public function updateWalletAddress($id, $walletAddress) {
        $data = $this->userDao->updateWalletAddress($id, $walletAddress);

        if ($data < 1) {
            return fail_json("设置失败！");
        }

        return success_json("设置成功！");
    }
}