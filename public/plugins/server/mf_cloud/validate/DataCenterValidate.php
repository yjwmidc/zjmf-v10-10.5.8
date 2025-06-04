<?php
namespace server\mf_cloud\validate;

use think\Validate;

/**
 * @title 数据中心验证
 * @use server\mf_cloud\validate
 */
class DataCenterValidate extends Validate
{
	protected $rule = [
        'id'                => 'require|integer',
		'country_id' 		=> 'require|integer',
        'product_id'        => 'require|integer',
        'city'              => 'require|length:1,255',
        'area'              => 'require|length:1,255',
        'cloud_config'      => 'require|in:node,area,node_group',
        'cloud_config_id'   => 'require|integer',
        'order'             => 'integer|between:0,999',
        'gpu_name'          => 'require|length:1,100',
    ];

    protected $message = [
    	'id.require'     		    => 'id_error',
    	'id.integer'     		    => 'id_error',
        'country_id.require'        => 'country_select_error',
        'country_id.integer'        => 'country_select_error',
        'product_id.require'        => 'product_id_error',
        'product_id.integer'        => 'product_id_error',
        'city.require'              => 'please_input_city',
        'city.length'               => 'city_format_error',
        'area.require'              => 'please_input_area',
        'area.length'               => 'mf_cloud_area_format_error',
        'cloud_config.require'      => 'param_error',
        'cloud_config.in'           => 'param_error',
        'cloud_config_id.require'   => 'cloud_config_id_cannot_be_empty',
        'cloud_config_id.integer'   => 'cloud_config_id_cannot_be_empty',
        'order.integer'             => 'mf_cloud_order_format_error',
        'order.between'             => 'mf_cloud_order_format_error',
        'gpu_name.require'          => 'mf_cloud_line_gpu_name_require',
        'gpu_name.length'           => 'mf_cloud_line_gpu_name_length_error',
    ];

    protected $scene = [
        'create'            => ['country_id','product_id','city','area','cloud_config','cloud_config_id','order'],
        'update'            => ['id','country_id','city','area','cloud_config','cloud_config_id','order'],
        'update_gpu_name'   => ['id','gpu_name'],
    ];

    public function sceneOrder(){
        return $this->only(['id','order'])
                    ->append('order', 'require');
    }

}