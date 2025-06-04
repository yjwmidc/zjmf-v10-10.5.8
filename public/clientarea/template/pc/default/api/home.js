// 会员中心首页
function indexData() {
  return Axios.get(`/index`);
}

//会员中心首页产品列表
function indexHost(params) {
  return Axios.get(`/index/host`, { params });
}
// 获取实名认证信息
function certificationInfo() {
  return Axios.get(`/certification/info`);
}
//工单列表
function ticket_list(params) {
  return Axios.get(`/ticket`, { params });
}
//会员中心首页新闻列表
function newsList(params) {
  return Axios.get(`/news/index`, { params });
}
// 推广者统计信息
function promoter_statistic() {
  return Axios.get(`/referral/promoter/statistic`);
}

// 开启推介计划
function openRecommend() {
  return Axios.post(`recommend/promoter`);
}
// 推广者基础信息
function promoterInfo() {
  return Axios.get(`/recommend/promoter`);
}

// 获取微信公众号用户关联信息
function getWxInfo() {
  return Axios.get(`/mp_weixin_notice/client`);
}
// 获取二维码
function getWxQrcode() {
  return Axios.get(`/mp_weixin_notice/qrcode`);
}

// 授信详情
function creditDetail() {
  return Axios.get(`/credit_limit`);
}
