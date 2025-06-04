<?php
namespace server\mf_dcim\controller\home;

use server\mf_dcim\logic\CloudLogic;
use server\mf_dcim\model\IpDefenceModel;
use server\mf_dcim\validate\CloudValidate;
use server\mf_dcim\validate\CartValidate;
use server\mf_dcim\model\HostLinkModel;
use server\mf_dcim\model\DataCenterModel;
use server\mf_dcim\model\ImageModel;
use server\mf_dcim\model\DurationModel;
use server\mf_dcim\model\LineModel;
use server\mf_dcim\model\LimitRuleModel;
use app\common\model\ProductModel;
use app\common\model\SystemLogModel;
use app\common\model\HostModel;
use think\facade\Cache;
use think\facade\View;

/**
 * @title DCIM(自定义配置)-前台
 * @desc DCIM(自定义配置)-前台
 * @use server\mf_dcim\controller\home\CloudController
 */
class CloudController
{
	/**
	 * 时间 2023-02-06
	 * @title 获取订购页面配置
	 * @desc 获取订购页面配置
	 * @url /console/v1/product/:id/mf_dcim/order_page
	 * @method  GET 
	 * @author hh
	 * @version v1
	 * @param   int id - 商品ID require
	 * @return  int data_center[].id - 国家ID
	 * @return  string data_center[].iso - 图标
	 * @return  string data_center[].name - 名称
	 * @return  string data_center[].city[].name - 城市
	 * @return  int data_center[].city[].area[].id - 数据中心ID
	 * @return  string data_center[].city[].area[].name - 区域
	 * @return  int data_center[].city[].area[].line[].id - 线路ID
	 * @return  string data_center[].city[].area[].line[].name - 线路名称
	 * @return  int data_center[].city[].area[].line[].data_center_id - 数据中心ID
	 * @return  string data_center[].city[].area[].line[].bill_type - 计费类型(bw=带宽计费,flow=流量计费)
	 * @return  int model_config[].id - 型号配置ID
	 * @return  string model_config[].name - 型号配置名称
	 * @return  string model_config[].cpu - 处理器
	 * @return  string model_config[].cpu_param - 处理器参数
	 * @return  string model_config[].memory - 内存
	 * @return  string model_config[].disk - 硬盘
	 * @return  int model_config[].support_optional - 允许增值选配(0=不允许,1=允许)
	 * @return  int model_config[].optional_only_for_upgrade - 增值仅用于升降级(0=关闭,1=开启)
	 * @return  int model_config[].leave_memory - 剩余内存
	 * @return  int model_config[].max_memory_num - 可增加内存数量
	 * @return  int model_config[].max_disk_num - 可增加硬盘数量
	 * @return  string model_config[].gpu - 显卡
	 * @return  int model_config[].max_gpu_num - 可增加显卡数量
	 * @return  int model_config[].qty - 库存数量
     * @return  int model_config[].auto_sync_dcim_stock - 自动同步DCIM库存(0=不启用,1=启用)
	 * @return  int model_config[].optional_memory[].id - 选配内存配置ID
	 * @return  string model_config[].optional_memory[].value - 选配内存配置名称
	 * @return  int model_config[].optional_memory[].other_config.memory - 选配内存大小
	 * @return  int model_config[].optional_memory[].other_config.memory_slot - 选配内存插槽
	 * @return  int model_config[].optional_disk[].id - 选配硬盘配置ID
	 * @return  string model_config[].optional_disk[].value - 选配硬盘配置名称
	 * @return  int model_config[].optional_gpu[].id - 选配显卡配置ID
	 * @return  string model_config[].optional_gpu[].value - 选配显卡配置名称
	 * @return  array limit_rule - 限制规则
     * @return  int limit_rule[].id - 限制规则ID
     * @return  object limit_rule[].rule - 条件数据
	 * @return  array limit_rule[].rule.data_center.id - 数据中心ID
	 * @return  string limit_rule[].rule.data_center.opt - 运算符(eq=等于,neq=不等于)
     * @return  string limit_rule[].rule.bw.min - 带宽最小值
     * @return  string limit_rule[].rule.bw.max - 带宽最大值
     * @return  string limit_rule[].rule.bw.opt - 运算符(eq=等于,neq=不等于)
     * @return  string limit_rule[].rule.flow.min - 流量最小值
     * @return  string limit_rule[].rule.flow.max - 流量最大值
     * @return  string limit_rule[].rule.flow.opt - 运算符(eq=等于,neq=不等于)
     * @return  array limit_rule[].rule.image.id - 操作系统ID
     * @return  string limit_rule[].rule.image.opt - 运算符(eq=等于,neq=不等于)
     * @return  array limit_rule[].rule.model_config.id - 型号配置ID
     * @return  string limit_rule[].rule.model_config.opt - 运算符(eq=等于,neq=不等于)
     * @return  object limit_rule[].result - 结果数据
     * @return  string limit_rule[].result.bw[].min - 带宽最小值
     * @return  string limit_rule[].result.bw[].max - 带宽最大值
     * @return  string limit_rule[].result.bw[].opt - 运算符(eq=等于,neq=不等于)
     * @return  string limit_rule[].result.flow[].min - 流量最小值
     * @return  string limit_rule[].result.flow[].max - 流量最大值
     * @return  string limit_rule[].result.flow[].opt - 运算符(eq=等于,neq=不等于)
     * @return  array limit_rule[].result.image[].id - 操作系统ID
     * @return  string limit_rule[].result.image[].opt - 运算符(eq=等于,neq=不等于)
     * @return  array limit_rule[].result.model_config[].id - 型号配置ID
     * @return  string limit_rule[].result.model_config[].opt - 运算符(eq=等于,neq=不等于)
	 */
	public function orderPage()
	{
		$param = request()->param();
		$param['product_id'] = $param['id'];

		$DataCenterModel = new DataCenterModel();

		$data = $DataCenterModel->orderPage($param);

		$result = [
			'status' => 200,
			'msg'	 => lang_plugins('success_message'),
			'data'	 => $data,
		];
		return json($result);
	}

	/**
	 * 时间 2023-02-06
	 * @title 获取操作系统列表
	 * @desc 获取操作系统列表
	 * @url /console/v1/product/:id/mf_dcim/image
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 商品ID require
	 * @param   int is_downstream 0 是否下游发起(0=否,1=是)
	 * @return  int list[].id - 操作系统分类ID
	 * @return  string list[].name - 操作系统分类名称
	 * @return  string list[].icon - 操作系统分类图标
	 * @return  int list[].image[].id - 操作系统ID
	 * @return  int list[].image[].image_group_id - 操作系统分类ID
	 * @return  string list[].image[].name - 操作系统名称
	 * @return  int list[].image[].charge - 是否收费(0=否,1=是)
	 * @return  string list[].image[].price - 价格
	 */
	public function imageList()
	{
		$param = request()->param();
		$param['product_id'] = $param['id'];

		$ImageModel = new ImageModel();

		$result = $ImageModel->homeImageList($param);
		return json($result);
	}

	/**
	 * 时间 2023-02-06
	 * @title 获取商品配置所有周期价格
	 * @desc 获取商品配置所有周期价格
	 * @url /console/v1/product/:id/mf_dcim/duration
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 商品ID require
	 * @param   int model_config_id - 型号配置ID
	 * @param   object optional_memory - 选配内存(如{"5":"12"},5是选配内存配置ID,12是数量)
	 * @param   object optional_disk - 选配硬盘(如{"5":"12"},5是选配硬盘配置ID,12是数量)
	 * @param   object optional_gpu - 选配显卡(如{"5":"12"},5是选配显卡配置ID,12是数量)
     * @param   int image_id - 镜像ID
     * @param   int line_id - 线路ID
     * @param   string bw - 带宽(带宽线路)
     * @param   string flow - 流量(流量线路)
     * @param   string ip_num - 公网IP数量
     * @param   int peak_defence - 防御峰值
     * @param   int is_downstream - 是否下游发起(0=否,1=是)
     * @return  int [].id - 周期ID
     * @return  string [].name - 周期名称
     * @return  string [].price - 周期总价
     * @return  float [].discount - 折扣(0=没有折扣)
     * @return  int [].num - 周期时长
     * @return  string [].unit - 单位(hour=小时,day=天,month=月)
     * @return  string [].client_level_discount - 用户等级折扣
	 */
	public function getAllDurationPrice()
	{
		$param = request()->param();

		$CartValidate = new CartValidate();
		if(!$CartValidate->scene('all_duration_price')->check($param)){
            return json(['status'=>400 , 'msg'=>lang_plugins($CartValidate->getError())]);
        }

		$DurationModel = new DurationModel();
		$result = $DurationModel->getAllDurationPrice($param);
		if(isset($result['data']) && !empty($result['data'])){
			foreach($result['data'] as $k=>$v){
				$result['data'][$k]['name'] = $v['name_show'] ?? $v['name'];
				unset($result['data'][$k]['name_show']);
			}
		}
		return json($result);
	}

	/**
	 * 时间 2023-02-14
	 * @title 获取线路配置
	 * @desc 获取线路配置
	 * @url /console/v1/product/:id/mf_dcim/line/:line_id
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 商品ID require
	 * @param   int line_id - 线路ID require
	 * @return  string bill_type - 计费类型(bw=带宽,flow=流量)
	 * @return  int sync_firewall_rule - 同步防火墙规则(0=关闭,1=开启)
	 * @return  string bw[].type - 配置方式(radio=单选,step=阶梯,total=总量)
	 * @return  string bw[].value - 带宽
	 * @return  string bw[].value_show - 自定义显示
	 * @return  int bw[].min_value - 最小值
	 * @return  int bw[].max_value - 最大值
	 * @return  int bw[].step - 步长
	 * @return  string flow[].value - 流量(流量线路)
	 * @return  string defence[].value - 防御
	 * @return  string defence[].desc - 防御显示
	 * @return  string order_default_defence - 默认防御
	 * @return  string ip[].value - 公网IP值
	 * @return  string ip[].desc - 公网IP显示
	 */
	public function lineConfig()
	{
		$param = request()->param();

		$LineModel = new LineModel();

		$data = $LineModel->homeLineConfig((int)$param['line_id']);

		$result = [
			'status' => 200,
			'msg'	 => lang_plugins('success_message'),
			'data'	 => $data,
		];
		return json($result);
	}

	/**
	 * 时间 2023-02-22
	 * @title 数据中心选择
	 * @desc 数据中心选择
	 * @url /console/v1/product/:id/mf_dcim/data_center
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 商品ID require
	 * @return  int list[].id - 数据中心ID
	 * @return  string list[].city - 城市
	 * @return  string list[].area - 区域
	 * @return  string list[].iso - 国家图标
	 * @return  string list[].country_name - 国家名称
	 */
	public function dataCenterSelect()
	{
		$param = request()->param();

		$DataCenterModel = new DataCenterModel();

		$result = $DataCenterModel->formatDisplay($param);
		return json($result);
	}

	/**
	* 时间 2023-02-09
	* @title 产品列表
	* @desc 产品列表
	* @url /console/v1/mf_dcim
	* @method  GET
	* @author hh
	* @version v1
     * @param   int page 1 页数
     * @param   int limit - 每页条数
     * @param   string orderby - 排序(id,due_time,status)
     * @param   string sort - 升/降序
     * @param   string keywords - 关键字搜索:商品名称/产品名称/IP
     * @param   int country_id - 搜索:国家ID
     * @param   string city - 搜索:城市
     * @param   string area - 搜索:区域
     * @param   string status - 产品状态(Unpaid=未付款,Pending=开通中,Active=已开通,Suspended=已暂停,Deleted=已删除)
     * @param   string tab - 状态using使用中expiring即将到期overdue已逾期deleted已删除
     * @return  array list - 列表数据
     * @return  int list[].id - 产品ID
     * @return  int list[].product_id - 商品ID
     * @return  string list[].name - 产品标识
     * @return  string list[].status - 产品状态(Unpaid=未付款,Pending=开通中,Active=已开通,Suspended=已暂停,Deleted=已删除)
     * @return  int list[].active_time - 开通时间
     * @return  int list[].due_time - 到期时间
     * @return  string list[].client_notes - 用户备注
     * @return  string list[].product_name - 商品名称
     * @return  string list[].country - 国家
     * @return  string list[].country_code - 国家代码
     * @return  int list[].country_id - 国家ID
     * @return  string list[].city - 城市
     * @return  string list[].area - 区域
     * @return  string list[].power_status - 电源状态(on=开机,off=关机,operating=操作中,fault=故障)
     * @return  string list[].image_name - 镜像名称
     * @return  string list[].image_icon - 镜像图标(Windows,CentOS,Ubuntu,Debian,ESXi,XenServer,FreeBSD,Fedora,ArchLinux,Rocky,AlmaLinux,OpenEuler,RedHat,其他)
     * @return  int list[].ip_num - IP数量
     * @return  string list[].dedicate_ip - 主IP
     * @return  string list[].assign_ip - 附加IP(英文逗号分隔)
     * @return  object list[].self_defined_field - 自定义字段值(键是自定义字段ID,值是填的内容)
     * @return  int list[].show_base_info - 产品列表是否展示基础信息：1是默认，0否
     * @return  int list[].is_auto_renew - 是否自动续费(0=否,1=是)
     * @return  int count - 总条数
     * @return  int using_count - 使用中产品数量
     * @return  int expiring_count - 即将到期产品数量
     * @return  int overdue_count - 已逾期产品数量
     * @return  int deleted_count - 已删除产品数量
     * @return  int all_count - 全部产品数量
     * @return  int data_center[].country_id - 国家ID
     * @return  string data_center[].city - 城市
     * @return  string data_center[].area - 区域
     * @return  string data_center[].country_name - 国家
     * @return  string data_center[].country_code - 国家代码
     * @return  int self_defined_field[].id - 自定义字段ID
     * @return  string self_defined_field[].field_name - 自定义字段名称
     * @return  string self_defined_field[].field_type - 字段类型(text=文本框,link=链接,password=密码,dropdown=下拉,tickbox=勾选框,textarea=文本区)
	*/
	public function list()
	{
		$param = request()->param();

        $HostLinkModel = new HostLinkModel();

        $subHostIds = $HostLinkModel->where('parent_host_id','>',0)->column('host_id');

        $param['sub_host_ids'] = $subHostIds;

		$HostModel = new HostModel();

		$result = $HostModel->homeHostList($param);

		return json($result);
	}

	/**
	 * 时间 2023-02-27
	 * @title 获取部分详情
	 * @desc 获取部分详情,下游用来获取部分信息
	 * @url /console/v1/mf_dcim/:id/part
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @return  int data_center.id - 数据中心ID
     * @return  string data_center.city - 城市
     * @return  string data_center.area - 区域
     * @return  string data_center.country - 国家
     * @return  string data_center.iso - 图标
     * @return  string ip - IP地址
     * @return  string power_status - 电源状态(on=开机,off=关机,operating=操作中,fault=故障)
     * @return  int image.id - 镜像ID
     * @return  string image.name - 镜像名称
     * @return  string image.image_group_name - 镜像分类
     * @return  string image.icon - 图标
	 */
	public function detailPart()
	{
		$param = request()->param();

		$HostLinkModel = new HostLinkModel();
		$result = $HostLinkModel->detailPart((int)$param['id']);
		return json($result);
	}

	/**
	 * 时间 2022-06-29
	 * @title 获取DCIM产品详情
	 * @desc 获取DCIM产品详情
	 * @url /console/v1/mf_dcim/:id
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
     * @return  int order_id - 订单ID
     * @return  string ip - IP地址
     * @return  string additional_ip - 附加IP(英文逗号分割)
     * @return  string power_status - 电源状态(on=开机,off=关机,operating=操作中,fault=故障)
     * @return  int model_config.id - 型号配置ID
     * @return  string model_config.name - 型号配置名称
     * @return  string model_config.cpu - 处理器
     * @return  string model_config.cpu_param - 处理器参数
     * @return  string model_config.memory - 内存
     * @return  string model_config.disk - 硬盘
     * @return  string model_config.gpu - 显卡
     * @return  int model_config.optional_memory[].id - 可选配内存配置ID
     * @return  string model_config.optional_memory[].value - 名称
     * @return  int model_config.optional_memory[].other_config.memory_slot - 槽位
     * @return  int model_config.optional_memory[].other_config.memory - 内存大小(GB)
     * @return  int model_config.optional_disk[].id - 可选配硬盘配置ID
     * @return  string model_config.optional_disk[].value - 名称
     * @return  int model_config.optional_gpu[].id - 可选配显卡配置ID
     * @return  string model_config.optional_gpu[].value - 名称
     * @return  int model_config.leave_memory - 当前机型剩余内存大小(GB)
     * @return  int model_config.max_memory_num - 当前机型可增加内存数量
     * @return  int model_config.max_disk_num - 当前机型可增加硬盘数量
     * @return  int model_config.max_gpu_num - 当前机型可增加显卡数量
     * @return  int line.id - 线路
     * @return  string line.name - 线路名称
     * @return  string line.bill_type - 计费类型(bw=带宽,flow=流量)
     * @return  int line.sync_firewall_rule - 同步防火墙规则(0=关闭,1=开启)
     * @return  string bw - 带宽(0表示没有)
     * @return  string bw_show - 带宽自定义显示
     * @return  string ip_num - IP数量
     * @return  string peak_defence - 防御峰值
     * @return  string username - 用户名
     * @return  string password - 密码
     * @return  int data_center.id - 数据中心ID
     * @return  string data_center.city - 城市
     * @return  string data_center.area - 区域
     * @return  string data_center.country - 国家
     * @return  string data_center.iso - 国家代码
     * @return  int image.id - 镜像ID
     * @return  string image.name - 镜像名称
     * @return  int image.image_group_id - 镜像分类ID
     * @return  string image.image_group_name - 镜像分类
     * @return  string image.icon - 图标
     * @return  int config.reinstall_sms_verify - 重装短信验证(0=不启用,1=启用)
     * @return  int config.reset_password_sms_verify - 重置密码短信验证(0=不启用,1=启用)
     * @return  int config.manual_resource - 是否手动资源(0=不启用,1=启用)
     * @return  string config.manual_resource_control_mode - 手动资源控制方式not_support不支持,ipmi,dcim_client客户端
     * @return  object optional_memory - 当前机器已添加内存配置({"5":1},5是ID,1是数量)
     * @return  object optional_disk - 当前机器已添加硬盘配置({"5":1},5是ID,1是数量)
     * @return  object optional_gpu - 当前机器已添加显卡配置({"5":1},5是ID,1是数量)
     * @return  array custom_show - 自定义展示字段
     * @return  string custom_show[].name - 字段名称
     * @return  string custom_show[].type - 字段类型(text=文本,password=密码,date=日期)
     * @return  string custom_show[].value - 值
	 */
	public function detail()
	{
		$param = request()->param();

		$HostLinkModel = new HostLinkModel();
		$result = $HostLinkModel->detail((int)$param['id']);
		return json($result);
	}

	/**
	 * 时间 2022-06-22
	 * @title 开机
	 * @desc 开机
	 * @url /console/v1/mf_dcim/:id/on
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   string client_operate_password - 操作密码,需要验证时传
	 */
	public function on()
	{
		$param = request()->param();

		try{
			$CloudLogic = new CloudLogic((int)$param['id']);

			$result = $CloudLogic->on();
			return json($result);
		}catch(\Exception $e){
			return json(['status'=>400, 'msg'=>$e->getMessage()]);
		}
	}

	/**
	 * 时间 2022-06-22
	 * @title 关机
	 * @desc 关机
	 * @url /console/v1/mf_dcim/:id/off
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   string client_operate_password - 操作密码,需要验证时传
	 */
	public function off()
	{
		$param = request()->param();

		try{
			$CloudLogic = new CloudLogic((int)$param['id']);

			$result = $CloudLogic->off();
			return json($result);
		}catch(\Exception $e){
			return json(['status'=>400, 'msg'=>$e->getMessage()]);
		}
	}

	/**
	 * 时间 2022-06-22
	 * @title 重启
	 * @desc 重启
	 * @url /console/v1/mf_dcim/:id/reboot
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   string client_operate_password - 操作密码,需要验证时传
	 */
	public function reboot()
	{
		$param = request()->param();

		try{
			$CloudLogic = new CloudLogic((int)$param['id']);

			$result = $CloudLogic->reboot();
			return json($result);
		}catch(\Exception $e){
			return json(['status'=>400, 'msg'=>$e->getMessage()]);
		}
	}

	/**
     * 时间 2024-05-08
     * @title 批量操作
     * @desc 批量操作
     * @url /console/v1/mf_dcim/batch_operate
     * @method  POST
     * @author theworld
     * @version v1
     * @param   array id - 产品ID require
     * @param   string action - 动作on开机off关机reboot重启 require
     * @param   string client_operate_password - 操作密码,需要验证时传
     */
    public function batchOperate()
    {
        $param = request()->param();

        $CloudValidate = new CloudValidate();
        if (!$CloudValidate->scene('batch')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($CloudValidate->getError())]);
        }

        $id = $param['id'] ?? [];
        $id = array_unique(array_filter($id, function ($x) {
            return is_numeric($x) && $x > 0;
        }));

        $result = [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => [],
        ];

        $action = [
            'on' => 'on',
            'off' => 'off',
            'reboot' => 'reboot',
        ];

        $action = $action[$param['action']];

        foreach ($id as $v) {
            try{
                $CloudLogic = new CloudLogic((int)$v);

                $res = $CloudLogic->$action();

                $result['data'][] = ['id' => (int)$v, 'status' => $res['status'], 'msg' => $res['msg']];
            }catch(\Exception $e){
                $result['data'][] = ['id' => (int)$v, 'status' => 400, 'msg' => $e->getMessage()];
            }
        }

        return json($result);
    }

	/**
	 * 时间 2022-06-29
	 * @title 获取控制台地址
	 * @desc 获取控制台地址
	 * @url /console/v1/mf_dcim/:id/vnc
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int more 0 获取更多信息(0=否,1=是)
	 * @param   string client_operate_password - 操作密码,需要验证时传
	 * @return  string url - 控制台地址
	 * @return  string vnc_url - 控制台websocket地址(more=1返回)
	 * @return  string vnc_pass - vnc密码(more=1返回)
	 * @return  string password - 机器密码(more=1返回)
	 * @return  string token - 控制台页面令牌(more=1返回)
	 */
	public function vnc()
	{
		$param = request()->param();

		try{
			$CloudLogic = new CloudLogic((int)$param['id']);

			$result = $CloudLogic->vnc($param);
			return json($result);
		}catch(\Exception $e){
			return json(['status'=>400, 'msg'=>$e->getMessage()]);
		}
	}

	/**
	 * 时间 2022-07-01
	 * @title 控制台页面
	 * @desc 控制台页面
	 * @url /console/v1/mf_dcim/:id/vnc
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param int id - 产品ID require
	 * @param string tmp_token - 控制台页面令牌 require
	 */
	public function vncPage()
	{
		$param = request()->param();

		$cache = Cache::get('mf_dcim_vnc_'.$param['id']);
		if(!empty($cache) && isset($param['tmp_token']) && $param['tmp_token'] === $cache['token']){
			View::assign($cache);
		}else{
			return lang_plugins('mf_dcim_vnc_token_expired');
		}
		return View::fetch(WEB_ROOT . 'plugins/server/mf_dcim/view/vnc_page.html');
	}

	/**
	 * 时间 2022-06-24
	 * @title 获取实例状态
	 * @desc 获取实例状态
	 * @url /console/v1/mf_dcim/:id/status
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @return  string status - 实例状态(pending=开通中,on=开机,off=关机,operating=操作中,fault=故障)
	 * @return  string desc - 实例状态描述
	 */
	public function status()
	{
		$param = request()->param();

		try{
			$CloudLogic = new CloudLogic((int)$param['id']);

			$result = $CloudLogic->status();
			return json($result);
		}catch(\Exception $e){
			return json(['status'=>400, 'msg'=>$e->getMessage()]);
		}
	}

	/**
	 * 时间 2022-06-24
	 * @title 重置密码
	 * @desc 重置密码
	 * @url /console/v1/mf_dcim/:id/reset_password
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   string password - 新密码 require
	 * @param   string code - 二次验证验证码
	 * @param   string client_operate_password - 操作密码,需要验证时传
	 */
	public function resetPassword()
	{
		$param = request()->param();

		$CloudValidate = new CloudValidate();
		if (!$CloudValidate->scene('reset_password')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($CloudValidate->getError())]);
        }

		try{
			$CloudLogic = new CloudLogic((int)$param['id']);

			$result = $CloudLogic->resetPassword($param);
			return json($result);
		}catch(\Exception $e){
			return json(['status'=>400, 'msg'=>$e->getMessage()]);
		}
	}

	/**
	 * 时间 2022-06-24
	 * @title 救援模式
	 * @desc 救援模式
	 * @url /console/v1/mf_dcim/:id/rescue
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int type - 指定救援系统类型(1=windows,2=linux) require
	 * @param   string client_operate_password - 操作密码,需要验证时传
	 */
	public function rescue()
	{
		$param = request()->param();

		$CloudValidate = new CloudValidate();
		if (!$CloudValidate->scene('rescue')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($CloudValidate->getError())]);
        }

		try{
			$CloudLogic = new CloudLogic((int)$param['id']);

			$result = $CloudLogic->rescue($param);
			return json($result);
		}catch(\Exception $e){
			return json(['status'=>400, 'msg'=>$e->getMessage()]);
		}
	}

	/**
	 * 时间 2022-06-30
	 * @title 重装系统
 	 * @desc 重装系统
	 * @url /console/v1/mf_dcim/:id/reinstall
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int image_id - 镜像ID require
	 * @param   string password - 密码 require
	 * @param   int port - 端口 require
	 * @param   int part_type - 分区类型0全盘格式化1第一分区格式化 require
	 * @param   string code - 二次验证验证码
	 * @param   string client_operate_password - 操作密码,需要验证时传
	 */
	public function reinstall()
	{
		$param = request()->param();

		$CloudValidate = new CloudValidate();
		if (!$CloudValidate->scene('reinstall')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($CloudValidate->getError())]);
        }

		try{
			$CloudLogic = new CloudLogic((int)$param['id']);

			$result = $CloudLogic->reinstall($param);
			return json($result);
		}catch(\Exception $e){
			return json(['status'=>400, 'msg'=>$e->getMessage()]);
		}
	}

	/**
	 * 时间 2022-06-27
	 * @title 获取图表数据
	 * @desc 获取图表数据
	 * @url /console/v1/mf_dcim/:id/chart
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int start_time - 开始秒级时间
	 * @return  array list - 图表数据
	 * @return  int list[].time - 时间(秒级时间戳)
	 * @return  float list[].in_bw - 进带宽
	 * @return  float list[].out_bw - 出带宽
	 * @return  string unit - 当前单位
	 */
	public function chart()
	{
		$param = request()->param();

		try{
			$CloudLogic = new CloudLogic((int)$param['id']);

			$result = $CloudLogic->chart($param);
			return json($result);
		}catch(\Exception $e){
			return json(['status'=>400, 'msg'=>$e->getMessage()]);
		}
	}

	/**
	 * 时间 2022-06-30
	 * @title 获取网络流量
	 * @desc 获取网络流量
	 * @url /console/v1/mf_dcim/:id/flow
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @return  string total -总流量
	 * @return  string used -已用流量
	 * @return  string leave - 剩余流量
	 * @return  string reset_flow_date - 流量归零时间
     * @return  int total_num - 总流量大小(0=不限)
     * @return  float used_num - 已用流量大小
     * @return  int base_flow - 基础流量(0=不限)
     * @return  int temp_flow - 临时流量
	 */
	public function flowDetail()
	{
		$param = request()->param();

		try{
			$CloudLogic = new CloudLogic((int)$param['id']);

			$result = $CloudLogic->flowDetail($param);
			return json($result);
		}catch(\Exception $e){
			return json(['status'=>400, 'msg'=>$e->getMessage()]);
		}
	}
	
	/**
	 * 时间 2022-07-01
	 * @title 日志
	 * @desc 日志
	 * @url /console/v1/mf_dcim/:id/log
	 * @method  GET
	 * @author hh
	 * @version v1
     * @param int id - 产品ID
     * @param string keywords - 关键字
     * @param int page - 页数
     * @param int limit - 每页条数
     * @param string orderby - 排序 id,description,create_time,ip
     * @param string sort - 升/降序 asc,desc
     * @return array list - 系统日志
     * @return int list[].id - 系统日志ID 
     * @return string list[].description - 描述 
     * @return string list[].create_time - 时间 
     * @return int list[].ip - IP 
     * @return int count - 系统日志总数
	 */
	public function log()
	{
		$param = request()->param();
		$param['type'] = 'host';
		$param['rel_id'] = $param['id'];

		$SystemLogModel = new SystemLogModel();
	 	$data = $SystemLogModel->systemLogList($param);

	 	$result = [
	 		'status' => 200,
	 		'msg'	 => lang_plugins('success_message'),
	 		'data'	 => $data,
	 	];
	 	return json($result);
	}

	/**
	 * 时间 2022-09-14
	 * @title 获取DCIM远程信息
	 * @desc 获取DCIM远程信息
	 * @url /console/v1/mf_dcim/:id/remote_info
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @return  string username - 远程用户名
	 * @return  string password - 远程密码
	 * @return  string port - 远程端口
	 * @return  int ip_num - IP数量
	 */
	public function remoteInfo()
	{
		$param = request()->param();

		try{
			$CloudLogic = new CloudLogic((int)$param['id']);

			$result = $CloudLogic->detail();
			return json($result);
		}catch(\Exception $e){
			return json(['status'=>400, 'msg'=>$e->getMessage()]);
		}
	}

	/**
	 * 时间 2022-07-01
	 * @title 获取IP列表
	 * @desc 获取IP列表
	 * @url /console/v1/mf_dcim/:id/ip
	 * @method  GET
	 * @author hh
	 * @version v1
     * @param int id - 产品ID
     * @param int page 1 页数
     * @param int limit - 每页条数
     * @return int list[].ip - IP
     * @return string list[].subnet_mask - 掩码
     * @return string list[].gateway - 网关
     * @return int count - 总数
	 */
	public function ipList()
	{
		$param = array_merge(request()->param(), ['page' => request()->page, 'limit' => request()->limit, 'sort' => request()->sort]);

		try{
			$CloudLogic = new CloudLogic((int)$param['id']);

			$param['host_id'] = $param['id'];
			$data = $CloudLogic->ipList($param);

			$result = [
	            'status' => 200,
	            'msg'    => lang_plugins('success_message'),
	            'data'   => $data
	        ];

			return json($result);
		}catch(\Exception $e){
			return json(['status'=>400, 'msg'=>$e->getMessage()]);
		}
	}

	/**
	 * 时间 2022-07-29
	 * @title 检查产品是够购买过镜像
	 * @desc 检查产品是够购买过镜像
	 * @url /console/v1/mf_dcim/:id/image/check
	 * @method GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int image_id - 镜像ID require
	 * @param   int is_downstream 0 是否下游(0=不是,1=是)
	 * @return  string price - 需要支付的金额(0.00表示镜像免费或已购买)
	 * @return  string description - 描述
	 */
	public function checkHostImage()
	{
		$param = request()->param();

		$ImageModel = new ImageModel();

		$result = $ImageModel->checkHostImage($param);
		return json($result);
	}

	/**
	 * 时间 2022-07-29
	 * @title 生成购买镜像订单
	 * @desc 生成购买镜像订单
	 * @url /console/v1/mf_dcim/:id/image/order
	 * @method POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   int image_id - 镜像ID require
	 * @return  string id - 订单ID
	 */
	public function createImageOrder()
	{
		$param = request()->param();

		$ImageModel = new ImageModel();

		$result = $ImageModel->createImageOrder($param);
		return json($result);
	}

	/**
	 * 时间 2023-02-13
	 * @title 计算产品配置升级价格
	 * @desc 计算产品配置升级价格
	 * @url /console/v1/mf_dcim/:id/common_config
	 * @method  GET
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   string ip_num - 公网IP数量
     * @param   string bw - 带宽
     * @param   int flow - 流量包
     * @param   int peak_defence - 防御峰值
     * @param   object optional_memory - 变更后的内存({"5":1},5是ID,1是数量)
     * @param   object optional_disk - 变更后的硬盘({"5":1},5是ID,1是数量)
     * @param   object optional_gpu - 变更后的硬盘({"5":1},5是ID,1是数量)
     * @return  string price - 价格
     * @return  string description - 生成的订单描述
     * @return  string price_difference - 差价
     * @return  string renew_price_difference - 续费差价
     * @return  string discount - 用户等级折扣
	 */
	public function calCommonConfigPrice()
	{
		$param = request()->param();

 		try{
        	$CloudLogic = new CloudLogic($param['id']);

			$result = $CloudLogic->calCommonConfigPrice($param);
			if(isset($result['data']['new_config_data'])) unset($result['data']['new_config_data']);
			if(isset($result['data']['order_item'])) unset($result['data']['order_item']);
			if(isset($result['data']['optional'])) unset($result['data']['optional']);
			if(isset($result['data']['new_admin_field'])) unset($result['data']['new_admin_field']);
        }catch(\Exception $e){
			$result = ['status'=>400, 'msg'=>$e->getMessage()];
        }
        return json($result);
	}


	/**
	 * 时间 2023-02-13
	 * @title 生成产品配置升级订单
	 * @desc 生成产品配置升级订单
	 * @url /console/v1/mf_dcim/:id/common_config/order
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   string ip_num - 公网IP数量
     * @param   string bw - 带宽
     * @param   int flow - 流量包
     * @param   int peak_defence - 防御峰值
     * @param   object optional_memory - 变更后的内存({"5":1},5是ID,1是数量)
     * @param   object optional_disk - 变更后的硬盘({"5":1},5是ID,1是数量)
     * @param   object optional_gpu - 变更后的硬盘({"5":1},5是ID,1是数量)
	 * @return  string data.id - 订单ID
	 */
	public function createCommonConfigOrder()
	{
		$param = request()->param();

        try{
        	$CloudLogic = new CloudLogic($param['id']);

			$result = $CloudLogic->createCommonConfigOrder($param);
        }catch(\Exception $e){
			$result = ['status'=>400, 'msg'=>$e->getMessage()];
        }
        return json($result);
	}

	/**
	 * 时间 2023-02-14
	 * @title 获取升级防御配置
	 * @desc 获取升级防御配置
	 * @url /console/v1/mf_dcim/:id/upgrade_defence_config
	 * @method  GET
	 * @author theworld
	 * @version v1
	 * @param   int id - 产品ID  require
	 * @param   string ip - IP require
	 * @return  string defence[].value - 防御
	 * @return  string defence[].desc - 防御显示
	 * @return  string current_defence - IP当前防御
	 */
	public function defenceConfig()
	{
		$param = request()->param();

		$LineModel = new LineModel();

		$data = $LineModel->defenceConfig($param);

		$result = [
			'status' => 200,
			'msg'	 => lang_plugins('success_message'),
			'data'	 => $data,
		];
		return json($result);
	}

	/**
	 * 时间 2023-02-13
	 * @title 计算升级防御价格
	 * @desc 计算升级防御价格
	 * @url /console/v1/mf_dcim/:id/upgrade_defence/price
	 * @method  GET
	 * @author theworld
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   string ip - IP require
	 * @param   string peak_defence - 防御峰值 require
     * @return  string price - 价格
     * @return  string description - 生成的订单描述
     * @return  string price_difference - 差价
     * @return  string renew_price_difference - 续费差价
     * @return  string base_price - 基础价格
	 */
	public function calDefencePrice()
	{
		$param = request()->param();

 		try{
            // 需要升级的子产品ID
            $subHostId = HostLinkModel::where('parent_host_id', $param['id'])->where('ip',$param['ip']??'')->value('host_id');
            if (!empty($subHostId)){
                $param['id'] = $subHostId;
            }
        	$CloudLogic = new CloudLogic($param['id']);

			$result = $CloudLogic->calDefencePrice($param);
			if(isset($result['data']['new_config_data'])) unset($result['data']['new_config_data']);
			if(isset($result['data']['order_item'])) unset($result['data']['order_item']);
			if(isset($result['data']['new_admin_field'])) unset($result['data']['new_admin_field']);
        }catch(\Exception $e){
			$result = ['status'=>400, 'msg'=>$e->getMessage()];
        }
        return json($result);
	}


	/**
	 * 时间 2023-02-13
	 * @title 生成升级防御订单
	 * @desc 生成升级防御订单
	 * @url /console/v1/mf_dcim/:id/upgrade_defence/order
	 * @method  POST
	 * @author theworld
	 * @version v1
	 * @param   int id - 产品ID require
	 * @param   string ip - IP require
	 * @param   string peak_defence - 防御峰值 require
	 * @return  string data.id - 订单ID
	 */
	public function createDefenceOrder()
	{
		$param = request()->param();

        try{
            // 需要升级的子产品ID
            $subHostId = HostLinkModel::where('parent_host_id', $param['id'])->where('ip',$param['ip']??'')->value('host_id');
            // TODO WYH 兼容老数据
            if (empty($subHostId) && !empty($param['ip'])){
                // 获取
                $IpDefenceModel = new IpDefenceModel();
                $ipDefence = $IpDefenceModel->where('host_id',$param['id'])
                    ->where('ip',$param['ip'])
                    ->find();
                if (!empty($ipDefence['defence'])){
                    $parentHostLink = HostLinkModel::where('host_id',$param['id'])->find();
                    if (!empty($parentHostLink)){
                        $configData = json_decode($parentHostLink['config_data'],true);
                        $tmp = explode('_',$ipDefence['defence']);
                        $defaultDefenceId = $tmp[2];
                        $CloudLogicOld = new CloudLogic($param['id']);
                        $CloudLogicOld->ipChange([
                            'ips' => [$param['ip']],
                            'only_create' => true,
                            'init' => true, // 初始化
                            'firewall_type' => $tmp[0] . '_' . $tmp[1],
                            'default_defence_id' => $defaultDefenceId,
                            'config_data' => $configData
                        ]);
                    }
                }
                // 需要升级的子产品ID
                $subHostId = HostLinkModel::where('parent_host_id', $param['id'])->where('ip',$param['ip']??'')->value('host_id');
            }
            $param['id'] = $subHostId;
        	$CloudLogic = new CloudLogic($param['id']);

			$result = $CloudLogic->createDefenceOrder($param);
        }catch(\Exception $e){
			$result = ['status'=>400, 'msg'=>$e->getMessage()];
        }
        return json($result);
	}

	/**
	 * 时间 2023-03-01
	 * @title 验证下单
	 * @desc 验证下单,用于下游使用
	 * @url /console/v1/product/:id/mf_dcim/validate_settle
	 * @method  POST
	 * @author hh
	 * @version v1
	 * @param   int id - 商品ID require
     * @param   int custom.duration_id - 周期ID require
     * @param   int custom.data_center_id - 数据中心ID require
     * @param   int custom.line_id - 线路ID require
     * @param   int custom.model_config_id - 型号配置ID require
     * @param   object custom.optional_memory - 变更后的内存({"5":1},5是ID,1是数量)
     * @param   object custom.optional_disk - 变更后的硬盘({"5":1},5是ID,1是数量)
     * @param   object custom.optional_gpu - 变更后的硬盘({"5":1},5是ID,1是数量)
     * @param   int custom.image_id - 镜像ID require
     * @param   string custom.bw - 带宽
     * @param   string custom.flow - 流量
     * @param   string custom.ip_num - 公网IP数量 require
     * @param   int custom.peak_defence - 防御峰值(G)
     * @param   string custom.notes - 备注
     * @param   int custom.auto_renew 0 是否自动续费(0=否,1=是)
     * @return  string price - 价格 
     * @return  string renew_price - 续费价格 
     * @return  string billing_cycle - 周期 
     * @return  int duration - 周期时长
     * @return  string description - 订单子项描述
     * @return  string base_price - 基础价格
     * @return  string preview[].name - 配置项名称
     * @return  string preview[].value - 配置项值
     * @return  string preview[].price - 配置项价格
     * @return  string discount - 用户等级折扣
     * @return  string order_item[].type - 订单子项类型(addon_idcsmart_client_level=用户等级)
     * @return  int order_item[].rel_id - 关联ID
     * @return  float order_item[].amount - 子项金额
     * @return  string order_item[].description - 子项描述
	 */
	public function validateSettle()
	{
		$param = request()->param();

		$CartValidate = new CartValidate();
		if(!$CartValidate->scene('cal')->check($param['custom'] ?? [])){
            return json(['status'=>400 , 'msg'=>lang_plugins($CartValidate->getError())]);
        }
        // 验证限制规则
        $LimitRuleModel = new LimitRuleModel();
        $checkLimitRule = $LimitRuleModel->checkLimitRule($param['id'], $param['custom']);
        if($checkLimitRule['status'] == 400){
            return json($checkLimitRule);
        }
        
        $param['product'] = ProductModel::find($param['id']);

		$DurationModel = new DurationModel();

		$res = $DurationModel->cartCalculatePrice($param, false);
		return json($res);
	}

}
