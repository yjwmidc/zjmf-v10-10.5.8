<?php
namespace app\admin\controller;

use app\common\model\HostModel;
use app\admin\validate\HostValidate;
use app\common\model\SelfDefinedFieldModel;
use app\common\model\HostIpModel;

/**
 * @title 产品管理
 * @desc 产品管理
 * @use app\admin\controller\HostController
 */
class HostController extends AdminBaseController
{
	public function initialize()
    {
        parent::initialize();
        $this->validate = new HostValidate();
    }

    /**
     * 时间 2022-05-13
     * @title 产品列表
     * @desc 产品列表
     * @author theworld
     * @version v1
     * @url /admin/v1/host
     * @method  GET
     * @param string keywords - 关键字,搜索范围:产品ID,商品名称,标识,用户名,邮箱,手机号
     * @param string billing_cycle - 付款周期
     * @param int client_id - 用户ID
     * @param int host_id - 产品ID
     * @param string status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败
     * @param string due_time - 到期时间(today=今天内three=最近三天seven=最近七天month=最近一个月custom=自定义expired=已到期)
     * @param int start_time - 开始时间
     * @param int end_time - 结束时间
     * @param int product_id - 商品ID
     * @param string name - 标识
     * @param string username - 用户名
     * @param string email - 邮箱
     * @param string phone - 手机号
     * @param int server_id - 接口ID
     * @param string first_payment_amount - 订购金额
     * @param string ip - IP
     * @param string tab - 状态using使用中expiring即将到期overdue已逾期deleted已删除
     * @param int view_id - 视图ID
     * @param int page - 页数
     * @param int limit - 每页条数
     * @param string orderby id 排序(id,renew_amount,due_time,first_payment_amount,active_time,client_id,reg_time)
     * @param string sort - 升/降序 asc,desc
     * @param string module - 搜索:模块
     * @return array list - 产品
     * @return int list[].id - 产品ID 
     * @return int list[].client_id - 用户ID 
     * @return int list[].client_name - 用户名 
     * @return string list[].email - 邮箱 
     * @return string list[].phone_code - 国际电话区号 
     * @return string list[].phone - 手机号 
     * @return string list[].company - 公司 
     * @return int list[].product_id - 商品ID 
     * @return string list[].product_name - 商品名称 
     * @return string list[].name - 标识 
     * @return int list[].active_time - 开通时间 
     * @return int list[].due_time - 到期时间
     * @return string list[].first_payment_amount - 金额
     * @return string list[].billing_cycle - 计费方式(免费free，一次onetime，周期先付recurring_prepayment,周期后付recurring_postpaid)
     * @return string list[].billing_cycle_name - 周期
     * @return string list[].status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败
     * @return string list[].renew_amount - 续费金额
     * @return string list[].client_notes - 用户备注
     * @return int list[].ip_num - IP数量
     * @return string list[].dedicate_ip - 主IP
     * @return string list[].assign_ip - 附加IP(英文逗号分隔)
     * @return string list[].server_name - 商品接口
     * @return string list[].admin_notes - 管理员备注
     * @return string list[].base_price - 当前周期原价
     * @return int list[].client_status - 用户是否启用0:禁用,1:正常
     * @return int list[].reg_time - 用户注册时间
     * @return string list[].country - 国家
     * @return string list[].address - 地址
     * @return string list[].language - 语言
     * @return string list[].notes - 备注
     * @return bool list[].certification - 是否实名认证true是false否(显示字段有certification返回)
     * @return string list[].certification_type - 实名类型person个人company企业(显示字段有certification返回)
     * @return string list[].client_level - 用户等级(显示字段有client_level返回)
     * @return string list[].client_level_color - 用户等级颜色(显示字段有client_level返回)
     * @return string list[].sale - 销售(显示字段有sale返回)
     * @return string list[].addon_client_custom_field_[id] - 用户自定义字段(显示字段有addon_client_custom_field_[id]返回,[id]为用户自定义字段ID)
     * @return string list[].self_defined_field_[id] - 商品自定义字段(显示字段有self_defined_field_[id]返回,[id]为商品自定义字段ID)
     * @return string list[].base_info - 产品基础信息
     * @return int count - 产品总数
     * @return int expiring_count - 即将到期产品数量
     * @return string total_renew_amount - 总续费金额
     * @return string page_total_renew_amount - 当前页总续费金额
     * @return int failed_action_count - 手动处理产品数量
     */
	public function hostList()
    {
		// 合并分页参数
        $param = array_merge($this->request->param(), ['page' => $this->request->page, 'limit' => $this->request->limit, 'sort' => $this->request->sort]);
        
        // 实例化模型类
        $HostModel = new HostModel();

        // 获取产品列表
        $data = $HostModel->hostList($param);

        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data
        ];
        return json($result);
	}

	/**
     * 时间 2022-05-13
     * @title 产品详情
     * @desc 产品详情
     * @param int id - 产品ID required
     * @return object host - 产品
     * @return int host.id - 产品ID
     * @return int host.order_id - 订单ID
     * @return int host.product_id - 商品ID
     * @return int host.server_id - 接口ID
     * @return string host.name - 标识
     * @return string host.notes - 备注
     * @return string host.first_payment_amount - 订购金额
     * @return string host.renew_amount - 续费金额
     * @return string host.billing_cycle - 计费周期
     * @return string host.billing_cycle_name - 模块计费周期名称
     * @return string host.billing_cycle_time - 模块计费周期时间
     * @return int host.active_time - 开通时间
     * @return int host.due_time - 到期时间
     * @return string host.status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败
     * @return string host.suspend_type - 暂停类型,overdue到期暂停,overtraffic超流暂停,certification_not_complete实名未完成,other其他
     * @return string host.suspend_reason - 暂停原因
     * @return string host.client_notes - 用户备注
     * @return int host.ratio_renew - 是否开启比例续费:0否,1是
     * @return string host.base_price - 购买周期原价
     * @return array status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败
     * @return string host.product_name - 商品名称
     * @return int host.agent - 代理产品0否1是
     * @return string host.upstream_host_id - 上游产品ID
     * @return string host.mode - 商品代理模式：only_api仅调用接口，sync同步商品
     * @return int host.addition.country_id - 国家ID
     * @return string host.addition.city - 城市
     * @return string host.addition.area - 区域
     * @return string host.addition.image_icon - 镜像图标(Windows,CentOS,Ubuntu,Debian,ESXi,XenServer,FreeBSD,Fedora,ArchLinux,Rocky,AlmaLinux,OpenEuler,RedHat,其他)
     * @return string host.addition.image_name - 镜像名称
     * @return  int self_defined_field[].id - 自定义字段ID
     * @return  string self_defined_field[].field_name - 字段名称
     * @return  string self_defined_field[].field_type - 字段类型(text=文本框,link=链接,password=密码,dropdown=下拉,checkbox=勾选框,textarea=文本区)
     * @return  string self_defined_field[].description - 字段描述
     * @return  string self_defined_field[].field_option - 下拉选项
     * @return  int self_defined_field[].is_required - 是否必填(0=否,1=是)
     * @return  string self_defined_field[].value - 当前值
     *@author theworld
     * @version v1
     * @url /admin/v1/host/:id
     * @method  GET
     */
	public function index()
    {
		// 接收参数
        $param = $this->request->param();
        
        // 实例化模型类
        $HostModel = new HostModel();
        $SelfDefinedFieldModel = new SelfDefinedFieldModel();

        // 获取产品
        $host = $HostModel->indexHost($param['id']);
        $selfDefinedField = $SelfDefinedFieldModel->showAdminHostDetailField(['host_id'=>$param['id']]);
        if(isset($host->addition)){
            $host['addition'] = (object)$host['addition'];
        }

        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => [
                'host'              => $host,
                'status'            => config('idcsmart.host_status'),
                'self_defined_field'=> $selfDefinedField,
            ]
        ];
        return json($result);
	}	

	/**
     * 时间 2022-05-13
     * @title 修改产品
     * @desc 修改产品
     * @author theworld
     * @version v1
     * @url /admin/v1/host/:id
     * @method  put
     * @param int id - 产品ID required
     * @param int product_id - 商品ID required
     * @param int server_id - 接口
     * @param string name - 标识
     * @param string notes - 备注
     * @param string upstream_host_id - 上游产品ID
     * @param float first_payment_amount - 订购金额 required
     * @param float renew_amount - 续费金额 required
     * @param string billing_cycle - 计费周期 required
     * @param string active_time - 开通时间
     * @param string due_time - 到期时间
     * @param string status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败
     * @param object self_defined_field - 自定义字段({"5":"123"},5是自定义字段ID,123是填写的内容)
     * @param int host.ratio_renew - 是否开启比例续费:0否,1是
     * @param float host.base_price - 购买周期原价
     * @param object customfield - 自定义字段
     */
	public function update()
    {
		// 接收参数
        $param = $this->request->param();

        // 参数验证
        if (!$this->validate->scene('update')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        // 实例化模型类
        $HostModel = new HostModel();
        
        // 修改产品
        $result = $HostModel->updateHost($param);

        return json($result);
	}

	/**
     * 时间 2022-05-13
     * @title 删除产品
     * @desc 删除产品
     * @author theworld
     * @version v1
     * @url /admin/v1/host/:id
     * @method  DELETE
     * @param int id - 产品ID required
     */
	public function delete()
    {
		// 接收参数
        $param = $this->request->param();

        // 实例化模型类
        $HostModel = new HostModel();
        
        // 删除产品
        $result = $HostModel->deleteHost($param);

        return json($result);
	}

    /**
     * 时间 2022-05-13
     * @title 批量删除产品
     * @desc 批量删除产品
     * @author theworld
     * @version v1
     * @url /admin/v1/host
     * @method  DELETE
     * @param array id - 产品ID required
     * @param int module_delete - 是否执行模块删除，1是0否 required
     */
    public function batchDelete()
    {
        // 接收参数
        $param = $this->request->param();

        // 实例化模型类
        $HostModel = new HostModel();
        
        // 批量删除产品
        $result = $HostModel->batchDeleteHost($param);

        return json($result);
    }

    /**
     * 时间 2022-05-30
     * @title 模块开通 
     * @desc 模块开通
     * @url /admin/v1/host/:id/module/create
     * @method  POST
     * @author hh
     * @version v1
     * @param   int id - 产品ID required
     */
    public function createAccount()
    {
        $param = $this->request->param();

        // 实例化模型类
        $HostModel = new HostModel();
        
        $result = $HostModel->createAccount($param['id']);
        return json($result);
    }

    /**
     * 时间 2022-05-30
     * @title 模块暂停
     * @desc 模块暂停
     * @url /admin/v1/host/:id/module/suspend
     * @method  POST
     * @author hh
     * @version v1
     * @param   int id - 产品ID required
     * @param   string suspend_type - 暂停类型(overdue=到期暂停,overtraffic=超流暂停,certification_not_complete=实名未完成,other=其他) required
     * @param   string suspend_reason - 暂停原因
     */
    public function suspendAccount()
    {
        $param = $this->request->param();

        // 参数验证
        if (!$this->validate->scene('suspend')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        // 实例化模型类
        $HostModel = new HostModel();
        
        $result = $HostModel->suspendAccount($param);
        return json($result);
    }

    /**
     * 时间 2022-05-30
     * @title 模块解除暂停
     * @desc 模块解除暂停
     * @url /admin/v1/host/:id/module/unsuspend
     * @method  POST
     * @author hh
     * @version v1
     * @param   int id - 产品ID required
     */
    public function unsuspendAccount()
    {
        $param = $this->request->param();

        // 实例化模型类
        $HostModel = new HostModel();
        
        $result = $HostModel->unsuspendAccount($param['id']);
        return json($result);
    }

    /**
     * 时间 2022-05-30
     * @title 模块删除
     * @desc 模块删除
     * @url /admin/v1/host/:id/module/terminate
     * @method  POST
     * @author hh
     * @version v1
     * @param   int id - 产品ID required
     */
    public function terminateAccount()
    {
        $param = $this->request->param();

        // 实例化模型类
        $HostModel = new HostModel();
        
        $result = $HostModel->terminateAccount($param['id']);
        return json($result);
    }

    /**
     * 时间 2022-05-30
     * @title 产品内页模块
     * @desc 产品内页模块
     * @url /admin/v1/host/:id/module
     * @method  GET
     * @author hh
     * @version v1
     * @param   int id - 产品ID required
     * @return  string content - 模块输出内容
     */
    public function adminArea()
    {
        $param = $this->request->param();

        // 实例化模型类
        $HostModel = new HostModel();
        
        $result = $HostModel->adminArea($param['id']);
        return json($result);
    }

    /**
     * 时间 2022-10-26
     * @title 获取用户所有产品
     * @desc 获取用户所有产品
     * @author theworld
     * @version v1
     * @url /admin/v1/client/:id/host/all
     * @method  GET
     * @param int id - 用户ID required
     * @return array list - 产品
     * @return int list[].id - 产品ID 
     * @return int list[].product_id - 商品ID 
     * @return string list[].product_name - 商品名称 
     * @return string list[].name - 标识 
     * @return int count - 产品总数
     */
    public function clientHost()
    {
        $param = $this->request->param();

        // 实例化模型类
        $HostModel = new HostModel();

        // 获取用户产品
        $data = $HostModel->clientHost($param);

        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data
        ];
        return json($result);
    }

    /**
     * 时间 2023-01-31
     * @title 模块按钮输出
     * @desc 模块按钮输出
     * @url /admin/v1/host/:id/module/button
     * @method  GET
     * @author hh
     * @version v1
     * @param   int id - 产品ID require
     * @return  string button[].type - 按钮类型(暂时都是default)
     * @return  string button[].func - 按钮功能(create=开通,suspend=暂停,unsuspend=解除暂停,terminate=删除,renew=续费)
     * @return  string button[].name - 名称
     */
    public function moduleButton()
    {
        $param = $this->request->param();

        // 实例化模型类
        $HostModel = new HostModel();

        $result = $HostModel->moduleAdminButton($param);
        return json($result);
    }

    /**
     * 时间 2023-04-14
     * @title 产品内页模块输入框输出 
     * @desc 产品内页模块输入框输出
     * @url /admin/v1/host/:id/module/field
     * @method  GET
     * @author hh
     * @version v1
     * @param   int id - 产品ID require
     * @return  string [].name - 配置小标题
     * @return  string [].field[].name - 名称
     * @return  string [].field[].key - 标识(不要重复)
     * @return  string [].field[].value - 当前值
     * @return  bool   [].field[].disable - 状态(false=可修改,true=不可修改)
     */
    public function moduleField()
    {
        $param = $this->request->param();

        // 实例化模型类
        $HostModel = new HostModel();

        $result = $HostModel->moduleField($param['id']);
        return json($result);
    }

    /**
     * 时间 2024-01-10
     * @title 产品IP详情
     * @desc  产品IP详情
     * @url /admin/v1/host/:id/ip
     * @method  GET
     * @author hh
     * @version v1
     * @param   int id - 产品ID require
     * @return  string dedicate_ip - 主IP
     * @return  string assign_ip - 附加IP(英文逗号分隔)
     * @return  int ip_num - IP数量
     */
    public function hostIpIndex()
    {
        $param = $this->request->param();

        $HostIpModel = new HostIpModel();

        $data = $HostIpModel->getHostIp(['host_id'=>$param['id']]);

        $result = [
            'status'    => 200,
            'msg'       => lang('success_message'),
            'data'      => $data,
        ];
        return json($result);
    }

    /**
     * 时间 2024-05-20
     * @title 后台产品内页实例操作输出
     * @desc  后台产品内页实例操作输出
     * @url /admin/v1/host/:id/module_operate
     * @method  GET
     * @author hh
     * @version v1
     * @param   int id - 产品ID required
     * @return  string content - 模块输出内容
     */
    public function adminAreaModuleOperate()
    {
        $param = $this->request->param();

        // 实例化模型类
        $HostModel = new HostModel();
        
        $result = $HostModel->adminAreaModuleOperate($param['id']);
        return json($result);
    }

    /**
     * 时间 2024-06-06
     * @title 拉取上游信息
     * @desc 拉取上游信息
     * @url /admin/v1/host/:id/module/sync
     * @method  GET
     * @author wyh
     * @version v1
     * @param   int id - 产品ID required
     */
    public function syncAccount()
    {
        $param = $this->request->param();

        // 实例化模型类
        $HostModel = new HostModel();

        $result = $HostModel->syncAccount($param['id']);

        return json($result);
    }

    /**
     * @时间 2024-12-10
     * @title 手动处理产品列表
     * @desc  手动处理产品列表
     * @author hh
     * @version v1
     * @url /admin/v1/host/failed_action
     * @method GET
     * @param   int page - 页数
     * @param   int limit - 每页条数
     * @param   string action - 搜索:失败动作(create=开通失败,suspend=暂停失败,terminate=删除失败)
     * @param   string keywords - 关键字:产品ID,商品名称,产品标识,IP地址
     * @param   string orderby failed_action_trigger_time 排序(id,due_time,failed_action_trigger_time)
     * @return  int list[].id - 产品ID
     * @return  string list[].name - 产品标识
     * @return  int list[].product_id - 商品ID
     * @return  string list[].product_name - 商品名称
     * @return  int list[].client_id - 用户ID
     * @return  string list[].status - 产品状态(Unpaid=未付款,Pending=开通中,Active=已开通,Suspended=已暂停,Deleted=已删除)
     * @return  string list[].failed_action - 失败动作(create=开通失败,suspend=暂停失败,terminate=删除失败)
     * @return  string list[].failed_action_reason - 失败原因
     * @return  string list[].renew_amount - 续费金额
     * @return  string list[].billing_cycle - 计费方式(免费free，一次onetime，周期先付recurring_prepayment,周期后付recurring_postpaid)
     * @return  string list[].billing_cycle_name - 模块计费周期名称
     * @return  int list[].due_time - 到期时间
     * @return  string list[].client_name - 用户名
     * @return  string list[].email - 邮箱
     * @return  int list[].phone_code - 区号
     * @return  string list[].phone - 手机号
     * @return  int list[].failed_action_trigger_time - 触发时间
     * @return  int count - 总条数
     * @return  int expiring_count - 即将到期产品数量
     * @return  int failed_action_count - 手动处理产品数量
     */
    public function failedActionHostList()
    {
        // 合并分页参数
        $param = array_merge($this->request->param(), ['page' => $this->request->page, 'limit' => $this->request->limit, 'sort' => $this->request->sort]);
        
        // 实例化模型类
        $HostModel = new HostModel();

        // 获取产品列表
        $data = $HostModel->failedActionHostList($param);

        $result = [
            'status' => 200,
            'msg'    => lang('success_message'),
            'data'   => $data,
        ];
        return json($result);
    }

    /**
     * @时间 2024-12-10
     * @title 标记已处理
     * @desc  标记已处理
     * @author hh
     * @version v1
     * @url /admin/v1/host/:id/mark_processed
     * @method  POST
     * @param  int id - 产品ID require
     */
    public function failedActionMarkProcessed()
    {
        $param = $this->request->param();

        // 实例化模型类
        $HostModel = new HostModel();

        $result = $HostModel->failedActionMarkProcessed((int)$param['id']);
        return json($result);
    }

    /**
     * @时间 2025-01-23
     * @title 批量同步
     * @desc  批量同步
     * @author hh
     * @version v1
     * @url /admin/v1/host/sync
     * @method  POST
     * @param   array product_id - 商品ID require
     * @param   array host_status - 产品状态(Active已开通Suspended已暂停) require
     */
    public function batchSyncAccount()
    {
        // 接收参数
        $param = $this->request->param();

        $data = [];
        $data['batch_sync_product_id'] = $param['product_id'] ?? [];
        $data['batch_sync_host_status'] = $param['host_status'] ?? [];

        if (!$this->validate->scene('batch_sync')->check($data)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        // 实例化模型类
        $HostModel = new HostModel();
        
        $result = $HostModel->batchSyncAccount($param);

        return json($result);
    }



}