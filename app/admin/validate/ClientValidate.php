<?php
namespace app\admin\validate;

use think\Validate;

/**
 * 用户管理验证
 */
class ClientValidate extends Validate
{
    protected $regex = ['password' => '/^[^\x{4e00}-\x{9fa5}\x{9fa6}-\x{9fef}\x{3400}-\x{4db5}\x{20000}-\x{2ebe0}\s]{6,32}$/u'];

	protected $rule = [
		'id' 			      => 'require|integer|gt:0',
        'username' 		      => 'max:20',
        'email' 		      => 'requireWithout:phone|email|unique:client',
        'phone_code' 	      => 'requireWithout:email',
        'phone' 		      => 'requireWithout:email|max:11|number|unique:client,phone_code^phone',
        'password' 		      => 'require|regex:password',
        'repassword'	      => 'require|confirm:password',
        'company'             => 'max:255',
        'country_id'          => 'integer|egt:0',
        'address'             => 'max:255',
        'notes'               => 'max:1000',
        'status'              => 'require|in:0,1',
        'operate_password'    => 'regex:password',
        'receive_sms'         => 'require|in:0,1',
        'receive_email'       => 'require|in:0,1',
    ];

    protected $message  =   [
    	'id.require'     			=> 'id_error',
    	'id.integer'     			=> 'id_error',
        'id.gt'                     => 'id_error',
        'username.max'     		    => 'client_name_cannot_exceed_20_chars',
        'email.requireWithout' 		=> 'please_enter_vaild_email',
        'email.email'        		=> 'please_enter_vaild_email', 
        'email.unique'              => 'email_has_been_registered',   
        'phone_code.requireWithout' => 'please_select_phone_code', 
        'phone.requireWithout'      => 'please_enter_vaild_phone', 
        'phone.max'        		    => 'please_enter_vaild_phone', 
        'phone.number'              => 'please_enter_vaild_phone',
        'phone.unique'              => 'phone_has_been_registered',  
        'password.require'   		=> 'please_enter_password', 
        'password.regex'            => 'password_formatted_incorrectly', 
        'repassword.require'        => 'please_enter_password_again', 
        'repassword.confirm'        => 'passwords_not_match',
        'company.max'               => 'company_cannot_exceed_255_chars',
        'country_id.integer'        => 'id_error',
        'country_id.egt'            => 'id_error',
        'address.max'               => 'address_cannot_exceed_255_chars',
        'notes.max'                 => 'notes_cannot_exceed_1000_chars',
        'status.require'            => 'param_error',
        'status.in'                 => 'param_error',
        'operate_password.regex'    => 'password_formatted_incorrectly',
        'receive_sms.require'       => 'param_error',
        'receive_sms.in'            => 'param_error',
        'receive_email.require'     => 'param_error',
        'receive_email.in'          => 'param_error',
    ];

    protected $scene = [
        'create' => ['username', 'email', 'phone_code', 'phone', 'password', 'repassword'],
        'status' => ['id', 'status'],
        'receive_sms' => ['id', 'receive_sms'],
        'receive_email' => ['id', 'receive_email'],
    ];

    # 修改验证
    public function sceneUpdate()
    {
        return $this->only(['id', 'username', 'email', 'phone_code', 'phone', 'password', 'company', 'country_id', 'address', 'notes'])
            ->remove('password', 'require');
    }
}