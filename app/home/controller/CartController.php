<?php
namespace app\home\controller;

use app\home\model\CartModel;
use app\home\validate\CartValidate;

/**
 * @title 购物车管理
 * @desc 购物车管理
 * @use app\home\controller\CartController
 */
class CartController extends HomeBaseController
{
	public function initialize()
    {
        parent::initialize();
        $this->validate = new CartValidate();
    }

	/**
     * 时间 2022-05-30
     * @title 获取购物车
     * @desc 获取购物车
     * @author theworld
     * @version v1
     * @url /console/v1/cart
     * @method  GET
     * @return  array list - 计算后数据
     * @return  int list[].product_id - 商品ID
     * @return  object list[].config_options - 自定义配置
     * @return  int list[].qty - 数量
     * @return  object list[].customfield - 自定义参数
     * @return  string list[].name - 商品名称
     * @return  string list[].description - 商品描述
     * @return  int list[].stock_control - 库存控制0:关闭1:启用
     * @return  int list[].stock_qty - 库存数量
     * @return  object list[].self_defined_field - 自定义字段({"5":"123"},5是自定义字段ID,123是填写的内容)
     */
	public function index()
	{
		$result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => (new CartModel())->indexCart()
        ];
        return json($result);
	}

	/**
     * 时间 2022-05-30
     * @title 加入购物车
     * @desc 加入购物车
     * @author theworld
     * @version v1
     * @url /console/v1/cart
     * @method  POST
     * @param  int product_id - 商品ID required
     * @param  object config_options - 自定义配置
     * @param  int qty - 数量 required
     * @param  object customfield - 自定义参数
     * @param  object self_defined_field - 自定义字段({"5":"123"},5是自定义字段ID,123是填写的内容)
     * @param  array products - 商品 批量加入购物车必传
     * @param  int products[].product_id - 商品ID
     * @param  object products[].config_options - 自定义配置
     * @param  int products[].qty - 数量
     * @param  object products[].customfield - 自定义参数
     * @param  object products[].self_defined_field - 自定义字段({"5":"123"},5是自定义字段ID,123是填写的内容)
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
        $CartModel = new CartModel();
        
        // 加入购物车
        $result = $CartModel->createCart($param);

        return json($result);
	}

	/**
     * 时间 2022-05-30
     * @title 编辑购物车商品
     * @desc 编辑购物车商品
     * @author theworld
     * @version v1
     * @url /console/v1/cart/:position
     * @method  PUT
     * @param  int position - 位置 required
     * @param  int product_id - 商品ID required
     * @param  object config_options - 自定义配置
     * @param  int qty - 数量 required
     * @param  object customfield - 自定义参数
     * @param  object self_defined_field - 自定义字段({"5":"123"},5是自定义字段ID,123是填写的内容)
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
        $CartModel = new CartModel();
        
        // 编辑购物车商品
        $result = $CartModel->updateCart($param);

        return json($result);
	}

	/**
     * 时间 2022-05-30
     * @title 修改购物车商品数量
     * @desc 修改购物车商品数量
     * @author theworld
     * @version v1
     * @url /console/v1/cart/:position/qty
     * @method  PUT
     * @param  int position - 位置 required
     * @param  int qty - 数量 required
     */
	public function updateQty()
	{
		// 接收参数
		$param = $this->request->param();

        // 参数验证
        if (!$this->validate->scene('update_qty')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

		// 实例化模型类
        $CartModel = new CartModel();
        
        // 编辑购物车商品数量
        $result = $CartModel->updateCartQty($param);

        return json($result);
	}

	/**
     * 时间 2022-05-30
     * @title 删除购物车商品
     * @desc 删除购物车商品
     * @author theworld
     * @version v1
     * @url /console/v1/cart/:position
     * @method  DELETE
     * @param  int position - 位置 required
     */
	public function delete()
	{
		// 接收参数
		$param = $this->request->param();

        // 参数验证
        if (!$this->validate->scene('delete')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

		// 实例化模型类
        $CartModel = new CartModel();
        
        // 删除购物车商品
        $result = $CartModel->deleteCart($param['position']);

        return json($result);
	}

    /**
     * 时间 2022-05-30
     * @title 批量删除购物车商品
     * @desc 批量删除购物车商品
     * @author theworld
     * @version v1
     * @url /console/v1/cart/batch
     * @method  DELETE
     * @param  array positions - 位置 required
     */
    public function batchDelete()
    {
        // 接收参数
        $param = $this->request->param();

        // 参数验证
        if (!$this->validate->scene('batch_delete')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        // 实例化模型类
        $CartModel = new CartModel();
        
        // 删除购物车商品
        $result = $CartModel->batchDeleteCart($param['positions']);

        return json($result);
    }

	/**
     * 时间 2022-05-30
     * @title 清空购物车
     * @desc 清空购物车
     * @author theworld
     * @version v1
     * @url /console/v1/cart
     * @method  DELETE
     */
	public function clear()
	{
	    $param = $this->request->param();
		// 实例化模型类
        $CartModel = new CartModel();
        
        // 清空购物车
        $result = $CartModel->clearCart($param);

        return json($result);
	}

	/**
     * 时间 2022-05-31
     * @title 结算购物车
     * @desc 结算购物车
     * @author theworld
     * @version v1
     * @url /console/v1/cart/settle
     * @method  POST
     * @param  array positions - 商品位置数组 required
     * @param  object customfield - 自定义参数,比如优惠码参数传:{"promo_code":["pr8nRQOGbmv5"]}
     * @param  int downstream_host_id - 下游产品ID
     * @param  string downstream_url - 下游地址
     * @param  string downstream_token - 下游产品token
     * @param  string downstream_system_type - 下游系统类型
     * @return int order_id - 订单ID
     */
	public function settle()
	{
		// 接收参数
		$param = $this->request->param();

		// 参数验证
        if (!$this->validate->scene('settle')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

		// 实例化模型类
        $CartModel = new CartModel();
        
        // 结算购物车
        $result = $CartModel->settle($param['positions'],$param['customfield']??[],$param);

        return json($result);
	}
}