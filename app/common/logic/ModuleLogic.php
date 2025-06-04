<?php 
namespace app\common\logic;

use app\common\model\HostModel;
use app\common\model\ProductModel;
use app\common\model\ServerModel;
use app\admin\model\PluginModel;
use app\common\model\UpstreamProductModel;
use think\facade\View;

/**
 * @title 模块逻辑
 * @desc 模块逻辑
 * @use  app\common\logic\ModuleLogic
 */
class ModuleLogic
{
	// 模块目录
	protected $path = WEB_ROOT . 'plugins/server/';

	/**
	 * 时间 2022-05-27
	 * @title 获取模块列表
	 * @desc 获取模块列表
	 * @author hh
	 * @version v1
	 * @return  string [].name - 模块名称
	 * @return  string [].display_name - 模块显示名称
	 * @return  string [].version - 版本号
	 */
	public function getModuleList(): array
	{
		$modules = [];
		if(is_dir($this->path)){
		    if($handle = opendir($this->path)){
		        while(($file = readdir($handle)) !== false){
		        	if($file != '.' && $file != '..' && is_dir($this->path . $file) && preg_match('/^[a-z][a-z0-9_]{0,99}$/', $file)){
		        	    if($ImportModule = $this->importModule($file)){
		        			if(method_exists($ImportModule, 'metaData')){
		        				$metaData = call_user_func([$ImportModule, 'metaData']);
		        				$modules[] = [
		        					'name'			=> $file,
		        					'display_name'	=> $metaData['display_name'] ?: $file,
		        					'version'		=> $metaData['version'] ?? '1.0.0',
		        				];
		        			}else{
		        				$modules[] = [
		        					'name'			=> $file,
		        					'display_name'	=> $file,
		        					'version'		=> '1.0.0',
		        				];
		        			}
		        		}
		        	}
		        }
		        closedir($handle);
		    }
		}
		return $modules;
	}

	/**
	 * 时间 2024-04-28
	 * @title 获取已用模块列表
	 * @desc  获取已用模块列表
	 * @author hh
	 * @version v1
	 * @return  int list[].id - ID
	 * @return  string list[].author - 作者
	 * @return  string list[].author_url - 作者地址
	 * @return  string list[].description - 描述
	 * @return  string list[].help_url - 帮助链接
	 * @return  int list[].menu_id - 对应菜单ID(模块没有对应菜单ID)
	 * @return  string list[].name - 标识
	 * @return  int list[].status - 状态(1=启用)
	 * @return  string list[].title - 标题
	 * @return  string list[].url - 跳转链接
	 * @return  string list[].version - 版本号
	 * @return  string list[].module - 应用类型(addon=插件,server=模块)
	 * @return  int count - 总条数
	 */
	public function enableModuleList()
	{
		$moduleList = $this->getModuleList();
		$module = [];
		foreach($moduleList as $v){
			$module[] = parse_name($v['name'], 1);
		}
		$moduleList = [];
		if(!empty($module)){
			$moduleList = PluginModel::field('id,author,author_url,description,help_url,id menu_id,name,status,title,url,version,module')
						->whereIn('name', $module)
						->where('module', 'server')
						->withAttr('menu_id', function($val){
							return 0;
						})
						->withAttr('url', function($val){
							return '';
						})
						->select()
						->toArray();
		}
		return ['list'=>$moduleList, 'count'=>count($moduleList) ];
	}

	/**
	 * 时间 2022-05-27
	 * @title 测试连接
	 * @desc 测试连接
	 * @author hh
	 * @version v1
	 * @param   ServerModel ServerModel - 接口模型 require
	 * @return  int status - 200=连接成功,400=连接失败
	 * @return  string msg - 信息
	 */
	public function testConnect(ServerModel $ServerModel): array
	{
		$module = $ServerModel['module'];
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'testConnect')){
				// 获取模块通用参数
				$res = call_user_func([$ImportModule, 'testConnect'], ['server'=>$ServerModel]);
				$res = $this->formatResult($res, lang('module_test_connect_success'), lang('module_test_connect_fail'));
			}else{
				$res['status'] = 400;
				$res['msg'] = lang('undefined_test_connect_function');
			}
		}else{
			$res['status'] = 400;
			$res['msg'] = lang('module_file_is_not_exist');
		}
		return $res;
	}

	/**
	 * 时间 2022-05-27
	 * @title 第一次使用模块创建接口后
	 * @desc 第一次使用模块创建接口后
	 * @author hh
	 * @version v1
	 * @param   string $module - 模块名称 require
	 */
	public function afterCreateFirstServer($module)
	{
		// 可以把添加表的操作放这里
		if($ImportModule = $this->importModule($module)){
			lang_plugins('success_message', [], true);

			if(method_exists($ImportModule, 'afterCreateFirstServer')){
				call_user_func([$ImportModule, 'afterCreateFirstServer']);
			}
			$moduleName = parse_name($module, 1);
			$version = '1.0.0';
			$title = $module;
			if(method_exists($ImportModule, 'metaData')){
		        $metaData = call_user_func([$ImportModule, 'metaData']);
		        $version = $metaData['version'] ?? '1.0.0';
		        $title = $metaData['display_name'] ?: $module;
		    }
			$exist = PluginModel::where('name', $moduleName)->where('module', 'server')->find();
			if(!empty($exist)){
				PluginModel::where('id', $exist['id'])->update([
					'title'        => $title,
					'version'      => $version,
                    'update_time'  => time(),
				]);
			}else{
				// 增加版本
				PluginModel::create([
					'status' 		=> 1,
					'name'	 		=> $moduleName,
					'title'	 		=> $title,
					'url'	 		=> '',
					'author' 		=> '',
					'version'		=> $version,
					'description' 	=> '',
					'config'		=> '',
					'module'		=> 'server',
					'create_time'	=> time(),
				]);
			}
		}
	}

	/**
	 * 时间 2022-05-27
	 * @title 删除最后一个使用该模块的接口
	 * @desc 删除最后一个使用该模块的接口
	 * @author hh
	 * @version v1
	 * @param   string $module - 模块名称 require
	 */
	public function afterDeleteLastServer($module)
	{
		// 可以把删表放这里
		if($ImportModule = $this->importModule($module)){
			lang_plugins('success_message', [], true);
			
			if(method_exists($ImportModule, 'afterDeleteLastServer')){
				call_user_func([$ImportModule, 'afterDeleteLastServer']);
			}
            $moduleName = parse_name($module, 1);
            PluginModel::where('name', $moduleName)->where('module', 'server')->delete();
		}
	}

	/**
	 * 时间 2022-05-16
	 * @title 产品开通
	 * @desc 产品开通
	 * @author hh
	 * @version v1
	 * @param   HostModel $HostModel - 产品模型 require
	 * @return  int status - 状态,200=成功,400=失败
	 * @return  string msg - 信息
	 */
	public function createAccount(HostModel $HostModel): array
	{
		// 模块调用
		$module = $HostModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'createAccount')){
				// 获取模块通用参数
				$params = $HostModel->getModuleParams();
				$res = call_user_func([$ImportModule, 'createAccount'], $params);
				return $this->formatResult($res, lang('module_create_success'), lang('module_create_success'));
			}else{
				return ['status'=>200, 'msg'=>lang('module_create_success')];
			}
		}
		return ['status'=>200, 'msg'=>lang('module_create_success')];
	}

	/**
	 * 时间 2022-05-16
	 * @title 产品暂停
	 * @desc 产品暂停
	 * @author hh
	 * @version v1
	 * @param   HostModel $HostModel - 产品模型 require
	 * @param   string param.suspend_type overdue 暂停类型(overdue=到期暂停,overtraffic=超流暂停,certification_not_complete=实名未完成,other=其他,downstream=下游暂停)
	 * @param   string param.suspend_reason - 暂停原因
	 * @return  int status - 状态,200=成功,400=失败
	 * @return  string msg - 信息
	 */
	public function suspendAccount(HostModel $HostModel, $param = []): array
	{
		// 模块调用
		$module = $HostModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'suspendAccount')){
				// 获取模块通用参数
				$params = $HostModel->getModuleParams();
				$params['suspend_type'] = $param['suspend_type'] ?? 'overdue';
				$params['suspend_reason'] = $param['suspend_reason'] ?? '';

				$res = call_user_func([$ImportModule, 'suspendAccount'], $params);
				return $this->formatResult($res, lang('module_suspend_success'), lang('module_suspend_fail'));
			}else{
				return ['status'=>200, 'msg'=>lang('module_suspend_success')];
			}
		}
		return ['status'=>200, 'msg'=>lang('module_suspend_success')];
	}

	/**
	 * 时间 2022-05-16
	 * @title 产品解除暂停
	 * @desc 产品解除暂停
	 * @author hh
	 * @version v1
	 * @param   HostModel $HostModel - 产品模型 require
	 * @param   array param - 追加参数
	 * @return  int status - 状态,200=成功,400=失败
	 * @return  string msg - 信息
	 */
	public function unsuspendAccount(HostModel $HostModel, array $param = []): array
	{
		// 模块调用
		$module = $HostModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'unsuspendAccount')){
				// 获取模块通用参数
				$params = $HostModel->getModuleParams();
				if(!empty($param)){
					$params = array_merge($param, $params);
				}
				$res = call_user_func([$ImportModule, 'unsuspendAccount'], $params);
				return $this->formatResult($res, lang('module_unsuspend_success'), lang('module_unsuspend_fail'));
			}else{
				return ['status'=>200, 'msg'=>lang('module_unsuspend_success')];
			}
		}	
		return ['status'=>200, 'msg'=>lang('module_unsuspend_success')];
	}

	/**
	 * 时间 2022-05-16
	 * @title 产品删除
	 * @desc 产品删除
	 * @author hh
	 * @version v1
	 * @param   HostModel $HostModel - 产品模型 require
	 * @return  int status - 状态,200=成功,400=失败
	 * @return  string msg - 信息
	 */
	public function terminateAccount(HostModel $HostModel): array
	{
		// 模块调用
		$module = $HostModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'terminateAccount')){
				// 获取模块通用参数
				$params = $HostModel->getModuleParams();
				$res = call_user_func([$ImportModule, 'terminateAccount'], $params);
				return $this->formatResult($res, lang('delete_success'), lang('delete_fail'));
			}else{
				return ['status'=>200, 'msg'=>lang('delete_success')];
			}
		}
		return ['status'=>200, 'msg'=>lang('delete_success')];
	}

	/**
	 * 时间 2022-05-16
	 * @title 续费订单支付后调用
	 * @desc 续费订单支付后调用
	 * @author hh
	 * @version v1
	 * @param   HostModel $HostModel - 产品模型 require
	 */
	public function renew(HostModel $HostModel): void
	{
		// 模块调用
		$module = $HostModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'renew')){
				// 获取模块通用参数
				$params = $HostModel->getModuleParams();
				$res = call_user_func([$ImportModule, 'renew'], $params);
                if (!empty($res['status']) && $res['status']!=200){
                    $HostModel->failedActionHandle([
                        'host_id' => $HostModel['id'],
                        'action' => 'renew',
                        'msg' => $res['msg']??''
                    ]);
                }
			}
		}
		// 不需要返回东西
	}

	/**
	 * 时间 2022-05-26
	 * @title 升降级配置项完成后调用
	 * @desc 升降级配置项完成后调用
	 * @author hh
	 * @version v1
	 * @param HostModel HostModel - 产品模型 require
	 * @param array params - 自定义参数 require
	 */
	public function changePackage(HostModel $HostModel, $params, $orderId=0)
	{
		$module = $HostModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'changePackage')){
				// 获取模块通用参数
				$moduleParams = $HostModel->getModuleParams();
				$moduleParams['custom'] = $params;
				$moduleParams['order_id'] = $orderId;
				$res = call_user_func([$ImportModule, 'changePackage'], $moduleParams);
			}
		}
		// 不需要返回
	}

	/**
	 * 时间 2022-06-01
	 * @title 升降级商品完成后调用
	 * @desc 升降级商品完成后调用
	 * @author hh
	 * @version v1
	 * @param   HostModel HostModel - 已经关联新商品的产品模型 require
	 * @param   array params - 自定义参数 require
	 */
	public function changeProduct(HostModel $HostModel, $params, $orderId=0)
	{
		$module = $HostModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'changeProduct')){
				// 获取模块通用参数
				$moduleParams = $HostModel->getModuleParams();
				$moduleParams['custom'] = $params;
				$moduleParams['order_id'] = $orderId;
				$res = call_user_func([$ImportModule, 'changeProduct'], $moduleParams);
			}
		}
		// 不需要返回
	}

	/**
	 * 时间 2022-05-26
	 * @title 购物车价格计算
	 * @desc 购物车价格计算
	 * @author hh
	 * @version v1
	 * @param   ProductModel $ProductModel - 产品模型 require
	 * @param   array $params - 模块自定义配置参数
	 * @param   int qty 1 数量
	 * @param   string scene buy 场景(cal_price=计算价格,buy=结算)
	 * @param   int position - 结算位置标识
	 * @return  int status - 状态(200=成功,400=失败)
	 * @return  string msg - 信息
	 * @return  string data.price - 价格
	 * @return  string data.renew_price - 续费价格
	 * @return  string data.billing_cycle - 周期名称
	 * @return  int data.duration - 周期时长(秒)
	 * @return  string data.description - 订单子项描述
	 * @return  string data.base_price - 基础价格
	 */
	public function cartCalculatePrice(ProductModel $ProductModel, $params = [], $qty=1, $scene = 'buy', $position = 0, $clientId=0)
	{
		$result = [];

        $module = $ProductModel->getModule($params);

		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'cartCalculatePrice')){
				// 获取模块通用参数
				$result = call_user_func([$ImportModule, 'cartCalculatePrice'], ['product'=>$ProductModel, 'custom'=>$params, 'qty'=>$qty, 'scene'=>$scene, 'position'=>$position,'client_id'=>$clientId]);

				if(isset($result['data']['billing_cycle'])){
					if(app('http')->getName() == 'home'){
		                $multiLanguage = hook_one('multi_language', [
		                    'replace' => [
		                        'billing_cycle' => $result['data']['billing_cycle'],
		                    ],
		                ]);
		                if(isset($multiLanguage['billing_cycle'])){
		                	// 附加返回
		                	$result['data']['customfield']['multi_language']['billing_cycle'] = $multiLanguage['billing_cycle'];
		                }
			        }
			    }
			}
		}
		if(empty($result)){
			$result = [
				'status'=>400,
				'msg'=>lang('module_file_is_not_exist'),
			];
		}

		return $result;
	}

	/**
	 * 时间 2022-05-16
	 * @title 后台商品接口配置输出
	 * @desc 后台商品接口配置输出
	 * @author hh
	 * @version v1
	 * @param   string module - 模块名称 require
	 * @param   ProductModel $ProductModel - 商品模型 require
	 * @return  string
	 */
	public function serverConfigOption($module, ProductModel $ProductModel)
	{
		$res = '';
		// 模块调用
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'serverConfigOption')){
				// 获取模块通用参数
				$res = call_user_func([$ImportModule, 'serverConfigOption'], ['product'=>$ProductModel]);
				$res = $this->formatTemplate($module, $res);
			}
		}
		return $res;
	}

	/**
	 * 时间 2022-05-16
	 * @title 产品列表页内容
	 * @desc 产品列表页内容
	 * @author hh
	 * @version v1
	 * @param   string $module - 模块名称 require
	 * @param   array $params.product_id - 当前导航关联的所有商品ID require
	 * @return  string
	 */
	public function hostList($module, $params): string
	{
		$res = '';
		// 模块调用
		// $module = $HostModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'hostList')){
				// 获取模块通用参数
				// $params = $HostModel->getModuleParams();
				$res = call_user_func([$ImportModule, 'hostList'], $params);
				$res = $this->formatTemplate($module, $res);
			}
		}
		return $res;
	}

	/**
	 * 时间 2022-05-16
	 * @title 产品前台内页输出
	 * @desc 产品前台内页输出
	 * @author hh
	 * @version v1
	 * @param   HostModel HostModel - 产品模型 require
	 * @return  string
	 */
	public function clientArea(HostModel $HostModel): string
	{
		$res = '';
		// 模块调用
		$module = $HostModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'clientArea')){
				// 获取模块通用参数
				$params = $HostModel->getModuleParams();
				$res = call_user_func([$ImportModule, 'clientArea'], $params);
				$res = $this->formatTemplate($module, $res);
			}
		}
		return $res;
	}

	/**
	 * 时间 2022-05-16
	 * @title 产品后台内页输出
	 * @desc 产品后台内页输出
	 * @author hh
	 * @version v1
	 * @param   HostModel HostModel - 产品模型 require
	 * @return  string
	 */
	public function adminArea(HostModel $HostModel): string
	{
		$res = '';
		// 模块调用
		$module = $HostModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'adminArea')){
				// 获取模块通用参数
				$params = $HostModel->getModuleParams();
				$res = call_user_func([$ImportModule, 'adminArea'], $params);
				$res = $this->formatTemplate($module, $res);
			}
		}
		return $res;
	}

	/**
	 * 时间 2022-05-30
	 * @title 前台商品购买页面输出
	 * @desc 前台商品购买页面输出
	 * @author hh
	 * @version v1
	 * @param   ProductModel ProductModel - 产品模型 require
	 * @return  string
	 */
	public function clientProductConfigOption(ProductModel $ProductModel): string
	{
		$res = '';
		$module = $ProductModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'clientProductConfigOption')){
				// 获取模块通用参数
				$res = call_user_func([$ImportModule, 'clientProductConfigOption'], ['product'=>$ProductModel]);
				$res = $this->formatTemplate($module, $res);
			}
		}
		return $res;
	}

	/**
	 * 时间 2022-05-30
	 * @title 在结算之后调用
	 * @desc 在结算之后调用,这时候可以存入产品配置项关联关系
	 * @author hh
	 * @version v1
	 * @param   ProductModel ProductModel - 商品模型 require
	 * @param   int hostId - 产品ID require
	 * @param   array params - 模块自定义参数 require
	 * @param   array customfields - 其他自定义参数
	 * @param   int position - 结算位置标识
	 */
	public function afterSettle(ProductModel $ProductModel, $hostId, $params,$customfields=[], $position = 0): void
	{
        $module = $ProductModel->getModule($params);

		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'afterSettle')){
				call_user_func([$ImportModule, 'afterSettle'], ['product'=>$ProductModel, 'host_id'=>$hostId, 'custom'=>$params, 'customfields'=>$customfields, 'position'=>$position]);
			}
		}
	}

	/**
	 * 时间 2022-06-02
	 * @title 获取当前产品所有周期价格
	 * @desc 获取当前产品所有周期价格
	 * @author hh
	 * @version v1
	 * @param   HostModel HostModel - 产品模型 require
	 * @return  int status - 状态码(200=成功,400=失败)
	 * @return  string msg - 提示信息
	 * @return  float data[].price - 金额
	 * @return  string data[].billing_cycle - 周期名称
	 * @return  int data[].duration - 周期时长(秒)
	 */
	public function durationPrice(HostModel $HostModel)
	{
		$res = [];
		$module = $HostModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'durationPrice')){
				// 获取模块通用参数
				$moduleParams = $HostModel->getModuleParams();
				$res = call_user_func([$ImportModule, 'durationPrice'], $moduleParams);
				
				if(app('http')->getName() == 'home'){
					if($res['status'] == 200){
						foreach($res['data'] as $k=>$v){
			                $multiLanguage = hook_one('multi_language', [
			                    'replace' => [
			                        'billing_cycle' => $v['billing_cycle'],
			                    ],
			                ]);
			                if(isset($multiLanguage['billing_cycle'])){
			                	// 附加返回
			                	$res['data'][$k]['customfield']['multi_language']['billing_cycle'] = $multiLanguage['billing_cycle'];
			                }
						}
					}
				}
			}
		}
		if(empty($res)){
			$res = ['status'=>400, 'msg'=>'module_file_is_not_exist'];
		}
		return $res;
	}

	/**
	 * 时间 2023-01-30
	 * @title 获取商品起售周期价格
	 * @desc 获取商品起售周期价格
	 * @author hh
	 * @version v1
	 * @param   int productId - 商品ID require
	 * @return  float price - 价格
	 * @return  string cycle - 周期
	 * @return  ProductModel product - ProductModel实例
	 */
	public function getPriceCycle($productId)
	{
		$res = [
			'price' => null,
			'cycle' => null
		];
		$ProductModel = ProductModel::findOrEmpty($productId);

		$module = $ProductModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'getPriceCycle')){
				$moduleRes = call_user_func([$ImportModule, 'getPriceCycle'], ['product'=>$ProductModel]);
				if(isset($moduleRes['price']) && is_numeric($moduleRes['price'])){
					$res['price'] = $moduleRes['price'];
				}
				if(isset($moduleRes['cycle'])){
					$res['cycle'] = $moduleRes['cycle'];
				}
			}
		}
		$res['product'] = $ProductModel;
		return $res;
	}

	/**
	 * 时间 2023-02-14
	 * @title 下载上游资源
	 * @desc 下载上游资源
	 * @author hh
	 * @version v1
	 * @param   ProductModel $ProductModel - 商品实例 require
	 * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     * @return  string data.module - 对应reserver模块名称
     * @return  string data.url - 对应reserver模块zip包
     * @return  string version - 当前模块版本号
	 */
	public function downloadResource(ProductModel $ProductModel)
	{
		$res = [];
		$module = $ProductModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'downloadResource')){
				$res = call_user_func([$ImportModule, 'downloadResource'], ['product'=>$ProductModel]);
			}
		}
		if(empty($res)){
			// 未实现该方法返回成功
			$res = ['status'=>200, 'msg'=>'module_file_is_not_exist', 'data'=>[] ];
		}
		return $res;
	}

	/**
     * 时间 2024-11-12
     * @title 下载上游插件资源
     * @desc 下载上游插件资源
     * @author theworld
     * @version v1
     * @param   ProductModel $ProductModel - 商品实例 require
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     * @return  array data.plugin - 插件列表
     * @return  string data.plugin[].module - 插件模块
     * @return  string data.plugin[].name - 插件标识
     * @return  string data.plugin[].url - zip包完整下载路径
     * @return  string data.plugin[].version - 版本号
     */
	public function downloadPluginResource(ProductModel $ProductModel)
	{
		$plugin = [];
        $hookRes = hook("upstream_plugin_resource",['product_id'=>$ProductModel['id']]);
        foreach ($hookRes as $res){
            if ($res['status']==200){
                $plugin[] = $res['data'];
            }
        }

		return ['status'=>200, 'msg'=>lang('success_message'), 'data' => ['plugin' => $plugin]];
	}

	/**
	 * 时间 2023-04-14
	 * @title 产品内页模块配置信息输出
	 * @desc 产品内页模块配置信息输出
	 * @author hh
	 * @version v1
	 * @param   HostModel $HostModel - HostModel实例 require
	 * @return  int status - 状态(200=成功,400=失败)
	 * @return  string msg - 信息
     * @return  string data[].name - 配置小标题
	 * @return  string data[].field[].name - 名称
	 * @return  string data[].field[].key - 标识(不要重复)
	 * @return  string data[].field[].value - 当前值
	 * @return  bool   data[].field[].disable - 状态(false=可修改,true=不可修改)
	 */
	public function adminField(HostModel $HostModel)
	{
		$data = [];
		// 模块调用
		$module = $HostModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'adminField')){
				// 获取模块通用参数
				$params = $HostModel->getModuleParams();
				$data = call_user_func([$ImportModule, 'adminField'], $params);
				// 格式化
				if(is_array($data) && !empty($data)){
					array_walk($data, function(&$value){
						array_walk($value['field'], function(&$v){
							$v['value'] = (string)$v['value'];
						});
					});
				}else{
					$data = [];
				}
			}
		}
		$res = [
			'status' => 200,
			'msg'	 => lang('success_message'),
			'data'	 => $data,
		];
		return $res;
	}

	/**
	 * 时间 2024-02-04
	 * @title 产品保存后
	 * @desc  产品保存后
	 * @author hh
	 * @version v1
	 * @param   HostModel $HostModel - 当前产品HostModel require
	 * @param   array     $module_admin_field - 模块自定义配置信息(键是配置标识,值是填写的内容)
	 * @return  int status - 状态(200=成功,400=失败)
	 * @return  string msg - 信息
	 */
	public function hostUpdate(HostModel $HostModel, $module_admin_field = [])
	{
		$res = [];
		// 模块调用
		$module = $HostModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'hostUpdate')){
				// 获取模块通用参数
				$params = $HostModel->getModuleParams();
				$params['module_admin_field'] = $module_admin_field;
				$res = call_user_func([$ImportModule, 'hostUpdate'], $params);
			}
		}
		if(!isset($res['status'])){
			$res = [
				'status' => 200,
				'msg'	 => lang('message_success')
			];
		}
		return $res;
	}

	/**
	 * 时间 2022-06-16
	 * @title 获取商品所有配置项
	 * @desc 获取商品所有配置项
	 * @author hh
	 * @version v1
	 * @param   ProductModel ProductModel - 商品模型 require
     * @return  int status 状态(200=成功,400=失败)
     * @return  string msg 信息
     * @return  string data[].name  配置项名称
     * @return  string data[].field 订购时对应配置的键
     * @return  string data[].type  类型(dropdown=下拉),只支持下拉
     * @return  string data[].option[].name 选项名称
     * @return  int|string data[].option[].value 对应选项值
	 */
	public function allConfigOption(ProductModel $ProductModel)
	{
		$res = [];
		$module = $ProductModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'allConfigOption')){
				$res = call_user_func([$ImportModule, 'allConfigOption'], ['product'=>$ProductModel]);				
			}
		}
		if(empty($res)){
			// 未实现该方法返回成功
			$res = ['status'=>200, 'msg'=>'module_file_is_not_exist', 'data'=>[] ];
		}
		return $res;
	}

	/**
	 * 时间 2024-05-20
	 * @title 后台产品内页实例操作输出
	 * @desc  后台产品内页实例操作输出
	 * @author hh
	 * @version v1
	 * @param   HostModel $HostModel - HostModel实例
	 * @return  string
	 */
	public function adminAreaModuleOperate(HostModel $HostModel): string
	{
		$res = '';
		// 模块调用
		$module = $HostModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'adminAreaModuleOperate')){
				// 获取模块通用参数
				$params = $HostModel->getModuleParams();
				$res = call_user_func([$ImportModule, 'adminAreaModuleOperate'], $params);
				$res = $this->formatTemplate($module, $res);
			}
		}
		return $res;
	}

	/**
	 * 时间 2024-06-17
	 * @title 同步信息
	 * @desc  同步信息,返回了对应的键才会同步修改
	 * @author hh
	 * @version v1
	 * @param   HostModel HostModel - 产品模型 require
     * @return  int status 状态(200=成功,400=失败)
     * @return  string msg 信息
     * @return  string data.dedicate_ip - 主IP
     * @return  string data.assign_ip - 附加IP
     * @return  int data.country_id - 国家ID
     * @return  string data.city - 城市
     * @return  string data.area - 区域
     * @return  string data.power_status - 电源状态(on=开机,off=关机,suspend=暂停,operating=操作中,fault=故障)
     * @return  string data.image_icon - 操作系统图标(Windows,CentOS,Ubuntu,Debian,ESXi,XenServer,FreeBSD,Fedora,ArchLinux,Rocky,AlmaLinux,OpenEuler,RedHat,其他)
     * @return  string data.image_name - 操作系统名称
	 */
	public function syncAccount(HostModel $HostModel): array
	{
		$res = [];
		// 模块调用
		$module = $HostModel->getModule();
		if($ImportModule = $this->importModule($module)){
			if(method_exists($ImportModule, 'syncAccount')){
				// 获取模块通用参数
				$params = $HostModel->getModuleParams();
				$res = call_user_func([$ImportModule, 'syncAccount'], $params);
			}
		}
		if(!isset($res['status'])){
			$res = [
				'status' => 200,
				'msg'	 => lang('message_success')
			];
		}
		return $res;
	}

	/**
	 * 时间 2024-06-21
	 * @title 模块是否有某个方法
	 * @desc  模块是否有某个方法
	 * @author hh
	 * @version v1
	 * @param   string $module - 模块 require
	 * @param   string $method - 方法名称 require
	 * @return  bool
	 */
	public function moduleMethodExist($module, $method)
	{
		$exist = false;
		if($ImportModule = $this->importModule($module)){
			$exist = method_exists($ImportModule, $method);
		}
		return $exist;
	}

    /**
	 * 时间 2022-06-08
	 * @title 验证模块名称是否正确
	 * @desc 验证模块名称是否正确
	 * @author hh
	 * @version v1
	 * @param   string $module - 模块名称 require
	 * @return  bool
	 */
	protected function checkModule($module)
	{
		return (bool)preg_match('/^[a-z][a-z0-9_]{0,99}$/', $module);
	}

	/**
	 * 时间 2022-05-16
	 * @title 引入商品模块文件
	 * @desc 引入商品模块文件
	 * @author hh
	 * @version v1
	 * @param   string module - 模块类型 require
	 * @return  bool|object - - false=没有对应类,object=成功实例化模块类
	 */
	protected function importModule($module)
	{
		if(!empty($module)){
			$className = parse_name($module, 1);

			$class = '\server\\'.$module.'\\'.$className;

			if(class_exists($class)){
				return new $class();
			}
		}
		return false;
	}

	/**
	 * 时间 2022-05-26
	 * @title 格式化文本返回
	 * @desc 格式化文本返回
	 * @author hh
	 * @version v1
	 * @param   string $module 模块名称 require
	 * @param   mixed  $res    模块返回 require
	 * @return  string
	 */
	private function formatTemplate($module, $res): string
	{
		$html = '';
		if(is_array($res)){
			// 认为是使用模板的方式来输出内容,格式大概如下
			// [
			// 	   'template'=>'abc.html',
			// 	   'vars'=>[
			// 	  		'aaaa'=>'bbb'
			// 	   ]
			// ]
			$template_file = $this->path . $module . '/' . $res['template'];
			if(file_exists($template_file)){
				$PluginModel=new PluginModel();
              	$addons = $PluginModel->plugins('addon')['list'];

              	$vars = isset($res['vars']) && !empty($res['vars']) && is_array($res['vars']) ? $res['vars'] : [];
				$vars['addons'] = $addons;
				$vars['system_version'] = configuration('system_version');

				View::assign($vars);
				// 调用方法变量
				$html = View::fetch($template_file);

				// css,js追加系统版本
		        $version = '?v='.configuration('system_version'); 

		        $pattern = '/<link\s+[^>]*?href="([^"]+\.css)"[^>]*>/i';
		          
		        $html = preg_replace_callback($pattern, function($matches) use ($version) {  
		            return str_replace($matches[1], $matches[1] . $version, $matches[0]);  
		        }, $html);  

		        $pattern = '/<script\s+[^>]*?src="([^"]+\.js)"[^>]*>/i';
		          
		        $html = preg_replace_callback($pattern, function($matches) use ($version) {  
		            return str_replace($matches[1], $matches[1] . $version, $matches[0]);  
		        }, $html);  

			}else{
				$html = lang('module_cannot_find_template_file');
			}
		}else if(is_string($res)){
			$html = $res;
		}else{
			$html = (string)$res;
		}
		return $html;
	}

	/**
	 * 时间 2022-05-13
	 * @title 格式化系统操作返回
	 * @desc 格式化系统操作返回
	 * @author hh
	 * @version v1
	 * @param  mixed res - 操作返回 required
	 * @param  string successMsg - 成功返回没有提示信息时,会用该信息提示
	 * @param  string failMsg - 失败返回没有提示信息时,会用该信息提示
	 * @return int status - 状态(200=成功,400=失败)
	 * @return string msg - 信息
	 */
	private function formatResult($res, $successMsg = '', $failMsg = ''): array
	{
		$result = [];
		// 不兼容原来的老模块写法,都必须按标准返回
		if(is_array($res)){
			$result = $res;
			
			if($result['status'] === 400){
				$result['msg'] = $result['msg'] ?? ($failMsg ?: lang('module_operate_fail'));
			}else if($result['status'] === 200){
				$result['msg'] = $result['msg'] ?? ($successMsg ?: lang('module_operate_success'));
			}else{
				$result = [];
				$result['status'] = 400;
				$result['msg'] = lang('module_res_format_error');
			}
		}else{
			$result = [];
			$result['status'] = 400;
			$result['msg'] = lang('module_res_format_error');
		}
		return $result;
	}

    /**
     * 时间 2023-01-30
     * @title 获取商品起售周期价格
     * @desc 获取商品起售周期价格
     * @author hh
     * @version v1
     * @param   int productId - 商品ID require
     * @return  float price - 价格
     * @return  string cycle - 周期
     * @return  ProductModel product - ProductModel实例
     */
    public function otherParams($productId)
    {
        $ProductModel = ProductModel::findOrEmpty($productId);

        $module = $ProductModel->getModule();
        if($ImportModule = $this->importModule($module)){
            if(method_exists($ImportModule, 'otherParams')){
                $moduleRes = call_user_func([$ImportModule, 'otherParams'], ['product'=>$ProductModel]);
                $res['data'] = $moduleRes;
            }
        }

        return $res??[];
    }

    public function syncOtherParams($productId, $param, $otherParams, UpstreamProductModel $upstreamProductModel)
    {
        $ProductModel = ProductModel::findOrEmpty($productId);
//        $module = $ProductModel->getModule();

        $module = $upstreamProductModel['res_module'];
        if($ImportModule = $this->importModule($module)){
            if(method_exists($ImportModule, 'syncOtherParams')){
                $moduleRes = call_user_func([$ImportModule, 'syncOtherParams'],
                    ['product'=>$ProductModel,'param'=>$param,'other_params'=>$otherParams,'upstream_product'=>$upstreamProductModel]);

            }
        }

        return $moduleRes??['status'=>400,'msg'=>lang('error_message')];
    }

    public function exchangeParams(ProductModel $ProductModel, $param, $sence, HostModel $HostModel)
    {
        $module = $ProductModel->getModule();
        if($ImportModule = $this->importModule($module)){
            if(method_exists($ImportModule, 'exchangeParams')){
                $param = call_user_func([$ImportModule, 'exchangeParams'], ['product'=>$ProductModel, 'param'=>$param, 'sence'=>$sence, 'host'=> $HostModel]);
            }
        }
        return $param;
    }

    public function hostOtherParams(HostModel $HostModel)
    {
        $product = (new ProductModel())->find($HostModel['product_id']);

        $module = $product->getModule();

        if($ImportModule = $this->importModule($module)){
            if(method_exists($ImportModule, 'hostOtherParams')){
                $param = call_user_func([$ImportModule, 'hostOtherParams'], ['host'=> $HostModel]);
            }
        }

        return $param??[];
    }

    public function syncHostOtherParams(HostModel $HostModel,array $otherParams)
    {
        $product = (new ProductModel())->find($HostModel['product_id']);

        $module = $product->getModule();

        if($ImportModule = $this->importModule($module)){
            if(method_exists($ImportModule, 'syncHostOtherParams')){
                $param = call_user_func([$ImportModule, 'syncHostOtherParams'], ['host'=> $HostModel,'other_params'=>$otherParams]);
            }
        }

        return $param??[];
    }

    /**
     * 时间 2022-05-16
     * @title 后台商品接口配置输出
     * @desc 后台商品接口配置输出
     * @author hh
     * @version v1
     * @param   string module - 模块名称 require
     * @param   ProductModel $ProductModel - 商品模型 require
     * @return  string
     */
    public function durationPresets($module, ProductModel $ProductModel, $durations)
    {
        // 模块调用
        if($ImportModule = $this->importModule($module)){
            if(method_exists($ImportModule, 'durationPresets')){
                // 获取模块通用参数
                $res = call_user_func([$ImportModule, 'durationPresets'], ['product'=>$ProductModel,'durations'=>$durations]);
            }
        }
        return $res??['status'=>400,'msg'=>lang('error_message')];
    }

    public function durationPresetsDelete($module, ProductModel $ProductModel)
    {
        // 模块调用
        if($ImportModule = $this->importModule($module)){
            if(method_exists($ImportModule, 'durationPresetsDelete')){
                // 获取模块通用参数
                $res = call_user_func([$ImportModule, 'durationPresetsDelete'], ['product'=>$ProductModel]);
            }
        }
        return $res??['status'=>400,'msg'=>lang('error_message')];
    }

}



