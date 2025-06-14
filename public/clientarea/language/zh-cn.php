<?php

return [
    # 通用
	'display_name' => '中文简体',//用于在语言切换下拉中显示
	'display_flag' => 'CN',//用于显示图片，使用国家代码大写
    'add_success' => '添加成功',
    'add_fail' => '添加失败',
    'success_message' => '请求成功',
    'fail_message' => '请求失败',
    'repeat_message' => '重复请求',
    'create_success' => '创建成功',
    'create_fail' => '创建失败',
    'delete_success' => '删除成功',
    'delete_fail' => '删除失败',
    'update_success' => '修改成功',
    'update_fail' => '修改失败',
    'save_success' => '保存成功',
    'save_fail' => '保存失败',
    'register_success' => '注册成功',
    'register_fail' => '注册失败',
    'pay_success' => '支付成功',
    'pay_fail' => '支付失败',
    'id_error' => 'ID错误',
    'param_error' => '参数错误',
    'cannot_repeat_opreate' => '不可重复操作',
    'disable_success' => '禁用成功',
    'disable_fail' => '禁用失败',
    'enable_success' => '启用成功',
    'enable_fail' => '启用失败',
    'login_success' => '登录成功',
    'login_fail' => '登录失败',
    'move_success' => '移动成功',
    'move_fail' => '移动失败',
    'buy_fail' => '购买失败',
    'buy_success' => '购买成功',
    'missing_route_paramters' => '缺少路由参数{param}',
    'range_of_values' => '{key}取值范围:{value}',
    'gateway_error' => '支付接口错误',
    'remember_password_value_0_or_1' => '记住密码取值为0或1',
    'password_is_change_please_login_again' => '密码已修改,请重新授权',
    'logout_success' => '成功退出',
    'inconsistent_login_ip' => '登录ip不一致',
    'login_user_ID_is_inconsistent' => '登录用户ID不一致',
    'log_out_automatically_after_2_hours_without_operation' => '2个小时未操作自动退出登录',
    'login_captcha' => '请输入图形验证码',
    'login_captcha_token' => '请输入图形验证码唯一识别码',
    'login_captcha_error' => '行为验证码错误,请查看配置是否正确',
    'certification_uncertified_cannot_buy_product' => '未实名认证不可购买产品',
    'maintenance_mode' => '维护中...',
    'file_ext_not_allow' => '上传文件后缀不允许',
    'resource_sign_error' => '资源签名错误',
    'resource_expired' => '资源已过期',
    'resource_not_exist' => '资源不存在',

    # 权限规则
    'clientarea_auth_rule_account_index' => '账户详情',
    'clientarea_auth_rule_account_update' => '账户编辑',
    'clientarea_auth_rule_account_verify_old_phone' => '验证原手机',
    'clientarea_auth_rule_account_update_phone' => '修改手机',
    'clientarea_auth_rule_account_verify_old_email' => '验证原邮箱',
    'clientarea_auth_rule_account_update_email' => '修改邮箱',
    'clientarea_auth_rule_account_update_password' => '修改密码',
    'clientarea_auth_rule_account_code_update_password' => '验证码修改密码',
    'clientarea_auth_rule_account_credit_list' => '余额变更记录列表',
    'clientarea_auth_rule_api_list' => 'API密钥列表',
    'clientarea_auth_rule_api_create' => '创建API密钥',
    'clientarea_auth_rule_api_white_list_setting' => 'API白名单设置',
    'clientarea_auth_rule_api_delete' => '删除API密钥',
    'clientarea_auth_rule_cart_index' => '获取购物车',
    'clientarea_auth_rule_cart_create' => '加入购物车',
    'clientarea_auth_rule_cart_update' => '编辑购物车商品',
    'clientarea_auth_rule_cart_update_qty' => '修改购物车商品数量',
    'clientarea_auth_rule_cart_delete' => '删除购物车商品',
    'clientarea_auth_rule_cart_batch_delete' => '批量删除购物车商品',
    'clientarea_auth_rule_cart_clear' => '清空购物车',
    'clientarea_auth_rule_cart_settle' => '结算购物车',
    'clientarea_auth_rule_log_list' => '操作日志',
    'clientarea_auth_rule_order_list' => '订单列表',
    'clientarea_auth_rule_order_index' => '订单详情',
    'clientarea_auth_rule_order_delete' => '删除订单',
    'clientarea_auth_rule_pay_pay' => '支付',
    'clientarea_auth_rule_pay_status' => '支付状态',
    'clientarea_auth_rule_pay_recharge' => '充值',
    'clientarea_auth_rule_pay_credit' => '使用(取消)余额',
    'clientarea_auth_rule_product_settle' => '结算商品',
    'clientarea_auth_rule_transaction_list' => '交易记录',

    # 权限
    'clientarea_auth_basic_auth' => '基础权限',
    'clientarea_auth_order_product' => '订购产品',
    'clientarea_auth_payment' => '支付',
    'clientarea_auth_account_info' => '账户信息',
    'clientarea_auth_outline' => '概要',
    'clientarea_auth_view_log' => '查看日志',
    'clientarea_auth_finance_info' => '财务信息',
    'clientarea_auth_order_record' => '订单记录',
    'clientarea_auth_transaction' => '交易记录',
    'clientarea_auth_balance_record' => '余额记录',
    'clientarea_auth_security_center' => '安全中心',
    'clientarea_auth_api' => 'API',
    'clientarea_auth_api_log' => 'API日志',
    'clientarea_auth_resource_center' => '资源中心',
    'clientarea_auth_product_auth' => '产品权限',
    'clientarea_auth_on_off_restart' => '开机、关机、重启',
    'clientarea_auth_refund_renew_upgrade' => '退款、续费、升降级',
    'clientarea_auth_control_reinstall_rescue_reset_set_mount' => '控制台、重装、救援、重置密码、设置启动项、挂载ISO',
    'clientarea_auth_delete' => '删除',

    # SEO
    // 'web_seo_default_title_index' => '业务系统',
    // 'web_seo_default_keywords_index' => '业务系统',
    //'web_seo_default_description_index' => '通过我们可靠和安全的云服务器托管解决方案，发现云计算的强大力量。我们的尖端技术确保快速高效的性能，而我们的专业支持团队可随时为您解答任何问题或疑虑。选择适合您业务需求的可定制计划，体验云托管的好处吧',
    'web_seo_default_title_about' => '公司介绍',
    'web_seo_default_keywords_about' => '公司介绍',
    'web_seo_default_description_about' => '我们是一家专注于提供高品质产品和优质服务的云计算公司。我们致力于为客户提供最好的购物体验和产品，不断创新和改进，以满足客户的需求。我们的团队由一群充满激情和创意的人组成，他们不断追求卓越，为客户提供最好的解决方案。',
    'web_seo_default_title_announce' => '官方公告',
    'web_seo_default_keywords_announce' => '官方公告',
    'web_seo_default_description_announce' => '欢迎来到我们的网站公告页面。在这里，您可以了解到我们最新的产品和服务信息，以及网站的更新和维护情况。我们会定期发布公告，以便及时向客户传达重要信息。如果您有任何疑问或建议，请随时联系我们的客服团队。感谢您的支持和关注!',
    'web_seo_default_title_cloud' => '云服务器',
    'web_seo_default_keywords_cloud' => '云服务器',
    'web_seo_default_description_cloud' => '我们提供高性能、可靠的云服务器托管服务，让您的业务在云端飞速发展。我们的云服务器采用最新技术，保证稳定、安全、高效的运行。我们的专业团队随时为您提供支持和解决方案，让您无后顾之忧。',
    'web_seo_default_title_contact' => '联系我们',
    'web_seo_default_keywords_contact' => '联系我们',
    'web_seo_default_description_contact' => '我们的联系我们页面提供了多种联系方式，包括在线客服、电话、邮件等，方便客户随时联系我们。我们的客服团队将竭诚为您解答任何问题，提供专业的咨询和服务。我们重视客户的反馈和建议，不断改进和完善我们的产品和服务，以满足客户的需求。',
    'web_seo_default_title_dedicated' => '物理裸机',
    'web_seo_default_keywords_dedicated' => '物理裸机',
    'web_seo_default_description_dedicated' => '我们提供高性能、可靠的物理裸机，让您的业务在独享资源的环境下得到最佳表现。我们的物理裸机采用最新硬件设备，保证稳定、安全、高效的运行。我们的专业团队随时为您提供支持和解决方案，让您无后顾之忧。',
    'web_seo_default_title_document' => '帮助文档',
    'web_seo_default_keywords_document' => '帮助文档',
    'web_seo_default_description_document' => '我们的帮助文档提供了丰富的资料和资源，包括产品手册、技术文档等。您可以轻松地找到所需的信息，帮助您更好地了解我们的产品和服务。我们不断更新和完善文档，确保您始终能够获得最新的信息。',
    'web_seo_default_title_domain' => '域名注册',
    'web_seo_default_keywords_domain' => '域名注册',
    'web_seo_default_description_domain' => '欢迎来到我们的域名注册页面!我们提供全球范围内的域名注册服务，包括各种顶级域名和国别域名。我们的注册流程简单快捷，价格实惠，同时提供安全可靠的域名管理工具，让您的域名管理更加便捷。',
    'web_seo_default_title_feedback' => '意见反馈',
    'web_seo_default_keywords_feedback' => '意见反馈',
    'web_seo_default_description_feedback' => '我们的意见反馈页面是为了听取读者的声音和反馈而设立的。我们欢迎您提出任何建议、意见或问题，以帮助我们改进和提高我们的服务质量。我们的团队会认真阅读每一条反馈，并尽快回复您。',
    'web_seo_default_title_icp' => 'ICP证件办理',
    'web_seo_default_keywords_icp' => 'ICP证件办理',
    'web_seo_default_description_icp' => '我们提供专业的ICP证件代办服务，帮助您快速、顺利地完成证件办理。我们的ICP办理页面简洁明了，操作简单易懂，让您轻松代理完成申请。我们还提供专业的咨询服务，解答您的疑问，确保整个过程顺利无误。',
    'web_seo_default_title_news' => '新闻资讯',
    'web_seo_default_keywords_news' => '新闻资讯',
    'web_seo_default_description_news' => '我们的网站资讯页面提供最新的行业动态、趋势和新闻。我们的编辑团队精选并整理了最有价值的信息，为读者提供高质量的内容。无论你是行业专家还是普通读者，我们都致力于为你提供最有用的信息，帮助你了解行业动态，把握机遇。',
    'web_seo_default_title_recruit' => '人才招聘',
    'web_seo_default_keywords_recruit' => '人才招聘',
    'web_seo_default_description_recruit' => '我们正在寻找充满激情和创意的人才加入我们的团队。我们欢迎有志于成为行业领袖的人士，不论你是刚刚毕业还是有多年工作经验，只要你有热情和才华，我们都欢迎你的加入。我们提供具有竞争力的薪酬和福利，以及良好的职业发展机会和培训计划。',
    'web_seo_default_title_rent' => '机柜租用',
    'web_seo_default_keywords_rent' => '机柜租用',
    'web_seo_default_description_rent' => '我们提供高品质的机柜租用服务，为您的服务器提供安全、稳定的托管环境。我们的机柜配备了先进的温控系统、UPS电源、网络设备等，确保您的服务器始终保持最佳状态。我们还提供24小时监控、定期维护等服务，让您的服务器始终保持高效运行。',
    'web_seo_default_title_service-guarantee' => '服务保障',
    'web_seo_default_keywords_service-guarantee' => '服务保障',
    'web_seo_default_description_service-guarantee' => '我们的服务保障提供了全面的服务保障信息。我们致力于为客户提供最优质的产品和服务，同时也为客户提供了完善的售后保障，确保客户的权益得到充分保障。我们的服务保障是您购物前必须了解的重要信息，让您放心购物，安心使用。',
    'web_seo_default_title_sms' => '短信服务',
    'web_seo_default_keywords_sms' => '短信服务',
    'web_seo_default_description_sms' => '短消息服务 三网合一,便捷接入,秒级可达 产品能力 极高成功率,极低延迟的验证码短信服务 短信通知 支持多种变量,高并发的短信通知群发 推广短信 支持含链接短信,方便用户短信内设置推广链接',
    'web_seo_default_title_ssl' => 'SSL数字证书',
    'web_seo_default_keywords_ssl' => 'SSL数字证书',
    'web_seo_default_description_ssl' => 'SSL证书是一个提供网站安全认证的产品。它包含了网站的SSL证书信息，如证书颁发机构、有效期、加密算法等。通过查看SSL证书页面，用户可以确认网站的身份和安全性，确保自己的个人信息和交易数据不会被窃取或篡改。',
    'web_seo_default_title_trademark' => '商标注册',
    'web_seo_default_keywords_trademark' => '商标注册',
    'web_seo_default_description_trademark' => '我们的商标注册服务为您提供专业、高效的商标注册服务。我们的团队由资深的商标注册专家组成，能够为您提供全方位的商标注册咨询和服务。我们的服务包括商标查询、商标申请、商标撤销、商标转让等，让您的商标注册过程更加顺畅。',
    'web_seo_default_title_trusteeship' => '服务器托管',
    'web_seo_default_keywords_trusteeship' => '服务器托管',
    'web_seo_default_description_trusteeship' => '我们提供专业的服务器托管服务，为您的业务提供安全、稳定的运行环境。我们的机房配备了先进的温控系统、UPS电源、网络设备等，确保您的服务器始终保持最佳状态。我们还提供24小时监控、定期维护等服务，让您的服务器始终保持高效运行。',

    # 前台登录注册修改密码
    'login_type_is_required' => '请传入登录类型',
    'login_type_only_code_or_password' => '登录类型取值为code或password',
    'login_account_require' => '请输入账号',
    'login_email_error' => '邮箱格式错误',
    'login_password_require' => '请输入密码',
    'login_password_len' => '密码长度为6-32位',
    'login_remember_password_is_0_or_1' => '记住密码取值为0或1',
    'login_email_is_not_register' => '邮箱未注册',
    'login_password_error' => '账号或密码错误',
    'login_email_is_not_open' => '未开启邮箱登录',
    'login_phone_is_not_open' => '未开启手机登录',
    'login_phone_verify_is_not_open' => '未开启手机验证码登录',
    'register_type_is_required' => '请传入注册类型',
    'register_type_only_phone_or_email' => '注册类型取值为phone或email',
    'register_account_is_required' => '请输入账号',
    'verification_code_has_expired' => '验证码已过期',
    'register_email_is_not_open' => '未开启邮箱注册',
    'register_phone_is_not_open' => '未开启手机注册/登录',
    'login_phone_code_require' => '请选择国家区号',
    'login_phone_code_error' => '国家区号错误',
    'login_phone_require' => '请输入手机号',
    'login_phone_is_not_right' => '手机号格式错误',
    'login_phone_is_not_register' => '手机号未注册',
    'login_client_is_disabled' => '该帐号已停用/关闭，请联系管理员处理',
    'please_enter_vaild_phone' => '请输入正确的手机号',
    'client_name_cannot_exceed_20_chars' => '用户姓名最多不能超过20个字符',
    'passwords_not_match' => '两次输入的密码不一致',
    'phone_has_been_registered' => '手机号已被注册',
    'email_has_been_registered' => '邮箱已被注册',
    'login_without_common_ip_need_verify' => '系统检测到您此次登录异常，为了保证账户安全，请核验您的身份',
    'operate_password_error' => '操作密码错误',

    # 支付
    'order_id_is_not_exist' => '请传入订单ID',
    'order_is_not_exist' => '订单不存在',
    'order_ownership_error' => '订单归属错误',
    'order_is_paid' => '订单已支付',
    'gateway_is_required' => '请选择支付方式',
    'no_support_gateway' => '不支持的支付方式',
    'recharge_is_not_open' => '充值功能未开启',
    'recharge_amount_is_greater_than_0' => '最小充值金额大于等于0.01',
    'min_recharge_is_error' => '最小充值金额为{min}',
    'max_recharge_is_error' => '最大充值金额为{max}',
    'recharge_success' => '充值成功',
    'client_recharge' => '用户充值，会员ID：{client_id}',
    'recharge_order_cannot_use_credit' => '充值订单不可使用余额',
    'client_credit_is_0' => '余额为0',
    'client_credit_is_used' => '您已使用过余额',
    'client_credit_is_not_enough' => '余额不足',
    'client_credit_no_certification_recharge' => '未认证无法充值',

	# 验证码
    'error_message'                                        => '请求错误',
    'verification_code_error' => '验证码错误',
    'please_get_verification_code' => '请获取验证码',
    'verification_code_can_only_sent_once_per_minute' => '验证码每分钟只能发送一次',

    # 导航
    'nav_index' => '首页',
    'nav_host_list' => '产品列表',
    'nav_finance_info' => '财务信息',
    'nav_account_info' => '账户信息',
    'nav_security' => '安全',
    'nav_goods_list' => '订购产品',

    # 日志
    'modify_profile' => '{client}将{description}',
    'old_to_new' => '{old}改为{new}',
    'submit_order' => '{client}提交订单：{order}包含商品：{product}',
    'bound_mobile' => '{client}绑定手机{phone}',
    'change_bound_mobile' => '{client}修改绑定手机为{phone}原手机号为：{old_phone}',
    'bound_email' => '{client}绑定邮箱{email}',
    'change_bound_email' => '{client}修改绑定邮箱为{email}原邮箱为：{old_email}',
    'change_password' => '{client}修改密码',

    'log_client_login' => '{client}登录系统',
    'log_client_login_status_disabled' => '{client}登录系统失败,已被禁用',
    'log_client_login_code_error' => '{client}登录系统失败,验证码不正确',
    'log_client_login_account_not_register' => '{client}登录系统失败,用户不存在',
    'log_client_login_password_error' => '{client}登录系统失败,密码不正确',
    'log_client_logout' => '{client}注销登录',
    'log_client_register' => '账号{account}注册成功',
    'log_client_pay_order_success' => '{client}支付订单:{order_id},支付金额:{amount}',
    'log_client_pay_with_credit_fail' => '{client}使用部分余额支付失败,失败原因:余额不足(可能将余额应用至其它订单).已将支付金额存入客户余额',
    'log_client_recharge' => '{client}交易流水号:{transaction},充值金额:{amount}',
    'log_client_upload_file' => '{client}上传附件，文件名：{file}',
    'log_client_login_operate_password_error' => '{client}登录系统失败,异常验证失败',
    'log_client_login_operate_password_success' => '{client}登录系统成功,异常验证成功',

    'log_client_add_api' => '{client}添加API，API名称：{name}',
    'log_client_edit_api' => '{client}修改API，API名称：{name}',
    'log_client_delete_api' => '{client}删除API，API名称：{name}',

    'log_client_cancel_order' => '{client}取消订单{order}',


    'client_username' => '姓名',
    'client_company' => '公司',
    'client_country' => '国家',
    'client_address' => '地址',
    'client_language' => '语言',
    'client_notes' => '备注',


    # 账户管理
    'please_enter_old_password' => '请输入旧密码',
    'please_enter_new_password' => '请输入新密码',
    'old_password_error' => '旧密码错误',
    'new_password_cannot_same_old_password' => '新密码不能和旧密码相同',
    'please_enter_code' => '请输入验证码',
    'please_verify_old_phone' => '请验证原手机',
    'please_verify_old_email' => '请验证原邮箱',
    'user_not_bind_phone' => '用户未绑定手机',
    'user_not_bind_email' => '用户未绑定邮箱',
    'verify_type_is_required' => '请选择验证类型',
    'verify_type_only_phone_or_email' => '验证类型只能为手机或邮箱',

    # 购物车
    'clear_cart_success' => '清空购物车成功',
    'position_error' => '位置错误',
    'please_enter_config_options' => '请传入自定义配置',
    'config_options_error' => '自定义配置错误',
    'please_enter_qty' => '请传入数量',
    'qty_error' => '数量错误',
    'product_inventory_shortage' => '商品库存不足',
    'there_are_no_items_in_the_cart' => '购物车内没有商品',
    'please_select_products_in_the_cart' => '请选择购物车商品',
    'cannot_only_buy_son_product' => '子商品不可直接购买',

    # API密钥
    'api_is_not_exist' => 'API密钥不存在',
    'please_enter_api_name' => '请输入API密钥名称',
    'api_name_cannot_exceed_10_chars' => 'API密钥名称最多不能超过10个字符',
    'please_select_api_status' => '清选择是否开启白名单',
    'api_status_error' => '白名单参数错误',
    'please_enter_api_ip' => '请输入白名单IP',
    'api_ip_error' => '白名单IP格式错误',
    'api_key_create_max' => '单个用户最多只允许创建10个API密钥',
    'api_auth_fail' => '鉴权失败',

    # 产品
    'host_notes_cannot_exceed_1000_chars' => '备注最多不能超过1000个字符',
    'upstream_host_is_not_exist' => '上游产品不存在',

    # 订单
    'order_cannot_cancel' => '未支付订单才可以取消',
    'order_host_not_unpaid' => '订单下产品不是未支付状态，不可取消订单',

    # 日志
    'log_api_auth_login' => '{client}API鉴权登录',

    # 意见反馈
    'please_enter_feedback_title' => '请输入标题',
    'feedback_title_cannot_exceed_255_chars' => '标题不能超过255个字符',
    'please_enter_feedback_description' => '请输入描述',
    'feedback_contact_cannot_exceed_255_chars' => '联系方式不能超过255个字符',

    # 方案咨询
    'please_enter_consult_contact' => '请输入联系人',
    'consult_contact_cannot_exceed_50_chars' => '联系人不能超过50个字符',
    'consult_company_cannot_exceed_255_chars' => '公司名称不能超过255个字符',
    'please_enter_consult_phone'  => '请输入手机号码',
    'consult_phone_cannot_exceed_20_chars' => '手机号码不能超过20个字符',
    'please_enter_consult_email' => '请输入联系邮箱', 
    'consult_email_error' => '联系邮箱格式错误', 
    'please_enter_consult_matter' => '请输入咨询产品', 
    'consult_matter_cannot_exceed_1000_chars' => '咨询产品不能超过1000个字符',

    'order_description_append' => '{product_name}({name}),购买时长:{billing_cycle_name}({time})',

    'notice_open_require' => '是否接收通知必填',
    'notice_open_in' => '是否接收通知必填取值0,1',
    'notice_method_require' => '接收方式必填',
    'notice_method_in' => '接收方式为all,email,sms',
    'notice_open' => '是否接收通知',
    'notice_method' => '接收通知方式',
    'notice_open_1' => '开启',
    'notice_open_0' => '关闭',

    'log_module_sync_account_success' => '{client}的产品{host}信息同步成功',
    'log_module_sync_account_failed' => '{client}的产品{host}信息同步失败',

    'host_not_the_same_product' => '非同一商品',
    'host_token_error' => '秘钥错误',
    'login_email_password_close' => '未开启邮箱密码登录',
];
