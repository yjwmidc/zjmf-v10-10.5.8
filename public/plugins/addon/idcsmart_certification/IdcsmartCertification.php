<?php
namespace addon\idcsmart_certification;

use addon\idcsmart_certification\model\CertificationCompanyModel;
use addon\idcsmart_certification\model\CertificationLogModel;
use addon\idcsmart_certification\model\CertificationPersonModel;
use app\admin\model\PluginModel;
use app\common\lib\Plugin;
use think\facade\Db;
use addon\idcsmart_certification\logic\IdcsmartCertificationLogic;

/*
 * 智简魔方实名认证插件
 * @author wyh
 * @time 2022-10-08
 * @copyright Copyright (c) 2013-2021 https://www.idcsmart.com All rights reserved.
 */
class IdcsmartCertification extends Plugin
{
    # 插件基本信息
    public $info = array(
        'name'        => 'IdcsmartCertification', //插件英文名,作为插件唯一标识,改成你的插件英文就行了
        'title'       => '实名认证',
        'description' => '实名认证',
        'author'      => '智简魔方',  //开发者
        'version'     => '2.3.1',      // 版本号
    );

    # 插件安装
    public function install()
    {
        $sql = [
            "DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_certification_log`",
            "CREATE TABLE `idcsmart_addon_idcsmart_certification_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL DEFAULT '0' COMMENT '客户ID',
  `card_name` varchar(255) NOT NULL DEFAULT '' COMMENT '认证名称',
  `card_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '证件类型:1身份证、2港澳通行证、3台湾通行证、4港澳居住证、5台湾居住证、6海外护照、7中国以外驾照、8其他',
  `card_number` varchar(255) DEFAULT '' COMMENT '证件号',
  `phone` varchar(255) NOT NULL DEFAULT '' COMMENT '手机号',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '1已认证，2未通过，3待审核，4已提交资料',
  `company` varchar(255) DEFAULT '' COMMENT '公司名称',
  `company_organ_code` varchar(255) DEFAULT '' COMMENT '营业执照号',
  `certify_id` varchar(255) NOT NULL DEFAULT '' COMMENT '认证证书',
  `auth_fail` varchar(255) DEFAULT '' COMMENT '失败原因',
  `img` text COMMENT '图片集合用逗号分割',
  `create_time` int(11) DEFAULT NULL COMMENT '提交时间',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '认证类型1个人，2企业，3个人转企业',
  `plugin_name` varchar(255) NOT NULL DEFAULT '' COMMENT '认证方式:支付插件标识',
  `custom_fields_json` text COMMENT '自定义字段json格式',
  `notes` text COMMENT '备注',
  `refresh` tinyint(1) NOT NULL DEFAULT '1' COMMENT '此字段解决一些插件如支付宝实名认证时，验证页面除了通过，即status=1时才刷新页面；其他的都不刷新验证页面。默认刷新1，0不刷新',
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='实名认证记录'",
            "DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_certification_company`",
            "CREATE TABLE `idcsmart_addon_idcsmart_certification_company` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL DEFAULT '0' COMMENT '客户ID',
  `card_name` varchar(255) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `card_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '证件类型:1身份证、2港澳通行证、3台湾通行证、4港澳居住证、5台湾居住证、6海外护照、7中国以外驾照、8其他',
  `card_number` varchar(255) NOT NULL DEFAULT '' COMMENT '证件号',
  `phone` varchar(255) NOT NULL DEFAULT '' COMMENT '手机号',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1已认证，2未通过，3待审核，4已提交资料',
  `company` varchar(255) NOT NULL DEFAULT '' COMMENT '公司名称',
  `company_organ_code` varchar(255) NOT NULL DEFAULT '' COMMENT '公司代码',
  `img_one` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证正面',
  `img_two` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证反面',
  `img_three` varchar(255) NOT NULL DEFAULT '' COMMENT '营业执照',
  `certify_id` varchar(255) NOT NULL DEFAULT '' COMMENT '认证证书',
  `auth_fail` varchar(255) NOT NULL DEFAULT '' COMMENT '失败原因',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '''',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '''',
  `custom_fields1` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段1-10',
  `custom_fields2` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `custom_fields3` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `custom_fields4` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `custom_fields5` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `custom_fields6` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `custom_fields7` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `custom_fields8` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `custom_fields9` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `custom_fields10` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='企业认证'",
            "DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_certification_person`",
            "CREATE TABLE `idcsmart_addon_idcsmart_certification_person` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL DEFAULT '0' COMMENT '客户ID',
  `card_name` varchar(255) NOT NULL DEFAULT '' COMMENT '认证卡姓名',
  `card_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '证件类型:1身份证、2港澳通行证、3台湾通行证、4港澳居住证、5台湾居住证、6海外护照、7中国以外驾照、8其他',
  `card_number` varchar(50) NOT NULL DEFAULT '' COMMENT '证件号',
  `phone` varchar(255) NOT NULL DEFAULT '' COMMENT '手机号',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1已认证，2未通过，3待审核，4已提交资料',
  `img_one` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证正面',
  `img_two` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证反面',
  `img_three` varchar(255) NOT NULL DEFAULT '' COMMENT '营业执照',
  `certify_id` varchar(255) NOT NULL DEFAULT '' COMMENT '认证证书',
  `auth_fail` varchar(255) NOT NULL DEFAULT '' COMMENT '失败原因',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `custom_fields1` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段1-10',
  `custom_fields2` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `custom_fields3` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `custom_fields4` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `custom_fields5` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `custom_fields6` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `custom_fields7` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `custom_fields8` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `custom_fields9` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `custom_fields10` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='个人认证'",
        ];
        foreach ($sql as $v){
            Db::execute($v);
        }

        # 插入邮件短信模板
        $templates = IdcsmartCertificationLogic::getDefaultConfig('certification_notice_template');
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
            "DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_certification_log`",
            "DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_certification_company`",
            "DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_certification_person`",
        ];
        foreach ($sql as $v){
            Db::execute($v);
        }
        # 删除插入的邮件短信模板
        $templates = IdcsmartCertificationLogic::getDefaultConfig('certification_notice_template');
        foreach ($templates as $key=>$template){
            notice_action_delete($key);
        }
        return true;
    }

    public function checkCertification($param)
    {
        $CertificationLogModel = new CertificationLogModel();

        return $CertificationLogModel->checkCertification($param['client_id']??0);
    }

    public function checkCertificationRecharge(){
        $PluginModel = new PluginModel();
        $plugin = $PluginModel->where('name','IdcsmartCertification')
            ->where('module','addon')
            ->find();
        if (!empty($plugin)){
            $config = json_decode($plugin['config'],true);
            if (isset($config['certification_recharge']) && $config['certification_recharge']){
                return true;
            }
        }
        return false;
    }

    public function updateCertificationPerson($param)
    {
        $CertificationLogModel = new CertificationLogModel();

        return $CertificationLogModel->updateCertificationPerson($param);
    }

    public function updateCertificationCompany($param)
    {
        $CertificationLogModel = new CertificationLogModel();

        return $CertificationLogModel->updateCertificationCompany($param);
    }

    # 获取已实名认证客户及认证类型(个人/企业)
    public function getCertificationList()
    {
        $CertificationPersonModel = new CertificationPersonModel();
        $person = $CertificationPersonModel->where('status',1)
            ->select()
            ->toArray();

        $re1 = [];

        foreach ($person as $item1){
            $re1[$item1['client_id']] = 'person';
        }

        $CertificationCompanyModel = new CertificationCompanyModel();
        $company = $CertificationCompanyModel->where('status',1)
            ->select()
            ->toArray();
        foreach ($company as $item2){
            $re1[$item2['client_id']] = 'company';
        }

        return $re1;
    }
    
    public function beforeOrderCreate($param)
    {
        $config = IdcsmartCertificationLogic::getDefaultConfig();

        if(isset($config['certification_uncertified_cannot_buy_product']) && $config['certification_uncertified_cannot_buy_product']==1){
            $CertificationLogModel = new CertificationLogModel();
            $clientId = get_client_id();
            if(!empty($clientId)){
                $res = $CertificationLogModel->checkCertification($clientId);

                if(!$res){
                    return ['status' => 400, 'msg' => lang_plugins('certification_uncertified_cannot_buy_product')];
                }
            }
            
        }
        return ['status' => 200];
        
    }

    public function certificationDetail($param)
    {
        $CertificationLogModel = new CertificationLogModel();

        return $CertificationLogModel->certificationDetail($param['client_id']??0);
    }

    public function beforeSearchClient($param)
    {
        $keywords = $param['keywords'] ?? '';
        $clientId = [];
        if(!empty($keywords)){
            $CertificationPersonModel = new CertificationPersonModel();
            $client = $CertificationPersonModel->where('card_name', 'like', "%{$keywords}%")->column('client_id');
            $clientId = array_merge($clientId, $client);
            $CertificationCompanyModel = new CertificationCompanyModel();
            $client = $CertificationCompanyModel->where('card_name|company', 'like', "%{$keywords}%")->column('client_id');
            $clientId = array_merge($clientId, $client);
            $clientId = array_unique($clientId);
        }
        return ['client_id' => $clientId];
    }

    // 升级
    public function upgrade()
    {
        $IdcsmartCertificationLogic = new IdcsmartCertificationLogic();
        $config = $IdcsmartCertificationLogic->getConfig();
        $config['certification_age'] = 18; // 默认18
        $PluginModel = new PluginModel();
        $name = $this->info['name'];
        $PluginModel = new \app\admin\model\PluginModel();
        $plugin = $PluginModel->where('name', $name)->find();
        $sql = [];
        if(isset($plugin['version'])){
            if(version_compare('1.0.5', $plugin['version'], '>')){
                $PluginModel->where('name','IdcsmartCertification')
                    ->where('module','addon')
                    ->update([
                        'config' => json_encode($config),
                        'update_time' => time()
                    ]);
            }
            if(version_compare('1.0.6', $plugin['version'], '>')){
                $sql = [
                    "insert  into `idcsmart_plugin_hook`(`name`,`status`,`plugin`,`module`,`order`) values ('check_certification_recharge',1,'IdcsmartCertification','addon',0);"
                ];
            }
            if(version_compare('2.3.1', $plugin['version'], '>')){
                notice_action_create([
                    'name' => 'idcsmart_certification_reject',
                    'name_lang' => '实名认证失败',
                    'sms_name' => 'Idcsmart',
                    'sms_template' => [
                        'title' => '实名认证失败',
                        'content' => '尊敬的用户，您好！很抱歉，您的实名认证未能通过，请核对提交的信息并重新认证。@var(system_website_name)。',
                    ],
                    'sms_global_name' => 'Idcsmart',
                    'sms_global_template' => [
                        'title' => '实名认证失败',
                        'content' => '尊敬的用户，您好！很抱歉，您的实名认证未能通过，请核对提交的信息并重新认证。@var(system_website_name)。',
                    ],
                    'email_name' => 'Smtp',
                    'email_template' => [
                        'name' => '实名认证失败',
                        'title' => '[{system_website_name}] 实名认证失败',
                        'content' => '<!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Document</title>
                <style>
                    li {
                        list-style: none;
                    }

                    a {
                        text-decoration: none;
                    }

                    body {
                        margin: 0;
                    }

                    .box {
                        background-color: #EBEBEB;
                        height: 100%;
                    }

                    .logo_top {
                        padding: 20px 0;
                    }

                    .logo_top img {
                        display: block;
                        width: auto;
                        margin: 0 auto;
                    }

                    .card {
                        width: 650px;
                        margin: 0 auto;
                        background-color: white;
                        font-size: 0.8rem;
                        line-height: 22px;
                        padding: 40px 50px;
                        box-sizing: border-box;
                    }

                    .contimg {
                        text-align: center;
                    }

                    button {
                        background-color: #F75697;
                        padding: 8px 16px;
                        border-radius: 6px;
                        outline: none;
                        color: white;
                        border: 0;
                    }

                    .lvst {
                        color: #57AC80;
                    }

                    .banquan {
                        display: flex;
                        justify-content: center;
                        flex-wrap: nowrap;
                        color: #B7B8B9;
                        font-size: 0.4rem;
                        padding: 20px 0;
                        margin: 0;
                        padding-left: 0;
                    }

                    .banquan li span {
                        display: inline-block;
                        padding: 0 8px;
                    }

                    @media (max-width: 650px) {
                        .card {
                            padding: 5% 5%;
                        }

                        .logo_top img,
                        .contimg img {
                            width: 280px;
                        }

                        .box {
                            height: auto;
                        }

                        .card {
                            width: auto;
                        }
                    }

                    @media (max-width: 280px) {
                        .logo_top img,
                        .contimg img {
                            width: 100%;
                        }
                    }
                </style>
            </head>

            <body>
            <div class="box">
            <div class="logo_top"><img src="{system_logo_url}" alt="" /></div>
            <div class="card">
            <h2 style="text-align: center;">[{system_website_name}] 实名认证失败通知</h2>
            <br /><strong>尊敬的用户，您好！</strong> <br /><span style="margin: 0; padding: 0; display: inline-block; margin-top: 20px;"> 很抱歉，您的实名认证未能通过，请核对提交的信息并重新认证。 </span> <br /><span style="margin: 0; padding: 0; display: inline-block; margin-top: 60px;"> 感谢您的理解与支持！ </span> <br /><span style="margin: 0; padding: 0; display: inline-block; width: 100%; text-align: right;"> <strong>{system_website_name}</strong> </span> <br /><span style="margin: 0; padding: 0; margin-top: 20px; display: inline-block; width: 100%; text-align: right;"> {send_time} </span></div>
            <ul class="banquan">
            <li>{system_website_name}</li>
            </ul>
            </div>
            </body>

            </html>',
                    ],
                ]);
            }
        }
        foreach ($sql as $v){
            Db::execute($v);
        }
        return true;
        return true;
    }

}