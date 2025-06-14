insert  into `idcsmart_plugin_hook`(`name`,`status`,`plugin`,`module`,`order`) values ('before_search_client',1,'IdcsmartCertification','addon',0);
insert  into `idcsmart_plugin_hook`(`name`,`status`,`plugin`,`module`,`order`) values ('before_search_client',1,'ClientCustomField','addon',0);
UPDATE `idcsmart_nav` SET `url` = REPLACE(`url`,'.html','.php');
UPDATE `idcsmart_menu` SET `url` = REPLACE(`url`,'.html','.php') WHERE `menu_type`!='custom';
UPDATE `idcsmart_auth` SET `url` = REPLACE(`url`,'.html','.php');
UPDATE `idcsmart_clientarea_auth` SET `url` = REPLACE(`url`,'.html','.php');
UPDATE `idcsmart_configuration` SET `value` = REPLACE(`value`,'agreement.html','agreement.php');
insert  into `idcsmart_auth`(`id`,`title`,`url`,`order`,`parent_id`,`module`,`plugin`) values (99,'auth_index','',-1,0,'','');
insert  into `idcsmart_auth`(`id`,`title`,`url`,`order`,`parent_id`,`module`,`plugin`) values (100,'auth_index_pendant','',0,0,'','');
insert  into `idcsmart_auth`(`id`,`title`,`url`,`order`,`parent_id`,`module`,`plugin`) values (101,'auth_index_base_info','',101,100,'','');
insert  into `idcsmart_auth`(`id`,`title`,`url`,`order`,`parent_id`,`module`,`plugin`) values (102,'auth_index_this_year_sale','',102,100,'','');
insert  into `idcsmart_auth`(`id`,`title`,`url`,`order`,`parent_id`,`module`,`plugin`) values (103,'auth_index_this_year_client','',103,100,'','');
insert  into `idcsmart_auth`(`id`,`title`,`url`,`order`,`parent_id`,`module`,`plugin`) values (104,'auth_index_visit_client','',104,100,'','');
insert  into `idcsmart_auth`(`id`,`title`,`url`,`order`,`parent_id`,`module`,`plugin`) values (105,'auth_index_online_admin','',105,100,'','');
insert  into `idcsmart_auth_link`(`auth_id`,`admin_role_id`) values (99,1);
insert  into `idcsmart_auth_link`(`auth_id`,`admin_role_id`) values (100,1);
insert  into `idcsmart_auth_link`(`auth_id`,`admin_role_id`) values (101,1);
insert  into `idcsmart_auth_link`(`auth_id`,`admin_role_id`) values (102,1);
insert  into `idcsmart_auth_link`(`auth_id`,`admin_role_id`) values (103,1);
insert  into `idcsmart_auth_link`(`auth_id`,`admin_role_id`) values (104,1);
insert  into `idcsmart_auth_link`(`auth_id`,`admin_role_id`) values (105,1);
insert  into `idcsmart_auth_rule`(`id`,`name`,`title`,`module`,`plugin`) values (109,'app\\admin\\controller\\IndexController::index','auth_rule_index_base_info','','');
insert  into `idcsmart_auth_rule`(`id`,`name`,`title`,`module`,`plugin`) values (110,'app\\admin\\controller\\IndexController::thisYearSale','auth_rule_index_this_year_sale','','');
insert  into `idcsmart_auth_rule`(`id`,`name`,`title`,`module`,`plugin`) values (111,'app\\admin\\controller\\IndexController::thisYearClient','auth_rule_index_this_year_client','','');
insert  into `idcsmart_auth_rule`(`id`,`name`,`title`,`module`,`plugin`) values (112,'app\\admin\\controller\\IndexController::visitClient','auth_rule_index_visit_client','','');
insert  into `idcsmart_auth_rule`(`id`,`name`,`title`,`module`,`plugin`) values (113,'app\\admin\\controller\\IndexController::onlineAdmin','auth_rule_index_online_admin','','');
insert  into `idcsmart_auth_rule_link`(`auth_rule_id`,`auth_id`) values (109,101);
insert  into `idcsmart_auth_rule_link`(`auth_rule_id`,`auth_id`) values (110,102);
insert  into `idcsmart_auth_rule_link`(`auth_rule_id`,`auth_id`) values (111,103);
insert  into `idcsmart_auth_rule_link`(`auth_rule_id`,`auth_id`) values (112,104);
insert  into `idcsmart_auth_rule_link`(`auth_rule_id`,`auth_id`) values (113,105);
insert  into `idcsmart_configuration`(`setting`,`value`,`create_time`,`update_time`,`description`) values ('web_theme','default',0,0,'官网主题');
UPDATE `idcsmart_email_template` SET `message` = REPLACE(`message`,'{product_info}','{product_name}');