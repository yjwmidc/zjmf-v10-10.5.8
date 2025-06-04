/* 通用接口API */

// 获取国家列表
function getCountry(params) {
  return Axios.get(`/country`, params);
}
// 获取支付接口
function getPayList() {
  return Axios.get("/gateway");
}
// 获取公共配置
function getCommon(params) {
  return Axios.get("/common", {params});
}
// 获取登录信息
function getLoginInfo() {
  return Axios.get("/login");
}
// 获取图形验证码
function getNewCaptcha() {
  return Axios.get("/captcha");
}

// 编辑账户
function updateAccount(params) {
  return Axios.put(`/account`, params);
}
// 验证图形验证码
function checkCaptcha(params) {
  return Axios.post("/captcha", params);
}

// 注册
function regist(params) {
  return Axios.post("/register", params);
}

// 注册页面获取用户自定义字段
function registCustomField() {
  return Axios.get("/register/custom_field");
}

// 登录
function logIn(params) {
  return Axios.post("/login", params);
}

// 忘记密码
function forgetPass(params) {
  return Axios.post("/account/password_reset", params);
}

// 退出登录
function logout() {
  return Axios.post("/logout");
}

// 获取权限
function getAuthRole() {
  return Axios.get("/auth");
}
// 发送短信验证码
function phoneCode(params) {
  return Axios.post("/phone/code", params);
}

// 获取邮箱验证码
function emailCode(params) {
  return Axios.post("/email/code", params);
}

//  全局搜索
function globalSearch(params) {
  return Axios.get("/global_search", {params});
}

// 获取前台导航
function getMenu() {
  return Axios.get("/menu");
}

/* 停用相关 */
// 获取停用页面
function refundPage(params) {
  return Axios.get(`/refund`, {params});
}
// 申请停用
function refund(params) {
  return Axios.post(`/refund`, params);
}
// 取消停用
function cancel(params) {
  return Axios.put(`/refund/${params.id}/cancel`, params);
}
// 获取产品停用信息
function refundMsg(params) {
  return Axios.get(`/refund/host/${params.id}/refund`, {params});
}

// 账户详情
function account() {
  return Axios.get(`/account`);
}
// 支付方式
function gatewayList() {
  return Axios.get(`/gateway`);
}
// 支付
function pay(params) {
  return Axios.post(`/pay`, params);
}
// 支付状态
function getPayStatus(id) {
  return Axios.get(`/pay/${id}/status`);
}

// 使用/取消余额
function creditPay(params) {
  return Axios.post(`/credit`, params);
}
// 订单详情
function orderDetails(id) {
  return Axios.get(`/order/${id}`);
}
/* 续费相关 */
// 续费页面
function renewPage(params) {
  return Axios.get(`/host/${params.id}/renew`, {params});
}
// 续费提交
function renew(params) {
  return Axios.post(`/host/${params.id}/renew`, params);
}
// 商品列表
function productList(params) {
  return Axios.get(`/product`, {params});
}
// 商品详情
function productInfo(id) {
  return Axios.get(`/product/${id}`);
}
// 获取购物车
function cartList() {
  return Axios.get(`/cart`);
}

// 会员中心首页
function indexData() {
  return Axios.get(`/index`);
}

// 应用优惠码
function applyPromoCode(params) {
  return Axios.post(`/promo_code/apply`, params);
}
// 获取可使用代金券
function enableList(params) {
  return Axios.get(`/voucher/enable`, {params});
}
// 获取提现设置
function withdrawConfig() {
  return Axios.get(`/withdraw/rule/credit`);
}
// 申请提现
function applyWithdraw(params) {
  return Axios.post(`/withdraw`, params);
}

// 账户详情
function accountDetail() {
  return Axios.get(`/account`);
}

// 账户权限
function accountPermissions(id) {
  return Axios.get(`/sub_account/${id}/auth`);
}

/**
 * @获取在线客服代码
 */
function queryCustomerServiceCode(id) {
  return Axios.get(`/online_service`);
}

// 获取实名认证信息
function certificationInfo() {
  return Axios.get(`/certification/info`);
}

// 帮助文档详情
function helpDetails(params) {
  return Axios.get(`/help/${params.id}`, {params});
}

// 获取返现信息
function getCashbackInfo(params) {
  return Axios.get(`/host/${params.id}/product_cashback`);
}
// 申请返现
function apllyCashback(params) {
  return Axios.post(`/host/${params.id}/product_cashback`);
}

// 授信详情
function creditDetail() {
  return Axios.get(`/credit_limit`);
}

// 信用额支付
function payCreditLimit(params) {
  return Axios.post(`/credit_limit/pay`, params);
}

// 流量包列表
function getFlowPacket(params) {
  return Axios.get(`/host/${params.id}/flow_packet`, {params});
}

// 订购流量包
function buyFlowPacket(params) {
  return Axios.post(`/host/${params.id}/flow_packet_order`, params);
}

// 站内信
function messageInfo() {
  return Axios.get(`/client_care/mail`);
}

// 站内信列表
function messageList(params) {
  return Axios.get(`/client_care/mail/list`, {params});
}

// 删除站内信
function deleteMessage(params) {
  return Axios.delete(`/client_care/mail`, {params});
}

// 标记已读站内信
function readMessage(params) {
  return Axios.put(`/client_care/mail/read`, params);
}

// 验证oauthtoken
function oauthToken() {
  return Axios.get(`/oauth/token`);
}

// 关联账户

function bindOauthAccount(params) {
  return Axios.post(`/oauth/client/bind`, params);
}

//跳转到登录授权网址
function oauthUrl(name) {
  return Axios.get(`/oauth/${name}`);
}

// 获取商品活动促销信息
function eventPromotion(params) {
  return Axios.get(`/event_promotion/product/${params.id}/event_promotion`, {
    params,
  });
}

// 应用活动促销

function applyEventPromotion(params) {
  return Axios.post(`/event_promotion/apply`, params);
}

// 商品订单页自定义字段
function customFieldsProduct(id) {
  return Axios.get(`/product/${id}/self_defined_field/order_page`);
}

// 批量续费页面
function batchRenewList(params) {
  return Axios.get(`/host/renew/batch`, {params});
}

// 批量续费
function aipBatchRenew(params) {
  return Axios.post(`/host/renew/batch`, params);
}

// 产品IP详情
function getHostIpDetails(id) {
  return Axios.get(`/host/${id}/ip`);
}

// 批量操作
function batchOperation(module, params) {
  return Axios.post(`/${module}/batch_operate`, params);
}

// 银行转账提交申请
function submitApplication(id) {
  return Axios.post(`/order/${id}/submit_application`);
}
// 上传凭证
function uploadProof(params) {
  return Axios.put(`/order/${params.id}/voucher`, params);
}
// 变更支付方式
function changePayType(id) {
  return Axios.put(`/order/${id}/gateway`);
}

// 产品是否正在转移
function hostIsTransfer(params) {
  return Axios.get(`/host_transfer/host/${params.id}/is_transfer`);
}

// 自动续费开关
function rennewAuto(params) {
  return Axios.put(`/host/${params.id}/renew/auto`, params);
}
// 产品列表获取产品详情-用于自动续费显示
function getHostSpecific(params) {
  return Axios.get(`/host/${params.id}/specific_info`);
}

// 获取升级防御配置
function apiGetUpDefenceConfig(params, module) {
  return Axios.get(`/${module}/${params.id}/upgrade_defence_config`, {
    params,
  });
}

// 计算升级防御价格
function apiCalculateUpDefencePrice(params, module) {
  return Axios.get(`/${module}/${params.id}/upgrade_defence/price`, {
    params,
  });
}

// 生成升级防御订单
function apiGenerateUpDefenceOrder(params, module) {
  return Axios.post(`/${module}/${params.id}/upgrade_defence/order`, params);
}

// 防火墙用户统计(首页产品列表)
function apiProductGetHostIp(params, module) {
  switch (module) {
    case "mf_cloud":
    case "mf_dcim":
    case "mf_dcim_cabinet":
      return Axios.get(`/aodun_firewall/host_ip`, {params});
    case "remf_cloud":
    case "remf_dcim":
      return Axios.get(`/aodun_firewall_agent/host_ip`, {params});
  }
}

// 防火墙用户统计刷新状态(首页产品列表)
function apiProductRefreshHostIpStatus(params, module) {
  switch (module) {
    case "mf_cloud":
    case "mf_dcim":
    case "mf_dcim_cabinet":
      return Axios.get(`/aodun_firewall/host_ip/${params.id}/refresh_status`, {
        params,
      });
    case "remf_cloud":
    case "remf_dcim":
      return Axios.get(
        `/aodun_firewall_agent/host_ip/${params.id}/refresh_status`,
        {
          params,
        }
      );
  }
}
