<?php
/**
 * @author theworld
 * @time 2022-06-21
 */
use think\facade\Route;

# 前台
Route::group('console/v1',function (){
    # 带上默认参数,可以使用继承控制器app\admin\controller\PluginBaseController的一些通用方法,也可以不追加这些参数(_plugin插件名称C风格,_controller控制器名称C风格,_action方法名称C风格)
    # 帮助中心
    Route::get('help/index', "\\addon\\idcsmart_help\\controller\\clientarea\\IndexController@indexIdcsmartHelp")
        ->append(['_plugin'=>'idcsmart_help','_controller'=>'index','_action'=>'index_idcsmart_help']);
    Route::get('help', "\\addon\\idcsmart_help\\controller\\clientarea\\IndexController@idcsmartHelp")
        ->append(['_plugin'=>'idcsmart_help','_controller'=>'index','_action'=>'idcsmart_help']);
    Route::get('help/:id', "\\addon\\idcsmart_help\\controller\\clientarea\\IndexController@idcsmartHelpDetail")
    ->append(['_plugin'=>'idcsmart_help','_controller'=>'index','_action'=>'idcsmart_help_detail']);
})
    ->allowCrossDomain([
        'Access-Control-Allow-Origin'        => $origin,
        'Access-Control-Allow-Credentials'   => 'true',
        'Access-Control-Max-Age'             => 600,
    ])
    ->middleware(\app\http\middleware\Check::class);
# 后台
Route::group(DIR_ADMIN . '/v1',function (){
    # 带上默认参数,可以使用继承控制器app\admin\controller\PluginBaseController的一些通用方法,也可以不追加这些参数(_plugin插件名称C风格,_controller控制器名称C风格,_action方法名称C风格)
    # 帮助中心
    Route::get('help', "\\addon\\idcsmart_help\\controller\\AdminIndexController@idcsmartHelpList")
        ->append(['_plugin'=>'idcsmart_help','_controller'=>'admin_index','_action'=>'idcsmart_help_list']);
    Route::get('help/:id', "\\addon\\idcsmart_help\\controller\\AdminIndexController@idcsmartHelpDetail")
        ->append(['_plugin'=>'idcsmart_help','_controller'=>'admin_index','_action'=>'idcsmart_help_detail']);
    Route::post('help', "\\addon\\idcsmart_help\\controller\\AdminIndexController@createIdcsmartHelp")
        ->append(['_plugin'=>'idcsmart_help','_controller'=>'admin_index','_action'=>'create_idcsmart_help']);
    Route::put('help/:id', "\\addon\\idcsmart_help\\controller\\AdminIndexController@updateIdcsmartHelp")
        ->append(['_plugin'=>'idcsmart_help','_controller'=>'admin_index','_action'=>'update_idcsmart_help']);
    Route::delete('help/:id', "\\addon\\idcsmart_help\\controller\\AdminIndexController@deleteIdcsmartHelp")
        ->append(['_plugin'=>'idcsmart_help','_controller'=>'admin_index','_action'=>'delete_idcsmart_help']);
    Route::put('help/:id/hidden', "\\addon\\idcsmart_help\\controller\\AdminIndexController@hiddenIdcsmartHelp")
        ->append(['_plugin'=>'idcsmart_help','_controller'=>'admin_index','_action'=>'hidden_idcsmart_help']);
    Route::get('help/type', "\\addon\\idcsmart_help\\controller\\AdminIndexController@idcsmartHelpTypeList")
        ->append(['_plugin'=>'idcsmart_help','_controller'=>'admin_index','_action'=>'idcsmart_help_type_list']);
    Route::post('help/type', "\\addon\\idcsmart_help\\controller\\AdminIndexController@createIdcsmartHelpType")
        ->append(['_plugin'=>'idcsmart_help','_controller'=>'admin_index','_action'=>'create_idcsmart_help_type']);
    Route::put('help/type/:id', "\\addon\\idcsmart_help\\controller\\AdminIndexController@updateIdcsmartHelpType")
        ->append(['_plugin'=>'idcsmart_help','_controller'=>'admin_index','_action'=>'update_idcsmart_help_type']);
    Route::delete('help/type/:id', "\\addon\\idcsmart_help\\controller\\AdminIndexController@deleteIdcsmartHelpType")
        ->append(['_plugin'=>'idcsmart_help','_controller'=>'admin_index','_action'=>'delete_idcsmart_help_type']);
    Route::get('help/index', "\\addon\\idcsmart_help\\controller\\AdminIndexController@indexIdcsmartHelp")
        ->append(['_plugin'=>'idcsmart_help','_controller'=>'admin_index','_action'=>'index_idcsmart_help']);
    Route::put('help/index', "\\addon\\idcsmart_help\\controller\\AdminIndexController@indexIdcsmartHelpSave")
        ->append(['_plugin'=>'idcsmart_help','_controller'=>'admin_index','_action'=>'index_idcsmart_help_save']);
})
    ->allowCrossDomain([
        'Access-Control-Allow-Origin'        => $origin,
        'Access-Control-Allow-Credentials'   => 'true',
        'Access-Control-Max-Age'             => 600,
    ])
    ->middleware(\app\http\middleware\CheckAdmin::class)
    ->middleware(\app\http\middleware\ParamFilter::class);
