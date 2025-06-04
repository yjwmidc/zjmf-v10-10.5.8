<?php 

use think\facade\Route;

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

# 前台,可登录不可登录的接口
Route::group('console/v1',function (){

	// 订购页面
    Route::get('product/:id/mf_cloud/order_page', "\\server\\mf_cloud\\controller\\home\\CloudController@orderPage");
    Route::get('product/:id/mf_cloud/image', "\\server\\mf_cloud\\controller\\home\\CloudController@imageList");
    Route::post('product/:id/mf_cloud/duration', "\\server\\mf_cloud\\controller\\home\\CloudController@getAllDurationPrice");
    Route::get('product/:id/mf_cloud/vpc_network/search', "\\server\\mf_cloud\\controller\\home\\CloudController@vpcNetworkSearch");
    Route::get('product/:id/mf_cloud/line/:line_id', "\\server\\mf_cloud\\controller\\home\\CloudController@lineConfig");
    Route::get('product/:id/mf_cloud/data_center', "\\server\\mf_cloud\\controller\\home\\CloudController@dataCenterSelect");

    // vnc
    Route::get('mf_cloud/:id/vnc', "\\server\\mf_cloud\\controller\\home\\CloudController@vncPage");


})->allowCrossDomain([
    'Access-Control-Allow-Origin'        => $origin,
    'Access-Control-Allow-Credentials'   => 'true',
    'Access-Control-Max-Age'             => 600,
])->middleware(\app\http\middleware\Check::class);

// 前台需要登录的接口
Route::group('console/v1',function (){

	Route::post('product/:id/mf_cloud/validate_settle', "\\server\\mf_cloud\\controller\\home\\CloudController@validateSettle");
	Route::get('mf_cloud', "\\server\\mf_cloud\\controller\\home\\CloudController@list");
	Route::get('mf_cloud/:id', "\\server\\mf_cloud\\controller\\home\\CloudController@detail");
	Route::get('mf_cloud/:id/part', "\\server\\mf_cloud\\controller\\home\\CloudController@detailPart");
	Route::get('mf_cloud/:id/status', "\\server\\mf_cloud\\controller\\home\\CloudController@status");
	Route::get('mf_cloud/:id/chart', "\\server\\mf_cloud\\controller\\home\\CloudController@chart");
	Route::get('mf_cloud/:id/disk', "\\server\\mf_cloud\\controller\\home\\CloudController@disk");
	Route::post('mf_cloud/:id/disk/:disk_id/unmount', "\\server\\mf_cloud\\controller\\home\\CloudController@diskUnmount");
	Route::post('mf_cloud/:id/disk/:disk_id/mount', "\\server\\mf_cloud\\controller\\home\\CloudController@diskMount");
	Route::get('mf_cloud/:id/snapshot', "\\server\\mf_cloud\\controller\\home\\CloudController@snapshot");
	Route::post('mf_cloud/:id/snapshot', "\\server\\mf_cloud\\controller\\home\\CloudController@snapshotCreate");
	Route::post('mf_cloud/:id/snapshot/restore', "\\server\\mf_cloud\\controller\\home\\CloudController@snapshotRestore");
	Route::delete('mf_cloud/:id/snapshot/:snapshot_id', "\\server\\mf_cloud\\controller\\home\\CloudController@snapshotDelete");
	Route::get('mf_cloud/:id/backup', "\\server\\mf_cloud\\controller\\home\\CloudController@backup");
	Route::post('mf_cloud/:id/backup', "\\server\\mf_cloud\\controller\\home\\CloudController@backupCreate");
	Route::post('mf_cloud/:id/backup/restore', "\\server\\mf_cloud\\controller\\home\\CloudController@backupRestore");
	Route::delete('mf_cloud/:id/backup/:backup_id', "\\server\\mf_cloud\\controller\\home\\CloudController@backupDelete");
	Route::get('mf_cloud/:id/flow', "\\server\\mf_cloud\\controller\\home\\CloudController@flowDetail");
	Route::get('mf_cloud/:id/log', "\\server\\mf_cloud\\controller\\home\\CloudController@log");
	Route::get('mf_cloud/:id/image/check', "\\server\\mf_cloud\\controller\\home\\CloudController@checkHostImage");
	Route::post('mf_cloud/:id/image/order', "\\server\\mf_cloud\\controller\\home\\CloudController@createImageOrder");
	Route::get('mf_cloud/:id/remote_info', "\\server\\mf_cloud\\controller\\home\\CloudController@remoteInfo");
	Route::get('mf_cloud/:id/ip', "\\server\\mf_cloud\\controller\\home\\CloudController@ipList");
	Route::post('mf_cloud/:id/disk/price', "\\server\\mf_cloud\\controller\\home\\CloudController@calBuyDiskPrice");
	Route::post('mf_cloud/:id/disk/order', "\\server\\mf_cloud\\controller\\home\\CloudController@createBuyDiskOrder");
	Route::post('mf_cloud/:id/disk/resize', "\\server\\mf_cloud\\controller\\home\\CloudController@calResizeDiskPrice");
	Route::post('mf_cloud/:id/disk/resize/order', "\\server\\mf_cloud\\controller\\home\\CloudController@createResizeDiskOrder");
	Route::post('mf_cloud/:id/simulate_physical_machine', "\\server\\mf_cloud\\controller\\home\\CloudController@simulatePhysicalMachine");
	Route::get('mf_cloud/:id/ipv6', "\\server\\mf_cloud\\controller\\home\\CloudController@ipv6List");
	Route::get('mf_cloud/:id/download_rdp', "\\server\\mf_cloud\\controller\\home\\CloudController@downloadRdp");
	Route::get('mf_cloud/:id/whether_renew', "\\server\\mf_cloud\\controller\\home\\CloudController@whetherRenew");

	Route::get('mf_cloud/:id/backup_config', "\\server\\mf_cloud\\controller\\home\\CloudController@calBackupConfigPrice");
	Route::post('mf_cloud/:id/backup_config/order', "\\server\\mf_cloud\\controller\\home\\CloudController@createBackupConfigOrder");

	Route::get('mf_cloud/:id/ip_num', "\\server\\mf_cloud\\controller\\home\\CloudController@calIpNumPrice");
	Route::post('mf_cloud/:id/ip_num/order', "\\server\\mf_cloud\\controller\\home\\CloudController@createIpNumOrder");

	Route::post('mf_cloud/:id/vpc_network', "\\server\\mf_cloud\\controller\\home\\CloudController@createVpcNetwork");
	Route::get('mf_cloud/:id/vpc_network', "\\server\\mf_cloud\\controller\\home\\CloudController@vpcNetworkList");
	// Route::put('mf_cloud/:id/vpc_network/:vpc_network_id', "\\server\\mf_cloud\\controller\\home\\CloudController@vpcNetworkUpdate");
	Route::delete('mf_cloud/:id/vpc_network/:vpc_network_id', "\\server\\mf_cloud\\controller\\home\\CloudController@vpcNetworkDelete");
	Route::put('mf_cloud/:id/vpc_network', "\\server\\mf_cloud\\controller\\home\\CloudController@changeVpcNetwork");
	Route::post('product/:id/mf_cloud/vpc_network', "\\server\\mf_cloud\\controller\\home\\CloudController@vpcNetworkCreateNew");
	Route::delete('product/:id/mf_cloud/vpc_network', "\\server\\mf_cloud\\controller\\home\\CloudController@vpcNetworkDeleteNew");
	Route::get('mf_cloud/vpc_network/host', "\\server\\mf_cloud\\controller\\home\\CloudController@enableVpcHost");


	Route::get('mf_cloud/:id/real_data', "\\server\\mf_cloud\\controller\\home\\CloudController@cloudRealData");

	Route::get('mf_cloud/:id/common_config', "\\server\\mf_cloud\\controller\\home\\CloudController@calCommonConfigPrice");
	Route::post('mf_cloud/:id/common_config/order', "\\server\\mf_cloud\\controller\\home\\CloudController@createCommonConfigOrder");

	// NAT转发建站
	Route::get('mf_cloud/:id/nat_acl', "\\server\\mf_cloud\\controller\\home\\CloudController@natAclList");
	Route::post('mf_cloud/:id/nat_acl', "\\server\\mf_cloud\\controller\\home\\CloudController@natAclCreate");
	Route::delete('mf_cloud/:id/nat_acl', "\\server\\mf_cloud\\controller\\home\\CloudController@natAclDelete");
	Route::get('mf_cloud/:id/nat_web', "\\server\\mf_cloud\\controller\\home\\CloudController@natWebList");
	Route::post('mf_cloud/:id/nat_web', "\\server\\mf_cloud\\controller\\home\\CloudController@natWebCreate");
	Route::delete('mf_cloud/:id/nat_web', "\\server\\mf_cloud\\controller\\home\\CloudController@natWebDelete");

	// 套餐升降级
	Route::get('mf_cloud/:id/recommend_config', "\\server\\mf_cloud\\controller\\home\\CloudController@getUpgradeRecommendConfig");
	Route::get('mf_cloud/:id/recommend_config/price', "\\server\\mf_cloud\\controller\\home\\CloudController@calUpgradeRecommendConfig");
	Route::post('mf_cloud/:id/recommend_config/order', "\\server\\mf_cloud\\controller\\home\\CloudController@createUpgradeRecommendConfigOrder");

	// Route::get('mf_cloud/:id/package/config/price', "\\server\\mf_cloud\\controller\\home\\CloudController@calPackageConfigPrice");
	// Route::post('mf_cloud/:id/package/config/order', "\\server\\mf_cloud\\controller\\home\\CloudController@createPackageConfigOrder");

	Route::get('mf_cloud/:id/upgrade_defence_config', "\\server\\mf_cloud\\controller\\home\\CloudController@defenceConfig");
	Route::get('mf_cloud/:id/upgrade_defence/price', "\\server\\mf_cloud\\controller\\home\\CloudController@calDefencePrice");
	Route::post('mf_cloud/:id/upgrade_defence/order', "\\server\\mf_cloud\\controller\\home\\CloudController@createDefenceOrder");

	Route::group('',function (){

		Route::post('mf_cloud/:id/on', "\\server\\mf_cloud\\controller\\home\\CloudController@on");
		Route::post('mf_cloud/:id/off', "\\server\\mf_cloud\\controller\\home\\CloudController@off");
		Route::post('mf_cloud/:id/reboot', "\\server\\mf_cloud\\controller\\home\\CloudController@reboot");
		Route::post('mf_cloud/:id/hard_off', "\\server\\mf_cloud\\controller\\home\\CloudController@hardOff");
		Route::post('mf_cloud/:id/hard_reboot', "\\server\\mf_cloud\\controller\\home\\CloudController@hardReboot");
		Route::post('mf_cloud/batch_operate', "\\server\\mf_cloud\\controller\\home\\CloudController@batchOperate");
		Route::post('mf_cloud/:id/vnc', "\\server\\mf_cloud\\controller\\home\\CloudController@vnc");
		Route::post('mf_cloud/:id/reset_password', "\\server\\mf_cloud\\controller\\home\\CloudController@resetPassword");
		Route::post('mf_cloud/:id/rescue', "\\server\\mf_cloud\\controller\\home\\CloudController@rescue");
		Route::post('mf_cloud/:id/rescue/exit', "\\server\\mf_cloud\\controller\\home\\CloudController@exitRescue");
		Route::post('mf_cloud/:id/reinstall', "\\server\\mf_cloud\\controller\\home\\CloudController@reinstall");

	})->middleware(\app\http\middleware\CheckClientOperatePassword::class);  // 需要验证操作密码
	    
})->allowCrossDomain([
    'Access-Control-Allow-Origin'        => $origin,
    'Access-Control-Allow-Credentials'   => 'true',
    'Access-Control-Max-Age'             => 600,
])
->middleware(\app\http\middleware\CheckHome::class)
->middleware(\app\http\middleware\ParamFilter::class)
->middleware(\server\mf_cloud\middleware\CheckAuthMiddleware::class)
    ->middleware(\app\http\middleware\RejectRepeatRequest::class);


# 后台,可登录不可登录的接口
Route::group(DIR_ADMIN . '/v1',function (){
    
    Route::get('mf_cloud/:id/vnc', "\\server\\mf_cloud\\controller\\admin\\CloudController@vncPage");

})->allowCrossDomain([
    'Access-Control-Allow-Origin'        => $origin,
    'Access-Control-Allow-Credentials'   => 'true',
    'Access-Control-Max-Age'             => 600,
])->middleware(\app\http\middleware\Check::class);

// 后台接口
Route::group(DIR_ADMIN . '/v1',function (){
    
    // 周期
	Route::post('mf_cloud/duration', "\\server\\mf_cloud\\controller\\admin\\DurationController@create");
	Route::get('mf_cloud/duration', "\\server\\mf_cloud\\controller\\admin\\DurationController@list");
	Route::put('mf_cloud/duration/:id', "\\server\\mf_cloud\\controller\\admin\\DurationController@update");
	Route::delete('mf_cloud/duration/:id', "\\server\\mf_cloud\\controller\\admin\\DurationController@delete");
	
	// CPU配置
	Route::post('mf_cloud/cpu', "\\server\\mf_cloud\\controller\\admin\\CpuController@create");
	Route::get('mf_cloud/cpu', "\\server\\mf_cloud\\controller\\admin\\CpuController@list");
	Route::put('mf_cloud/cpu/:id', "\\server\\mf_cloud\\controller\\admin\\CpuController@update");
	Route::delete('mf_cloud/cpu/:id', "\\server\\mf_cloud\\controller\\admin\\CpuController@delete");
	Route::get('mf_cloud/cpu/:id', "\\server\\mf_cloud\\controller\\admin\\CpuController@index");

	// 内存配置
	Route::post('mf_cloud/memory', "\\server\\mf_cloud\\controller\\admin\\MemoryController@create");
	Route::get('mf_cloud/memory', "\\server\\mf_cloud\\controller\\admin\\MemoryController@list");
	Route::put('mf_cloud/memory/:id', "\\server\\mf_cloud\\controller\\admin\\MemoryController@update");
	Route::delete('mf_cloud/memory/:id', "\\server\\mf_cloud\\controller\\admin\\MemoryController@delete");
	Route::get('mf_cloud/memory/:id', "\\server\\mf_cloud\\controller\\admin\\MemoryController@index");

	// 系统盘配置
	Route::post('mf_cloud/system_disk', "\\server\\mf_cloud\\controller\\admin\\SystemDiskController@create");
	Route::get('mf_cloud/system_disk', "\\server\\mf_cloud\\controller\\admin\\SystemDiskController@list");
	Route::put('mf_cloud/system_disk/:id', "\\server\\mf_cloud\\controller\\admin\\SystemDiskController@update");
	Route::delete('mf_cloud/system_disk/:id', "\\server\\mf_cloud\\controller\\admin\\SystemDiskController@delete");
	Route::get('mf_cloud/system_disk/:id', "\\server\\mf_cloud\\controller\\admin\\SystemDiskController@index");
	Route::get('mf_cloud/system_disk/type', "\\server\\mf_cloud\\controller\\admin\\SystemDiskController@diskTypeList");

	// 数据盘配置
	Route::post('mf_cloud/data_disk', "\\server\\mf_cloud\\controller\\admin\\DataDiskController@create");
	Route::get('mf_cloud/data_disk', "\\server\\mf_cloud\\controller\\admin\\DataDiskController@list");
	Route::put('mf_cloud/data_disk/:id', "\\server\\mf_cloud\\controller\\admin\\DataDiskController@update");
	Route::delete('mf_cloud/data_disk/:id', "\\server\\mf_cloud\\controller\\admin\\DataDiskController@delete");
	Route::get('mf_cloud/data_disk/:id', "\\server\\mf_cloud\\controller\\admin\\DataDiskController@index");
	Route::get('mf_cloud/data_disk/type', "\\server\\mf_cloud\\controller\\admin\\DataDiskController@diskTypeList");

	// 系统盘性能限制
	Route::post('mf_cloud/system_disk_limit', "\\server\\mf_cloud\\controller\\admin\\SystemDiskController@diskLimitCreate");
	Route::get('mf_cloud/system_disk_limit', "\\server\\mf_cloud\\controller\\admin\\SystemDiskController@diskLimitList");
	Route::put('mf_cloud/system_disk_limit/:id', "\\server\\mf_cloud\\controller\\admin\\SystemDiskController@diskLimitUpdate");
	Route::delete('mf_cloud/system_disk_limit/:id', "\\server\\mf_cloud\\controller\\admin\\SystemDiskController@diskLimitDelete");

	// 数据盘性能限制
	Route::post('mf_cloud/data_disk_limit', "\\server\\mf_cloud\\controller\\admin\\DataDiskController@diskLimitCreate");
	Route::get('mf_cloud/data_disk_limit', "\\server\\mf_cloud\\controller\\admin\\DataDiskController@diskLimitList");
	Route::put('mf_cloud/data_disk_limit/:id', "\\server\\mf_cloud\\controller\\admin\\DataDiskController@diskLimitUpdate");
	Route::delete('mf_cloud/data_disk_limit/:id', "\\server\\mf_cloud\\controller\\admin\\DataDiskController@diskLimitDelete");

	// 数据中心
	Route::post('mf_cloud/data_center', "\\server\\mf_cloud\\controller\\admin\\DataCenterController@create");
	Route::get('mf_cloud/data_center', "\\server\\mf_cloud\\controller\\admin\\DataCenterController@list");
	Route::put('mf_cloud/data_center/:id', "\\server\\mf_cloud\\controller\\admin\\DataCenterController@update");
	Route::delete('mf_cloud/data_center/:id', "\\server\\mf_cloud\\controller\\admin\\DataCenterController@delete");
	Route::get('mf_cloud/data_center/select', "\\server\\mf_cloud\\controller\\admin\\DataCenterController@dataCenterSelect");
	Route::get('mf_cloud/data_center/:id', "\\server\\mf_cloud\\controller\\admin\\DataCenterController@dataCenterDetail");

	Route::get('mf_cloud/data_center/gpu/:id', "\\server\\mf_cloud\\controller\\admin\\DataCenterController@dataCenterGpuIndex");
	Route::post('mf_cloud/data_center/:id/gpu', "\\server\\mf_cloud\\controller\\admin\\DataCenterController@dataCenterGpuCreate");
	Route::put('mf_cloud/data_center/gpu/:id', "\\server\\mf_cloud\\controller\\admin\\DataCenterController@dataCenterGpuUpdate");
	Route::delete('mf_cloud/data_center/gpu/:id', "\\server\\mf_cloud\\controller\\admin\\DataCenterController@dataCenterGpuOptionDelete");
	Route::put('mf_cloud/data_center/:id/gpu_name', "\\server\\mf_cloud\\controller\\admin\\DataCenterController@updateGpuName");
	Route::delete('mf_cloud/data_center/:id/gpu', "\\server\\mf_cloud\\controller\\admin\\DataCenterController@dataCenterGpuDelete");
	
	// 操作系统分类
	Route::post('mf_cloud/image_group', "\\server\\mf_cloud\\controller\\admin\\ImageController@imageGroupCreate");
	Route::get('mf_cloud/image_group', "\\server\\mf_cloud\\controller\\admin\\ImageController@imageGroupList");
	Route::put('mf_cloud/image_group/:id', "\\server\\mf_cloud\\controller\\admin\\ImageController@imageGroupUpdate");
	Route::delete('mf_cloud/image_group/:id', "\\server\\mf_cloud\\controller\\admin\\ImageController@imageGroupDelete");
	Route::put('mf_cloud/image_group/order', "\\server\\mf_cloud\\controller\\admin\\ImageController@imageGroupOrder");

	// 操作系统
	Route::post('mf_cloud/image', "\\server\\mf_cloud\\controller\\admin\\ImageController@imageCreate");
	Route::get('mf_cloud/image', "\\server\\mf_cloud\\controller\\admin\\ImageController@imageList");
	Route::put('mf_cloud/image/:id', "\\server\\mf_cloud\\controller\\admin\\ImageController@imageUpdate");
	Route::delete('mf_cloud/image/:id', "\\server\\mf_cloud\\controller\\admin\\ImageController@imageDelete");
	Route::get('mf_cloud/image/sync', "\\server\\mf_cloud\\controller\\admin\\ImageController@imageSync");
	Route::put('mf_cloud/image/:id/enable', "\\server\\mf_cloud\\controller\\admin\\ImageController@toggleImageEnable");
	Route::delete('mf_cloud/image', "\\server\\mf_cloud\\controller\\admin\\ImageController@imageBatchDelete");
	Route::put('mf_cloud/image/:id/drag', "\\server\\mf_cloud\\controller\\admin\\ImageController@dragToSort");
	Route::get('mf_cloud/local_image/sync', "\\server\\mf_cloud\\controller\\admin\\ImageController@localImageSync");

	// 其他设置
	Route::put('mf_cloud/config', "\\server\\mf_cloud\\controller\\admin\\ConfigController@save");
	Route::get('mf_cloud/config', "\\server\\mf_cloud\\controller\\admin\\ConfigController@index");
	Route::put('mf_cloud/config/disk_limit_enable', "\\server\\mf_cloud\\controller\\admin\\ConfigController@toggleDiskLimitEnable");
	Route::post('mf_cloud/config/check_clear', "\\server\\mf_cloud\\controller\\admin\\ConfigController@checkClear");
	Route::post('mf_cloud/config/disk_num_limit', "\\server\\mf_cloud\\controller\\admin\\ConfigController@saveDiskNumLimitConfig");
	Route::post('mf_cloud/config/free_disk', "\\server\\mf_cloud\\controller\\admin\\ConfigController@saveFreeDiskConfig");
	Route::put('mf_cloud/config/only_sale_recommend_config', "\\server\\mf_cloud\\controller\\admin\\ConfigController@toggleOnlySaleRecommendConfigEnable");
	Route::put('mf_cloud/config/no_upgrade_tip_show', "\\server\\mf_cloud\\controller\\admin\\ConfigController@toggleNoUpgradeTipShowEnable");
	Route::put('mf_cloud/config/global_defence', "\\server\\mf_cloud\\controller\\admin\\ConfigController@saveGlobalDefenceConfig");

	// 线路
	Route::post('mf_cloud/line', "\\server\\mf_cloud\\controller\\admin\\LineController@create");
	Route::put('mf_cloud/line/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@update");
	Route::delete('mf_cloud/line/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@delete");
	Route::get('mf_cloud/line/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@index");
	Route::put('mf_cloud/line/:id/hidden', "\\server\\mf_cloud\\controller\\admin\\LineController@updateHidden");

	Route::post('mf_cloud/line/:id/line_bw', "\\server\\mf_cloud\\controller\\admin\\LineController@lineBwCreate");
	Route::get('mf_cloud/line_bw/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@lineBwIndex");
	Route::put('mf_cloud/line_bw/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@lineBwUpdate");
	Route::delete('mf_cloud/line_bw/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@lineBwDelete");

	Route::post('mf_cloud/line/:id/line_flow', "\\server\\mf_cloud\\controller\\admin\\LineController@lineFlowCreate");
	Route::get('mf_cloud/line_flow/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@lineFlowIndex");
	Route::put('mf_cloud/line_flow/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@lineFlowUpdate");
	Route::delete('mf_cloud/line_flow/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@lineFlowDelete");

	Route::post('mf_cloud/line/:id/line_defence', "\\server\\mf_cloud\\controller\\admin\\LineController@lineDefenceCreate");
	Route::get('mf_cloud/line_defence/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@lineDefenceIndex");
	Route::put('mf_cloud/line_defence/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@lineDefenceUpdate");
	Route::put('mf_cloud/line_defence/:id/drag_sort', "\\server\\mf_cloud\\controller\\admin\\LineController@lineDefenceDragSort");
	Route::delete('mf_cloud/line_defence/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@lineDefenceDelete");
	Route::post('mf_cloud/line/:id/firewall_defence_rule', "\\server\\mf_cloud\\controller\\admin\\LineController@importDefenceRule");

	Route::post('mf_cloud/line/:id/line_ip', "\\server\\mf_cloud\\controller\\admin\\LineController@lineIpCreate");
	Route::get('mf_cloud/line_ip/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@lineIpIndex");
	Route::put('mf_cloud/line_ip/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@lineIpUpdate");
	Route::delete('mf_cloud/line_ip/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@lineIpDelete");

	// Route::post('mf_cloud/line/:id/line_gpu', "\\server\\mf_cloud\\controller\\admin\\LineController@lineGpuCreate");
	// Route::get('mf_cloud/line_gpu/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@lineGpuIndex");
	// Route::put('mf_cloud/line_gpu/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@lineGpuUpdate");
	// Route::delete('mf_cloud/line_gpu/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@lineGpuDelete");

	Route::post('mf_cloud/line/:id/line_ipv6', "\\server\\mf_cloud\\controller\\admin\\LineController@lineIpv6Create");
	Route::get('mf_cloud/line_ipv6/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@lineIpv6Index");
	Route::put('mf_cloud/line_ipv6/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@lineIpv6Update");
	Route::delete('mf_cloud/line_ipv6/:id', "\\server\\mf_cloud\\controller\\admin\\LineController@lineIpv6Delete");

	// 推荐配置
	Route::post('mf_cloud/recommend_config', "\\server\\mf_cloud\\controller\\admin\\RecommendConfigController@create");
	Route::get('mf_cloud/recommend_config', "\\server\\mf_cloud\\controller\\admin\\RecommendConfigController@list");
	Route::put('mf_cloud/recommend_config/:id', "\\server\\mf_cloud\\controller\\admin\\RecommendConfigController@update");
	Route::delete('mf_cloud/recommend_config/:id', "\\server\\mf_cloud\\controller\\admin\\RecommendConfigController@delete");
	Route::get('mf_cloud/recommend_config/:id', "\\server\\mf_cloud\\controller\\admin\\RecommendConfigController@index");
	Route::put('mf_cloud/recommend_config/upgrade_range', "\\server\\mf_cloud\\controller\\admin\\RecommendConfigController@saveUpgradeRange");
	Route::put('mf_cloud/recommend_config/:id/hidden', "\\server\\mf_cloud\\controller\\admin\\RecommendConfigController@updateHidden");
	Route::put('mf_cloud/recommend_config/:id/upgrade_show', "\\server\\mf_cloud\\controller\\admin\\RecommendConfigController@updateUpgradeShow");
	Route::put('mf_cloud/recommend_config/:id/ontrial', "\\server\\mf_cloud\\controller\\admin\\RecommendConfigController@updateOntrial");

 	// 周期比例
	Route::get('mf_cloud/duration_ratio', "\\server\\mf_cloud\\controller\\admin\\DurationController@indexDurationRatio");
	Route::put('mf_cloud/duration_ratio', "\\server\\mf_cloud\\controller\\admin\\DurationController@saveDurationRatio");
	Route::post('mf_cloud/duration_ratio/fill', "\\server\\mf_cloud\\controller\\admin\\DurationController@fillDurationRatio");

	// 限制规则
	Route::post('mf_cloud/limit_rule', "\\server\\mf_cloud\\controller\\admin\\LimitRuleController@create");
	Route::get('mf_cloud/limit_rule', "\\server\\mf_cloud\\controller\\admin\\LimitRuleController@list");
	Route::put('mf_cloud/limit_rule/:id', "\\server\\mf_cloud\\controller\\admin\\LimitRuleController@update");
	Route::delete('mf_cloud/limit_rule/:id', "\\server\\mf_cloud\\controller\\admin\\LimitRuleController@delete");

	// 全局设置
	Route::get('mf_cloud/firewall_defence_rule', "\\server\\mf_cloud\\controller\\admin\\ConfigController@firewallDefenceRule");
	Route::post('mf_cloud/firewall_defence_rule', "\\server\\mf_cloud\\controller\\admin\\ConfigController@importDefenceRule");
	Route::get('mf_cloud/global_defence', "\\server\\mf_cloud\\controller\\admin\\ConfigController@globalDefenceList");
	Route::get('mf_cloud/global_defence/:id', "\\server\\mf_cloud\\controller\\admin\\ConfigController@globalDefenceIndex");
	Route::put('mf_cloud/global_defence/:id', "\\server\\mf_cloud\\controller\\admin\\ConfigController@globalDefenceUpdate");
	Route::delete('mf_cloud/global_defence/:id', "\\server\\mf_cloud\\controller\\admin\\ConfigController@globalDefenceDelete");
	Route::put('mf_cloud/global_defence/:id/drag_sort', "\\server\\mf_cloud\\controller\\admin\\ConfigController@globalDefenceDragSort");

	// 实例操作接口,需要增加新的中间件用来验证权限
	Route::group('', function (){

		Route::post('mf_cloud/:id/on', "\\server\\mf_cloud\\controller\\admin\\CloudController@on");
		Route::post('mf_cloud/:id/off', "\\server\\mf_cloud\\controller\\admin\\CloudController@off");
		Route::post('mf_cloud/:id/reboot', "\\server\\mf_cloud\\controller\\admin\\CloudController@reboot");
		Route::post('mf_cloud/:id/hard_off', "\\server\\mf_cloud\\controller\\admin\\CloudController@hardOff");
		Route::post('mf_cloud/:id/hard_reboot', "\\server\\mf_cloud\\controller\\admin\\CloudController@hardReboot");
		Route::post('mf_cloud/:id/vnc', "\\server\\mf_cloud\\controller\\admin\\CloudController@vnc");
		Route::post('mf_cloud/:id/reset_password', "\\server\\mf_cloud\\controller\\admin\\CloudController@resetPassword");
		Route::post('mf_cloud/:id/rescue', "\\server\\mf_cloud\\controller\\admin\\CloudController@rescue");
		Route::post('mf_cloud/:id/rescue/exit', "\\server\\mf_cloud\\controller\\admin\\CloudController@exitRescue");
		Route::post('mf_cloud/:id/reinstall', "\\server\\mf_cloud\\controller\\admin\\CloudController@reinstall");
		Route::delete('mf_cloud/:id/ip', "\\server\\mf_cloud\\controller\\admin\\CloudController@deleteIp");
		Route::post('mf_cloud/:id/ip', "\\server\\mf_cloud\\controller\\admin\\CloudController@addIp");
		Route::put('mf_cloud/:id/ip', "\\server\\mf_cloud\\controller\\admin\\CloudController@changeIp");

	})->middleware(\app\http\middleware\CheckAdminOperatePassword::class, 'host_operate');  // 需要验证操作密码

	// 实例操作
	Route::get('mf_cloud/:id', "\\server\\mf_cloud\\controller\\admin\\CloudController@adminDetail");
	Route::get('mf_cloud/:id/status', "\\server\\mf_cloud\\controller\\admin\\CloudController@status");
	Route::get('mf_cloud/:id/remote_info', "\\server\\mf_cloud\\controller\\admin\\CloudController@remoteInfo");
	Route::get('mf_cloud/:id/flow', "\\server\\mf_cloud\\controller\\admin\\CloudController@flowDetail");
	Route::get('mf_cloud/:id/ip', "\\server\\mf_cloud\\controller\\admin\\CloudController@getDefaultBwGroupIp");
	Route::get('mf_cloud/:id/ip/free', "\\server\\mf_cloud\\controller\\admin\\CloudController@getFreeIp");
	Route::get('mf_cloud/:id/ip/enable', "\\server\\mf_cloud\\controller\\admin\\CloudController@getEnableIp");

})->allowCrossDomain([
    'Access-Control-Allow-Origin'        => $origin,
    'Access-Control-Allow-Credentials'   => 'true',
    'Access-Control-Max-Age'             => 600,
])->middleware(\app\http\middleware\CheckAdmin::class);



// Route::get(DIR_ADMIN . '/v1/mf_cloud/:id/vnc', "\\server\\mf_cloud\\controller\\admin\\CloudController@vncPage");