<?php 
namespace server\mf_dcim\model;

use think\Model;
use think\facade\Cache;
use app\common\model\HostModel;
use app\common\model\OrderModel;
use app\common\model\ServerModel;
use app\common\model\ProductModel;
use server\mf_dcim\logic\ToolLogic;
use server\mf_dcim\idcsmart_dcim\Dcim;
use app\common\model\HostAdditionModel;
use app\common\logic\DownstreamProductLogic;
use app\common\model\LocalImageModel;
use app\common\model\LocalImageGroupModel;

/**
 * @title 镜像模型
 * @use   server\mf_dcim\model\ImageModel
 */
class ImageModel extends Model
{
	protected $name = 'module_mf_dcim_image';

    // 设置字段信息
    protected $schema = [
        'id'                => 'int',
        'product_id'        => 'int',
        'image_group_id'    => 'int',
        'name'              => 'string',
        'enable'            => 'int',
        'charge'            => 'int',
        'price'             => 'float',
        'rel_image_id'      => 'int',
        'order'             => 'int',
        'upstream_id'       => 'int',
    ];

    /**
     * 时间 2023-02-01
     * @title 添加操作系统
     * @desc 添加操作系统
     * @author hh
     * @version v1
     * @param   string param.image_group_id - 操作系统分类ID require
     * @param   string param.name - 系统名称 require
     * @param   int param.charge - 是否收费(0=不收费,1=收费) require
     * @param   float param.price - 价格 requireIf,charge=1
     * @param   int param.enable - 是否可用(0=禁用,1=启用) require
     * @param   int param.rel_image_id - 操作系统ID require
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     * @return  int data.id - 操作系统ID
     */
    public function imageCreate($param)
    {
        $imageGroup = ImageGroupModel::find($param['image_group_id']);
        if(empty($imageGroup)){
            return ['status'=>400, 'msg'=>lang_plugins('mf_dcim_image_group_not_found')];
        }
        $param['product_id'] = $imageGroup['product_id'];

        $image = $this->create($param, ['product_id','image_group_id','name','charge','price','enable','rel_image_id']);

        $productName = ProductModel::where('id', $imageGroup['product_id'])->value('name');

        $description = lang_plugins('mf_dcim_log_add_image_success', [
            '{product}' => 'product#'.$param['product_id'].'#'.$productName.'#',
            '{name}'    => $param['name'],
        ]);
        active_log($description, 'product', $param['product_id']);

        $result = [
            'status' => 200,
            'msg'    => lang_plugins('create_success'),
            'data'   => [
                'id' => (int)$image->id,
            ],
        ];
        return $result;
    }

    /**
     * 时间 2023-02-01
     * @title 操作系统列表
     * @desc 操作系统列表
     * @author hh
     * @version v1
     * @param   int param.page - 页数
     * @param   int param.limit - 每页条数
     * @param   int param.product_id - 商品ID
     * @param   int param.image_group_id - 搜索操作系统分类ID
     * @param   string param.keywords - 搜索:操作系统名称
     * @return  int list[].id - 操作系统分类ID
     * @return  int list[].image_group_id - 操作系统分类ID
     * @return  string list[].name - 操作系统名称
     * @return  int list[].charge - 是否收费(0=否,1=是)
     * @return  string list[].price - 价格
     * @return  int list[].enable - 是否启用(0=否,1=是)
     * @return  int list[].rel_image_id - 魔方云操作系统ID
     * @return  string list[].image_group_name - 操作系统分类名称
     * @return  string list[].icon - 操作系统分类图标
     * @return  int count - 总条数
     */
    public function imageList($param)
    {
        bcscale(2);
        $param['page'] = isset($param['page']) ? ($param['page'] ? (int)$param['page'] : 1) : 1;
        $param['limit'] = isset($param['limit']) ? ($param['limit'] ? (int)$param['limit'] : config('idcsmart.limit')) : config('idcsmart.limit');

        $where = [];
        if(isset($param['product_id']) && is_numeric($param['product_id'])){
            $where[] = ['i.product_id', '=', $param['product_id']];
        }
        if(isset($param['image_group_id']) && !empty($param['image_group_id']) ){
            $where[] = ['i.image_group_id', '=', $param['image_group_id']];
        }
        if(isset($param['keywords']) && $param['keywords'] !== ''){
            $where[] = ['i.name', 'LIKE', '%'.$param['keywords'].'%'];
        }
        
        $isDownstream = isset($param['is_downstream']) && $param['is_downstream'] == 1;
        $param['product_id'] = $param['product_id'] ?? 0;
        // 下游
        $clientLevel = [];
        if($isDownstream){
            $DurationModel = new DurationModel();

            $clientLevel = $DurationModel->getClientLevel([
                'product_id'    => $param['product_id'],
                'client_id'     => get_client_id(),
            ]);
        }

        $list = $this
            ->alias('i')
            ->field('i.id,i.image_group_id,i.name,i.charge,i.price,i.enable,i.rel_image_id,ig.name image_group_name,ig.icon')
            ->where($where)
            ->leftJoin('module_mf_dcim_image_group ig', 'i.image_group_id=ig.id')
            ->withAttr('price', function($val) use ($isDownstream, $clientLevel) {
                if($isDownstream && !empty($clientLevel) && $val > 0){
                    $discount = bcdiv($val * $clientLevel['discount_percent'], 100, 2);
                    if($discount > 0){
                        $val = bcsub($val, $discount);
                    }
                }
                return $val;
            })
            ->page($param['page'], $param['limit'])
            ->order('i.order', 'asc')
            ->order('i.id', 'desc')
            ->select()
            ->toArray();

        $count = $this
            ->alias('i')
            ->where($where)
            ->count();

        return ['list'=>$list, 'count'=>$count];
    }

    /**
     * 时间 2023-02-01
     * @title 修改操作系统
     * @desc 修改操作系统
     * @author hh
     * @version v1
     * @param   int param.id - 操作系统ID require
     * @param   string param.image_group_id - 操作系统分类ID require
     * @param   string param.name - 系统名称 require
     * @param   int param.charge - 是否收费(0=不收费,1=收费) require
     * @param   float param.price - 价格 requireIf,charge=1
     * @param   int param.enable - 是否可用(0=禁用,1=启用) require
     * @param   int param.rel_image_id - 操作系统ID require
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     */
    public function imageUpdate($param)
    {
        $image = $this->find($param['id']);
        if(empty($image)){
            return ['status'=>400, 'msg'=>lang_plugins('mf_dcim_image_not_found')];
        }
        $imageGroup = ImageGroupModel::find($param['image_group_id']);
        if(empty($imageGroup) || $image['product_id'] != $imageGroup['product_id']){
            return ['status'=>400, 'msg'=>lang_plugins('mf_dcim_image_group_not_found')];
        }

        $this->update($param, ['id'=>$image->id], ['image_group_id','name','charge','price','enable','rel_image_id']);

        $switch = [lang_plugins('mf_dcim_switch_off'), lang_plugins('mf_dcim_switch_on')];

        $des = [
            'image_group_id' => lang_plugins('mf_dcim_image_group'),
            'name' => lang_plugins('mf_dcim_image_name'),
            'charge' => lang_plugins('mf_dcim_image_charge'),
            'price' => lang_plugins('mf_dcim_price'),
            'enable' => lang_plugins('mf_dcim_image_enable'),
            'rel_image_id' => lang_plugins('mf_dcim_image_rel_image_id'),
        ];

        $old = $image->toArray();
        $old['image_group_id'] = ImageGroupModel::where('id', $image['image_group_id'])->value('name');
        $old['charge'] = $switch[ $old['charge'] ];
        $old['enable'] = $switch[ $old['enable'] ];

        $param['image_group_id'] = $imageGroup['name'];
        $param['charge'] = $switch[ $param['charge'] ];
        $param['enable'] = $switch[ $param['enable'] ];

        $description = ToolLogic::createEditLog($old, $param, $des);
        if(!empty($description)){
            $productName = ProductModel::where('id', $image['product_id'])->value('name');

            $description = lang_plugins('mf_dcim_log_modify_image_success', [
                '{product}' => 'product#'.$image['product_id'].'#'.$productName.'#',
                '{detail}'  => $description,
            ]);
            active_log($description, 'product', $image['product_id']);
        }

        // 变更名称分组后,同步修改附加信息表
        if($old['name'] != $param['name'] || $old['image_group_id'] != $param['image_group_id']){
            $hostId = HostLinkModel::where('image_id', $image['id'])->column('host_id');
            if(!empty($hostId)){
                HostAdditionModel::whereIn('host_id', $hostId)->update([
                    'image_icon' => $imageGroup['icon'],
                    'image_name' => $param['name'],
                    'update_time'=> time(),
                ]);
            }
        }

        $result = [
            'status' => 200,
            'msg'    => lang_plugins('update_success'),
        ];
        return $result;
    }

    /**
     * 时间 2023-02-01
     * @title 删除操作系统
     * @desc 删除操作系统
     * @author hh
     * @version v1
     * @param   int id - 操作系统ID require
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     */
    public function imageDelete($id)
    {
        $image = $this->find($id);
        if(empty($image)){
            return ['status'=>400, 'msg'=>lang_plugins('mf_dcim_image_not_found')];
        }
        $this->startTrans();
        try{
            $this->where('id', $id)->delete();

            $this->commit();
        }catch(\Exception $e){
            $this->rollback();
            return ['status'=>400, 'msg'=>lang_plugins('delete_fail')];
        }

        $productName = ProductModel::where('id', $image['product_id'])->value('name');

        $description = lang_plugins('mf_dcim_log_delete_image_success', [
            '{product}' => 'product#'.$image['product_id'].'#'.$productName.'#',
            '{name}'    => $image['name'],
        ]);
        active_log($description, 'product', $image['product_id']);

        $result = [
            'status' => 200,
            'msg'    => lang_plugins('delete_success'),
        ];
        return $result;
    }
    
    /**
     * 时间 2023-02-06
     * @title 切换是否可用
     * @desc 切换是否可用
     * @author hh
     * @version v1
     * @param   int param.id - 操作系统ID require
     * @param   int param.enable - 是否启用(0=禁用,1=启用) require
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     */
    public function toggleImageEnable($param)
    {
        $image = $this->find($param['id']);
        if(empty($image)){
            return ['status'=>400, 'msg'=>lang_plugins('mf_dcim_image_not_found')];
        }
        if($image['enable'] == $param['enable']){
            return ['status'=>200, 'msg'=>lang_plugins('success_message')];
        }

        $act = [lang_plugins('mf_dcim_disable'), lang_plugins('mf_dcim_enable')];

        $this->update(['enable'=>$param['enable']], ['id'=>$image['id']]);

        $productName = ProductModel::where('id', $image['product_id'])->value('name');
        
        $description = lang_plugins('mf_dcim_log_toggle_image_enable_success', [
            '{product}' => 'product#'.$image['product_id'].'#'.$productName.'#',
            '{act}'     => $act[ $param['enable'] ],
            '{name}'    => $image['name'],
        ]);
        active_log($description, 'product', $image['product_id']);

        return ['status'=>200, 'msg'=>lang_plugins('success_message')];
    }

    /**
     * 时间 2023-02-06
     * @title 获取操作系统列表
     * @desc 获取操作系统列表
     * @author hh
     * @version v1
     * @param   int param.product_id - 商品ID require
     * @param   int param.is_downstream 0 是否下游发起(0=否,1=是)
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     * @return  int data.list[].id - 操作系统分类ID
     * @return  string data.list[].name - 操作系统分类名称
     * @return  string data.list[].icon - 操作系统分类图标
     * @return  int data.list[].image[].id - 操作系统ID
     * @return  int data.list[].image[].image_group_id - 操作系统分类ID
     * @return  string data.list[].image[].name - 操作系统名称
     * @return  int data.list[].image[].charge - 是否收费(0=否,1=是)
     * @return  string data.list[].image[].price - 价格
     */
    public function homeImageList($param)
    {
        $isDownstream = isset($param['is_downstream']) && $param['is_downstream'] == 1;
        $DurationModel = new DurationModel();

        $where = [];
        $where[] = ['product_id', '=', $param['product_id'] ?? 0];

        // 操作系统
        $imageGroup = ImageGroupModel::field('id,name,icon')->where($where)->order('order', 'asc')->order('id', 'desc')->select()->toArray();

        $image = $this
                ->field('id,image_group_id,name,charge,price')
                ->where($where)
                ->where('enable', 1)
                ->order('order', 'asc')
                ->order('id', 'desc')
                ->select()
                ->toArray();
        $imageArr = [];
        foreach($image as $v){
            if($isDownstream){
                $v['price'] = $DurationModel->downstreamSubClientLevelPrice([
                    'product_id' => $param['product_id'] ?? 0,
                    'client_id'  => get_client_id(),
                    'price'      => $v['price'],
                ]);
            }
            $imageArr[ $v['image_group_id'] ][] = $v;
        }
        foreach($imageGroup as $k=>$v){
            if(isset($imageArr[$v['id']])){
                $imageGroup[$k]['image'] = $imageArr[ $v['id'] ];
            }else{
                unset($imageGroup[$k]);
            }
        }

        $result = [
            'status' => 200,
            'msg'    => lang_plugins('success_message'),
            'data'   => [
                'list' => array_values($imageGroup)
            ]
        ];
        return $result;

    }
    
    /**
     * 时间 2022-07-29
     * @title 检查产品是够购买过镜像
     * @desc 检查产品是够购买过镜像
     * @author hh
     * @version v1
     * @param   int param.id - 产品ID require
     * @param   int param.image_id - 镜像ID require
     * @param   int param.is_downstream 0 是否下游(0=不是,1=是)
     * @return  int status - 状态码(200=成功,400=失败)
     * @return  string msg - 提示信息
     * @return  string data.price - 需要支付的金额(0.00表示镜像免费或已购买)
     * @return  string data.description - 描述
     */
    public function checkHostImage($param)
    {
        $result = [
            'status' => 200,
            'msg'    => lang_plugins('success_message'),
            'data'   => []
        ];

        // 验证产品和用户
        $host = HostModel::find($param['id']);
        if(empty($host) || $host['status'] != 'Active' || $host['is_delete']){
            return ['status'=>400, 'msg'=>lang_plugins('mf_dcim_not_link_dcim')];
        }
        // 前台判断
        $app = app('http')->getName();
        if($app == 'home'){
            if($host['client_id'] != get_client_id()){
                return ['status'=>400, 'msg'=>lang_plugins('mf_dcim_host_not_found')];
            }
        }
        $hostLink = HostLinkModel::where('host_id', $param['id'])->find();
        if(empty($hostLink)){
            return ['status'=>400, 'msg'=>lang_plugins('mf_dcim_not_link_dcim')];
        }
        $image = ImageModel::find($param['image_id'] ?? 0);
        if(empty($image) || $image['enable'] == 0){
            return ['status'=>400, 'msg'=>lang_plugins('mf_dcim_image_not_found')];
        }
        if(isset($param['check_limit_rule']) && $param['check_limit_rule'] == 1){
            $HostLinkModel = new HostLinkModel();
            $currentConfig = $HostLinkModel->currentConfig($param['id']);
            $currentConfig['image_id'] = $param['image_id'];

            $productId = HostModel::where('id', $param['id'])->value('product_id');

            $LimitRuleModel = new LimitRuleModel();
            $checkLimitRule = $LimitRuleModel->checkLimitRule($productId, $currentConfig, ['image']);
            if($checkLimitRule['status'] == 400){
                return ['status'=>400, 'msg'=>lang_plugins('mf_dcim_cannot_reinstall_for_limit_rule')];
            }
        }

        $configData = json_decode($hostLink['config_data'], true);
        $duration = DurationModel::where('product_id', $host['product_id'])->where('num', $configData['duration']['num'] ?? 0)->where('unit', $configData['duration']['unit'] ?? 'month')->find();
        
        $result['data']['price'] = '0.00';
        if($host['billing_cycle'] != 'free' && $image['charge'] == 1){
            $res = HostImageLinkModel::where('host_id', $param['id'])->where('image_id', $param['image_id'])->find();
            if(empty($res)){
                bcscale(2);
                $image['price'] = bcmul($image['price'], $duration['price_factor'] ?? 1);

                $isDownstream = isset($param['is_downstream']) && $param['is_downstream'] == 1;
                if($isDownstream){
                    $DurationModel = new DurationModel();
                    $image['price'] = $DurationModel->downstreamSubClientLevelPrice([
                        'product_id' => $host['product_id'],
                        'client_id'  => $host['client_id'],
                        'price'      => $image['price'],
                    ]);
                }

                $result['data']['price'] = amount_format($image['price']);
                $result['data']['description'] = lang_plugins('mf_dcim_buy_image') . $image['name'];
            }
        }
        $result['data']['price_difference'] = $result['data']['price'];
        $result['data']['renew_price_difference'] = 0;
        $result['data']['base_price'] = $host['base_price'];

        return $result;
    }

    /**
     * 时间 2022-07-29
     * @title 生成购买镜像订单
     * @desc 生成购买镜像订单
     * @author hh
     * @version v1
     * @param   int param.id - 产品ID require
     * @param   int param.image_id - 镜像ID require
     * @return  int status - 状态码(200=成功,400=失败)
     * @return  string msg - 提示信息
     * @return  string data.id - 订单ID
     */
    public function createImageOrder($param)
    {
        if(isset($param['is_downstream'])){
            unset($param['is_downstream']);
        }
        $param['check_limit_rule'] = 1;
        $res = $this->checkHostImage($param);
        if($res['status'] == 400){
            return $res;
        }
        /*if($res['data']['price'] == 0){
            return ['status'=>400, 'msg'=>lang_plugins('no_need_to_buy_this_image')];
        }*/

        $image = ImageModel::find($param['image_id']);
        $description = lang_plugins("buy_image", ['name'=>$image['name']]);

        $OrderModel = new OrderModel();

        $data = [
            'host_id'     => $param['id'],
            'client_id'   => get_client_id(),
            'type'        => 'upgrade_config',
            'amount'      => $res['data']['price'],
            'description' => $description,
            'price_difference' => $res['data']['price_difference'],
            'renew_price_difference' => $res['data']['renew_price_difference'],
            'base_price' => $res['data']['base_price'],
            'upgrade_refund' => 0,
            'config_options' => [
                'type'     => 'buy_image',
                'image_id' => $param['image_id'],
            ],
            'customfield' => $param['customfield'] ?? [],
        ];
        return $OrderModel->createOrder($data);
    }

    public function getDefaultUserInfo($ImageModel = null){
        $ImageModel = $ImageModel ?? $this;

        $imageGroup = ImageGroupModel::where('id', $ImageModel['image_group_id'] ?? 0)->value('name');

        if(stripos($imageGroup, 'windows') === 0){
            $result = [
                'username' => 'administrator',
                'port'     => 3306, 
            ];
        }else{
            $result = [
                'username' => 'root',
                'port'     => 22, 
            ];
        }
        return $result;
    }

    /**
     * 时间 2022-09-25
     * @title 拉取镜像
     * @desc 拉取镜像
     * @author hh
     * @version v1
     * @param   int productId - 商品ID require
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     */
    public function imageSync($productId)
    {
        $result = ['status'=>200, 'msg'=>lang_plugins('success_message')];

        $cacheKey = 'SYNC_MF_DCIM_IMAGE_'.$productId;
        if(Cache::has($cacheKey)){
            return $result;
        }
        Cache::set($cacheKey, 1, 180);

        $ProductModel = ProductModel::find($productId);
        if(empty($ProductModel)){
            Cache::delete($cacheKey);
            return ['status'=>400, 'msg'=>lang_plugins('product_id_error')];
        }
        if($ProductModel->getModule() != 'mf_dcim'){
            Cache::delete($cacheKey);
            return ['status'=>400, 'msg'=>lang_plugins('product_not_link_mf_dcim_module')];
        }
        if($ProductModel['type'] == 'server_group'){
            Cache::delete($cacheKey);
            return ['status'=>400, 'msg'=>lang_plugins('mf_dcim_product_must_link_server_can_sync_image')];
        }
        $ServerModel = ServerModel::find($ProductModel['rel_id']);
        $ServerModel['password'] = aes_password_decode($ServerModel['password']);

        $Dcim = new Dcim($ServerModel);

        $res = $Dcim->getAllMirrorOs();
        if($res['status'] != 200){
            Cache::delete($cacheKey);
            return ['status'=>400, 'msg'=>lang_plugins('mf_dcim_sync_image_failed')];
        }

        $ConfigModel = new ConfigModel();
        $config = $ConfigModel->indexConfig(['product_id'=>$productId]);
        if($config['data']['manual_resource']==1){
            $ImageGroupModel = new ImageGroupModel();
            $this->where('product_id', $productId)->delete();
            $ImageGroupModel->where('product_id', $productId)->delete();
            $ConfigModel->where('product_id', $productId)->update(['manual_resource' => 0]);
        }
        
        $imageLink = array_column($res['data']['group'], 'name', 'id');

        // 添加组
        $imageGroup = ImageGroupModel::field('id,name')->where('product_id', $productId)->select()->toArray();
        $imageGroup = array_column($imageGroup, 'id', 'name') ?? [];
        $index = 0;
        foreach($res['data']['group'] as $v){
            if(empty($imageGroup[$v['name']])){
                $ImageGroupModel = ImageGroupModel::create(['product_id'=>$productId, 'name'=>$v['name'], 'icon'=>$v['name'], 'order'=>$index ]);
                $imageGroup[ $v['name'] ] = $ImageGroupModel->id;
                $index++;
            }
        }
         // 获取当前产品已填加的镜像
        $image = ImageModel::field('id,rel_image_id')->where('product_id', $productId)->select()->toArray();
        $image = array_column($image, 'id', 'rel_image_id');

        $data = [];
        foreach($res['data']['os'] as $v){
            if(!isset($image[$v['id']])){
                $one = [
                    'image_group_id'=>$imageGroup[ $imageLink[$v['group_id']] ],
                    'name'=>$v['name'],
                    'enable'=>1,
                    'charge'=>0,
                    'price'=>0.00,
                    'product_id'=>$productId,
                    'rel_image_id'=>$v['id'],
                ];

                $data[] = $one;
            }
        }
        if(!empty($data)){
            $ImageModel = new ImageModel();
            $ImageModel->insertAll($data);
        }
        
        Cache::delete($cacheKey);
        return $result;
    }

    /**
     * 时间 2024-04-30
     * @title 批量删除操作系统
     * @desc  批量删除操作系统
     * @author hh
     * @version v1
     * @param   array id - 操作系统ID require
     * @return  int status - 状态码(200=成功,400=失败)
     * @return  string msg - 提示信息 
     */
    public function imageBatchDelete($id)
    {
        $image = $this
                ->field('id,product_id,name')
                ->whereIn('id', $id)
                ->select()
                ->toArray();
        if(empty($image)){
            return ['status'=>400, 'msg'=>lang_plugins('mf_dcim_image_not_found')];
        }
        $id = array_column($image, 'id');

        $this->startTrans();
        try{
            $this->whereIn('id', $id)->delete();

            $this->commit();
        }catch(\Exception $e){
            $this->rollback();
            return ['status'=>400, 'msg'=>lang_plugins('delete_fail')];
        }
        $imageName = array_column($image, 'name');
        $imageName = implode(',', $imageName);

        $productName = ProductModel::where('id', $image[0]['product_id'])->value('name');

        $description = lang_plugins('mf_dcim_log_delete_image_success', [
            '{product}' => 'product#'.$image[0]['product_id'].'#'.$productName.'#',
            '{name}'    => $imageName,
        ]);
        active_log($description, 'product', $image[0]['product_id']);

        $result = [
            'status' => 200,
            'msg'    => lang_plugins('delete_success'),
        ];
        return $result;
    }

    /**
     * 时间 2024-08-08
     * @title 拖动排序
     * @desc  拖动排序
     * @author hh
     * @version v1
     * @param   int prev_id - 前一个镜像ID(0=表示置顶) require
     * @param   int id - 当前镜像ID require
     * @return  int status - 状态码(200=成功,400=失败)
     * @return  string msg - 提示信息
     */
    public function dragToSort($param){
        $image = $this->find($param['id']);
        if(empty($image)){
            return ['status'=>400, 'msg'=>lang_plugins('mf_dcim_image_not_found')];
        }
        if($param['prev_id'] == 0){
            $preOrder = -1;
            $order = 0;
        }else{
            $preImage = $this->find($param['prev_id']);
            if(empty($preImage)){
                return ['status'=>400, 'msg'=>lang_plugins('mf_dcim_image_not_found')];
            }
            $preOrder = $preImage['order'];
            $order = $preImage['order'] + 1;
        }
        // 往后移动,
        if($param['prev_id'] == 0){
            // 所有向后移
            $this->where('product_id', $image['product_id'])->where('order', '>=', $preOrder)->inc('order', 1)->update();
        }else{
            // 前一个之后的所有后移
            $this->where('order', '>=', $preOrder)->where('id', '<', $param['prev_id'])->inc('order', 2)->update();
        }
        $this->where('id', $param['id'])->update(['order'=>$order]);

        return ['status'=>200, 'msg'=>lang_plugins('success_message') ];
    }

    /**
     * 时间 2024-08-27
     * @title 名称获取器
     * @desc  名称获取器
     * @author hh
     * @version v1
     * @param   string value - 名称 require
     * @return  string
     */
    public function getNameAttr($value)
    {
        if(app('http')->getName() == 'home'){
            $multiLanguage = hook_one('multi_language', [
                'replace' => [
                    'name' => $value,
                ],
            ]);
            if(isset($multiLanguage['name'])){
                $value = $multiLanguage['name'];
            }
        }
        return $value;
    }

     /**
     * 时间 2024-10-24
     * @title 拉取本地操作系统
     * @desc 拉取本地操作系统
     * @author theworld
     * @version v1
     * @param   int productId - 商品ID require
     */
    public function localImageSync($productId)
    {
        $ProductModel = ProductModel::find($productId);
        if(empty($ProductModel)){
            return ['status'=>400, 'msg'=>lang_plugins('product_id_error')];
        }
        if($ProductModel->getModule() != 'mf_dcim'){
            return ['status'=>400, 'msg'=>lang_plugins('product_not_link_mf_dcim_module')];
        }

        $DownstreamProductLogic = new DownstreamProductLogic($ProductModel);
        if($DownstreamProductLogic->isDownstreamSync){
            return ['status'=>400, 'msg'=>lang_plugins('mf_dcim_product_is_downstream_sync_cannot_sync_local_image')];
        }

        $ConfigModel = new ConfigModel();
        $config = $ConfigModel->indexConfig(['product_id'=>$productId]);

        $this->startTrans();
        try{
            $time = time();
            if($config['data']['manual_resource']==1){
                $ImageGroupModel = new ImageGroupModel();
                $imageGroup = $ImageGroupModel->where('product_id', $productId)->column('name');

                $localImageGroup = LocalImageGroupModel::select()->toArray();

                $data = [];
                foreach ($localImageGroup as $key => $value) {
                    if(!in_array($value['name'], $imageGroup)){
                        $data[] = [
                            'product_id' => $productId,
                            'name' => $value['name'],
                            'icon' => $value['icon'],
                            'order' => $value['order'],
                            'create_time' => $time,
                        ];
                    }
                }
                if(!empty($data)){
                    $ImageGroupModel->insertAll($data);
                }

                $imageGroup = $ImageGroupModel->where('product_id', $productId)->select()->toArray();
                $imageGroup = array_column($imageGroup, 'id', 'name');
                $image = $this->where('product_id', $productId)
                    ->column('name');

                $localImage = LocalImageModel::alias('a')->field('a.id,a.name,a.order,b.name group_name')->leftJoin('local_image_group b','b.id=a.group_id')->select()->toArray();

                $data = [];
                foreach ($localImage as $key => $value) {
                    if(!in_array($value['name'], $image)){
                        $data[] = [
                            'image_group_id'    => $imageGroup[ $value['group_name'] ],
                            'name'              => $value['name'],
                            'enable'            => 1,
                            'charge'            => 0,
                            'price'             => 0.00,
                            'product_id'        => $productId,
                            'rel_image_id'      => $value['id'],
                            'order'             => $value['order'],
                        ];
                    }
                }
                if(!empty($data)){
                    $this->insertAll($data);
                }
            }else{
                $ImageGroupModel = new ImageGroupModel();
                $this->where('product_id', $productId)->delete();
                $ImageGroupModel->where('product_id', $productId)->delete();

                $localImageGroup = LocalImageGroupModel::select()->toArray();

                $data = [];
                foreach ($localImageGroup as $key => $value) {
                    $data[] = [
                        'product_id' => $productId,
                        'name' => $value['name'],
                        'icon' => $value['icon'],
                        'order' => $value['order'],
                        'create_time' => $time,
                    ];
                }
                if(!empty($data)){
                    $ImageGroupModel->insertAll($data);
                }
                $imageGroup = $ImageGroupModel->where('product_id', $productId)->select()->toArray();
                $imageGroup = array_column($imageGroup, 'id', 'name');
                $localImage = LocalImageModel::alias('a')->field('a.id,a.name,a.order,b.name group_name')->leftJoin('local_image_group b','b.id=a.group_id')->select()->toArray();

                $data = [];
                foreach ($localImage as $key => $value) {
                    $data[] = [
                        'image_group_id'    => $imageGroup[ $value['group_name'] ],
                        'name'              => $value['name'],
                        'enable'            => 1,
                        'charge'            => 0,
                        'price'             => 0.00,
                        'product_id'        => $productId,
                        'rel_image_id'      => $value['id'],
                        'order'             => $value['order'],
                    ];
                }
                if(!empty($data)){
                    $this->insertAll($data);
                }

                $ConfigModel->where('product_id', $productId)->update(['manual_resource' => 1]);

            }
            $this->commit();
        }catch(\Exception $e){
            $this->rollback();
            return ['status'=>400, 'msg'=>$e->getMessage()];
        }

        return ['status'=>200, 'msg'=>lang_plugins('success_message')];
    }
}