<?php
/**
 * Created by PhpStorm.
 * User: Freeman
 * Date: 2017/8/10
 * Time: 11:44
 */

namespace Home\Dao;


use Enum\RoleEnum;
use Enum\SysEnum;
use Enum\UserEnum;
use Enum\WalletEnum;

class SysDao extends BaseDao {
    public function _initialize ($db) {
        parent::_initialize($db);
    }

    public function getBanner(){
        $sql = "SELECT * from sys_banner WHERE status = %d order by weight desc";
        return $this->db->query($sql,array(1));
    }

    public function getSysConfig ($code) {
        $sql = "SELECT * FROM sys_config WHERE code = %s";
        return $this->db->query($sql, $code);
    }

    public function getSysConfigByPid ($pid) {
        $sql = "SELECT * FROM sys_config WHERE pId = %d";
        $data = $this->db->query($sql, $pid);
        $arr = array();
        foreach ($data AS $key => $value) {
            $arr[$value['code']] = $value['value'];
        }
        return $arr;
    }
    /**
     * 先查询共享充值者的用户信息
     */
    public function getCardInfo($roleId){
        $sql ="SELECT sba.account,au.id AS userId, sba.type
               FROM sys_bank_account sba
               LEFT JOIN ad_auth_user_role_rel arr ON sba.userId = arr.userId
               LEFT JOIN ad_auth_role ar ON ar.id = arr.roleId
               LEFT JOIN ad_user au ON au.id = arr.userId
               WHERE sba.status=".RoleEnum::STATUS_ON."  AND arr.roleId =%d";
        $data = $this->db->query($sql,$roleId);
        return $data;
}
    /**
     * 金融专员信息
     */
    public function assistant($roleId,$bank, $pay){
        $sql = "SELECT au.id, au.phone, au.username , au.level,sa.account FROM ad_user au 
                LEFT JOIN ad_auth_user_role_rel al ON au.id = al.userId
                LEFT JOIN ad_auth_role ar ON ar.id = al.roleId
                LEFT JOIN sys_bank_account sa ON sa.userId = au.id
                WHERE ar.id = %d AND au.status = %d AND sa.type IN (%d,%d) AND sa.status = %d GROUP BY au.id ";
        $data = $this->db->query($sql, array($roleId,RoleEnum::STATUS_ON, $bank, $pay, RoleEnum::STATUS_ON));
        return $data;
    }

    /**
     * 由ID获取入金员信息
     */
    public function assistantInfo($assistantId){
        $sql = "SELECT * FROM ad_user WHERE id = %d";
        return $this->db->query($sql,$assistantId);
    }

    /**
     * 获取充值渠道信息
     */
    public function rechargeChannel($userId){
        $sql = "SELECT au.username, au.phone, sa.* FROM sys_bank_account sa LEFT JOIN ad_user au ON au.id = sa.userId
                WHERE sa.status=".UserEnum::USER_SHARE_ON." AND au.id = %d ORDER BY sa.type DESC ";
        return $this->db->query($sql, $userId);
    }


    /**
     * 获取公告信息
     */
    public function headlines($offset, $pageSize){
        $sql = "SELECT id, title, content, type,createTime FROM sys_headlines WHERE status = %d ORDER BY weight DESC LIMIT %d,%d";
        $data = $this->db->query($sql, array(SysEnum::STATUS_HEADLINE_ON,$offset, $pageSize));
        return $data;
    }
    public function headlinesCount(){
        $sql = "SELECT COUNT(*) AS total FROM sys_headlines WHERE status = %d";
        $data = $this->db->query($sql, SysEnum::STATUS_HEADLINE_ON);
        return $data[0]['total'];
    }

    /**
     * 获取已置顶的公告
     * @return mixed
     */
    public function HeadlinesPopUp() {
        $sql = "SELECT * FROM sys_headlines WHERE type = %d";
        $data = $this->db->query($sql, array(SysEnum::TYPE_POPUP));
        return $data;
    }

    /**
     * 查看公告
     * @param $id
     * @return mixed
     */
    public function checkHeadlines($id){
       $sql = "SELECT * FROM sys_headlines WHERE status = %d AND id = %d";
        $data = $this->db->query($sql, array(SysEnum::STATUS_HEADLINE_ON, $id));
        return $data;
    }

}