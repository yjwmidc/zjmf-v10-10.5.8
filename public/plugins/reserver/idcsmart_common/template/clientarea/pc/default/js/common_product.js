const template = document.getElementsByClassName("common-config")[0];
Vue.prototype.lang = Object.assign(window.lang, window.module_lang);
new Vue({
  components: {
    asideMenu,
    topMenu,
    payDialog,
    discountCode,
    eventCode,
    customGoods,
  },
  created() {
    if (window.performance.navigation.type === 2) {
      sessionStorage.removeItem("product_information");
    }
    let temp = {};
    const params = getUrlParams();
    this.id = params.id;
    if (params.config || sessionStorage.getItem("product_information")) {
      try {
        temp = JSON.parse(params.config);
        this.isUpdate = true;
        this.isConfig = true;
      } catch (e) {
        temp = JSON.parse(sessionStorage.getItem("product_information")) || {};
        this.isUpdate = params.change;
      }
    }

    // 回显配置
    if (temp && temp.config_options) {
      this.backfill = temp.config_options;
      this.configForm.config_options = temp.config_options;
      this.customfield = temp.customfield;
      this.self_defined_field = temp.self_defined_field || {};
      this.cycle = temp.config_options.cycle;
      this.orderData.qty = temp.qty;
      this.position = temp.position;
    }
    this.getCommonData();
    this.getGoodsName();
    this.getCountryList();
  },
  mounted() {
    this.addons_js_arr = JSON.parse(
      document.querySelector("#addons_js").getAttribute("addons_js")
    ); // 插件列表
    const arr = this.addons_js_arr.map((item) => {
      return item.name;
    });
    if (arr.includes("PromoCode")) {
      // 开启了优惠码插件
      this.isShowPromo = true;
    }
    if (arr.includes("IdcsmartClientLevel")) {
      // 开启了等级优惠
      this.isShowLevel = true;
    }
    if (arr.includes("EventPromotion")) {
      // 开启活动满减
      this.isShowFull = true;
    }
    if (arr.includes("EventPromotion")) {
      // 开启活动满减
      this.isShowFull = true;
    }

    this.getConfig();
    window.addEventListener("message", (event) => this.buyNow(event));
  },
  updated() {
    // 关闭loading
    document.getElementById("mainLoading").style.display = "none";
    document.getElementsByClassName("template")[0].style.display = "block";
    this.isShowBtn = true;
  },
  destroyed() {},
  computed: {
    calStr() {
      const temp = this.basicInfo.order_page_description
        ?.replace(/&lt;/g, "<")
        .replace(/&gt;/g, ">")
        .replace(/&quot;/g, '"')
        .replace(/&/g, "&")
        .replace(/"/g, '"')
        .replace(/'/g, "'");
      return temp;
    },
    calcDes() {
      return (val) => {
        const temp = val
          .replace(/&lt;/g, "<")
          .replace(/&gt;/g, ">")
          .replace(/&quot;/g, '"')
          .replace(/&/g, "&")
          .replace(/"/g, '"')
          .replace(/'/g, "'");
        return temp;
      };
    },
    calcSwitch() {
      return (item) => {
        return item.subs.map((el) => el.id).sort((a, b) => a - b);
      };
    },
    calcCountry() {
      return (val) => {
        return this.countryList.filter((item) => val === item.iso)[0]?.name_zh;
      };
    },
    calcCity() {
      return (id) => {
        return this.filterCountry[id].filter(
          (item) => item[0]?.country === this.curCountry[id]
        )[0];
      };
    },
  },
  data() {
    return {
      id: "",
      goodsInfo: {},
      position: "",
      addons_js_arr: [], // 插件数组
      isShowPromo: false, // 是否开启优惠码
      isShowLevel: false, // 是否开启等级优惠
      isShowFull: false,
      is_show_custom: false,
      isUpdate: false,
      isConfig: false,
      isUseDiscountCode: false, // 是否使用优惠码
      backfill: {}, // 回填参数
      customfield: {}, // 自定义字段
      self_defined_field: {}, // 自定义字段
      submitLoading: false,
      totalPrice: 0,
      commonData: {},
      eventData: {
        id: "",
        discount: 0,
      },
      // 订单数据
      orderData: {
        qty: 1,
        // 是否勾选阅读
        isRead: false,
        // 付款周期
        duration: "",
      },
      // 右侧展示区域
      showInfo: [],
      base_price: "",
      // 商品原单价
      onePrice: 0,
      // 商品原总价
      original_price: 0,

      timerId: null, // 订单id
      basicInfo: {}, // 基础信息
      configoptions: [], // 配置项
      custom_cycles: [], // 自定义周期
      curCycle: 0,
      cycle: "",
      onetime: "",
      pay_type: "",
      // 提交数据
      configForm: {
        // 自定义配置项
      },
      isShowBtn: false,
      // 国家列表
      countryList: [],
      // 处理过后的国家列表
      filterCountry: {},
      curCountry: {}, // 当前国家，根据配置id存入对应的初始索引
      cartDialog: false,
      dataLoading: false,
      selectOsObj: {},
      // 客户等级折扣金额
      clDiscount: 0,
      // 优惠码折扣金额
      code_discount: 0,
      osSelectData: [],
      osGroupId: "",
      osIcon: "",
      handNum: 0,
    };
  },
  filters: {
    formateTime(time) {
      if (time && time !== 0) {
        return formateDate(time * 1000);
      } else {
        return "--";
      }
    },
    filterMoney(money) {
      if (isNaN(money) || money * 1 < 0) {
        return "0.00";
      } else {
        return formatNuberFiexd(money);
      }
    },
  },
  methods: {
    getGoodsName() {
      productInfo(this.id).then((res) => {
        this.goodsInfo = res.data.data.product;
        document.title =
          this.commonData.website_name + "-" + res.data.data.product.name;
      });
    },
    // 解析url
    getQuery(url) {
      const str = url.substr(url.indexOf("?") + 1);
      const arr = str.split("&");
      const res = {};
      for (let i = 0; i < arr.length; i++) {
        const item = arr[i].split("=");
        res[item[0]] = item[1];
      }
      return res;
    },
    async getCountryList() {
      try {
        const res = await getCountry();
        this.countryList = res.data.data.list;
      } catch (error) {}
    },
    // 镜像分组改变时
    osSelectGroupChange(e, item) {
      const obj = item.subs.filter((sub) => sub.os === this.osGroupId)[0];
      this.osSelectData = obj.version;
      this.osIcon =
        "/plugins/reserver/idcsmart_common/template/clientarea/pc/default/img/idcsmart_common/" +
        obj.os.toLowerCase() +
        ".svg";
      this.configForm[item.id] = obj.version[0].id;
      this.changeConfig();
    },
    eventChange(evetObj) {
      if (this.eventData.id !== evetObj.id) {
        this.eventData.id = evetObj.id || "";
        this.customfield.event_promotion = this.eventData.id;
        this.changeConfig();
      }
    },
    // 镜像版本改变时
    osSelectChange() {
      this.changeConfig();
    },
    createArr([m, n], step) {
      // 每个阶段最小值开始计算，最小值可取
      let temp = [m];
      let cur = m;
      while (cur < n && cur + step <= n) {
        cur += step;
        temp.push(cur);
      }
      return temp;
    },
    async getConfig() {
      try {
        const res = await getCommonDetail(this.id);
        const temp = res.data.data;
        this.basicInfo = temp.common_product;
        this.configoptions = temp.configoptions
          .filter((item) => item.subs.length)
          .map((item) => {
            if (item.option_type === "quantity_range") {
              item.rangeOption = [];
              item.subs = item.subs.sort((a, b) => a.qty_min - b.qty_min);
              item.subs.forEach((sub) => {
                item.rangeOption.push(
                  ...this.createArr([sub.qty_min, sub.qty_max], item.qty_change)
                );
              });
            }
            return item;
          });
        this.custom_cycles = temp.custom_cycles;
        this.pay_type = temp.common_product.pay_type;
        this.onetime =
          temp.cycles.onetime === "-1.00" ? "0.00" : temp.cycles.onetime;
        // 初始化自定义配置参数
        const obj = this.configoptions.reduce((all, cur) => {
          all[cur.id] =
            cur.option_type === "multi_select" ||
            cur.option_type === "quantity" ||
            cur.option_type === "quantity_range"
              ? [
                  cur.option_type === "multi_select"
                    ? cur.subs[0].id
                    : cur.subs[0].qty_min,
                ]
              : cur.subs[0].id;
          // 区域的时候保存国家
          if (cur.option_type === "area") {
            this.filterCountry[cur.id] = this.toTree(cur.subs);
            this.$set(this.curCountry, cur.id, 0);
          }

          if (cur.option_type === "os") {
            this.osGroupId = cur.subs[0].os;
            this.osSelectData = cur.subs[0].version;
            all[cur.id] = cur.subs[0].version[0].id;
            this.osIcon =
              "/plugins/reserver/idcsmart_common/template/clientarea/pc/default/img/idcsmart_common/" +
              cur.subs[0].os.toLowerCase() +
              ".svg";
          }
          return all;
        }, {});
        this.configForm = obj;
        if (this.pay_type === "onetime") {
          this.cycle = "onetime";
        } else if (this.pay_type === "free") {
          this.cycle = "free";
        } else {
          this.cycle = temp.custom_cycles[0].id;
        }

        // this.changeConfig()
        this.changeConfig(this.backfill.cycle ? true : false);
      } catch (error) {}
    },

    // 数组转树
    toTree(data) {
      var temp = Object.values(
        data.reduce((res, item) => {
          res[item.country]
            ? res[item.country].push(item)
            : (res[item.country] = [item]);
          return res;
        }, {})
      );
      return temp;
    },
    // 切换配置选项
    changeItem() {
      this.changeConfig();
    },
    // 使用优惠码
    getDiscount(data) {
      this.customfield.promo_code = data[1];
      this.isUseDiscountCode = true;
      this.changeConfig();
    },
    // 更改配置计算价格
    async changeConfig(bol = false) {
      try {
        if (bol === true) {
          /* 处理 quantity quantity_range  */
          const _temp = this.backfill.configoption;
          Object.keys(_temp).forEach((item) => {
            const type = this.configoptions.filter((el) => el.id === item)[0]
              ?.option_type;
            if (type === "quantity" || type === "quantity_range") {
              _temp[item] = _temp[item][0];
            }
          });
          this.configForm = _temp;
          this.cycle = this.backfill.cycle;
          this.curCycle = this.custom_cycles.findIndex(
            (item) => item.id * 1 === this.cycle * 1
          );
        }
        const temp = this.formatData();

        const params = {
          id: this.id,
          config_options: {
            configoption: temp,
            cycle: this.cycle,
            promo_code: this.customfield.promo_code,
            event_promotion: this.customfield.event_promotion,
          },
          qty: this.orderData.qty,
        };
        this.dataLoading = true;
        const res = await calcPrice(params);

        this.original_price = res.data.data.price * 1;
        this.totalPrice = res.data.data.price_total * 1;
        this.clDiscount = res.data.data.price_client_level_discount * 1 || 0;
        this.code_discount = res.data.data.price_promo_code_discount * 1 || 0;
        this.eventData.discount =
          res.data.data.price_event_promotion_discount * 1 || 0;

        this.base_price = res.data.data.base_price;
        this.showInfo = res.data.data.preview;
        this.onePrice = res.data.data.price; // 原单价
        this.orderData.duration = res.data.data.duration;

        // 重新计算周期显示
        const result = await calculate(params);
        this.custom_cycles = result.data.data.custom_cycles;
        this.onetime = result.data.data.cycles.onetime;
        this.dataLoading = false;
      } catch (error) {
        this.dataLoading = false;
      }
    },
    removeDiscountCode() {
      this.isUseDiscountCode = false;
      this.customfield.promo_code = "";
      this.code_discount = 0;
      this.changeConfig();
    },
    // 切换国家
    changeCountry(id, index) {
      this.$set(this.curCountry, id, index);
      this.configForm[id] = this.filterCountry[id][index][0]?.id;
      this.changeConfig();
    },
    // 切换城市
    changeCity(el, id) {
      this.configForm[id] = el.id;
      this.changeConfig();
    },
    // 切换单击选择
    changeClick(id, el) {
      this.configForm[id] = el.id;
      this.changeConfig();
    },
    // 切换数量
    // changeNum(val, id) {
    //   this.configForm[id] = [val * 1];
    //   this.changeConfig();
    // },
    changeRange(num, item) {
      try {
        if (!item.rangeOption.includes(num)) {
          const val = item.rangeOption.reduce((prev, curr) =>
            Math.abs(curr - num) < Math.abs(prev - num) ? curr : prev
          );
          this.configForm[item.id] = [val * 1];
        }
        setTimeout(() => {
          this.handNum++;
          this.changeConfig();
        }, 0);
      } catch (error) {
        console.log("error", error);
      }
    },
    // 切换周期
    changeCycle(item, index) {
      this.cycle = item.id;
      this.curCycle = index;
      this.changeConfig();
    },
    // 商品购买数量减少
    delQty() {
      if (this.basicInfo.allow_qty === 0) {
        return false;
      }
      if (this.orderData.qty > 1) {
        this.orderData.qty--;
        this.changeConfig();
      }
    },
    // 商品购买数量增加
    addQty() {
      if (this.basicInfo.allow_qty === 0) {
        return false;
      }
      this.orderData.qty++;
      this.changeConfig();
    },

    formatData() {
      // 处理数量类型的转为数组
      const temp = JSON.parse(JSON.stringify(this.configForm));
      Object.keys(temp).forEach((el) => {
        const arr = this.configoptions.filter((item) => item.id * 1 === el * 1);
        if (
          arr[0].option_type === "quantity" ||
          arr[0].option_type === "quantity_range" ||
          arr[0].option_type === "multi_select"
        ) {
          if (typeof temp[el] !== "object") {
            temp[el] = [temp[el]];
          }
        }
      });
      return temp;
    },
    // 立即购买
    async buyNow(e) {
      if (e.data && e.data.type !== "iframeBuy") {
        return;
      }
      if (
        Boolean(
          (JSON.parse(localStorage.getItem("common_set_before")) || {})
            .custom_fields?.before_settle === 1
        )
      ) {
        window.open("/account.htm");
        return;
      }
      const flag = this.$refs.customGoodRef.getSelfDefinedField();
      if (!flag) return;
      const temp = this.formatData();
      const params = {
        product_id: this.id,
        config_options: {
          configoption: temp,
          cycle: this.cycle,
        },
        qty: this.orderData.qty,
        customfield: this.customfield,
        self_defined_field: this.self_defined_field,
      };
      // 直接传配置到结算页面
      if (e.data && e.data.type === "iframeBuy") {
        const postObj = { type: "iframeBuy", params, price: this.totalPrice };
        window.parent.postMessage(postObj, "*");
        return;
      }
      location.href = `/cart/settlement.htm?id=${params.product_id}`;
      sessionStorage.setItem("product_information", JSON.stringify(params));
    },
    // 加入购物车
    async addCart() {
      // if (!this.orderData.isRead) {
      //   this.$message.warning("请先阅读并勾选协议")
      //   return false
      // }
      try {
        const flag = this.$refs.customGoodRef.getSelfDefinedField();
        if (!flag) return;
        const temp = this.formatData();
        const params = {
          product_id: this.id,
          config_options: {
            configoption: temp,
            cycle: this.cycle,
          },
          qty: this.orderData.qty,
          customfield: this.customfield,
          self_defined_field: this.self_defined_field,
        };
        const res = await addToCart(params);
        if (res.data.status === 200) {
          this.cartDialog = true;
          const result = await getCart();
          localStorage.setItem(
            "cartNum",
            "cartNum-" + result.data.data.list.length
          );
        }
      } catch (error) {
        this.$message.error(error.data.msg);
      }
    },
    // 修改购物车
    async changeCart() {
      try {
        const flag = this.$refs.customGoodRef.getSelfDefinedField();
        if (!flag) return;
        const temp = this.formatData();
        const params = {
          position: this.position,
          product_id: this.id,
          config_options: {
            configoption: temp,
            cycle: this.cycle,
          },
          qty: this.orderData.qty,
          customfield: this.customfield,
          self_defined_field: this.self_defined_field,
        };
        this.dataLoading = true;
        const res = await updateCart(params);
        this.$message.success(res.data.msg);
        setTimeout(() => {
          location.href = "/cart/shoppingCar.htm";
        }, 300);
        this.dataLoading = false;
      } catch (error) {
        console.log("errore", error);
        this.$message.error(error.data.msg);
      }
    },
    goToCart() {
      location.href = "/cart/shoppingCar.htm";
      this.cartDialog = false;
    },
    // 支付成功回调
    paySuccess(e) {
      this.submitLoading = false;
      location.href = "common_product_list.htm";
    },
    // 取消支付回调
    payCancel(e) {
      this.submitLoading = false;
      location.href = "finance.htm";
    },
    // 获取通用配置
    getCommonData() {
      this.commonData = JSON.parse(localStorage.getItem("common_set_before"));

      document.title =
        this.commonData.website_name + "-" + lang.common_cloud_text109;
    },
  },
}).$mount(template);
