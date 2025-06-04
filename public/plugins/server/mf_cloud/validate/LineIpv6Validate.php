<?php
namespace server\mf_cloud\validate;

use think\Validate;

/**
 * @title 线路IPv6验证
 * @use  server\mf_cloud\validate\LineIpValidate
 */
class LineIpv6Validate extends Validate
{
	protected $rule = [
        'id'                => 'require|integer',
        'type'              => 'require|in:radio,step,total',
        'value'             => 'require|integer|between:0,1000',
        'min_value'         => 'require|integer|between:0,1000',
        'max_value'         => 'require|integer|between:0,1000|egt:min_value',
        'price'             => 'checkPrice:thinkphp',
    ];

    protected $message = [
        'id.require'                    => 'id_error',
        'id.integer'                    => 'id_error',
        'type.require'                  => 'please_select_line_bw_type',
        'type.in'                       => 'line_bw_type_error',
        'value.require'                 => 'mf_cloud_ipv6_num_require',
        'value.integer'                 => 'mf_cloud_ipv6_num_format_error',
        'value.between'                 => 'mf_cloud_ipv6_num_format_error',
        'min_value.require'             => 'please_input_line_bw_min_value',
        'min_value.integer'             => 'mf_cloud_ipv6_num_format_error',
        'min_value.between'             => 'mf_cloud_ipv6_num_format_error',
        'max_value.require'             => 'please_input_line_bw_max_value',
        'max_value.integer'             => 'mf_cloud_ipv6_num_format_error',
        'max_value.between'             => 'mf_cloud_ipv6_num_format_error',
        'max_value.egt'                 => 'line_bw_max_value_must_gt_min_value',
        'price.checkPrice'              => 'price_cannot_lt_zero',
    ];

    protected $scene = [
        'create'        => ['id','type','price'],
        'update'        => ['id','price'],
        'radio'         => ['value'],
        'step'          => ['min_value','max_value'],
        'line_create'   => ['type','price'],
    ];

    public function checkPrice($value){
        if(!is_array($value)){
            return false;
        }
        foreach($value as $v){
            if(!is_numeric($v) || $v<0 || $v>9999999){
                return 'price_must_between_0_999999';
            }
        }
        return true;
    }


}