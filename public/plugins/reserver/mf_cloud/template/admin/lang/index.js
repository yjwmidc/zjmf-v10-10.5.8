(function () {
  /* mf_cloud */
  const module_lang = {
    "zh-cn": {
      cloud_on: "开机",
      cloud_off: "关机",
      cloud_suspend: "暂停",
      cloud_operating: "操作中",
      cloud_fault: "故障",
    },
    "zh-hk": {
      cloud_on: "開機",
      cloud_off: "關機",
      cloud_suspend: "暫停",
      cloud_operating: "操作中",
      cloud_fault: "故障",
    },
    "en-us": {
      cloud_on: "on",
      cloud_off: "off",
      cloud_suspend: "suspend",
      cloud_operating: "operating",
      cloud_fault: "fault",
    },
  };
  const DEFAULT_LANG = localStorage.getItem("backLang") || "zh-cn";
  window.module_lang = module_lang[DEFAULT_LANG];
})();
