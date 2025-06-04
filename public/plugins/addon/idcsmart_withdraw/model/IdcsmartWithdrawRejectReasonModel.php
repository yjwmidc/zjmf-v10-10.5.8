<?php
namespace addon\idcsmart_withdraw\model;

use think\db\Query;
use think\facade\Cache;
use think\Model;
use app\admin\model\PluginModel;

/**
 * @title 提现驳回原因模型
 * @desc 提现驳回原因模型
 * @use addon\idcsmart_withdraw\model\IdcsmartWithdrawRejectReasonModel
 */
class IdcsmartWithdrawRejectReasonModel extends Model
{
    protected $name = 'addon_idcsmart_withdraw_reject_reason';

    // 设置字段信息
    protected $schema = [
        'id'      		    => 'int',
        'reason'            => 'string',
        'admin_id'       	=> 'int',
        'create_time'       => 'int',
        'update_time'       => 'int',
    ];

    /**
     * 时间 2022-10-25
     * @title 驳回原因列表
     * @desc 驳回原因列表
     * @author theworld
     * @version v1
     * @return array list - 驳回原因
     * @return int list[].id - 驳回原因ID
     * @return string list[].reason - 驳回原因内容
     * @return string list[].admin_id - 管理员ID
     * @return string list[].admin - 管理员
     * @return string list[].create_time - 添加时间
     * @return int count - 驳回原因总数
     */
    public function idcsmartWithdrawRejectReasonList()
    {
    	$count = $this->alias('aiwrr')
            ->field('aiwrr.id')
            ->count();
        $list = $this->alias('aiwrr')
            ->field('aiwrr.id,aiwrr.reason,aiwrr.admin_id,a.name admin,aiwrr.create_time')
            ->leftJoin('admin a', 'a.id=aiwrr.admin_id')
            ->select()
            ->toArray();

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 时间 2022-10-25
     * @title 添加驳回原因
     * @desc 添加驳回原因
     * @author theworld
     * @version v1
     * @param string param.reason - 驳回原因 required
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function createIdcsmartWithdrawRejectReason($param)
    {
        $adminId = get_admin_id();

        $param['reason'] = $param['reason'] ?? '';
        if(empty($param['reason']) || !is_string($param['reason'])){
            return ['status' => 400, 'msg' => lang_plugins('addon_idcsmart_withdraw_reject_reason_require')];
        }
        if(strlen($param['reason'])>1000){
            return ['status' => 400, 'msg' => lang_plugins('addon_idcsmart_withdraw_reject_reason_max')];
        }

        $this->startTrans();
        try {
            $reason = $this->create([
                'reason' => $param['reason'] ?? '',
                'admin_id' => $adminId,
                'create_time' => time()
            ]);

            # 记录日志
            

            $this->commit();
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return ['status' => 400, 'msg' => lang_plugins('create_fail')];
        }
        return ['status' => 200, 'msg' => lang_plugins('create_success')];
    }

    /**
     * 时间 2022-10-25
     * @title 修改驳回原因
     * @desc 修改驳回原因
     * @author theworld
     * @version v1
     * @param int param.id - 驳回原因ID required
     * @param string param.reason - 驳回原因 required
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function updateIdcsmartWithdrawRejectReason($param)
    {
        // 验证驳回原因ID
        $reason = $this->find($param['id']);
        if(empty($reason)){
            return ['status'=>400, 'msg'=>lang_plugins('addon_idcsmart_withdraw_reject_reason_is_not_exist')];
        }

        $param['reason'] = $param['reason'] ?? '';
        if(empty($param['reason']) || !is_string($param['reason'])){
            return ['status' => 400, 'msg' => lang_plugins('addon_idcsmart_withdraw_reject_reason_require')];
        }
        if(strlen($param['reason'])>1000){
            return ['status' => 400, 'msg' => lang_plugins('addon_idcsmart_withdraw_reject_reason_max')];
        }

        $this->startTrans();
        try {
            $this->update([
                'reason' => $param['reason'] ?? '',
                'update_time' => time()
            ], ['id' => $param['id']]);

            # 记录日志
            

            $this->commit();
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return ['status' => 400, 'msg' => lang_plugins('update_fail')];
        }
        return ['status' => 200, 'msg' => lang_plugins('update_success')];
    }

    /**
     * 时间 2022-10-25
     * @title 删除驳回原因
     * @desc 删除驳回原因
     * @author theworld
     * @version v1
     * @param int id - 驳回原因ID required
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function deleteIdcsmartWithdrawRejectReason($id)
    {
    	// 验证驳回原因ID
        $reason = $this->find($id);
        if(empty($reason)){
            return ['status'=>400, 'msg'=>lang_plugins('addon_idcsmart_withdraw_reject_reason_is_not_exist')];
        }

        $this->startTrans();
        try {
            # 记录日志
            
            $this->destroy($id);
            $this->commit();
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return ['status' => 400, 'msg' => lang_plugins('delete_fail')];
        }
        return ['status' => 200, 'msg' => lang_plugins('delete_success')];
    }
    
}