<?php 
namespace server\mf_cloud\idcsmart_cloud;

use think\facade\Cache;

/**
 * 云操作类,v3.3.8
 */
class IdcsmartCloud{

	protected $username = '';  // 用户名
	protected $password = '';  // 密码
	protected $url 		= '';  // 基础地址,包含二级目录
	protected $cache    = '';  // 登录缓存标识
	protected $timeout  = 30;  // 超时时间
	protected $isAgent  = false;  // 是否是代理商

	public function __construct($config){
		$this->username = $config['username'];
		$this->password = $config['password'];
		$this->url 		= rtrim($config['url'], '/');
		if(!empty($config['id'])){
			$this->cache    = 'MODULE_IDCSMART_CLOUD_'.$config['id'];
		}
		if($config['is_agent']){
			$this->isAgent = true;
		}
	}

	public function setIsAgent($is_agent){
		$this->isAgent = $is_agent;
	}

	/* 限速组 */

	/**
	 * 时间 2022-06-10
	 * @title 限速组列表
	 * @author hh
	 * @version v1
	 * @param   array $params 请求参数
	 */
	public function bwList(array $params = []){
		return $this->request('bws', $params);
	}

	/**
	 * 时间 2022-06-10
	 * @title 限速组IP列表
	 * @author hh
	 * @version v1
	 * @param   int $id 限速组ID
	 * @param   array $params 请求参数
	 */
	public function bwIpList(int $id, array $params = []){
		return $this->request('bws/'.$id.'/ip', $params);
	}

	/**
	 * 时间 2022-06-10
	 * @title 限速组IP列表
	 * @author hh
	 * @version v1
	 * @param   int $id 限速组ID
	 * @param   array $params 请求参数
	 */
	public function bwModify(int $id, array $params = []){
		return $this->request('bws/'.$id, $params, 'PUT');
	}

	/* 实例 */

	/**
	 * 时间 2023-02-14
	 * @title 实例列表
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 */
	public function cloudList(array $params = []){
		return $this->request('clouds', $params);
	}

	/**
	 * 时间 2022-06-08
	 * @title 实例详情
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 */
	public function cloudDetail(int $id){
		return $this->request('clouds/'.$id);
	}

	/**
	 * 时间 2022-06-08
	 * @title 创建实例
	 * @author hh
	 * @version v1
	 * @param   array $params 创建参数
	 */
	public function cloudCreate(array $params){
		return $this->request('clouds', $params, 'POST');
	}

	/**
	 * 时间 2022-06-08
	 * @title 实例开机
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 */
	public function cloudOn(int $id){
		return $this->request('clouds/'.$id.'/on', [], 'POST');
	}

	/**
	 * 时间 2022-06-08
	 * @title 实例关机
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 */
	public function cloudOff(int $id){
		return $this->request('clouds/'.$id.'/off', [], 'POST');
	}

	/**
	 * 时间 2022-06-08
	 * @title 实例重启
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 */
	public function cloudReboot(int $id){
		return $this->request('clouds/'.$id.'/reboot', [], 'POST');
	}

	/**
	 * 时间 2022-06-08
	 * @title 实例硬重启
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 */
	public function cloudHardReboot(int $id){
		return $this->request('clouds/'.$id.'/hard_reboot', [], 'POST');
	}

	/**
	 * 时间 2022-06-08
	 * @title 实例硬关机
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 */
	public function cloudHardOff(int $id){
		return $this->request('clouds/'.$id.'/hardoff', [], 'POST');
	}

	/**
	 * 时间 2022-06-08
	 * @title 实例暂停
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 */
	public function cloudSuspend(int $id, array $params = []){
		return $this->request('clouds/'.$id.'/suspend', $params, 'POST');
	}

	/**
	 * 时间 2022-06-08
	 * @title 实例解除暂停
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 */
	public function cloudUnsuspend(int $id){
		return $this->request('clouds/'.$id.'/unsuspend', [], 'POST');
	}

	/**
	 * 时间 2022-06-08
	 * @title 实例解除挂起
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 */
	public function cloudResume(int $id){
		return $this->request('clouds/'.$id.'/resume', [], 'POST');
	}

	/**
	 * 时间 2022-06-08
	 * @title 实例重装
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 */
	public function cloudReinstall(int $id, array $params){
		return $this->request('clouds/'.$id.'/reinstall', $params, 'PUT');
	}

	/**
	 * 时间 2022-06-08
	 * @title 实例VNC
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 */
	public function cloudVnc(int $id){
		return $this->request('clouds/'.$id.'/vnc', [], 'POST');
	}

	/**
	 * 时间 2022-06-08
	 * @title 获取实例状态
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 */
	public function cloudStatus(int $id){
		return $this->request('clouds/'.$id.'/status');
	}

	/**
	 * 时间 2022-06-08
	 * @title 修改实例带宽
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 */
	public function cloudModifyBw(int $id, array $params){
		return $this->request('clouds/'.$id.'/bw', $params, 'PUT');
	}

	/**
	 * 时间 2022-06-08
	 * @title 增加临时流量
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 */
	public function cloudIncTempTraffic(int $id, int $traffic){
		return $this->request('clouds/'.$id.'/temp_traffic', ['traffic'=>$traffic], 'PUT');
	}

	/**
	 * 时间 2022-06-08
	 * @title 进入救援系统
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 */
	public function cloudRescue(int $id, array $params){
		return $this->request('clouds/'.$id.'/rescue', $params, 'POST');
	}

	/**
	 * 时间 2022-06-08
	 * @title 退出救援系统
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 */
	public function cloudExitRescue(int $id){
		return $this->request('clouds/'.$id.'/rescue', [], 'DELETE');
	}

	/**
	 * 时间 2022-06-08
	 * @title 重置密码
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 * @param   string $password 新密码
	 */
	public function cloudResetPassword(int $id, string $password){
		return $this->request('clouds/'.$id.'/password', ['password'=>$password], 'PUT');
	}

	/**
	 * 时间 2022-06-09
	 * @title 删除实例
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 * @param   int $force 是否强制删除(0=放入回收站,1=直接删除)
	 */
	public function cloudDelete(int $id, int $force = 0){
		return $this->request('clouds/'.$id, ['force'=>$force], 'DELETE');
	}

	/**
	 * 时间 2022-06-09
	 * @title 修改实例
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 * @param   array $params 修改参数
	 */
	public function cloudModify(int $id, array $params){
		return $this->request('clouds/'.$id, $params, 'PUT');
	}

	/**
	 * 时间 2022-06-09
	 * @title 修改实例IP数量
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 * @param   array $params 修改参数
	 */
	public function cloudModifyIpNum(int $id, array $params){
		return $this->request('clouds/'.$id.'/ip', $params, 'PUT');
	}

	/**
	 * 时间 2022-07-12
	 * @title 模板列表
	 * @author theworld
	 * @version v1
	 * @param   int|array $id 实例ID
	 * @param   array $param 请求参数
	 */
	public function cloudTemplate($id){
		return $this->request('templates', ['hostid' => $id, 'per_page'=>999]);
	}

	/**
	 * 时间 2022-06-09
	 * @title 创建实例模板
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 * @param   string $name 模板名称
	 */
	public function cloudCreateTemplate(int $id, string $name){
		return $this->request('clouds/'.$id.'/templates', ['name'=>$name], 'POST');
	}

	/**
	 * 时间 2022-06-09
	 * @title 下载RDP
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 */
	public function cloudDownloadRdp(int $id){
		return $this->request('clouds/'.$id.'/download_rdp');
	}

	/**
	 * 时间 2022-06-30
	 * @title 切换网络类型
	 * @author hh
	 * @version v1
	 * @param   int $id  实例ID
	 * @param   array $params 请求参数
	 */
	public function cloudChangeNetworkType(int $id, array $params){
		return $this->request('clouds/'.$id.'/network_type', $params, 'PUT');
	}

	/**
	 * 时间 2022-06-30
	 * @title 切换VPC网络
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 * @param   array $params 请求参数
	 */
	public function cloudChangeVpcNetwork(int $id, array $params){
		return $this->request('clouds/'.$id.'/vpc_networks', $params, 'PUT');
	}

	/**
	 * 时间 2023-11-17
	 * @title 实例变更用户
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID require
	 * @param   array $params 请求参数
	 */
	public function cloudChangeUser($id, $params = []){
		return $this->request('clouds/'.$id.'/users', $params, 'PUT');
	}

	/**
	 * 时间 2023-02-14
	 * @title 修改CPU限制
	 * @author hh
	 * @version v1
	 * @param   int id 实例ID
	 * @param   string cpu_limit CPU限制
	 */
	public function cloudModifyCpuLimit(int $id, $cpu_limit = ''){
		return $this->request('clouds/'.$id.'/cpu_limit', ['cpu_limit'=>$cpu_limit], 'PUT');
	}

	/**
	 * 时间 2023-02-14
	 * @title 修改IPv6数量
	 * @author hh
	 * @version v1
	 * @param   int id 实例ID
	 * @param   int num 目标IPv6数量
	 */
	public function cloudModifyIpv6(int $id, $num = 0){
		return $this->request('clouds/'.$id.'/ipv6', ['num'=>$num], 'PUT');
	}

	/**
	 * 时间 2024-05-11
	 * @title 实例IPv6列表
	 * @desc  实例IPv6列表
	 * @author hh
	 * @version v1
	 * @param   int id 实例ID
	 * @param   array $param 请求参数
	 */
	public function cloudIpv6(int $id, array $param = []){
		return $this->request('clouds/'.$id.'/ipv6', $param, 'GET');
	}

	/**
	 * 时间 2023-02-14
	 * @title 删除IPv6
	 * @author hh
	 * @version v1
	 * @param   int id 实例ID
	 */
	public function cloudDeleteIpv6(int $id, $param = [])
	{
		return $this->request('clouds/'.$id.'/ipv6', $param, 'DELETE');
	}

	/**
	 * 时间 2022-06-27
	 * @title 实例备份/快照列表
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 * @param   array $param 请求参数
	 */
	public function cloudSnapshot(int $id, array $param){
		return $this->request('clouds/'.$id.'/snapshots', $param);
	}

	/**
	 * 时间 2022-06-27
	 * @title 备份/快照还原
	 * @author hh
	 * @version v1
	 * @param   int $id 磁盘ID
	 * @param   array $param 请求参数
	 */
	public function snapshotCreate(int $id, array $param){
		return $this->request('disks/'.$id.'/snapshots', $param, 'POST');
	}

	/**
	 * 时间 2022-06-27
	 * @title 备份/快照还原
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 * @param   int $snapshot_id 快照ID
	 */
	public function snapshotRestore(int $id, int $snapshot_id){
		return $this->request('snapshots/'.$snapshot_id.'/restore', ['hostid'=>$id], 'POST');
	}

	/**
	 * 时间 2022-06-27
	 * @title 删除备份/快照
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 * @param   int $snapshot_id 快照ID
	 */
	public function snapshotDelete(int $id, int $snapshot_id){
		return $this->request('snapshots/'.$snapshot_id, ['hostid'=>$id], 'DELETE');
	}

	/**
	 * 时间 2022-06-30
	 * @title 流量计费统计
	 * @author hh
	 * @version v1
	 * @param   int    $id 实例ID
	 */
	public function netInfo(int $id){
		return $this->request('net_info?host_id='.$id);
	}

	/**
	 * 时间 2022-07-01
	 * @title 创建定时备份/快照
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID 
	 * @param   array $param 请求参数
	 */
	public function cloudCreateCronSnap(int $id, $param){
		return $this->request('clouds/'.$id.'/cron_snap', $param, 'POST');
	}

	/**
	 * 时间 2022-07-01
	 * @title 删除定时备份/快照
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 * @param   int $cron_id 定时任务ID
	 */
	public function cloudDeleteCronSnap(int $id, int $cron_id){
		return $this->request('clouds/'.$id.'/cron_snap/'.$cron_id, [], 'DELETE');
	}

	/**
	 * 时间 2022-07-01
	 * @title 获取定时备份/快照
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID 
	 */
	public function cloudCronSnap(int $id){
		return $this->request('clouds/'.$id.'/cron_snap');
	}


	/* 实例模板 */

	/**
	 * 时间 2022-06-09
	 * @title 删除模板
	 * @author hh
	 * @version v1
	 * @param   int $id 模板ID
	 */
	public function templateDelete(int $id){
		return $this->request('templates/'.$id, [], 'DELETE');
	}

	/* 磁盘 */

	/**
	 * 时间 2022-09-27
	 * @title 添加并挂在磁盘
	 * @author hh
	 * @version v1
	 * @param   [type] $id     实例ID
	 * @param   [type] $params [description]
	 */
	public function addAndMountDisk($id, $params){
		return $this->request('clouds/'.$id.'/disks', $params, 'POST');
	}

	/**
	 * 时间 2022-06-09
	 * @title 删除磁盘
	 * @author hh
	 * @version v1
	 * @param   int $id 磁盘ID
	 */
	public function diskDelete(int $id){
		return $this->request('disks/'.$id, [], 'DELETE');
	}

	/**
	 * 时间 2022-06-09
	 * @title 修改磁盘配置
	 * @author hh
	 * @version v1
	 * @param   int $id 磁盘ID
	 * @param   array $params 请求参数
	 */
	public function diskModify(int $id, array $params){
		return $this->request('disks/'.$id, $params, 'PUT');
	}

	/**
	 * 时间 2022-06-09
	 * @title 卸载磁盘
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 * @param   int $disk_id 磁盘ID
	 * @param   int $force 是否强制卸载
	 */
	public function cloudUmountDisk(int $id, int $disk_id, int $force = 0){
		return $this->request('clouds/'.$id.'/disks/'.$disk_id, ['force'=>$force], 'DELETE');
	}

	/**
	 * 时间 2022-06-09
	 * @title 挂载磁盘
	 * @author hh
	 * @version v1
	 * @param   int $id 磁盘ID
	 */
	public function diskMount(int $id, array $params = []){
		return $this->request('disks/'.$id.'/mount', $params, 'POST');
	}

	/**
	 * 时间 2022-06-09
	 * @title 设为启动盘
	 * @author hh
	 * @version v1
	 * @param   int $id 磁盘ID
	 */
	public function diskSetBoot(int $id){
		return $this->request('disks/'.$id.'/boot', [], 'POST');
	}

	/**
	 * 时间 2022-06-09
	 * @title 创建磁盘
	 * @author hh
	 * @version v1
	 */
	public function diskCreate($params){
		return $this->request('disks', $params, 'POST');
	}

	/**
	 * 时间 2022-07-08
	 * @title 快照列表
	 * @author hh
	 * @version v1
	 */
	public function diskSnapshot($params){
		return $this->request('disks/snapshots', $params);
	}

	/**
	 * 时间 2022-07-08
	 * @title 备份列表
	 * @author hh
	 * @version v1
	 */
	public function diskBackup($params){
		return $this->request('disks/backups', $params);
	}


	/* 弹性IP */

	/**
	 * 时间 2022-06-09
	 * @title 创建弹性IP
	 * @author hh
	 * @version v1
	 * @param array $params 请求参数
	 */
	public function elasticIpCreate($params){
		return $this->request('elastic_ip', $params, 'POST');
	}

	/**
	 * 时间 2022-06-09
	 * @title 附加弹性IP
	 * @author hh
	 * @version v1
	 * @param int $id 弹性IPID
	 * @param array $params 请求参数
	 */
	public function elasticIpAttach(int $id, array $params){
		return $this->request('elastic_ip/'.$id.'/attach', $params, 'POST');
	}

	/**
	 * 时间 2022-06-09
	 * @title 卸下弹性IP
	 * @author hh
	 * @version v1
	 * @param int $id 弹性IPID
	 */
	public function elasticIpDetach(int $id, array $params){
		return $this->request('elastic_ip/'.$id.'/detach', $params, 'POST');
	}

	/**
	 * 时间 2022-06-09
	 * @title 删除弹性IP
	 * @author hh
	 * @version v1
	 * @param array $params 请求参数
	 */
	public function elasticIpDelete(int $id){
		return $this->request('elastic_ip/'.$id, [], 'DELETE');
	}

	/**
	 * 时间 2022-06-09
	 * @title 添加浮动IP
	 * @author hh
	 * @version v1
	 * @param int $id 实例ID
	 * @param array $params 请求参数
	 */
	public function floatIpAdd(int $id, array $params){
		return $this->request('clouds/'.$id.'/floatip', $params, 'POST');
	}

	/**
	 * 时间 2022-06-09
	 * @title 删除浮动IP
	 * @author hh
	 * @version v1
	 * @param int $id 实例ID
	 * @param array $params 请求参数
	 */
	public function floatIpDelete(int $id, array $params){
		return $this->request('clouds/'.$id.'/floatip', $params, 'DELETE');
	}

	/**
	 * 时间 2022-06-09
	 * @title 变更浮动IP
	 * @author hh
	 * @version v1
	 * @param int $id 实例ID
	 * @param array $params 请求参数
	 */
	public function floatIpUpdate(int $id, array $params){
		return $this->request('clouds/'.$id.'/floatip', $params, 'PUT');
	}

	/**
	 * 时间 2022-06-09
	 * @title 获取可用的IP
	 * @author hh
	 * @version v1
	 * @param   array  $param - 请求参数
	 */
	public function getFreeIp(array $param)
	{
		return $this->request('ip/free', $param, 'GET');
	}

	/* 镜像 */

	/**
	 * 时间 2022-09-23
	 * @title 镜像列表
	 * @author hh
	 * @version v1
	 * @param array $params 请求参数
	 */
	public function getImageList($params){
		return $this->request('image', $params);
	}

	/**
	 * 时间 2022-06-21
	 * @title 获取区域/节点下可用镜像
	 * @author hh
	 * @version v1
	 * @param   array  $params - 请求参数
	 */
	public function getEnableImage(array $params){
		return $this->request('node_images', $params);
	}

	/**
	 * 时间 2022-06-20
	 * @title 获取所有系统镜像
	 * @author hh
	 * @version v1
	 */
	public function getImageSystem(){
		return $this->request('images/system');
	}

	/**
	 * 时间 2022-06-20
	 * @title 获取所有自定义镜像
	 * @author hh
	 * @version v1
	 * @param  int status - 状态(是否隐藏,1=已激活,2=已隐藏)
	 */
	public function getImageCustom($status = 0){
		return $this->request('images/custom', ['status'=>$status]);
	}

	/**
	 * 时间 2022-06-28
	 * @title 根据文件名获取镜像ID
	 * @author hh
	 * @version v1
	 * @param   string $filename - 文件名称 require
	 */
	public function getImageId($filename){
		return $this->request('images/search', ['filename'=>$filename], 'POST');
	}

	/**
	 * 时间 2022-06-30
	 * @title 获取镜像详情
	 * @author hh
	 * @version v1
	 * @param   int $id - 镜像ID require
	 */
	public function getImage($id){
		return $this->request('image/'.$id);
	}

	/* 镜像分组 */

	/**
	 * 时间 2022-09-23
	 * @title 获取镜像分组列表
	 * @author hh
	 * @version v1
	 * @param   array $params 请求参数
	 */
	public function getImageGroup($params){
		return $this->request('imageGroup', $params);
	}


	/* 首页 */

	/**
	 * 时间 2022-06-08
	 * @title 获取登录用户信息
	 * @author hh
	 * @version v1
	 */
	public function userInfo(){
		return $this->request('user_info');
	}

	/* ISO */



	/* 图表 */

	/**
	 * 时间 2022-06-08
	 * @title 图表数据
	 * @author hh
	 * @version v1
	 * @param   array $params 接口参数
	 */
	public function chart(array $params){
		return $this->request('statistics', $params);
	}

	/* 用户 */

	/**
	 * 时间 2022-06-08
	 * @title 创建用户
	 * @author hh
	 * @version v1
	 * @param   array $params 创建参数
	 */
	public function userCreate(array $params){
		return $this->request('user', $params, 'POST');
	}

	/**
	 * 时间 2022-06-08
	 * @title 验证用户名是否已存在
	 * @author hh
	 * @version v1
	 * @param   string $username 用户名
	 */
	public function userCheck(string $username){
		return $this->request('user/check', ['username'=>$username], 'POST');
	}

	/* 安全组 */

	/**
	 * 时间 2022-06-09
	 * @title 安全组详情
	 * @author hh
	 * @version v1
	 * @param   int $id 安全组ID
	 */
	public function securityGroupDetail(int $id, int $get_all_rule = 1){
		return $this->request('security_groups/'.$id, ['get_all_rule'=>$get_all_rule]);
	}

	/**
	 * 时间 2022-06-09
	 * @title 创建安全组
	 * @author hh
	 * @version v1
	 * @param   array $params 请求参数
	 */
	public function securityGroupCreate(array $params){
		return $this->request('security_groups', $params, 'POST');
	}

	/**
	 * 时间 2022-06-09
	 * @title 修改安全组
	 * @author hh
	 * @version v1
	 * @param   int $id 安全组ID
	 * @param   array $params 请求参数
	 */
	public function securityGroupModify(int $id, array $params){
		return $this->request('security_groups/'.$id, $params, 'PUT');
	}

	/**
	 * 时间 2022-06-09
	 * @title 删除安全组
	 * @author hh
	 * @version v1
	 * @param   int $id 安全组ID
	 */
	public function securityGroupDelete(int $id){
		return $this->request('security_groups/'.$id, [], 'DELETE');
	}

	/**
	 * 时间 2022-06-09
	 * @title 安全组规则列表
	 * @author hh
	 * @version v1
	 * @param   int $id 安全组ID
	 * @param   array $params 请求参数
	 */
	public function securityGroupRuleList(int $id, array $params){
		return $this->request('security_groups/'.$id.'/rules', $params);
	}

	/**
	 * 时间 2022-06-09
	 * @title 添加安全组规则
	 * @author hh
	 * @version v1
	 * @param   int $id 安全组ID
	 * @param   array $params 请求参数
	 */
	public function securityGroupRuleCreate(int $id, array $params){
		return $this->request('security_groups/'.$id.'/rules', $params, 'POST');
	}

	/**
	 * 时间 2022-06-09
	 * @title 修改安全组规则
	 * @author hh
	 * @version v1
	 * @param   int $id 安全组规则ID
	 * @param   array $params 请求参数
	 */
	public function securityGroupRuleModify(int $id, array $params){
		return $this->request('security_group_rules/'.$id, $params, 'PUT');
	}

	/**
	 * 时间 2022-06-09
	 * @title 删除安全组规则
	 * @author hh
	 * @version v1
	 * @param   int $id 安全组规则ID
	 */
	public function securityGroupRuleDelete(int $id){
		return $this->request('security_group_rules/'.$id, [], 'DELETE');
	}

	/**
	 * 时间 2022-06-09
	 * @title 批量删除安全组规则
	 * @author hh
	 * @version v1
	 * @param   array $id 安全组规则ID
	 */
	public function securityGroupRuleBatchDelete(array $id){
		return $this->request('security_group_rules', $id, 'DELETE');
	}

	/**
	 * 时间 2022-09-08
	 * @title 关联安全组
	 * @author hh
	 * @version v1
	 * @param   int $id 安全组ID
	 * @param   array $params 请求参数
	 */
	public function linkSecurityGroup(int $id, array $params){
		return $this->request('security_groups/'.$id.'/links', $params, 'POST');
	}

	/**
	 * 时间 2022-09-08
	 * @title 解除关联安全组
	 * @author hh
	 * @version v1
	 * @param   int $id 实例ID
	 * @param   array $params 请求参数
	 */
	public function delLinkSecurityGroup(int $id){
		return $this->request('clouds/'.$id.'/security_groups', [], 'DELETE');
	}

	/* SSH密钥 */

	/**
	 * 时间 2022-06-10
	 * @title 创建SSH密钥
	 * @author hh
	 * @version v1
	 * @param   array $params 请求参数
	 */
	public function sshKeyCreate(array $params){
		return $this->request('ssh_keys', $params, 'POST');
	}

	/**
	 * 时间 2022-06-10
	 * @title 删除SSH密钥
	 * @author hh
	 * @version v1
	 * @param   int $id SSH密钥ID
	 */
	public function sshKeyDelete(int $id){
		return $this->request('ssh_keys/'.$id, [], 'DELETE');
	}

	/* 任务 */

	/**
	 * 时间 2022-06-10
	 * @title 任务列表
	 * @author hh
	 * @version v1
	 * @param   array $params 请求参数
	 */
	public function taskList(array $params = []){
		return $this->request('tasks', $params);
	}

	/**
	 * 时间 2022-06-10
	 * @title 任务详情
	 * @author hh
	 * @version v1
	 * @param   int $id 任务ID
	 */
	public function taskDetail(int $id){
		return $this->request('tasks/'.$id);
	}

	/**
	 * 时间 2022-06-10
	 * @title 取消任务
	 * @author hh
	 * @version v1
	 * @param   int $id 任务ID
	 */
	public function taskCancel(int $id){
		return $this->request('tasks/'.$id, [], 'POST');
	}

	/* VPC网络 */
	
	/**
	 * 时间 2022-06-10
	 * @title 修改VPC网络
	 * @author hh
	 * @version v1
	 * @param   int $id VPC网络ID
	 * @param   array $params 请求参数
	 */
	public function vpcNetworkModify(int $id, array $params){
		return $this->request('vpc_networks/'.$id, $params, 'PUT');
	}

	/**
	 * 时间 2022-06-30
	 * @title VPC网络详情
	 * @author hh
	 * @version v1
	 * @param   int $id - VPC网络ID
	 */
	public function vpcNetworkDetail(int $id){
		return $this->request('vpc_networks/'.$id);
	}

	/* 转发建站 */

	/**
	 * 时间 2023-08-30
	 * @title 实例NAT转发列表
	 * @author hh
	 * @version v1
	 * @param   int $id - 实例ID require
	 * @param   array $params - 请求参数
	 */
	public function natAclList($id, $params = []){
		return $this->request('clouds/'.$id.'/nat_acl', $params, 'GET');
	}

	/**
	 * 时间 2023-09-20
	 * @title 创建NAT转发
	 * @author hh
	 * @version v1
	 * @param   int $id - 实例ID require
	 * @param   array $params - 请求参数 require
	 */
	public function natAclCreate($id, $params = []){
		return $this->request('clouds/'.$id.'/nat_acl', $params, 'POST');
	}

	/**
	 * 时间 2023-09-20
	 * @title 删除NAT转发
	 * @author hh
	 * @version v1
	 * @param   int $id - 实例ID require
	 * @param   int $nat_acl_id - 转发ID require
	 */
	public function natAclDelete($id, $nat_acl_id){
		return $this->request('nat_acl/'.$nat_acl_id.'?hostid='.$id, [], 'DELETE');
	}

	/**
	 * 时间 2023-09-20
	 * @title 实例NAT建站列表
	 * @author hh
	 * @version v1
	 * @param   int $id - 实例ID require
	 * @param   array $params - 请求参数
	 */
	public function natWebList($id, $params = []){
		return $this->request('clouds/'.$id.'/nat_web', $params, 'GET');
	}

	/**
	 * 时间 2023-09-20
	 * @title 创建NAT建站
	 * @author hh
	 * @version v1
	 * @param   int $id - 实例ID require
	 * @param   array $params - 请求参数 require
	 */
	public function natWebCreate($id, $params = []){
		return $this->request('clouds/'.$id.'/nat_web', $params, 'POST');
	}

	/**
	 * 时间 2023-09-20
	 * @title 删除NAT建站
	 * @author hh
	 * @version v1
	 * @param   int $id - 实例ID require
	 * @param   int $nat_web_id - 建站ID require
	 */
	public function natWebDelete($id, $nat_web_id){
		return $this->request('nat_web/'.$nat_web_id.'?hostid='.$id, [], 'DELETE');
	}

	/**
	 * @时间 2024-12-20
	 * @title 下载RDP
	 * @desc  下载RDP
	 * @author hh
	 * @version v1
	 * @param   int $id - 实例ID require
	 */
	public function downloadRdp($id){
		return $this->request('clouds/'.$id.'/download_rdp', [], 'GET');
	}

	/**
	 * @时间 2025-01-08
	 * @title 获取节点/区域/节点分组内可用GPU数量
	 * @desc  获取节点/区域/节点分组内可用GPU数量
	 * @author hh
	 * @version v1
     * @param  string type - 类型(node=节点,area=区域,node_group=节点分组) require
     * @param  int id - 对应类型ID require
	 */
	public function getFreeGpu($param)
	{
		$param['id'] = [
			$param['id'] ?? 0,
		];
		return $this->request('through/pcis/gpu_num', $param, 'GET');
	}

	/**
	 * @时间 2025-01-08
	 * @title 获取硬件直通
	 * @desc  获取硬件直通
	 * @author hh
	 * @version v1
	 * @param   int id - 实例ID require
	 */
	public function cloudHardwareThrough($id)
	{
		return $this->request('clouds/'. $id .'/hardware_through', [], 'GET');
	}

	/**
	 * @时间 2025-01-09
	 * @title 实例根据数量挂载显卡
	 * @desc  实例根据数量挂载显卡
	 * @author hh
	 * @version v1
	 * @param   int id - 实例ID require
	 * @param   int num - 显卡数量 require
	 */
	public function cloudMountPciGpu($id, $num)
	{
		return $this->request('clouds/'. $id .'/pci/gpu_num', ['num'=>$num], 'POST');
	}

	/**
	 * @时间 2025-01-09
	 * @title 实例迁移
	 * @desc  实例迁移
	 * @author hh
	 * @version v1
	 * @param   int id - 实例ID require
	 * @param   array param - 迁移参数 require
	 */
	public function cloudMigrate($id, $param = [])
	{
		return $this->request('clouds/'. $id .'/migrate', $param, 'POST');
	}

	/**
	 * 时间 2022-06-08
	 * @title 登录
	 * @author hh
	 * @version v1
	 * @param   bool $force 是否强制登录(忽略缓存)
	 * @param   bool $test  测试缓存是否可用
	 */
	public function login(bool $force = false, bool $test = false){
		if(!$force){
			$token = $this->getCache($this->cache);

			if(!empty($token)){
				// 验证token是否可用
				if($test){
					$result = $this->userInfo();
					if($result['status'] == 200){
						$result = [
							'status'=>200,
							'data'=>[
								'token'=>$token,
							],
						];
					}
				}else{
					$result = [
						'status'=>200,
						'data'=>[
							'token'=>$token,
							'cache'=>true,  // 使用缓存
						],
					];
				}
				return $result;
			}
		}
		// 重新登录
		if($this->isAgent){
			$url = $this->url . '/index.php?path=token';
		}else{
			$url = $this->url . '/v1/login?a=a';
		}

		$data = [
			'username'=>$this->username,
			'password'=>$this->password,
		];
		$res = curl($url, $data, $this->timeout, 'POST');
		if(!empty($res['error'])){
			return ['status'=>400, 'msg'=>'CURL_ERROR: '.$res['error'], 'http_code'=>$res['http_code'] ];
		}
		if($res['http_code'] >= 200 && $res['http_code'] < 300){
			if($this->isAgent){
				$res['content'] = json_decode($res['content'], true);
				$token = $res['content']['token'];
			}else{
				$token = trim($res['content'], '"');
			}

			$this->setCache($this->cache, $token, 12*3600);
			$result = [
				'status'=>200,
				'data'=>[
					'token'=>$token,
				],
			];
		}else{
			$this->deleteCache($this->cache);

			$content = json_decode($res['content'] ?? '', true) ?: [];
			$result = [
				'status'	=> 400,
				'msg'		=> $content['error'] ?? '登录失败',
				'http_code'	=> 400,
			];
		}
		return $result;
	}

	/* 功能方法 */

	/**
	 * 时间 2022-06-08
	 * @title 设置缓存
	 * @desc 设置缓存
	 * @author hh
	 * @version v1
	 * @param   string $name 缓存名称
	 * @param   mixed  $value 缓存内容
	 * @param   int    $time   缓存时间(秒)
	 * @return  mixed  缓存内容
	 */
	public function setCache($name, $value, $time){
		return $name ? Cache::set($name, $value, $time) : false;
	}

	/**
	 * 时间 2022-06-08
	 * @title 获取缓存
	 * @desc 获取缓存
	 * @author hh
	 * @version v1
	 * @param   string $name 缓存名称
	 * @return  mixed
	 */
	public function getCache($name){
		return $name ? Cache::get($name) : false;
	}

	/**
	 * 时间 2022-06-08
	 * @title 删除缓存
	 * @desc 删除缓存
	 * @author hh
	 * @version v1
	 * @param   string $name 缓存名称
	 * @return  bool
	 */
	public function deleteCache($name){
		return $name ? Cache::delete($name) : false;
	}

	/**
	 * 时间 2022-06-08
	 * @title 请求
	 * @author hh
	 * @version v1
	 * @param   string $path    地址
	 * @param   array  $data    数据
	 * @param   string $request 请求方式
	 */
	public function request($path, $data = [], $request = 'GET'){
		$loginRes = $this->login();
		if($loginRes['status'] != 200){
			return $loginRes;
		}
		$header = [
			'access-token: '.$loginRes['data']['token'],
		];

		// 如果是代理商
		if($this->isAgent){
            $url = $this->url . '/index.php?path='.ltrim($path, '/');
        }else{
            $url = $this->url . '/v1/'.ltrim($path);
        }

		// 调用公共curl方法
		$res = curl($url, $data, $this->timeout, $request, $header);
		if(!empty($res['error'])){
			return ['status'=>400, 'msg'=>'CURL_ERROR: '.$res['error']];
		}
		$content = json_decode($res['content'] ?? '', true) ?: [];
		if($res['http_code'] >= 200 && $res['http_code'] < 300){
			$result = [
				'status' => 200,
				'data'	 => $content,
			];
		}else if($res['http_code'] == 401){
			// 登录过期,尝试重新登录并重新调用
			if(isset($loginRes['data']['cache'])){
				$res = $this->login(true);
				if($res['status'] != 200){
					return $res;
				}
				$result = $this->request($path, $data, $request);
			}else{
				$result = ['status'=>400, 'msg'=>$res['error']];
			}
		}else{
			$result = ['status'=>400, 'msg'=>$content['error'] ?? '执行失败'];
		}
		$result['http_code'] = $res['http_code'];
		$result['content'] = $res['content'];
		return $result;
	}
}

