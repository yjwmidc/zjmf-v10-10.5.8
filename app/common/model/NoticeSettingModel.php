<?php
namespace app\common\model;

use think\Model;
use think\Db;
use app\admin\model\PluginModel;
use app\common\model\EmailTemplateModel;
use app\common\model\SmsTemplateModel;
use app\common\model\ConfigurationModel;
/**
 * @title 通知发送管理
 * @desc  通知发送管理
 * @use app\common\model\NoticeSettingModel
 */
class NoticeSettingModel extends Model
{
	protected $name = 'notice_setting';
	protected $pk = 'name';

	protected $schema = [
        'id'            		=> 'int',
        'name'          		=> 'string',
        'name_lang'     		=> 'string',
        'sms_global_name'   	=> 'string',
        'sms_global_template'   => 'int',
        'sms_name'   			=> 'string',
        'sms_template'   		=> 'int',
        'sms_enable'   			=> 'int',
        'email_name'   			=> 'string',
        'email_template'   		=> 'int',
        'email_enable'   		=> 'int',
        'type'                  => 'string',
    ];

    /**
     * 时间 2022-05-18
     * @title 发送管理
     * @desc  发送管理
     * @author xiong
     * @version v1
     * @return array list - 发送管理
     * @return string list[].name - 动作名称 
     * @return int list[].sms_global_name - 短信国际接口名称 
     * @return int list[].sms_global_template - 短信国际接口模板id 
     * @return string list[].sms_name - 短信国内接口名称 
     * @return int list[].sms_template - 短信国内接口模板id 
     * @return int list[].sms_enable - 启用状态，0禁用,1启用 
     * @return string list[].email_name - 邮件接口名称 
     * @return int list[].email_template - 邮件接口模板id 
     * @return int list[].email_enable - 启用状态，0禁用,1启用 
     * @return string list[].type - 通知分类标识
     * @return array configuration - 默认接口
     * @return string configuration.send_sms - 默认国内短信接口
     * @return string configuration.send_sms_global - 默认国际短信接口
     * @return string configuration.send_email - 默认邮件接口
     * @return string type[].name - 通知分类标识
     * @return string type[].name_lang - 分类名称
     */
    public function settingList(): array
    {
        $notice_setting = $this
                    ->field('name,name_lang,sms_global_name,sms_global_template,sms_name,sms_template,sms_enable,email_name,email_template,email_enable,type')
                    ->filter(function($item){
                        if(empty($item->name_lang)){
                            $item->name_lang = lang('notice_action_'.$item['name']);
                        }
                    })
		            ->select()
		            ->toArray();
		// $notice_action = config("idcsmart.notice_action");
		// $notice_setting_new = [];
		// foreach($notice_setting as $v){			
		// 	$notice_setting_new[$v['name']] = $v;
		// 	if(empty($v['name_lang'])) $notice_setting_new[$v['name']]['name_lang'] = lang('notice_action_'.$v['name']);
		// }
		// unset($notice_setting);
		// if($notice_setting_new){
		// 	$notice_setting_new_keys = array_keys($notice_setting_new);
		// 	$notice_action=array_merge($notice_action,$notice_setting_new_keys);
		// 	$notice_action=array_values(array_unique($notice_action));
		// }
		// foreach($notice_action as $v){
		// 	if(empty($notice_setting_new[$v])){
		// 		$arr = [
		// 			'name' => $v,
		// 			'name_lang' => lang('notice_action_'.$v),
		// 			'sms_global_name' => '',
		// 			'sms_global_template' => 0,
		// 			'sms_name' => '',
		// 			'sms_template' => 0,
		// 			'sms_enable' => 0,
		// 			'email_name' => '',
		// 			'email_template' => 0,
		// 			'email_enable' => 0,
		// 		];
		// 		$notice_action_new[] = $arr;
		// 	}else{
		// 		$notice_action_new[] = $notice_setting_new[$v];
		// 	}
		// }
		$configuration = (new ConfigurationModel())->sendList();

        // 获取所有分类
        $type = $this->getNoticeSettingType();

        return ['list' => $notice_setting,'configuration' => $configuration, 'type' => $type ];
    }
    /**
     * 时间 2022-05-18
     * @title 发送管理
     * @desc 发送管理
     * @author xiong
     * @version v1
     * @param string name - 动作名称 required
     * @return string name - 动作名称 
     * @return int sms_global_name - 短信国际接口名称 
     * @return int sms_global_template - 短信国际接口模板id 
     * @return string sms_name - 短信国内接口名称 
     * @return int sms_template - 短信国内接口模板id 
     * @return int sms_enable - 启用状态，0禁用,1启用 
     * @return string email_name - 邮件接口名称 
     * @return int email_template - 邮件接口模板id 
     * @return int email_enable - 启用状态，0禁用,1启用 
     */
    public function indexSetting($name)
    {
        $notice_setting = $this->field('name,name_lang,sms_global_name,sms_global_template,sms_name,sms_template,sms_enable,email_name,email_template,email_enable')
		->find($name);
		if(empty($notice_setting)){
			$notice_setting = [
					'name' => $name,
					'sms_global_name' => '',
					'sms_global_template' => 0,
					'sms_name' => '',
					'sms_template' => 0,
					'sms_enable' => 0,
					'email_name' => '',
					'email_template' => 0,
					'email_enable' => 0,
				];
		}
		if(empty($notice_setting['name_lang'])) $notice_setting['name_lang'] = lang('notice_action_'.$notice_setting['name']);
        return $notice_setting;
    }
    /**
     * 时间 2022-05-18
     * @title 发送设置
     * @desc 发送设置
     * @author xiong
     * @version v1
     * @param array $name - 动作名称为键 
     * @param string $name.name - 动作名称 
     * @param int $name.sms_global_name - 短信国际接口名称 
     * @param int $name.sms_global_template - 短信国际接口模板id 
     * @param string $name.sms_name - 短信国内接口名称 
     * @param int $name.sms_template - 短信国内接口模板id 
     * @param int $name.sms_enable - 启用状态，0禁用,1启用 
     * @param string $name.email_name - 邮件接口名称 
     * @param int $name.email_template - 邮件接口模板id 
     * @param int $name.email_enable - 启用状态，0禁用,1启用 
     * @param array configuration - 默认接口
     * @param string configuration.send_sms - 默认国内短信接口
     * @param string configuration.send_sms_global - 默认国际短信接口
     * @param string configuration.send_email - 默认邮件接口	 
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function updateNoticeSetting($params)
    {
        /* if (empty($param['name'])){
            return ['status'=>400, 'msg'=>lang('notice_setting_name_not_exist')];
        } */
		$configuration = (!empty($params['configuration']))?$params['configuration']:[]; unset($params['configuration']);
		$action_name = array_keys($params);
		$notice_action = config("idcsmart.notice_action");
		$action_name_intersect = array_intersect($action_name,$notice_action);
		if(count($action_name_intersect) != count($notice_action)){
			return ['status'=>400, 'msg'=>lang('notice_setting_name_not_exist')];
		}
        $this->startTrans();
        try {
			
			$sms = (new PluginModel)->pluginList(['module'=>'sms']);//短信插件
			$mail=(new PluginModel)->pluginList(['module'=>'mail']);//邮件插件
			$sms_type = !empty($sms['list']) ? array_column($sms['list'],"sms_type","name"):[]; 	
			$sms_status = !empty($sms['list']) ? array_column($sms['list'],"status","name"):[]; 
			$mail_name = !empty($mail['list']) ? array_column($mail['list'],"name","name"):[]; 
			$SmsTemplateModel=new SmsTemplateModel();
			$EmailTemplateModel=new EmailTemplateModel();
			if(!empty($configuration)){
				if(empty($sms_type[$configuration['send_sms']]) || !in_array(0,$sms_type[$configuration['send_sms']]) || empty($sms_status[$configuration['send_sms']])){
					return ['status'=>400, 'msg'=>lang('send_sms_interface_is_not_exist_domestic')];//默认设置国内短信接口不存在
				}
				if(empty($sms_type[$configuration['send_sms_global']]) || !in_array(1,$sms_type[$configuration['send_sms_global']]) || empty($sms_status[$configuration['send_sms_global']])){
					return ['status'=>400, 'msg'=>lang('send_sms_interface_is_not_exist_domestic')];//默认设置国际短信接口不存在
				}
				if(empty($mail_name[$configuration['send_email']]) ){
					return ['status'=>400, 'msg'=>lang('send_mail_interface_is_not_exist')];//邮件接口不存在
				}
				(new ConfigurationModel())->sendUpdate($configuration);//保存默认发送接口
			}
			
			
			$description = [];

			foreach($params as $name=>$param){
				$notice_setting = $this->indexSetting($param['name']);
				//短信判断
								
					
				//国内短信
				if(!empty($param['sms_name'])){
					if(empty($sms_type[$param['sms_name']]) || !in_array(0,$sms_type[$param['sms_name']]) || empty($sms_status[$param['sms_name']])){
						return ['status'=>400, 'msg'=>lang('send_sms_interface_is_not_exist_domestic')];//国内短信接口不存在
					}
					if($sms_status[$param['sms_name']]==0){
						return ['status'=>400, 'msg'=>lang('send_sms_interface_is_disabled_domestic')];//国内短信接口已禁用
					}else if($sms_status[$param['sms_name']]==3){
						return ['status'=>400, 'msg'=>lang('send_sms_interface_not_installed_domestic')];//国内短信接口未安装
					}
					$sms_cn_template = $SmsTemplateModel->smsTemplateList($param['sms_name']);//国内模板
					$sms_cn_template = array_column($sms_cn_template['list'],"type","id");//type=0	
					if(!isset($sms_cn_template[$param['sms_template']]) || $sms_cn_template[$param['sms_template']]!=0){
						return ['status'=>400, 'msg'=>lang('notice_setting_sms_template_error')];//国内短信模板不存在
					}
				}
				//国际短信
				if(!empty($param['sms_global_name'])){
					if(empty($sms_type[$param['sms_global_name']]) || !in_array(1,$sms_type[$param['sms_global_name']]) || empty($sms_status[$param['sms_global_name']])){
						return ['status'=>400, 'msg'=>lang('send_sms_interface_is_not_exist_global')];//国际短信接口不存在
					}
					if($sms_status[$param['sms_global_name']]==0){
						return ['status'=>400, 'msg'=>lang('send_sms_interface_is_disabled_global')];//国际短信接口已禁用
					}else if($sms_status[$param['sms_global_name']]==3){
						return ['status'=>400, 'msg'=>lang('send_sms_interface_is_not_exist_global')];//国际短信接口不存在
					}
					
					/* if(empty($sms_status[$param['sms_global_name']])){
						return ['status'=>400, 'msg'=>lang('send_sms_interface_is_disabled_global')];//国际短信接口已禁用
					}
					if(empty($sms_type[$param['sms_global_name']])){
						return ['status'=>400, 'msg'=>lang('send_sms_interface_is_not_exist_global')];//国际短信接口不存在
					}
					if(!in_array(1,$sms_type[$param['sms_global_name']])){
						return ['status'=>400, 'msg'=>lang('send_sms_interface_is_not_exist_global')];//国际短信接口不支持国内发送
					} */
					$sms_global_template = $SmsTemplateModel->smsTemplateList($param['sms_global_name']);//国际
					$sms_global_template = array_column($sms_global_template['list'],"type","id");//type=1
					//if($sms_global_template[$param['sms_global_template']]===1){
					if(empty($sms_global_template[$param['sms_global_template']]) || $sms_global_template[$param['sms_global_template']]!=1){
						return ['status'=>400, 'msg'=>lang('notice_setting_sms_global_template_error')];//国际短信模板不存在
					}
				}
				
				//邮件判断
				if(!empty($param['email_name'])){
									
						
					if(empty($mail_name[$param['email_name']]) ){
						return ['status'=>400, 'msg'=>lang('send_mail_interface_is_not_exist')];//邮件接口不存在
					}
					$email_template = $EmailTemplateModel->emailTemplateList();
					$email_template=array_column($email_template['list'],"id","id");
					if(!in_array($param['email_template'],$email_template)){
						return ['status'=>400, 'msg'=>lang('notice_setting_email_template_error')];//邮件模板不存在
					}
				}
			
				if (empty($notice_setting)){
					
					$this->create([
						'name' => $param['name'],
						'sms_global_name' => $param['sms_global_name'] ? trim($param['sms_global_name']) : '',
						'sms_global_template' => $param['sms_global_template'] ? trim($param['sms_global_template']) : 0,
						'sms_name' => $param['sms_name'] ? trim($param['sms_name']) : '',
						'sms_template' => $param['sms_template'] ? trim($param['sms_template']) : 0,
						'sms_enable' => $param['sms_enable'] ? trim($param['sms_enable']) : 0,
						'email_name' => $param['email_name'] ? trim($param['email_name']) : '',
						'email_template' => $param['email_template'] ? trim($param['email_template']) : 0,
						'email_enable' => $param['email_enable'] ? trim($param['email_enable']) : 0,
					]);

				}else{
					$this->update([
						'name' => $param['name'],
						'sms_global_name' => isset($param['sms_global_name']) ? trim($param['sms_global_name']) : $notice_setting['sms_global_name'],
						'sms_global_template' => isset($param['sms_global_template']) ? trim($param['sms_global_template']) : $notice_setting['sms_global_template'],
						'sms_name' => isset($param['sms_name']) ? trim($param['sms_name']) : $notice_setting['sms_name'],
						'sms_template' => isset($param['sms_template']) ? trim($param['sms_template']) : $notice_setting['sms_template'],
						'sms_enable' => isset($param['sms_enable']) ? trim($param['sms_enable']) : $notice_setting['sms_enable'],
						'email_name' => isset($param['email_name']) ? trim($param['email_name']) : $notice_setting['email_name'],
						'email_template' => isset($param['email_template']) ? trim($param['email_template']) : $notice_setting['email_template'],
						'email_enable' => isset($param['email_enable']) ? trim($param['email_enable']) : $notice_setting['email_enable'],
					], ['name' => $param['name']]);
				}
				# 日志描述
				$notice_setting = $notice_setting->toArray();
				$new_param=[
					'sms_global_name'=>$param['sms_global_name'],
					'sms_global_template'=>$param['sms_global_template'],
					'sms_name'=>$param['sms_name'],
					'sms_template'=>$param['sms_template'],
					'sms_enable'=>$param['sms_enable'],
					'email_name'=>$param['email_name'],
					'email_template'=>$param['email_template'],
					'email_enable'=>$param['email_enable']
				];
				$desc = array_diff_assoc($new_param,$notice_setting);
				$name_lang = $notice_setting['name_lang'];
				foreach($desc as $k=>$v){				
					$lang = '"'.$name_lang.'-'.lang("send_notice_log_{$k}").'"';
					if($k=='sms_enable' || $k=='email_enable'){
						$lang = '"'.lang("send_notice_log_".str_replace('enable','name',$k)).'"';
						$lang_old = lang("configuration_log_switch_{$notice_setting[$k]}");
						$lang_new = lang("configuration_log_switch_{$v}");
					}else if(strpos($k,'name')>0){	
						if(strpos($k,'sms')!==false){
							$sms_list = array_column($sms['list'],"title","name");				
							$lang_old = (isset($notice_setting[$k]) && !empty($notice_setting[$k])) ? ((!empty($sms_list[$notice_setting[$k]])) ? $sms_list[$notice_setting[$k]] : "''") : "''";
							$lang_new = $sms_list[$v] ?? '';
						}else{
							$mail_list = array_column($mail['list'],"title","name");				
							$lang_old = (isset($notice_setting[$k]) && !empty($notice_setting[$k])) ? ((!empty($mail_list[$notice_setting[$k]])) ? $mail_list[$notice_setting[$k]] : "''") : "''";
							$lang_new = $mail_list[$v] ?? '';
						}
					}else if(strpos($k,'template')>0){
						$interface = str_replace('template','name',$k);
						if(strpos($k,'sms')!==false){
							if(!empty($notice_setting[$k])){	
								
								$old_sms_param = [
									'id'=>$notice_setting[$k],
									'name'=>$notice_setting[$interface],
								];
								$old_sms_template = $SmsTemplateModel->indexSmsTemplate($old_sms_param);
								$lang_old = $old_sms_template->title ?? '';

							}else{
								$lang_old = "''";
							}	
							if(empty($desc[$interface])){
							    $sms_param_name = $notice_setting[$interface];
							}else{
							    $sms_param_name = $desc[$interface];
							}	
							$sms_param = [
								'id'=>$v,
								'name'=>$sms_param_name,
							];
							$sms_template = $SmsTemplateModel->indexSmsTemplate($sms_param); 
							$lang_new = $sms_template->title ?? '';
						}else{
							$old_mail_template = $EmailTemplateModel->indexEmailTemplate($notice_setting[$k]);
							$lang_old = $old_mail_template->name ?? '';
							$mail_template = $EmailTemplateModel->indexEmailTemplate($v);
							$lang_new = $mail_template->name ?? '';
						}
					}else{				
						$lang_old = $notice_setting[$k];
						$lang_new = $v;		
					}
					
					$description[] = lang('admin_old_to_new',['{field}'=>$lang, '{old}'=>'"'.$lang_old.'"', '{new}'=>'"'.$lang_new.'"']);
				}
			}
			$description = implode(',', $description);
			//日志
            if($description) active_log(lang('admin_notice_send_log_update', ['{admin}'=>request()->admin_name, '{description}'=>$description]), 'admin', request()->admin_id);
            $this->commit();
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return ['status' => 400, 'msg' =>lang('save_fail').$e->getMessage()];
        }
        return ['status' => 200, 'msg' => lang('save_success')];
    }
	/**
	 * @title 创建动作
	 * @desc 创建动作
	 * @author xiong
	 * @version v1
	 * @param string param.name - 动作英文标识 required
	 * @param string param.name_lang  - 动作名称（在页面显示的名称） required
	 * @param string param.type other 通知分类(配置文件idcsmart.notice_setting_type中支持的,默认不传其他)
	 * @param string param.sms_name  - 短信接口标识名（可以为空，默认智简魔方短信接口）
	 * @param string param.sms_template[].title  - 短信模板标题 required
	 * @param string param.sms_template[].content  - 短信模板内容 required
	 * @param string param.sms_global_name  - 国际短信接口标识名（可以为空，默认智简魔方短信接口）
	 * @param string param.sms_global_template[].title  - 国际短信模板标题 required
	 * @param string param.sms_global_template[].content  - 国际短信模板内容 required
	 * @param string param.email_name  - 邮件接口名称（可以为空，默认SMTP接口）  
	 * @param string param.email_template[].name  - 邮件模板名称 required
	 * @param string param.email_template[].title  - 邮件模板标题 required
	 * @param string param.email_template[].content  - 邮件模板内容 required
	 * @return mixed
	 */
	function noticeActionCreate($param)
	{	
		$notice_setting = $this->field('name,sms_global_name,sms_global_template,sms_name,sms_template,sms_enable,email_name,email_template,email_enable')
            ->where('name',$param['name']??'')
            ->find();

		if (!empty($notice_setting)){
            return ['status'=>400, 'msg'=>lang('添加失败，动作已经存在')];
        }else if (!empty($patam['name_lang'])){
            return ['status'=>400, 'msg'=>lang('动作名称不能为空')];
        }
		if(empty($param['type'])){
			$param['type'] = 'other';
		}else{
			if(!in_array($param['type'],config('idcsmart.notice_setting_type'))){
				return ['status'=>400, 'msg'=>lang('添加失败，动作类型错误')];
			}
		}

		$this->startTrans();
        try {
			$EmailTemplateModel = new EmailTemplateModel();
			$SmsTemplateModel = new SmsTemplateModel();
			$sms_name = '';
			if(!empty($param['sms_name']) || !empty($param['sms_template'])){			
				$sms_name = $param['sms_name'] ? trim($param['sms_name']) : 'Idcsmart';
				//创建国内短信模板
				$sms = [
					'name'=>$sms_name,
					'type'=>0,
					'title'=>$param['sms_template']['title'],
					'content'=>$param['sms_template']['content'],
					'notes'=>'',
					'status'=>0,
				];
				$sms_template = $SmsTemplateModel->createSmsTemplate($sms);	
			}	
			$sms_global_name = '';
			if(!empty($param['sms_global_name']) || !empty($param['sms_global_template'])){	
				$sms_global_name = $param['sms_global_name'] ? trim($param['sms_global_name']) : 'Idcsmart';
				//创建国际短信模板
				$sms1=[
					'name'=>$sms_global_name,
					'type'=>1,
					'title'=>$param['sms_global_template']['title'],
					'content'=>$param['sms_global_template']['content'],
					'notes'=>'',
					'status'=>0,
				];
				$sms_global_template = $SmsTemplateModel->createSmsTemplate($sms1);
			}
			$email_name = '';
			if(!empty($param['email_name'])){
				$email_name = $param['email_name'] ? trim($param['email_name']) : 'Smtp';
				//添加邮件模板
				$email=[
					'name'=>$param['email_template']['name'],
					'subject'=>$param['email_template']['title'],
					'message'=>$param['email_template']['content'],
				];
				$email_template=$EmailTemplateModel->createEmailTemplate($email);		
			}
			
			$create_notice_setting = [
				'name' => $param['name'],
				'name_lang' => $param['name_lang'],
				'sms_global_name' => $sms_global_name,
				'sms_global_template' => !empty($sms_global_template['id'])?$sms_global_template['id']:0,
				'sms_name' => $sms_name,
				'sms_template' => !empty($sms_template['id'])?$sms_template['id']:0,
				'sms_enable' => 1,
				'email_name' => $email_name,
				'email_template' => !empty($email_template['id'])?$email_template['id']:0,
				'email_enable' => 1,
				'type' => $param['type'],
			];
			$this->create($create_notice_setting);
			$this->commit();
		} catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return ['status' => 400, 'msg' =>lang('save_fail') .$e->getMessage()  ];
        }
        return ['status' => 200, 'msg' => lang('save_success')];
	}
    /**
     * @title 删除动作
     * @desc 删除动作
     * @author xiong
     * @version v1
	 * @param string param.name - 动作英文标识 required
     */
    public function noticeActionDelete($name)
    {
    	$notice_setting = $this->find($name);
    	if (empty($notice_setting)){
            return ['status'=>400, 'msg'=>lang('动作删除失败，找不到动作')];
        }
    	$this->startTrans();
		try {
            # 记录日志
            //active_log(lang('admin_delete_email_template', ['{admin}'=>request()->admin_name, '{template}'=>'#'.$emailTemplate->id]), 'email_template', $emailTemplate->id);
            $EmailTemplateModel=new EmailTemplateModel();
			$SmsTemplateModel=new SmsTemplateModel();
			$SmsTemplateModel->deleteSmsTemplate($notice_setting['sms_global_template']);	
			$SmsTemplateModel->deleteSmsTemplate($notice_setting['sms_template']);	
			$EmailTemplateModel->deleteEmailTemplate($notice_setting['email_template']);	
			
			$this->destroy($name);
		    $this->commit();
		} catch (\Exception $e) {
		    // 回滚事务
		    $this->rollback();
		    return ['status' => 400, 'msg' => lang('delete_fail')];
		}

		hook('after_notice_action_delete',['name'=>$name]);

    	return ['status' => 200, 'msg' => lang('delete_success')];
    }

    /**
     * @时间 2025-03-07
     * @title 获取通知分类
     * @desc  获取通知分类
     * @author hh
     * @version v1
     * @return  string type - 通知类型标识 require
     * @return  string
     */
    public function getNoticeSettingType(): array
    {
        $exist = $this
                ->field('id,type')
                ->where('id', '>', 0)
                ->group('type')
                ->select()
                ->toArray();
        $exist = array_column($exist, 'id', 'type');

        $type = config('idcsmart.notice_setting_type');
        $data = [];
        foreach($type as $v){
            if(!isset($exist[$v])){
                continue;
            }
            $data[] = [
                'name'      => $v,
                'name_lang' => lang('notice_setting_type_'.$v),
            ];
        }
        return $data;
    }

	/**
     * @时间 2025-03-07
     * @title 获取通知动作名称
     * @desc  获取通知动作名称
     * @author hh
     * @version v1
     * @return  string name - 通知标识 require
     * @return  string
     */
	public function getNoticeSettingName($name)
	{
		$name_lang = idcsmart_cache('NOTICE_SETTING_NAME_' . $name);
		if(empty($name_lang)){
			$name_lang = NoticeSettingModel::where('name', $name)->value('name_lang');
			if(empty($name_lang)){
				$name_lang = lang('notice_action_'.$name);
			}
			idcsmart_cache('NOTICE_SETTING_NAME_' . $name, $name_lang, 60);
		}
		return $name_lang;
	}

}
