<?php
namespace app\common\validate;

use think\Validate;

/**
 * @title 自定义字段验证类
 * @description 接口说明:自定义字段验证类
 */
class SelfDefinedFieldValidate extends Validate
{
    protected $rule = [
        'id'                        => 'require|integer',
        'type'                      => 'require|in:product,product_group',
        'relid'                     => 'requireIf:type,product|integer',
        'field_name'                => 'require|length:1,255',
        'is_required'               => 'require|in:0,1',
        'field_type'                => 'require|in:text,link,password,dropdown,tickbox,textarea,explain',
        'description'               => 'length:0,255',
        'regexpr'                   => 'length:0,255|checkRegexpr:thinkphp',
        'field_option'              => 'length:0,2000|checkFieldOption:thinkphp',
        'show_order_page'           => 'require|in:0,1',
        'show_order_detail'         => 'require|in:0,1',
        'show_client_host_detail'   => 'require|in:0,1',
        'show_admin_host_detail'    => 'require|in:0,1',
        'show_client_host_list'     => 'require|in:0,1',
        'show_admin_host_list'      => 'require|in:0,1',
        'prev_id'                   => 'require|integer',
        'explain_content'           => 'max:65535',
        'product_group_id'          => 'require|array',
    ];

    protected $message = [
        'id.require'                        => 'id_error',
        'id.integer'                        => 'id_error',
        'type.require'                      => 'param_error',
        'type.in'                           => 'param_error',
        'relid.requireIf'                   => 'product_id_error',
        'relid.integer'                     => 'product_id_error',
        'field_name.require'                => 'self_defined_field_field_name_require',
        'field_name.length'                 => 'self_defined_field_field_name_format_error',
        'is_required.require'               => 'param_error',
        'is_required.in'                    => 'param_error',
        'field_type.require'                => 'self_defined_field_field_type_require',
        'field_type.in'                     => 'self_defined_field_field_type_param_error',
        'description.length'                => 'self_defined_field_description_length_error',
        'regexpr.length'                    => 'self_defined_field_regexpr_length_error',
        'field_option.length'               => 'self_defined_field_field_option_length_error',
        'show_order_page.require'           => 'param_error',
        'show_order_page.in'                => 'param_error',
        'show_order_detail.require'         => 'param_error',
        'show_order_detail.in'              => 'param_error',
        'show_client_host_detail.require'   => 'param_error',
        'show_client_host_detail.in'        => 'param_error',
        'show_admin_host_detail.require'    => 'param_error',
        'show_admin_host_detail.in'         => 'param_error',
        'show_client_host_list.require'     => 'param_error',
        'show_client_host_list.in'          => 'param_error',
        'show_admin_host_list.require'      => 'param_error',
        'show_admin_host_list.in'           => 'param_error',
        'prev_id.require'                   => 'param_error',
        // 'explain_content.require'           => '请输入说明内容',
        'explain_content.max'               => 'self_defined_field_explain_content_max',
        'product_group_id.require'          => 'param_error',
        'product_group_id.array'            => 'param_error',
    ];

    protected $scene = [
        'create'    => ['type','relid','field_name','is_required','field_type','description','regexpr','field_option','show_order_page','show_order_detail','show_client_host_detail','show_admin_host_detail','show_client_host_list','show_admin_host_list'],
        'update'    => ['id','field_name','is_required','field_type','description','regexpr','field_option','show_order_page','show_order_detail','show_client_host_detail','show_admin_host_detail','show_client_host_list','show_admin_host_list'],
        'drag'      => ['id','prev_id'],
        'explain_create' => ['type','relid','field_name','field_type','explain_content'],
        'explain_update' => ['id','field_name','field_type','explain_content'],
        'related' => ['id', 'product_group_id'],
    ];

    /**
     * 时间 2024-02-20
     * @title 验证field_option
     * @desc 验证field_option,仅当field_type=dropdown时验证必须
     * @author hh
     * @version v1
     * @param   string value - 下拉选项 require
     * @param   array $data - 所有参数 require
     * @return  bool|string
     */
    protected function checkFieldOption($value, $type, $data)
    {
        if($data['field_type'] == 'dropdown'){
            if(!isset($value) || $value === ''){
                return 'self_defined_field_field_option_require';
            }
        }
        return true;
    }

    /**
     * 时间 2024-02-20
     * @title 验证正则表达式格式
     * @desc  验证正则表达式格式,如/[0-9]/
     * @author hh
     * @version v1
     * @param   string value - 正则表达式 require
     * @return  bool|string
     */
    protected function checkRegexpr($value)
    {
        if($value !== ''){
            try{
                $match = preg_match("{$value}", '');
                if($match === false){
                    return 'self_defined_field_regexpr_format_error';
                }
            }catch(\Exception $e){
                return 'self_defined_field_regexpr_format_error';
            }
        }
        return true;
    }



}