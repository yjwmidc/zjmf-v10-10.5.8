<?php
namespace addon\idcsmart_withdraw;

use app\common\lib\Plugin;
use think\facade\Db;
use addon\idcsmart_withdraw\model\IdcsmartWithdrawModel;
use addon\idcsmart_withdraw\validate\IdcsmartWithdrawValidate;
use addon\idcsmart_withdraw\logic\IdcsmartWithdrawLogic;

/*
 * 提现插件
 * @author theworld
 * @time 2022-06-08
 * @copyright Copyright (c) 2013-2021 https://www.idcsmart.com All rights reserved.
 */
class IdcsmartWithdraw extends Plugin
{
	# 插件基本信息
	public $info = array(
		'name'        => 'IdcsmartWithdraw', //插件英文名,作为插件唯一标识,改成你的插件英文就行了
		'title'       => '提现',
		'description' => '提现',
		'author'      => '智简魔方',  //开发者
		'version'     => '2.3.1',      // 版本号
	);
	# 插件安装
	public function install()
	{
		$sql = [
			"DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_withdraw`",
			"CREATE TABLE `idcsmart_addon_idcsmart_withdraw` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '提现ID',
  `source` varchar(100) NOT NULL DEFAULT '' COMMENT '提现来源',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `method` varchar(100) NOT NULL DEFAULT 'bank' COMMENT '提现方式bank银行卡alipay',
  `addon_idcsmart_withdraw_method_id` int(11) NOT NULL DEFAULT '0' COMMENT '提现方式ID',
  `card_number` varchar(100) NOT NULL DEFAULT '' COMMENT '银行卡号',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '姓名',
  `account` varchar(100) NOT NULL DEFAULT '' COMMENT '账号',
  `notes` varchar(1000) NOT NULL DEFAULT '' COMMENT '备注',
  `client_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态0待审核1审核通过2审核驳回3确认已汇款',
  `reason` varchar(1000) NOT NULL DEFAULT '' COMMENT '驳回原因',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '手续费',
  `transaction_id` int(11) NOT NULL DEFAULT '0' COMMENT '交易流水ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `admin_id` (`admin_id`),
  KEY `transaction_id` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='提现表'",
			"DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_withdraw_rule`",
			"CREATE TABLE `idcsmart_addon_idcsmart_withdraw_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '提现规则ID',
  `source` varchar(100) NOT NULL DEFAULT 'credit' COMMENT '提现来源',
  `method` text NOT NULL COMMENT '提现方式ID',
  `process` varchar(20) NOT NULL DEFAULT 'artificial' COMMENT '提现流程artificial人工auto自动',
  `min` varchar(20) NOT NULL DEFAULT '' COMMENT '最小金额限制',
  `max` varchar(20) NOT NULL DEFAULT '' COMMENT '最大金额限制',
  `cycle` varchar(20) NOT NULL DEFAULT 'day' COMMENT '提现周期day每天week每周month每月',
  `cycle_limit` varchar(20) NOT NULL DEFAULT '' COMMENT '提现周期次数限制,空不限',
  `withdraw_fee_type` varchar(20) NOT NULL DEFAULT 'fixed' COMMENT '手续费类型fixed固定percent百分比',
  `withdraw_fee` varchar(20) NOT NULL DEFAULT '' COMMENT '固定手续费金额',
  `percent` varchar(20) NOT NULL DEFAULT '' COMMENT '手续费百分比',
  `percent_min` varchar(20) NOT NULL DEFAULT '' COMMENT '最低手续费',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态0关闭1开启',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='提现规则表'",
			"DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_withdraw_method`",
			"CREATE TABLE `idcsmart_addon_idcsmart_withdraw_method` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '提现方式ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='提现方式表'",
			"DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_withdraw_reject_reason`",
			"CREATE TABLE `idcsmart_addon_idcsmart_withdraw_reject_reason` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '驳回原因ID',
  `reason` varchar(1000) NOT NULL DEFAULT '' COMMENT '驳回原因',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='驳回原因表'",
		];
		foreach ($sql as $v){
			Db::execute($v);
		}

		# 插入邮件短信模板
		$templates = IdcsmartWithdrawLogic::getDefaultConfig('idcsmart_withdraw_notice_template');
		foreach ($templates as $key=>$template){
			$template['name'] = $key;
			notice_action_create($template);
		}

		# 安装成功返回true，失败false
		return true;
	}
	# 插件卸载
	public function uninstall()
	{
		$sql = [
			"DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_withdraw`",
			"DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_withdraw_rule`",
			"DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_withdraw_method`",
			"DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_withdraw_reject_reason`",
		];
		foreach ($sql as $v){
			Db::execute($v);
		}

		# 删除插入的邮件短信模板
		$templates = IdcsmartWithdrawLogic::getDefaultConfig('idcsmart_withdraw_notice_template');
		foreach ($templates as $key=>$template){
			notice_action_delete($key);
		}
		
		return true;
	}

	/**
	 * 时间 2022-07-26
	 * @title 申请提现
	 * @desc 申请提现
	 * @author theworld
	 * @version v1
	 * @param string param.source - 提现来源 required
	 * @param int param.method_id - 提现方式ID required
	 * @param float param.amount - 提现金额 required
	 * @param string param.card_number - 银行卡号 
	 * @param string param.name - 姓名
	 * @param string param.account - 账号
	 * @param string param.notes - 备注
	 * @param float param.fee - 提现手续费,非余额提现时使用
	 * @return int status - 状态码,200成功,400失败
	 * @return string msg - 提示信息
	 */
	public function clientWithdraw($param)
	{
		$IdcsmartWithdrawValidate = new IdcsmartWithdrawValidate();
		
		// 参数验证
		if (!$IdcsmartWithdrawValidate->scene('withdraw')->check($param)){
			return ['status' => 400 , 'msg' => lang_plugins($this->validate->getError())];
		}

		$IdcsmartWithdrawModel = new IdcsmartWithdrawModel();

		return $IdcsmartWithdrawModel->idcsmartWithdraw($param);
	}

	# 插件升级
	public function upgrade()
	{
		$name = $this->info['name'];
		$version = $this->info['version'];
		$PluginModel = new \app\admin\model\PluginModel();
		$plugin = $PluginModel->where('name', $name)->find();
		$sql = [];
		if(isset($plugin['version'])){
			if(version_compare('1.0.1', $plugin['version'], '>')){
				$sql[] = "ALTER TABLE `idcsmart_addon_idcsmart_withdraw_rule` ADD COLUMN `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态0关闭1开启';";
			}
			if(version_compare('2.1.0', $plugin['version'], '>')){
				# 插入邮件短信模板
				$templates = IdcsmartWithdrawLogic::getDefaultConfig('idcsmart_withdraw_notice_template');
				foreach ($templates as $key=>$template){
					$template['name'] = $key;
					notice_action_create($template);
				}
			}
			if(version_compare('2.3.1', $plugin['version'], '>')){
				$sql[] = "INSERT INTO `idcsmart_plugin_hook`(`name`,`status`,`plugin`,`module`,`order`) VALUES ('get_payment_channels',1,'IdcsmartWithdraw','addon',0);";
				$sql[] = "INSERT INTO `idcsmart_plugin_hook`(`name`,`status`,`plugin`,`module`,`order`) VALUES ('search_payment_channels',1,'IdcsmartWithdraw','addon',0);";
			}
		}
		foreach ($sql as $v){
			Db::execute($v);
		}
		return true;
	}

	/**
	 * 时间 2025-01-21
	 * @title 获取支付渠道
	 * @desc  获取支付渠道,返回例如['1'=>['payment_channel'=>'支付宝']]
	 * @author theworld
	 * @version v1
	 * @param   array param.transaction_id - 交易流水ID require
	 * @return  string [transaction_id].payment_channel - 支付渠道
	 */
	public function getPaymentChannels($param)
	{
		$transactionId = $param['transaction_id'] ?? [];
		$paymentChannels = [];
		if(!empty($transactionId)){
			$withdraw = IdcsmartWithdrawModel::alias('a')
				->field('a.transaction_id,b.name')
				->leftjoin('idcsmart_addon_idcsmart_withdraw_method b', 'b.id=a.addon_idcsmart_withdraw_method_id')
				->whereIn('a.transaction_id', $transactionId)
				->select()
				->toArray();
			foreach($withdraw as $v){
				$paymentChannels[ $v['transaction_id'] ] = [
					'payment_channel' => $v['name'],
				];
			}
		}
		return $paymentChannels;
	}

	/**
	 * 时间 2025-01-21
	 * @title 搜索支付渠道
	 * @desc  搜索支付渠道
	 * @author theworld
	 * @version v1
	 * @param   array param.payment_channel - 支付渠道 require
	 * @return  array
	 */
	public function searchPaymentChannels($param)
	{
		$param['payment_channel'] = $param['payment_channel'] ?? '';
		if(!empty($param['payment_channel'])){
			$transactionId = IdcsmartWithdrawModel::alias('a')
				->leftjoin('idcsmart_addon_idcsmart_withdraw_method b', 'b.id=a.addon_idcsmart_withdraw_method_id')
				->where('b.name', 'like', $param['payment_channel'])
				->column('a.transaction_id');
		}
		return ['transaction_id' => $transactionId ?? []];
	}

}