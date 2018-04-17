<?php
/**
 * Created by PhpStorm.
 * User: Freeman
 * Date: 2017/8/15
 * Time: 9:03
 */

namespace Home\Controller;


use Home\Service\WalletService;

class BonusController extends BaseController {
    private $walletService;

    public function __construct() {
        parent::__construct();
        $this->walletService = new WalletService();
    }

    /**
     * 获取充值记录
     * @author fealr
     */
   public function getBonusLog () {
       $userId = I("userId", 0, "intval");   //用户ID
       $offset = I('start', 1, 'intval'); //数据起始位置
       $pageSize = I('length', 10, 'intval'); //每页显示条数
       $offset = ($offset - 1) * $pageSize; //分页
       $this->ajaxReturn($this->walletService->getBonusLog($userId,  $offset, $pageSize));
   }
}