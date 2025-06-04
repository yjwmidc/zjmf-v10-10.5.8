<?php 

use app\common\model\OrderItemModel;
use app\common\model\ProductModel;
use app\common\model\UpstreamProductModel;
use reserver\mf_cloud\logic\RouteLogic;

// 产品转移
add_hook('host_transfer', function($param){
	if(isset($param['res_module']) && $param['res_module'] == 'mf_cloud' && !empty($param['upstream_host'])){
		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['host']['id']);
			$RouteLogic->clientId = $param['target_client']['id'];
		}catch(\Exception $e){
			return [];
		}

		if(!$RouteLogic->isOnlyApi){
			return [];
		}
		
		$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d', $RouteLogic->upstream_host_id), [], 'GET');
		if($result['status'] != 200){
			return $result;
		}
		if(isset($result['data']['vpc_network']['id'])){
			// 是VPC,新建VPC并切换为新的VPC
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/vpc_network', $RouteLogic->upstream_host_id), ['name'=>$result['data']['vpc_network']['name'], 'ips'=>$result['data']['vpc_network']['ips'] ?? '10.0.0.0/16'], 'POST');
			if($result['status'] == 200){
				$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/vpc_network', $RouteLogic->upstream_host_id), ['vpc_network_id'=>$result['data']['id'] ], 'PUT');
			}
			return $result;
		}
	}
});

// 产品转移
add_hook('before_host_transfer', function($param){
	if(isset($param['res_module']) && $param['res_module'] == 'mf_cloud'){
		try{
			$RouteLogic = new RouteLogic();
			$RouteLogic->routeByHost($param['host']['id']);
			$RouteLogic->clientId = $param['target_client']['id'];
		}catch(\Exception $e){
			return [];
		}

		$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d', $RouteLogic->upstream_host_id), [], 'GET');
		if($result['status'] != 200){
			return $result;
		}
		if(isset($result['data']['vpc_network']['id'])){
			$result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/status', $RouteLogic->upstream_host_id), [], 'GET');
			if($result['status'] != 200){
				return $result;
			}
			if($result['data']['status'] == 'operating'){
				return ['status'=>400, 'msg'=>lang_plugins('res_mf_cloud_host_operate_cannot_transfer')];
			}

		}
	}
});

// 续费之前
add_hook('before_host_renew', function($param){
	try{
		$RouteLogic = new RouteLogic();
        $RouteLogic->routeByHost($param['host_id']);

        $result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/whether_renew', $RouteLogic->upstream_host_id), [], 'GET');

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
            $RouteLogic = new RouteLogic();
	        $RouteLogic->routeByHost($hostId);

	        $result = $RouteLogic->curl( sprintf('console/v1/remf_cloud/%d/whether_renew', $RouteLogic->upstream_host_id), [], 'GET');

            return $result;
        }catch(PDOException $e){

        }catch(\Exception $e){
        
        }
	}
});


// 创建host link 关联ip
add_hook("upstream_push_after_module_create",function ($param){
    $HostModel = new \app\common\model\HostModel();
    $host = $HostModel->where('id',$param['host_id'])->find();
    $UpstreamHostModel = new \app\common\model\UpstreamHostModel();
    $upstreamProduct = UpstreamProductModel::where('product_id',$host['product_id']??0)->find();
    $IpDefenceModel = new \server\mf_cloud\model\IpDefenceModel();
    if (!empty($upstreamProduct)){
        if ($upstreamProduct['mode']=='sync'){
            $product = ProductModel::where('id',$host['product_id']??0)->find();
            $module = $product->getModule();
        }else{
            $module = $upstreamProduct['res_module'];
        }
        if ($module=='mf_cloud' && class_exists('\\server\\mf_cloud\\model\\HostLinkModel')){
            $HostLinkModel = new \server\mf_cloud\model\HostLinkModel();
            $hostLInks = $HostLinkModel->where('parent_host_id',$host['id'])
                ->select()
                ->toArray();
            if (!empty($ip = $param['data']['host_ip'])){
                if(!empty($ip['assign_ip']) && !empty($ip['dedicate_ip']) ){
                    $ips = explode(',', $ip['assign_ip']);
                    $ips[] = $ip['dedicate_ip'];
                }else if(!empty($ip['dedicate_ip'])){
                    $ips = [ $ip['dedicate_ip'] ];
                }else{
                    $ips = [];
                }
                $time = time();
                $moduleInfo = $param['data']['module_info']??[];
                $map = [];
                $mapConfigData = [];
                foreach ($moduleInfo as $item){
                    $map[$item['ip']??''] = $item['host_id']??0;
                    $mapConfigData[$item['ip']??''] = $item['config_data']??[];
                }
                foreach ($hostLInks as $k=>$v){
                    $HostLinkModel->where('id',$v['id'])
                        ->update([
                            'ip' => $ips[$k]??'',
                            'update_time' => $time,
                        ]);
                    // 开通子产品
                    $HostModel->where('id',$v['host_id'])->update([
                        'status'      => 'Active',
                    ]);
                    // 更新上下游产品关联
                    $UpstreamHostModel->where('host_id',$v['host_id'])
                        ->update([
                            'upstream_host_id' => $map[$ips[$k]??'']??0,
                            'update_time' => $time,
                        ]);
                    $configData = json_decode($mapConfigData[$ips[$k]??'']??[],true);
                    $IpDefenceModel->insert([
                        'host_id' => $v['host_id'],
                        'ip' => $ips[$k]??'',
                        'defence' => $configData['defence']['value']??''
                    ]);
                }
            }
        }
    }
});

// 升降级附加ip才调用此钩子，升降级防御时不处理
add_hook('upstream_push_after_update_host',function ($param){
    if (isset($param['data']['type']) && $param['data']['type']=='upgrade_ip_num'){
        $HostModel = new \app\common\model\HostModel();
        $host = $HostModel->where('id',$param['host_id'])->find();
        $UpstreamHostModel = new \app\common\model\UpstreamHostModel();
        $upstreamProduct = UpstreamProductModel::where('product_id',$host['product_id']??0)->find();
        if (!empty($upstreamProduct)){
            if ($upstreamProduct['mode']=='sync'){
                $product = ProductModel::where('id',$host['product_id']??0)->find();
                $module = $product->getModule();
            }else{
                $module = $upstreamProduct['res_module'];
            }
            if ($module=='mf_cloud' && class_exists('\\server\\mf_cloud\\model\\HostLinkModel')){
                $upstreamHost = $UpstreamHostModel->where('host_id',$param['host_id'])->find();
                $HostLinkModel = new \server\mf_cloud\model\HostLinkModel();
                $moduleInfo = $param['data']['module_info']??[];
                $map = [];
                foreach ($moduleInfo as $item){
                    $map[$item['ip']??''] = $item['host_id']??0;
                }
                $time = time();
                if (!empty($moduleInfo[0]['config_data'])){
                    $configData = json_decode($moduleInfo[0]['config_data'],true);
                    $firewallType = $configData['defence']['firewall_type'];
                    $defaultDefenceId = str_replace($firewallType.'_','', $configData['line']['order_default_defence']??'');
                }
                try {
                    $CloudLogic = new \server\mf_cloud\logic\CloudLogic($param['host_id'],true);
                    $CloudLogic->ipChange([
                        'ips' => array_keys($map),
                        'agent' => !($upstreamProduct['mode'] == 'sync'),
                        'firewall_type' => $firewallType??'aodun_firewall',
                        'default_defence_id' => $defaultDefenceId??0,
                    ]);
                    // 获取本地数据
                    $hostLInks = $HostLinkModel->where('parent_host_id',$host['id'])
                        ->select()
                        ->toArray();
                    $ips = array_keys($map);
                    foreach ($hostLInks as $k=>$v){
                        // 更新上下游产品关联
                        $exist = $UpstreamHostModel->where('host_id',$v['host_id'])->find();
                        if (!empty($exist)){
                            $UpstreamHostModel->where('host_id',$v['host_id'])
                                ->update([
                                    'upstream_host_id' => $map[$ips[$k]??'']??0,
                                    'update_time' => $time,
                                ]);
                        }else{
                            $UpstreamHostModel->insert([
                                'supplier_id' => $upstreamHost['supplier_id'],
                                'host_id' => $v['host_id'],
                                'upstream_host_id' => $map[$ips[$k]??'']??0,
                                'upstream_configoption' => '',
                                'create_time' => $time,
                            ]);
                        }
                    }
                }catch (\Exception $e){
                }

            }
        }
    }
    if (isset($param['data']['type']) && $param['data']['type']=='upgrade_defence'){
        $HostModel = new \app\common\model\HostModel();
        $host = $HostModel->where('id',$param['host_id'])->find();
        $UpstreamHostModel = new \app\common\model\UpstreamHostModel();
        $upstreamProduct = UpstreamProductModel::where('product_id',$host['product_id']??0)->find();
        if (!empty($upstreamProduct)){
            if ($upstreamProduct['mode']=='sync'){
                $product = ProductModel::where('id',$host['product_id']??0)->find();
                $module = $product->getModule();
            }else{
                $module = $upstreamProduct['res_module'];
            }
            if ($module=='mf_cloud' && class_exists('\\server\\mf_cloud\\model\\HostLinkModel')){
                $HostLinkModel = new \server\mf_cloud\model\HostLinkModel();
                $subHostIds = $HostLinkModel->where('parent_host_id',$host['id'])->column('host_id');
                $upstreamHost = $UpstreamHostModel->whereIn('host_id',$subHostIds)
                    ->select()
                    ->toArray();
                $map = array_column($upstreamHost,'host_id','upstream_host_id');
                $currentSubHostId = $map[$param['data']['host']['id']??0]??0;
                $HostModel->update([
                    'base_info'     => $param['data']['host']['base_info'] ?? '',
                    'status' => $param['data']['host']['status'],
                    'due_time' => $param['data']['host']['due_time'],
                    'update_time' => time()
                ], ['id' => $currentSubHostId]);
            }
        }

    }
});

// 续费
add_hook('upstream_push_after_host_renew',function ($param){
    $HostModel = new \app\common\model\HostModel();
    $host = $HostModel->where('id',$param['host_id'])->find();
    $UpstreamHostModel = new \app\common\model\UpstreamHostModel();
    $upstreamProduct = UpstreamProductModel::where('product_id',$host['product_id']??0)->find();
    if (!empty($upstreamProduct)){
        if ($upstreamProduct['mode']=='sync'){
            $product = ProductModel::where('id',$host['product_id']??0)->find();
            $module = $product->getModule();
        }else{
            $module = $upstreamProduct['res_module'];
        }
        if ($module=='mf_cloud' && class_exists('\\server\\mf_cloud\\model\\HostLinkModel')){
            $HostLinkModel = new \server\mf_cloud\model\HostLinkModel();
            $subHostIds = $HostLinkModel->where('parent_host_id',$host['id'])->column('host_id');
            $upstreamHost = $UpstreamHostModel->whereIn('host_id',$subHostIds)
                ->select()
                ->toArray();
            $map = array_column($upstreamHost,'host_id','upstream_host_id');
            $currentSubHostId = $map[$param['data']['host']['id']??0]??0;
            $HostModel->update([
                'base_info'     => $param['data']['host']['base_info'] ?? '',
                'status' => $param['data']['host']['status'],
                'due_time' => $param['data']['host']['due_time'],
                'update_time' => time()
            ], ['id' => $currentSubHostId]);
        }
    }
});

add_hook('before_module_host_terminate',function ($param){
    $HostModel = new \app\common\model\HostModel();
    $host = $HostModel->where('id',$param['id'])->find();
    $UpstreamHostModel = new \app\common\model\UpstreamHostModel();
    $upstreamProduct = UpstreamProductModel::where('product_id',$host['product_id']??0)->find();
    if (!empty($upstreamProduct)){
        if ($upstreamProduct['mode']=='sync'){
            $product = ProductModel::where('id',$host['product_id']??0)->find();
            $module = $product->getModule();
        }else{
            $module = $upstreamProduct['res_module'];
        }
        if ($module=='mf_cloud' && class_exists('\\server\\mf_cloud\\model\\HostLinkModel')){
            $HostLinkModel = new \server\mf_cloud\model\HostLinkModel();
            $subHostIds = $HostLinkModel->where('parent_host_id',$host['id'])->column('host_id');
            $HostModel->whereIn('id',$subHostIds)->update([
                'status' => 'Deleted',
            ]);
            if (!empty($host['is_sub'])){
                $HostModel->where('id',$host['id'])->update([
                    'status' => 'Deleted',
                ]);
            }
        }
    }
});

// TODO 处理飞讯旧数据
add_hook('upstream_sync_host',function ($param){
    if (!empty($param['down_parent_host_id'])){
        $HostModel = new \app\common\model\HostModel();
        $host = $HostModel->where('id',$param['down_parent_host_id'])->find();
        $UpstreamHostModel = new \app\common\model\UpstreamHostModel();
        $upstreamProduct = UpstreamProductModel::where('product_id',$host['product_id']??0)->find();
        if (!empty($upstreamProduct)){
            // 仅处理接口代理
            $module = $upstreamProduct['res_module'];
            if ($module=='mf_cloud'){
                $upstreamHost = $UpstreamHostModel->where('host_id', $param['down_parent_host_id'])->find();
                if (!empty($upstreamHost)){
                    $SupplierModel = new \app\common\model\SupplierModel();
                    $supplier = $SupplierModel->where('id', $upstreamProduct['supplier_id'])->find();
                    if (!empty($supplier)){
                        $param['data'] = (new \app\common\logic\UpstreamLogic())->rsaDecrypt($param['data'], aes_password_decode($supplier['secret']));
                        $data = json_decode($param['data'], true);
                        $ip = $data['other_info']['ip']??'';
                        if (!empty($ip)){
                            try {
                                $CloudLogic = new \server\mf_cloud\logic\CloudLogic($param['down_parent_host_id'],true);
                                $CloudLogic->ipChange([
                                    'ips' => [$ip],
                                    'agent' => true,
                                    'only_create' => true,
                                    'firewall_type' => $data['other_info']['firewall_type']??'',
                                    'default_defence_id' => $data['other_info']['default_defence_id']??'',
                                ]);
                                $subHostLink = \server\mf_cloud\model\HostLinkModel::where('ip',$ip)
                                    ->where('parent_host_id',$param['down_parent_host_id'])
                                    ->find();
                                if (!empty($subHostLink)){
                                    $UpstreamHostModel->where('host_id',$subHostLink['host_id'])->update([
                                        'upstream_host_id' => $data['host']['id']??0
                                    ]);
                                    $IpDefenceModel = new \server\mf_cloud\model\IpDefenceModel();
                                    $ipDefence = $IpDefenceModel->where('host_id',$subHostLink['host_id'])
                                        ->where('ip',$ip)
                                        ->find();
                                    if (!empty($ipDefence)){
                                        $ipDefence->save([
                                            'defence' => $data['other_info']['firewall_type'].'_'.$data['other_info']['default_defence_id']
                                        ]);
                                    }else{
                                        $IpDefenceModel->insert([
                                            'host_id' => $subHostLink['host_id'],
                                            'ip' => $ip,
                                            'defence' => $data['other_info']['firewall_type'].'_'.$data['other_info']['default_defence_id']
                                        ]);
                                    }
                                    $renewPrice = $data['renew_price']??0;
                                    $upstreamProduct = UpstreamProductModel::where('product_id', $host['product_id'])->where('mode','only_api')->find();
                                    if (!empty($upstreamProduct)){
                                        if ($upstreamProduct['renew_profit_type']==1){
                                            $renewPrice = bcadd($renewPrice, $upstreamProduct['renew_profit_percent'],2);
                                        }else{
                                            $renewPrice = bcmul($renewPrice, ($upstreamProduct['renew_profit_percent']+100)/100,2);
                                        }
                                    }
                                    $clientLevel = $CloudLogic->getClientLevel([
                                        'client_id' => $host['client_id'],
                                        'product_id' => $host['product_id'],
                                    ]);
                                    if (!empty($clientLevel)){
                                        $discount = bcdiv($renewPrice*$clientLevel['discount_percent'], 100, 2);
                                        $renewPrice = bcsub($renewPrice,$discount,2);
                                    }
                                    $update = [
                                        'renew_amount' => $renewPrice,
                                    ];
                                    if (!empty($data['other_info']['due_time'])){
                                        $update['due_time'] = $data['other_info']['due_time'];
                                    }
                                    $HostModel->where('id',$subHostLink['host_id'])->update($update);
                                }

                            }catch (\Exception $e){

                            }
                        }
                    }
                }
            }
        }
    }
});