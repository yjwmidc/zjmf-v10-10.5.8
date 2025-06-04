<?php
namespace server\idcsmart_common\controller\home;

use app\common\model\HostModel;
use app\common\model\SystemLogModel;
use app\event\controller\BaseController;
use server\idcsmart_common\IdcsmartCommon;
use server\idcsmart_common\model\IdcsmartCommonProductModel;

/**
 * @title 通用商品-商品配置信息(前台)
 * @desc 通用商品-商品配置信息(前台)
 * @use server\idcsmart_common\controller\home\IdcsmartCommonProductController
 */
class IdcsmartCommonProductController extends BaseController
{
    # 初始验证
    public function initialize()
    {
        parent::initialize();
        app('http')->name('home');
    }

    /**
     * 时间 2022-09-28
     * @title 产品列表
     * @desc 产品列表
     * @author wyh
     * @version v1
     * @url /console/v1/idcsmart_common/host
     * @method  GET
     * @param int m - 菜单ID
     * @param int client_id - 客户ID
     * @param string keywords - 关键字,搜索范围:产品ID,商品名称,标识
     * @param string status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败
     * @param string tab - 状态using使用中expiring即将到期overdue已逾期deleted已删除
     * @param int page - 页数
     * @param int limit - 每页条数
     * @param string orderby - 排序 id,active_time,due_time
     * @param string sort - 升/降序 asc,desc
     * @return array list - 产品
     * @return int list[].id - 产品ID
     * @return int list[].product_id - 商品ID
     * @return string list[].product_name - 商品名称
     * @return string list[].name - 标识
     * @return int list[].active_time - 开通时间
     * @return int list[].due_time - 到期时间
     * @return string list[].first_payment_amount - 金额
     * @return string list[].billing_cycle - 周期
     * @return string list[].status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败
     * @return object list[].self_defined_field - 产品自定义字段自定义字段，格式{"自定义字段ID":"值"}
     * @return int list[].is_auto_renew - 是否自动续费(0=否,1=是)
     * @return int count - 产品总数
     * @return int using_count - 使用中产品数量
     * @return int expiring_count - 即将到期产品数量
     * @return int overdue_count - 已逾期产品数量
     * @return int deleted_count - 已删除产品数量
     * @return int all_count - 全部产品数量
     */
    public function hostList()
    {
        // 合并分页参数
        $param = array_merge($this->request->param(), ['page' => $this->request->page, 'limit' => $this->request->limit, 'sort' => $this->request->sort]);

        // 实例化模型类
        $IdcsmartCommonProductModel = new IdcsmartCommonProductModel();

        // 获取产品列表
        $data = $IdcsmartCommonProductModel->hostList($param);

        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data
        ];
        return json($result);
    }

    /**
     * 时间 2022-09-26
     * @title 前台商品配置信息
     * @desc 前台商品配置信息
     * @url /console/v1/idcsmart_common/product/:product_id/configoption
     * @method  GET
     * @author wyh
     * @version v1
     * @param   int product_id - 商品ID require
     * @return  object common_product - 商品基础信息
     * @return  string common_product.name - 商品名称
     * @return  string common_product.order_page_description - 订购页面html
     * @return  int common_product.allow_qty - 是否允许选择数量:1是，0否默认
     * @return  string common_product.pay_type - 付款类型(免费free，一次onetime，周期先付recurring_prepayment,周期后付recurring_postpaid
     * @return  object configoptions - 配置项信息
     * @return  int configoptions.id - 配置项ID
     * @return  string configoptions.option_name - 配置项名称
     * @return  string configoptions.option_type -  配置项类型：select下拉单选，multi_select下拉多选，radio点击单选，quantity数量输入，quantity_range数量拖动，yes_no是否，area区域
     * @return  int configoptions.qty_min - 数量时最小值
     * @return  int configoptions.qty_max - 数量时最大值
     * @return  string configoptions.unit - 单位
     * @return  int configoptions.allow_repeat - 数量类型时：是否允许重复:开启后,前台购买时，可通过点击添加按钮，自动创建一个新的配置项，取名如bw1
     * @return  int configoptions.max_repeat - 最大允许重复数量
     * @return  string configoptions.description - 说明
     * @return  int configoptions.qty_change - 数量变化值
     * @return array configoptions.subs - 子项信息
     * @return  int configoptions.subs.id - 子项ID
     * @return  string configoptions.subs.option_name - 子项名称
     * @return  int configoptions.subs.qty_min - 子项最小值
     * @return  int configoptions.subs.qty_max - 子项最大值
     * @return object cycles - 周期({"onetime":1.00})
     * @return array custom_cycles - 自定义周期
     * @return int custom_cycles[].id - 自定义周期ID
     * @return string custom_cycles[].name - 自定义周期名称
     * @return int custom_cycles[].cycle_time - 自定义周期时长
     * @return string custom_cycles[].cycle_unit - 自定义周期单位
     * @return float custom_cycles[].amount - 商品自定义周期金额
     * @return float custom_cycles[].cycle_amount - (商品+配置项)自定义周期金额
     */
    public function cartConfigoption()
    {
        $param = $this->request->param();

        $IdcsmartCommonProductModel = new IdcsmartCommonProductModel();

        $result = $IdcsmartCommonProductModel->cartConfigoption($param);

        return json($result);
	}

    /**
     * 时间 2022-09-26
     * @title 前台商品配置信息计算价格
     * @desc 前台商品配置信息计算价格
     * @url /console/v1/idcsmart_common/product/:product_id/configoption/calculate
     * @method  POST
     * @author wyh
     * @version v1
     * @param   object configoption - 配置信息{168:1,514:53} require
     * @return object cycles - 周期({"onetime":1.00})
     * @return array custom_cycles - 自定义周期
     * @return int custom_cycles[].id - 自定义周期ID
     * @return string custom_cycles[].name - 自定义周期名称
     * @return int custom_cycles[].cycle_time - 自定义周期时长
     * @return string custom_cycles[].cycle_unit - 自定义周期单位
     * @return float custom_cycles[].cycle_amount - 自定义周期金额
     */
    public function cartConfigoptionCalculate()
    {
        $param = $this->request->param();

        $IdcsmartCommonProductModel = new IdcsmartCommonProductModel();

        $result = $IdcsmartCommonProductModel->cartConfigoptionCalculate($param);

        return json($result);
    }

    /**
     * 时间 2022-09-26
     * @title 前台产品内页
     * @desc 前台产品内页
     * @url /console/v1/idcsmart_common/host/:host_id/configoption
     * @method  GET
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @return  object host - 财务信息
     * @return  int host.create_time - 订购时间
     * @return  int host.due_time - 到期时间
     * @return  string host.billing_cycle - 计费方式:计费周期免费free，一次onetime，周期先付recurring_prepayment,周期后付recurring_postpaid
     * @return  string host.billing_cycle_name - 模块计费周期名称
     * @return  int host.billing_cycle_time - 模块计费周期时间,秒
     * @return  float host.renew_amount - 续费金额
     * @return  float host.first_payment_amount - 首付金额
     * @return  string host.dedicatedip - 独立ip
     * @return  string host.username - 用户名
     * @return  string host.password - 密码
     * @return  string host.os - 操作系统，后台未配置时显示远程操作系统模板ID
     * @return  string host.assignedips - 分配ip，逗号分隔
     * @return  int host.bwlimit - 流量限制
     * @return  float host.bwusage - 流量使用
     * @return  array configoptions - 配置项信息
     * @return  int configoptions[].id - 配置项ID
     * @return  string configoptions[].option_name - 配置项名称
     * @return  string configoptions[].option_type - 配置项类型：select下拉单选，multi_select下拉多选，radio点击单选，quantity数量输入，quantity_range数量拖动，yes_no是否，area区域，os操作系统
     * @return  string configoptions[].unit - 单位
     * @return  array configoptions[].subs -
     * @return  string configoptions[].subs[].option_name - 子项名称
     * @return  int configoptions[].qty - 数量(当类型为数量时,显示此值)
     * @return array chart - 图表tab
     * @return string chart[].title - 标题
     * @return string chart[].type - 类型
     * @return array chart[].select - 下拉选择
     * @return string chart[].select[].name - 名称
     * @return string chart[].select[].value - 值
     * @return array client_area - 客户自定义tab区域
     * @return string client_area[].key - 键
     * @return string client_area[].name - 名称标题
     * @return array client_button - 管理按钮区域(默认模块操作)
     * @return array client_button.console - 控制台
     * @return string client_button.console[].func - 模块(调模块动作传此值)
     * @return string client_button.console[].name - 模块名称
     * @return string client_button.console[].type - 类型
     * @return array client_button.control - 下拉管理
     * @return string client_button.control[].func - 模块(调模块动作传此值)
     * @return string client_button.control[].name - 模块名称
     * @return string client_button.control[].type - 类型
     * @return array os - 操作系统
     * @return int os[].id - 配置项ID
     * @return string os[].option_name - 配置项名称
     * @return string os[].option_type - 配置项类型
     * @return array os[].subs - 子项
     * @return string os[].subs[].os - 操作系统
     * @return array os[].subs[].version - 操作系统详细版本
     * @return int os[].subs[].version[].id - 子项ID
     * @return string os[].subs[].version[].option_name - 名称
     */
    public function hostConfigotpion()
    {
        $param = $this->request->param();

        $IdcsmartCommonProductModel = new IdcsmartCommonProductModel();

        $result = $IdcsmartCommonProductModel->hostConfigotpion($param);

        return json($result);
	}

    /**
     * 时间 2023-11-21
     * @title 前台产品内页自定义页面输出
     * @desc 前台产品内页自定义页面输出
     * @url /console/v1/idcsmart_common/host/:host_id/configoption/area
     * @method  GET
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @param   string key - snapshot快照等 require
     * @param   string api_url - 替换原来模板内的接口地址
     */
	public function clientAreaOutput()
    {
        $param = $this->request->param();

        $IdcsmartCommonProductModel = new IdcsmartCommonProductModel();

        $result = $IdcsmartCommonProductModel->clientAreaOutput($param);

        return json($result);
    }

    /**
     * 时间 2023-11-21
     * @title 前台产品内页图表页面
     * @desc 前台产品内页图表页面
     * @url /console/v1/idcsmart_common/host/:host_id/configoption/chart
     * @method  GET
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @param   array chart - 图表数据 require
     * @param   int chart[].start - 开始时间 require
     * @param   string chart[].type - 类型：cpu/disk/flow require
     * @param   string chart[].select - select的value值 require
     */
    public function chartData()
    {
        $param = $this->request->param();

        $IdcsmartCommonProductModel = new IdcsmartCommonProductModel();

        $result = $IdcsmartCommonProductModel->chartData($param);

        return json($result);
    }

    /**
     * 时间 2023-11-21
     * @title 执行子模块方法
     * @desc 执行子模块方法
     * @url /console/v1/idcsmart_common/host/:host_id/provision/:func
     * @method  POST
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @param   string func - 模块方法:如on开机/off关机等 require
     */
    public function provisionFunc()
    {
        $param = $this->request->param();

        $IdcsmartCommonProductModel = new IdcsmartCommonProductModel();

        $result = $IdcsmartCommonProductModel->provisionFunc($param);

        return json($result);
    }

    /**
     * 时间 2023-11-21
     * @title 执行子模块方法(解决操作密码不好统一处理的问题)
     * @desc 执行子模块方法
     * @url /console/v1/idcsmart_common/host/:host_id/provision/status
     * @method  POST
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @param   string func - 模块方法:如on开机/off关机等 require
     */
    public function provisionFuncStatus()
    {
        $param = $this->request->param();

        $param['func'] = 'status';

        $IdcsmartCommonProductModel = new IdcsmartCommonProductModel();

        $result = $IdcsmartCommonProductModel->provisionFunc($param);

        return json($result);
    }

    /**
     * 时间 2023-11-21
     * @title 执行子模块自定义方法
     * @desc 执行子模块自定义方法
     * @url /console/v1/idcsmart_common/host/:host_id/custom/provision
     * @method  POST
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @param   string func - 模块方法:如on开机/off关机等 require
     * @param   array custom_fields - 自定义字段
     */
    public function provisionFuncCustom()
    {
        $param = $this->request->param();

        $IdcsmartCommonProductModel = new IdcsmartCommonProductModel();

        $result = $IdcsmartCommonProductModel->provisionFuncCustom($param);

        return json($result);
    }

    /**
     * 时间 2022-09-26
     * @title 产品升降级页面
     * @desc 产品升降级页面
     * @url /console/v1/idcsmart_common/host/:host_id/upgrade
     * @method  GET
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @return  object host -
     * @return  int host.product_id - 商品ID
     * @return  string host.name - 名称
     * @return  float host.first_payment_amount - 金额
     * @return  string host.billing_cycle_name - 周期
     * @return  array configoptions - 配置
     * @return  string configoptions[].option_type - 配置类型
     * @return  string configoptions[].option_name - 名称
     * @return  string configoptions[].sub_name - 子项名称
     * @return  int configoptions[].qty - 数量(类型为数量时,显示此值)
     * @return  int configoptions[].configoption_sub_id - 子项ID
     * @return  array son_host - 子产品
     * @return  int son_host[].id - 子产品ID
     * @return  string son_host[].name - 名称
     * @return  float son_host[].first_payment_amount - 金额
     * @return  string son_host[].billing_cycle_name - 周期
     * @return  array upgrade - 可升降级商品(参考购物车配置那块数据)
     */
    public function upgradePage()
    {
        $param = $this->request->param();

        $IdcsmartCommonProductModel = new IdcsmartCommonProductModel();

        $result = $IdcsmartCommonProductModel->upgradePage($param);

        return json($result);
    }

    /**
     * 时间 2022-09-26
     * @title 产品升降级异步获取升降级价格
     * @desc 产品升降级异步获取升降级价格
     * @url /console/v1/idcsmart_common/host/:host_id/sync_upgrade_price
     * @method  POST
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @param  object  - 与购物车计算价格参数一致:{"configoption":{"1"：2,"2":3,"4":[1,2,3]},"cycle":"monthly","product_id":104,son:{}}
     */
    public function syncUpgradePrice()
    {
        $param = $this->request->param();

        $IdcsmartCommonProductModel = new IdcsmartCommonProductModel();

        $result = $IdcsmartCommonProductModel->syncUpgradePrice($param);

        return json($result);
    }

    /**
     * 时间 2022-09-26
     * @title 产品升降级
     * @desc 产品升降级
     * @url /console/v1/idcsmart_common/host/:host_id/upgrade
     * @method  POST
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @param   int product_id - 商品ID require
     * @param   object config_options - 与购物车结算的一样:{"configoption":{"1"：2,"2":3,"4":[1,2,3]},"cycle":"monthly","product_id":104,son:{}} require
     */
    public function upgrade()
    {
        $param = $this->request->param();

        $IdcsmartCommonProductModel = new IdcsmartCommonProductModel();

        $result = $IdcsmartCommonProductModel->upgrade($param);

        return json($result);
    }

    /**
     * 时间 2022-09-26
     * @title 产品配置升降级页面
     * @desc 产品配置升降级页面
     * @url /console/v1/idcsmart_common/host/:host_id/upgrade_config
     * @method  GET
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @return  object host -
     * @return  int host.product_id - 商品ID
     * @return  string host.name - 名称
     * @return  float host.first_payment_amount - 金额
     * @return  string host.billing_cycle_name - 周期
     * @return  object configoptions - 配置
     * @return  array configoptions - 配置
     * @return  string configoptions[].option_type - 配置类型
     * @return  string configoptions[].option_name - 名称
     * @return  string configoptions[].sub_name - 子项名称
     * @return  int configoptions[].qty - 数量(类型为数量时,显示此值)
     * @return  array son_host - 子产品
     * @return  int son_host[].id - 子产品ID
     * @return  string son_host[].name - 名称
     * @return  float son_host[].first_payment_amount - 金额
     * @return  string son_host[].billing_cycle_name - 周期
     * @return  array upgrade_configoptions - 可升降级配置项
     * @return  int upgrade_configoptions[].id - 配置项ID
     * @return  int upgrade_configoptions[].option_name - 配置项名称
     * @return  array upgrade_configoptions[].subs - 配置子项数据
     */
    public function upgradeConfigPage()
    {
        $param = $this->request->param();

        $IdcsmartCommonProductModel = new IdcsmartCommonProductModel();

        $result = $IdcsmartCommonProductModel->upgradeConfigPage($param);

        return json($result);
    }

    /**
     * 时间 2024-05-24
     * @title 产品配置升降级异步获取升降级价格
     * @desc 产品配置升降级异步获取升降级价格
     * @url /console/v1/idcsmart_common/host/:host_id/sync_upgrade_config_price
     * @method  POST
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @param  object configoption - "configoption":{"1"：2,"2":3,"4":[1,2,3]}
     * @return string data.price - 价格
     *
     */
    public function syncUpgradeConfigPrice()
    {
        $param = $this->request->param();

        $IdcsmartCommonProductModel = new IdcsmartCommonProductModel();

        $result = $IdcsmartCommonProductModel->syncUpgradeConfigPrice($param);

        return json($result);
    }

    /**
     * 时间 2022-09-26
     * @title 产品配置升降级
     * @desc 产品配置升降级
     * @url /console/v1/idcsmart_common/host/:host_id/upgrade_config
     * @method  POST
     * @author wyh
     * @version v1
     * @param   int host_id - 产品ID require
     * @param  object configoption - "configoption":{"1"：2,"2":3,"4":[1,2,3]}
     * @return int id - 订单ID
     */
    public function upgradeConfig()
    {
        $param = $this->request->param();

        $IdcsmartCommonProductModel = new IdcsmartCommonProductModel();

        $result = $IdcsmartCommonProductModel->upgradeConfig($param);

        return json($result);
    }

    /**
     * 时间 2024-05-23
     * @title 日志
     * @desc 日志
     * @url /console/v1/idcsmart_common/:id/log
     * @method  GET
     * @author wyh
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


}


