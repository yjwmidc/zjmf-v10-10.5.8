<?php
namespace app\home\controller;

use app\common\model\HostModel;
use app\common\model\UpstreamHostModel;
use app\home\validate\HostValidate;
use app\common\model\SelfDefinedFieldModel;
use app\common\model\HostIpModel;

/**
 * @title 产品管理
 * @desc 产品管理
 * @use app\home\controller\HostController
 */
class HostController extends HomeBaseController
{
    public function initialize()
    {
        parent::initialize();
        $this->validate = new HostValidate();
    }
    
    /**
     * 时间 2022-05-19
     * @title 产品列表
     * @desc 产品列表
     * @author theworld
     * @version v1
     * @url /console/v1/host
     * @method  GET
     * @param string keywords - 关键字,搜索范围:产品ID,商品名称,标识
     * @param string status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除
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
     * @return string list[].status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除
     * @return string list[].renew_amount - 续费金额
     * @return string list[].client_notes - 用户备注
     * @return int list[].ip_num - 金额
     * @return int count - 产品总数
     */
	public function list()
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
     * 时间 2022-10-13
     * @title 自定义导航产品列表
     * @desc 自定义导航产品列表
     * @author theworld
     * @version v1
     * @url /console/v1/menu/:id/host
     * @method  GET
     * @param int id - 导航ID
     * @return  string data.content - 模块输出内容
     */
    public function menuHostList()
    {
        $param = $this->request->param();
        
        // 实例化模型类
        $HostModel = new HostModel();

        // 获取产品
        $result = $HostModel->menuHostList((int)$param['id']);
        return json($result);
    }

	/**
     * 时间 2022-05-19
     * @title 产品详情
     * @desc 产品详情
     * @author theworld
     * @version v1
     * @url /console/v1/host/:id
     * @method  GET
     * @param int id - 产品ID required
     * @return object host - 产品
     * @return int host.id - 产品ID 
     * @return int host.order_id - 订单ID 
     * @return int host.product_id - 商品ID 
     * @return string host.name - 标识 
     * @return string notes - 备注 
     * @return string host.first_payment_amount - 订购金额
     * @return string host.renew_amount - 续费金额
     * @return string host.billing_cycle - 计费周期
     * @return string host.billing_cycle_name - 模块计费周期名称
     * @return string host.billing_cycle_time - 模块计费周期时间,秒
     * @return int host.active_time - 开通时间 
     * @return int host.due_time - 到期时间
     * @return string host.status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败
     * @return string host.suspend_type - 暂停类型,overdue到期暂停,overtraffic超流暂停,certification_not_complete实名未完成,other其他
     * @return string host.suspend_reason - 暂停原因
     * @return int host.ratio_renew - 是否开启比例续费:0否,1是
     * @return string host.base_price - 购买周期原价
     * @return string host.product_name - 商品名称
     * @return int host.agent - 代理产品0否1是
     * @return string host.upstream_host_id - 上游产品ID
     * @return string host.base_info - 产品基础信息
     * @return int host.addition.country_id - 国家ID
     * @return string host.addition.city - 城市
     * @return string host.addition.area - 区域
     * @return string host.addition.image_icon - 镜像图标(Windows,CentOS,Ubuntu,Debian,ESXi,XenServer,FreeBSD,Fedora,ArchLinux,Rocky,AlmaLinux,OpenEuler,RedHat,其他)
     * @return string host.addition.image_name - 镜像名称
     * @return string host.addition.username - 实例用户名
     * @return string host.addition.password - 实例密码
     * @return int host.addition.port - 端口
     * @return  int self_defined_field[].id - 自定义字段ID
     * @return  string self_defined_field[].field_name - 字段名称
     * @return  string self_defined_field[].field_type - 字段类型(text=文本框,link=链接,password=密码,dropdown=下拉,checkbox=勾选框,textarea=文本区)
     * @return  string self_defined_field[].value - 当前值
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
        $selfDefinedField = $SelfDefinedFieldModel->showClientHostDetailField(['host_id'=>$param['id']]);
        if(isset($host->addition)){
            $host['addition'] = (object)$host['addition'];
        }

        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => [
                'host'              => $host,
                'self_defined_field'=> $selfDefinedField,
            ]
        ];
        return json($result);
	}

    /**
     * 时间 2022-05-30
     * @title 获取产品内页
     * @desc 获取产品内页
     * @url /console/v1/host/:id/view
     * @method  GET
     * @author hh
     * @version v1
     * @param   int id - 产品ID required
     * @return  string content - 模块输出内容
     */
    public function clientArea()
    {
        $param = $this->request->param();
        
        // 实例化模型类
        $HostModel = new HostModel();

        // 获取产品
        $result = $HostModel->clientArea((int)$param['id']);
        return json($result);
    }

    /**
     * 时间 2022-08-11
     * @title 修改产品备注
     * @desc 修改产品备注
     * @author theworld
     * @version v1
     * @url /console/v1/host/:id/notes
     * @method  put
     * @param int id - 产品ID required
     * @param string notes - 备注
     */
    public function updateHostNotes()
    {
        // 接收参数
        $param = $this->request->param();

        // 参数验证
        if (!$this->validate->scene('update_notes')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        // 实例化模型类
        $HostModel = new HostModel();
        
        // 修改产品备注
        $result = $HostModel->updateHostNotes($param);

        return json($result);
    }

    /**
     * 时间 2022-10-26
     * @title 获取用户所有产品
     * @desc 获取用户所有产品
     * @author theworld
     * @version v1
     * @url /console/v1/host/all
     * @method  GET
     * @return array list - 产品
     * @return int list[].id - 产品ID 
     * @return int list[].product_id - 商品ID 
     * @return string list[].product_name - 商品名称 
     * @return string list[].name - 标识
     * @return string list[].status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败Cancelled已取消
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
            'msg' => lang_plugins('success_message'),
            'data' => $data
        ];
        return json($result);
    }

    /**
     * 时间 2023-02-20
     * @title 模块暂停
     * @desc 模块暂停
     * @url /console/v1/host/:id/module/suspend
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
     * 时间 2023-02-20
     * @title 模块解除暂停
     * @desc 模块解除暂停
     * @url /console/v1/host/:id/module/unsuspend
     * @method  POST
     * @author wyh
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
     * 时间 2024-04-30
     * @title 产品IP详情
     * @desc  产品IP详情
     * @url /console/v1/host/:id/ip
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

    public function hostUpdateDownstream()
    {
        $param = $this->request->param();

        $HostModel = new HostModel();

        $result = $HostModel->hostUpdateDownstream($param);

        return json($result);
    }

    /**
     * @时间 2024-12-09
     * @title 获取产品具体信息
     * @desc  获取产品具体信息,目前用于续费开关
     * @author hh
     * @version v1
     * @url /console/v1/host/:id/specific_info
     * @method  GET
     * @param   int id - 产品ID
     * @return  int id - 产品ID
     * @return  string name - 产品标识
     * @return  string renew_amount - 续费金额
     * @return  string billing_cycle_name - 模块计费周期名称
     * @return  int due_time - 到期时间
     * @return  int ip_num - IP数量
     * @return  string dedicate_ip - 主IP
     * @return  string assign_ip - 附加IP(英文逗号分隔)
     * @return  string country - 国家
     * @return  string country_code - 国家代码
     * @return  int country_id - 国家ID
     * @return  string city - 城市
     * @return  string area - 区域
     */
    public function hostSpecificInfo()
    {
        $param = $this->request->param();

        $HostModel = new HostModel();

        $data = $HostModel->hostSpecificInfo((int)$param['id']);

        $result = [
            'status' => 200,
            'msg'    => lang('success_message'),
            'data'   => (object)$data,
        ];
        return json($result);
    }


}