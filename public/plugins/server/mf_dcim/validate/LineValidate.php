<?php
namespace server\mf_dcim\validate;

use think\Validate;
use server\mf_dcim\model\OptionModel;

/**
 * @title 线路验证
 * @use  server\mf_dcim\validate\LineValidate
 */
class LineValidate extends Validate
{
	protected $rule = [
        'id'                    => 'require|integer',
        'data_center_id'        => 'require|integer',
        'name'                  => 'require|length:1,50',
        'bill_type'             => 'require|in:bw,flow',
        'bw_ip_group'           => 'integer',
        'defence_enable'        => 'require|in:0,1',
        'defence_ip_group'      => 'integer',
        'bw_data'               => 'requireIf:bill_type,bw|array|checkBwData:thinkphp',
        'flow_data'             => 'requireIf:bill_type,flow|array|checkFlowData:thinkphp',
        'defence_data'          => 'requireIf:defence_enable,1|array|checkDefenceData:thinkphp',
        'ip_data'               => 'require|array|checkIpData:thinkphp',
        'order'                 => 'require|number|between:0,999',
        'hidden'                => 'require|in:0,1',
        'sync_firewall_rule'    => 'require|in:0,1',
        'order_default_defence' => 'checkDefence:thinkphp',
    ];

    protected $message = [
        'id.require'                    => 'id_error',
        'id.integer'                    => 'id_error',
        'data_center_id.require'        => 'mf_dcim_please_select_data_center',
        'data_center_id.integer'        => 'mf_dcim_please_select_data_center',
        'name.require'                  => 'mf_dcim_please_input_line_name',
        'name.length'                   => 'mf_dcim_line_name_length_error',
        'bill_type.require'             => 'mf_dcim_please_select_line_bill_type',
        'bill_type.in'                  => 'mf_dcim_please_select_line_bill_type',
        'bw_ip_group.integer'           => 'mf_dcim_line_bw_ip_group_must_int',
        'defence_enable.require'        => 'mf_dcim_line_defence_enable_param_error',
        'defence_enable.in'             => 'mf_dcim_line_defence_enable_param_error',
        'defence_ip_group.integer'      => 'mf_dcim_line_defence_ip_group_must_int',
        'bw_data.requireIf'             => 'mf_dcim_please_add_at_lease_one_bw_data',
        'bw_data.array'                 => 'mf_dcim_please_add_at_lease_one_bw_data',
        'flow_data.requireIf'           => 'mf_dcim_please_add_at_lease_one_flow_data',
        'flow_data.array'               => 'mf_dcim_please_add_at_lease_one_flow_data',
        'defence_data.requireIf'        => 'mf_dcim_please_add_at_lease_one_defence_data',
        'defence_data.array'            => 'mf_dcim_please_add_at_lease_one_defence_data',
        'ip_data.require'               => 'mf_dcim_please_add_at_lease_one_ip_data',
        'ip_data.array'                 => 'mf_dcim_please_add_at_lease_one_ip_data',
        'order.require'                 => 'mf_dcim_order_require',
        'order.number'                  => 'mf_dcim_order_format_error',
        'order.between'                 => 'mf_dcim_order_format_error',
        'sync_firewall_rule.require'    => 'param_error',
        'sync_firewall_rule.in'         => 'param_error',
    ];

    protected $scene = [
        'create' => ['data_center_id','name','bill_type','bw_ip_group','defence_enable','defence_ip_group','bw_data','flow_data','defence_data','ip_data','order','sync_firewall_rule'],
        'update' => ['id','name','bw_ip_group','defence_enable','defence_ip_group','order','sync_firewall_rule','order_default_defence'],
        'update_hidden' => ['id','hidden'],
    ];

    // 验证带宽计费数据
    public function checkBwData($value, $t, $param){
        if($param['bill_type'] == 'flow'){
            return true;
        }
        $type = null;
        $LineBwValidate = new LineBwValidate();
        foreach($value as $k=>$v){
            if (!$LineBwValidate->scene('line_create')->check($v)){
                return $LineBwValidate->getError();
            }
            // 验证类型是否一致
            if(!isset($type)){
                $type = $v['type'];
            }else{
                if($type != $v['type']){
                    return 'mf_dcim_option_type_must_only_one_type';
                }
            }
            // 验证范围数字是否有交集
            if($type == 'radio'){
                if (!$LineBwValidate->scene('radio')->check($v)){
                    return $LineBwValidate->getError();
                }
            }else{
                if (!$LineBwValidate->scene('step')->check($v)){
                    return $LineBwValidate->getError();
                }
                foreach($value as $kk=>$vv){
                    if($k != $kk){
                        // 有交集
                        if(!($v['max_value']<$vv['min_value'] || $v['min_value']>$vv['max_value'])){
                            return 'mf_dcim_line_bw_range_intersect';
                        }
                    }
                }
            }
        }
        if($type == 'radio'){
            $optionValue = array_column($value, 'value');
            if( count($optionValue) != count( array_unique($optionValue) )){
                return 'mf_dcim_line_bw_already_exist';
            }
        }
        return true;
    }
    
    public function checkFlowData($value, $t, $param){
        if($param['bill_type'] == 'bw'){
            return true;
        }
        $LineFlowValidate = new LineFlowValidate();
        foreach($value as $k=>$v){
            if (!$LineFlowValidate->scene('line_create')->check($v)){
                return $LineFlowValidate->getError();
            }
        }
        $optionValue = array_column($value, 'value');
        if( count($optionValue) != count( array_unique($optionValue) )){
            return 'mf_dcim_line_flow_already_exist';
        }
        return true;
    }

    public function checkDefenceData($value, $type, $data){
        $firewallType = '';
        $LineDefenceValidate = new LineDefenceValidate();
        foreach($value as $k=>$v){
            if($data['sync_firewall_rule']==1){
                if (!$LineDefenceValidate->scene('sync_create')->check($v)){
                    return $LineDefenceValidate->getError();
                }
                if(empty($firewallType)){
                    $firewallType = $v['firewall_type'];
                }else if($firewallType != $v['firewall_type']){
                    return 'mf_dcim_sync_firewall_type_error';
                }
                $value[$k]['value'] = $v['firewall_type'] . '_' . $v['defence_rule_id'];
            }else{
                if (!$LineDefenceValidate->scene('line_create')->check($v)){
                    return $LineDefenceValidate->getError();
                }
            } 
        }
        $optionValue = array_column($value, 'value');
        if( count($optionValue) != count( array_unique($optionValue) )){
            return 'mf_dcim_line_defence_already_exist';
        }
        return true;
    }

    public function checkIpData($value){
        $type = null;
        $LineIpValidate = new LineIpValidate();
        foreach($value as $k=>$v){
            if (!$LineIpValidate->scene('line_create')->check($v)){
                return $LineIpValidate->getError();
            }
            // 验证类型是否一致
            if(!isset($type)){
                $type = $v['type'];
            }else{
                if($type != $v['type']){
                    return 'mf_dcim_option_type_must_only_one_type';
                }
            }
            // 验证范围数字是否有交集
            if($type == 'radio'){
                if (!$LineIpValidate->scene('radio')->check($v)){
                    return $LineIpValidate->getError();
                }
            }else{
                if (!$LineIpValidate->scene('step')->check($v)){
                    return $LineIpValidate->getError();
                }
                foreach($value as $kk=>$vv){
                    if($k != $kk){
                        // 有交集
                        if(!($v['max_value']<$vv['min_value'] || $v['min_value']>$vv['max_value'])){
                            return 'mf_dcim_line_ip_already_exist';
                        }
                    }
                }
            }
        }
        if($type == 'radio'){
            $optionValue = array_column($value, 'value');
            if( count($optionValue) != count( array_unique($optionValue) )){
                return 'mf_dcim_line_ip_already_exist';
            }
        }
        return true;
    }

    public function checkDefence($value, $t, $param)
    {
        if(!empty($value)){
            $option = OptionModel::where('rel_id', $param['id'])->where('rel_type', OptionModel::LINE_DEFENCE)->where('value', $value)->find();
            if(empty($option)){
                return 'mf_dcim_cabinet_order_default_defence_not_exist';
            }
        }
        
        return true;
    }






}