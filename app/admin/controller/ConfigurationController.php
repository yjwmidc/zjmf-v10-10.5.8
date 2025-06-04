<?php
namespace app\admin\controller;

use app\common\model\ConfigurationModel;
use app\admin\validate\ConfigurationValidate;
use think\captcha\Captcha;

/**
 * @title 系统设置
 * @desc 系统设置
 * @use app\admin\controller\ConfigurationController
 */
class ConfigurationController extends AdminBaseController
{
	public function initialize()
    {
        parent::initialize();
        $this->validate = new ConfigurationValidate();
    }
    /**
     * 时间 2022-5-10
     * @title 获取系统设置
     * @desc 获取系统设置
     * @url /admin/v1/configuration/system
     * @method  GET
     * @author xiong
     * @version v1
     * @return  string lang_admin - 后台默认语言
     * @return  int lang_home_open - 前台多语言开关:1开启0关闭
     * @return  string lang_home - 前台默认语言
     * @return  int maintenance_mode - 维护模式开关:1开启0关闭
     * @return  string maintenance_mode_message - 维护模式内容
     * @return  string website_name - 网站名称
     * @return  string website_url - 网站域名地址
     * @return  string terms_service_url - 服务条款地址
     * @return  string terms_privacy_url - 隐私条款地址
     * @return  string system_logo - 系统LOGO
     * @return  int client_start_id_value - 用户注册开始ID
     * @return  int order_start_id_value - 用户订单开始ID
     * @return  string clientarea_url - 会员中心地址
     * @return  string tab_logo - 标签页LOGO
     * @return  int home_show_deleted_host - 前台是否展示已删除产品:1是0否
     * @return  array prohibit_user_information_changes - 禁止用户信息变更
     * @return  array user_information_fields - 用户信息字段
     * @return  int|string user_information_fields.id - 用户信息字段ID
     * @return  string user_information_fields.name - 用户信息字段名称
     * @return  string user_information_fields.name - 用户信息字段名称
     * @return  string clientarea_logo_url - 会员中心LOGO跳转地址
     * @return  int clientarea_logo_url_blank - 会员中心LOGO跳转是否打开新页面:1是0否
     * @return  object customfield - 自定义参数
     * @return  string ip_white_list - IP白名单，提示回车分隔
     */
    public function systemList()
    {
		//实例化模型类
		$ConfigurationModel = new ConfigurationModel();
		
		//获取系统设置
		$data=$ConfigurationModel->systemList();
        $data['customfield'] = (object)$data['customfield'];
        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data,
        ];
       return json($result);
    }

    /**
     * 时间 2022-5-10
     * @title 保存系统设置
     * @desc 保存系统设置
     * @url /admin/v1/configuration/system
     * @method  PUT
     * @author xiong
     * @version v1
     * @param  string lang_admin - 后台默认语言
     * @param  int lang_home_open - 前台多语言开关:1开启0关闭
     * @param  string lang_home - 前台默认语言
     * @param  int maintenance_mode - 维护模式开关:1开启0关闭
     * @param  string maintenance_mode_message - 维护模式内容
     * @param  string website_name - 网站名称
     * @param  string website_url - 网站域名地址
     * @param  string terms_service_url - 服务条款地址
     * @param  string terms_privacy_url - 隐私条款地址
     * @param  string system_logo - 系统LOGO
     * @param  int client_start_id_value - 用户注册开始ID
     * @param  int order_start_id_value - 用户订单开始ID
     * @param  string clientarea_url - 会员中心地址
     * @param  string tab_logo - 标签页LOGO
     * @param  int home_show_deleted_host - 前台是否展示已删除产品:1是0否
     * @param  array prohibit_user_information_changes - 禁止用户信息变更
     * @param  string clientarea_logo_url - 会员中心LOGO跳转地址
     * @param  int clientarea_logo_url_blank - 会员中心LOGO跳转是否打开新页面:1是0否
     * @param  object customfield - 自定义参数
     * @param  string ip_white_list - IP白名单，提示回车分隔
     */
    public function systemUpdate()
    {
		//接收参数
		$param = $this->request->param();
		
        //参数验证
        if (!$this->validate->scene('system_update')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }
		
		//实例化模型类
		$ConfigurationModel = new ConfigurationModel();
		
		//保存系统设置
		$result = $ConfigurationModel->systemUpdate($param);   
		
        return json($result);
    }
    /**
     * 时间 2022-5-10
     * @title 获取登录设置
     * @desc 获取登录设置
     * @url /admin/v1/configuration/login
     * @method  GET
     * @author xiong
     * @version v1
     * @return  int register_email - 邮箱注册开关:1开启0关闭
     * @return  int register_phone - 是否允许手机号注册/密码登录:1开启0关闭
     * @return  int login_phone_verify - 是否支持手机验证码登录:1开启0关闭
     * @return  int home_login_check_ip - 前台登录检查IP:1开启0关闭
     * @return  int admin_login_check_ip - 后台登录检查IP:1开启0关闭
     * @return  int code_client_email_register - 邮箱注册是否需要验证码:1开启0关闭
     * @return  int code_client_phone_register - 手机注册是否需要验证码:1开启0关闭
     * @return  int limit_email_suffix - 是否限制邮箱后缀:1开启0关闭
     * @return  string email_suffix - 邮箱后缀
     * @return  int home_login_check_common_ip - 前台是否检测常用登录IP:1开启0关闭
     * @return  array home_login_ip_exception_verify - 用户异常登录验证方式(operate_password=操作密码)
     * @return  array home_enforce_safe_method - 前台强制安全选项(phone=手机,email=邮箱,operate_password=操作密码,certification=实名认证,oauth=三方登录扫码)
     * @return  array admin_enforce_safe_method - 后台强制安全选项(operate_password=操作密码)
     * @return  int admin_allow_remember_account - 后台是否允许记住账号:1开启0关闭
     * @return  int login_email_password - 是否开启邮箱密码登录:1开启0关闭
     * @return  array admin_enforce_safe_method_scene - 后台强制安全选项场景(all=全部,client_delete=用户删除,update_client_status=用户停启用,host_operate=产品相关操作,order_delete=订单删除,clear_order_recycle=清空回收站,plugin_uninstall_disable=插件卸载/禁用)
     */
    public function loginList()
    {
		//实例化模型类
		$ConfigurationModel = new ConfigurationModel();
		
		//获取登录设置
		$data=$ConfigurationModel->loginList();
        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data,
        ];
       return json($result);
    }

    /**
     * 时间 2022-5-10
     * @title 保存登录设置
     * @desc 保存登录设置
     * @url /admin/v1/configuration/login
     * @method  PUT
     * @author xiong
     * @version v1
     * @param  int register_email - 邮箱注册开关:1开启0关闭
     * @param  int register_phone - 是否允许手机号注册/密码登录:1开启0关闭
     * @param  int login_phone_verify - 是否支持手机验证码登录:1开启0关闭
     * @param  int home_login_check_ip - 前台登录检查IP:1开启0关闭
     * @param  int admin_login_check_ip - 后台登录检查IP:1开启0关闭
     * @param  int code_client_email_register - 邮箱注册是否需要验证码:1开启0关闭
     * @param  int code_client_phone_register - 手机注册是否需要验证码:1开启0关闭
     * @param  int limit_email_suffix - 是否限制邮箱后缀:1开启0关闭
     * @param  string email_suffix - 邮箱后缀
     * @param  int home_login_check_common_ip - 前台是否检测常用登录IP:1开启0关闭
     * @param  array home_login_ip_exception_verify - 用户异常登录验证方式(operate_password=操作密码)
     * @param  array home_enforce_safe_method - 前台强制安全选项(phone=手机,email=邮箱,operate_password=操作密码,certification=实名认证,oauth=三方登录扫码)
     * @param  array admin_enforce_safe_method - 后台强制安全选项(operate_password=操作密码)
     * @param  int admin_allow_remember_account - 后台是否允许记住账号:1开启0关闭
     * @param  array admin_enforce_safe_method_scene - 后台强制安全选项场景(all=全部,client_delete=用户删除,update_client_status=用户停启用,host_operate=产品相关操作,order_delete=订单删除,clear_order_recycle=清空回收站,plugin_uninstall_disable=插件卸载/禁用)
     * @param  string first_login_method - 首选登录方式(code=验证码,password=密码)
     * @param  string first_password_login_method - 密码登录首选(phone=手机,email=邮箱)
     * @param  int login_email_password - 是否开启邮箱登录:1开启0关闭
     */
    public function loginUpdate()
    {
		//接收参数
		$param = $this->request->param();
		
        //参数验证
        if (!$this->validate->scene('login_update')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }
		
		//实例化模型类
		$ConfigurationModel = new ConfigurationModel();
		
		//保存系统设置
		$result = $ConfigurationModel->loginUpdate($param);   
		
        return json($result);
    }
    /**
     * 时间 2022-5-10
     * @title 获取图形验证码预览
     * @desc 获取图形验证码预览
     * @url /admin/v1/configuration/security/captcha
     * @method  GET
     * @author xiong
     * @version v1
     * @param  int captcha_width - 图形验证码宽度 required
     * @param  int captcha_height - 图形验证码高度 required
     * @param  int captcha_length - 图形验证码字符长度 required
     * @return  string captcha - 图形验证码图片
     */
    public function securityCaptcha()
    {
		//接收参数
		$param = $this->request->param();
		$config = [
            'imageW' => $param['captcha_width'],
            'imageH' => $param['captcha_height'],
            'length' => $param['captcha_length'],
            'codeSet' => '1234567890',
        ];
        $Captcha = new Captcha(app('config'),app('session'));
        $response = $Captcha->create($config);
		$result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => [
				'captcha' => 'data:png;base64,' . base64_encode($response->getData()),
			]
        ];
        return json($result);
    }  
	/**
     * 时间 2022-5-10
     * @title 获取验证码设置
     * @desc 获取验证码设置
     * @url /admin/v1/configuration/security
     * @method  GET
     * @author xiong
     * @version v1
     * @return  int captcha_client_register - 客户注册图形验证码开关:1开启0关闭
     * @return  int captcha_client_login - 客户登录图形验证码开关:1开启0关闭
     * @return  int captcha_client_login_error - 客户登录失败图形验证码开关:1开启0关闭
     * @return  int captcha_admin_login - 管理员登录图形验证码开关:1开启0关闭
     * @return  string captcha_plugin - 验证码插件(从/admin/v1/captcha_list接口获取)
     * @return  int code_client_email_register - 邮箱注册数字验证码开关:1开启0关闭
     * @return  int captcha_client_verify - 验证手机/邮箱图形验证码开关:1开启0关闭
     * @return  int captcha_client_update - 修改手机/邮箱图形验证码开关:1开启0关闭
     * @return  int captcha_client_password_reset - 重置密码图形验证码开关:1开启0关闭
     * @return  int captcha_client_oauth - 三方登录图形验证码开关:1开启0关闭
     */
    public function securityList()
    {
		//实例化模型类
		$ConfigurationModel = new ConfigurationModel();
		
		//获取验证码设置
		$data=$ConfigurationModel->securityList();
        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data,
        ];
       return json($result);
    }

    /**
     * 时间 2022-5-10
     * @title 保存验证码设置
     * @desc 保存验证码设置
     * @url /admin/v1/configuration/security
     * @method  PUT
     * @author xiong
     * @version v1
     * @param  int captcha_client_register - 客户注册图形验证码开关:1开启0关闭
     * @param  int captcha_client_login - 客户登录图形验证码开关:1开启0关闭
     * @param  int captcha_client_login_error - 客户登录失败图形验证码开关:1开启0关闭
     * @param  int captcha_admin_login - 管理员登录图形验证码开关:1开启0关闭
     * @param  string captcha_plugin - 验证码插件(从/admin/v1/captcha_list接口获取)
     * @param  int code_client_email_register - 邮箱注册数字验证码开关:1开启0关闭
     * @param  int captcha_client_verify - 验证手机/邮箱图形验证码开关:1开启0关闭
     * @param  int captcha_client_update - 修改手机/邮箱图形验证码开关:1开启0关闭
     * @param  int captcha_client_password_reset - 重置密码图形验证码开关:1开启0关闭
     * @param  int captcha_client_oauth - 三方登录图形验证码开关:1开启0关闭
     */
    public function securityUpdate()
    {
		//接收参数
		$param = $this->request->param();
		
        //参数验证
        if (!$this->validate->scene('security_update')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }
		
		//实例化模型类
		$ConfigurationModel = new ConfigurationModel();
		
		//保存验证码设置
		$result = $ConfigurationModel->securityUpdate($param);   
		
        return json($result);
    }
    /**
     * 时间 2022-5-10
     * @title 获取货币设置
     * @desc 获取货币设置
     * @url /admin/v1/configuration/currency
     * @method  GET
     * @author xiong
     * @version v1
     * @return  string currency_code - 货币代码
     * @return  string currency_prefix - 货币符号
     * @return  string currency_suffix - 货币后缀
     * @return  int recharge_open - 启用充值:1开启0关闭
     * @return  int recharge_min - 单笔最小金额
     * @return  int recharge_notice - 充值提示开关:1开启0关闭
     * @return  string recharge_money_notice_content - 充值金额提示内容
     * @return  string recharge_pay_notice_content - 充值支付提示内容
     */
    public function currencyList()
    {
		//实例化模型类
		$ConfigurationModel = new ConfigurationModel();
		
		//获取验证码设置
		$data=$ConfigurationModel->currencyList();
        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data,
        ];
       return json($result);
    }

    /**
     * 时间 2022-5-10
     * @title 保存货币设置
     * @desc 保存货币设置
     * @url /admin/v1/configuration/currency
     * @method  PUT
     * @author xiong
     * @version v1
     * @param  string currency_code - 货币代码
     * @param  string currency_prefix - 货币符号
     * @param  string currency_suffix - 货币后缀
     * @param  int recharge_open - 启用充值:1开启0关闭
     * @param  int recharge_min - 单笔最小金额
     * @param  int recharge_notice - 充值提示开关:1开启0关闭
     * @param  string recharge_money_notice_content - 充值金额提示内容
     * @param  string recharge_pay_notice_content - 充值支付提示内容
     */
    public function currencyUpdate()
    {
		//接收参数
		$param = $this->request->param();
		
        //参数验证
        if (!$this->validate->scene('currency_update')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }
		
		//实例化模型类
		$ConfigurationModel = new ConfigurationModel();
		
		//保存验证码设置
		$result = $ConfigurationModel->currencyUpdate($param);   
		
        return json($result);
    }
    /**
     * 时间 2022-5-10
     * @title 获取自动化设置
     * @desc 获取自动化设置
     * @url /admin/v1/configuration/cron
     * @method  GET
     * @return int cron_shell - 自动化脚本
     * @return int cron_status - 自动化状态,正常返回success,不正常返回error
     * @return int cron_due_suspend_swhitch - 产品到期暂停开关 1开启，0关闭
     * @return int cron_due_suspend_day - 产品到期暂停X天后暂停
     * @return int cron_due_unsuspend_swhitch - 财务原因产品暂停后付款自动解封开关 1开启，0关闭
     * @return int cron_due_terminate_swhitch - 产品到期删除开关 1开启，0关闭
     * @return int cron_due_terminate_day - 产品到期X天后删除
     * @return int cron_due_renewal_first_swhitch - 续费第一次提醒开关 1开启，0关闭
     * @return int cron_due_renewal_first_day - 续费X天后到期第一次提醒
     * @return int cron_due_renewal_second_swhitch - 续费第二次提醒开关 1开启，0关闭
     * @return int cron_due_renewal_second_day - 续费X天后到期第二次提醒
     * @return int cron_overdue_first_swhitch - 产品逾期第一次提醒开关 1开启，0关闭
     * @return int cron_overdue_first_day - 产品逾期X天后第一次提醒
     * @return int cron_overdue_second_swhitch - 产品逾期第二次提醒开关 1开启，0关闭
     * @return int cron_overdue_second_day - 产品逾期X天后第二次提醒
     * @return int cron_overdue_third_swhitch - 产品逾期第三次提醒开关 1开启，0关闭
     * @return int cron_overdue_third_day - 产品逾期X天后第三次提醒
     * @return int cron_ticket_close_swhitch - 自动关闭工单开关 1开启，0关闭
     * @return int cron_ticket_close_day - 已回复状态的工单超过x小时后关闭
     * @return int cron_aff_swhitch - 推介月报开关 1开启，0关闭
     * @return int cron_order_overdue_swhitch - 订单未付款通知开关 1开启，0关闭 required
     * @return int cron_order_overdue_day - 订单未付款X天后通知 required
     * @return int cron_task_shell - 任务队列命令 required
     * @return int cron_task_status - 任务队列最新状态:success成功，error失败 required
     * @return int cron_order_unpaid_delete_swhitch - 订单自动删除开关 1开启，0关闭 required
     * @return int cron_order_unpaid_delete_day - 订单未付款X天后自动删除 required
     * @return int cron_day_start_time - 定时任务开始时间 required
     * @return int cron_system_log_delete_swhitch - 系统日志自动删除开关 1开启，0关闭 required
     * @return int cron_system_log_delete_day - 系统日志创建X天后自动删除 required
     * @return int cron_sms_log_delete_swhitch - 短信日志自动删除开关 1开启，0关闭 required
     * @return int cron_sms_log_delete_day - 短信日志创建X天后自动删除 required
     * @return int cron_email_log_delete_swhitch - 邮件日志自动删除开关 1开启，0关闭 required
     * @return int cron_email_log_delete_day - 邮件日志创建X天后自动删除 required
     * @return int task_fail_retry_open - 任务是否重试 required
     * @return int task_fail_retry_times - 任务重试次数 required
     * @author xiong
     * @version v1
     */
    public function cronList()
    {
		//实例化模型类
		$ConfigurationModel = new ConfigurationModel();
		
		//获取自动化设置
		$data=$ConfigurationModel->cronList();
        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data,
        ];
       return json($result);
    }

    /**
     * 时间 2022-5-10
     * @title 保存自动化设置
     * @desc 保存自动化设置
     * @url /admin/v1/configuration/cron
     * @method  PUT
     * @author xiong
     * @version v1
     * @param int cron_due_suspend_swhitch - 产品到期暂停开关1开启，0关闭 required
     * @param int cron_due_suspend_day - 产品到期暂停X天后暂停 required
     * @param int cron_due_unsuspend_swhitch - 财务原因产品暂停后付款自动解封开关1开启，0关闭 required
     * @param int cron_due_terminate_swhitch - 产品到期删除开关1开启，0关闭 required
     * @param int cron_due_terminate_day - 产品到期X天后删除 required
     * @param int cron_due_renewal_first_swhitch - 续费第一次提醒开关1开启，0关闭 required
     * @param int cron_due_renewal_first_day - 续费X天后到期第一次提醒 required
     * @param int cron_due_renewal_second_swhitch - 续费第二次提醒开关1开启，0关闭 required
     * @param int cron_due_renewal_second_day - 续费X天后到期第二次提醒 required
     * @param int cron_overdue_first_swhitch - 产品逾期第一次提醒开关1开启，0关闭 required
     * @param int cron_overdue_first_day - 产品逾期X天后第一次提醒 required
     * @param int cron_overdue_second_swhitch - 产品逾期第二次提醒开关1开启，0关闭 required
     * @param int cron_overdue_second_day - 产品逾期X天后第二次提醒 required
     * @param int cron_overdue_third_swhitch - 产品逾期第三次提醒开关1开启，0关闭 required
     * @param int cron_overdue_third_day - 产品逾期X天后第三次提醒 required
     * @param int cron_ticket_close_swhitch - 自动关闭工单开关 1开启，0关闭 required
     * @param int cron_ticket_close_day - 已回复状态的工单超过x小时后关闭 required
     * @param int cron_aff_swhitch - 推介月报开关 1开启，0关闭 required
     * @param int cron_order_overdue_swhitch - 订单未付款通知开关 1开启，0关闭 required
     * @param int cron_order_overdue_day - 订单未付款X天后通知 required
     * @param int cron_order_unpaid_delete_swhitch - 订单自动删除开关 1开启，0关闭 required
     * @param int cron_order_unpaid_delete_day - 订单未付款X天后自动删除 required
     * @param int cron_day_start_time - 定时任务开始时间 required
     * @param int cron_system_log_delete_swhitch - 系统日志自动删除开关 1开启，0关闭 required
     * @param int cron_system_log_delete_day - 系统日志创建X天后自动删除 required
     * @param int cron_sms_log_delete_swhitch - 短信日志自动删除开关 1开启，0关闭 required
     * @param int cron_sms_log_delete_day - 短信日志创建X天后自动删除 required
     * @param int cron_email_log_delete_swhitch - 邮件日志自动删除开关 1开启，0关闭 required
     * @param int cron_email_log_delete_day - 邮件日志创建X天后自动删除 required
     */
    public function cronUpdate()
    {
		//接收参数
		$param = $this->request->param();
		
        //参数验证
        if (!$this->validate->scene('cron_update')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }
		
		//实例化模型类
		$ConfigurationModel = new ConfigurationModel();
		
		//保存验证码设置
		$result = $ConfigurationModel->cronUpdate($param);   
		
        return json($result);
    }

    /**
     * 时间 2022-08-12
     * @title 获取主题设置
     * @desc 获取主题设置
     * @url /admin/v1/configuration/theme
     * @method  GET
     * @author theworld
     * @version v1
     * @return string admin_theme - 后台主题
     * @return string clientarea_theme - 会员中心主题
     * @return string cart_theme - 购物车主题
     * @return string cart_theme_mobile - 购物车主题手机端
     * @return int cart_instruction - 购物车说明开关0关闭1开启
     * @return string cart_instruction_content - 购物车说明内容
     * @return int cart_change_product - 购物车说切换商品开关0关闭1开启
     * @return int web_switch - 官网开关0关闭1开启
     * @return string web_theme - 官网主题
     * @return array admin_theme_list - 后台主题列表
     * @return string first_navigation - 一级导航名称
     * @return string second_navigation - 二级导航名称
     * @return string admin_theme_list[].name - 名称
     * @return string admin_theme_list[].img - 图片
     * @return array clientarea_theme_list - 会员中心主题列表
     * @return string clientarea_theme_list[].name - 名称
     * @return string clientarea_theme_list[].img - 图片
     * @return array web_theme_list - 官网主题列表
     * @return string web_theme_list[].name - 名称
     * @return string web_theme_list[].img - 图片
     * @return string cart_theme_mobile_list[].name - 名称
     * @return string cart_theme_mobile_list[].img - 图片
     */
    public function themeList()
    {
        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();
        
        //获取主题设置
        $data = $ConfigurationModel->themeList();
        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data,
        ];
       return json($result);
    }

    /**
     * 时间 2022-08-12
     * @title 保存主题设置
     * @desc 保存主题设置
     * @url /admin/v1/configuration/theme
     * @method  PUT
     * @author theworld
     * @version v1
     * @param string admin_theme - 后台主题 required
     * @param string clientarea_theme - 会员中心主题 required
     * @param string cart_theme - 购物车主题 required
     * @param string clientarea_theme_mobile_switch - 是否开启购物车主题 required
     * @param string cart_theme_mobile - 购物车主题手机端 required
     * @param int cart_instruction - 购物车说明开关0关闭1开启 required
     * @param string cart_instruction_content - 购物车说明内容
     * @param int cart_change_product - 购物车说切换商品开关0关闭1开启 required
     * @param int web_switch - 官网开关0关闭1开启 required
     * @param string web_theme - 官网主题 required
     * @param string first_navigation - 一级导航名称
     * @param string second_navigation - 二级导航名称
     */
    public function themeUpdate()
    {
        //接收参数
        $param = $this->request->param();
        
        //参数验证
        if (!$this->validate->scene('theme_update')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }
        
        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();
        
        //保存主题设置
        $result = $ConfigurationModel->themeUpdate($param);   
        
        return json($result);
    }

    /**
     * 时间 2022-09-23
     * @title 获取实名设置
     * @desc 获取实名设置
     * @url /admin/v1/configuration/certification
     * @method  GET
     * @author wyh
     * @version v1
     * @return int certification_open - 实名认证是否开启:1开启默认,0关
     * @return int certification_approval - 是否人工复审:1开启默认，0关
     * @return int certification_notice - 审批通过后,是否通知客户:1通知默认,0否
     * @return int certification_update_client_name - 是否自动更新姓名:1是,0否默认
     * @return int certification_upload - 是否需要上传证件照:1是,0否默认
     * @return int certification_update_client_phone - 手机一致性:1是,0否默认
     * @return int certification_uncertified_suspended_host - 未认证暂停产品:1是,0否默认
     */
    public function certificationList()
    {
        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //获取主题设置
        $data = $ConfigurationModel->certificationList();
        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data,
        ];
        return json($result);
    }

    /**
     * 时间 2022-08-12
     * @title 保存实名设置
     * @desc 保存实名设置
     * @url /admin/v1/configuration/certification
     * @method  PUT
     * @author theworld
     * @version v1
     * @param int certification_open - 实名认证是否开启:1开启默认,0关 required
     * @param int certification_approval - 是否人工复审:1开启默认，0关 required
     * @param int certification_notice - 审批通过后,是否通知客户:1通知默认,0否 required
     * @param int certification_update_client_name - 是否自动更新姓名:1是,0否默认 required
     * @param int certification_upload - 是否需要上传证件照:1是,0否默认 required
     * @param int certification_update_client_phone - 手机一致性:1是,0否默认 required
     * @param int certification_uncertified_suspended_host - 未认证暂停产品:1是,0否默认 required
     */
    public function certificationUpdate()
    {
        //接收参数
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('certification_update')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //保存主题设置
        $result = $ConfigurationModel->certificationUpdate($param);

        return json($result);
    }

    /**
     * 时间 2023-02-28
     * @title 获取信息配置
     * @desc 获取信息配置
     * @url /admin/v1/configuration/info
     * @method  GET
     * @author theworld
     * @version v1
     * @return string enterprise_name - 企业名称
     * @return string enterprise_telephone - 企业电话
     * @return string enterprise_mailbox - 企业邮箱
     * @return string enterprise_qrcode - 企业二维码
     * @return string online_customer_service_link - 在线客服链接
     * @return string icp_info - ICP信息
     * @return string icp_info_link - ICP信息信息链接
     * @return string public_security_network_preparation - 公安网备
     * @return string public_security_network_preparation_link - 公安网备链接
     * @return string telecom_appreciation - 电信增值
     * @return string copyright_info - 版权信息
     * @return string official_website_logo - 官网LOGO
     * @return string cloud_product_link - 云产品跳转链接
     * @return string dcim_product_link - DCIM产品跳转链接
     */
    public function infoList()
    {
        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //获取信息配置
        $data = $ConfigurationModel->infoList();
        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data,
        ];
        return json($result);
    }

    /**
     * 时间 2023-02-28
     * @title 保存信息配置
     * @desc 保存信息配置
     * @url /admin/v1/configuration/info
     * @method  PUT
     * @author theworld
     * @version v1
     * @param string enterprise_name - 企业名称 required
     * @param string enterprise_telephone - 企业电话 required
     * @param string enterprise_mailbox - 企业邮箱 required
     * @param string enterprise_qrcode - 企业二维码 required
     * @param string online_customer_service_link - 在线客服链接 required
     * @param string icp_info - ICP信息 required
     * @param string icp_info_link - ICP信息信息链接 required
     * @param string public_security_network_preparation - 公安网备 required
     * @param string public_security_network_preparation_link - 公安网备链接 required
     * @param string telecom_appreciation - 电信增值 required
     * @param string copyright_info - 版权信息 required
     * @param string official_website_logo - 官网LOGO required
     * @param string cloud_product_link - 云产品跳转链接 required
     * @param string dcim_product_link - DCIM产品跳转链接 required
     */
    public function infoUpdate()
    {
        //接收参数
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('info_update')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //保存信息配置
        $result = $ConfigurationModel->infoUpdate($param);

        return json($result);
    }

    /**
     * 时间 2023-09-07
     * @title debug页面
     * @desc debug页面
     * @url /admin/v1/configuration/debug
     * @method  GET
     * @author wyh
     * @version v1
     * @return string debug_model - 1开启debug模式
     * @return string debug_model_auth - debug模式授权码
     * @return string debug_model_expire_time - 到期时间
     */
    public function debugInfo()
    {
        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //获取信息配置
        $data = $ConfigurationModel->debugInfo();

        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data,
        ];

        return json($result);
    }

    /**
     * 时间 2023-09-07
     * @title 保存debug页面
     * @desc 保存debug页面
     * @url /admin/v1/configuration/debug
     * @method  PUT
     * @author wyh
     * @version v1
     * @param string debug_model - 1开启debug模式 required
     */
    public function debug()
    {
        //接收参数
        $param = $this->request->param();

        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //保存信息配置
        $result = $ConfigurationModel->debug($param);

        return json($result);
    }

    /**
     * 时间 2024-01-26
     * @title 对象存储页面
     * @desc 对象存储页面
     * @url /admin/v1/configuration/oss
     * @method  GET
     * @author wyh
     * @version v1
     * @return string oss_method - 对象存储方式，默认本地存储：LocalOss
     * @return string oss_sms_plugin - 短信接口
     * @return string oss_sms_plugin_template - 短信模板
     * @return array oss_sms_plugin_admin - 短信通知人员
     * @return string oss_mail_plugin - 邮件接口
     * @return string oss_mail_plugin_template - 邮件模板
     * @return array oss_mail_plugin_admin - 邮件通知人员
     */
    public function getOssConfig()
    {
        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //获取信息配置
        $data = $ConfigurationModel->getOssConfig();

        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data,
        ];

        return json($result);
    }

    /**
     * 时间 2024-01-26
     * @title 保存对象存储页面
     * @desc 保存对象存储页面
     * @url /admin/v1/configuration/oss
     * @method  PUT
     * @author wyh
     * @version v1
     * @param string oss_method - 对象存储方式，默认本地存储：LocalOss
     * @param string oss_sms_plugin - 短信接口
     * @param string oss_sms_plugin_template - 短信模板
     * @param array oss_sms_plugin_admin - 短信通知人员
     * @param string oss_mail_plugin - 邮件接口
     * @param string oss_mail_plugin_template - 邮件模板
     * @param array oss_mail_plugin_admin - 邮件通知人员
     * @param string password - 当修改本地存储时，需要传此字段
     */
    public function ossConfig()
    {
        //接收参数
        $param = $this->request->param();

        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //保存信息配置
        $result = $ConfigurationModel->ossConfig($param);

        return json($result);
    }

     /**
     * 时间 2024-04-02
     * @title 获取网站参数配置
     * @desc 获取网站参数配置
     * @url /admin/v1/configuration/web
     * @method  GET
     * @author theworld
     * @version v1
     * @return string enterprise_name - 企业名称
     * @return string enterprise_telephone - 企业电话
     * @return string enterprise_mailbox - 企业邮箱
     * @return string enterprise_qrcode - 企业二维码
     * @return string online_customer_service_link - 在线客服链接
     * @return string icp_info - ICP信息
     * @return string icp_info_link - ICP信息链接
     * @return string public_security_network_preparation - 公安网备
     * @return string public_security_network_preparation_link - 公安网备链接
     * @return string telecom_appreciation - 电信增值
     * @return string copyright_info - 版权信息
     * @return string official_website_logo - 官网LOGO
     */
    public function webList()
    {
        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //获取网站参数配置
        $data = $ConfigurationModel->webList();
        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data,
        ];
        return json($result);
    }

    /**
     * 时间 2024-04-02
     * @title 保存网站参数配置
     * @desc 保存网站参数配置
     * @url /admin/v1/configuration/web
     * @method  PUT
     * @author theworld
     * @version v1
     * @param string enterprise_name - 企业名称 required
     * @param string enterprise_telephone - 企业电话 required
     * @param string enterprise_mailbox - 企业邮箱 required
     * @param string enterprise_qrcode - 企业二维码 required
     * @param string online_customer_service_link - 在线客服链接 required
     * @param string icp_info - ICP信息 required
     * @param string icp_info_link - ICP信息信息链接 required
     * @param string public_security_network_preparation - 公安网备 required
     * @param string public_security_network_preparation_link - 公安网备链接 required
     * @param string telecom_appreciation - 电信增值 required
     * @param string copyright_info - 版权信息 required
     * @param string official_website_logo - 官网LOGO required
     */
    public function webUpdate()
    {
        //接收参数
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('web')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //保存网站参数配置
        $result = $ConfigurationModel->webUpdate($param);

        return json($result);
    }

    /**
     * 时间 2024-04-02
     * @title 获取云服务器配置
     * @desc 获取云服务器配置
     * @url /admin/v1/configuration/cloud_server
     * @method  GET
     * @author theworld
     * @version v1
     * @return int cloud_server_more_offers - 更多优惠0关闭1开启
     */
    public function cloudServerList()
    {
        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //获取云服务器配置
        $data = $ConfigurationModel->cloudServerList();
        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data,
        ];
        return json($result);
    }

    /**
     * 时间 2024-04-02
     * @title 保存云服务器配置
     * @desc 保存云服务器配置
     * @url /admin/v1/configuration/cloud_server
     * @method  PUT
     * @author theworld
     * @version v1
     * @param int cloud_server_more_offers - 更多优惠0关闭1开启 required
     */
    public function cloudServerUpdate()
    {
        //接收参数
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('cloud_server')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //保存云服务器配置
        $result = $ConfigurationModel->cloudServerUpdate($param);

        return json($result);
    }

    /**
     * 时间 2024-04-02
     * @title 获取物理服务器配置
     * @desc 获取物理服务器配置
     * @url /admin/v1/configuration/physical_server
     * @method  GET
     * @author theworld
     * @version v1
     * @return int physical_server_more_offers - 更多优惠0关闭1开启
     */
    public function physicalServerList()
    {
        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //获取物理服务器配置
        $data = $ConfigurationModel->physicalServerList();
        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data,
        ];
        return json($result);
    }

    /**
     * 时间 2024-04-02
     * @title 保存物理服务器配置
     * @desc 保存物理服务器配置
     * @url /admin/v1/configuration/physical_server
     * @method  PUT
     * @author theworld
     * @version v1
     * @param int physical_server_more_offers - 更多优惠0关闭1开启 required
     */
    public function physicalServerUpdate()
    {
        //接收参数
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('physical_server')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //保存物理服务器配置
        $result = $ConfigurationModel->physicalServerUpdate($param);

        return json($result);
    }

    /**
     * 时间 2024-04-02
     * @title 获取ICP配置
     * @desc 获取ICP配置
     * @url /admin/v1/configuration/icp
     * @method  GET
     * @author theworld
     * @version v1
     * @return int icp_product_id - 购买/咨询商品ID
     */
    public function icpList()
    {
        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //获取ICP配置
        $data = $ConfigurationModel->icpList();
        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data,
        ];
        return json($result);
    }

    /**
     * 时间 2024-04-02
     * @title 保存ICP配置
     * @desc 保存ICP配置
     * @url /admin/v1/configuration/icp
     * @method  PUT
     * @author theworld
     * @version v1
     * @param int icp_product_id - 购买/咨询商品ID required
     */
    public function icpUpdate()
    {
        //接收参数
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('icp')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //保存ICP配置
        $result = $ConfigurationModel->icpUpdate($param);

        return json($result);
    }

    /**
     * 时间 2024-10-22
     * @title 获取商品全局设置
     * @desc 获取商品全局设置
     * @url /admin/v1/configuration/product
     * @method  GET
     * @author theworld
     * @version v1
     * @return int self_defined_field_apply_range - 自定义字段应用范围(0=无1=商品分组新增商品)
     * @return int custom_host_name_apply_range - 自定义标识应用范围(0=无1=商品分组新增商品)
     * @return int product_duration_group_presets_open - 是否开启商品周期分组预设
     * @return int product_duration_group_presets_apply_range - 商品周期分组预设应用范围(0全局默认，1接口新增商品)
     * @return int product_duration_group_presets_default_id - 商品周期分组预设全局默认分组ID
     * @return int product_new_host_renew_with_ratio_open - 新产品续费按周期比例折算(0关闭，1开启)
     * @return int product_new_host_renew_with_ratio_apply_range - 新产品续费按周期比例折算范围(2商品分组下新产品，1接口下新产品，0全部新产品)
     * @return array product_new_host_renew_with_ratio_apply_range_2 - 二级分组id，逗号分隔
     * @return array product_new_host_renew_with_ratio_apply_range_1 - 接口id，逗号分隔
     * @return int product_global_renew_rule - 商品到期日计算规则(0实际到期日，1产品到期日)
     * @return int product_global_show_base_info - 基础信息展示(0关闭，1开启)
     * @return int product_renew_with_new_open - 商品续费时重新计算续费金额(0关闭，1开启)
     * @return array product_renew_with_new_product_ids - 所选商品ID
     * @return int product_overdue_not_delete_open - 商品到期后不自动删除(0关闭，1开启)
     * @return array product_overdue_not_delete_product_ids - 到期后不自动删除的商品ID
     * @return int host_sync_due_time_open - 产品到期时间与上游一致(0关闭，1开启)
     * @return int host_sync_due_time_apply_range - 产品到期时间与上游一致应用范围(0全部上游商品，1自定义上游商品)
     * @return array host_sync_due_time_product_ids - 产品到期时间与上游一致的商品ID
     */
    public function productGlobalSetting()
    {
        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //获取商品全局设置
        $data = $ConfigurationModel->productGlobalSetting();
        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data,
        ];
        return json($result);
    }

    /**
     * 时间 2024-10-22
     * @title 保存商品全局设置
     * @desc 保存商品全局设置
     * @url /admin/v1/configuration/product
     * @method  PUT
     * @param int self_defined_field_apply_range - 自定义字段应用范围(0=无1=商品分组新增商品) required
     * @param int custom_host_name_apply_range - 自定义标识应用范围(0=无1=商品分组新增商品) required
     * @param int product_duration_group_presets_open - 是否开启商品周期分组预设 required
     * @param int product_duration_group_presets_apply_range - 商品周期分组预设应用范围(0全局默认，1接口新增商品) required
     * @param int product_duration_group_presets_default_id - 商品周期分组预设全局默认分组ID required
     * @param int product_new_host_renew_with_ratio_open - 新产品续费按周期比例折算(0关闭，1开启)
     * @param int product_new_host_renew_with_ratio_apply_range - 新产品续费按周期比例折算范围(2商品分组下新产品，1接口下新产品，0全部新产品)
     * @param array product_new_host_renew_with_ratio_apply_range_2 - 二级分组id数组
     * @param array product_new_host_renew_with_ratio_apply_range_1 - 接口id数组
     * @param int product_global_renew_rule - 商品到期日计算规则(0实际到期日，1产品到期日)
     * @param int product_global_show_base_info - 基础信息展示(0关闭，1开启)
     * @param int product_renew_with_new_open - 商品续费时重新计算续费金额(0关闭，1开启)
     * @param array product_renew_with_new_product_ids - 所选商品ID
     * @param int product_overdue_not_delete_open - 商品到期后不自动删除(0关闭，1开启)
     * @param array product_overdue_not_delete_product_ids - 到期后不自动删除的商品ID
     * @param int host_sync_due_time_open - 产品到期时间与上游一致(0关闭，1开启)
     * @param int host_sync_due_time_apply_range - 产品到期时间与上游一致应用范围(0全部上游商品，1自定义上游商品)
     * @param array host_sync_due_time_product_ids - 产品到期时间与上游一致的商品ID
     * @author theworld
     * @version v1
     */
    public function productGlobalSettingUpdate()
    {
        //接收参数
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('product')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //保存商品全局设置
        $result = $ConfigurationModel->productGlobalSettingUpdate($param);

        return json($result);
    }

    /**
     * 时间 2024-12-23
     * @title 游客可见商品
     * @desc 游客可见商品
     * @url /admin/v1/configuration/tourist_visible_product
     * @method  get
     * @return array tourist_visible_product_ids - 商品ID
     * @author wyh
     * @version v1
     */
    public function touristVisibleProduct()
    {
        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //获取游客可见商品
        $result = $ConfigurationModel->touristVisibleProduct();

        return json($result);
    }

    /**
     * 时间 2024-12-23
     * @title 游客可见商品
     * @desc 游客可见商品
     * @url /admin/v1/configuration/tourist_visible_product
     * @method  put
     * @param  array tourist_visible_product_ids - 商品ID
     * @author wyh
     * @version v1
     */
    public function touristVisibleProductUpdate()
    {
        $param = $this->request->param();

        $ConfigurationModel = new ConfigurationModel();

        $result = $ConfigurationModel->touristVisibleProductUpdate($param);

        return json($result);
    }

    /**
     * 时间 2025-01-16
     * @title 获取代理商余额预警设置
     * @desc  获取代理商余额预警设置
     * @url /admin/v1/configuration/supplier_credit_warning
     * @method  GET
     * @author hh
     * @version v1
     * @return int supplier_credit_warning_notice - 余额预警(0=关闭,1=开启)
     * @return string supplier_credit_amount - 自定义余额提醒大小
     * @return int supplier_credit_push_frequency - 推送频率(1=一天一次,2=一天两次,3=一天三次)
     */
    public function supplierCreditWarning()
    {
        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //获取商品全局设置
        $data = $ConfigurationModel->supplierCreditWarning();
        $result = [
            'status' => 200,
            'msg'    => lang('success_message'),
            'data'   => $data,
        ];
        return json($result);
    }

    /**
     * 时间 2025-01-16
     * @title 保存代理商余额预警设置
     * @desc  保存代理商余额预警设置
     * @url /admin/v1/configuration/supplier_credit_warning
     * @method  PUT
     * @param  int supplier_credit_warning_notice - 余额预警(0=关闭,1=开启)
     * @param  float supplier_credit_amount - 自定义余额提醒大小
     * @param  int supplier_credit_push_frequency - 推送频率(1=一天一次,2=一天两次,3=一天三次)
     * @author hh
     * @version v1
     */
    public function supplierCreditWarningUpdate()
    {
        //接收参数
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('supplier_credit_amount')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        //实例化模型类
        $ConfigurationModel = new ConfigurationModel();

        //保存商品全局设置
        $result = $ConfigurationModel->supplierCreditWarningUpdate($param);

        return json($result);
    }

}

