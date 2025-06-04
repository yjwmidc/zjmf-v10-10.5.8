<?php
namespace app\admin\controller;

use app\common\model\TransactionModel;
use app\admin\validate\TransactionValidate;

/**
 * @title 交易流水管理
 * @desc 交易流水管理
 * @use app\admin\controller\TransactionController
 */
class TransactionController extends AdminBaseController
{
	public function initialize()
    {
        parent::initialize();
        $this->validate = new TransactionValidate();
    }

    /**
     * 时间 2022-05-17
     * @title 交易流水列表
     * @desc 交易流水列表
     * @author theworld
     * @version v1
     * @url /admin/v1/transaction
     * @method  GET
     * @param string keywords - 关键字,搜索范围:交易流水号,订单ID,用户名称,邮箱,手机号
     * @param string type - 类型new新订单renew续费订单upgrade升降级订单artificial人工订单
     * @param int client_id - 用户ID
     * @param int order_id - 订单ID
     * @param string amount - 金额
     * @param string gateway - 支付方式
     * @param int start_time - 开始时间
     * @param int end_time - 结束时间
     * @param int page - 页数
     * @param int limit - 每页条数
     * @param string orderby id 排序(id,amount,transaction_number,order_id,create_time,client_id,reg_time)
     * @param string sort - 升/降序 asc,desc
     * @return array list - 交易流水
     * @return int list[].id - 交易流水ID 
     * @return float list[].amount - 金额
     * @return string list[].gateway - 支付方式
     * @return string list[].transaction_number - 交易流水号
     * @return int list[].client_id - 用户ID 
     * @return string list[].client_name - 用户名称 
     * @return string list[].email - 邮箱 
     * @return string list[].phone_code - 国际电话区号 
     * @return string list[].phone - 手机号 
     * @return string list[].company - 公司 
     * @return int list[].order_id - 关联订单ID 
     * @return int list[].create_time - 交易时间
     * @return string list[].type - 订单类型new新订单renew续费订单upgrade升降级订单artificial人工订单
     * @return int list[].client_status - 用户是否启用0:禁用,1:正常
     * @return int list[].reg_time - 用户注册时间
     * @return string list[].country - 国家
     * @return string list[].address - 地址
     * @return string list[].language - 语言
     * @return string list[].notes - 备注
     * @return string list[].transaction_notes - 交易流水备注
     * @return array list[].hosts - 产品
     * @return int list[].hosts[].id - 产品ID
     * @return string list[].hosts[].name - 商品名称
     * @return array list[].descriptions - 描述
     * @return bool list[].certification - 是否实名认证true是false否(显示字段有certification返回)
     * @return string list[].certification_type - 实名类型person个人company企业(显示字段有certification返回)
     * @return string list[].client_level - 用户等级(显示字段有client_level返回)
     * @return string list[].client_level_color - 用户等级颜色(显示字段有client_level返回)
     * @return string list[].sale - 销售(显示字段有sale返回)
     * @return string list[].addon_client_custom_field_[id] - 用户自定义字段(显示字段有addon_client_custom_field_[id]返回,[id]为用户自定义字段ID)
     * @return int count - 交易流水总数
     * @return string total_amount - 总金额
     * @return string page_total_amount - 当前页总金额
     */
	public function transactionList()
    {
		// 合并分页参数
        $param = array_merge($this->request->param(), ['page' => $this->request->page, 'limit' => $this->request->limit, 'sort' => $this->request->sort]);
        
        // 实例化模型类
        $TransactionModel = new TransactionModel();

        // 获取交易流水列表
        $data = $TransactionModel->transactionList($param);

        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data
        ];
        return json($result);
	}

    /**
     * 时间 2022-05-17
     * @title 新增交易流水
     * @desc 新增交易流水
     * @author theworld
     * @version v1
     * @url /admin/v1/transaction
     * @method  POST
     * @param float amount - 金额 required
     * @param string gateway - 支付方式 required
     * @param string transaction_number - 交易流水号
     * @param int client_id - 用户ID required
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
        $TransactionModel = new TransactionModel();
        
        // 新建流水
        $result = $TransactionModel->createTransaction($param);

        return json($result);
	}

    /**
     * 时间 2022-10-12
     * @title 编辑交易流水
     * @desc 编辑交易流水
     * @author theworld
     * @version v1
     * @url /admin/v1/transaction/:id
     * @method  PUT
     * @param int id - 交易流水ID required
     * @param float amount - 金额 required
     * @param string gateway - 支付方式 required
     * @param string transaction_number - 交易流水号
     * @param int client_id - 用户ID required
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
        $TransactionModel = new TransactionModel();
        
        // 编辑交易流水
        $result = $TransactionModel->updateTransaction($param);

        return json($result);
    }

    /**
     * 时间 2022-05-17
     * @title 删除交易流水
     * @desc 删除交易流水
     * @author theworld
     * @version v1
     * @url /admin/v1/transaction/:id
     * @method  DELETE
     * @param int id - 交易流水ID required
     */
	public function delete()
    {
		// 接收参数
        $param = $this->request->param();

        // 实例化模型类
        $TransactionModel = new TransactionModel();
        
        // 删除流水
        $result = $TransactionModel->deleteTransaction($param['id']);

        return json($result);
	}
}