/* 用户管理 + 业务管理 API */

// 用户管理-用户列表
function getClientList(params, id) {
  if (id) {
    return Axios.get(`/client?custom_field[IdcsmartClientLevel_level]=${id}`, {
      params,
    });
  } else {
    return Axios.get(`/client`, {params});
  }
}
// 用户管理-添加用户
function addClient(params) {
  return Axios.post(`/client`, params);
}
// 用户管理-切换状态
function changeOpen(id, params) {
  return Axios.put(`/client/${id}/status`, params);
}
// 用户管理-修改资料
function updateClient(id, params) {
  return Axios.put(`/client/${id}`, params);
}
// 用户管理-删除用户
function deleteClient(params) {
  return Axios.delete(`/client/${params.id}`, {params});
}
// 用户管理-用户详情
function getClientDetail(id) {
  return Axios.get(`/client/${id}`);
}
// 以用户登录
function loginByUserId(id) {
  return Axios.post(`/client/${id}/login`);
}
// 获取用户退款
function getRefund(id) {
  return Axios.get(`/refund/client/${id}/amount`);
}
// 用户余额管理-用户余额变更记录列表
function getMoneyDetail(id, params) {
  return Axios.get(`/client/${id}/credit`, {params});
}

// 用户余额管理-更改用户余额
function updateClientDetail(id, params) {
  return Axios.put(`/client/${id}/credit`, params);
}

// 用户信息-产品列表
function getClientPro(id, params) {
  return Axios.get(`/host?client_id=${id}`, {params});
}
// 用户信息-订单管理
function getClientOrder(id) {
  return Axios.get(`/order?client_id=${id}`);
}
// 用户信息-交易流水
function getClientOrder(params) {
  return Axios.get(`/transaction`, {params});
}
// 产品管理-删除流水
function deleteFlow(id) {
  return Axios.delete(`/transaction/${id}`);
}
// 产品管理-新增/编辑流水
function addAndUpdateFlow(type, params) {
  if (type === "add") {
    return Axios.post(`/transaction`, params);
  } else if (type === "update") {
    return Axios.put(`/transaction/${params.id}`, params);
  }
}
// 用户信息-日志
function getLog(id, params) {
  return Axios.get(`/log/system?client_id=${id}`, {params});
}

// 产品管理-删除产品
function deletePro(params) {
  return Axios.delete(`/host/${params.id}`, {
    data: params,
  });
}

/* 业务管理相关API */

// 订单管理-订单列表
function getOrder(params) {
  return Axios.get("/order", {params});
}
// 订单管理-新建订单
function createOrder(params) {
  return Axios.post("/order", params);
}

// 订单管理-订单详情
function getOrderDetail(id) {
  return Axios.get(`/order/${id}`);
}

// 订单管理-调整订单金额
function updateOrder(params) {
  return Axios.put(`/order/${params.id}/amount`, params);
}
// 订单管理-编辑人工调整的订单子项
function updateArtificialOrder(params) {
  return Axios.put(`/order/item/${params.id}`, params);
}
// 订单管理-删除订单
function delOrderDetail(params) {
  return Axios.delete(`/order/${params.id}`, {params});
}
// 订单管理-标记支付
function signPayOrder(params) {
  return Axios.put(`/order/${params.id}/status/paid`, params);
}

// 获取商品一级分组
function getFirstGroup() {
  return Axios.get(`/product/group/first`);
}
// 获取商品一级分组
function getSecondGroup() {
  return Axios.get(`/product/group/second`);
}

// 获取商品列表
function getProList(params) {
  return Axios.get(`/product`, {params});
}
// 获取产品列表
function getShopList(params) {
  return Axios.get(`/host`, {params});
}
// 获取产品相关的可升降级的商品
function getRelationList(id) {
  return Axios.get(`/product/${id}/upgrade`);
}

// 获取商品配置项参数
function getProConfig(params) {
  return Axios.get(`product/${params.id}/config_option`, {params});
}
// 根据商品配置请求价格
function getProPrice(params) {
  return Axios.post(`/product/${params.id}/config_option`, params);
}

// 获取产品详情
function getProductDetail(id) {
  return Axios.get(`/host/${id}`);
}
// 修改产品
function updateProduct(id, params) {
  return Axios.put(`/host/${id}`, params);
}
// 接口
function getInterface(params) {
  return Axios.get("/server", {params});
}
// 获取升降级订单金额
function getUpgradeAmount(params) {
  return Axios.post("/order/upgrade/amount", params);
}
// 产品模块
function getproModule(id) {
  return Axios.get(`/host/${id}/module`);
}
// 续费页面
function getSingleRenew(id) {
  return Axios.get(`/host/${id}/renew`);
}
// 续费
function postSingleRenew(params) {
  return Axios.post(`/host/${params.id}/renew`, params);
}

// 批量续费页面
function getRenewBatch(params) {
  return Axios.get(`/host/renew/batch`, {params});
}
// 批量续费
function postRenewBatch(params) {
  return Axios.post(`/host/renew/batch`, params);
}
// 系统设置
function getSystemOpt() {
  return Axios.get("/configuration/system");
}

// 充值
function recharge(params) {
  return Axios.post(`/client/${params.client_id}/recharge`, params);
}

// 获取用户等级
function getClientLevel(id) {
  return Axios.get(`/client_level/client/${id}`);
}
function updateClientLevel(params) {
  return Axios.put(`/client_level/client`, params);
}
// 所有用户等级
function getAllLevel() {
  return Axios.get(`/client_level/all`);
}

// 插件列表
function getAddon(params) {
  return Axios.get(`/active_plugin`, {params});
}
// 	产品优惠码使用记录
function proPromoRecord(params) {
  return Axios.get(`/promo_code/host/${params.id}/log`, {params});
}
/**
 * @获取子账户对应主账户
 * @param string
 */
function getAdminAccountApi(params) {
  return Axios.get(`/sub_account/parent`, {params});
}

/**
 * @获取子账户列表
 * @param string
 */
function getchildAccountListApi(params) {
  return Axios.get(`/sub_account`, {params});
}

/* 1-7新增产品手动开通等 */
// 模块开通
function createModule(params) {
  return Axios.post(`/host/${params.id}/module/create`, params);
}
function suspendModule(params) {
  return Axios.post(`/host/${params.id}/module/suspend`, params);
}
function unsuspendModule(params) {
  return Axios.post(`/host/${params.id}/module/unsuspend`, params);
}
function delModule(params) {
  return Axios.post(`/host/${params.id}/module/terminate`, params);
}

// 批量删除产品
function deleteHost(params) {
  return Axios.delete(`/host`, {params});
}

/* 2023-1-30新增订单详情 */
// 订单详情
function getOrderDetails(params) {
  return Axios.get(`/order/${params.id}`);
}
// 订单退款
function orderRefund(params) {
  return Axios.post(`/order/${params.id}/refund`, params);
}
// 订单退款记录列表
function getOrderRefundRecord(params) {
  return Axios.get(`/order/${params.id}/refund_record`, {params});
}
// 删除退款记录
function delOrderRecord(params) {
  return Axios.delete(`/refund_record/${params.id}`);
}
// 订单应用余额
function orderApplyCredit(params) {
  return Axios.post(`/order/${params.id}/apply_credit`, params);
}
// 订单扣除余额
function orderRemoveCredit(params) {
  return Axios.post(`/order/${params.id}/remove_credit`, params);
}
// 修改订单支付方式
function changePayway(params) {
  return Axios.put(`/order/${params.id}/gateway`, params);
}
// 修改订单备注
function changeOrderNotes(params) {
  return Axios.put(`/order/${params.id}/notes`, params);
}
// 订单管理-删除人工调整的订单子项
function delArtificialOrder(params) {
  return Axios.delete(`/order/item/${params.id}`, params);
}

// 1-31 模块按钮输出
function getMoudleBtns(params) {
  return Axios.get(`/host/${params.id}/module/button`);
}

//产品详情
function upHostDetail(id) {
  return Axios.get(`/upstream/host/${id}`);
}

/* 个人资料-信息记录 */

function getRecordList(params) {
  return Axios.get(`/client/${params.id}/record`, {params});
}
function addAndUpdateRecord(type, params) {
  if (type === "add") {
    return Axios.post(`/client/${params.id}/record`, params);
  } else if (type === "update") {
    return Axios.put(`/client/record/${params.id}`, params);
  }
}
function deleteRecord(params) {
  return Axios.delete(`/client/record/${params.id}`);
}

// 获取支付接口
function getPayList() {
  return Axios.get("/gateway");
}

// 获取用户自定义字段和值
function clientCustomDetail(id) {
  return Axios.get(`/client/${id}/client_custom_field_value`);
}

// 获取商品下拉优化插件配置
function getSelectConfig() {
  return Axios.get(`/product_drop_down_select/config`);
}

// 产品内页模块输入框输出
function hostField(id) {
  return Axios.get(`/host/${id}/module/field`);
}

// 批量删除订单
function batchDelOrder(params) {
  return Axios.delete(`/order`, {params});
}

// 后台生成新订单
function settleOrder(params) {
  return Axios.post(`/product/settle`, params);
}

/* 手动资源包 */
function getManualResource(params) {
  return Axios.get(`/manual_resource`, {params});
}
// 分配
function changeResource(type, params) {
  return Axios.put(`/manual_resource/${params.id}/${type}`, params);
}
// 获取电源状态
function getResourceStatus(params) {
  return Axios.get(`/manual_resource/${params.id}/status`);
}
// 供应商列表
function ApiSupplier(params) {
  return Axios.get(`/manual_resource/supplier`, {params});
}

// 获取产品转移信息
function getProductTransfer(id) {
  return Axios.get(`/host/${id}/host_transfer`);
}

// 产品转移

function transferProduct(params) {
  return Axios.post(`/host/${params.id}/host_transfer`, params);
}

// 查看所有IP
function getAllIp(params) {
  return Axios.get(`/host/${params.id}/ip`);
}

/* DCIM资源 */
function getDcimResource(params) {
  return Axios.get(`/mf_dcim/host/${params.id}/sales`, {params});
}
// 分配 type: assign | free
function changeDcimResource(type, params) {
  return Axios.post(`/mf_dcim/host/${params.id}/${type}`, params);
}

/* 机柜租用 */
function getCabinetRent(params) {
  return Axios.get(`/mf_dcim_cabinet/host/${params.id}/cabinet_rent`, {
    params,
  });
}
// 分配 type: assign | free
function changeCabinetResource(type, params) {
  return Axios.post(`/mf_dcim_cabinet/host/${params.id}/${type}`, params);
}

//支出列表
function costList(params) {
  return Axios.get(`/order/${params.id}/cost_pay`, {params});
}

// 拖动排序
function costDrag(params) {
  return Axios.put(`/cost_pay/self_defined_field/${params.id}/drag`, params);
}

//添加支出
function addCost(params) {
  return Axios.post(`/order/${params.id}/cost_pay`, params);
}

// 修改支出
function updateCost(params) {
  return Axios.put(`/cost_pay/${params.id}`, params);
}

//删除支出
function deleteCost(id) {
  return Axios.delete(`/cost_pay/${id}`);
}

// 支出详情
function costDetail(id) {
  return Axios.get(`/cost_pay/${id}`);
}

//成本支出自定义字段列表
function costCustomFieldList() {
  return Axios.get(`/cost_pay/self_defined_field`);
}

//添加成本支出自定义字段
function addCostCustomField(params) {
  return Axios.post(`/cost_pay/self_defined_field`, params);
}

// 修改成本支出自定义字段
function updateCostCustomField(params) {
  return Axios.put(`/cost_pay/self_defined_field/${params.id}`, params);
}

//删除成本支出自定义字段
function deleteCostCustomField(id) {
  return Axios.delete(`/cost_pay/self_defined_field/${id}`);
}

// 修改成本支出自定义字段列表展示
function changeCostCustomFieldShow(params) {
  return Axios.put(
    `/cost_pay/self_defined_field/${params.id}/show_list`,
    params
  );
}
/* 回收站 */
function getRecycleConfig() {
  return Axios.get(`/order/recycle_bin/config`);
}
function openRecycleConfig() {
  return Axios.post(`/order/recycle_bin/enable`);
}
function changeRecycleConfig(params) {
  return Axios.put(`/order/recycle_bin/config`, params);
}
function getRecycleList(params) {
  return Axios.get(`/order/recycle_bin`, {params});
}
function recoverRecycleList(params) {
  return Axios.post(`/order/recycle_bin/recover`, params);
}
function delRecycleList(params) {
  return Axios.delete(`/order/recycle_bin`, {data: params});
}
function clearRecycleList(params) {
  return Axios.post(`/order/recycle_bin/clear`, params);
}
function lockRecycleList(params) {
  return Axios.post(`/order/lock`, params);
}
function unlockRecycleList(params) {
  return Axios.post(`/order/unlock`, params);
}

// 退款通过
function refundPass(id) {
  return Axios.put(`/refund_record/${id}/pending`);
}

// 退款拒绝
function refundReject(params) {
  return Axios.put(`/refund_record/${params.id}/reject`, params);
}

// 已退款

function apiRefunded(params) {
  return Axios.put(`/refund_record/${params.id}/refunded`, params);
}

/* 视图字段 */
// 获取视图详情
function getViewFiled(params) {
  return Axios.get("/view", {params});
}
// 编辑视图字段
function saveViewFiled(params) {
  return Axios.put(`/view/${params.id}/field`, params);
}
// 获取视图可用数据范围
function getViewRange(params) {
  return Axios.get(`/view/data_range`, {params});
}
// 视图列表
function getViewList(params) {
  return Axios.get(`/view/list`, {params});
}
// 复制视图
function copyViewFiled(params) {
  return Axios.post(`/view/${params.id}/copy`, params);
}
// 新建/编辑视图
function addAndEditViewFiled(type, params) {
  if (type === "add") {
    return Axios.post(`/view`, params);
  } else if (type === "update") {
    return Axios.put(`/view/${params.id}`, params);
  }
}

// 删除视图
function deleteViewFiled(params) {
  return Axios.delete(`/view/${params.id}`, {params});
}
// 视图切换状态
function changeViewFiled(params) {
  return Axios.put(`/view/${params.id}/status`, params);
}
// 视图排序
function changeViewFiledOrder(params) {
  return Axios.put(`/view/order`, params);
}
// 指定视图
function specifyView(params) {
  return Axios.put(`/view/choose`, params);
}

// 后台餐票内页实例操作输出
function getMoudleOperate(params) {
  return Axios.get(`/host/${params.id}/module_operate`);
}

// 拉取信息
function syncInfo(id) {
  return Axios.get(`/host/${id}/module/sync`);
}

// 审核订单
function reviewOrder(params) {
  return Axios.post(`/order/${params.id}/review`, params);
}
// 上传凭证
function uploadProof(params) {
  return Axios.put(`/order/${params.id}/voucher`, params);
}

// 修改用户接收短信 | 邮件
function updateReceive(type, params) {
  return Axios.put(`/client/${params.id}/${type}`, params);
}

// 获取可购买流量包
function getUsableFlow(params) {
  return Axios.get(`/host/${params.id}/flow_packet`, {params});
}
// 购买流量包
function buyFlow(params) {
  return Axios.post(`/host/${params.id}/flow_packet_order`, params);
}

// 订单内页产品退款列表
function apiHostRefundList(params) {
  return Axios.get(`/order/${params.id}/host_refund`, {params});
}

// 计算订单可退金额
function apiOrderRefundAmount(params) {
  return Axios.get(`/order/${params.id}/refund_amount`, {params});
}

// 手动处理产品列表
function getFailAction(_, params) {
  return Axios.get(`/host/failed_action`, {params});
}
// 标记已处理
function markProcessed(params) {
  return Axios.post(`/host/${params.id}/mark_processed`, params);
}
// 模块列表
function getModuleData(params) {
  return Axios.get(`/module`, {params});
}
// 批量同步
function batchSyncHost(params) {
  return Axios.post(`/host/sync`, params);
}

// 获取支付接口列表
function apiPluginGatewayList(params) {
  return Axios.get(`/plugin/gateway`, {params});
}

// 获取防御列表
function apiDefenceList(params) {
  return Axios.get(`/aodun_firewall/host_ip`, {params});
}
// 防火墙用户统计
function apiAngetDefenceList(params) {
  return Axios.get(`/aodun_firewall_agent/host_ip`, {params});
}
// 获取订单开票状态
function getOrderInvoiceStatus(params) {
  return Axios.get(`/invoice/order/${params.id}/status`);
}
