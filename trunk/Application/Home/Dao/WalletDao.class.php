<?php
/**
 * Created by PhpStorm.
 * User: Freeman
 * Date: 2017/8/14
 * Time: 16:13
 */

namespace Home\Dao;


use Enum\NumEnum;
use Enum\RechargeEnum;
use Enum\SysEnum;
use Enum\WalletEnum;

class WalletDao extends BaseDao {

    public function __construct ($db) {
        parent::__construct($db);
    }

    /**
     * 为用户创建钱包
     * @param $investorId
     * @return mixed
     */
    public function add($investorId) {
        $sql = "INSERT INTO user_wallet (userId) VALUES (%d)";
        return $this->db->execute($sql,array($investorId));
    }

    /**
     * 增加一条充值记录
     * @param $walletId
     * @param $money
     * @return array
     */
    public function userRecharge($walletId,$money,$orderNo,$rechargeType){

        $sql = "INSERT INTO user_recharge (walletId, amount, orderNo,rechargeType) VALUES (%d,%d,'%s',%d)";
        $this->db->execute($sql, array($walletId, $money, $orderNo,$rechargeType));
        $data = $this->db->query("SELECT last_insert_id() AS id");
        $id = $data[0]['id'];
        $result = array('id'=>$id, 'orderNo'=>$orderNo);
        return $result;
    }

    /**
     * 由充值记录ID获取充值人信息
     * @param $rechargeId
     */
    public function userInfo($rechargeId){
        $sql = "SELECT u.realname, u.phone, ur.orderNo, ur.amount, sa.account, sa.type,ur.adminBankUserId AS assistantId, ur.rechargeImage
                FROM user u 
                LEFT JOIN user_wallet uw ON u.id = uw.userId
                LEFT JOIN user_recharge ur ON uw.id = ur.walletId 
                LEFT JOIN sys_bank_account sa ON ur.rechargeChannelId = sa.id  WHERE ur.id = %d";
        $data = $this->db->query($sql, $rechargeId);
        return $data;
    }
    /**
     * 获取提现手续费
     * @param $code
     * @return mixed
     */
    public function getRate($code){
        $sql = "SELECT `value` FROM sys_config WHERE pId = %d";
        $data = $this->db->query($sql,$code);
        return $data;
    }

    /**
     * 获取钱包信息
     * @param $userId
     * @return mixed
     */
    public function getWalletInfoByInvestorId ($userId) {
        $sql = "SELECT * FROM user_wallet WHERE userId = %d";
        return $this->db->query($sql, $userId)[0];
    }

    /**
     * 充值记录
     * @param $walletId
     * @return mixed
     */
    public function getRechargeLog ($userId, $status, $offset, $pageSize) {
        $status = $status."";
        $sql = "SELECT ir.id, ir.userId, truncate(ir.amount/100,2) AS money,
                ir.orderNo,ir.status,ir.type,ir.createTime
                FROM user_recharge ir
                WHERE ir.userId = %d AND ir.status IN (%s)
                ORDER BY ir.createTime DESC LIMIT %d,%d";
        return $this->db->query($sql, array($userId, $status, $offset, $pageSize));
    }

    public function getRechargeLogCount ($userId, $status) {
        $sql = "SELECT COUNT(*) AS total
                FROM user_recharge ir
                WHERE ir.userId = %d AND ir.status IN (%s)  ";
        return $this->db->query($sql, array($userId, $status))[0]['total'];
    }

    /**
     * 提现记录
     * @param $walletId
     * @param $status
     * @param $offset
     * @param $pageSize
     * @return mixed
     */
    public function getWithdrawLog ($userId, $status, $offset, $pageSize) {
        $status = $status."";
        $sql = "SELECT w.id, w.userId, truncate(w.amount/100,2) AS amount, if(w.amount/100 * w.rate/100>= %d,truncate(w.amount/100 * w.rate/100,2),truncate(5,2)) AS serviceMoney, w.orderNo, w.status, w.remark, w.createTime,
                i.bank, i.openingBank, i.bankCard, i.realname
                FROM user_withdraw w 
                LEFT JOIN  user i ON i.id = w.userId
                WHERE w.userId = %d AND w.status IN (%s) ORDER BY w.createTime DESC LIMIT %d,%d";
        return $this->db->query($sql, array(NumEnum::RATE_MIN_AMOUNT,$userId, $status, $offset, $pageSize));
    }

    public function getWithdrawLogCount ($userId, $status) {
        $sql = "SELECT count(*) AS total FROM user_withdraw WHERE userId = %d AND status IN (%s)";
        return $this->db->query($sql, array($userId, $status))[0]['total'];
    }

    /**
     * 获取消费优贝明细
     */
    public function getBonusLog($userId, $offset, $pageSize){
        $sql = "SELECT bonusAmount * 0.01 AS amount, bonusCode , createTime, orderId
                FROM user_bonus_static_card
                WHERE userId = %d ORDER BY createTime DESC"." limit $offset,$pageSize";
        return $this->db->query($sql, array($userId));
    }
    public function getBonusLogCount($userId){
        $sql = "SELECT COUNT(*) AS total FROM user_bonus_static_card WHERE userId = %d  ";
        $data = $this->db->query($sql, array($userId, 1 ));
        return $data[0]['total'];
    }

}