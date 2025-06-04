<?php
namespace addon\idcsmart_help;

use app\common\lib\Plugin;
use think\facade\Db;

require_once __DIR__ . '/common.php';
/*
 * 帮助中心
 * @author theworld
 * @time 2022-06-08
 * @copyright Copyright (c) 2013-2021 https://www.idcsmart.com All rights reserved.
 */
class IdcsmartHelp extends Plugin
{
    # 插件基本信息
    public $info = array(
        'name'        => 'IdcsmartHelp', //插件英文名,作为插件唯一标识,改成你的插件英文就行了
        'title'       => '帮助中心',
        'description' => '帮助中心',
        'author'      => '智简魔方',  //开发者
        'version'     => '2.3.0',      // 版本号
    );
    # 插件安装
    public function install()
    {
        $sql = [
            "DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_help`",
            "CREATE TABLE `idcsmart_addon_idcsmart_help` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '帮助文档ID',
  `addon_idcsmart_help_type_id` int(11) NOT NULL DEFAULT '0' COMMENT '帮助文档分类ID',
  `title` varchar(200) NOT NULL DEFAULT '' COMMENT '标题',
  `content` longtext NOT NULL COMMENT '内容',
  `keywords` varchar(200) NOT NULL DEFAULT '' COMMENT '关键字',
  `attachment` text NOT NULL COMMENT '附件',
  `hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:显示1:隐藏',
  `index_hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '首页管理显示隐藏0:显示1:隐藏',
  `read` int(11) NOT NULL DEFAULT '0' COMMENT '阅读量',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '最后操作人',
  `cron_release` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否定时发布',
  `cron_release_time` int(11) NOT NULL DEFAULT '0' COMMENT '定时发布时间',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `addon_idcsmart_help_type_id` (`addon_idcsmart_help_type_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='帮助文档表'",
            "DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_help_type`",
            "CREATE TABLE `idcsmart_addon_idcsmart_help_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '帮助文档分类ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `index_section` int(11) NOT NULL DEFAULT '0' COMMENT '首页版块',
  `index_hot_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '首页是否根据热度显示文档0:否1:是',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '最后操作人',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='帮助文档分类表'",
        ];
        foreach ($sql as $v){
            Db::execute($v);
        }
        # 安装成功返回true，失败false
        return true;
    }
    # 插件卸载
    public function uninstall()
    {
        $sql = [
            "DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_help`",
            "DROP TABLE IF EXISTS `idcsmart_addon_idcsmart_help_type`",
        ];
        foreach ($sql as $v){
            Db::execute($v);
        }
        return true;
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
                $sql[] = "ALTER TABLE `idcsmart_addon_idcsmart_help` ADD COLUMN `cron_release` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否定时发布';";
                $sql[] = "ALTER TABLE `idcsmart_addon_idcsmart_help` ADD COLUMN `cron_release_time` int(11) NOT NULL DEFAULT '0' COMMENT '定时发布时间';";
            }
        }
        foreach ($sql as $v){
            Db::execute($v);
        }
        return true;
    }

}