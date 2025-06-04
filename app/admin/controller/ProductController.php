<?php
namespace app\admin\controller;

use app\admin\validate\ProductValidate;
use app\admin\validate\ProductNoticeGroupValidate;
use app\common\model\ProductModel;
use app\common\model\SyncImageLogModel;
use app\common\model\ProductNoticeGroupModel;

/**
 * @title 商品管理
 * @desc 商品管理
 * @use app\admin\controller\ProductController
 */
class ProductController extends AdminBaseController
{
    public function initialize()
    {
        parent::initialize();
        $this->validate = new ProductValidate();
    }

    /**
     * 时间 2022-5-17
     * @title 商品列表
     * @desc 商品列表
     * @url /admin/v1/product
     * @method  GET
     * @author wyh
     * @version v1
     * @param string type - 关联类型
     * @param string rel_id - 关联ID
     * @param string keywords - 关键字
     * @param int page - 页数
     * @param int limit - 每页条数
     * @param string orderby - 排序 id,name,description
     * @param string sort - 升/降序 asc,desc
     * @param  int exclude_domain - 是否排除域名(0=否,1=是)
     * @return array list - 商品列表
     * @return int list[].id - ID
     * @return string list[].name - 商品名
     * @return string list[].description - 描述
     * @return int list[].stock_control - 是否开启库存控制:1开启,0关闭
     * @return int list[].qty - 库存
     * @return string list[].pay_type - 付款类型(免费free，一次onetime，周期先付recurring_prepayment,周期后付recurring_postpaid
     * @return int list[].hidden - 是否隐藏:1隐藏,0显示
     * @return int list[].product_group_name_second - 二级分组名称
     * @return int list[].product_group_id_second - 二级分组ID
     * @return int list[].product_group_name_first - 一级分组名称
     * @return int list[].product_group_id_first - 一级分组ID
     * @return int list[].agentable - 是否可代理商品0否1是(后台字段)
     * @return int list[].agent - 代理商品0否1是
     * @return int list[].host_num - 产品数量
     * @return int count - 商品总数
     */
    public function productList()
    {
        # 合并分页参数
        $param = array_merge($this->request->param(),[]);//['page'=>$this->request->page,'limit'=>$this->request->limit,'sort'=>$this->request->sort]);

        $result = [
            'status'=>200,
            'msg'=>lang('success_message'),
            'data' =>(new ProductModel())->productListSearch($param)
        ];
        return json($result);
    }

    /**
     * 时间 2022-10-12
     * @title 根据模块获取商品列表
     * @desc 根据模块获取商品列表
     * @url /admin/v1/module/:module/product
     * @method  GET
     * @author theworld
     * @version v1
     * @param string module - 模块名称
     * @return array list - 一级分组列表
     * @return int list[].id - 一级分组ID
     * @return string list[].name - 一级分组名称
     * @return array list[].child - 二级分组
     * @return int list[].child[].id - 二级分组ID
     * @return string list[].child[].name - 二级分组名称
     * @return array list[].child[].child - 商品
     * @return int list[].child[].child[].id - 商品ID
     * @return string list[].child[].child[].name - 商品名称
     */
    public function moduleProductList()
    {
        $param = $this->request->param();

        $result = [
            'status'=>200,
            'msg'=>lang('success_message'),
            'data' =>(new ProductModel())->moduleProductList($param)
        ];
        return json($result);
    }

    /**
     * 时间 2022-5-17
     * @title 商品详情
     * @desc 商品详情
     * @url /admin/v1/product/:id
     * @method  GET
     * @param int id - 商品ID required
     * @return object product - 商品
     * @return int product.id - ID
     * @return string product.name - 商品名称
     * @return int product.product_group_id - 所属商品组ID
     * @return string product.description - 商品描述
     * @return int product.hidden - 0显示默认，1隐藏
     * @return int product.stock_control - 库存控制(1:启用)默认0
     * @return int product.qty - 库存数量(与stock_control有关)
     * @return int product.product_id - 父商品ID
     * @return array product.plugin_custom_fields - 自定义字段{is_link:是否已有子商品,是,置灰}
     * @return int product.show - 是否将商品展示在会员中心对应模块的列表中:0否1是
     * @return string product.renew_rule - 续费规则：due到期日，current当前时间
     * @return string mode - 代理模式：only_api仅调用接口，sync同步商品
     * @return string supplier_name - 供应商名称
     * @return int profit_type - 0表示百分比价格方案，1表示自定义金额
     * @return int show_base_info - 产品列表是否展示基础信息：1是默认，0否
     * @return string module - 商品对应模块
     * @return object plugin_custom_fields - 插件钩子返回的自定义字段{"k1":"v1"}
     * @return object pay_ontrial - 试用配置
     * @return int pay_ontrial.status - 是否开启
     * @return string pay_ontrial.cycle_type - 时长单位(hour/day/month)
     * @return int pay_ontrial.cycle_num - 时长
     * @return string pay_ontrial.client_limit - no不限制/new新用户/host用户必须存在激活中的产品
     * @return string pay_ontrial.account_limit - 账户限制(email绑定邮件/phone绑定手机/certification)
     * @return string pay_ontrial.old_client_exclusive - 老用户专享(商品ID多选，逗号分隔)
     * @return int pay_ontrial.max - 单用户最大试用数量
     * @author wyh
     * @version v1
     */
    public function index()
    {
        $param = $this->request->param();

        $result = [
            'status'=>200,
            'msg'=>lang('success_message'),
            'data' =>[
                'product' => (new ProductModel())->indexProduct(intval($param['id']))
            ]
        ];
        return json($result);
    }

    /**
     * 时间 2022-5-17
     * @title 新建商品
     * @desc 新建商品
     * @url /admin/v1/product
     * @method  post
     * @author wyh
     * @version v1
     * @param string name 测试商品 商品名称 required
     * @param int product_group_id 1 分组ID(只传二级分组ID) required
     * @param object customfield - 自定义字段，格式如：{"promo":"123"} required
     * @param string renew_rule - 续费规则：due到期日，current当前时间
     * @return int product_id - 商品ID
     */
    public function create()
    {
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('create')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        $result = (new ProductModel())->createProduct($param);

        return json($result);
    }

    /**
     * 时间 2022-5-17
     * @title 编辑商品
     * @desc 编辑商品
     * @url /admin/v1/product/:id
     * @method  put
     * @param string name 测试商品 商品名称 required
     * @param int product_group_id 1 分组ID(只传二级分组ID) required
     * @param string description 1 描述 required
     * @param int hidden 1 是否隐藏:1隐藏默认,0显示 required
     * @param int stock_control 1 库存控制(1:启用)默认0 required
     * @param int qty 1 库存数量(与stock_control有关) required
     * @param string pay_type recurring_prepayment 付款类型(免费free，一次onetime，周期先付recurring_prepayment(默认),周期后付recurring_postpaid required
     * @param int auto_setup 1 是否自动开通:1是默认,0否 required
     * @param string type server_group 关联类型:server,server_group required
     * @param int rel_id 1 关联ID required
     * @param array upgrade [1,3,4] 可升降级商品ID,数组
     * @param int product_id 1 父级商品ID
     * @param string price - 商品起售价格
     * @param string renew_rule - 续费规则：due到期日，current当前时间
     * @param int show_base_info - 产品列表是否展示基础信息：1是默认，0否
     * @author wyh
     * @version v1
     */
    public function update()
    {
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('edit')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        $result = (new ProductModel())->updateProduct($param);

        return json($result);
    }

    /**
     * 时间 2022-6-10
     * @title 编辑商品接口
     * @desc 编辑商品接口
     * @url /admin/v1/product/:id/server
     * @method  put
     * @author wyh
     * @version v1
     * @param int auto_setup 1 是否自动开通:1是默认,0否 required
     * @param string type server_group 关联类型:server,server_group required
     * @param int rel_id 1 关联ID required
     * @param int show 1 是否将商品展示在会员中心对应模块的列表中:0否1是 required
     * @param int show_base_info 1 产品列表是否展示基础信息：1是默认，0否
     */
    public function updateServer()
    {
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('edit_server')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        $result = (new ProductModel())->updateServer($param);

        return json($result);
    }

    /**
     * 时间 2022-5-17
     * @title 删除商品
     * @desc 删除商品
     * @url /admin/v1/product/:id
     * @method  delete
     * @author wyh
     * @version v1
     * @param int id 1 商品ID required
     */
    public function delete()
    {
        $param = $this->request->param();

        $result = (new ProductModel())->deleteProduct(intval($param['id']));

        return json($result);
    }

    /**
     * 时间 2022-5-17
     * @title 隐藏/显示商品
     * @desc 隐藏/显示商品
     * @url /admin/v1/product/:id/:hidden
     * @method  put
     * @author wyh
     * @version v1
     * @param int id 1 商品ID required
     * @param int hidden 1 商品ID required
     */
    public function hidden()
    {
        $param = $this->request->param();

        $result = (new ProductModel())->hiddenProduct($param);

        return json($result);
    }

    /**
     * 时间 2022-5-18
     * @title 商品拖动排序
     * @desc 商品拖动排序
     * @url /admin/v1/product/order/:id
     * @method  put
     * @author wyh
     * @version v1
     * @param int id 1 商品ID required
     * @param int pre_product_id 1 移动后前一个商品ID(没有则传0) required
     * @param int product_group_id 1 移动后的商品组ID required
     * @param int backward 1 是否向后移动:1是,0否 required
     */
    public function order()
    {
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('order')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        $result = (new ProductModel())->orderProduct($param);

        return json($result);
    }

    /**
     * 时间 2022-5-31
     * @title 获取商品关联的升降级商品
     * @desc 获取商品关联的升降级商品
     * @url /admin/v1/product/:id/upgrade
     * @method  get
     * @author wyh
     * @version v1
     * @param int id 1 商品ID required
     * @return array list - 商品列表
     * @return int list[].id - ID
     * @return string list[].name - 商品名
     */
    public function upgrade()
    {
        $param = $this->request->param();

        $result = (new ProductModel())->upgradeProduct(intval($param['id']));

        return json($result);
    }
    
    /**
     * 时间 2022-05-30
     * @title 选择接口获取配置
     * @desc 选择接口获取配置
     * @url /admin/v1/product/:id/server/config_option
     * @method  GET
     * @author hh
     * @version v1
     * @param   int id - 商品ID required
     * @param   string type - 关联类型(server=接口,server_group=接口分组) required
     * @param   int rel_id - 关联ID required
     * @return  string content - 模块输出内容
     */
    public function moduleServerConfigOption()
    {
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('module_server_config_option')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }
        
        $ProductModel = new ProductModel();
        $result = $ProductModel->moduleServerConfigOption($param);
        return json($result);
    }

    /**
     * 时间 2022-05-31
     * @title 修改配置计算价格
     * @desc 修改配置计算价格
     * @url /admin/v1/product/:id/config_option
     * @method  POST
     * @author hh
     * @version v1
     * @param   int id - 商品ID required
     * @param   array config_options - 模块自定义配置参数
     * @return  string price - 价格
     * @return  string renew_price - 续费价格
     * @return  string billing_cycle - 周期名称
     * @return  int duration - 周期时长(秒)
     * @return  string description - 订单子项描述
     * @return  string base_price - 基础价格
     */
    public function moduleCalculatePrice()
    {
        $param = $this->request->param();
        $param['product_id'] = $param['id'] ?? 0;

        $ProductModel = new ProductModel();

        $result = $ProductModel->productCalculatePrice($param);
        return json($result);
    }

    /**
     * 时间 2023-02-20
     * @title 保存可代理商品
     * @desc 保存可代理商品
     * @url /admin/v1/product/agentable
     * @method  PUT
     * @author theworld
     * @version v1
     * @param array param.id - 商品ID require
     */ 
    public function saveAgentableProduct()
    {
        $param = $this->request->param();

        $ProductModel = new ProductModel();
        $result = $ProductModel->saveAgentableProduct($param);

        return json($result);
    }

    /**
     * 时间 2023-03-01
     * @title 根据上游模块获取商品列表
     * @desc 根据上游模块获取商品列表
     * @url /admin/v1/res_module/:module/product
     * @method  GET
     * @author theworld
     * @version v1
     * @param string module - 模块名称
     * @return array list - 一级分组列表
     * @return int list[].id - 一级分组ID
     * @return string list[].name - 一级分组名称
     * @return array list[].child - 二级分组
     * @return int list[].child[].id - 二级分组ID
     * @return string list[].child[].name - 二级分组名称
     * @return array list[].child[].child - 商品
     * @return int list[].child[].child[].id - 商品ID
     * @return string list[].child[].child[].name - 商品名称
     */
    public function resModuleProductList()
    {
        $param = $this->request->param();

        $result = [
            'status'=>200,
            'msg'=>lang('success_message'),
            'data' =>(new ProductModel())->resModuleProductList($param)
        ];
        return json($result);
    }

    /**
     * 时间 2022-10-12
     * @title 根据模块获取商品列表
     * @desc 根据模块获取商品列表
     * @url /admin/v1/module/product
     * @method  GET
     * @author theworld
     * @version v1
     * @param  array|string module - 模块名称
     * @param  int type - 类型(0=本地模块,1=同步代理)
     * @return array list - 一级分组列表
     * @return int list[].id - 一级分组ID
     * @return string list[].name - 一级分组名称
     * @return array list[].child - 二级分组
     * @return int list[].child[].id - 二级分组ID
     * @return string list[].child[].name - 二级分组名称
     * @return array list[].child[].child - 商品
     * @return int list[].child[].child[].id - 商品ID
     * @return string list[].child[].child[].name - 商品名称
     */
    public function modulesProductList()
    {
        $param = $this->request->param();
        $param['module'] = $param['module'] ?? [];

        $result = [
            'status'=>200,
            'msg'=>lang('success_message'),
            'data' =>(new ProductModel())->moduleProductList($param)
        ];
        return json($result);
    }

    /**
     * 时间 2023-10-16
     * @title 复制商品
     * @desc 复制商品
     * @url /admin/v1/product/:id/copy
     * @method  POST
     * @author theworld
     * @version v1
     * @param int id - 商品ID require
     * @param int product_group_id - 二级分组ID
     */
    public function copy()
    {
        $param = $this->request->param();

        $ProductModel = new ProductModel();
        $result = $ProductModel->copyProduct($param);
        return json($result);
    }

    /**
     * 时间 2024-07-02
     * @title 获取商品自定义标识配置
     * @desc 获取商品自定义标识配置
     * @url /admin/v1/product/:id/custom_host_name
     * @method  GET
     * @author theworld
     * @version v1
     * @param int id - 商品ID require
     * @return int custom_host_name - 自定义主机标识开关(0=关闭,1=开启)
     * @return string custom_host_name_prefix - 自定义主机标识前缀
     * @return array custom_host_name_string_allow - 允许的字符串(number=数字,upper=大写字母,lower=小写字母)
     * @return int custom_host_name_string_length - 字符串长度
     */
    public function getCustomHostName()
    {
        $param = $this->request->param();

        $result = [
            'status'=>200,
            'msg'=>lang('success_message'),
            'data' =>(new ProductModel())->getCustomHostName($param['id'])
        ];
        return json($result);
    }

    /**
     * 时间 2024-07-02
     * @title 保存商品自定义标识配置
     * @desc 保存商品自定义标识配置
     * @url /admin/v1/product/:id/custom_host_name
     * @method  PUT
     * @author theworld
     * @version v1
     * @param int id - 商品ID require
     * @param int custom_host_name - 自定义主机标识开关(0=关闭,1=开启) require
     * @param string custom_host_name_prefix - 自定义主机标识前缀 require
     * @param array custom_host_name_string_allow - 允许的字符串(number=数字,upper=大写字母,lower=小写字母) require
     * @param int custom_host_name_string_length - 字符串长度 require
     */
    public function saveCustomHostName()
    {
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('custom_host_name')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        $ProductModel = new ProductModel();
        $result = $ProductModel->saveCustomHostName($param);
        return json($result);
    }

     /**
     * 时间 2024-10-24
     * @title 同步镜像日志列表
     * @desc 同步镜像日志列表
     * @url /admin/v1/product/sync_image_log
     * @method  GET
     * @author theworld
     * @version v1
     * @param string keywords - 关键字
     * @param int page - 页数
     * @param int limit - 每页条数
     * @param string orderby - 排序 id,create_time
     * @param string sort - 升/降序 asc,desc
     * @return array list - 同步镜像日志
     * @return int list[].id - 同步镜像日志ID
     * @return int list[].product_id - 商品ID 
     * @return string list[].name - 商品名称
     * @return string list[].result - 同步结果 
     * @return int list[].create_time - 同步时间
     * @return int count - 同步镜像日志总数
     */
    public function syncImageLogList()
    {
        $param = array_merge($this->request->param(), ['page' => $this->request->page, 'limit' => $this->request->limit, 'sort' => $this->request->sort]);

        $result = [
            'status'=>200,
            'msg'=>lang('success_message'),
            'data' =>(new SyncImageLogModel())->logList($param)
        ];
        return json($result);
    }

    /**
     * 时间 2024-10-24
     * @title 同步镜像
     * @desc 同步镜像
     * @url /admin/v1/product/sync_image
     * @method  POST
     * @author theworld
     * @version v1
     * @param array product_id - 商品ID
     */
    public function syncImage()
    {
        $param = $this->request->param();

        $SyncImageLogModel = new SyncImageLogModel();
        $result = $SyncImageLogModel->syncImage($param);
        return json($result);
    }

    /**
     * 时间 2025-02-12
     * @title 试用配置
     * @desc 试用配置
     * @url /admin/v1/product/:id/pay_ontrial
     * @method  put
     * @author wyh
     * @version v1
     * @param int id - 商品ID require
     * @param object pay_ontrial - 试用配置
     * @param int pay_ontrial.status - 是否开启
     * @param string pay_ontrial.cycle_type - 时长单位(hour/day/month)
     * @param int pay_ontrial.cycle_num - 时长
     * @param string pay_ontrial.client_limit - no不限制/new新用户/host用户必须存在激活中的产品
     * @param array pay_ontrial.account_limit - 账户限制，多选(email绑定邮件/phone绑定手机/certification)
     * @param array pay_ontrial.old_client_exclusive - 老用户专享，数组(商品ID多选，逗号分隔)
     * @param int pay_ontrial.max - 单用户最大试用数量
     */
    public function payOntrial()
    {
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('pay_ontrial')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        $ProductModel = new ProductModel();

        $result = $ProductModel->payOntrial($param);

        return json($result);
    }

    /**
     * 时间 2025-03-07
     * @title 全局通知管理列表
     * @desc  全局通知管理列表
     * @url /admin/v1/product/notice_group
     * @method  GET
     * @author hh
     * @version v1
     * @param   string type - 通知类型标识 require
     * @param   int page - 页数
     * @param   int limit - 每页条数
     * @param   string name - 搜索:分组名称
     * @param   string product_name - 搜索:商品名称
     * @return  int list[].id - 触发动作组ID
     * @return  string list[].name - 触发动作组名称
     * @return  int list[].is_default - 是否默认组(0=否,1=是)
     * @return  int list[].notice_setting.xxx - 对应触发动作状态(xxx=动作标识,0=关闭,1=开启)
     * @return  int list[].product[].id - 商品ID
     * @return  string list[].product[].name - 商品名称
     * @return  int count - 总条数
     * @return  string notice_type[].type - 通知类型标识
     * @return  string notice_type[].name - 通知类型名称
     * @return  string notice_setting[].name - 通知标识
     * @return  string notice_setting[].name_lang - 通知名称
     */
    public function productNoticeGroupList()
    {
        $param = array_merge($this->request->param(), ['page'=>$this->request->page,'limit'=>$this->request->limit]);

        $ProductNoticeGroupModel = new ProductNoticeGroupModel();
        $data = $ProductNoticeGroupModel->productNoticeGroupList($param);

        $result = [
            'status' => 200,
            'msg'    => lang('success_message'),
            'data'   => $data,
        ];
        return json($result);
    }

    /**
     * 时间 2025-03-07
     * @title 创建触发动作组
     * @desc  创建触发动作组
     * @url /admin/v1/product/notice_group
     * @method  POST
     * @author hh
     * @version v1
     * @param   string type - 通知类型标识 require
     * @param   string name - 触发动作组名称 require
     * @param   int notice_setting.xxx - 对应触发动作状态(xxx=动作标识,0=关闭,1=开启) require
     * @param   array product_id - 商品ID
     * @return  int id - 触发动作组ID
     */
    public function productNoticeGroupCreate()
    {
        $param = $this->request->param();

        $ProductNoticeGroupValidate = new ProductNoticeGroupValidate();
        if (!$ProductNoticeGroupValidate->scene('create')->check($param)){
            return json(['status' => 400 , 'msg' => lang($ProductNoticeGroupValidate->getError())]);
        }

        $ProductNoticeGroupModel = new ProductNoticeGroupModel();
        $result = $ProductNoticeGroupModel->productNoticeGroupCreate($param);

        return json($result);
    }

    /**
     * 时间 2025-03-07
     * @title 修改触发动作组
     * @desc  修改触发动作组
     * @url /admin/v1/product/notice_group/:id
     * @method  PUT
     * @author hh
     * @version v1
     * @param   int id - 触发动作组ID require
     * @param   string name - 触发动作组名称 require
     * @param   int notice_setting.xxx - 对应触发动作状态(xxx=动作标识,0=关闭,1=开启) require
     * @param   array product_id - 商品ID
     */
    public function productNoticeGroupUpdate()
    {
        $param = $this->request->param();

        $ProductNoticeGroupValidate = new ProductNoticeGroupValidate();
        if (!$ProductNoticeGroupValidate->scene('update')->check($param)){
            return json(['status' => 400 , 'msg' => lang($ProductNoticeGroupValidate->getError())]);
        }

        $ProductNoticeGroupModel = new ProductNoticeGroupModel();
        $result = $ProductNoticeGroupModel->productNoticeGroupUpdate($param);

        return json($result);
    }

    /**
     * 时间 2025-03-07
     * @title 删除触发动作组
     * @desc  删除触发动作组
     * @url /admin/v1/product/notice_group/:id
     * @method  DELETE
     * @author hh
     * @version v1
     * @param   int id - 触发动作组ID require
     */
    public function productNoticeGroupDelete()
    {
        $param = $this->request->param();

        $ProductNoticeGroupValidate = new ProductNoticeGroupValidate();
        if (!$ProductNoticeGroupValidate->scene('delete')->check($param)){
            return json(['status' => 400 , 'msg' => lang($ProductNoticeGroupValidate->getError())]);
        }

        $ProductNoticeGroupModel = new ProductNoticeGroupModel();
        $result = $ProductNoticeGroupModel->productNoticeGroupDelete($param);

        return json($result);
    }

    /**
     * 时间 2025-03-07
     * @title 修改触发动作组动作状态
     * @desc  修改触发动作组动作状态
     * @url /admin/v1/product/notice_group/:id/act/status
     * @method  PUT
     * @author hh
     * @version v1
     * @param   int id - 触发动作组ID require
     * @param   string act - 动作标识 require
     * @param   int status - 状态(0=否,1=是) require
     */
    public function productNoticeGroupUpdateActStatus()
    {
        $param = $this->request->param();

        $ProductNoticeGroupValidate = new ProductNoticeGroupValidate();
        if (!$ProductNoticeGroupValidate->scene('update_act')->check($param)){
            return json(['status' => 400 , 'msg' => lang($ProductNoticeGroupValidate->getError())]);
        }

        $ProductNoticeGroupModel = new ProductNoticeGroupModel();
        $result = $ProductNoticeGroupModel->productNoticeGroupUpdateActStatus($param);

        return json($result);
    }

}

