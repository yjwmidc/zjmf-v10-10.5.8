<?php 
namespace server\mf_cloud\model;

use think\Model;
use app\common\model\ProductModel;
use server\mf_cloud\validate\MemoryValidate;
use server\mf_cloud\validate\LineBwValidate;
use server\mf_cloud\validate\LineIpValidate;
use server\mf_cloud\validate\LineIpv6Validate;
use server\mf_cloud\validate\DiskValidate;
use server\mf_cloud\validate\LineDefenceValidate;
use server\mf_cloud\logic\ToolLogic;

/**
 * @title 配置参数模型
 * @use server\mf_cloud\model\OptionModel
 */
class OptionModel extends Model
{
	protected $name = 'module_mf_cloud_option';

    // 设置字段信息
    protected $schema = [
        'id'            => 'int',
        'product_id'    => 'int',
        'rel_type'      => 'int',
        'rel_id'        => 'int',
        'type'          => 'string',
        'value'         => 'string',
        'min_value'     => 'int',
        'max_value'     => 'int',
        'step'          => 'int',
        'other_config'  => 'string',
        'create_time'   => 'int',
        'upstream_id'   => 'int',
        'firewall_type' => 'string',
        'defence_rule_id' => 'int',
        'order'         => 'int',
    ];

    // json字段
    protected $json = ['other_config'];

    // 获取后json字段转为数组
    protected $jsonAssoc  = true;

    // rel_type常量
    const CPU = 0;
    const MEMORY = 1;
    const LINE_BW = 2;
    const LINE_FLOW = 3;
    const LINE_DEFENCE = 4;
    const LINE_IP = 5;
    const SYSTEM_DISK = 6;
    const DATA_DISK = 7;
    const LINE_GPU = 8;  // 已废弃
    const LINE_IPV6 = 9;
    const DATA_CENTER_GPU = 10;
    const GLOBAL_DEFENCE = 11;

    /**
     * 时间 2023-01-31
     * @title CPU配置详情
     * @desc CPU配置详情
     * @author hh
     * @version v1
     * @param   int id - 通用配置ID require
     * @return  int id - 通用配置ID
     * @return  int value - 核心数
     * @return  int duration[].id - 周期ID
     * @return  string duration[].name - 周期名称
     * @return  string duration[].price - 价格
     * @return  string other_config.advanced_cpu - 智能CPU规则ID
     * @return  string other_config.cpu_limit - CPU限制
     */
    public function cpuIndex($id)
    {
        $option = $this->optionIndex($id);
        if(empty($option) || $option['rel_type'] != OptionModel::CPU){
            return (object)[];
        }
        $data = [
            'id' => $option['id'],
            'value' => (int)$option['value'],
            'duration' => $option['duration'],
            'other_config' => $option['other_config'],
        ];
        return $data;
    }

    /**
     * 时间 2023-01-31
     * @title 内存配置详情
     * @desc 内存配置详情
     * @author hh
     * @version v1
     * @param   int id - 配置ID require
     * @return  int id - 配置ID
     * @return  string type - 配置方式(radio=单选,step=阶梯,total=总量)
     * @return  int value - 内存
     * @return  int min_value - 最小值
     * @return  int max_value - 最大值
     * @return  int step - 最小变化值
     * @return  int duration[].id - 周期ID
     * @return  string duration[].name - 周期名称
     * @return  string duration[].price - 价格
     */
    public function memoryIndex($id)
    {
        $option = $this->optionIndex($id);
        if(empty($option) || $option['rel_type'] != OptionModel::MEMORY){
            return (object)[];
        }
        $data = [
            'id' => $option['id'],
            'type' => $option['type'],
            'value' => (int)$option['value'],
            'min_value' => $option['min_value'],
            'max_value' => $option['max_value'],
            'step' => $option['step'],
            'duration' => $option['duration'],
        ];
        return $data;
    }

    /**
     * 时间 2023-01-31
     * @title 线路带宽配置详情
     * @desc  线路带宽配置详情
     * @author hh
     * @version v1
     * @param   int id - 通用配置ID require
     * @return  int id - 通用配置ID
     * @return  string type - 配置方式(radio=单选,step=阶梯,total=总量)
     * @return  int value - 带宽
     * @return  int min_value - 最小值
     * @return  int max_value - 最大值
     * @return  int step - 最小变化值
     * @return  int duration[].id - 周期ID
     * @return  string duration[].name - 周期名称
     * @return  string duration[].price - 价格
     * @return  string other_config.in_bw - 流入带宽
     * @return  string other_config.advanced_bw - 智能带宽规则ID
     */
    public function lineBwIndex($id)
    {
        $option = $this->optionIndex($id);
        if(empty($option) || $option['rel_type'] != OptionModel::LINE_BW){
            return (object)[];
        }
        $data = [
            'id' => $option['id'],
            'type' => $option['type'],
            'value' => (int)$option['value'],
            'min_value' => $option['min_value'],
            'max_value' => $option['max_value'],
            'step' => $option['step'],
            'duration' => $option['duration'],
            'other_config' => $option['other_config'],
        ];
        return $data;
    }

    /**
     * 时间 2023-01-31
     * @title 线路流量配置详情
     * @desc 线路流量配置详情
     * @author hh
     * @version v1
     * @param   int id - 通用配置ID require
     * @return  int id - 通用配置ID
     * @return  int value - 流量
     * @return  int duration[].id - 周期ID
     * @return  string duration[].name - 周期名称
     * @return  string duration[].price - 价格
     * @return  int other_config.in_bw - 入站带宽
     * @return  int other_config.out_bw - 出站带宽
     * @return  int other_config.traffic_type - 计费方向(1=进,2=出,3=进+出)
     * @return  string other_config.bill_cycle - 计费周期(month=自然月,last_30days=购买日循环)
     */
    public function lineFlowIndex($id)
    {
        $option = $this->optionIndex($id);
        if(empty($option) || $option['rel_type'] != OptionModel::LINE_FLOW){
            return (object)[];
        }
        $data = [
            'id' => $option['id'],
            'value' => (int)$option['value'],
            'duration' => $option['duration'],
            'other_config' => $option['other_config'],
        ];
        return $data;
    }

    /**
     * 时间 2023-01-31
     * @title 线路防护配置详情
     * @desc 线路防护配置详情
     * @author hh
     * @version v1
     * @param   int id - 通用配置ID require
     * @return  int id - 通用配置ID
     * @return  string value - 防御峰值(G)
     * @return  string firewall_type - 防火墙类型
     * @return  int defence_rule_id - 防御规则ID
     * @return  int duration[].id - 周期ID
     * @return  string duration[].name - 周期名称
     * @return  string duration[].price - 价格
     */
    public function lineDefenceIndex($id)
    {
        $option = $this->optionIndex($id);
        if(empty($option) || $option['rel_type'] != OptionModel::LINE_DEFENCE){
            return (object)[];
        }
        $data = [
            'id' => $option['id'],
            'value' => $option['value'],
            'firewall_type' => $option['firewall_type'],
            'defence_rule_id' => $option['defence_rule_id'],
            'duration' => $option['duration'],
            'order' => $option['order']??0,
        ];
        return $data;
    }

    /**
     * 时间 2023-01-31
     * @title 线路IP配置详情
     * @desc 线路IP配置详情
     * @author hh
     * @version v1
     * @param   int id - 通用配置ID require
     * @return  int id - 通用配置ID
     * @return  string type - 配置方式(radio=单选,step=阶梯,total=总量)
     * @return  int value - IP数量
     * @return  int min_value - 最小值
     * @return  int max_value - 最大值
     * @return  int step - 最小变化值
     * @return  int duration[].id - 周期ID
     * @return  string duration[].name - 周期名称
     * @return  string duration[].price - 价格
     */
    public function lineIpIndex($id)
    {
        $option = $this->optionIndex($id);
        if(empty($option) || $option['rel_type'] != OptionModel::LINE_IP){
            return (object)[];
        }
        $data = [
            'id'        => $option['id'],
            'type'      => $option['type'],
            'value'     => (int)$option['value'],
            'min_value' => $option['min_value'],
            'max_value' => $option['max_value'],
            'step'      => $option['step'],
            'duration'  => $option['duration'],
        ];
        return $data;
    }

    /**
     * 时间 2023-01-31
     * @title 磁盘配置详情
     * @desc 磁盘配置详情
     * @author hh
     * @version v1
     * @param   int id - 配置ID require
     * @param   int rel_type - 配置类型(6=系统盘,7=数据盘) require
     * @return  int id - 配置ID
     * @return  string type - 配置方式(radio=单选,step=阶梯,total=总量)
     * @return  int value - 磁盘大小
     * @return  int min_value - 最小值
     * @return  int max_value - 最大值
     * @return  int step - 最小变化值
     * @return  int duration[].id - 周期ID
     * @return  string duration[].name - 周期名称
     * @return  string duration[].price - 价格
     * @return  string other_config.disk_type - 磁盘类型
     * @return  string other_config.store_id - 存储ID
     */
    public function diskIndex($id, $rel_type)
    {
        $option = $this->optionIndex($id);
        if(empty($option) || $option['rel_type'] != $rel_type){
            return (object)[];
        }
        $data = [
            'id' => $option['id'],
            'type' => $option['type'],
            'value' => (int)$option['value'],
            'min_value' => $option['min_value'],
            'max_value' => $option['max_value'],
            'step' => $option['step'],
            'duration' => $option['duration'],
            'other_config' => $option['other_config'],
        ];
        return $data;
    }

    /**
     * 时间 2023-02-08
     * @title 获取磁盘类型
     * @desc 获取磁盘类型
     * @author hh
     * @version v1
     * @param  int product_id - 商品ID require
     * @param  int rel_type - 配置类型(6=系统盘,7=数据盘) require
     * @return string list[].name - 磁盘类型名称
     * @return string list[].value - 磁盘类型值
     */
    public function getDiskType($param)
    {
        $list = [];
        $where = [];

        if(isset($param['product_id']) && !empty($param['product_id'])){
            $where[] = ['product_id', '=', $param['product_id']];
        }
        $where[] = ['rel_type', '=', $param['rel_type']];

        $option = $this
                ->where($where)
                ->select();

        foreach($option as $v){
            $list[] = $v['other_config']['disk_type'] ?? '';
        }
        $list = array_unique($list);

        $data = [];
        foreach($list as $v){
            if($v === ''){
                
            }else{
                $data[] = [
                    'name'  => $v,
                    'value' => $v,
                ];
            }
        }
        array_unshift($data, [
            'name'  => lang_plugins('mf_cloud_default'),
            'value' => '',
        ]);
        return ['list'=>$data];
    }

    /**
     * 时间 2024-05-08
     * @title 线路IPv6配置详情
     * @desc  线路IPv6配置详情
     * @author hh
     * @version v1
     * @param   int id - 通用配置ID require
     * @return  int id - 通用配置ID
     * @return  string type - 配置方式(radio=单选,step=阶梯,total=总量)
     * @return  int value - IPv6数量
     * @return  int min_value - 最小值
     * @return  int max_value - 最大值
     * @return  int step - 最小变化值
     * @return  int duration[].id - 周期ID
     * @return  string duration[].name - 周期名称
     * @return  string duration[].price - 价格
     */
    public function lineIpv6Index($id)
    {
        $option = $this->optionIndex($id);
        if(empty($option) || $option['rel_type'] != OptionModel::LINE_IPV6){
            return (object)[];
        }
        $data = [
            'id'        => $option['id'],
            'type'      => $option['type'],
            'value'     => (int)$option['value'],
            'min_value' => $option['min_value'],
            'max_value' => $option['max_value'],
            'step'      => $option['step'],
            'duration'  => $option['duration'],
        ];
        return $data;
    }

    /**
     * 时间 2024-06-21
     * @title 数据中心显卡配置详情
     * @desc  数据中心显卡配置详情
     * @author hh
     * @version v1
     * @param   int id - 通用配置ID require
     * @return  int id - 通用配置ID
     * @return  int value - 显卡数量
     * @return  int duration[].id - 周期ID
     * @return  string duration[].name - 周期名称
     * @return  string duration[].price - 价格
     */
    public function dataCenterGpuIndex($id)
    {
        $option = $this->optionIndex($id);
        if(empty($option) || $option['rel_type'] != OptionModel::DATA_CENTER_GPU){
            return (object)[];
        }
        $data = [
            'id'        => $option['id'],
            'value'     => (int)$option['value'],
            'duration'  => $option['duration'],
        ];
        return $data;
    }

    /**
     * 时间 2023-02-02
     * @title 添加配置
     * @desc 添加配置
     * @author hh
     * @version v1
     * @param   int param.product_id - 商品ID require
     * @param   string param.type - 配置方式(radio=单选,step=阶梯,total=总量) require
     * @param   int param.rel_type - 配置类型(0=CPU配置,1=内存配置,2=线路带宽配置,3=线路流量配置,4=线路防护配置,5=线路附加IP,6=系统盘,7=数据盘,8=线路显卡,9=线路IPv6) require
     * @param   int param.rel_id 0 关联ID(rel_type=2,3,4,5,8,9时是线路ID)
     * @param   int param.value - 值
     * @param   int param.min_value - 最小值
     * @param   int param.max_value - 最大值
     * @param   array param.price - 周期价格(如["5"=>"12"],5是周期ID,12是价格) require
     * @param   array param.other_config - 其他配置
     * @param   string param.other_config.advanced_cpu - 智能CPU规则ID,rel_type=0
     * @param   string param.other_config.cpu_limit - CPU限制,rel_type=0
     * @param   string param.other_config.in_bw - 进带宽,rel_type=2
     * @param   string param.other_config.advanced_bw - 智能带宽规则ID,rel_type=2
     * @param   int param.other_config.in_bw - 入站带宽,rel_type=3 requireIf:rel_type=3
     * @param   int param.other_config.out_bw - 出站带宽,rel_type=3 requireIf:rel_type=3
     * @param   int param.other_config.traffic_type - 计费方向(1=进,2=出,3=进+出),rel_type=3 requireIf:rel_type=3
     * @param   string param.other_config.bill_cycle month 计费周期(month=自然月,last_30days=购买日循环),rel_type=3
     * @param   string param.other_config.disk_type - 磁盘类型,rel_type=6/7
     * @param   string param.other_config.store_id - 储存ID,rel_type=6/7
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     * @return  int data.id - 配置ID
     */
    public function optionCreate($param)
    {
        if(in_array($param['rel_type'], [OptionModel::CPU,OptionModel::MEMORY,OptionModel::SYSTEM_DISK,OptionModel::DATA_DISK])){
            $ProductModel = ProductModel::find($param['product_id']);
            if(empty($ProductModel)){
                return ['status'=>400, 'msg'=>lang_plugins('product_not_found')];
            }
            if($ProductModel->getModule() != 'mf_cloud'){
                return ['status'=>400, 'msg'=>lang_plugins('product_not_link_idcsmart_cloud_module')];
            }
            $productId = $ProductModel['id'];

            $param['rel_id'] = 0;
        }else if(in_array($param['rel_type'], [OptionModel::LINE_BW,OptionModel::LINE_FLOW,OptionModel::LINE_DEFENCE,OptionModel::LINE_IP,OptionModel::LINE_IPV6])){
            $line = LineModel::find($param['rel_id']);
            if(empty($line)){
                return ['status'=>400, 'msg'=>lang_plugins('line_not_found')];
            }
            $dataCenter = DataCenterModel::find($line['data_center_id']);
            if(empty($dataCenter)){
                return ['status'=>400, 'msg'=>lang_plugins('line_not_found')];
            }
            if($param['rel_type'] == OptionModel::LINE_DEFENCE && $line['sync_firewall_rule'] == 1){
                return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_line_only_can_sync_firewall_rule') ];
            }

            $productId = $dataCenter['product_id'];

            $param['rel_id'] = $line['id'];
        }else if(in_array($param['rel_type'], [OptionModel::DATA_CENTER_GPU])){
            $dataCenter = DataCenterModel::find($param['rel_id']);
            if(empty($dataCenter)){
                return ['status'=>400, 'msg'=>lang_plugins('data_center_not_found')];
            }
            $productId = $dataCenter['product_id'];

            $param['rel_id'] = $dataCenter['id'];
        }

        $ConfigModel = new ConfigModel();
        $config = $ConfigModel->indexConfig(['product_id'=>$productId]);
        $config = $config['data'];

        // 验证周期价格
        $duration = DurationModel::where('product_id', $productId)->column('id');

        $this->startTrans();
        try{
            $type = 'radio';
            $insert = [
                'product_id'        => $productId,
                'rel_type'          => $param['rel_type'],
                'rel_id'            => $param['rel_id'],
                'other_config'      => json_encode([]),
                'create_time'       => time(),
            ];

            if($param['rel_type'] == OptionModel::CPU){
                if($config['type'] == 'hyperv'){
                    $insert['other_config'] = json_encode([
                        'advanced_cpu'  => '',
                        'cpu_limit'     => $param['other_config']['cpu_limit'] ?? '',
                    ]);
                }else{
                    $insert['other_config'] = json_encode([
                        'advanced_cpu'  => $param['other_config']['advanced_cpu'] ?? '',
                        'cpu_limit'     => $param['other_config']['cpu_limit'] ?? '',
                    ]);
                }
            }else if($param['rel_type'] == OptionModel::MEMORY){
                // 获取当前类型
                $type = $this
                    ->where('product_id', $productId)
                    ->where('rel_type', $param['rel_type'])
                    ->lock(true)
                    ->value('type');

                $noRaw = false;
                if(empty($type)){
                    $noRaw = true;
                    $type = $param['type'];
                }

                // 先放置在这点
                $MemoryValidate = new MemoryValidate();
                if($type == 'radio'){
                    if (!$MemoryValidate->scene('radio')->check($param)){
                        throw new \Exception(lang_plugins($MemoryValidate->getError()));
                    }
                }else{
                    if (!$MemoryValidate->scene('step')->check($param)){
                        throw new \Exception(lang_plugins($MemoryValidate->getError()));
                    }
                }
                // 没有内存时可以保存单位
                if($noRaw && isset($param['memory_unit']) && !empty($param['memory_unit'])){
                    ConfigModel::where('product_id', $productId)->update([
                        'memory_unit' => strtoupper($param['memory_unit']),
                    ]);
                }
            }else if($param['rel_type'] == OptionModel::LINE_BW){
                if($line['bill_type'] != 'bw'){
                    throw new \Exception(lang_plugins('mf_cloud_line_is_not_bw_cannot_add_bw_option'));
                }
                $type = $this
                    ->where('product_id', $productId)
                    ->where('rel_type', $param['rel_type'])
                    ->where('rel_id', $param['rel_id'])
                    ->lock(true)
                    ->value('type');
                if(empty($type)){
                    $type = $param['type'];
                }

                // 先放置在这点
                $LineBwValidate = new LineBwValidate();
                if($type == 'radio'){
                    if (!$LineBwValidate->scene('radio')->check($param)){
                        throw new \Exception(lang_plugins($LineBwValidate->getError()));
                    }
                }else{
                    if (!$LineBwValidate->scene('step')->check($param)){
                        throw new \Exception(lang_plugins($LineBwValidate->getError()));
                    }
                }
                if($config['type'] == 'hyperv'){
                    $insert['other_config'] = json_encode([
                        'in_bw'         => '',
                        'advanced_bw'   => $param['other_config']['advanced_bw'] ?? '',
                    ]);
                }else{
                    $insert['other_config'] = json_encode([
                        'in_bw'         => $param['other_config']['in_bw'] ?? '',
                        'advanced_bw'   => $param['other_config']['advanced_bw'] ?? '',
                    ]);
                }
            }else if($param['rel_type'] == OptionModel::LINE_FLOW){
                if($line['bill_type'] != 'flow'){
                    throw new \Exception(lang_plugins('mf_cloud_line_is_not_flow_cannot_add_flow_option'));
                }
                $insert['other_config'] = json_encode([
                    'in_bw' => (int)$param['other_config']['in_bw'],
                    'out_bw' => (int)$param['other_config']['out_bw'],
                    'traffic_type' => (int)$param['other_config']['traffic_type'],
                    'bill_cycle' => $param['other_config']['bill_cycle'] ?? 'month',
                ]);
            }else if($param['rel_type'] == OptionModel::LINE_DEFENCE){

            }else if($param['rel_type'] == OptionModel::LINE_IP){
                $type = $this
                    ->where('product_id', $productId)
                    ->where('rel_type', $param['rel_type'])
                    ->where('rel_id', $param['rel_id'])
                    ->lock(true)
                    ->value('type');
                if(empty($type)){
                    $type = $param['type'];
                }

                // 先放置在这点
                $LineIpValidate = new LineIpValidate();
                if($type == 'radio'){
                    if (!$LineIpValidate->scene('radio')->check($param)){
                        throw new \Exception(lang_plugins($LineIpValidate->getError()));
                    }
                }else{
                    if (!$LineIpValidate->scene('step')->check($param)){
                        throw new \Exception(lang_plugins($LineIpValidate->getError()));
                    }
                }
            }else if($param['rel_type'] == OptionModel::SYSTEM_DISK){
                $type = $this
                    ->where('product_id', $productId)
                    ->where('rel_type', $param['rel_type'])
                    ->lock(true)
                    ->value('type');
                if(empty($type)){
                    $type = $param['type'];
                }

                // 先放置在这点
                $DiskValidate = new DiskValidate();
                if($type == 'radio'){
                    if (!$DiskValidate->scene('radio')->check($param)){
                        throw new \Exception(lang_plugins($DiskValidate->getError()));
                    }
                }else{
                    if (!$DiskValidate->scene('step')->check($param)){
                        throw new \Exception(lang_plugins($DiskValidate->getError()));
                    }
                }
                $insert['other_config'] = json_encode([
                    'disk_type' => $param['other_config']['disk_type'] ?? '',
                    'store_id'  => $param['other_config']['store_id'] ?? '',
                ]);
            }else if($param['rel_type'] == OptionModel::DATA_DISK){
                $type = $this
                    ->where('product_id', $productId)
                    ->where('rel_type', $param['rel_type'])
                    ->lock(true)
                    ->value('type');
                if(empty($type)){
                    $type = $param['type'];
                }
                // 先放置在这点
                $DiskValidate = new DiskValidate();
                if($type == 'radio'){
                    if (!$DiskValidate->scene('radio')->check($param)){
                        throw new \Exception(lang_plugins($DiskValidate->getError()));
                    }
                }else{
                    if (!$DiskValidate->scene('step')->check($param)){
                        throw new \Exception(lang_plugins($DiskValidate->getError()));
                    }
                }
                $insert['other_config'] = json_encode([
                    'disk_type' => $param['other_config']['disk_type'] ?? '',
                    'store_id'  => $param['other_config']['store_id'] ?? '',
                ]);
            }else if($param['rel_type'] == OptionModel::LINE_IPV6){
                $type = $this
                    ->where('product_id', $productId)
                    ->where('rel_type', $param['rel_type'])
                    ->where('rel_id', $param['rel_id'])
                    ->lock(true)
                    ->value('type');
                if(empty($type)){
                    $type = $param['type'];
                }

                // 先放置在这点
                $LineIpv6Validate = new LineIpv6Validate();
                if($type == 'radio'){
                    if (!$LineIpv6Validate->scene('radio')->check($param)){
                        throw new \Exception(lang_plugins($LineIpv6Validate->getError()));
                    }
                }else{
                    if (!$LineIpv6Validate->scene('step')->check($param)){
                        throw new \Exception(lang_plugins($LineIpv6Validate->getError()));
                    }
                }
            }

            $insert['type'] = $type;
            if($type == 'radio'){
                
                $whereSame = [];
                $whereSame[] = ['product_id', '=', $productId];
                $whereSame[] = ['rel_type', '=', $param['rel_type']];
                $whereSame[] = ['rel_id', '=', $param['rel_id']];
                $whereSame[] = ['value', '=', $param['value']];

                if(in_array($param['rel_type'], [OptionModel::SYSTEM_DISK, OptionModel::DATA_DISK])){
                    $whereSame[] = ['other_config', 'LIKE', rtrim(str_replace('\\', '\\\\', json_encode(['disk_type'=>$param['other_config']['disk_type'] ?? ''])), '}').'%'];
                }
                $same = $this
                        ->where($whereSame)
                        ->find();
                if(!empty($same)){
                    throw new \Exception(lang_plugins('mf_cloud_already_add_the_same_option'));
                }

                $insert['value'] = $param['value'];
            }else{

                $whereSame = [];
                $whereSame[] = ['product_id', '=', $productId];
                $whereSame[] = ['rel_type', '=', $param['rel_type']];
                $whereSame[] = ['rel_id', '=', $param['rel_id']];
                $whereSame[] = ['min_value', '<=', $param['max_value']];
                $whereSame[] = ['max_value', '>=', $param['min_value']];

                if(in_array($param['rel_type'], [OptionModel::SYSTEM_DISK, OptionModel::DATA_DISK])){
                    $whereSame[] = ['other_config', 'LIKE', rtrim(str_replace('\\', '\\\\', json_encode(['disk_type'=>$param['other_config']['disk_type'] ?? ''])), '}').'%'];
                }

                // 范围是否交叉
                $intersect = $this
                    ->where($whereSame)
                    ->find();
                if(!empty($intersect)){
                    throw new \Exception(lang_plugins('mf_cloud_option_intersect'));
                }
                $insert['min_value'] = $param['min_value'];
                $insert['max_value'] = $param['max_value'];
                $insert['step'] = 1; //$param['step']; 不能设置步长
            }
            $option = $this->create($insert);

            $priceArr = [];
            foreach($duration as $v){
                if(isset($param['price'][$v])){
                    $priceArr[] = [
                        'product_id'    => $productId,
                        'rel_type'      => PriceModel::REL_TYPE_OPTION,
                        'rel_id'        => $option->id,
                        'duration_id'   => $v,
                        'price'         => $param['price'][$v],
                    ];
                }
            }
            if(!empty($priceArr)){
                $PriceModel = new PriceModel();
                $PriceModel->insertAll($priceArr);
            }
            $this->commit();
        }catch(\Exception $e){
            $this->rollback();
            return ['status'=>400, 'msg'=>$e->getMessage() ];
        }

        $optionType = [
            lang_plugins('mf_cloud_option_0'),
            lang_plugins('mf_cloud_option_1'),
            lang_plugins('mf_cloud_option_2'),
            lang_plugins('mf_cloud_option_3'),
            lang_plugins('mf_cloud_option_4'),
            lang_plugins('mf_cloud_option_5'),
            lang_plugins('mf_cloud_option_6'),
            lang_plugins('mf_cloud_option_7'),
            lang_plugins('mf_cloud_option_8'),
            lang_plugins('mf_cloud_option_9'),
            lang_plugins('mf_cloud_option_8'),
            lang_plugins('mf_cloud_option_11'),
        ];

        $nameType = [
            lang_plugins('mf_cloud_option_value_0'),
            lang_plugins('mf_cloud_option_value_1'),
            lang_plugins('mf_cloud_option_value_2'),
            lang_plugins('mf_cloud_option_value_3'),
            lang_plugins('mf_cloud_option_value_4'),
            lang_plugins('mf_cloud_option_value_5'),
            lang_plugins('mf_cloud_option_value_6'),
            lang_plugins('mf_cloud_option_value_7'),
            lang_plugins('mf_cloud_option_value_8'),
            lang_plugins('mf_cloud_option_value_9'),
            lang_plugins('mf_cloud_option_value_8'),
            lang_plugins('mf_cloud_option_value_11'),
        ];

        $productName = ProductModel::where('id', $productId)->value('name');

        $description = lang_plugins('log_mf_cloud_add_option_success', [
            '{product}' => 'product#'.$productId.'#'.$productName.'#',
            '{option}'  => $optionType[ $param['rel_type'] ],
            '{name}'    => $nameType[ $param['rel_type'] ],
            '{detail}'  => $type == 'radio' ? $param['value'] : $param['min_value'].'-'.$param['max_value'],
        ]);
        active_log($description, 'product', $productId);

        $result = [
            'status' => 200,
            'msg'    => lang_plugins('create_success'),
            'data'   => [
                'id' => (int)$option->id,
            ],
        ];
        return $result;
    }

    /**
     * 时间 2023-01-31
     * @title 获取配置列表
     * @desc 获取配置列表
     * @author hh
     * @version v1
     * @param   int param.page 1 页数
     * @param   int param.limit - 每页条数
     * @param   string param.orderby - 排序(id,value)
     * @param   string param.sort - 升降序(asc,desc)
     * @param   int param.product_id - 商品ID
     * @param   int param.rel_type - 配置类型(0=CPU配置,1=内存配置,2=线路带宽配置,3=线路流量配置,4=线路防护配置,5=线路附加IP,6=系统盘,7=数据盘,8=线路显卡)
     * @param   int param.rel_id - 关联ID(rel_type=2,3,4,5,8时是线路ID)
     * @param   string field - 要获取的字段
     * @return  int list[].id - 配置ID
     * @return  int list[].product_id - 商品ID
     * @return  string list[].type - 配置方式(radio=单选,step=阶梯,total=总量)
     * @return  string list[].value - 值
     * @return  int list[].min_value - 最小值
     * @return  int list[].max_value - 最大值
     * @return  int list[].step - 步长
     * @return  array list[].other_config - 其他配置
     * @return  int count - 总条数
     */
    public function optionList($param, $field = null){
        $param['page'] = isset($param['page']) ? ($param['page'] ? (int)$param['page'] : 1) : 1;
        $param['limit'] = isset($param['limit']) ? ($param['limit'] ? (int)$param['limit'] : config('idcsmart.limit')) : config('idcsmart.limit');
        $param['sort'] = isset($param['sort']) ? ($param['sort'] ?: config('idcsmart.sort')) : config('idcsmart.sort');

        if (!isset($param['orderby']) || !in_array($param['orderby'], ['id','value'])){
            $param['orderby'] = 'value,min_value';
        }

        $where = [];
        if(!empty($param['product_id'])){
            $where[] = ['product_id', '=', $param['product_id']];
        }
        $where[] = ['rel_type', '=', $param['rel_type']];
        if(isset($param['rel_id']) && $param['rel_id']>=0){
            $where[] = ['rel_id', '=', $param['rel_id']];
        }

        $field = $field.',product_id' ?? 'id,product_id,type,value,min_value,max_value,step,other_config';

        $list = $this
                ->field($field)
                ->where($where)
                ->page($param['page'], $param['limit'])
                ->order('order','asc')
                ->order($param['orderby'], 'asc')
                ->select()
                ->toArray();
    
        $count = $this
                ->where($where)
                ->count();
        
        // 计算列表价格
        if(!empty($list)){
            $id = array_column($list, 'id');

            if($param['rel_type']==OptionModel::GLOBAL_DEFENCE){
                $duration = DurationModel::alias('d')
                    ->field('d.id,d.name,p.price,p.rel_id')
                    ->leftJoin('module_mf_cloud_price p', 'd.id=p.duration_id')
                    ->where('d.product_id', $param['product_id'])
                    ->where('p.rel_type', 'option')
                    ->whereIn('p.rel_id', $id)
                    ->withAttr('price', function($val){
                        return $val ?? '';
                    })
                    ->select()
                    ->toArray();

                $durationArr = [];
                foreach ($duration as $key => $value) {
                    $durationArr[$value['rel_id']][] = ['id' => $value['id'], 'name' => $value['name'], 'price' => $value['price']];
                }
            }

            // 时间最短的周期
            $firstDuration = DurationModel::field('id,name,num,unit')->where('product_id', $list[0]['product_id'])->orderRaw('field(unit, "hour","day","month")')->order('num', 'asc')->find();
            if(!empty($firstDuration)){
                $price = PriceModel::alias('p')
                    ->field('p.rel_id,p.price,o.other_config,o.rel_type')
                    ->where('p.product_id', $list[0]['product_id'])
                    ->where('p.rel_type', PriceModel::REL_TYPE_OPTION)
                    ->whereIn('p.rel_id', $id)
                    ->where('p.duration_id', $firstDuration['id'])
                    ->leftJoin('module_mf_cloud_option o', 'p.rel_id=o.id')
                    ->select()
                    ->toArray();

                $priceArr = [];
                foreach($price as $k=>$v){
                    $priceArr[ $v['rel_id'] ] = $v;
                }

                foreach($list as $k=>$v){
                    if(isset($v['type'])){
                        if($v['type'] == 'step'){
                            $disk_type = null;
                            if($param['rel_type'] == OptionModel::SYSTEM_DISK || $param['rel_type'] == OptionModel::DATA_DISK){
                                $disk_type = $v['other_config']['disk_type'] ?? '';
                            }
                            $list[$k]['price'] = $priceArr[$v['id']]['price'] ?? 0;
                            foreach($list as $kk=>$vv){
                                // 范围内的阶梯
                                if($v['min_value'] > $vv['max_value']){
                                    if($param['rel_type'] == OptionModel::SYSTEM_DISK || $param['rel_type'] == OptionModel::DATA_DISK){
                                        $vv['other_config'] = is_array($vv['other_config']) ? $vv['other_config'] : json_decode($vv['other_config'], true);
                                        if($disk_type !== $vv['other_config']['disk_type'] ?? ''){
                                            continue;
                                        }
                                    }
                                    $list[$k]['price'] = bcadd($list[$k]['price'], bcmul($priceArr[$vv['id']]['price'] ?? 0, $vv['min_value']==0?$vv['max_value']:$vv['max_value']-$vv['min_value']+1));
                                }
                            }
                        }else if($v['type'] == 'total'){
                            $list[$k]['price'] = isset($priceArr[$v['id']]['price']) ? bcmul($priceArr[$v['id']]['price'], $v['min_value']) : '0.00';
                        }else{
                            $list[$k]['price'] = $priceArr[$v['id']]['price'] ?? '0.00';
                        }
                    }else{
                        // 单选
                        $list[$k]['price'] = $priceArr[$v['id']]['price'] ?? '0.00';
                    }
                    // 为0时显示
                    if (isset($v['type']) && $v['type'] != 'radio' && isset($v['min_value']) && $v['min_value']==0){
                        $list[$k]['price'] = '0.00';
                    }
                    $list[$k]['duration'] = $firstDuration['name'];

                    // if(isset($v['other_config'])){
                    //     $list[$k]['other_config'] = json_decode($v['other_config'], true);
                    // }
                    
                    if(isset($durationArr)){
                        $list[$k]['duration_price'] = $durationArr[$v['id']] ?? [];
                    }

                    // 除了防御其他都是数字
                    if(!in_array($param['rel_type'], [OptionModel::LINE_DEFENCE,OptionModel::GLOBAL_DEFENCE])){
                        $list[$k]['value'] = (int)$v['value'];
                    }
                }
            }else{
                foreach($list as $k=>$v){
                    $list[$k]['price'] = '0.00';
                    $list[$k]['duration'] = '';

                    if(isset($durationArr)){
                        $list[$k]['duration_price'] = $durationArr[$v['id']] ?? [];
                    }

                    // 除了防御其他都是数字
                    if(!in_array($param['rel_type'], [OptionModel::LINE_DEFENCE,OptionModel::GLOBAL_DEFENCE])){
                        $list[$k]['value'] = (int)$v['value'];
                    }
                }
            }
        }
        // 根据value,min_value排序
        if(!in_array($param['rel_type'], [OptionModel::LINE_DEFENCE,OptionModel::GLOBAL_DEFENCE])){
            if($param['orderby'] == 'value,min_value'){
                usort($list, function($a, $b){
                    return ((isset($a['value']) && (int)$a['value'] > (int)$b['value']) || (isset($a['min_value']) && (int)$a['min_value'] > (int)$b['min_value'])) ? 1 : -1;
                });
            }else if($param['orderby'] == 'value'){
                usort($list, function($a, $b){
                    return (isset($a['value']) && (int)$a['value'] > (int)$b['value']) ? 1 : -1;
                });
            }
        }

        return ['list'=>$list, 'count'=>$count];
    }

    /**
     * 时间 2023-02-02
     * @title 修改配置
     * @desc 修改配置
     * @author hh
     * @version v1
     * @param   int param.id - 配置ID require
     * @param   int param.value - 值
     * @param   int param.min_value - 最小值
     * @param   int param.max_value - 最大值
     * @param   array param.price - 周期价格(如["5"=>"12"],5是周期ID,12是价格) require
     * @param   array param.other_config - 其他配置
     * @param   string param.other_config.advanced_cpu - 智能CPU规则ID,rel_type=0
     * @param   string param.other_config.cpu_limit - CPU限制,rel_type=0
     * @param   string param.other_config.in_bw - 进带宽,rel_type=2
     * @param   string param.other_config.advanced_bw - 智能带宽规则ID,rel_type=2
     * @param   int param.other_config.in_bw - 入站带宽,rel_type=3
     * @param   int param.other_config.out_bw - 出站带宽,rel_type=3
     * @param   int param.other_config.traffic_type - 计费方向(1=进,2=出,3=进+出),rel_type=3
     * @param   string param.other_config.bill_cycle month 计费周期(month=自然月,last_30days=购买日循环),rel_type=3
     * @param   string param.other_config.disk_type - 磁盘类型,rel_type=6/7
     * @param   string param.other_config.store_id - 储存ID,rel_type=6/7
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     */
    public function optionUpdate($param)
    {
        $option = $this->find($param['id']);
        if(empty($option)){
            return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_option_not_found')];
        }
        $productId = $option['product_id'];
        $param['rel_type'] = $option['rel_type'];
        $param['rel_id'] = $option['rel_id'];
        $oldOtherConfig = $option['other_config'];

        $ConfigModel = new ConfigModel();
        $config = $ConfigModel->indexConfig(['product_id'=>$productId]);
        $config = $config['data'];

        // 验证周期价格
        $duration = DurationModel::field('id,name')->where('product_id', $productId)->select();

        $oldPrice = PriceModel::field('duration_id,price')->where('product_id', $productId)->where('rel_type', PriceModel::REL_TYPE_OPTION)->where('rel_id', $option->id)->select()->toArray();
        $oldPrice = array_column($oldPrice, 'price', 'duration_id');
        
        $this->startTrans();
        try{
            $type = $option['type'];

            $update = [];
            if($param['rel_type'] == OptionModel::CPU){
                if($config['type'] == 'hyperv'){
                    $update['other_config'] = json_encode([
                        'advanced_cpu'  => '',
                        'cpu_limit'     => $param['other_config']['cpu_limit'] ?? '',
                    ]);
                }else{
                    $update['other_config'] = json_encode([
                        'advanced_cpu'  => $param['other_config']['advanced_cpu'] ?? '',
                        'cpu_limit'     => $param['other_config']['cpu_limit'] ?? '',
                    ]);
                }
            }else if($param['rel_type'] == OptionModel::MEMORY){
                // 先放置在这点
                $MemoryValidate = new MemoryValidate();
                if($type == 'radio'){
                    if (!$MemoryValidate->scene('radio')->check($param)){
                        throw new \Exception(lang_plugins($MemoryValidate->getError()));
                    }
                }else{
                    if (!$MemoryValidate->scene('step')->check($param)){
                        throw new \Exception(lang_plugins($MemoryValidate->getError()));
                    }
                }
            }else if($param['rel_type'] == OptionModel::LINE_BW){
                // 先放置在这点
                $LineBwValidate = new LineBwValidate();
                if($type == 'radio'){
                    if (!$LineBwValidate->scene('radio')->check($param)){
                        throw new \Exception(lang_plugins($LineBwValidate->getError()));
                    }
                }else{
                    if (!$LineBwValidate->scene('step')->check($param)){
                        throw new \Exception(lang_plugins($LineBwValidate->getError()));
                    }
                }
                if($config['type'] == 'hyperv'){
                    $update['other_config'] = json_encode([
                        'in_bw'         => '',
                        'advanced_bw'   => '',
                    ]);
                }else{
                    $update['other_config'] = json_encode([
                        'in_bw'         => $param['other_config']['in_bw'] ?? '',
                        'advanced_bw'   => $param['other_config']['advanced_bw'] ?? '',
                    ]);
                }
            }else if($param['rel_type'] == OptionModel::LINE_FLOW){
                $update['other_config'] = json_encode([
                    'in_bw' => intval($param['other_config']['in_bw'] ?? 0),
                    'out_bw' => intval($param['other_config']['out_bw'] ?? 0),
                    'traffic_type' => intval($param['other_config']['traffic_type'] ?? 1),
                    'bill_cycle' => $param['other_config']['bill_cycle'] ?? 'month',
                ]);
            }else if($param['rel_type'] == OptionModel::LINE_DEFENCE){
                $line = LineModel::find($option['rel_id']);
                if(empty($line)){
                    throw new \Exception( lang_plugins('mf_cloud_option_not_found') );                    
                }
                $LineDefenceValidate = new LineDefenceValidate();
                if($line['sync_firewall_rule'] == 0){
                    if (!$LineDefenceValidate->scene('update')->check($param)){
                        return json(['status' => 400 , 'msg' => lang_plugins($LineDefenceValidate->getError())]);
                    }
                }else{
                    if (!$LineDefenceValidate->scene('firewall_update')->check($param)){
                        return json(['status' => 400 , 'msg' => lang_plugins($LineDefenceValidate->getError())]);
                    }
                    $param['value'] = $option['value'];
                }
            }else if($param['rel_type'] == OptionModel::LINE_IP){
                // 先放置在这点
                $LineIpValidate = new LineIpValidate();
                if($type == 'radio'){
                    if (!$LineIpValidate->scene('radio')->check($param)){
                        throw new \Exception(lang_plugins($LineIpValidate->getError()));
                    }
                }else{
                    if (!$LineIpValidate->scene('step')->check($param)){
                        throw new \Exception(lang_plugins($LineIpValidate->getError()));
                    }
                }
            }else if($param['rel_type'] == OptionModel::SYSTEM_DISK){
                // 先放置在这点
                $DiskValidate = new DiskValidate();
                if($type == 'radio'){
                    if (!$DiskValidate->scene('radio')->check($param)){
                        throw new \Exception(lang_plugins($DiskValidate->getError()));
                    }
                }else{
                    if (!$DiskValidate->scene('step')->check($param)){
                        throw new \Exception(lang_plugins($DiskValidate->getError()));
                    }
                }
                $update['other_config'] = json_encode([
                    'disk_type' => $param['other_config']['disk_type'] ?? '',
                    'store_id'  => $param['other_config']['store_id'] ?? '',
                ]);
            }else if($param['rel_type'] == OptionModel::DATA_DISK){
                // 先放置在这点
                $DiskValidate = new DiskValidate();
                if($type == 'radio'){
                    if (!$DiskValidate->scene('radio')->check($param)){
                        throw new \Exception(lang_plugins($DiskValidate->getError()));
                    }
                }else{
                    if (!$DiskValidate->scene('step')->check($param)){
                        throw new \Exception(lang_plugins($DiskValidate->getError()));
                    }
                }
                $update['other_config'] = json_encode([
                    'disk_type' => $param['other_config']['disk_type'] ?? '',
                    'store_id'  => $param['other_config']['store_id'] ?? '',
                ]);
            }else if($param['rel_type'] == OptionModel::LINE_IPV6){
                // 先放置在这点
                $LineIpv6Validate = new LineIpv6Validate();
                if($type == 'radio'){
                    if (!$LineIpv6Validate->scene('radio')->check($param)){
                        throw new \Exception(lang_plugins($LineIpv6Validate->getError()));
                    }
                }else{
                    if (!$LineIpv6Validate->scene('step')->check($param)){
                        throw new \Exception(lang_plugins($LineIpv6Validate->getError()));
                    }
                }
            }else if($param['rel_type'] == OptionModel::GLOBAL_DEFENCE){
                $param['value'] = $option['value'];
            }

            // 类型不能修改了
            // $insert['type'] = $type;
            if($type == 'radio'){

                $whereSame = [];
                $whereSame[] = ['product_id', '=', $productId];
                $whereSame[] = ['rel_type', '=', $param['rel_type']];
                $whereSame[] = ['rel_id', '=', $param['rel_id']];
                $whereSame[] = ['value', '=', $param['value']];
                $whereSame[] = ['id', '<>', $param['id']];

                if(in_array($param['rel_type'], [OptionModel::SYSTEM_DISK, OptionModel::DATA_DISK])){
                    $whereSame[] = ['other_config', 'LIKE', rtrim(str_replace('\\', '\\\\', json_encode(['disk_type'=>$param['other_config']['disk_type'] ?? ''])), '}').'%'];
                }
                // 必须是数字
                $same = $this
                        ->where($whereSame)
                        ->find();
                if(!empty($same)){
                    throw new \Exception(lang_plugins('mf_cloud_already_add_the_same_option'));
                }

                $update['value'] = $param['value'];
            }else{

                $whereSame = [];
                $whereSame[] = ['product_id', '=', $productId];
                $whereSame[] = ['rel_type', '=', $param['rel_type']];
                $whereSame[] = ['rel_id', '=', $param['rel_id']];
                $whereSame[] = ['min_value', '<=', $param['max_value']];
                $whereSame[] = ['max_value', '>=', $param['min_value']];
                $whereSame[] = ['id', '<>', $param['id']];

                if(in_array($param['rel_type'], [OptionModel::SYSTEM_DISK, OptionModel::DATA_DISK])){
                    $whereSame[] = ['other_config', 'LIKE', rtrim(str_replace('\\', '\\\\', json_encode(['disk_type'=>$param['other_config']['disk_type'] ?? ''])), '}').'%'];
                }

                // 范围是否交叉
                $intersect = $this
                    ->where($whereSame)
                    ->find();
                if(!empty($intersect)){
                    throw new \Exception(lang_plugins('mf_cloud_option_intersect'));
                }
                $update['min_value'] = $param['min_value'];
                $update['max_value'] = $param['max_value'];
                $update['step'] = $param['step'];
            }
            $update['order'] = $param['order']??0;
            $this->update($update, ['id'=>$option->id]);

            $priceArr = [];
            foreach($duration as $v){
                if(isset($param['price'][$v['id']])){
                    $priceArr[] = [
                        'product_id'    => $productId,
                        'rel_type'      => PriceModel::REL_TYPE_OPTION,
                        'rel_id'        => $option->id,
                        'duration_id'   => $v['id'],
                        'price'         => $param['price'][$v['id']],
                    ];
                }
            }

            PriceModel::where('product_id', $productId)->where('rel_type', PriceModel::REL_TYPE_OPTION)->where('rel_id', $option->id)->delete();
            if(!empty($priceArr)){
                $PriceModel = new PriceModel();
                $PriceModel->insertAll($priceArr);
            }
            $this->commit();
        }catch(\Exception $e){
            $this->rollback();
            return ['status'=>400, 'msg'=>$e->getMessage() ];
        }

        $optionType = [
            lang_plugins('mf_cloud_option_0'),
            lang_plugins('mf_cloud_option_1'),
            lang_plugins('mf_cloud_option_2'),
            lang_plugins('mf_cloud_option_3'),
            lang_plugins('mf_cloud_option_4'),
            lang_plugins('mf_cloud_option_5'),
            lang_plugins('mf_cloud_option_6'),
            lang_plugins('mf_cloud_option_7'),
            lang_plugins('mf_cloud_option_8'),
            lang_plugins('mf_cloud_option_9'),
            lang_plugins('mf_cloud_option_8'),
            lang_plugins('mf_cloud_option_11'),
        ];

        $nameType = [
            lang_plugins('mf_cloud_option_value_0'),
            lang_plugins('mf_cloud_option_value_1'),
            lang_plugins('mf_cloud_option_value_2'),
            lang_plugins('mf_cloud_option_value_3'),
            lang_plugins('mf_cloud_option_value_4'),
            lang_plugins('mf_cloud_option_value_5'),
            lang_plugins('mf_cloud_option_value_6'),
            lang_plugins('mf_cloud_option_value_7'),
            lang_plugins('mf_cloud_option_value_8'),
            lang_plugins('mf_cloud_option_value_9'),
            lang_plugins('mf_cloud_option_value_8'),
            lang_plugins('mf_cloud_option_value_11'),
        ];

        $trafficType = [
            '',
            lang_plugins('mf_cloud_option_traffic_type_in'),
            lang_plugins('mf_cloud_option_traffic_type_out'),
            lang_plugins('mf_cloud_option_traffic_type_all'),
        ];

        $billCycle = [
            'month' => lang_plugins('mf_cloud_option_bill_cycle_month'),
            'last_30days' => lang_plugins('mf_cloud_option_bill_cycle_last_30days'),
        ];

        $des = [
            'value' => $nameType[ $param['rel_type'] ],
        ];

        $old = [
            'value' => $type == 'radio' ? $option['value'] : $option['min_value'].'-'.$option['max_value']
        ];

        $new = [
            'value' => $type == 'radio' ? $param['value'] : $param['min_value'].'-'.$param['max_value']
        ];
        if($option['rel_type'] == OptionModel::CPU){
            $des['advanced_cpu'] = lang_plugins('mf_cloud_advanced_cpu');
            $des['cpu_limit'] = lang_plugins('mf_cloud_cpu_limit');

            $old['advanced_cpu'] = $oldOtherConfig['advanced_cpu'] ?? '';
            $old['cpu_limit'] = $oldOtherConfig['cpu_limit'] ?? '';

            $new['advanced_cpu'] = $param['other_config']['advanced_cpu'] ?? '';
            $new['cpu_limit'] = $param['other_config']['cpu_limit'] ?? '';
        }else if($option['rel_type'] == OptionModel::LINE_BW){
            $des['in_bw'] = lang_plugins('mf_cloud_line_bw_in_bw');
            $des['advanced_bw'] = lang_plugins('mf_cloud_advanced_bw');

            $old['in_bw'] = $oldOtherConfig['in_bw'] ?? '';
            $old['advanced_bw'] = $oldOtherConfig['advanced_bw'] ?? '';

            $new['in_bw'] = $param['other_config']['in_bw'] ?? '';
            $new['advanced_bw'] = $param['other_config']['advanced_bw'] ?? '';
        }else if($option['rel_type'] == OptionModel::LINE_FLOW){
            $des['in_bw'] = lang_plugins('mf_cloud_line_flow_in_bw');
            $des['out_bw'] = lang_plugins('mf_cloud_line_flow_out_bw');
            $des['traffic_type'] = lang_plugins('mf_cloud_line_flow_traffic_type');
            $des['bill_cycle'] = lang_plugins('mf_cloud_line_flow_bill_cycle');

            $old['in_bw'] = $oldOtherConfig['in_bw'] ?? '';
            $old['out_bw'] = $oldOtherConfig['out_bw'] ?? '';
            $old['traffic_type'] = $trafficType[ $oldOtherConfig['traffic_type'] ?? 1];
            $old['bill_cycle'] = $billCycle[ $oldOtherConfig['bill_cycle'] ?? 'month' ];

            $new['in_bw'] = $param['other_config']['in_bw'] ?? '';
            $new['out_bw'] = $param['other_config']['out_bw'] ?? '';
            $new['traffic_type'] = $trafficType[ $param['other_config']['traffic_type'] ?? 1];
            $new['bill_cycle'] = $billCycle[ $param['other_config']['bill_cycle'] ?? 'month' ];
        }else if($option['rel_type'] == OptionModel::SYSTEM_DISK || $option['rel_type'] == OptionModel::DATA_DISK){
            $des['disk_type'] = lang_plugins('mf_cloud_disk_type');
            $des['store_id'] = lang_plugins('store_id');

            $old['disk_type'] = $oldOtherConfig['disk_type'] ?? '';
            $old['store_id'] = $oldOtherConfig['store_id'] ?? '';

            $new['disk_type'] = $param['other_config']['disk_type'] ?? '';
            $new['store_id'] = $param['other_config']['store_id'] ?? '';
        }
        // 每个周期的价格对比
        foreach($duration as $v){
            $des[ 'duration_'.$v['id'] ] = $v['name'].lang_plugins('price');
            $old[ 'duration_'.$v['id'] ] = $oldPrice[ $v['id'] ] ?? lang_plugins('null');
            $new[ 'duration_'.$v['id'] ] = $param['price'][$v['id']] ?? lang_plugins('null');
        }

        $description = ToolLogic::createEditLog($old, $new, $des);
        if(!empty($description)){
            $productName = ProductModel::where('id', $productId)->value('name');

            $description = lang_plugins('log_mf_cloud_modify_option_success', [
                '{product}' => 'product#'.$productId.'#'.$productName.'#',
                '{option}'  => $optionType[ $param['rel_type'] ],
                '{detail}'  => $description,
            ]);
            active_log($description, 'product', $productId);
        }

        $result = [
            'status' => 200,
            'msg'    => lang_plugins('update_success')
        ];
        return $result;
    }

    /**
     * 时间 2023-01-31
     * @title 删除配置
     * @desc 删除配置
     * @author hh
     * @version v1
     * @param   int id - 配置ID require
     * @param   int rel_type - 配置类型(0=CPU配置,1=内存配置,2=线路带宽配置,3=线路流量配置,4=线路防护配置,5=线路附加IP,6=系统盘,7=数据盘,8=线路显卡,9=线路IPv6) 传入了验证配置类型
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     */
    public function optionDelete($id, $rel_type = null)
    {
        $option = $this->find($id);
        if(empty($option)){
            return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_option_not_found')];
        }
        $productId = $option['product_id'];
        $otherConfig = $option['other_config'];

        if(isset($rel_type) && $option['rel_type'] != $rel_type){
            return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_option_not_found')];
        }
        // 如果是删除最后一个内存配置
        if($option['rel_type'] == OptionModel::MEMORY){
            $otherMemory = $this->where('product_id', $productId)->where('rel_type', $option['rel_type'])->where('id', '<>', $id)->value('id');
            if(empty($otherMemory)){
                // 是否有包含内存的高级规则
                $memoryLimitRule = LimitRuleModel::where('product_id', $productId)
                                ->where(function($query){
                                    $query->whereOr('rule', 'like', '%"memory"%')
                                          ->whereOr('result', 'like', '%"memory"%');
                                })
                                ->find();
                if(!empty($memoryLimitRule)){
                    return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_exist_memory_limit_rule_cannot_delete_memory')];
                }
            }
        }

        $this->startTrans();
        try{
            $this->where('id', $id)->delete();
            PriceModel::where('product_id', $productId)->where('rel_type', PriceModel::REL_TYPE_OPTION)->where('rel_id', $id)->delete();

            // 删除完了线路部分配置,自动关闭开关
            if(in_array($option['rel_type'], [OptionModel::LINE_DEFENCE, OptionModel::LINE_IP,OptionModel::LINE_IPV6])){
                // 是否还有对应配置,没有自动关闭开关
                $other = $this->where('product_id', $productId)->where('rel_type', $option['rel_type'])->where('rel_id', $option['rel_id'])->find();
                if(empty($other)){
                    if($option['rel_type'] == OptionModel::LINE_DEFENCE){
                        LineModel::where('id', $option['rel_id'])->update(['defence_enable'=>0]);
                    }else if($option['rel_type'] == OptionModel::LINE_IP){
                        LineModel::where('id', $option['rel_id'])->update(['ip_enable'=>0]);
                    }else if($option['rel_type'] == OptionModel::LINE_IPV6){
                        LineModel::where('id', $option['rel_id'])->update(['ipv6_enable'=>0]);
                    }
                }

                LineModel::where('id', $option['rel_id'])->where('order_default_defence', $option['value'])->update([
                    'order_default_defence' => '',
                ]);
            }
            // 全局防御
            if($option['rel_type'] == OptionModel::GLOBAL_DEFENCE){
                ConfigModel::where('product_id', $productId)->where('order_default_defence', $option['value'])->update([
                    'order_default_defence' => '',
                ]);
            }

            $this->commit();
        }catch(\Exception $e){
            $this->rollback();
            return ['status'=>400, 'msg'=>lang_plugins('delete_success')];
        }

        $optionType = [
            lang_plugins('mf_cloud_option_0'),
            lang_plugins('mf_cloud_option_1'),
            lang_plugins('mf_cloud_option_2'),
            lang_plugins('mf_cloud_option_3'),
            lang_plugins('mf_cloud_option_4'),
            lang_plugins('mf_cloud_option_5'),
            lang_plugins('mf_cloud_option_6'),
            lang_plugins('mf_cloud_option_7'),
            lang_plugins('mf_cloud_option_8'),
            lang_plugins('mf_cloud_option_9'),
            lang_plugins('mf_cloud_option_8'),
            lang_plugins('mf_cloud_option_11'),
        ];

        $nameType = [
            lang_plugins('mf_cloud_option_value_0'),
            lang_plugins('mf_cloud_option_value_1'),
            lang_plugins('mf_cloud_option_value_2'),
            lang_plugins('mf_cloud_option_value_3'),
            lang_plugins('mf_cloud_option_value_4'),
            lang_plugins('mf_cloud_option_value_5'),
            lang_plugins('mf_cloud_option_value_6'),
            lang_plugins('mf_cloud_option_value_7'),
            lang_plugins('mf_cloud_option_value_8'),
            lang_plugins('mf_cloud_option_value_9'),
            lang_plugins('mf_cloud_option_value_8'),
            lang_plugins('mf_cloud_option_value_11'),
        ];

        $productName = ProductModel::where('id', $option['product_id'])->value('name');

        $description = lang_plugins('log_mf_cloud_delete_option_success', [
            '{product}' => 'product#'.$option['product_id'].'#'.$productName.'#',
            '{option}'  => $optionType[ $option['rel_type'] ],
            '{name}'    => $nameType[ $option['rel_type'] ],
            '{detail}'  => $option['type'] == 'radio' ? $option['value'] : $option['min_value'].'-'.$option['max_value'],
        ]);
        active_log($description, 'product', $option['product_id']);

        $result = [
            'status' => 200,
            'msg'    => lang_plugins('delete_success'),
        ];
        return $result;
    }

    /**
     * 时间 2023-01-31
     * @title 配置详情
     * @desc 配置详情
     * @author hh
     * @version v1
     * @param  int id - 配置ID require
     * @return int id - 配置ID
     * @return int product_id - 商品ID
     * @return int rel_type - 配置类型(0=CPU配置,1=内存配置,2=线路带宽配置,3=线路流量配置,4=线路防护配置,5=线路附加IP,6=系统盘,7=数据盘,8=线路显卡,9=线路IPv6)
     * @return int rel_id - 关联ID(rel_type=2,3,4,5,8,9时是线路ID)
     * @return int type - 计费方式(radio=单选,step=阶梯,total=总量)
     * @return int value - 值
     * @return int min_value - 最小值
     * @return int max_value - 最大值
     * @return int step - 步长
     * @return int duration[].id - 周期ID
     * @return string duration[].name - 周期名称
     * @return string duration[].price - 周期价格
     * @return array other_config - 其他配置
     * @return string other_config.advanced_cpu - 智能CPU规则ID,rel_type=0
     * @return string other_config.cpu_limit - CPU限制,rel_type=0
     * @return string other_config.in_bw - 进带宽,rel_type=2
     * @return string other_config.advanced_bw - 智能带宽规则ID,rel_type=2
     * @return int other_config.in_bw - 入站带宽,rel_type=3
     * @return int other_config.out_bw - 出站带宽,rel_type=3
     * @return int other_config.traffic_type - 计费方向(1=进,2=出,3=进+出),rel_type=3
     * @return string other_config.bill_cycle month 计费周期(month=自然月,last_30days=购买日循环),rel_type=3
     * @return string other_config.disk_type - 磁盘类型,rel_type=6/7
     * @return string other_config.store_id - 储存ID,rel_type=6/7
     * @return string firewall_type - 防火墙类型
     * @return int defence_rule_id - 防御规则ID
     */
    public function optionIndex($id): array
    {
        $option = $this
                ->field('id,product_id,rel_type,rel_id,type,value,min_value,max_value,step,other_config,firewall_type,defence_rule_id')
                ->find($id);
        if(empty($option)){
            return [];
        }

        $duration = DurationModel::alias('d')
                    ->field('d.id,d.name,p.price')
                    ->leftJoin('module_mf_cloud_price p', 'p.product_id='.$option['product_id'].' AND p.rel_type='.PriceModel::REL_TYPE_OPTION.' AND p.rel_id='.$id.' AND d.id=p.duration_id')
                    ->where('d.product_id', $option['product_id'])
                    ->withAttr('price', function($val){
                        return $val ?? '';
                    })
                    ->select()
                    ->toArray();

        $option = $option->toArray();
        $option['duration'] = $duration;

        return $option;
    }

    /**
     * 时间 2023-02-06
     * @title 匹配对应配置所有周期价格
     * @desc 匹配对应配置所有周期价格
     * @author hh
     * @version v1
     * @param   int  $productId - 商品ID require
     * @param   int  $relType   - 配置类型(0=CPU配置,1=内存配置,2=线路带宽配置,3=线路流量配置,4=线路防护配置,5=线路附加IP,6=系统盘,7=数据盘,8=线路显卡,9=线路IPv6) require
     * @param   int  $relId     - 关联ID(rel_type=2,3,4,5,8,9时是线路ID) require
     * @param   int  $value     - 当前值 require
     * @param   string  $diskType - 磁盘类型
     * @param   int  $freeSize - 免费大小
     * @return  bool match - 是否有该配置(false=无,true=有)
     * @return  array price - 配置已设置的周期价格(键是周期ID,值是价格)
     */
    public function optionDurationPrice($productId, $relType, $relId = 0, $value = 0, $diskType = NULL, $freeSize = NULL)
    {
        $data = [];
        $match = false;

        $whereOption = [];
        $whereOption[] = ['product_id', '=', $productId];
        $whereOption[] = ['rel_type', '=', $relType];
        $whereOption[] = ['rel_id', '=', $relId];

        if(!is_null($diskType)){
            $diskType = (string)$diskType;
            $whereOption[] = ['other_config', 'LIKE', rtrim(str_replace('\\', '\\\\', json_encode(['disk_type'=>$diskType])), '}').'%'];
        }

        $type = OptionModel::where($whereOption)->value('type');
        if($type == 'radio'){
            $whereOption[] = ['value', '=', $value];

            $optionId = OptionModel::where($whereOption)->value('id');
            if(!empty($optionId)){
                $match = true;
                $data = PriceModel::field('duration_id,price')->where('product_id', $productId)->where('rel_type', PriceModel::REL_TYPE_OPTION)->where('rel_id', $optionId)->select()->toArray();
                $data = array_column($data, 'price', 'duration_id');
            }
        }else if($type == 'step'){
            $whereOption[] = ['min_value', '<=', $value];
            $whereOption[] = ['max_value', '>=', $value];

            $option = OptionModel::where($whereOption)->find();
            if(!empty($option) && (($value - $option['min_value']) % $option['step'] == 0)){
                $match = true;

                $wherePrice = [];
                $wherePrice[] = ['o.product_id', '=', $productId];
                $wherePrice[] = ['o.rel_type', '=', $relType];
                $wherePrice[] = ['o.rel_id', '=', $relId];
                $wherePrice[] = ['o.min_value', '<=', $value];
                $wherePrice[] = ['p.rel_type', '=', PriceModel::REL_TYPE_OPTION];

                if(!is_null($diskType)){
                    $wherePrice[] = ['o.other_config', 'LIKE', rtrim(str_replace('\\', '\\\\', json_encode(['disk_type'=>$diskType])), '}').'%' ];
                }

                $price = OptionModel::alias('o')
                        ->field('o.min_value,o.max_value,p.rel_id,p.duration_id,p.price')
                        ->where($wherePrice)
                        ->leftJoin('module_mf_cloud_price p', 'o.id=p.rel_id')
                        ->order('o.min_value', 'asc')
                        ->group('p.rel_id,p.duration_id')
                        ->select();

                foreach($price as $v){
                    $v['min_value'] = max(1, $v['min_value']);
                    if($value > $v['max_value']){
                        if(is_numeric($freeSize)){
                            if($freeSize > $v['max_value']){
                                $stepPrice = 0;
                            }else if($freeSize >= $v['min_value'] && $freeSize <= $v['max_value']){
                                $stepPrice = bcmul($v['max_value'] - $freeSize, $v['price']);
                            }else{
                                $stepPrice = bcmul($v['max_value'] - $v['min_value'] + 1, $v['price']);
                            }
                        }else{
                            $stepPrice = bcmul($v['max_value'] - $v['min_value'] + 1, $v['price']);
                        }
                    }else{
                        // 最后一层
                        if(is_numeric($freeSize)){
                            if($freeSize > $value){
                                $stepPrice = 0;
                            }else if($freeSize >= $v['min_value'] && $freeSize <= $value){
                                $stepPrice = bcmul($value - $freeSize, $v['price']);
                            }else{
                                $stepPrice = bcmul($value - $v['min_value'] + 1, $v['price']);
                            }
                        }else{
                            $stepPrice = bcmul($value - $v['min_value'] + 1, $v['price']);
                        }
                    }
                    if(!isset($data[ $v['duration_id'] ])){
                        $data[ $v['duration_id'] ] = $stepPrice;
                    }else{
                        $data[ $v['duration_id'] ] = bcadd($data[ $v['duration_id'] ], $stepPrice);
                    }
                }
            }
        }else if($type == 'total'){
            $whereOption[] = ['min_value', '<=', $value];
            $whereOption[] = ['max_value', '>=', $value];

            $option = OptionModel::where($whereOption)->find();
            if(!empty($option) && (($value - $option['min_value']) % $option['step'] == 0)){
                $match = true;
                $optionId = $option['id'];

                $price = PriceModel::field('duration_id,price')->where('product_id', $productId)->where('rel_type', PriceModel::REL_TYPE_OPTION)->where('rel_id', $optionId)->select()->toArray();
                foreach($price as $v){
                    if(is_numeric($freeSize)){
                        $data[ $v['duration_id'] ] = bcmul($v['price'], max($value-$freeSize, 0));
                    }else{
                        $data[ $v['duration_id'] ] = bcmul($v['price'], $value);
                    }
                }
            }
        }

        return ['match'=>$match, 'price'=>$data];
    }

    /**
     * 时间 2023-02-06
     * @title 匹配对应配置某个周期价格
     * @desc 匹配对应配置某个周期价格
     * @author hh
     * @version v1
     * @param   int  $productId - 商品ID require
     * @param   int  $relType   - 配置类型(0=CPU配置,1=内存配置,2=线路带宽配置,3=线路流量配置,4=线路防护配置,5=线路附加IP,6=系统盘,7=数据盘,8=线路显卡,9=线路IPv6) require
     * @param   int  $relId     - 关联ID(rel_type=2,3,4,5,8,9时是线路ID) require
     * @param   int  $value     - 当前值 require
     * @param   int  $durationId     - 周期ID require
     * @param   string  $diskType - 磁盘类型
     * @return  bool match - 是否有该配置(false=无,true=有)
     * @return  string price - 价格
     * @return  OptionModel option - 匹配的配置模型实例
     */
    public function matchOptionDurationPrice($productId, $relType, $relId = 0, $value = 0, $durationId = 0, $diskType = NULL, $freeSize = NULL)
    {
        $match = false;  // 配置是否匹配
        $price = null;   // 价格

        $whereOption = [];
        $whereOption[] = ['product_id', '=', $productId];
        $whereOption[] = ['rel_type', '=', $relType];
        $whereOption[] = ['rel_id', '=', $relId];

        if(!is_null($diskType)){
            $diskType = (string)$diskType;
            $whereOption[] = ['other_config', 'LIKE', rtrim(str_replace('\\', '\\\\', json_encode(['disk_type'=>$diskType])), '}').'%'];
        }

        $type = OptionModel::where($whereOption)->value('type');
        if($type == 'radio'){
            $whereOption[] = ['value', '=', $value];

            $option = OptionModel::where($whereOption)->find();
            if(!empty($option)){
                $match = true;
                $price = PriceModel::where('product_id', $productId)->where('rel_type', PriceModel::REL_TYPE_OPTION)->where('rel_id', $option['id'])->where('duration_id', $durationId)->value('price');
            }
        }else if($type == 'step'){
            $whereOption[] = ['min_value', '<=', $value];
            $whereOption[] = ['max_value', '>=', $value];

            $option = OptionModel::where($whereOption)->find();
            if(!empty($option) && (($value - $option['min_value']) % $option['step'] == 0)){
                $match = true;

                $wherePrice = [];
                $wherePrice[] = ['o.product_id', '=', $productId];
                $wherePrice[] = ['o.rel_type', '=', $relType];
                $wherePrice[] = ['o.rel_id', '=', $relId];
                $wherePrice[] = ['o.min_value', '<=', $value];
                $wherePrice[] = ['p.rel_type', '=', PriceModel::REL_TYPE_OPTION];

                if(!is_null($diskType)){
                    $wherePrice[] = ['o.other_config', 'LIKE', rtrim(str_replace('\\', '\\\\', json_encode(['disk_type'=>$diskType])), '}').'%'];
                }

                $priceArr = OptionModel::alias('o')
                        ->field('o.min_value,o.max_value,p.rel_id,p.duration_id,p.price')
                        ->where($wherePrice)
                        ->where('p.duration_id', $durationId)
                        ->leftJoin('module_mf_cloud_price p', 'o.id=p.rel_id')
                        ->order('o.min_value', 'asc')
                        ->group('p.rel_id')
                        ->select();

                foreach($priceArr as $v){
                    if(!is_numeric($v['price'])){
                        continue;
                    }
                    $v['min_value'] = max(1, $v['min_value']);
                    if($value > $v['max_value']){
                        if(is_numeric($freeSize)){
                            if($freeSize > $v['max_value']){
                                $stepPrice = 0;
                            }else if($freeSize >= $v['min_value'] && $freeSize <= $v['max_value']){
                                $stepPrice = bcmul($v['max_value'] - $freeSize, $v['price']);
                            }else{
                                $stepPrice = bcmul($v['max_value'] - $v['min_value'] + 1, $v['price']);
                            }
                        }else{
                            $stepPrice = bcmul($v['max_value'] - $v['min_value'] + 1, $v['price']);
                        }
                    }else{
                        // 最后一层
                        if(is_numeric($freeSize)){
                            if($freeSize > $value){
                                $stepPrice = 0;
                            }else if($freeSize >= $v['min_value'] && $freeSize <= $value){
                                $stepPrice = bcmul($value - $freeSize, $v['price']);
                            }else{
                                $stepPrice = bcmul($value - $v['min_value'] + 1, $v['price']);
                            }
                        }else{
                            $stepPrice = bcmul($value - $v['min_value'] + 1, $v['price']);
                        }
                    }
                    $price = bcadd($price ?? 0, $stepPrice);
                }
            }
        }else if($type == 'total'){
            $whereOption[] = ['min_value', '<=', $value];
            $whereOption[] = ['max_value', '>=', $value];

            $option = OptionModel::where($whereOption)->find();
            if(!empty($option) && (($value - $option['min_value']) % $option['step'] == 0)){
                $optionId = $option['id'];

                $match = true;

                $price = PriceModel::field('duration_id,price')->where('product_id', $productId)->where('rel_type', PriceModel::REL_TYPE_OPTION)->where('rel_id', $optionId)->where('duration_id', $durationId)->value('price');
                if(is_numeric($freeSize)){
                    $price = bcmul($price, max($value-$freeSize, 0));
                }else{
                    $price = bcmul($price, $value);
                }
            }
        }
        return ['match'=>$match, 'price'=>$price, 'option'=>$option ?? [] ];
    }

    /**
     * 时间 2023-02-02
     * @title 全局防护配置列表
     * @desc 全局防护配置列表
     * @author theworld
     * @version v1
     * @param   int param.product_id - 商品ID require
     * @param   int param.rel_type - 配置类型(0=CPU配置,1=内存配置,2=线路带宽配置,3=线路流量配置,4=线路防护配置,5=线路附加IP,6=系统盘,7=数据盘,8=线路显卡,9=线路IPv6,10=显卡,11=全局防御) require
     * @param   int param.rel_id 0 关联ID
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     * @return  int data.defence_data[].id - 配置ID
     * @return  string data.defence_data[].value - 防御峰值(G)
     * @return  string data.defence_data[].price - 价格
     * @return  string data.defence_data[].duration - 周期
     * @return  string data.defence_data[].firewall_type - 防火墙类型
     * @return  int data.defence_data[].defence_rule_id - 防御规则ID
     * @return  string data.defence_data[].defence_rule_name - 防御规则名称
     * @return  string data.defence_data[].defense_peak - 防御峰值
     * @return  int defence_data[].duration_price[].id - 周期ID
     * @return  string defence_data[].duration_price[].name - 周期名称
     * @return  string defence_data[].duration_price[].price - 价格
     */
    public function globalDefenceList($param)
    {
        $productId = intval($param['product_id'] ?? 0);
        $relType = $param['rel_type'];
        $relId = $param['rel_id'] ?? 0;

        $param = [];
        $param['product_id'] = $productId;
        $param['sort'] = 'asc';
        $param['page'] = 1;
        $param['limit'] = 999;
        $param['rel_type'] = $relType;
        $param['rel_id'] = $relId;
        $param['orderby'] = 'value';
        $field = 'id,value,firewall_type,defence_rule_id,order';
        $result = $this->optionList($param, $field);

        $ConfigModel = new ConfigModel();
        $config = $ConfigModel->indexConfig(['product_id' => $productId]);

        // if(  $config['data']['sync_firewall_rule']==1){
            $hookRes = hook('firewall_set_meal_list', ['product_id' => $productId]);
            $firewallRule = [];
            foreach ($hookRes as $key => $value) {
                if(isset($value['type']) && !empty($value['list'])){
                    foreach ($value['list'] as $v) {
                        $firewallRule[$value['type']][$v['id']] = $v;
                    }
                }
            }

            foreach ($result['list'] as $k => $v) {
                if(isset($firewallRule[$v['firewall_type']][$v['defence_rule_id']])){
                    $result['list'][$k]['defence_rule_name'] = $firewallRule[$v['firewall_type']][$v['defence_rule_id']]['name'];
                    $result['list'][$k]['defense_peak'] = $firewallRule[$v['firewall_type']][$v['defence_rule_id']]['defense_peak'];
                }else{
                    $result['list'][$k]['defence_rule_name'] = '';
                    $result['list'][$k]['defense_peak'] = '';
                }
            }
        // }

        $data = ['defence_data' => $result['list']];

        $result = [
            'status' => 200,
            'msg'    => lang_plugins('success_message'),
            'data'   => $data,
        ];
        return $result;
    }

    /**
     * 时间 2023-01-31
     * @title 全局防护配置详情
     * @desc 全局防护配置详情
     * @author theworld
     * @version v1
     * @param   int id - 通用配置ID require
     * @return  int id - 通用配置ID
     * @return  string value - 防御峰值
     * @return  string firewall_type - 防火墙类型
     * @return  int defence_rule_id - 防御规则ID
     * @return  int duration[].id - 周期ID
     * @return  string duration[].name - 周期名称
     * @return  string duration[].price - 价格
     */
    public function globalDefenceIndex($id)
    {
        $option = $this->optionIndex($id);
        if(empty($option) || $option['rel_type'] != OptionModel::GLOBAL_DEFENCE){
            return (object)[];
        }
        $data = [
            'id' => $option['id'],
            'value' => $option['value'],
            'firewall_type' => $option['firewall_type'],
            'defence_rule_id' => $option['defence_rule_id'],
            'duration' => $option['duration'],
        ];
        return $data;
    }

    /**
     * 时间 2023-02-02
     * @title 导入防火墙防御规则
     * @desc  导入防火墙防御规则
     * @author hh
     * @version v1
     * @param   int param.product_id - 商品ID require
     * @param   int param.rel_type - 配置类型(6=全局防护配置) require
     * @param   int param.rel_id - 关联ID(rel_type=2,3,4,5时是线路ID)
     * @param   string param.firewall_type - 防火墙类型 require
     * @param   array param.defence_rule_id - 防御规则ID require
     */
    public function importDefenceRule($param)
    {
        if(in_array($param['rel_type'], [OptionModel::LINE_DEFENCE])){
            $line = LineModel::find($param['rel_id']);
            if(empty($line)){
                return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_line_not_found')];
            }
            $dataCenter = DataCenterModel::find($line['data_center_id']);
            if(empty($dataCenter)){
                return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_line_not_found')];
            }
            if($line['sync_firewall_rule']!=1){
                return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_line_cannot_sync_firewall_rule')];
            }

            $productId = $dataCenter['product_id'];

            $param['rel_id'] = $line['id'];
        }else{
            $productId = $param['product_id'];

            $ConfigModel = new ConfigModel();
            $config = $ConfigModel->indexConfig(['product_id' => $productId]);

            if($config['data']['sync_firewall_rule']!=1){
                return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_cannot_sync_firewall_rule')];
            }
            $param['rel_id'] = 0;
        }

        $hookRes = hook('firewall_set_meal_list', ['product_id' => $productId]);
        $firewallRule = [];
        foreach ($hookRes as $key => $value) {
            if(isset($value['type']) && !empty($value['list'])){
                $firewallRule[$value['type']] = array_column($value['list'], 'defense_peak', 'id');
            }
        }

        if(!isset($firewallRule[$param['firewall_type']])){
            return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_sync_firewall_rule_type_error') ];
        }

        $defence = [];
        foreach ($firewallRule[$param['firewall_type']] as $k => $v) {
            if(in_array($k, $param['defence_rule_id'])){
                $defence[$k] = $v;
            }
        }
        if(count($defence)!=count($param['defence_rule_id'])){
            return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_sync_firewall_rule_id_error')];
        }

        $exist = $this->where('product_id', $productId)->where('rel_type', $param['rel_type'])->where('rel_id', $param['rel_id'] ?? 0)->select()->toArray();
        if(!empty($exist)){
            $firewallType = array_column($exist, 'firewall_type');
            if($firewallType[0]!=$param['firewall_type']){
                return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_sync_firewall_type_error')];
            }
            $defenceRuleId = array_column($exist, 'defence_rule_id');
            if(count(array_intersect($defenceRuleId, $param['defence_rule_id']))>0){
                return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_sync_firewall_rule_defence_exist')];
            }
        }

        $productName = ProductModel::where('id', $productId)->value('name');

        $this->startTrans();
        try{
            $insert = [];
            foreach ($param['defence_rule_id'] as $v) {
                $insert[] = [
                    'type'              => 'radio',
                    'product_id'        => $productId,
                    'rel_type'          => $param['rel_type'],
                    'rel_id'            => $param['rel_id'],
                    'other_config'      => json_encode([]),
                    'value'             => $param['firewall_type'].'_'.$v,
                    'firewall_type'     => $param['firewall_type'],
                    'defence_rule_id'   => $v,
                    'create_time'       => time(),
                ];
            }
            $this->insertAll($insert);

            $this->commit();
        }catch(\Exception $e){
            $this->rollback();
            return ['status'=>400, 'msg'=>$e->getMessage() ];
        }

        $optionType = [
            lang_plugins('mf_cloud_option_0'),
            lang_plugins('mf_cloud_option_1'),
            lang_plugins('mf_cloud_option_2'),
            lang_plugins('mf_cloud_option_3'),
            lang_plugins('mf_cloud_option_4'),
            lang_plugins('mf_cloud_option_5'),
            lang_plugins('mf_cloud_option_6'),
            lang_plugins('mf_cloud_option_7'),
            lang_plugins('mf_cloud_option_8'),
            lang_plugins('mf_cloud_option_9'),
            lang_plugins('mf_cloud_option_8'),
            lang_plugins('mf_cloud_option_11'),
        ];

        $nameType = [
            lang_plugins('mf_cloud_option_value_0'),
            lang_plugins('mf_cloud_option_value_1'),
            lang_plugins('mf_cloud_option_value_2'),
            lang_plugins('mf_cloud_option_value_3'),
            lang_plugins('mf_cloud_option_value_4'),
            lang_plugins('mf_cloud_option_value_5'),
            lang_plugins('mf_cloud_option_value_6'),
            lang_plugins('mf_cloud_option_value_7'),
            lang_plugins('mf_cloud_option_value_8'),
            lang_plugins('mf_cloud_option_value_9'),
            lang_plugins('mf_cloud_option_value_8'),
            lang_plugins('mf_cloud_option_value_11'),
        ];

        $description = lang_plugins('log_mf_cloud_add_option_success', [
            '{product}'=> 'product#'.$productId.'#'.$productName.'#',
            '{option}' => $optionType[ $param['rel_type'] ],
            '{name}'   => $nameType[ $param['rel_type'] ],
            '{detail}' => implode(',', $defence),
        ]);
        active_log($description, 'product', $productId);

        $result = [
            'status' => 200,
            'msg'    => lang_plugins('create_success'),
        ];
        return $result;
    }

    public function lineDefenceDragSort($param)
    {
        $lineDefence = $this->where('rel_type',4)->where('id',$param['id'])->find();
        if(!$lineDefence){
            return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_option_not_found')];
        }
        if($param['prev_id'] == 0){
            $preOrder = -1;
            $order = 0;
        }else{
            $preLineDefence = $this->where('rel_type',4)
                ->where('product_id',$lineDefence['product_id'])
                ->where('id',$param['prev_id'])
                ->find();
            if(!$preLineDefence){
                return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_option_not_found')];
            }
            $preOrder = $preLineDefence['order'];
            $order = $preLineDefence['order'] + 1;
        }
        $this->where('rel_type', 4)
            ->where('order', '>=', $preOrder)
            ->where('id', '>', $param['prev_id'])
            ->inc('order', 2)
            ->update();
        $this->where('id', $param['id'])->update(['order'=>$order]);
        return ['status'=>200, 'msg'=>lang_plugins('success_message')];
    }

    public function globalDefenceDragSort($param)
    {
        $lineDefence = $this->where('rel_type',11)->where('id',$param['id'])->find();
        if(!$lineDefence){
            return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_option_not_found')];
        }
        if($param['prev_id'] == 0){
            $preOrder = -1;
            $order = 0;
        }else{
            $preLineDefence = $this->where('rel_type',11)
                ->where('product_id',$lineDefence['product_id'])
                ->where('id',$param['prev_id'])
                ->find();
            if(!$preLineDefence){
                return ['status'=>400, 'msg'=>lang_plugins('mf_cloud_option_not_found')];
            }
            $preOrder = $preLineDefence['order'];
            $order = $preLineDefence['order'] + 1;
        }
        $this->where('rel_type', 11)
            ->where('order', '>=', $preOrder)
            ->where('id', '>', $param['prev_id'])
            ->inc('order', 2)
            ->update();
        $this->where('id', $param['id'])->update(['order'=>$order]);
        return ['status'=>200, 'msg'=>lang_plugins('success_message')];
    }


}