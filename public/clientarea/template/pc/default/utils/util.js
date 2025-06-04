//获取cookie、
function getCookie(name) {
  let arr,
    reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
  if ((arr = document.cookie.match(reg))) return arr[2];
  else return null;
}

//设置cookie
function setCookie(c_name, value, expiredays = 1) {
  let exdate = new Date();
  exdate.setDate(exdate.getDate() + expiredays);
  document.cookie =
    c_name +
    "=" +
    escape(value) +
    (expiredays == null ? "" : ";expires=" + exdate.toGMTString());
}

//删除cookie
function delCookie(name) {
  let exp = new Date();
  exp.setTime(exp.getTime() - 1);
  let cval = getCookie(name);
  if (cval != null)
    document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
}

// 时间戳转换
/**
 * @param  timestamp 时间戳
 * @param  format 格式 YYYY-MM-DD HH:mm
 * @returns YY-MM-DD hh:mm
 */
function formateDate(time, format = "YYYY-MM-DD HH:mm") {
  const date = new Date(time);
  Y = date.getFullYear() + "-";
  M =
    (date.getMonth() + 1 < 10
      ? "0" + (date.getMonth() + 1)
      : date.getMonth() + 1) + "-";
  D = (date.getDate() < 10 ? "0" + date.getDate() : date.getDate()) + " ";
  h = (date.getHours() < 10 ? "0" + date.getHours() : date.getHours()) + ":";
  m = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
  s = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
  if (format === "YYYY-MM-DD HH:mm") {
    return Y + M + D + h + m;
  } else if (format === "YYYY-MM-DD HH:mm:ss") {
    return Y + M + D + h + m + ":" + s;
  } else if (format === "YYYY-MM-DD") {
    return Y + M + D;
  }
}
/**
 * @param  timestamp 时间戳
 * @returns YY.MM.DD
 */
function formateDate1(time) {
  const date = new Date(time);
  Y = date.getFullYear() + ".";
  M =
    (date.getMonth() + 1 < 10
      ? "0" + (date.getMonth() + 1)
      : date.getMonth() + 1) + ".";
  D = (date.getDate() < 10 ? "0" + date.getDate() : date.getDate()) + " ";
  h = (date.getHours() < 10 ? "0" + date.getHours() : date.getHours()) + ":";
  m = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
  return Y + M + D;
}
/**
 * @param  timestamp 时间戳
 * @returns YY年MM月DD日
 */
function formateDate2(time) {
  const date = new Date(time);
  Y = date.getFullYear() + "年";
  M =
    (date.getMonth() + 1 < 10
      ? "0" + (date.getMonth() + 1)
      : date.getMonth() + 1) + "月";
  D = (date.getDate() < 10 ? "0" + date.getDate() : date.getDate()) + "日";
  return Y + M + D;
}
/**
 * 生成密码字符串
 * 33~47：!~/
 * 48~57：0~9
 * 58~64：:~@
 * 65~90：A~Z
 * 91~96：[~`
 * 97~122：a~z
 * 123~127：{~
 * @param length 长度  生成的长度是length
 * @param hasNum 是否包含数字 1-包含 0-不包含
 * @param hasChar 是否包含字母 1-包含 0-不包含
 * @param hasSymbol 是否包含其他符号 1-包含 0-不包含
 * @param caseSense 是否大小写敏感 1-敏感 0-不敏感
 * @param lowerCase 是否只需要小写，只有当hasChar为0且caseSense为1时起作用 1-全部小写 0-全部大写
 */

function genEnCode(length, hasNum, hasChar, hasSymbol, caseSense, lowerCase) {
  let m = "";
  if (hasNum == 0 && hasChar == 0 && hasSymbol == 0) return m;
  for (let i = length; i >= 0; i--) {
    let num = Math.floor(Math.random() * 94 + 33);
    if (
      (hasNum == 0 && num >= 48 && num <= 57) ||
      (hasChar == 0 &&
        ((num >= 65 && num <= 90) || (num >= 97 && num <= 122))) ||
      (hasSymbol == 0 &&
        ((num >= 33 && num <= 47) ||
          (num >= 58 && num <= 64) ||
          (num >= 91 && num <= 96) ||
          (num >= 123 && num <= 127)))
    ) {
      i++;
      continue;
    }
    m += String.fromCharCode(num);
  }
  if (caseSense == "0") {
    m = lowerCase == "0" ? m.toUpperCase() : m.toLowerCase();
  }
  return m;
}

/**
 *
 * @param {Number} n 返回n个随机字母字符串
 * @returns
 */
function randomCoding(n) {
  //创建26个字母数组
  const arr = [
    "A",
    "B",
    "C",
    "D",
    "E",
    "F",
    "G",
    "H",
    "I",
    "J",
    "K",
    "L",
    "M",
    "N",
    "O",
    "P",
    "Q",
    "R",
    "S",
    "T",
    "U",
    "V",
    "W",
    "X",
    "Y",
    "Z",
  ];
  let idvalue = "";
  for (let i = 0; i < n; i++) {
    idvalue += arr[Math.floor(Math.random() * 26)];
  }
  return idvalue;
}

/**
 * 防抖函数
 * @param {Function} func 需要防抖的目标函数
 * @param {number} wait 延迟时间（毫秒）
 * @param {boolean} [immediate=false] 是否立即执行（true 表示第一次触发立即执行）
 * @returns {Function} 返回防抖处理后的函数
 */
function debounce(func, wait = 500, immediate = false) {
  let timeoutId;
  let result;
  const debounced = function (...args) {
    const context = this;
    // 清除现有定时器
    if (timeoutId) clearTimeout(timeoutId);

    // 立即执行模式
    if (immediate) {
      const callNow = !timeoutId;
      timeoutId = setTimeout(() => {
        timeoutId = null;
      }, wait);
      if (callNow) result = func.apply(context, args);
    }
    // 非立即执行模式
    else {
      timeoutId = setTimeout(() => {
        func.apply(context, args);
      }, wait);
    }

    return result;
  };

  // 添加取消方法
  debounced.cancel = function () {
    clearTimeout(timeoutId);
    timeoutId = null;
  };

  return debounced;
}

/**
 *
 * @param num 需要处理的三分数字
 * @param fixed 保留小数位数
 * @param separator 货币分隔符
 * @returns 1,000.00
 */
function formatMoneyNumber(num, fixed = 2, separator = ",") {
  // 判断数字是否为 null 或非数字类型，并默认设置为 0.00
  if (num == null || isNaN(num)) {
    num = 0.0;
  }

  // 将数字转换为字符串，并将负号单独提取出来
  let str = String(Math.abs(Number(num)).toFixed(fixed));
  let [integer, decimal] = str.split(".");

  // 在整数部分添加千位分隔符
  let result = "";
  while (integer.length > 3) {
    result = separator + integer.slice(-3) + result;
    integer = integer.slice(0, -3);
  }
  result = integer + result;

  // 如果原始数字是负数，则在最终结果中添加负号
  if (num < 0) {
    result = "-" + result;
  }

  // 如果有小数部分，则在最终结果中添加小数点和小数部分
  if (decimal != null) {
    result += `.${decimal}`;
  }
  return result;
}

function formatNuberFiexd(num, fixed = 2) {
  // 判断数字是否为 null 或非数字类型，并默认设置为 0.00
  if (num == null || isNaN(num)) {
    num = 0.0;
  }
  return Number(num).toFixed(fixed);
}
/**
 * 判断是否有某个插件
 * @param pluginName 插件名称 string
 * @returns {Boolean} 返回布尔值
 */
function havePlugin(pluginName) {
  const addonsDom = document.querySelector("#addons_js");
  let addonsArr = [];
  let arr = [];
  if (addonsDom) {
    addonsArr = JSON.parse(addonsDom.getAttribute("addons_js")); // 插件列表
    arr = addonsArr.map((item) => {
      return item.name;
    });
  }
  return arr.includes(pluginName);
}
/**
 * 根据插件名称获取插件Id
 * @param pluginName 插件名称 string
 * @returns { String | Number }  id
 */
function getPluginId(pluginName) {
  const addonsDom = document.querySelector("#addons_js") || [];
  if (addonsDom) {
    const addonsArr = JSON.parse(addonsDom.getAttribute("addons_js")); // 插件列表
    for (let index = 0; index < addonsArr.length; index++) {
      const element = addonsArr[index];
      if (pluginName === element.name) {
        return element.id;
      }
    }
  } else {
    console.log("请检查页面是否有插件dom");
  }
}

/**
 * 获取当前会员中心主题
 * @returns { String }
 */
function getClientareaTheme() {
  // 放在html上的clientarea_theme
  return document.documentElement.getAttribute("clientarea_theme") || "default";
}

/**
 * 获取当前购物车主题
 * @returns { String }
 */
function getCartTheme() {
  // 放在html上的clientarea_theme
  return document.documentElement.getAttribute("cart_theme") || "default";
}

/**
 * 获取url参数
 * @returns { Object } url地址参数
 */
function getUrlParams() {
  const url = window.location.href;
  // 判断是否有参数
  if (url.indexOf("?") === -1) {
    return {};
  }
  const params = url.split("?")[1];
  const paramsArr = params.split("&");
  const paramsObj = {};
  paramsArr.forEach((item) => {
    const key = item.split("=")[0];
    const value = item.split("=")[1];
    // 解析中文
    paramsObj[key] = decodeURIComponent(value);
  });
  return paramsObj;
}

/**
 * 导出EXCEL工具函数
 * @params result axios请求返回的结果
 * @returns { Promise }
 */
function exportExcelFun(res) {
  return new Promise((resolve, reject) => {
    try {
      const fileName = res.headers["content-disposition"].split("filename=")[1];
      const blob = new Blob([res.data], {type: res.headers["content-type"]});
      const downloadElement = document.createElement("a");
      const href = window.URL.createObjectURL(blob); //创建下载的链接
      downloadElement.href = href;
      downloadElement.download = fileName; //下载后文件名
      document.body.appendChild(downloadElement);
      downloadElement.click(); //点击下载
      document.body.removeChild(downloadElement); //下载完成移除元素
      window.URL.revokeObjectURL(href); //释放掉blob对象
      resolve();
    } catch (err) {
      console.log(err);
      reject(err);
    }
  });
}

/**
 * 用于检测插件多语言是否完整
 * @param { Object } plugin_lang
 * @returns // 控制台打印结果
 */
function checkLangFun(plugin_lang) {
  // 判断 plugin_lang 对象中的各个语言包数量是否一致
  const arr1 = Object.keys(plugin_lang["zh-cn"]);
  const arr2 = Object.keys(plugin_lang["en-us"]);
  const arr3 = Object.keys(plugin_lang["zh-hk"]);
  if (
    arr1.length !== arr2.length ||
    arr1.length !== arr3.length ||
    arr2.length !== arr3.length
  ) {
    // 找出哪个语言包的哪个变量丢失了
    const totalObj = Object.assign(
      {},
      plugin_lang["zh-cn"],
      plugin_lang["en-us"],
      plugin_lang["zh-hk"]
    );
    Object.keys(totalObj).forEach((item) => {
      if (!arr1.includes(item)) {
        console.log("zh-cn缺少", item);
      }
      if (!arr2.includes(item)) {
        console.log("en-us缺少", item);
      }
      if (!arr3.includes(item)) {
        console.log("zh-hk缺少", item);
      }
    });
  }
}

/**
 * 获取浏览器语言
 * @returns {string}
 */
function getBrowserLanguage() {
  if (!sessionStorage.getItem("brow_lang")) {
    let langType = "zh-cn";
    const lang =
      navigator.language ||
      navigator.browserLanguage ||
      navigator.systemLanguage ||
      navigator.userLanguage;
    if (lang.indexOf("zh-cn") !== -1 || lang.indexOf("zh-CN") !== -1) {
      langType = "zh-cn";
    } else if (
      lang.indexOf("zh-tw") !== -1 ||
      lang.indexOf("zh-TW") !== -1 ||
      lang.indexOf("zh-hk") !== -1 ||
      lang.indexOf("zh-HK") !== -1
    ) {
      langType = "zh-hk";
    } else {
      langType = "en-us";
    }
    sessionStorage.setItem("brow_lang", langType);
  }
  return sessionStorage.getItem("brow_lang");
}

/**
 * 复制文本工具函数
 * @param {string} text
 * @returns {void}
 */
function copyText(text) {
  // 检查浏览器是否支持 clipboard API
  if (navigator.clipboard) {
    navigator.clipboard
      .writeText(text)
      .then(() => {
        Vue.prototype.$message.success(lang.pay_text17);
      })
      .catch((err) => {
        console.error("文本复制失败:", err);
      });
  } else {
    // 动态创建 textarea 标签
    const textarea = document.createElement("textarea");
    // 将该 textarea 设为 readonly 防止 iOS 下自动唤起键盘，同时将 textarea 移出可视区域
    textarea.readOnly = "readonly";
    textarea.style.position = "absolute";
    textarea.style.top = "0px";
    textarea.style.left = "-9999px";
    textarea.style.zIndex = "-9999";
    // 将要 copy 的值赋给 textarea 标签的 value 属性
    textarea.value = text;
    // 将 textarea 插入到 el 中
    document.body.appendChild(textarea);
    // 兼容IOS 没有 select() 方法
    if (textarea.createTextRange) {
      textarea.select(); // 选中值并复制
    } else {
      textarea.setSelectionRange(0, text.length);
      textarea.focus();
    }
    const result = document.execCommand("Copy");
    if (result) {
      Vue.prototype.$message.success(lang.pay_text17);
    }
    document.body.removeChild(textarea);
  }
}
