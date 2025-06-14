<?php

return [
    'success_message' => '請求成功',
    'fail_message' => '請求失敗',
    'param_error' => '參數錯誤',
    'delete_success' => '刪除成功',
    'delete_fail' => '刪除失敗',
    'cannot_repeat_opreate' => '不可重複操作',
    'client_is_not_exist' => '客戶不存在',
    'id_error' => 'ID錯誤',
    'refund_suspend' => '停用',
    'refund_pending' => '待審核',
    'refund_suspending' => '待停用',
    'refund_suspend_1' => '停用中',
    'refund_suspended' => '已停用',
    'refund_refund' => '已退款',
    'refund_reject' => '已駁回',
    'refund_cancelled' => '已取消',
    'refund_cancelled_button' => '取消停用',
    'refund_reject_reason' => '駁回原因',
    'refund_product_id_require' => '請選擇商品ID',
    'refund_product_id_integer' => '商品ID為整數',
    'refund_type_require' => '請選擇退款類型',
    'refund_type_in' => '退款類型取值為Artificial或Auto',
    'refund_require_in' => '退款要求取值為First或Same',
    'refund_range_control_require' => '購買天數控制必填',
    'refund_range_control_in' => '購買天數控制取值為0或1',
    'refund_range_require' => '購買天數必填',
    'refund_range_integer' => '購買天數為整數',
    'refund_range_egt' => '購買天數為大於等於0的整數',
    'refund_rule_require' => '請選擇退款規則',
    'refund_rule_in' => '退款規則取值為Day,Month或Ratio',
    'refund_ratio_value_float' => '比例為浮點數',
    'refund_ratio_value_egt' => '比例大於等於0',
    'refund_ratio_value_elt' => '比例小於等於100',
    'refund_product_is_not_exist' => '商品不存在',
    'refund_rule_only_day_or_month' => '退款規則取值僅為Day或Month',
    'refund_rule_only_ratio' => '退款規則取值僅為Ratio',
    'refund_ratio_require' => '比例必填',
    'refund_refund_product_is_not_exist' => '退款商品不存在',
    'refund_refund_product_is_exist' => '商品已添加至退款商品,不可重複添加',
    'refund_reason_content_require' => '請填寫停用原因內容',
    'refund_reason_content_max' => '內容長度不超過500個字符',
    'refund_refund_reason_is_not_exist' => '停用原因不存在',
    'refund_reason_custom_require' => '停用原因自定義是否開啟必填',
    'refund_reason_custom_in' => '停用原因自定義是否開啟取值為0或1',
    'refund_host_is_not_exist' => '產品不存在',
    'refund_host_cannot_suspend' => '產品不可停用',
    'refund_suspend_reason_max' => '產品停用原因不超過500個字符',
    'refund_suspend_reason_array' => '產品停用原因為數組',
    'refund_suspend_reason_is_not_exist' => '產品停用原因不存在',
    'refund_suspend_reason_require' => '請選擇產品停用原因',
    'refund_refund_is_not_exist' => '停用申請不存在',
    'refund_refund_only_pending' => '停用申請僅待審核狀態可操作',
    'refund_reject_reason_require' => '請填寫駁回原因',
    'refund_reject_reason_max' => '駁回原因不超過2000個字符',
    'refund_refund_only_pending_or_suspending' => '停用申請僅待審核或待停用可操作',
    'refund_refund_type_in' => '停用時間為Expire或Immediate',
    'refund_product_pay_type_free' => '免費商品不可添加為退款商品',
    'refund_product_refunded' => '產品已申請停用,不可再次申請',
    'refund_to_client_credit' => '退款至用戶餘額',

    # 日誌
    'refund_create_refund_product' => '{admin}新增可退款商品:{product}',
    'refund_update_refund_product' => '{admin}修改可退款商品:{product}',
    'refund_delete_refund_product' => '{admin}刪除可退款商品:{product}',
    'refund_create_refund_reason' => '{admin}新增退款原因:{reason}',
    'refund_update_refund_reason' => '{admin}修改退款原因:{reason}',
    'refund_delete_refund_reason' => '{admin}刪除退款原因:{reason}',
    'refund_stop_refund_reason_custom' => '{admin}關閉用戶自定義退款原因',
    'refund_start_refund_reason_custom' => '{admin}開啟用戶自定義退款原因',
    'refund_pending_refund_product' => '{admin}通過用戶:{client}的停用申請,退還用戶:{currency_prefix}{amount}{currency_suffix}',
    'refund_reject_refund_product' => '{admin}駁回用戶:{client}的停用申請,駁回原因:{reason}',
    'refund_cancel_refund_product' => '{admin}取消用戶:{client}的停用申請',
    'refund_refund_host' => '{client}停用產品:{host},退款金額:{currency_prefix}{amount}{currency_suffix}',
    'refund_refund_host_fail' => '{client}停用產品{host}失敗，失敗原因：{reason}',

    # 導航
    'nav_plugin_addon_idcsmart_refund' => '退款',
    'nav_plugin_addon_refund' => '退款',
    'nav_plugin_addon_refund_list' => '停用列表',
    'nav_plugin_addon_refund_product_list' => '商品管理',

    'client_refund_success_send_mail' => '產品退款成功,發送郵件',
    'client_refund_success_send_sms' => '產品退款成功,發送短信',
    'admin_refund_reject_send_mail' => '產品退款駁回,發送郵件',
    'admin_refund_reject_send_sms' => '產品退款駁回,發送短信',
    'client_refund_cancel_send_mail' => '產品取消請求,發送郵件',
    'client_refund_cancel_send_sms' => '產品取消請求,發送短信',
    'client_create_refund_send_mail' => '產品退款申請,發送郵件',
    'client_create_refund_send_sms' => '產品退款申請,發送短信',

    'auth_user_refund' => '退款管理',
    'auth_user_refund_apply_list' => '申請清單',
    'auth_user_refund_apply_list_view' => '檢視頁面',
    'auth_user_refund_apply_list_approve' => '通過審核',
    'auth_user_refund_apply_list_reject' => '審核駁回',
    'auth_user_refund_apply_list_cancel_apply' => '取消申請',
    'auth_user_refund_product' => '商品管理',
    'auth_user_refund_product_view' => '檢視頁面',
    'auth_user_refund_product_create_product' => '新增可退款商品',
    'auth_user_refund_product_suspend_reason' => '停用原因管理',
    'auth_user_refund_product_update_product' => '編輯退款商品',
    'auth_user_refund_product_delete_product' => '刪除退款商品',
];
