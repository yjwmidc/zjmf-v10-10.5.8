<?php
namespace app\admin\controller;

use app\common\model\ClientModel;
use app\admin\validate\ClientValidate;

/**
 * @title 用户管理
 * @desc 用户管理
 * @use app\admin\controller\ClientController
 */
class ClientController extends AdminBaseController
{	
    public function initialize()
    {
        parent::initialize();
        $this->validate = new ClientValidate();
    }

	/**
     * 时间 2022-05-10
     * @title 用户列表
     * @desc 用户列表
     * @author theworld
     * @version v1
     * @url /admin/v1/client
     * @method  GET
     * @param object custom_field - 自定义字段,key为自定义字段名称,value为自定义字段的值
     * @param string type - 关键字类型,id用户ID,username姓名,phone手机号,email邮箱,company公司
     * @param string keywords - 关键字,搜索范围随关键字类型变化，默认搜索范围:用户ID,姓名,邮箱,手机号,公司
     * @param int client_id - 用户ID,精确搜索
     * @param int page - 页数
     * @param int limit - 每页条数
     * @param string orderby id 排序(id,reg_time,host_active_num,host_num,credit,cost_price,refund_price,withdraw_price)
     * @param string sort - 升/降序 asc,desc
     * @param int show_sub_client - 显示子账户(0=隐藏,1=显示)
     * @return array list - 用户
     * @return int list[].id - 用户ID 
     * @return string list[].username - 姓名 
     * @return string list[].email - 邮箱 
     * @return int list[].phone_code - 国际电话区号 
     * @return string list[].phone - 手机号 
     * @return int list[].status - 状态;0:禁用,1:正常 
     * @return int list[].reg_time - 注册时间
     * @return string list[].country - 国家
     * @return string list[].address - 地址
     * @return string list[].company - 公司 
     * @return string list[].language - 语言
     * @return string list[].notes - 备注
     * @return string list[].credit - 余额
     * @return int list[].host_num - 产品数量 
     * @return int list[].host_active_num - 已激活产品数量
     * @return array list[].custom_field - 自定义字段
     * @return string list[].custom_field[].name - 名称
     * @return string list[].custom_field[].value - 值
     * @return string list[].cost_price - 消费金额
     * @return bool list[].certification 是否实名认证true是false否
     * @return string list[].certification_type 实名类型person个人company企业
     * @return string list[].client_level - 用户等级(显示字段有client_level返回)
     * @return string list[].client_level_color - 用户等级颜色(显示字段有client_level返回)
     * @return string list[].sale - 销售(显示字段有sale返回)
     * @return array list[].oauth - 关联的三方登录类型
     * @return int list[].mp_weixin_notice - 微信公众号关注状态(0=未关注1=已关注)
     * @return string list[].refund_price - 退款金额(显示字段有refund_price返回)
     * @return string list[].withdraw_price - 提现金额(显示字段有withdraw_price返回)
     * @return string list[].addon_client_custom_field_[num] - 用户自定义字段(显示字段有addon_client_custom_field_[num]返回,[num]为数字)
     * @return int count - 用户总数
     * @return string total_credit - 总余额
     * @return string page_total_credit - 当前页总余额
     */
	public function clientList()
	{
        // 合并分页参数
        $param = array_merge($this->request->param(), ['page' => $this->request->page, 'limit' => $this->request->limit, 'sort' => $this->request->sort]);
        
        // 实例化模型类
        $ClientModel = new ClientModel();

        // 获取用户列表
        $data = $ClientModel->clientList($param);

        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data
        ];
        return json($result);
	}

    /**
     * 时间 2022-05-10
     * @title 用户详情
     * @desc 用户详情
     * @author theworld
     * @version v1
     * @url /admin/v1/client/:id
     * @method  GET
     * @param int id - 用户ID required
     * @return object client - 用户
     * @return int client.id - 用户ID 
     * @return string client.username - 姓名 
     * @return string client.email - 邮箱 
     * @return int client.phone_code - 国际电话区号 
     * @return string client.phone - 手机号 
     * @return string client.company - 公司 
     * @return int client.country_id - 国家ID 
     * @return string client.address - 地址 
     * @return string client.language - 语言 
     * @return string client.notes - 备注
     * @return int client.status - 状态;0:禁用,1:正常 
     * @return int client.register_time - 注册时间 
     * @return int client.last_login_time - 上次登录时间 
     * @return string client.last_login_ip - 上次登录IP
     * @return string client.credit - 余额 
     * @return string client.consume - 消费 
     * @return string client.refund - 退款 
     * @return string client.withdraw - 提现 
     * @return int client.host_num - 产品数量 
     * @return int client.host_active_num - 已激活产品数量
     * @return array client.login_logs - 登录记录
     * @return string client.login_logs[].ip - IP
     * @return int client.login_logs[].login_time - 登录时间
     * @return boolean client.certification - 是否实名认证true是false否
     * @return object client.certification_detail - 实名认证详情(当certification==true时,才会有此字段)
     * @return object certification_detail.company 企业实名认证详情
     * @return string client.certification_detail.company.card_name - 认证姓名
     * @return int client.certification_detail.company.card_type - 证件类型:1身份证、2港澳通行证、3台湾通行证、4港澳居住证、5台湾居住证、6海外护照、7中国以外驾照、8其他
     * @return string client.certification_detail.company.card_number - 证件号
     * @return string client.certification_detail.company.phone - 手机号
     * @return int client.certification_detail.company.status - 状态1已认证，2未通过，3待审核，4已提交资料
     * @return string client.certification_detail.company.company - 公司名称
     * @return string client.certification_detail.company.company_organ_code - 公司代码
     * @return string client.certification_detail.company.img_one - 身份证正面
     * @return string client.certification_detail.company.img_two - 身份证反面
     * @return string client.certification_detail.company.img_three - 营业执照
     * @return string client.certification_detail.company.auth_fail - 失败原因
     * @return object certification_detail.person 个人实名认证详情
     * @return string client.certification_detail.person.card_name - 认证姓名
     * @return int client.certification_detail.person.card_type - 证件类型:1身份证、2港澳通行证、3台湾通行证、4港澳居住证、5台湾居住证、6海外护照、7中国以外驾照、8其他
     * @return string client.certification_detail.person.card_number - 证件号
     * @return string client.certification_detail.person.phone - 手机号
     * @return int client.certification_detail.person.status - 状态1已认证，2未通过，3待审核，4已提交资料
     * @return string client.certification_detail.person.img_one - 身份证正面
     * @return string client.certification_detail.person.img_two - 身份证反面
     * @return string client.certification_detail.person.img_three - 营业执照
     * @return string client.certification_detail.person.auth_fail - 失败原因
     * @return bool client.set_operate_password - 是否设置了操作密码
     * @return int client.receive_sms - 接收短信(0=关闭1=开启)
     * @return int client.receive_email - 接收邮件(0=关闭1=开启)
     * @return array client.oauth - 关联的三方登录类型
     * @return int client.mp_weixin_notice - 微信公众号关注状态(0=未关注1=已关注)
     */
    public function index()
    {
        // 接收参数
        $param = $this->request->param();
        
        // 实例化模型类
        $ClientModel = new ClientModel();

        // 获取用户
        $client = $ClientModel->indexClient($param['id']);

        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => [
                'client' => $client
            ]
        ];
        return json($result);
    }

	/**
     * 时间 2022-05-10
     * @title 新建用户
     * @desc 新建用户
     * @author theworld
     * @version v1
     * @url /admin/v1/client
     * @method  POST
     * @param string username - 姓名
     * @param string email - 邮箱 邮箱手机号两者至少输入一个
     * @param int phone_code - 国际电话区号 输入手机号时必须传此参数
     * @param string phone - 手机号 邮箱手机号两者至少输入一个
     * @param string password - 密码 required
     * @param string repassword - 重复密码 required
     * @return int id - 用户ID
     */
	public function create()
    {
        // 接收参数
		$param = $this->request->param();

        // 参数验证
        if (!$this->validate->scene('create')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

		// 实例化模型类
        $ClientModel = new ClientModel();
        
        // 新建用户
        $result = $ClientModel->createClient($param);

        return json($result);
	}

	/**
     * 时间 2022-05-10
     * @title 修改用户
     * @desc 修改用户
     * @author theworld
     * @version v1
     * @url /admin/v1/client/:id
     * @method  PUT
     * @param int id - 用户ID required
     * @param string username - 姓名
     * @param string email - 邮箱 邮箱手机号两者至少输入一个
     * @param int phone_code - 国际电话区号 输入手机号时必须传此参数
     * @param string phone - 手机号 邮箱手机号两者至少输入一个
     * @param string company - 公司
     * @param string country - 国家
     * @param string address - 地址
     * @param string language - 语言
     * @param string notes - 备注
     * @param string password - 密码 为空代表不修改
     * @param string operate_password - 操作密码 为空代表不修改
     * @param object customfield 自定义字段
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
        $ClientModel = new ClientModel();
        
        // 修改用户
        $result = $ClientModel->updateClient($param);

        return json($result);
	}

	/**
     * 时间 2022-05-10
     * @title 删除用户
     * @desc 删除用户
     * @author theworld
     * @version v1
     * @url /admin/v1/client/:id
     * @method  DELETE
     * @param int id - 用户ID required
     */
	public function delete()
    {
        // 接收参数
        $param = $this->request->param();

        // 实例化模型类
        $ClientModel = new ClientModel();
        
        // 删除用户
        $result = $ClientModel->deleteClient($param);

        return json($result);

	}

    /**
     * 时间 2022-5-26
     * @title 用户状态切换
     * @desc 用户状态切换
     * @author theworld
     * @version v1
     * @url /admin/v1/client/:id/status
     * @method  put
     * @param int id - 用户ID required
     * @param int status 1 状态:0禁用,1启用 required
     */
    public function status()
    {
        // 接收参数
        $param = $this->request->param();

        // 参数验证
        if (!$this->validate->scene('status')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        // 实例化模型类
        $ClientModel = new ClientModel();
        
        // 更改状态
        $result = $ClientModel->updateClientStatus($param);

        return json($result);
    }

    /**
     * 时间 2022-5-26
     * @title 修改用户接收短信
     * @desc 修改用户接收短信
     * @author theworld
     * @version v1
     * @url /admin/v1/client/:id/receive_sms
     * @method  put
     * @param int id - 用户ID required
     * @param int receive_sms 1 接收短信:0禁用,1启用 required
     */
    public function receiveSms()
    {
        // 接收参数
        $param = $this->request->param();

        // 参数验证
        if (!$this->validate->scene('receive_sms')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        // 实例化模型类
        $ClientModel = new ClientModel();
        
        // 修改用户接收短信
        $result = $ClientModel->updateClientReceiveSms($param);

        return json($result);
    }

    /**
     * 时间 2022-5-26
     * @title 修改用户接收邮件
     * @desc 修改用户接收邮件
     * @author theworld
     * @version v1
     * @url /admin/v1/client/:id/receive_email
     * @method  put
     * @param int id - 用户ID required
     * @param int receive_email 1 接收邮件:0禁用,1启用 required
     */
    public function receiveEmail()
    {
        // 接收参数
        $param = $this->request->param();

        // 参数验证
        if (!$this->validate->scene('receive_email')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        // 实例化模型类
        $ClientModel = new ClientModel();
        
        // 修改用户接收邮件
        $result = $ClientModel->updateClientReceiveEmail($param);

        return json($result);
    }

    /**
     * 时间 2022-05-16
     * @title 搜索用户
     * @desc 搜索用户
     * @author theworld
     * @version v1
     * @url /admin/v1/client/search
     * @method  GET
     * @param string keywords - 关键字,搜索范围:用户ID,姓名,邮箱,手机号
     * @param int client_id - 用户ID,精确搜索
     * @return array list - 用户
     * @return int list[].id - 用户ID 
     * @return string list[].username - 姓名
     */
    public function search()
    {
        // 接收参数
        $param = $this->request->param();
        
        // 实例化模型类
        $ClientModel = new ClientModel();

        // 获取用户列表
        $data = $ClientModel->searchClient($param);

        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data
        ];
        return json($result);
    }

    /**
     * 时间 2022-05-30
     * @title 以用户登录
     * @desc 以用户登录
     * @author wyh
     * @version v1
     * @url /admin/v1/client/:id/login
     * @method  POST
     * @param int id - 用户ID required
     * @return string jwt - jwt:获取后放在请求头Authorization里,拼接成如下格式:Bearer yJ0eX.test.ste
     */
    public function login()
    {
        // 接收参数
        $param = $this->request->param();

        // 实例化模型类
        $ClientModel = new ClientModel();

        // 获取用户列表
        $result = $ClientModel->loginByClient(intval($param['id']));

        return json($result);
    }
}