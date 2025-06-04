<?php
namespace server\mf_cloud\validate;

use think\Validate;

/**
 * @title 磁盘配置验证
 * @use  server\mf_cloud\validate\DiskValidate
 */
class DiskValidate extends Validate
{
	protected $rule = [
        'id'                => 'require|integer',
        'product_id'        => 'require|integer',
        'type'              => 'require|in:radio,step,total',
        'value'             => 'require|integer|between:1,1048576',
        'min_value'         => 'require|integer|between:1,1048576',
        'max_value'         => 'require|integer|between:1,1048576|egt:min_value',
        'price'             => 'checkPrice:thinkphp',
        'other_config'      => 'checkOtherConfig:thinkphp',
        'disk_type'         => 'length:0,50',
        'store_id'          => 'integer|between:0,99999999',
    ];

    protected $message = [
        'id.require'                    => 'id_error',
        'id.integer'                    => 'id_error',
        'product_id.require'            => 'product_id_error',
        'product_id.integer'            => 'product_id_error',
        'type.require'                  => 'please_select_config_type',
        'type.in'                       => 'config_type_error',
        'value.require'                 => 'please_input_disk_size',
        'value.integer'                 => 'disk_size_format_error',
        'value.between'                 => 'disk_size_format_error',
        'min_value.require'             => 'please_input_disk_min_value',
        'min_value.integer'             => 'disk_min_value_format_error',
        'min_value.between'             => 'disk_min_value_format_error',
        'max_value.require'             => 'please_input_disk_max_value',
        'max_value.integer'             => 'disk_max_value_format_error',
        'max_value.between'             => 'disk_max_value_format_error',
        'max_value.egt'                 => 'line_bw_max_value_must_gt_min_value',
        'price.checkPrice'              => 'price_cannot_lt_zero',
        'disk_type.length'              => 'disk_type_format_error',
        'store_id.integer'              => 'mf_cloud_store_id_format_error',
        'store_id.between'              => 'mf_cloud_store_id_format_error',
    ];

    protected $scene = [
        'create'        => ['product_id','type','price','other_config'],
        'update'        => ['id','price','other_config'],
        'radio'         => ['value'],
        'step'          => ['min_value','max_value'],
        'other_config'  => ['disk_type','store_id'],
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

    public function checkOtherConfig($value){
        $DiskValidate = new DiskValidate();
        if(!$DiskValidate->scene('other_config')->check($value)){
            return $DiskValidate->getError();
        }
        return true;
    }

}