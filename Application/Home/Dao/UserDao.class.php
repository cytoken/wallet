<?php
/**
 * Created by PhpStorm.
 * User: Freeman
 * Date: 2017/8/9
 * Time: 14:52
 */

namespace Home\Dao;


use Enum\BonusEnum;
use Enum\MallEnum;
use Enum\NumEnum;
use Enum\UserEnum;
use Tools\APIUtil;

class UserDao extends BaseDao
{
    public function __construct($db) {
        parent::__construct($db);
    }

    /**
     * 获取用户信息
     * @param $id
     * @return mixed
     * @author
     */
    public function get($id) {
        $querySql = "SELECT * FROM user WHERE id = %d";
        return $this->db->query($querySql, $id);
    }

    /**
     * 获取用户信息
     * @param $id
     * @return mixed
     * @author
     */
    public function getUserData($id) {
        $querySql = "SELECT user_wallet.id as walletId,user.headimgurl,user.id as userId,user.phone as userPhone FROM user left join user_wallet on user.id=user_wallet.userId WHERE user.id = '%s'";
        return $this->db->query($querySql, $id);
    }

    /**
     * 通过手机号获取用户信息
     * @param $phone
     * @return mixed
     */
    public function getByPhone($phone) {
        $querySql = "SELECT * FROM user WHERE phone = '%s'";
        return $this->db->query($querySql, $phone);
    }

    /**
     * 用户注册
     * @param $phone
     * @param $passWord
     * @param $inviteCode
     * @param $pId
     * @return mixed
     */
    public function signUp($phone, $passWord, $inviteCode, $pId, $qrCodeUrL) {
        $insertSql = "INSERT INTO user (phone, password, inviteCode, pid,qrCodeUrL) VALUES ('%s', '%s', '%s', %d,'%s')";
        $this->db->execute($insertSql, array($phone, $passWord, $inviteCode, $pId, $qrCodeUrL));
        $result = $this->db->query("SELECT last_insert_id() AS id");
        $id = $result[0]['id'];
        return $id;

    }


    /**
     * 通过扫码注册的需要这个弹出框来验证
     */
    public function getPhoneById($inviteCode) {
        $sql = "select ifnull(realname,'YOU优用户') AS realname,phone from user where inviteCode='%s'";
        $data = $this->db->query($sql, $inviteCode);
        return $data;
    }

    /**
     * 通过邀请码查询邀请者id
     * @param $code
     * @return mixed
     */
    public function getIdByCode($code) {
        $sql = "SELECT id FROM user WHERE inviteCode = '%s'";
        $data = $this->db->query($sql, $code);
        if (empty($data)) {
            return 0;
        } else {
            return $data[0]['id'];
        }
    }

    /**
     * 检查用户来源
     * @param $userId
     * @return mixed
     */
    public function checkUserSource($userId) {
        $sql = "SELECT * FROM user_invest_used WHERE  userId = %d AND leaderId IN(28345, 41796)  ";
        return $this->db->query($sql, $userId);
    }


    /**
     * 更新密码
     * @param $id
     * @param $passWord
     * @return mixed
     */
    public function updatePassWord($id, $passWord) {
        $updateSql = "UPDATE user SET password = '%s' WHERE id = %d";
        return $this->db->execute($updateSql, array($passWord, $id));
    }

    /**
     * 设置交易密码
     * @param $id
     * @param $passWord
     * @return mixed
     */
    public function updateDealPassWord($id, $passWord) {
        $sql = "UPDATE user SET dealPassword = '%s' WHERE id = %d ";
        return $this->db->execute($sql, array($passWord, $id));
    }

    /**
     * 检查手机是否注册
     * @param $phone
     * @return mixed
     */
    public function getPhoneByName($username) {
        $querySql = "SELECT phone FROM user WHERE username = '%s'";
        $data = $this->db->query($querySql, $username);
        return $data[0]['phone'];
    }

    /**
     * 设置用户资料
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

        $sql = "UPDATE user SET realname = '%s', nickname = '%s', bank = '%s', openingBank = '%s', bankCard = '%s', status = %d WHERE id = %d";

        return $this->db->execute($sql, array($realName, $nickname, $bank, $openingBank, $bankCard, NumEnum::USER_STATUS_ZERO, $id));
    }

    /**
     * 设置头像
     * @param $id
     * @param $url
     * @return mixed
     */
    public function updateHeadImg($id, $url) {
        $sql = "UPDATE user SET headimgurl = '%s' WHERE id = %d";

        return $this->db->execute($sql, array($url, $id));
    }

    /**
     * 会员列表
     * @param $pid
     * @param $offset
     * @param $pageSize
     * @return mixed
     */
    public function memberList($pid, $offset, $pageSize) {
        $sql = "SELECT id, phone, realname ,headimgurl FROM user WHERE pid = %d ORDER BY createTime DESC LIMIT %d, %d";
        return $this->db->query($sql, array($pid, $offset, $pageSize));
    }

    /**
     * 会员列表
     * @param $pid
     * @return mixed
     */
    public function memberListCount($pid) {
        $sql = "SELECT count(*) AS total FROM user WHERE pid = %d";
        return $this->db->query($sql, $pid)[0]['total'];
    }

    public function updateWalletAddress($id, $walletAddress) {
        $sql = "UPDATE user SET walletAddress = '%s' WHERE id = %d";

        return $this->db->execute($sql, array($walletAddress, $id));
    }

}