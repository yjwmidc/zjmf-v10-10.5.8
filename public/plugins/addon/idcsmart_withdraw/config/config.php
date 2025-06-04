<?php

$domain = request()->domain();
return [
	# 短信/邮件模板初始化
    "idcsmart_withdraw_notice_template" => [ 
        'cash_withdrawal_notice' => [
            'name_lang' => '提现打款',
            'type' => 'order_pay',
            'sms_name' => 'Idcsmart',           
            'sms_template' => [
                'title' => '提现打款',
                'content' => '您有一笔【@var(withdraw_amount)】的提现已打款，请及时查收。'
            ],
            'sms_global_name' => 'Idcsmart',
            'sms_global_template' => [
                'title' => '提现打款',
                'content' => '您有一笔【@var(withdraw_amount)】的提现已打款，请及时查收。'
            ],
            'email_name' => 'Smtp',
            'email_template' => [
                'name' => '提现打款',
                'title' => '[{system_website_name}]提现打款',
                'content' => file_get_contents(WEB_ROOT . 'plugins/addon/idcsmart_withdraw/config/email_template/cash_withdrawal_notice.html')
            ],
            
        ], 
    ],
];