<?php
/**
 * Created by PhpStorm.
 * User: Freeman
 * Date: 2017/8/15
 * Time: 9:03
 */

namespace Home\Controller;


use Home\Service\WalletService;

class WalletController extends BaseController {
    private $walletService;

    public function __construct() {
        parent::__construct();
        $this->walletService = new WalletService();
    }

   /**
     * 获取钱包信息
     * @author fealr
     */
   public function getWalletInfo () {
       $userId = I("userId", 0, "intval");
       $this->ajaxReturn($this->walletService->getWalletInfo($userId));
   }

    /**
     * 获取充值记录
     * @author fealr
     */
   public function getRechargeLog () {
       $userId = I("userId", 0, "intval");   //钱包id
       $status = I("status", "1,2,3", "trim");    //充值状态状态
       $offset = I('start', 1, 'intval'); //数据起始位置
       $pageSize = I('length', 10, 'intval'); //每页显示条数
       $offset = ($offset - 1) * $pageSize; //分页
       $this->ajaxReturn($this->walletService->getRechargeLog($userId, $status, $offset, $pageSize));
   }

    /**
     * 获取提现记录
     * @author fealr
     */
    public function getWithdrawLog () {
        $userId = I("userId", 0, "intval");
        $status = I("status", "1,2,3", "trim");
        $offset = I('start', 1, 'intval'); //数据起始位置
        $pageSize = I('length', 10, 'intval'); //每页显示条数
        $offset = ($offset - 1) * $pageSize; //分页
        $this->ajaxReturn($this->walletService->getWithdrawLog($userId, $status, $offset, $pageSize));
    }

    /**
     * 获取提现费率
     */
    public function getRate(){
        $this->ajaxReturn($this->walletService->getRate());
    }


    /**
     * 申请提现
     * @author wanfeng
     */
    public function withdraw() {
        $t1 = microtime(true);
        $userId = I("userId", 0, "intval");
        $walletId = I("walletId", 0, "intval");
        $this->loger('walletId',$walletId);
        $type=I('type','','strip_tags');//提现类型0：优贝 1：余额
        $money = I("money", '', "intval");
        $this->loger('money',$money);

        if($userId == 0){
            $this->ajaxReturn(fail_json("你还未登录~"));

        }
        if($walletId == 0){
            $this->ajaxReturn(fail_json("网络繁忙,请稍后再试~"));

        }
        if($money<=0){
            $this->ajaxReturn(fail_json("请输入正确金额~"));

        }
        $result =$this->walletService->withdraw($walletId, $userId, $money*100,$type);
        $this->timeLogger($t1, __ACTION__);
        $this->ajaxReturn($result);
        $this->ajaxReturn(fail_json('功能升级，暂不开放提现~，请等待！'));
    }
}