// 插件列表
function getAddonList(params) {
  return Axios.get(`/plugin/addon`, { params });
}
// 启用禁用插件
function changeAddonStatus(params) {
  return Axios.put(`/plugin/addon/${params.name}/${params.status}`, params);
}
// 安装/卸载插件
function deleteMoudle(type, params) {
  if (type === "install") {
    return Axios.post(`/plugin/addon/${params.name}`);
  } else if (type === 'uninstall') {
    return Axios.delete(`/plugin/addon/${params.name}`, {
      data: params
    });
  }
}
// 获取导航
function getMenus() {
  return Axios.get("/menu");
}
// 获取已购买应用最新版本
function getActiveVersion() {
  return Axios.get("/app_market/app/version");
}
// 插件升级
function upgradePlugin(params) {
  return Axios.post(`/plugin/${params.module}/${params.name}/upgrade`);
}

// 获取系统版本
function getSysyemVersion() {
  return Axios.get("/system/version");
}

// 同步插件
function syncPlugins() {
  return Axios.get("/plugin/sync");
}

// 下载插件
function downloadPlugin(id) {
  return Axios.get(`/plugin/${id}/download`);
}

/* hook */
function getHookPlugin() {
  return Axios.get("/plugin/hook");
}
function changeHookOrder(params) {
  return Axios.put("/plugin/hook/order", params);
}

function getAppConfig() {
  return Axios.get(`/app_market/configuration`);
}
