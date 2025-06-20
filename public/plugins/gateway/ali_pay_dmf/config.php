<?php
/**
 * @desc 插件后台配置
 * @author wyh
 * @version 1.0
 * @time 2022-05-27
 */
return [
    'module_name'            => [    # 在后台插件配置表单中的键名(统一规范:小写+下划线),会是config[module_name]
        'title' => '名称',            # 表单的label标题
        'type'  => 'text',           # 表单的类型：text文本,password密码,checkbox复选框,select下拉,radio单选,textarea文本区域,tip提示
        'value' => '支付宝当面付',     # 表单的默认值
        'tip'   => 'friendly name',  # 表单的帮助提示
        'size'  => 200,               # 输入框长度(当type类型为text,password,textarea,tip时,可传入此键)
    ],
    'app_id'                 => [
        'title' => 'appID',
        'type'  => 'text',
        'value' => '',
        'tip'   => '',
        'size'  => 200,
    ],
    'merchant_private_key'   => [
        'title' => '商户私钥',
        'type'  => 'text',
        'value' => '',
        'tip'   => '',
        'size'  => 200,
    ],
    'alipay_public_key'      => [
        'title' => '支付宝公钥',
        'type'  => 'text',
        'value' => '',
        'tip'   => '',
        'size'  => 200,
    ],
    # 此配置作为范例
    'mode'                   => [
        'title'      => '模式',
        'type'       => 'radio',
        'options'    => [            # 当type类型为checkbox,select,radio时,需要有此键,作为选项
            'debug'  => '调试',
            'online' => '上线',
        ],
        'value'      => 'online',
        'tip'        => '请选择模式',
        'attribute'  => 'disabled',  # 属性,加入此键:disabled表示禁止编辑,只能看;
    ],

];
