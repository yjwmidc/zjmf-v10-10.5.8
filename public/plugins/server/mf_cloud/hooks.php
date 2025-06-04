<?php

use app\common\model\HostIpModel;
use app\common\model\ProductGroupModel;
use app\common\model\ProductModel;
use app\common\model\ServerModel;
use server\mf_cloud\model\DurationModel;
use server\mf_cloud\model\HostLinkModel;
use think\db\exception\PDOException;
use server\mf_cloud\model\BackupConfigModel;
use server\mf_cloud\model\DataCenterModel;
use server\mf_cloud\model\ConfigModel;
use server\mf_cloud\model\DiskLimitModel;
use server\mf_cloud\model\ImageGroupModel;
use server\mf_cloud\model\ImageModel;
use server\mf_cloud\model\LineModel;
use server\mf_cloud\model\OptionModel;
use server\mf_cloud\model\PriceModel;
use server\mf_cloud\model\RecommendConfigModel;
use server\mf_cloud\model\RecommendConfigUpgradeRangeModel;
use server\mf_cloud\model\ResourcePackageModel;
use server\mf_cloud\model\DurationRatioModel;
use server\mf_cloud\model\VpcNetworkModel;
use server\mf_cloud\model\LimitRuleModel;
use server\mf_cloud\model\IpDefenceModel;
use server\mf_cloud\model\HostImageLinkModel;
use app\common\model\OrderItemModel;
use app\common\model\HostModel;

use server\mf_cloud\logic\CloudLogic;

// 商品删除后
add_hook('after_product_delete', function($param){
	if(!isset($param['module']) || $param['module'] != 'mf_cloud'){
		return false;
	}
	try{
		$dataCenterId = DataCenterModel::where('product_id', $param['id'])->column('id');
		$recommendConfigId = RecommendConfigModel::where('product_id', $param['id'])->column('id');

		BackupConfigModel::where('product_id', $param['id'])->delete();
		ConfigModel::where('product_id', $param['id'])->delete();
		DataCenterModel::where('product_id', $param['id'])->delete();
		DiskLimitModel::where('product_id', $param['id'])->delete();
		DurationModel::where('product_id', $param['id'])->delete();
		ImageModel::where('product_id', $param['id'])->delete();
		ImageGroupModel::where('product_id', $param['id'])->delete();
		if(!empty($dataCenterId)){
			LineModel::whereIn('data_center_id', $dataCenterId)->delete();
		}
		OptionModel::where('product_id', $param['id'])->delete();
		PriceModel::where('product_id', $param['id'])->delete();
		RecommendConfigModel::where('product_id', $param['id'])->delete();
		VpcNetworkModel::where('product_id', $param['id'])->delete();
		ResourcePackageModel::where('product_id', $param['id'])->delete();
		if(!empty($recommendConfigId)){
			RecommendConfigUpgradeRangeModel::whereIn('recommend_config_id', $recommendConfigId)->delete();
			RecommendConfigUpgradeRangeModel::whereIn('rel_recommend_config_id', $recommendConfigId)->delete();
		}
		DurationRatioModel::where('product_id', $param['id'])->delete();
		LimitRuleModel::where('product_id', $param['id'])->delete();
	}catch(\PDOException $e){
		
	}catch(\Exception $e){

	}
});

// 产品删除后
add_hook('after_host_delete', function($param){
	if(isset($param['module']) && $param['module'] == 'mf_cloud'){
		HostLinkModel::where('host_id', $param['id'])->delete();
		IpDefenceModel::where('host_id', $param['id'])->delete();
		HostImageLinkModel::where('host_id', $param['id'])->delete();
	}
});

//商品复制后
add_hook('after_product_copy', function($param){
	try{
		$DurationModel = new DurationModel();
		$duration = $DurationModel->where('product_id', $param['product_id'])->select()->toArray();
		if(!empty($duration)){
			$durationIdArr = [];
			foreach ($duration as $key => $value) {
				$id = $value['id'];
				$durationIdArr[$id] = 0;
				unset($value['id']);
				$value['product_id'] = $param['id'];
				$r = $DurationModel->create($value);
				$durationIdArr[$id] = $r->id;
			}

			$DataCenterModel = new DataCenterModel();
			$dataCenter = $DataCenterModel->where('product_id', $param['product_id'])->select()->toArray();
			$dataCenterIdArr = [];
			foreach ($dataCenter as $key => $value) {
				$id = $value['id'];
				$dataCenterIdArr[$id] = 0;
				unset($value['id']);
				$value['product_id'] = $param['id'];
				$r = $DataCenterModel->create($value);
				$dataCenterIdArr[$id] = $r->id;
			}

			$ConfigModel = new ConfigModel();
			$config = $ConfigModel->where('product_id', $param['product_id'])->select()->toArray();
			$configIdArr = [];
			foreach ($config as $key => $value) {
				$id = $value['id'];
				$configIdArr[$id] = 0;
				unset($value['id']);
				$value['product_id'] = $param['id'];
				$r = $ConfigModel->create($value);
				$configIdArr[$id] = $r->id;
			}

			$DiskLimitModel = new DiskLimitModel();
			$diskLimit = $DiskLimitModel->where('product_id', $param['product_id'])->select()->toArray();
			$diskLimitIdArr = [];
			foreach ($diskLimit as $key => $value) {
				$id = $value['id'];
				$diskLimitIdArr[$id] = 0;
				unset($value['id']);
				$value['product_id'] = $param['id'];
				$r = $DiskLimitModel->create($value);
				$diskLimitIdArr[$id] = $r->id;
			}

			$BackupConfigModel = new BackupConfigModel();
			$backupConfig = $BackupConfigModel->where('product_id', $param['product_id'])->select()->toArray();
			$backupConfigIdArr = [];
			foreach ($backupConfig as $key => $value) {
				$id = $value['id'];
				$backupConfigIdArr[$id] = 0;
				unset($value['id']);
				$value['product_id'] = $param['id'];
				$r = $BackupConfigModel->create($value);
				$backupConfigIdArr[$id] = $r->id;
			}

			$ImageGroupModel = new ImageGroupModel();
			$imageGroup = $ImageGroupModel->where('product_id', $param['product_id'])->select()->toArray();
			$imageGroupIdArr = [];
			foreach ($imageGroup as $key => $value) {
				$id = $value['id'];
				$imageGroupIdArr[$id] = 0;
				unset($value['id']);
				$value['product_id'] = $param['id'];
				$r = $ImageGroupModel->create($value);
				$imageGroupIdArr[$id] = $r->id;
			}

			$ImageModel = new ImageModel();
			$image = $ImageModel->where('product_id', $param['product_id'])->select()->toArray();
			$imageIdArr = [];
			foreach ($image as $key => $value) {
				$id = $value['id'];
				$imageIdArr[$id] = 0;
				unset($value['id']);
				$value['product_id'] = $param['id'];
				$value['image_group_id'] = $imageGroupIdArr[$value['image_group_id']] ?? 0;
				$r = $ImageModel->create($value);
				$imageIdArr[$id] = $r->id;
			}

			$LineModel = new LineModel();
			$line = $LineModel->whereIn('data_center_id', array_keys($dataCenterIdArr))->select()->toArray();
			$lineIdArr = [];
			foreach ($line as $key => $value) {
				$id = $value['id'];
				$lineIdArr[$id] = 0;
				unset($value['id']);
				$value['data_center_id'] = $dataCenterIdArr[$value['data_center_id']] ?? 0;
				$r = $LineModel->create($value);
				$lineIdArr[$id] = $r->id;
			}

			$OptionModel = new OptionModel();
			$option = $OptionModel->where('product_id', $param['product_id'])->select()->toArray();
			$optionIdArr = [];
			foreach ($option as $key => $value) {
				$id = $value['id'];
				$optionIdArr[$id] = 0;
				unset($value['id']);
				$value['product_id'] = $param['id'];
				if(in_array($value['rel_type'], [2, 3, 4, 5, 8, 9])){
					$value['rel_id'] = $lineIdArr[$value['rel_id']] ?? 0;
				}
				if(in_array($value['rel_type'], [10])){
					$value['rel_id'] = $dataCenterIdArr[$value['rel_id']] ?? 0;
				}
				$r = $OptionModel->create($value);
				$optionIdArr[$id] = $r->id;
			}

			$RecommendConfigModel = new RecommendConfigModel();
			$recommendConfig = $RecommendConfigModel->where('product_id', $param['product_id'])->select()->toArray();
			$recommendConfigIdArr = [];
			foreach ($recommendConfig as $key => $value) {
				$id = $value['id'];
				$recommendConfigIdArr[$id] = 0;
				unset($value['id']);
				$value['product_id'] = $param['id'];
				$value['data_center_id'] = $dataCenterIdArr[$value['data_center_id']] ?? 0;
				$value['line_id'] = $lineIdArr[$value['line_id']] ?? 0;
				$r = $RecommendConfigModel->create($value);
				$recommendConfigIdArr[$id] = $r->id;
			}

			$RecommendConfigUpgradeRangeModel = new RecommendConfigUpgradeRangeModel();
			$recommendConfigUpgrade = $RecommendConfigUpgradeRangeModel->whereIn('recommend_config_id', array_keys($recommendConfigIdArr))->select()->toArray();
			$recommendConfigUpgradeIdArr = [];
			foreach ($recommendConfigUpgrade as $key => $value) {
				$value['recommend_config_id'] = $recommendConfigIdArr[$value['recommend_config_id']] ?? 0;
				$value['rel_recommend_config_id'] = $recommendConfigIdArr[$value['rel_recommend_config_id']] ?? 0;
				$RecommendConfigUpgradeRangeModel->insert($value);
			}

			$PriceModel = new PriceModel();
			$price = $PriceModel->where('product_id', $param['product_id'])->select()->toArray();
			$priceIdArr = [];
			foreach ($price as $key => $value) {
				$id = $value['id'];
				$priceIdArr[$id] = 0;
				unset($value['id']);
				$value['product_id'] = $param['id'];
				if($value['rel_type'] == 0){
					$value['rel_id'] = $optionIdArr[$value['rel_id']] ?? 0;
				}else if($value['rel_type'] == 1){
					$value['rel_id'] = $recommendConfigIdArr[$value['rel_id']] ?? 0;
				}else{
					continue;
				}
				$value['duration_id'] = $durationIdArr[$value['duration_id']] ?? 0;
				$r = $PriceModel->create($value);
				$priceIdArr[$id] = $r->id;
			}

			$ResourcePackageModel = new ResourcePackageModel();
			$resourcePackage = $ResourcePackageModel->where('product_id', $param['product_id'])->select()->toArray();
			$resourcePackageIdArr = [];
			foreach ($resourcePackage as $key => $value) {
				$id = $value['id'];
				$resourcePackageIdArr[$id] = 0;
				unset($value['id']);
				$value['product_id'] = $param['id'];
				$r = $ResourcePackageModel->create($value);
				$resourcePackageIdArr[$id] = $r->id;
			}

			// 获取周期比例
			$DurationRatioModel = new DurationRatioModel();
			$durationRatio = $DurationRatioModel->where('product_id', $param['product_id'])->select()->toArray();
			foreach ($durationRatio as $key => $value) {
				if(isset($durationIdArr[$value['duration_id']])){
					$value['product_id'] = $param['id'];
					$value['duration_id'] = $durationIdArr[$value['duration_id']];
					$DurationRatioModel->create($value);
				}
			}

            // 限制规则
			$LimitRuleModel = new LimitRuleModel();
			$limitRule = $LimitRuleModel->where('product_id', $param['product_id'])->select()->toArray();
			foreach ($limitRule as $key => $value) {
				$id = $value['id'];
				unset($value['id']);
				$value['product_id'] = $param['id'];
				$value['rule'] = json_decode($value['rule'], true);
				$value['result'] = json_decode($value['result'], true);
                if(isset($value['rule']['data_center']['id'])){
                    foreach($value['rule']['data_center']['id'] as $kk=>$vv){
                        if(isset($dataCenterIdArr[$vv])){
                            $value['rule']['data_center']['id'][$kk] = (int)$dataCenterIdArr[$vv];
                        }else{
                            unset($value['rule']['data_center']['id'][$kk]);
                        }
                    }
                    $value['rule']['data_center']['id'] = array_values($value['rule']['data_center']['id']);
                }
                if(isset($value['rule']['image']['id'])){
                    foreach($value['rule']['image']['id'] as $kk=>$vv){
                        if(isset($imageIdArr[$vv])){
                            $value['rule']['image']['id'][$kk] = (int)$imageIdArr[$vv];
                        }else{
                            unset($value['rule']['image']['id'][$kk]);
                        }
                    }
                    $value['rule']['image']['id'] = array_values($value['rule']['image']['id']);
                }
                if(isset($value['result']['image'])){
                	foreach($value['result']['image'] as $kk=>$resultItem){
                		foreach($resultItem['id'] as $kkk=>$vvv){
	                        if(isset($imageIdArr[$vvv])){
	                            $value['result']['image'][$kk]['id'][$kkk] = (int)$imageIdArr[$vvv];
	                        }else{
	                            unset($value['result']['image'][$kk]['id'][$kkk]);
	                        }
	                    }
	                    $value['result']['image'][$kk]['id'] = array_values($value['result']['image'][$kk]['id']);
                	}
                }
                // if(isset($value['rule']['recommend_config']['id'])){
                //     foreach($value['rule']['recommend_config']['id'] as $kk=>$vv){
                //         if(isset($recommendConfigIdArr[$vv])){
                //             $value['rule']['recommend_config']['id'][$kk] = $recommendConfigIdArr[$vv];
                //         }else{
                //             unset($value['rule']['recommend_config']['id'][$kk]);
                //         }
                //     }
                //     $value['rule']['recommend_config']['id'] = array_values($value['rule']['recommend_config']['id']);
                // }
                // if(isset($value['rule']['duration']['id'])){
                //     foreach($value['rule']['duration']['id'] as $kk=>$vv){
                //         if(isset($durationIdArr[$vv])){
                //             $value['rule']['duration']['id'][$kk] = $durationIdArr[$vv];
                //         }else{
                //             unset($value['rule']['duration']['id'][$kk]);
                //         }
                //     }
                //     $value['rule']['duration']['id'] = array_values($value['rule']['duration']['id']);
                // }
                $value['rule'] = json_encode($value['rule']);
                $value['result'] = json_encode($value['result']);
                $value['rule_md5'] = md5($value['rule']);
				$r = $LimitRuleModel->create($value);
			}

		}
	}catch(\Exception $e){
		return $e->getMessage();
	}
});

// 购买流量包后
add_hook('flow_packet_order_paid', function($param){
	$hostId = $param['host_id'];
	$flow = $param['flow_packet']['capacity'];
	$moduleParam = $param['module_param'];

	if(!empty($moduleParam['server']) && $moduleParam['server']['module'] == 'mf_cloud'){
		$hash = \server\mf_cloud\logic\ToolLogic::formatParam($moduleParam['server']['hash']);

		$idcsmartCloud = new \server\mf_cloud\idcsmart_cloud\IdcsmartCloud($moduleParam['server']);
		$idcsmartCloud->setIsAgent(isset($hash['account_type']) && $hash['account_type'] == 'agent');

		$hostLink = HostLinkModel::where('host_id', $hostId)->find();
		$res = $idcsmartCloud->cloudIncTempTraffic($hostLink['rel_id'] ?? 0, (int)$flow);
		if($res['status'] == 200){
			$description = lang_plugins('log_mf_cloud_buy_flow_packet_success', [
				'{host}'	=> 'host#'.$hostId.'#'.$moduleParam['host']['name'].'#',
				'{order}' 	=> '#'.$param['order_id'],
				'{flow}' 	=> $flow.'G',
			]);

	        // 如果是流量暂停在检查流量
	        if($moduleParam['host']['status'] == 'Suspended' && $moduleParam['host']['suspend_type'] == 'overtraffic'){
		        if($moduleParam['host']['due_time'] == 0 || time() < $moduleParam['host']['due_time']){
		        	$res = $idcsmartCloud->netInfo($hostLink['rel_id']);
		            if($res['status'] == 'success' && $res['data']['info']['30_day']['float'] < 100){
		                //执行解除暂停
		                $result = $moduleParam['host']->unsuspendAccount($hostId);
		                if ($result['status'] == 200){
	                        $description .= lang_plugins('log_mf_cloud_buy_flow_packet_and_unsuspend_success');
	                    }else{
	                        $description .= lang_plugins('log_mf_cloud_buy_flow_packet_but_unsuspend_fail', ['{reason}'=>$result['msg']]);
	                    }
		            }
		        }
	        }
	    }else{
	    	$description = lang_plugins('log_mf_cloud_buy_flow_packet_remote_add_fail', [
	    		'{host}'	=> 'host#'.$hostId.'#'.$moduleParam['host']['name'].'#',
	    		'{order}'	=> '#'.$param['order_id'],
	    		'{flow}'	=> $flow.'G',
	    	]);
	    }
	    // 记录日志
	    active_log($description, 'host', $hostId);
	}
});

// 在购买流量包之前
add_hook('flow_packet_before_order', function($param){
	try{
		$hostLink = HostLinkModel::where('host_id', $param['host']['id'])->find();
		if(!empty($hostLink)){
			$configData = json_decode($hostLink['config_data'], true);
			if(isset($configData['line']['bill_type']) && $configData['line']['bill_type'] !== 'flow'){
				// 不是流量线路,不能购买
				return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_cannot_buy_flow_packet')];
			}
		}
	}catch(PDOException $e){

	}
});

// 获取产品转移信息
add_hook('host_transfer_info', function($param){
	if($param['module'] == 'mf_cloud'){
		$HostLinkModel = new HostLinkModel();
		return $HostLinkModel->hostTransferInfo($param);
	}
});

// 产品转移
add_hook('host_transfer', function($param){
	if($param['module'] == 'mf_cloud'){
		$HostLinkModel = new HostLinkModel();
		return $HostLinkModel->hostTransfer($param);
	}
});

// 在产品转移之前
add_hook('before_host_transfer', function($param){
	if($param['module'] == 'mf_cloud'){
		$HostLinkModel = new HostLinkModel();
		return $HostLinkModel->beforeHostTransfer($param);
	}
});

// 产品转移
add_hook('order_paid', function($param){
	try{
		$OrderItemModel = new OrderItemModel();
		$orderItem = $OrderItemModel->where('order_id', $param['id'])->where('type', 'upgrade')->find();
		if(!empty($orderItem) && isset($orderItem['host_id']) && isset($orderItem['product_id'])){
			$hostLink = HostLinkModel::where('host_id', $orderItem['host_id'])->find();
			if(!empty($hostLink)){
				$ConfigModel = new ConfigModel();
	            $config = $ConfigModel->indexConfig(['product_id' => $orderItem['product_id']]);

	            if(isset($config['data']['manual_manage']) && $config['data']['manual_manage']==1){
					system_notice([
						'name'                  => 'host_module_action',
						'email_description'     => lang('host_module_action'),
						'sms_description'       => lang('host_module_action'),
						'task_data' => [
							'client_id' => $orderItem['client_id'],
							'host_id'	=> $orderItem['id'],
							'template_param'=>[
								'module_action' => lang_plugins('upgrade'),
							],
						],
					]);

	                $ManualResourceLogModel = new \addon\manual_resource\model\ManualResourceLogModel();
	                $ManualResourceLogModel->createLog([
	                    'host_id'                   => $orderItem['host_id'],
	                    'type'                      => 'upgrade',
	                    'client_id'                 => $orderItem['client_id'],
	                    'data'						=> [
	                    	'desc' => $orderItem['description'],
	                    ]
	                ]);

	                $host = HostModel::find($orderItem['host_id']);

	                $description = lang_plugins('log_host_start_upgrade_in_progress', ['{hostname}'=>$host['name']]);

	                active_log($description, 'host', $orderItem['host_id']);
	            }
			}
		}
	}catch(PDOException $e){

	}
});

// 申请停用之前
add_hook('before_host_refund', function($param){
	try{
		$hostLink = HostLinkModel::where('host_id', $param['host_id'])->find();
		if(!empty($hostLink)){
			$productId = HostModel::where('id', $param['host_id'])->value('product_id');
			$configData = json_decode($hostLink['config_data'], true);

			// 获取当前周期
			$duration = DurationModel::where('product_id', $productId)->where('num', $configData['duration']['num'])->where('unit', $configData['duration']['unit'])->find();
			if(!empty($duration) && $duration['support_apply_for_suspend'] == 0){
				return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_host_duration_not_support_apply_for_suspend') ];
			}
		}
	}catch(PDOException $e){

	}
});

// 续费之前
add_hook('before_host_renew', function($param){
	try{
		$CloudLogic = new CloudLogic($param['host_id']);
		$result = $CloudLogic->whetherRenew();

		return $result;
	}catch(PDOException $e){

	}catch(\Exception $e){

    }
});

// 订单支付前
add_hook('before_order_pay', function($param){
	if($param['order']['type'] != 'renew'){
		return false;
	}
	$OrderItemModel = new OrderItemModel();
	$hostId = $OrderItemModel
			->where('order_id', $param['order']['id'])
			->where('host_id', '>', 0)
			->value('host_id');
	if(!empty($hostId)){
		try{
            $CloudLogic = new CloudLogic($hostId);
            $result = $CloudLogic->whetherRenew();

            return $result;
        }catch(PDOException $e){

        }catch(\Exception $e){
        
        }
	}
});

// 检查迁移任务,1分钟任务
add_hook('minute_cron', function(){
	try{
		$HostLinkModel = new HostLinkModel();
		$data = $HostLinkModel
				->field('hl.host_id')
				->alias('hl')
				->join('host h', 'hl.host_id=h.id')
				->where('hl.migrate_task_id', '>', 0)
				->select()
				->toArray();

	}catch(\Exception $e){
		$data = [];
	}
	if(empty($data)){
		return false;
	}
	foreach($data as $v){
		try{
            $CloudLogic = new CloudLogic($v['host_id']);
            $CloudLogic->checkMigrateTask();
        }catch(PDOException $e){

        }catch(\Exception $e){
        
        }
	}
});

// 在代理防火墙IP同步后
add_hook('after_create_firewall_agent_host_ip', function($param){
	try{
		$HostLinkModel = new HostLinkModel();
		$HostLinkModel->afterCreateFirewallAgentHostIp($param);
	}catch(\Exception $e){

	}
});

// 需求同步至下游的模块相关信息
add_hook('push_downstream_module_info',function ($param){
    $id = $param['id']??0;
    $host = HostModel::find($id);
    $upstreamHost = \app\common\model\UpstreamHostModel::where('host_id',$id)->find();
    if (!empty($upstreamHost)){
        $UpstreamProductModel = new \app\common\model\UpstreamProductModel();
        $upstreamProduct = $UpstreamProductModel->where('product_id',$host['product_id'])->find();
        if (!empty($upstreamProduct)){
            if ($upstreamProduct['mode']=='sync'){
                $product = \app\common\model\ProductModel::where('id',$host['product_id']??0)->find();
                $module = $product->getModule();
            }else{
                $module = $upstreamProduct['res_module'];
            }
            if ($module=='mf_cloud'){
                $param['module_info'] = HostLinkModel::where('parent_host_id',$id)
                    ->field('host_id,ip,config_data')
                    ->select()
                    ->toArray();
                // 若产品是子产品，则替换为主产品的host_ip
                if (!empty($host['is_sub'])){
                    $parentHostId = HostLinkModel::where('host_id',$id)->value('parent_host_id');
                    if (!empty($parentHostId)){
                        $HostIpModel = new HostIpModel();
                        $param['host_ip'] = $HostIpModel->getHostIp(['host_id'=>$parentHostId,'client_id'=>$host['client_id']]);
                    }
                }
            }
        }
    }else{
        if($host->getModule() == 'mf_cloud'){
            $param['module_info'] = HostLinkModel::where('parent_host_id',$id)
                ->field('host_id,ip,config_data')
                ->select()
                ->toArray();
            // 若产品是子产品，则替换为主产品的host_ip
            if (!empty($host['is_sub'])){
                $parentHostId = HostLinkModel::where('host_id',$id)->value('parent_host_id');
                if (!empty($parentHostId)){
                    $HostIpModel = new HostIpModel();
                    $param['host_ip'] = $HostIpModel->getHostIp(['host_id'=>$parentHostId,'client_id'=>$host['client_id']]);

                    // TODO 旧数据处理
                    $parentHost = HostModel::where('id',$parentHostId)->find();
                    $subHostLink = HostLinkModel::where('host_id',$id)->find();
                    $ipDefence = IpDefenceModel::where('host_id',$id)
                        ->where('ip',$subHostLink['ip']??'')
                        ->find();
                    if ($ipDefence){
                        $tmp = explode('_',$ipDefence['defence']);
                        $defaultDefenceId = $tmp[2];
                        $firewallType = $tmp[0] . '_' . $tmp[1];
                    }
                    $param['down_parent_host_id'] = $parentHost['downstream_host_id']??0;
                    $param['parent_host_id'] = $parentHostId;
                    $param['other_info'] = [
                        'ip' => $subHostLink['ip']??'',
                        'firewall_type' => $firewallType??'',
                        'default_defence_id' => $defaultDefenceId??0,
                        'due_time' => $host['due_time']??0,
                    ];
                }
            }
        }
    }
});

// 修改商品按需计费配置前
// add_hook('before_product_on_demand_update', function($param){
// 	if($param['module'] == 'mf_cloud'){
// 		// 检查周期是否正确
// 		$DurationModel = new DurationModel();
// 		$duration = $DurationModel
// 					->where('id', $param['param']['duration_id'])
// 					->where('product_id', $param['param']['product_id'])
// 					->find();
// 		if(empty($duration)){
// 			return ['status'=>400, 'msg'=>lang_plugins('duration_not_found') ];
// 		}
// 	}
// });