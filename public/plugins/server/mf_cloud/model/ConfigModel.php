<?php 
namespace server\mf_cloud\model;

use app\common\model\ProductDurationRatioModel;
use think\Model;
use app\common\model\ServerModel;
use app\common\model\HostModel;
use app\common\model\ProductModel;
use app\common\model\OrderModel;
use server\mf_cloud\idcsmart_cloud\IdcsmartCloud;
use server\mf_cloud\logic\ToolLogic;
use server\mf_cloud\logic\DownstreamCloudLogic;

/**
 * @title 设置模型
 * @use server\mf_cloud\model\ConfigModel
 */
class ConfigModel extends Model{

	protected $name = 'module_mf_cloud_config';

    // 设置字段信息
    protected $schema = [
        'id'                        => 'int',
        'product_id'                => 'int',
        'node_priority'             => 'int',
        'ip_mac_bind'               => 'int',
        'support_ssh_key'           => 'int',
        'rand_ssh_port'             => 'int',
        'support_normal_network'    => 'int',
        'support_vpc_network'       => 'int',
        'support_public_ip'         => 'int',
        'backup_enable'             => 'int',
        'snap_enable'               => 'int',
        'disk_limit_enable'         => 'int',
        'reinstall_sms_verify'      => 'int',
        'reset_password_sms_verify' => 'int',
        'niccard'                   => 'int',
        'cpu_model'                 => 'int',
        'ipv6_num'                  => 'string',
        'nat_acl_limit'             => 'string',
        'nat_web_limit'             => 'string',
        'memory_unit'               => 'string',
        'type'                      => 'string',
        'disk_limit_switch'         => 'int',
        'disk_limit_num'            => 'int',
        'free_disk_switch'          => 'int',
        'free_disk_size'            => 'int',
        'only_sale_recommend_config'=> 'int',
        'no_upgrade_tip_show'       => 'int',
        'default_nat_acl'           => 'int',
        'default_nat_web'           => 'int',
        'rand_ssh_port_start'       => 'string',
        'rand_ssh_port_end'         => 'string',
        'rand_ssh_port_windows'     => 'string',
        'rand_ssh_port_linux'       => 'string',
        'default_one_ipv4'          => 'int',
        'manual_manage'             => 'int',
        'upstream_id'               => 'int',
        'is_agent'                  => 'int',
        'global_defence_strategy'   => 'int',
        'sync_firewall_rule'        => 'int',
        'order_default_defence'     => 'string',
        'free_disk_type'            => 'string',
    ];

    // 缓存
    protected $firewallDefenceRule = [];

    // 类型常量
    const TYPE_HOST      = 'host';
    const TYPE_LIGHTHOST = 'lightHost';
    const TYPE_HYPERV    = 'hyperv';

    /**
     * 时间 2022-06-20
     * @title 获取设置
     * @desc  获取设置
     * @author hh
     * @version v1
     * @param   int param.product_id - 商品ID require
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     * @return  int data.ip_mac_bind - 嵌套虚拟化(0=关闭,1=开启)
     * @return  int data.support_ssh_key - 是否支持SSH密钥(0=关闭,1=开启)
     * @return  int data.rand_ssh_port - SSH端口设置(0=默认,1=随机端口,2=指定端口)
     * @return  int data.support_normal_network - 经典网络(0=不支持,1=支持)
     * @return  int data.support_vpc_network - VPC网络(0=不支持,1=支持)
     * @return  int data.support_public_ip - 是否允许公网IP(0=不支持,1=支持)
     * @return  int data.backup_enable - 是否启用备份(0=不启用,1=启用)
     * @return  int data.snap_enable - 是否启用快照(0=不启用,1=启用)
     * @return  int data.disk_limit_enable - 性能限制(0=不启用,1=启用)
     * @return  int data.reinstall_sms_verify - 重装短信验证(0=不启用,1=启用)
     * @return  int data.reset_password_sms_verify - 重置密码短信验证(0=不启用,1=启用)
     * @return  int data.niccard - 网卡驱动(0=默认,1=Realtek 8139,2=Intel PRO/1000,3=Virtio)
     * @return  string data.ipv6_num - IPv6数量
     * @return  string data.nat_acl_limit - NAT转发限制
     * @return  string data.nat_web_limit - NAT建站限制
     * @return  int data.cpu_model - CPU模式(0=默认,1=host-passthrough,2=host-model,3=custom)
     * @return  string data.memory_unit - 内存单位(GB,MB)
     * @return  string data.type - 类型(host=KVM加强版,lightHost=KVM轻量版,hyperv=Hyper-V)
     * @return  int data.node_priority - 开通平衡规则(1=数量平均,2=负载最低,3=内存最低,4=填满一个)
     * @return  int data.disk_limit_switch - 数据盘数量限制开关(0=关闭,1=开启)
     * @return  int data.disk_limit_num - 数据盘限制数量
     * @return  int data.free_disk_switch - 免费数据盘开关(0=关闭,1=开启)
     * @return  int data.free_disk_size - 免费数据盘大小(G)
     * @return  string data.free_disk_type - 免费数据盘类型
     * @return  int data.only_sale_recommend_config - 仅售卖套餐(0=关闭,1=开启)
     * @return  int data.no_upgrade_tip_show - 不可升降级时订购页提示(0=关闭,1=开启)
     * @return  int data.default_nat_acl - 默认NAT转发(0=关闭,1=开启)
     * @return  int data.default_nat_web - 默认NAT建站(0=关闭,1=开启)
     * @return  bool data.is_agent - 是否是代理商(是的时候才能添加资源包)
     * @return  string data.rand_ssh_port_start - 随机端口开始端口
     * @return  string data.rand_ssh_port_end - 随机端口结束端口
     * @return  string data.rand_ssh_port_windows - 指定端口Windows
     * @return  string data.rand_ssh_port_linux - 指定端口Linux
     * @return  int data.default_one_ipv4 - 默认携带IPv4(0=关闭,1=开启)
     * @return  int data.manual_manage - 手动管理商品(0=关闭,1=开启)
     * @return  int data.backup_data[].id - 备份配置ID
     * @return  int data.backup_data[].num - 备份数量
     * @return  string data.backup_data[].price - 备份价格
     * @return  int data.snap_data[].id - 快照配置ID
     * @return  int data.snap_data[].num - 快照数量
     * @return  string data.snap_data[].price - 快照价格
     * @return  int data.resource_package[].id - 资源包ID
     * @return  int data.resource_package[].rid - 魔方云资源包ID
     * @return  string data.resource_package[].name - 资源包名称
     * @return  array data.duration_id - 不允许申请停用周期ID
     * @return  int data.sync_firewall_rule - 同步防火墙规则(0=关闭,1=开启)
     * @return  string data.order_default_defence - 订购默认防御峰值
     */
    public function indexConfig($param)
    {
        $ProductModel = ProductModel::find($param['product_id'] ?? 0);
        if(empty($ProductModel)){
            return ['status'=>400, 'msg'=>lang_plugins('product_not_found')];
        }
        if($ProductModel->getModule() != 'mf_cloud'){
            return ['status'=>400, 'msg'=>lang_plugins('product_not_link_idcsmart_cloud_module')];
        }

        $where = [];
        $where[] = ['product_id', '=', $param['product_id']];

        $config = $this
                ->where($where)
                ->find();
        if(empty($config)){
            $config = $this->getDefaultConfig();

            $insert = $config;
            $insert['product_id'] = $ProductModel->id;
            $this->insert($insert);
        }else{
            unset($config['id'], $config['product_id']);
        }

        // 是否支持代理商
        $config['is_agent'] = false;
        if($ProductModel['type'] == 'server'){
            $server = ServerModel::find($ProductModel['rel_id']);
            if(!empty($server)){
                $hash = ToolLogic::formatParam($server['hash']);
                $config['is_agent'] = isset($hash['account_type']) && $hash['account_type'] == 'agent';
            }
        }

        $BackupConfigModel = new BackupConfigModel();
        $backupData = $BackupConfigModel->backupConfigList(['product_id'=>$param['product_id'], 'type'=>'backup']);
        $config['backup_data'] = $backupData['list'];

        $backupData = $BackupConfigModel->backupConfigList(['product_id'=>$param['product_id'], 'type'=>'snap']);
        $config['snap_data'] = $backupData['list'];


        $config['resource_package'] = [];
        if($config['is_agent']){
            $config['resource_package'] = ResourcePackageModel::field('id,rid,name')->where('product_id', $ProductModel->id)->select()->toArray();
        }

        $DurationModel = new DurationModel();
        $duration = $DurationModel->getNotSupportApplyForSuspendDuration($param['product_id']);
        $config['duration_id'] = array_column($duration, 'id');

        $result = [
            'status' => 200,
            'msg'    => lang_plugins('success_message'),
            'data'   => $config,
        ];
        return $result;
    }

    /**
     * 时间 2022-06-20
     * @title 保存其他设置
     * @desc 保存其他设置
     * @author hh
     * @version v1
     * @param  int param.product_id - 商品ID require
     * @param  string param.type - 类型(host=KVM加强版,lightHost=KVM轻量版,hyperv=Hyper-V) require
     * @param  int param.node_priority - 开通平衡规则(1=数量平均,2=负载最低,3=内存最低,4=填满一个) require
     * @param  int param.ip_mac_bind - 嵌套虚拟化(0=关闭,1=开启)
     * @param  int param.support_ssh_key - 是否支持SSH密钥(0=关闭,1=开启)
     * @param  int param.rand_ssh_port - SSH端口设置(0=默认,1=随机端口,2=指定端口) require
     * @param  int param.support_normal_network - 经典网络(0=不支持,1=支持)
     * @param  int param.support_vpc_network - VPC网络(0=不支持,1=支持)
     * @param  int param.support_public_ip - 是否允许公网IP(0=不支持,1=支持)
     * @param  int param.backup_enable - 是否启用备份(0=不启用,1=启用) require
     * @param  int param.snap_enable - 是否启用快照(0=不启用,1=启用)
     * @param  int param.reinstall_sms_verify - 重装短信验证(0=不启用,1=启用) require
     * @param  int param.reset_password_sms_verify - 重置密码短信验证(0=不启用,1=启用) require
     * @param  int param.niccard - 网卡驱动(0=默认,1=Realtek 8139,2=Intel PRO/1000,3=Virtio)
     * @param  int param.cpu_model - CPU模式(0=默认,1=host-passthrough,2=host-model,3=custom)
     * @param  string param.ipv6_num - IPv6数量
     * @param  string param.nat_acl_limit - NAT转发限制
     * @param  string param.nat_web_limit - NAT建站限制
     * @param  int param.default_nat_acl - 默认NAT转发(0=关闭,1=开启)
     * @param  int param.default_nat_web - 默认NAT建站(0=关闭,1=开启)
     * @param  array param.backup_data - 允许备份数量数据
     * @param  int param.backup_data[].num - 数量
     * @param  float param.backup_data[].price - 价格
     * @param  array param.snap_data - 允许快照数量数据
     * @param  int param.snap_data[].num - 数量
     * @param  float param.snap_data[].price - 价格
     * @param  array param.resource_package - 资源包数据
     * @param  int param.resource_package[].rid - 魔方云资源包ID
     * @param  string param.resource_package[].name - 资源包名称
     * @param  string param.rand_ssh_port_start - 随机端口开始端口 requireIf,rand_ssh_port=1
     * @param  string param.rand_ssh_port_end - 随机端口结束端口 requireIf,rand_ssh_port=1
     * @param  string param.rand_ssh_port_windows - 指定端口Windows requireIf,rand_ssh_port=2
     * @param  string param.rand_ssh_port_linux - 指定端口Linux requireIf,rand_ssh_port=2
     * @param  int param.default_one_ipv4 - 默认携带IPv4(0=关闭,1=开启) require
     * @param  int param.manual_manage - 手动管理商品(0=关闭,1=开启) require
     * @param  array param.duration_id - 不允许申请停用周期ID
     * @return int status - 状态(200=成功,400=失败)
     * @return string msg - 信息
     */
    public function saveConfig($param)
    {
        $ProductModel = ProductModel::find($param['product_id']);
        if(empty($ProductModel)){
            return ['status'=>400, 'msg'=>lang_plugins('product_not_found')];
        }
        if($ProductModel->getModule() != 'mf_cloud'){
            return ['status'=>400, 'msg'=>lang_plugins('product_not_link_idcsmart_cloud_module')];
        }
        $isAgent = false;
        $productId = $ProductModel->id;
        if($ProductModel['type'] == 'server'){
            $server = ServerModel::find($ProductModel['rel_id']);
            if(!empty($server)){
                $hash = ToolLogic::formatParam($server['hash']);
                $isAgent = isset($hash['account_type']) && $hash['account_type'] == 'agent';
            }
        }
        if($param['type'] == 'hyperv'){
            // 不能填写的给默认值
            $param['ipv6_num'] = '';
            $param['nat_acl_limit'] = '';
            $param['nat_web_limit'] = '';
            $param['niccard'] = 0;
            $param['cpu_model'] = 0;
            $param['ip_mac_bind'] = 0;
            $param['support_ssh_key'] = 0;
            $param['support_normal_network'] = 1;
            $param['support_vpc_network'] = 0;
            $param['support_public_ip'] = 1;
            $param['snap_enable'] = 0;
            if(isset($param['snap_data'])){
                unset($param['snap_data']);
            }
        }else if($param['type'] == 'lightHost'){
            $param['support_normal_network'] = 1;
            $param['support_vpc_network'] = 0;
            $param['support_public_ip'] = 1;
        }
        
        $appendLog = '';
        if(isset($param['backup_data'])){
            if(count($param['backup_data']) > 5){
                return ['status'=>400, 'msg'=>lang_plugins('over_max_allow_num')];
            }
            if( count(array_unique(array_column($param['backup_data'], 'num'))) != count($param['backup_data'])){
                return ['status'=>400, 'msg'=>lang_plugins('already_add_the_same_number')];
            }
            $BackupConfigModel = new BackupConfigModel();
            $res = $BackupConfigModel->saveBackupConfig($param['product_id'], $param['backup_data'], 'backup');
            $appendLog .= $res['data']['desc'];
        }
        if(isset($param['snap_data'])){
            if(count($param['snap_data']) > 5){
                return ['status'=>400, 'msg'=>lang_plugins('over_max_allow_num')];
            }
            if( count(array_unique(array_column($param['snap_data'], 'num'))) != count($param['snap_data'])){
                return ['status'=>400, 'msg'=>lang_plugins('already_add_the_same_number')];
            }
            $BackupConfigModel = new BackupConfigModel();
            $res = $BackupConfigModel->saveBackupConfig($param['product_id'], $param['snap_data'], 'snap');
            $appendLog .= $res['data']['desc'];
        }
        if($isAgent && isset($param['resource_package'])){
            $ResourcePackageModel = new ResourcePackageModel();
            $ResourcePackageModel->saveResourcePackage($productId, $param['resource_package']);
        }

        $clearData = $this->isClear($productId, $param['type']);

        $config = $this->where('product_id', $param['product_id'])->find();
        if(empty($config)){
            $config = $this->getDefaultConfig();

            $insert = $config;
            $insert['product_id'] = $param['product_id'];
            $this->insert($insert);
        }
        $this->update($param, ['product_id'=>$param['product_id']], ['type','node_priority','ip_mac_bind','support_ssh_key','rand_ssh_port','support_normal_network','support_vpc_network','support_public_ip','backup_enable','snap_enable','reinstall_sms_verify','reset_password_sms_verify','niccard','cpu_model','ipv6_num','nat_acl_limit','nat_web_limit','default_nat_acl','default_nat_web','rand_ssh_port_start','rand_ssh_port_end','rand_ssh_port_windows','rand_ssh_port_linux','default_one_ipv4','manual_manage']);
        if($clearData['clear']){
            if(isset($clearData['line_id']) && !empty($clearData['line_id'])){
                LineModel::whereIn('id', $clearData['line_id'])->delete();
            }
        }

        $DurationModel = new DurationModel();
        $oldDuration = $DurationModel->getNotSupportApplyForSuspendDuration($productId);
        $oldDuration = array_column($oldDuration, 'name');

        $DurationModel->where('product_id', $productId)->where('support_apply_for_suspend', 0)->update(['support_apply_for_suspend'=>1]);
        if(!empty($param['duration_id'])){
            $DurationModel->where('product_id', $productId)->whereIn('id', $param['duration_id'])->update(['support_apply_for_suspend'=>0]);
        }
        $newDuration = $DurationModel->getNotSupportApplyForSuspendDuration($productId);
        $newDuration = array_column($newDuration, 'name');

        $switch = [lang_plugins('switch_off'), lang_plugins('switch_on')];
        $nodePriority = [
            '',
            lang_plugins('node_priority_1'),
            lang_plugins('node_priority_2'),
            lang_plugins('node_priority_3'),
            lang_plugins('node_priority_4'),
        ];
        $niccard = [
            lang_plugins('mf_cloud_default'),
            'Realtek 8139',
            'Intel PRO/1000',
            'Virtio',
        ];
        $cpuModel = [
            lang_plugins('mf_cloud_default'),
            'host-passthrough',
            'host-model',
            'custom',
        ];
        $type = [
            'host'      => lang_plugins('mf_cloud_kvm_plus'),
            'lightHost' => lang_plugins('mf_cloud_kvm_light'),
            'hyperv'    => 'Hyper-V',
        ];
        $randSshPort = [
            lang_plugins('mf_cloud_default'),
            lang_plugins('mf_cloud_rand_port'),
            lang_plugins('mf_cloud_custom_port'),
        ];

        $desc = [
            'node_priority'             => lang_plugins('mf_cloud_config_node_priority'),
            'ip_mac_bind'               => lang_plugins('mf_cloud_config_ip_mac_bind'),
            'support_ssh_key'           => lang_plugins('mf_cloud_config_support_ssh_key'),
            'rand_ssh_port'             => lang_plugins('mf_cloud_config_rand_ssh_port'),
            'support_normal_network'    => lang_plugins('mf_cloud_config_support_normal_network'),
            'support_vpc_network'       => lang_plugins('mf_cloud_config_support_vpc_network'),
            'backup_enable'             => lang_plugins('backup_enable'),
            'snap_enable'               => lang_plugins('snap_enable'),
            'reinstall_sms_verify'      => lang_plugins('mf_cloud_reinstall_sms_verify'),
            'reset_password_sms_verify' => lang_plugins('mf_cloud_reset_password_sms_verify'),
            'niccard'                   => lang_plugins('mf_cloud_niccard'),
            'cpu_model'                 => lang_plugins('mf_cloud_cpu_model'),
            'ipv6_num'                  => lang_plugins('mf_cloud_ipv6_num'),
            'nat_acl_limit'             => lang_plugins('mf_cloud_nat_acl_limit'),
            'nat_web_limit'             => lang_plugins('mf_cloud_nat_web_limit'),
            'type'                      => lang_plugins('mf_cloud_type'),
            'default_nat_acl'           => lang_plugins('mf_cloud_default_nat_acl'),
            'default_nat_web'           => lang_plugins('mf_cloud_default_nat_web'),
            'rand_ssh_port_start'       => lang_plugins('mf_cloud_rand_ssh_port_start'),
            'rand_ssh_port_end'         => lang_plugins('mf_cloud_rand_ssh_port_end'),
            'rand_ssh_port_windows'     => lang_plugins('mf_cloud_rand_ssh_port_windows'),
            'rand_ssh_port_linux'       => lang_plugins('mf_cloud_rand_ssh_port_linux'),
            'default_one_ipv4'          => lang_plugins('mf_cloud_default_one_ipv4'),
            'manual_manage'             => lang_plugins('mf_cloud_manual_manage'),
            'duration_id'               => lang_plugins('mf_cloud_not_support_apply_for_suspend_duration'),
        ];

        $config['node_priority']                = $nodePriority[ $config['node_priority'] ];
        $config['ip_mac_bind']                  = $switch[ $config['ip_mac_bind'] ];
        $config['support_ssh_key']              = $switch[ $config['support_ssh_key'] ];
        $config['rand_ssh_port']                = $randSshPort[ $config['rand_ssh_port'] ];
        $config['support_normal_network']       = $switch[ $config['support_normal_network'] ];
        $config['support_vpc_network']          = $switch[ $config['support_vpc_network'] ];
        $config['backup_enable']                = $switch[ $config['backup_enable'] ];
        $config['snap_enable']                  = $switch[ $config['snap_enable'] ];
        $config['reinstall_sms_verify']         = $switch[ $config['reinstall_sms_verify'] ];
        $config['reset_password_sms_verify']    = $switch[ $config['reset_password_sms_verify'] ];
        $config['niccard']                      = $niccard[ $config['niccard'] ];
        $config['cpu_model']                    = $cpuModel[ $config['cpu_model'] ];
        $config['type']                         = $type[ $config['type'] ];
        $config['default_nat_acl']              = $switch[ $config['default_nat_acl'] ];
        $config['default_nat_web']              = $switch[ $config['default_nat_web'] ];
        $config['default_one_ipv4']             = $switch[ $config['default_one_ipv4'] ];
        $config['manual_manage']                = $switch[ $config['manual_manage'] ];
        $config['duration_id']                  = implode(',', $oldDuration);

        if(isset($param['node_priority']) && $param['node_priority'] !== '')   $param['node_priority'] = $nodePriority[ $param['node_priority'] ];
        if(isset($param['ip_mac_bind']) && $param['ip_mac_bind'] !== '') $param['ip_mac_bind'] = $switch[ $param['ip_mac_bind'] ];
        if(isset($param['support_ssh_key']) && $param['support_ssh_key'] !== '') $param['support_ssh_key'] = $switch[ $param['support_ssh_key'] ];
        if(isset($param['rand_ssh_port']) && $param['rand_ssh_port'] !== '') $param['rand_ssh_port'] = $randSshPort[ $param['rand_ssh_port'] ];
        if(isset($param['support_normal_network']) && $param['support_normal_network'] !== '') $param['support_normal_network'] = $switch[ $param['support_normal_network'] ];
        if(isset($param['support_vpc_network']) && $param['support_vpc_network'] !== '') $param['support_vpc_network'] = $switch[ $param['support_vpc_network'] ];
        if(isset($param['backup_enable']) && $param['backup_enable'] !== '') $param['backup_enable'] = $switch[ $param['backup_enable'] ];
        if(isset($param['snap_enable']) && $param['snap_enable'] !== '') $param['snap_enable'] = $switch[ $param['snap_enable'] ];
        if(isset($param['reinstall_sms_verify']) && $param['reinstall_sms_verify'] !== '') $param['reinstall_sms_verify'] = $switch[ $param['reinstall_sms_verify'] ];
        if(isset($param['reset_password_sms_verify']) && $param['reset_password_sms_verify'] !== '') $param['reset_password_sms_verify'] = $switch[ $param['reset_password_sms_verify'] ];
        if(isset($param['niccard']) && $param['niccard'] !== '') $param['niccard'] = $niccard[ $param['niccard'] ];
        if(isset($param['cpu_model']) && $param['cpu_model'] !== '') $param['cpu_model'] = $cpuModel[ $param['cpu_model'] ];
        if(isset($param['type']) && $param['type'] !== '') $param['type'] = $type[ $param['type'] ];
        if(isset($param['default_nat_acl']) && $param['default_nat_acl'] !== '') $param['default_nat_acl'] = $switch[ $param['default_nat_acl'] ];
        if(isset($param['default_nat_web']) && $param['default_nat_web'] !== '') $param['default_nat_web'] = $switch[ $param['default_nat_web'] ];
        if(isset($param['default_one_ipv4']) && $param['default_one_ipv4'] !== '') $param['default_one_ipv4'] = $switch[ $param['default_one_ipv4'] ];
        if(isset($param['manual_manage']) && $param['manual_manage'] !== '') $param['manual_manage'] = $switch[ $param['manual_manage'] ];
        $param['duration_id'] = implode(',', $newDuration);

        $description = ToolLogic::createEditLog($config, $param, $desc);
        if(!empty($description) || !empty($appendLog) ){
            $description = lang_plugins('log_modify_config_success', [
                '{product}' => 'product#'.$productId.'#'.$ProductModel->name.'#',
                '{detail}'  => $description.$appendLog,
            ]);
            active_log($description, 'product', $param['product_id']);
        }
        return ['status'=>200, 'msg'=>lang_plugins('update_success')];
    }

    /**
     * 时间 2023-02-02
     * @title 切换配置开关
     * @desc 切换配置开关
     * @author hh
     * @version v1
     * @param   int param.product_id - 商品ID require
     * @param   string param.field - 要修改的字段 require
     * @param   int param.status - 开关状态(0=关闭,1=开启) require
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     */
    public function toggleSwitch($param)
    {
        $ProductModel = ProductModel::find($param['product_id']);
        if(empty($ProductModel)){
            return ['status'=>400, 'msg'=>lang_plugins('product_not_found')];
        }
        if($ProductModel->getModule() != 'mf_cloud'){
            return ['status'=>400, 'msg'=>lang_plugins('product_not_link_idcsmart_cloud_module')];
        }
        $config = $this->where('product_id', $param['product_id'])->find();
        if(empty($config)){
            $config = $this->getDefaultConfig();

            $insert = $config;
            $insert['product_id'] = $param['product_id'];
            $this->insert($insert);
        }
        $this->update([ $param['field'] => $param['status'] ], ['product_id'=>$ProductModel->id]);

        return ['status'=>200, 'msg'=>lang_plugins('update_success')];
    }

    /**
     * 时间 2023-02-01
     * @title 获取其他设置默认值
     * @desc 获取其他设置默认值
     * @author hh
     * @version v1
     * @return  string type - 类型(host=加强版,lightHost=轻量版,hyperv=Hyper-V)
     * @return  int node_priority - 开通平衡规则(1=数量平均,2=负载最低,3=内存最低,4=填满一个)
     * @return  int ip_mac_bind - 嵌套虚拟化(0=关闭,1=开启)
     * @return  int support_ssh_key - 是否支持SSH密钥(0=关闭,1=开启)
     * @return  int rand_ssh_port - SSH端口设置(0=默认,1=随机端口,2=指定端口)
     * @return  int support_normal_network - 经典网络(0=不支持,1=支持)
     * @return  int support_vpc_network - VPC网络(0=不支持,1=支持)
     * @return  int support_public_ip - 是否允许公网IP(0=不支持,1=支持)
     * @return  int backup_enable - 是否启用备份(0=不启用,1=启用)
     * @return  int snap_enable - 是否启用快照(0=不启用,1=启用)
     * @return  int disk_limit_enable - 性能限制(0=不启用,1=启用)
     * @return  int reinstall_sms_verify - 重装短信验证(0=不启用,1=启用)
     * @return  int reset_password_sms_verify - 重置密码短信验证(0=不启用,1=启用)
     * @return  int niccard - 网卡驱动(0=默认,1=Realtek 8139,2=Intel PRO/1000,3=Virtio)
     * @return  int cpu_model - CPU模式(0=默认,1=host-passthrough,2=host-model,3=custom)
     * @return  string ipv6_num - IPv6数量
     * @return  string nat_acl_limit - NAT转发
     * @return  string nat_web_limit - NAT建站
     * @return  string memory_unit - 内存单位(GB,MB)
     * @return  int disk_limit_switch - 数据盘数量限制开关(0=关闭,1=开启)
     * @return  int disk_limit_num - 数据盘限制数量
     * @return  int free_disk_switch - 免费数据盘开关(0=关闭,1=开启)
     * @return  int free_disk_size - 免费数据盘大小(G)
     * @return  int only_sale_recommend_config - 仅售卖套餐(0=关闭,1=开启)
     * @return  int no_upgrade_tip_show - 不可升降级时订购页提示(0=关闭,1=开启)
     * @return  int default_nat_acl - 默认NAT转发(0=关闭,1=开启)
     * @return  int default_nat_web - 默认NAT建站(0=关闭,1=开启)
     * @return  string rand_ssh_port_start - 随机端口开始端口
     * @return  string rand_ssh_port_end - 随机端口结束端口
     * @return  string rand_ssh_port_windows - 指定端口Windows
     * @return  string rand_ssh_port_linux - 指定端口Linux
     * @return  int default_one_ipv4 - 默认携带IPv4(0=关闭,1=开启)
     * @return  int manual_manage - 手动管理商品(0=关闭,1=开启)
     * @return  int sync_firewall_rule - 同步防火墙规则(0=关闭,1=开启)
     * @return  string order_default_defence - 订购默认防御峰值
     * @return  string free_disk_type - 免费数据盘类型
     */
    public function getDefaultConfig()
    {
        $defaultConfig = [
            'type'                      => 'host',
            'node_priority'             => 1,
            'ip_mac_bind'               => 0,
            'support_ssh_key'           => 0,
            'rand_ssh_port'             => 0,
            'support_normal_network'    => 1,
            'support_vpc_network'       => 0,
            'support_public_ip'         => 0,
            'backup_enable'             => 0,
            'snap_enable'               => 0,
            'disk_limit_enable'         => 0,
            'reinstall_sms_verify'      => 0,
            'reset_password_sms_verify' => 0,
            'niccard'                   => 0,
            'cpu_model'                 => 0,
            'ipv6_num'                  => '',
            'nat_acl_limit'             => '',
            'nat_web_limit'             => '',
            'memory_unit'               => 'GB',
            'disk_limit_switch'         => 0,
            'disk_limit_num'            => 16,
            'free_disk_switch'          => 0,
            'free_disk_size'            => 1,
            'only_sale_recommend_config'=> 0,
            'no_upgrade_tip_show'       => 1,
            'default_nat_acl'           => 0,
            'default_nat_web'           => 0,
            'rand_ssh_port_start'       => '',
            'rand_ssh_port_end'         => '',
            'rand_ssh_port_windows'     => '',
            'rand_ssh_port_linux'       => '',
            'default_one_ipv4'          => 1,
            'manual_manage'             => 0,
            'sync_firewall_rule'        => 0,
            'order_default_defence'     => '',
            'free_disk_type'            => '',
        ];
        return $defaultConfig;
    }

    /**
     * 时间 2022-09-25
     * @title 计算备份配置价格
     * @desc  计算备份配置价格
     * @author hh
     * @version v1
     * @param   int param.id - 产品ID require
     * @param   string param.type - 类型(snap=快照,backup=备份) require
     * @param   int param.num - 数量 require
     * @param   int param.is_downstream 0 是否下游发起(0=否,1=是)
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     * @return  string data.price - 价格
     * @return  string data.description - 描述
     * @return  string data.price_difference - 差价
     * @return  string data.renew_price_difference - 续费差价
     * @return  string data.backup_config.type - 类型(snap=快照,backup=备份)
     * @return  int data.backup_config.num - 数量
     * @return  string data.backup_config.price - 价格
     * @return  string data.base_price - 基础价格
     */
    public function calConfigPrice($param)
    {
        bcscale(2);
        // 验证产品和用户
        $host = HostModel::find($param['id']);
        if(empty($host) || $host['status'] != 'Active' || $host['is_delete']){
            return ['status'=>400, 'msg'=>lang_plugins('host_not_create')];
        }
        $productId = $host['product_id'];
        // 前台判断
        $app = app('http')->getName();
        if($app == 'home'){
            if($host['client_id'] != get_client_id()){
                return ['status'=>400, 'msg'=>lang_plugins('host_is_not_exist')];
            }
        }    
        $hostLink = HostLinkModel::where('host_id', $param['id'])->find();
        if(empty($hostLink)){
            return ['status'=>400, 'msg'=>lang_plugins('host_not_create')];
        }
        if( $hostLink[ $param['type'].'_num' ] == $param['num']){
            return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_num_not_change')];
        }
        $ConfigModel = ConfigModel::where('product_id', $host['product_id'])->find();

        $type = ['backup'=>lang_plugins('backup'), 'snap'=>lang_plugins('snap')];

        // 已用数量
        $used = 0;

        $DownstreamCloudLogic = new DownstreamCloudLogic($host);
        if($DownstreamCloudLogic->isDownstream()){
            if($param['type'] == 'backup'){
                $res = $DownstreamCloudLogic->backupList([
                    'page'  => 1,
                    'limit' => 999,
                ]);
            }else{
                $res = $DownstreamCloudLogic->snapshotList([
                    'page'  => 1,
                    'limit' => 999,
                ]);
            }
            if($res['status'] != 200){
                return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_host_status_except_please_wait_and_retry')];
            }
            $used = $res['data']['count'] ?? 0;
        }else{
            $ServerModel = ServerModel::find($host['server_id']);
            $IdcsmartCloud = new IdcsmartCloud($ServerModel);
            // 当前已用数量
            $res = $IdcsmartCloud->cloudSnapshot($hostLink['rel_id'], ['per_page'=>999, 'type'=>$param['type']]);
            if($res['status'] != 200){
                return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_host_status_except_please_wait_and_retry')];
            }
            $used = $res['data']['meta']['total'] ?? 0;
        }
        if($param['num'] < $used){
            return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_cannot_downgrade_to_this_num', ['{num}'=>$param['num']]) ];
        }
        if(!isset($ConfigModel[$param['type'].'_enable']) || $ConfigModel[$param['type'].'_enable'] == 0){
            return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_not_support_buy_backup', ['{type}'=>$type[$param['type']] ]) ];
        }
        $arr = BackupConfigModel::where('product_id', $host['product_id'])
            ->where('type', $param['type'])
            ->select()
            ->toArray();
        $arr = array_column($arr, 'price', 'num');
        if(!isset($arr[ $param['num'] ])){
            return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_not_support_buy_this_num')];
        }
        $configData = json_decode($hostLink['config_data'], true);

        // 试用
        if ($configData['duration']['id'] == config('idcsmart.pay_ontrial')){
            return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_not_support_this_duration_to_upgrade')];
        }

        // 匹配周期
        $duration = DurationModel::where('product_id', $productId)->where('num', $configData['duration']['num'])->where('unit', $configData['duration']['unit'])->find();
        if(empty($duration)){
            return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_not_support_upgrade')];
        }
        $firstDuration = DurationModel::field('id,name,num,unit')->where('product_id', $productId)->orderRaw('field(unit, "hour","day","month")')->order('num', 'asc')->find();
        // 计算倍率
        $multiplier = 1;
        // if($duration['unit'] == $firstDuration['unit']){
        //     $multiplier = round($duration['num']/$firstDuration['num'], 2);
        // }else{
        //     if($duration['unit'] == 'day' && $firstDuration['unit'] == 'hour'){
        //         $multiplier = round($duration['num']*24/$firstDuration['num'], 2);
        //     }else if($duration['unit'] == 'month' && $firstDuration['unit'] == 'hour'){
        //         $multiplier = round($duration['num']*30*24/$firstDuration['num'], 2);
        //     }else if($duration['unit'] == 'month' && $firstDuration['unit'] == 'day'){
        //         $multiplier = round($duration['num']*30/$firstDuration['num'], 2);
        //     }
        // }
        $diffTime = $host['due_time'] - time();

        $price = 0;
        $priceDifference = 0;

        $ProductDurationRatioModel = new ProductDurationRatioModel();
        $firstRatio = $ProductDurationRatioModel->where('product_id',$productId)->where('duration_id',$firstDuration['id'])->value('ratio');
        $ratio = $ProductDurationRatioModel->where('product_id',$productId)->where('duration_id',$duration['id'])->value("ratio");
        if ($firstRatio>0 && $ratio>0){
            $multiplier = $ratio/$firstRatio;
        }

        // 原价,找不到数量就当成0
        $oldPrice = bcmul($arr[ $hostLink[ $param['type'].'_num' ] ] ?? 0, $multiplier);
        $price = bcmul($arr[ $param['num'] ], $multiplier);

        // 增加价格系数
        $oldPrice = bcmul($oldPrice, $duration['price_factor']);
        $price = bcmul($price, $duration['price_factor']);

        $backupConfigData = [
            'type'  => $param['type'],
            'num'   => $param['num'],
            'price' => $price,
        ];
    
        if($host['billing_cycle'] == 'free'){
            $price = 0;
            $priceDifference = 0;
        }else{
            // 周期
            $priceDifference = bcsub($price, $oldPrice);
            $price = $priceDifference * $diffTime/$host['billing_cycle_time'];
        }
        $description = $type[$param['type']]. lang_plugins('mf_cloud_num') . '：' . $hostLink[ $param['type'].'_num' ].' => '.$param['num'];

        $basePrice = bcadd($host['base_price'],$priceDifference,2);
        
        // 下游
        $isDownstream = isset($param['is_downstream']) && $param['is_downstream'] == 1;
        if($isDownstream){
            $DurationModel = new DurationModel();
            $price = $DurationModel->downstreamSubClientLevelPrice([
                'product_id' => $productId,
                'client_id'  => $host['client_id'],
                'price'      => $price,
            ]);
            $priceDifference = $DurationModel->downstreamSubClientLevelPrice([
                'product_id' => $productId,
                'client_id'  => $host['client_id'],
                'price'      => $priceDifference,
            ]);
            // 返给下游的基础价格
            $basePrice = $DurationModel->downstreamSubClientLevelPrice([
                'product_id' => $productId,
                'client_id'  => $host['client_id'],
                'price'      => $basePrice,
            ]);
        }

        $realPriceDifference = $price;

        $price = max(0, $price);
        $price = amount_format($price);

        $result = [
            'status' => 200,
            'msg'    => lang_plugins('success_message'),
            'data'   => [
                'price' => $price,
                'description' => $description,
                'price_difference' => $realPriceDifference,
                'renew_price_difference' => $priceDifference,
                'backup_config' => $backupConfigData,
                'base_price' => $basePrice
            ]
        ];

        return $result;
    }

    /**
     * 时间 2022-07-29
     * @title 生成备份/快照数量订单
     * @desc 生成备份/快照数量订单
     * @author hh
     * @version v1
     * @param   int param.id - 产品ID require
     * @param   string param.type - 类型(snap=快照,backup=备份) require
     * @param   int param.num - 数量 require
     * @return  int status - 状态码(200=成功,400=失败)
     * @return  string msg - 提示信息
     * @return  string data.id - 订单ID
     */
    public function createBackupConfigOrder($param)
    {
        if(isset($param['is_downstream'])){
            unset($param['is_downstream']);
        }
        $hookRes = hook('before_host_upgrade', [
            'host_id'   => $param['id'],
            'scene_desc'=> lang_plugins('mf_cloud_upgrade_scene_change_'.$param['type'].'_num'),
        ]);
        foreach($hookRes as $v){
            if(isset($v['status']) && $v['status'] == 400){
                return $v;
            }
        }
        $res = $this->calConfigPrice($param);
        if($res['status'] == 400){
            return $res;
        }

        $OrderModel = new OrderModel();

        $data = [
            'host_id'     => $param['id'],
            'client_id'   => get_client_id(),
            'type'        => 'upgrade_config',
            'amount'      => $res['data']['price'],
            'description' => $res['data']['description'],
            'price_difference' => $res['data']['price_difference'],
            'renew_price_difference' => $res['data']['renew_price_difference'],
            'base_price' => $res['data']['base_price'],
            'upgrade_refund' => 0,
            'config_options' => [
                'type'       => 'modify_backup',
                'backup_type' => $param['type'],
                'num' => $param['num'],
                'backup_config' => $res['data']['backup_config'],
            ],
            'customfield' => $param['customfield'] ?? [],
        ];
        return $OrderModel->createOrder($data);
    }

    /**
     * 时间 2023-08-22
     * @title 是否清空配置
     * @desc  是否清空配置,套餐和线路
     * @author hh
     * @version v1
     * @param   int productId - 商品ID require
     * @param   string newType - 类型(host=KVM加强版,lightHost=KVM轻量版,hyperv=Hyper-V) require
     * @return  bool clear - 是否清空(false=否,true=是)
     * @return  array recommend_config_id - 套餐ID
     * @return  array line_id - 线路ID
     * @return  string desc - 描述
     */
    public function isClear($productId, $newType)
    {
        $result = [
            'clear' => false
        ];

        $config = $this->where('product_id', $productId)->find();
        if(empty($config)){
            return $result;
        }
        if($config['type'] == 'host'){
            if($newType == 'lightHost'){
                
            }else if($newType == 'hyperv'){
                $flowLine = LineModel::alias('l')
                            ->field('l.id,l.name')
                            ->leftJoin('module_mf_cloud_data_center dc', 'l.data_center_id=dc.id')
                            ->where('dc.product_id', $productId)
                            ->where('l.bill_type', 'flow')
                            ->select()
                            ->toArray();

                if(!empty($flowLine)){
                    $desc = lang_plugins('mf_cloud_switch_type_will_delete');
                    $desc .= lang_plugins('mf_cloud_line') . ':' . implode(',', array_column($flowLine, 'name'));
                    
                    $result = [
                        'clear' => true,
                        'recommend_config_id' => [],
                        'line_id' => array_column($flowLine, 'id'),
                        'desc' => rtrim($desc, ','),
                    ];
                }
            }
        }else if($config['type'] == 'lightHost'){
            if($newType == 'host'){
                
            }else if($newType == 'hyperv'){
                $flowLine = LineModel::alias('l')
                            ->field('l.id,l.name')
                            ->leftJoin('module_mf_cloud_data_center dc', 'l.data_center_id=dc.id')
                            ->where('dc.product_id', $productId)
                            ->where('l.bill_type', 'flow')
                            ->select()
                            ->toArray();
                if(!empty($flowLine)){
                    $desc = lang_plugins('mf_cloud_switch_type_will_delete');
                    $desc .= lang_plugins('mf_cloud_line') . ':' . implode(',', array_column($flowLine, 'name'));

                    $result = [
                        'clear' => true,
                        'recommend_config_id' => [],
                        'line_id' => array_column($flowLine, 'id'),
                        'desc' => rtrim($desc, ','),
                    ];
                }
            }
        }
        return $result;
    }

    /**
     * 时间 2023-09-06
     * @title 保存数据盘数量限制
     * @desc  保存数据盘数量限制
     * @author hh
     * @version v1
     * @param   int param.product_id - 商品ID require
     * @param   int param.disk_limit_switch - 数据盘数量限制开关(0=关闭,1=开启)
     * @param   int param.disk_limit_num - 数据盘限制数量
     * @return  int status - 状态码(200=成功,400=失败)
     * @return  string msg - 提示信息
     */
    public function saveDiskNumLimitConfig($param)
    {
        $ProductModel = ProductModel::find($param['product_id']);
        if(empty($ProductModel)){
            return ['status'=>400, 'msg'=>lang_plugins('product_not_found')];
        }
        if($ProductModel->getModule() != 'mf_cloud'){
            return ['status'=>400, 'msg'=>lang_plugins('product_not_link_idcsmart_cloud_module')];
        }
        $productId = $ProductModel->id;
        
        $config = $this->where('product_id', $param['product_id'])->find();
        if(empty($config)){
            $config = $this->getDefaultConfig();

            $insert = $config;
            $insert['product_id'] = $param['product_id'];
            $this->insert($insert);
        }
        $this->update($param, ['product_id'=>$param['product_id']], ['disk_limit_switch','disk_limit_num']);
        
        $switch = [lang_plugins('switch_off'), lang_plugins('switch_on')];
        
        $desc = [
            'disk_limit_switch' => lang_plugins('mf_cloud_disk_limit_switch'),
            'disk_limit_num'    => lang_plugins('mf_cloud_disk_limit_num'),
        ];

        $config['disk_limit_switch'] = $switch[ $config['disk_limit_switch'] ];
        $param['disk_limit_switch']  = $switch[ $param['disk_limit_switch'] ];

        $description = ToolLogic::createEditLog($config, $param, $desc);
        if(!empty($description)){
            $description = lang_plugins('log_modify_config_success', [
                '{product}' => 'product#'.$productId.'#'.$ProductModel->name.'#',
                '{detail}'  => $description,
            ]);
            active_log($description, 'product', $param['product_id']);
        }
        return ['status'=>200, 'msg'=>lang_plugins('update_success')];
    }

    /**
     * 时间 2023-09-11
     * @title 保存免费数据盘配置
     * @desc  保存免费数据盘配置
     * @author hh
     * @version v1
     * @param   int param.product_id - 商品ID require
     * @param   int param.free_disk_switch - 免费数据盘开关(0=关闭,1=开启) require
     * @param   int param.free_disk_size - 免费数据盘大小(G)
     * @param   string param.free_disk_type - 免费数据盘类型
     * @return  int status - 状态码(200=成功,400=失败)
     * @return  string msg - 提示信息
     */
    public function saveFreeDiskConfig($param)
    {
        $ProductModel = ProductModel::find($param['product_id']);
        if(empty($ProductModel)){
            return ['status'=>400, 'msg'=>lang_plugins('product_not_found')];
        }
        if($ProductModel->getModule() != 'mf_cloud'){
            return ['status'=>400, 'msg'=>lang_plugins('product_not_link_idcsmart_cloud_module')];
        }
        $productId = $ProductModel->id;
        
        $config = $this->where('product_id', $param['product_id'])->find();
        if(empty($config)){
            $config = $this->getDefaultConfig();

            $insert = $config;
            $insert['product_id'] = $param['product_id'];
            $this->insert($insert);
        }
        if(isset($param['free_disk_size']) && $param['free_disk_switch'] <= 0){
            unset($param['free_disk_size']);
        }

        $this->update($param, ['product_id'=>$param['product_id']], ['free_disk_switch','free_disk_size','free_disk_type']);
        
        $switch = [lang_plugins('switch_off'), lang_plugins('switch_on')];
        
        $desc = [
            'free_disk_switch' => lang_plugins('mf_cloud_free_disk_switch'),
            'free_disk_size'    => lang_plugins('mf_cloud_free_disk_size'),
            'free_disk_type'    => lang_plugins('mf_cloud_free_disk_type'),
        ];

        $config['free_disk_switch'] = $switch[ $config['free_disk_switch'] ];
        $param['free_disk_switch']  = $switch[ $param['free_disk_switch'] ];

        $description = ToolLogic::createEditLog($config, $param, $desc);
        if(!empty($description)){
            $description = lang_plugins('log_modify_config_success', [
                '{product}' => 'product#'.$productId.'#'.$ProductModel->name.'#',
                '{detail}'  => $description,
            ]);
            active_log($description, 'product', $param['product_id']);
        }
        return ['status'=>200, 'msg'=>lang_plugins('update_success')];
    }

    /**
     * 时间 2024-02-19
     * @title 获取数据盘数量限制
     * @desc 获取数据盘数量限制
     * @author hh
     * @version v1
     * @param   int $productId - 商品ID require
     * @return  int
     */
    public function getDataDiskLimitNum($productId)
    {
        $config = $this
            ->field('disk_limit_switch,disk_limit_num')
            ->where('product_id', $productId)
            ->find();
        if(!empty($config)){
            return $config['disk_limit_switch'] == 1 ? $config['disk_limit_num'] : 16;
        }else{
            return 16;
        }
    }

    /**
     * @时间 2025-01-14
     * @title 保存全局防御设置
     * @desc  保存全局防御设置
     * @author hh
     * @version v1
     * @param   int param.product_id - 商品ID require
     * @param   int param.sync_firewall_rule - 同步防火墙规则(0=关闭,1=开启)
     * @param   string param.order_default_defence - 订购默认防御
     * @return  int status - 状态码(200=成功,400=失败)
     * @return  string msg - 提示信息
     */
    public function saveGlobalDefenceConfig($param)
    {
        $ProductModel = ProductModel::find($param['product_id']);
        if(empty($ProductModel)){
            return ['status'=>400, 'msg'=>lang_plugins('product_not_found')];
        }
        if($ProductModel->getModule() != 'mf_cloud'){
            return ['status'=>400, 'msg'=>lang_plugins('product_not_link_idcsmart_cloud_module')];
        }
        $productId = $ProductModel->id;

        $OptionModel = new OptionModel();
        if($param['sync_firewall_rule'] == 1){
            // 验证
            if(!empty($param['order_default_defence'])){
                $option = $OptionModel
                        ->where('product_id', $productId)
                        ->where('rel_type', OptionModel::GLOBAL_DEFENCE)
                        ->where('value', $param['order_default_defence'])
                        ->find();
                if(empty($option)){
                    return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_defence_rule_not_found') ];
                }
            }else{
                $param['order_default_defence'] = '';
            }
        }else{
            $param['order_default_defence'] = '';
        }
        
        $config = $this->where('product_id', $param['product_id'])->find();
        if(empty($config)){
            $config = $this->getDefaultConfig();

            $insert = $config;
            $insert['product_id'] = $param['product_id'];
            $this->insert($insert);
        }

        $this->startTrans();
        try{
            $this->update($param, ['product_id'=>$param['product_id']], ['sync_firewall_rule','order_default_defence']);

            if($param['sync_firewall_rule'] == 0){
                $optionId = $OptionModel
                        ->where('product_id', $param['product_id'])
                        ->where('rel_type', OptionModel::GLOBAL_DEFENCE)
                        ->column('id');
                if(!empty($optionId)){
                    $OptionModel->whereIn('id', $optionId)->delete();
                    PriceModel::where('product_id', $param['product_id'])->where('rel_type', PriceModel::REL_TYPE_OPTION)->whereIn('rel_id', $optionId)->delete();
                }
            }

            $this->commit();
        }catch(\Exception $e){
            $this->rollback();

            return ['status'=>400, 'msg'=>$e->getMessage() ];
        }
        
        $switch = [lang_plugins('switch_off'), lang_plugins('switch_on')];
        
        $desc = [
            'sync_firewall_rule' => lang_plugins('mf_cloud_config_sync_firewall_rule'),
        ];

        $config['sync_firewall_rule'] = $switch[ $config['sync_firewall_rule'] ];
        $param['sync_firewall_rule']  = $switch[ $param['sync_firewall_rule'] ];

        $description = ToolLogic::createEditLog($config, $param, $desc);
        if(!empty($description)){
            $description = lang_plugins('log_modify_config_success', [
                '{product}' => 'product#'.$productId.'#'.$ProductModel->name.'#',
                '{detail}'  => $description,
            ]);
            active_log($description, 'product', $param['product_id']);
        }
        return ['status'=>200, 'msg'=>lang_plugins('update_success')];
    }

    /**
     * 时间 2025-01-14
     * @title 获取防火墙防御规则
     * @desc  获取防火墙防御规则
     * @author hh
     * @version v1
     * @param   int param.product_id - 商品ID require
     * @return  array rule - 防火墙规则
     * @return  string rule[].type - 防火墙类型
     * @return  array rule[].list - 防御规则
     * @return  int rule[].list[].id - 防御规则ID
     * @return  string rule[].list[].name - 名称
     * @return  string rule[].list[].defense_peak - 防御峰值,单位Gbps
     * @return  int rule[].list[].enabled - 是否可用(0=否1=是)
     * @return  int rule[].list[].create_time - 创建时间
     * @return  int rule[].list[].update_time - 更新时间
     * @return  string rule[].type - 防火墙类型
     * @return  string rule[].name - 防火墙名称
     */
    public function firewallDefenceRule($param)
    {
        $rule = [];
        $hookRes = hook('firewall_set_meal_list', ['product_id' => intval($param['product_id'] ?? 0)]);
        foreach ($hookRes as $key => $value) {
            if(isset($value['type']) && !empty($value['list']) ){
                $rule[] = $value;
            }
        }
        $result = [
            'rule' => $rule,
        ];
        return $result;
    }

    /**
     * @时间 2025-01-15
     * @title 获取防火墙规则
     * @desc  获取防火墙规则
     * @author hh
     * @version v1
     * @param   int param.product_id - 商品ID require
     * @param   string param.firewall_type - 防火墙类型 require
     * @param   int param.defence_rule_id - 防御规则ID require
     */
    public function getFirewallDefenceRule($param)
    {
        $data = [];
        if(!isset($this->firewallDefenceRule[ $param['product_id'] ])){
            $result = $this->firewallDefenceRule($param);
            
            $this->firewallDefenceRule[ $param['product_id'] ] = $result;
        }else{
            $result = $this->firewallDefenceRule[ $param['product_id'] ];
        }
        foreach($result['rule'] as $v){
            if($param['firewall_type'] == $v['type']){
                foreach($v['list'] as $vv){
                    if($param['defence_rule_id'] == $vv['id']){
                        $data = $vv;
                        break;
                    }
                }
            }
        }
        return $data;
    }



}