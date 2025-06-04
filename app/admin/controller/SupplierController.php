<?php
namespace app\admin\controller;

use app\common\model\SupplierModel;
use app\admin\validate\SupplierValidate;

/**
 * @title 上下游供应商(后台)
 * @desc 上下游供应商(后台)
 * @use app\admin\controller\SupplierController
 */
class SupplierController extends AdminBaseController
{
    public function initialize()
    {
        parent::initialize();
        $this->validate = new SupplierValidate();
    }

    /**
     * 时间 2023-02-13
     * @title 供应商列表
     * @desc 供应商列表
     * @author theworld
     * @version v1
     * @url /admin/v1/supplier
     * @method  GET
     * @param string keywords - 关键字,搜索范围:供应商名称,链接地址
     * @param int page - 页数
     * @param int limit - 每页条数
     * @param string orderby - 排序 id
     * @param string sort - 升/降序 asc,desc
     * @return array list - 供应商
     * @return int list[].id - 供应商ID 
     * @return string list[].type - 供应商类型default默认业务系统whmcs财务系统finance魔方财务
     * @return string list[].name - 供应商名称 
     * @return string list[].url - 链接地址 
     * @return string list[].currency_name - 货币名称
     * @return string list[].currency_code - 货币标识 
     * @return string list[].rate - 汇率 
     * @return int list[].auto_update_rate - 自动更新汇率0关闭1开启 
     * @return int list[].rate_update_time - 汇率更新时间 
     * @return int list[].host_num - 产品数量 
     * @return int list[].product_num - 商品数量
     * @return string list[].credit - 上游账户余额,空字符串标识未获取到
     * @return int count - 供应商总数
     */
    public function list()
    {
    	// 合并分页参数
        $param = array_merge($this->request->param(), ['page' => $this->request->page, 'limit' => $this->request->limit, 'sort' => $this->request->sort]);

        // 实例化模型类
        $SupplierModel = new SupplierModel();

        // 获取供应商列表
        $data = $SupplierModel->supplierList($param);

        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data
        ];
        return json($result);
    }

    /**
     * 时间 2023-02-13
     * @title 供应商详情
     * @desc 供应商详情
     * @author theworld
     * @version v1
     * @url /admin/v1/supplier/:id
     * @method  GET
     * @param int id - 供应商ID required
     * @return object supplier - 供应商
     * @return int supplier.id - 供应商ID 
     * @return string supplier.type - 供应商类型default默认业务系统whmcs财务系统finance魔方财务
     * @return string supplier.name - 名称 
     * @return string supplier.url - 链接地址 
     * @return string supplier.username - 用户名 
     * @return string supplier.token - API密钥 
     * @return string supplier.secret - API私钥 
     * @return string supplier.contact - 联系方式 
     * @return string supplier.notes - 备注
     * @return string supplier.currency_code - 货币标识 
     * @return string supplier.rate - 汇率 
     * @return int supplier.auto_update_rate - 自动更新汇率0关闭1开启 
     * @return int supplier.rate_update_time - 汇率更新时间 
     */
    public function index()
    {
        // 接收参数
        $param = $this->request->param();
        
        // 实例化模型类
        $SupplierModel = new SupplierModel();

        // 获取供应商
        $supplier = $SupplierModel->indexSupplier($param['id']);

        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => [
                'supplier' => $supplier
            ]
        ];
        return json($result);
    }

    /**
     * 时间 2023-02-13
     * @title 添加供应商
     * @desc 添加供应商
     * @author theworld
     * @version v1
     * @url /admin/v1/supplier
     * @method  POST
     * @param string type - 供应商类型default默认业务系统whmcs财务系统finance魔方财务 required
     * @param string name - 名称 required
     * @param string url - 链接地址 required
     * @param string username - 用户名 required
     * @param string token - API密钥 required
     * @param string secret - API私钥 required
     * @param string contact - 联系方式
     * @param string notes - 备注
     */
    public function create()
    {
        // 接收参数
        $param = $this->request->param();
        $param['type'] = $param['type'] ?? 'default';

        // 参数验证
        if (!$this->validate->scene('create')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        // 实例化模型类
        $SupplierModel = new SupplierModel();
        
        // 新建供应商
        $result = $SupplierModel->createSupplier($param);

        return json($result);
    }

    /**
     * 时间 2023-02-13
     * @title 编辑供应商
     * @desc 编辑供应商
     * @author theworld
     * @version v1
     * @url /admin/v1/supplier/:id
     * @method  PUT
     * @param int id - 供应商ID required
     * @param string type - 供应商类型default默认业务系统whmcs财务系统finance魔方财务 required
     * @param string name - 名称 required
     * @param string url - 链接地址 required
     * @param string username - 用户名 required
     * @param string token - API密钥 required
     * @param string secret - API私钥 required
     * @param string contact - 联系方式
     * @param string notes - 备注
     */
    public function update()
    {
        // 接收参数
        $param = $this->request->param();
        $param['type'] = $param['type'] ?? 'default';

        // 参数验证
        if (!$this->validate->scene('update')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        // 实例化模型类
        $SupplierModel = new SupplierModel();
        
        // 修改供应商
        $result = $SupplierModel->updateSupplier($param);

        return json($result);
    }

    /**
     * 时间 2024-05-07
     * @title 编辑兑换汇率
     * @desc 编辑兑换汇率
     * @author theworld
     * @version v1
     * @url /admin/v1/supplier/:id/rate
     * @method  PUT
     * @param int id - 供应商ID required
     * @param int auto_update_rate - 自动更新汇率0关闭1开启 required
     * @param float rate - 汇率
     */
    public function updateSupplierRate()
    {
        // 接收参数
        $param = $this->request->param();
        
        // 参数验证
        if (!$this->validate->scene('rate')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        // 实例化模型类
        $SupplierModel = new SupplierModel();
        
        // 修改供应商
        $result = $SupplierModel->updateSupplierRate($param);

        return json($result);
    }

    /**
     * 时间 2023-02-13
     * @title 删除供应商
     * @desc 删除供应商
     * @author theworld
     * @version v1
     * @url /admin/v1/supplier/:id
     * @method  DELETE
     * @param int id - 供应商ID required
     */
    public function delete()
    {
        // 接收参数
        $param = $this->request->param();

        // 实例化模型类
        $SupplierModel = new SupplierModel();
        
        // 删除供应商
        $result = $SupplierModel->deleteSupplier($param['id']);

        return json($result);

    }

    /**
     * 时间 2023-02-13
     * @title 检查供应商接口连接状态
     * @desc 检查供应商接口连接状态
     * @author theworld
     * @version v1
     * @url /admin/v1/supplier/:id/status
     * @method  GET
     * @param int id - 供应商ID required
     */
    public function status()
    {
        // 接收参数
        $param = $this->request->param();

        // 实例化模型类
        $SupplierModel = new SupplierModel();
        
        // 检查供应商接口连接状态
        $result = $SupplierModel->supplierStatus($param['id']);

        return json($result);

    }

    /**
     * 时间 2023-02-13
     * @title 获取供应商商品列表
     * @desc 获取供应商商品列表
     * @author theworld
     * @version v1
     * @url /admin/v1/supplier/:id/product
     * @method  GET
     * @param int id - 供应商ID required
     * @return array list - 商品列表
     * @return int list[].id - 商品ID 
     * @return string list[].name - 商品名
     * @return string list[].description - 描述
     * @return string list[].price - 商品最低价格
     * @return string list[].cycle - 商品最低周期
     */
    public function product()
    {
        // 接收参数
        $param = $this->request->param();

        // 实例化模型类
        $SupplierModel = new SupplierModel();
        
        // 获取供应商商品列表
        $data = $SupplierModel->supplierProduct($param['id']);

        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data
        ];
        return json($result);

    }

    /**
     * 时间 2025-01-17
     * @title 刷新供应商余额
     * @desc  刷新供应商余额
     * @author hh
     * @version v1
     * @url /admin/v1/supplier/:id/credit
     * @method  GET
     * @param  int id - 供应商ID required
     * @return string credit - 余额,非数字代表没获取到
     */
    public function supplierCredit()
    {
        // 接收参数
        $param = $this->request->param();

        $SupplierModel = new SupplierModel();
        
        $result = $SupplierModel->supplierCredit($param['id']);

        return json($result);
    }


}