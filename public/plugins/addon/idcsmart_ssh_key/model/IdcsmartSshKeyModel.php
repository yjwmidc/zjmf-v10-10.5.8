<?php
namespace addon\idcsmart_ssh_key\model;

use think\db\Query;
use think\facade\Cache;
use think\Model;
use app\common\model\ClientModel;
use addon\idcsmart_ssh_key\IdcsmartSshKey;

/**
 * @title SSH秘钥模型
 * @desc SSH秘钥模型
 * @use addon\idcsmart_ssh_key\model\IdcsmartSshKeyModel
 */
class IdcsmartSshKeyModel extends Model
{
    protected $name = 'addon_idcsmart_ssh_key';

    // 设置字段信息
    protected $schema = [
        'id'      		=> 'int',
        'client_id'     => 'int',
        'name'     	    => 'string',
        'public_key'    => 'string',
        'finger_print'  => 'string',
        'create_time'   => 'int',
        'update_time'   => 'int',
    ];

    /**
     * 时间 2022-07-07
     * @title SSH密钥列表
     * @desc SSH密钥列表
     * @author theworld
     * @version v1
     * @param int param.page - 页数
     * @param int param.limit - 每页条数
     * @param string param.orderby - 排序 id
     * @param string param.sort - 升/降序 asc,desc
     * @param string app - 前后台home前台admin后台
     * @return array list - SSH密钥
     * @return int list[].id - SSH密钥ID
     * @return string list[].name - 名称
     * @return string list[].public_key - 公钥
     * @return string list[].finger_print - 指纹
     * @return string list[].client - 用户,仅后台返回
     * @return int count - SSH密钥总数
     */
    public function idcsmartSshKeyList($param, $app = '')
    {
        if($app == 'home'){
            $param['client_id'] = get_client_id();
        }else{
            $param['client_id'] = $param['client_id'] ?? 0;
        }
        $param['orderby'] = isset($param['orderby']) && in_array($param['orderby'], ['id']) ? 'aisk.'.$param['orderby'] : 'aisk.id';

        $where = function (Query $query) use($param, $app) {
            if($app=='home'){
                $query->where('aisk.client_id', $param['client_id']);
            }else{
                if(!empty($param['client_id'])){
                    $query->where('aisk.client_id', $param['client_id']);
                }
            }
        };
        
    	$count = $this->alias('aisk')
            ->field('aisk.id')
            ->where($where)
            ->count();
        $list = $this->alias('aisk')
            ->field('aisk.id,aisk.name,aisk.public_key,aisk.finger_print,c.username client')
            ->leftJoin('client c', 'c.id=aisk.client_id')
            ->where($where)
            ->limit($param['limit'])
            ->page($param['page'])
            ->order($param['orderby'], $param['sort'])
            ->select()
            ->toArray();
        foreach ($list as $key => $value) {
            if($app=='home'){
                unset($list[$key]['client']);
            }
        }
        return ['list' => $list, 'count' => $count];
    }

    /**
     * 时间 2022-07-07
     * @title 创建SSH密钥
     * @desc 创建SSH密钥
     * @author theworld
     * @version v1
     * @param string param.name - 名称 required
     * @param string param.public_key - 公钥 required
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function createIdcsmartSshKey($param)
    {
        $clientId = get_client_id();
        // 验证用户ID
        $client = ClientModel::find($clientId);

        if (empty($client)){
            return ['status'=>400, 'msg'=>lang_plugins('fail_message')];
        }

        $count = $this->where('client_id', $clientId)->count();
        if($count>=20){
            return ['status'=>400, 'msg'=>lang_plugins('addon_idcsmart_ssh_key_create_max')];
        }

        $this->startTrans();
        try {
            $idcsmartSshKey = $this->create([
                'client_id' => $clientId,
                'name' => $param['name'] ?? '',
                'public_key' => $param['public_key'] ?? '',
                'finger_print' => getPublicKeyFingerprint($param['public_key']),
                'create_time' => time()
            ]);

            # 记录日志
            active_log(lang_plugins('log_client_add_ssh_key', ['{client}'=>'client#'.$client['id'].'#'.$client['username'].'#','{name}'=>$param['name']]), 'addon_idcsmart_ssh_key', $idcsmartSshKey->id);

            $this->commit();
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return ['status' => 400, 'msg' => lang_plugins('create_fail')];
        }
        return ['status' => 200, 'msg' => lang_plugins('create_success')];
    }

    /**
     * 时间 2022-07-07
     * @title 编辑SSH密钥
     * @desc 编辑SSH密钥
     * @author theworld
     * @version v1
     * @param int param.id - SSH密钥ID required
     * @param string param.name - 名称 required
     * @param string param.public_key - 公钥 required
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function updateIdcsmartSshKey($param)
    {
        $clientId = get_client_id();
        // 验证用户ID
        $client = ClientModel::find($clientId);

        if (empty($client)){
            return ['status'=>400, 'msg'=>lang_plugins('fail_message')];
        }

        // 验证SSH密钥ID
        $idcsmartSshKey = $this->find($param['id']);
        if(empty($idcsmartSshKey)){
            return ['status'=>400, 'msg'=>lang_plugins('addon_idcsmart_ssh_key_is_not_exist')];
        }

        if($idcsmartSshKey['client_id']!=$clientId){
            return ['status'=>400, 'msg'=>lang_plugins('addon_idcsmart_ssh_key_is_not_exist')];
        }


        $this->startTrans();
        try {
            $this->update([
                'name' => $param['name'] ?? '',
                'public_key' => $param['public_key'] ?? '',
                'finger_print' => getPublicKeyFingerprint($param['public_key']),
                'update_time' => time()
            ], ['id' => $param['id']]);

            # 记录日志
            active_log(lang_plugins('log_client_edit_ssh_key', ['{client}'=>'client#'.$client['id'].'#'.$client['username'].'#','{name}'=>$param['name']]), 'addon_idcsmart_ssh_key', $idcsmartSshKey->id);

            $this->commit();
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return ['status' => 400, 'msg' => lang_plugins('update_fail')];
        }
        return ['status' => 200, 'msg' => lang_plugins('update_success')];
    }

    /**
     * 时间 2022-07-07
     * @title 删除SSH密钥
     * @desc 删除SSH密钥
     * @author theworld
     * @version v1
     * @param int id - SSH密钥ID required
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function deleteIdcsmartSshKey($id)
    {
        $clientId = get_client_id();
        // 验证用户ID
        $client = ClientModel::find($clientId);

        if (empty($client)){
            return ['status'=>400, 'msg'=>lang_plugins('fail_message')];
        }

        // 验证SSH密钥ID
        $idcsmartSshKey = $this->find($id);
        if(empty($idcsmartSshKey)){
            return ['status'=>400, 'msg'=>lang_plugins('addon_idcsmart_ssh_key_is_not_exist')];
        }

        if($idcsmartSshKey['client_id']!=$clientId){
            return ['status'=>400, 'msg'=>lang_plugins('addon_idcsmart_ssh_key_is_not_exist')];
        }

        $this->startTrans();
        try {
            # 记录日志
            active_log(lang_plugins('log_client_delete_ssh_key', ['{client}'=>'client#'.$client['id'].'#'.$client['username'].'#','{name}'=>$idcsmartSshKey['name']]), 'addon_idcsmart_ssh_key', $idcsmartSshKey->id);
            
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