<?php
namespace server\mf_dcim\validate;

use think\Validate;

/**
 * @title 线路带宽验证
 * @use  server\mf_dcim\validate\LineBwValidate
 */
class LineBwValidate extends Validate
{
	protected $rule = [
        'id'                => 'require|integer',
        'type'              => 'require|in:radio,step,total',
        'value'             => 'require|checkBw:thinkphp',
        'min_value'         => 'require|integer|between:1,30000',
        'max_value'         => 'require|integer|between:1,30000|egt:min_value',
        'price'             => 'checkPrice:thinkphp',
        'other_config'      => 'checkOtherConfig:thinkphp',
        'in_bw'             => 'integer|between:1,30000',
        'value_show'        => 'max:255',
    ];

    protected $message = [
        'id.require'                    => 'id_error',
        'id.integer'                    => 'id_error',
        'type.require'                  => 'mf_dcim_please_select_line_bw_type',
        'type.in'                       => 'mf_dcim_line_bw_type_error',
        'value.require'                 => 'mf_dcim_please_input_bw',
        'min_value.require'             => 'mf_dcim_please_input_min_value',
        'min_value.integer'             => 'mf_dcim_line_bw_min_value_format_error',
        'min_value.between'             => 'mf_dcim_line_bw_min_value_format_error',
        'max_value.require'             => 'mf_dcim_please_input_max_value',
        'max_value.integer'             => 'mf_dcim_line_bw_max_value_format_error',
        'max_value.between'             => 'mf_dcim_line_bw_max_value_format_error',
        'max_value.egt'                 => 'mf_dcim_max_value_must_gt_min_value',
        'price.checkPrice'              => 'mf_dcim_price_cannot_lt_zero',
        'in_bw.integer'                 => 'mf_dcim_in_bw_format_error',
        'in_bw.between'                 => 'mf_dcim_in_bw_format_error',
        'value_show.max'                => 'mf_dcim_value_show_length_error',
    ];

    protected $scene = [
        'create'        => ['id','type','price','other_config','value_show'],
        'update'        => ['id','price','other_config','value_show'],
        'radio'         => ['value'],
        'step'          => ['min_value','max_value'],
        'other_config'  => ['in_bw'],
        'line_create'   => ['type','price','other_config','value_show'],
    ];

    public function checkPrice($value)
    {
        if(!is_array($value)){
            return false;
        }
        foreach($value as $v){
            if(!is_numeric($v) || $v<0 || $v>9999999){
                return 'mf_dcim_price_must_between_0_999999';
            }
        }
        return true;
    }

    public function checkOtherConfig($value)
    {
        $LineBwValidate = new LineBwValidate();
        if(!$LineBwValidate->scene('other_config')->check($value)){
            return $LineBwValidate->getError();
        }
        return true;
    }

    /**
     * 时间 2023-05-15
     * @title 验证带宽格式
     * @desc  验证带宽格式
     * @author hh
     * @version v1
     * @param   int|string $value - 带宽 require
     */
    public function checkBw($value)
    {
        if(is_numeric($value)){
            if(strpos($value, '.') !== false || $value<1 || $value > 30000){
                return 'mf_dcim_line_bw_format_error';
            }
        }else if($value == 'NC'){

        }else{
            return 'mf_dcim_line_bw_format_error';
        }
        return true;
    }


}