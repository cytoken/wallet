<?php
/**
 * Created by PhpStorm.
 * User: Freeman
 * Date: 2017/8/14
 * Time: 16:55
 */

namespace Home\Service;

use Enum\SysEnum;
use Enum\UserEnum;
use Home\Dao\SysDao;
use Home\Dao\WalletDao;
use Home\Dao\UserDao;
use Think\Exception;
use Think\Model;

class WalletService extends BaseService {

    private $db;
    private $walletDao;
    private $sysDao;
    private $userDao;

    public function __construct() {
        $this->db = new Model();
        $this->walletDao = new WalletDao($this->db);
        $this->sysDao = new SysDao($this->db);
        $this->userDao = new UserDao($this->db);
    }


    /**
     * @param $walletId
     * @param $userId
     * @param $money
     * @return mixed
     * 
     */
    public function withdraw($walletId, $userId, $money,$type) {
        $userInfo = $this->userDao->get($userId);

        if($userInfo[0]['state'] !=UserEnum::USER_ACCOUNT_ON){
            return fail_json("您的账户已被冻结,请等待管理员解冻后再提现！");
        }
        if($userInfo[0]['status'] != 1) {
            return fail_json("请完善个人信息,并等待审核通过后再提现！");
        }
        if($money<0||$money == 0){
            return fail_json("提现金额不能小于0！");
        }

        $data = $this->walletDao->getWalletInfoByInvestorId($userId);
        if ($type==0){
            if ($money > $data['bitcoin']) {
                return fail_json("余额不足！");
            }

           $this->loger('优贝提现金额',$money % 20000);
            $moneys=$money%20000;
            $moneyss=$money%50000;
            if (!($moneys== 0 || $moneyss== 0)) {
                return fail_json("提现必须是200或者500的倍数!");
            }

        }elseif($type==1){
            if ($money > $data['money']) {
                return fail_json("余额不足！");
            }
            $this->loger('余额提现金额',$money);

        }

        $orderNo = build_order_no();

        $this->loger('money',$money);
        try{
            $this->db->startTrans();
            $bitcoin=0;
            if ($type==0){//优贝提现
                $rate = $this->walletDao->getCodeRate(SysEnum::WITHDRAW_RATE_BALANCE);//提现手续费
                $this->loger('优贝手续费',$rate);
                $outBalance = $this->walletDao->outBitcoin($userId, $bitcoin, $money);

                $serviceCharge = $money*($rate-20)/100;//手续费

                $changeAmount = $money*($rate-2)/100;//转日消费优贝的金额

                $addWithdraw = $this->walletDao->addWithdrawLog($walletId, $money, $serviceCharge ,$changeAmount,$orderNo, $rate, $type);
                if ($outBalance<0 || $addWithdraw<0){
                    $this->db->rollback();
                    return fail_json("申请提现失败！");
                }

            }elseif ($type==1){
                $rate = $this->walletDao->getCodeRate(SysEnum::WITHDRAW_RATE);//提现手续费
                $this->loger('余额手续费',$rate);

                if($money < 10000){//提现金额小于100元的，到账金额为100元，额外收2元手续费,可以视作提现费率为0，但是手续费为2%
                    $serviceCharge = 200;//手续费
                    $changeAmount = 0;//转日消费优贝的金额
                    $outBalance = $this->walletDao->outBitcoin($userId, $money, $bitcoin);
                }else{
                    $serviceCharge = ($money* $rate)/100;//手续费
                    $changeAmount = 0;//转日消费优贝的金额
                    $outBalance = $this->walletDao->outBitcoin($userId, $money, $bitcoin);
                }

                $addWithdraw = $this->walletDao->addWithdrawLog($walletId, $money, $serviceCharge ,$changeAmount,$orderNo, $rate,$type);
                if ($outBalance<0 || $addWithdraw<0){
                    $this->db->rollback();
                    return fail_json("申请提现失败！");
                }
            }

            $this->db->commit();
            return success_json("申请提现成功！");
        }catch (Exception $e) {
            $this->db->rollback();
            return fail_json("申请提现失败！");
        }
    }

    /**
     * 检查金额是否满足交易
     * @param $userId
     * @param $money
     * @return bool
     */
    public function checkBalance ($userId, $money) {
         $data = $this->walletDao->getWalletInfoByInvestorId($userId);
         if ($money > $data['bitcoin']) {
             return false;
         }
         return true;
    }

    /**
     * 获取提现费率
     */
    public function getRate(){
        $data = $this->walletDao->getRate(SysEnum::WITHDRAW_RATE_ID);
        return success_json("获取数据成功！", $data);
    }

    /**
     * 获取钱包数据
     * @param $userId
     * @return mixed
     */
    public function getWalletInfo ($userId) {

        $data = $this->walletDao->getWalletInfoByInvestorId($userId);

        $data['frozenAmount'] = $data['frozenAmount']/100;//银豆
        $data['freeAmount'] = $data['freeAmount']/100;
        $data['amount'] = $data['amount']/100;
        $data['total'] = $data['frozenAmount'] + $data['amount'];
        $this->loger('获取数据成功',$data);
        return success_json("获取数据成功！", $data);
    }

    /**
     * 充值记录
     * @param $walletId
     * @return mixed
     */
    public function getRechargeLog ($userId, $status, $offset, $pageSize) {
        $total = $this->walletDao->getRechargeLogCount($userId, $status);
        if ($total){
            $data = $this->walletDao->getRechargeLog($userId, $status, $offset, $pageSize);
            return success_json("获取数据成功！", $data, $total);
        }else{
            return fail_json('获取数据失败');
        }
    }

    /**
     * 提现记录
     * @param $walletId
     * @return mixed
     */
    public function getWithdrawLog ($userId, $status, $offset, $pageSize) {
        $total = $this->walletDao->getWithdrawLogCount($userId, $status);
        if ($total){
            $data = $this->walletDao->getWithdrawLog($userId, $status, $offset, $pageSize);
            return success_json("获取数据成功！", $data, $total);
        }else{
            return fail_json('获取数据失败！');
        }

    }

    /**
     * 充值记录
     * @param $walletId
     * @return mixed
     */
    public function getBonusLog ($userId, $offset, $pageSize) {
        $total = $this->walletDao->getBonusLogCount($userId);
        if ($total){
            $data = $this->walletDao->getBonusLog($userId, $offset, $pageSize);
            return success_json("获取数据成功！", $data, $total);
        }else{
            return fail_json('获取数据失败');
        }
    }

}