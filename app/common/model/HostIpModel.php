<?php
namespace app\common\model;

use think\Model;

/**
 * @title 产品IP模型
 * @desc 产品IP模型
 * @use app\common\model\HostIpModel
 */
class HostIpModel extends Model
{
	protected $name = 'host_ip';

    // 设置字段信息
    protected $schema = [
        'id'                    => 'int',
        'host_id'               => 'int',
        'dedicate_ip'           => 'string',
        'assign_ip'             => 'string',
        'ip_num'                => 'int',
        'create_time'           => 'int',
        'update_time'           => 'int',
    ];

    /**
     * 时间 2024-01-10
     * @title 保存产品IP
     * @desc  保存产品IP
     * @author hh
     * @version v1
     * @param   int param.host_id - 产品ID require
     * @param   string param.dedicate_ip - 主IP require
     * @param   string param.assign_ip - 附加IP(英文逗号分隔) require
     * @param   int param.ip_num - IP数量
     * @param   bool param.write_log true 是否添加日志
     * @return  bool
     */
    public function hostIpSave(array $param)
    {
        if(empty($param['host_id'])){
            return false;
        }
        $param['write_log'] = $param['write_log'] ?? true;

        $ipNum = 0;
        if(!empty($param['dedicate_ip'])){
            $ipNum += 1;
        }
        $param['assign_ip'] = trim($param['assign_ip']);
        if(!empty($param['assign_ip'])){
            $assignIp = explode(',', $param['assign_ip']);
            $assignIp = array_filter($assignIp, function($value) use ($param) {
                return !empty($value) && $value != $param['dedicate_ip'];
            });
            $ipNum += count($assignIp);
            $param['assign_ip'] = implode(',', $assignIp);
        }
        if(isset($param['ip_num']) && !empty($param['ip_num'])){
            $ipNum  = intval($param['ip_num']);
        }
        $exist = $this->where('host_id', $param['host_id'])->find();
        if(!empty($exist)){
            $this->where('host_id', $param['host_id'])->update([
                'dedicate_ip'   => $param['dedicate_ip'],
                'assign_ip'     => $param['assign_ip'],
                'ip_num'        => $ipNum,
                'update_time'   => time(),
            ]);
        }else{
            $exist = [
                'dedicate_ip'   => '',
                'assign_ip'     => '',
            ];

            $this->create([
                'host_id'       => $param['host_id'],
                'dedicate_ip'   => $param['dedicate_ip'],
                'assign_ip'     => $param['assign_ip'],
                'ip_num'        => $ipNum,
                'create_time'   => time(),
            ]);
        }

        if($param['write_log']){
            $detail = [];
            if($exist['dedicate_ip'] != $param['dedicate_ip']){
                $detail[] = lang('log_admin_update_description', [
                    '{field}'       => lang('host_ip_dedicate_ip'),
                    '{old}'         => $exist['dedicate_ip'],
                    '{new}'         => $param['dedicate_ip'],
                ]);
            }
            if($exist['assign_ip'] != $param['assign_ip']){
                $detail[] = lang('log_admin_update_description', [
                    '{field}'       => lang('host_ip_assign_ip'),
                    '{old}'         => $exist['assign_ip'],
                    '{new}'         => $param['assign_ip'],
                ]);
            }
            if(!empty($detail)){
                $hostName = HostModel::where('id', $param['host_id'])->value('name');

                $description = lang('log_host_ip_update_success', [
                    '{host}'    => 'host#'.$param['host_id'].'#'.$hostName.'#',
                    '{detail}'  => implode(',', $detail),
                ]);
                active_log($description, 'host', $param['host_id']);
            }
        }

        hook('after_host_ip_create',['host_id'=>$param['host_id']]);

        return true;
    }

    /**
     * 时间 2024-01-10
     * @title 获取产品IP
     * @desc  获取产品IP
     * @author hh
     * @version v1
     * @param   int param.host_id - 产品ID require
     * @return  string dedicate_ip - 主IP
     * @return  string assign_ip - 附加IP(英文逗号分隔)
     * @return  int ip_num - IP数量
     */
    public function getHostIp(array $param) 
    {
        // 前台
        // 20240522 wyh 改，留了个大坑，多级代理根本推送不下去ip
        $clientId = $param['client_id']??get_client_id();
        if(app('http')->getName() == 'home'){
            $host = HostModel::find($param['host_id']);
            if(empty($host) || $host['is_delete'] || $host['client_id'] != $clientId){
                // 前台无权限或者实例不存在
                $hostIp = [
                    'dedicate_ip'   => '',
                    'assign_ip'     => '',
                    'ip_num'        => 0,
                ];
                return $hostIp;
            }
        }

        $hostIp = $this
                ->field('dedicate_ip,assign_ip,ip_num')
                ->where('host_id', $param['host_id'])
                ->find();
        if(empty($hostIp)){
            $hostIp = [
                'dedicate_ip'   => '',
                'assign_ip'     => '',
                'ip_num'        => 0,
            ];
        }else{
            $hostIp = $hostIp->toArray();
        }
        return $hostIp;
    }











}