const {showToast, showNotify} = vant;
window.lang = Object.assign(window.lang, window.module_lang);
// 验证码通过
function captchaCheckSuccsss(bol, captcha, token) {
  if (bol) {
    // 验证码验证通过
    getData(captcha, token);
  }
}
// 取消验证码验证
function captchaCheckCancel() {
  captchaCancel();
}
const app2 = Vue.createApp({
  components: {
    topMenu,
    curSelect,
    countDownButton,
    payDialog,
    discountCode,
    cashBack,
    flowPacket,
    safeConfirm,
    captchaDialog,
    cashCoupon,
    hostStatus,
  },
  created() {
    // 获取产品id
    const params = getUrlParams();
    this.id = params.id * 1;
    // 获取通用信息
    this.getCommonData();
    // 获取产品详情
    this.getHostDetail();
    // 获取实例详情
    this.getCloudDetail();

    this.getCloudStatus();
  },
  mounted() {
    window.captchaCancel = this.captchaCancel;
    window.getData = this.getData;
    this.addons_js_arr = JSON.parse(
      document.querySelector("#addons_js").getAttribute("addons_js")
    ); // 插件列表
    const arr = this.addons_js_arr.map((item) => {
      return item.name;
    });
    this.addonsArr = arr;
    if (arr.includes("PromoCode")) {
      // 开启了优惠码插件
      this.isShowPromo = true;
      // 优惠码信息
      this.getPromoCode();
    }
    if (arr.includes("IdcsmartClientLevel")) {
      // 开启了等级优惠
      this.isShowLevel = true;
    }
    if (arr.includes("IdcsmartVoucher")) {
      // 开启了代金券
      this.isShowCash = true;
    }
    // 开启了插件才拉取接口
    // 退款相关
    if (arr.includes("IdcsmartRefund")) {
      // 开启了退款
      this.isRefundPlugin = true;
      this.getRefundMsg();
    }
    if (arr.includes("IdcsmartRenew")) {
      // 开启了续费
      this.isRenewPlugin = true;
      this.getRenewStatus();
    }
  },
  computed: {
    calcPower() {
      return (status) => {
        switch (status) {
          case "on":
            return "success";
          case "off":
            return "danger";
          case "fault":
            return "";
        }
      };
    },
    calcMin() {
      return (item) => {
        if (item.option_type === 14) {
          return item.qty;
        } else {
          return item.qty_minimum;
        }
      };
    },
    calcDisable() {
      // 处理数据盘升降级不能选择的情况
      return (el, item) => {
        // if (item.option_type === 13) {
        //   return el.firstname * 1 < item.options.filter( o => o.id === this.configForm[item.id])[0]?.firstname * 1
        // } else {
        //   return false
        // }
        return false;
      };
    },
    calcValue() {
      return (item) => {
        const numType = [4, 7, 9, 11, 14, 15, 16, 17, 18, 19];
        if (numType.includes(item.option_type)) {
          return item.qty;
        } else {
          return item.suboption_name;
        }
      };
    },
    calcUnit() {
      return (item) => {
        switch (item.option_type) {
          case 11:
          case 18:
            return "Mbps";
          case 4:
          case 15:
            return lang.mf_one;
          case 7:
          case 16:
            return lang.mf_cores;
          case 9:
          case 14:
          case 17:
          case 19:
            return "GB";
        }
      };
    },
    vpcIps() {
      if (
        this.vpc_ips.vpc2 !== undefined &&
        this.vpc_ips.vpc3 !== undefined &&
        this.vpc_ips.vpc4 !== undefined
      ) {
        const str =
          this.vpc_ips.vpc1.value +
          "." +
          this.vpc_ips.vpc2 +
          "." +
          this.vpc_ips.vpc3 +
          "." +
          this.vpc_ips.vpc4 +
          "/" +
          this.vpc_ips.vpc6.value;
        return str;
      } else {
        return "";
      }
    },
    calcCpu() {
      return this.params.cpu + lang.mf_cores;
    },
    calcCpuList() {
      // 根据区域来判断计算可选cpu数据
      if (this.activeName1 === "fast") {
        return;
      }
      const temp =
        this.configLimitList.filter(
          (item) =>
            item.type === "data_center" &&
            this.params.data_center_id === item.data_center_id
        ) || [];
      const cpu = temp.reduce((all, cur) => {
        all.push(...cur.cpu.split(","));
        return all;
      }, []);
      return this.cpuList.filter((item) => !cpu.includes(String(item.value)));
    },
    calaMemoryList() {
      // 计算可选内存，根据 cpu + 区域
      if (this.activeName1 === "fast") {
        return;
      }
      const temp = this.configLimitList.filter((item) =>
        item.cpu.split(",").includes(String(this.params.cpu))
      );
      if (temp.length === 0) {
        // 没有匹配到限制条件
        if (this.memoryList[0]?.type === "radio") {
          return this.memoryList;
        } else {
          this.memoryTip = this.createTip(this.memoryArr);
          this.memMarks = this.createMarks(this.memoryArr); // data 原数据，目标marks
          return this.memoryArr;
        }
      }
      // 分两种情况，单选和范围，单选：memory 范围，min_memory，max_memory
      if (temp[0].memory) {
        const memory = Array.from(
          new Set(
            temp.reduce((all, cur) => {
              all.push(...cur.memory.split(","));
              return all;
            }, [])
          )
        );
        const filMem = this.memoryList.filter(
          (item) => !memory.includes(String(item.value))
        );
        return filMem;
      } else {
        // 范围
        let fArr = [];
        temp.forEach((item) => {
          fArr.push(...this.createArr([item.min_memory, item.max_memory]));
        });
        fArr = Array.from(new Set(fArr));
        const filterArr = this.memoryArr.filter((item) => !fArr.includes(item));
        this.memoryTip = this.createTip(filterArr);
        this.memMarks = this.createMarks(filterArr); // data 原数据，目标marks
        return filterArr.filter((item) => !fArr.includes(item));
      }
    },
    calcPassword() {
      return (pas) => {
        return new Array(pas.length).fill("*").join("");
      };
    },
  },
  watch: {
    // 获取订购页磁盘的价格/扩容页磁盘的价格
    moreDiskData: {
      handler(newValue, oldValue) {
        if (this.isOrderOrExpan) {
          // 获取订购磁盘 总价格
          this.getOrderDiskPrice();
        } else {
          // 获取扩容磁盘弹窗 总价格
        }
      },
      deep: true,
    },
    oldDiskList: {
      handler(newValue, oldValue) {
        if (this.isOrderOrExpan) {
          // 获取订购磁盘 总价格
          this.getOrderDiskPrice();
        } else {
          // 获取扩容磁盘弹窗 总价格
          this.getExpanDiskPrice();
        }
      },
      deep: true,
    },
    vpcIps: {
      handler(newVal) {
        this.ips = newVal;
      },
      immediate: true,
      deep: true,
    },
    renewParams: {
      handler() {
        let n = 0;
        // l:当前周期的续费价格
        const l = this.hostData.renew_amount;
        if (this.isShowPromo && this.renewParams.customfield.promo_code) {
          // n: 算出来的价格
          n =
            (this.renewParams.base_price * 1000 -
              this.renewParams.clDiscount * 1000 -
              this.renewParams.code_discount * 1000) /
              1000 >
            0
              ? (this.renewParams.base_price * 1000 -
                  this.renewParams.clDiscount * 1000 -
                  this.renewParams.code_discount * 1000) /
                1000
              : 0;
        } else {
          //  n: 算出来的价格
          n =
            (this.renewParams.original_price * 1000 -
              this.renewParams.clDiscount * 1000 -
              this.renewParams.code_discount * 1000) /
              1000 >
            0
              ? (this.renewParams.original_price * 1000 -
                  this.renewParams.clDiscount * 1000 -
                  this.renewParams.code_discount * 1000) /
                1000
              : 0;
        }
        let t = n;
        // 如果当前周期和选择的周期相同，则和当前周期对比价格
        if (
          this.hostData.billing_cycle_time === this.renewParams.duration ||
          this.hostData.billing_cycle_name === this.renewParams.billing_cycle
        ) {
          // 谁大取谁
          t = n;
        }
        this.renewParams.totalPrice =
          t * 1000 - this.renewParams.cash_discount * 1000 > 0
            ? (
                (t * 1000 - this.renewParams.cash_discount * 1000) /
                1000
              ).toFixed(2)
            : 0;
      },
      immediate: true,
      deep: true,
    },
    // bsData: {
    //     handler(newValue, oldValue) {
    //         // 开启备份/快照的价格
    //         this.getBsPrice()
    //     },
    //     deep: true
    // }
  },
  data() {
    return {
      lang: window.lang,
      addonsArr: [],
      isRefundPlugin: false,
      isRenewPlugin: false,
      initLoading: true,
      commonData: {
        currency_prefix: "",
        currency_suffix: "",
      },
      activeName: "summarize",
      configLimitList: [], // 限制规则
      configObj: {},
      backup_config: [],
      snap_config: [],
      // 实例id
      id: null,
      // 产品id
      product_id: 0,
      // 实例状态
      status: "operating",
      // 实例状态描述
      statusText: "",
      cpu_realData: {},
      // 代金券对象
      cashObj: {},
      // 是否救援系统
      isRescue: false,
      // 是否开启代金券
      isShowCash: false,
      // 产品详情
      hostData: {
        billing_cycle_name: "",
        status: "Active",
        first_payment_amount: "",
        renew_amount: "",
      },
      // 实例详情
      cloudData: {
        data_center: {
          iso: "CN",
        },
        image: {
          icon: "",
        },
        package: {
          cpu: "",
          memory: "",
          out_bw: "",
          system_disk_size: "",
        },
        system_disk: {},
        iconName: "Windows",
      },
      // 是否显示支付信息
      isShowPayMsg: false,
      imgBaseUrl: "",
      // 是否显示添加备注弹窗
      isShowNotesDialog: false,
      // 备份输入框内容
      notesValue: "",
      // 显示重装系统弹窗
      isShowReinstallDialog: false,
      // 重装系统弹窗内容
      reinstallData: {
        image_id: null,
        password: null,
        ssh_key_id: null,
        port: null,
        osGroupId: null,
        osId: null,
        type: "pass",
      },
      // 镜像数据
      osData: [],
      timeOptions: [
        {value: "1", text: lang.common_cloud_label15},
        {value: "2", text: lang.common_cloud_label16},
        {value: "3", text: lang.common_cloud_label17},
      ],
      // 镜像版本选择框数据
      osSelectData: [],
      // 镜像图片地址
      osIcon: "",
      osName: "",
      // Shhkey列表
      sshKeyData: [],
      // 错误提示信息
      errText: "",
      // 镜像是否需要付费
      isPayImg: false,
      payMoney: 0,
      // 镜像优惠价格
      payDiscount: 0,
      // 镜像优惠码价格
      payCodePrice: 0,
      onOffvisible: false,
      rebotVisibel: false,
      showImgPick: false,
      isShowImage: false,
      codeString: "",
      isShowIp: false,
      renewLoading: false, // 续费计算折扣loading
      // 停用信息
      refundData: {},
      // 停用状态
      refundStatus: {
        Pending: lang.finance_text97,
        Suspending: lang.finance_text136,
        Suspend: lang.finance_text137,
        Suspended: lang.finance_text138,
        Refund: lang.finance_text139,
        Reject: lang.finance_text140,
        Cancelled: lang.finance_text141,
      },

      // 停用相关
      // 是否显示停用弹窗
      isShowRefund: false,
      // 停用页面信息
      refundPageData: {
        host: {
          create_time: 0,
          first_payment_amount: 0,
        },
        configs: [],
      },
      // 停用页面参数
      refundParams: {
        host_id: 0,
        suspend_reason: "",
        type: "Expire",
      },
      refundLoading: false,

      addons_js_arr: [], // 插件列表
      isShowPromo: false, // 是否开启优惠码
      isShowLevel: false, // 是否开启等级优惠
      // 续费
      // 显示续费弹窗
      isShowRenew: false, // 续费的总计loading
      renewBtnLoading: false, // 续费按钮的loading
      // 续费页面信息
      renewPageData: [],
      renewActiveId: 0,
      renewOrderId: 0,
      isShowRefund: false,
      hostStatus: {
        Unpaid: {
          text: lang.order_text4,
          color: "#F64E60",
          bgColor: "#FFE2E5",
        },
        Pending: {
          text: lang.finance_text88,
          color: "#3699FF",
          bgColor: "#E1F0FF",
        },
        Active: {
          text: lang.finance_text142,
          color: "#1BC5BD",
          bgColor: "#C9F7F5",
        },
        Suspended: {
          text: lang.finance_text143,
          color: "#F99600",
          bgColor: "#FFE2E5",
        },
        Deleted: {
          text: lang.finance_text144,
          color: "#9696A3",
          bgColor: "#F2F2F7",
        },
        Failed: {
          text: lang.finance_text88,
          color: "#FFA800",
          bgColor: "#FFF4DE",
        },
      },
      isRead: false,
      isShowPass: false,
      passHidenCode: "",
      rescueStatusData: {},
      consoleData: {},
      // 管理开始
      // 开关机状态
      powerStatus: "",
      powerList: [],
      consoleList: [],

      restartList: [
        {
          id: 3,
          label: lang.common_cloud_text13,
          value: "rebot",
        },
        {
          id: 4,
          label: lang.common_cloud_text41,
          value: "hardRebot",
        },
      ],
      powerStatusArr: {
        on: {
          text: lang.common_cloud_text10,
          icon: `/plugins/reserver/mf_finance_common/template/clientarea/mobile/mfm201/img/cloud/on.svg`,
        },
        off: {
          text: lang.common_cloud_text11,
          icon: `/plugins/reserver/mf_finance_common/template/clientarea/mobile/mfm201/img/cloud/off.svg`,
        },
        operating: {
          text: lang.common_cloud_text12,
          icon: `/plugins/reserver/mf_finance_common/template/clientarea/mobile/mfm201/img/cloud/operating.svg`,
        },
        fault: {
          text: lang.common_cloud_text86,
          icon: `/plugins/reserver/mf_finance_common/template/clientarea/mobile/mfm201/img/cloud/fault.svg`,
        },
        suspend: {
          text: lang.common_cloud_text87,
          icon: `/plugins/reserver/mf_finance_common/template/clientarea/mobile/mfm201/img/cloud/suspended.svg`,
        },
      },
      loading1: false,
      loading2: false,
      loading3: false,
      loading4: false,
      loading5: false,
      ipValueData: [],
      // 重置密码弹窗数据
      rePassData: {
        password: "",
        checked: false,
      },
      osOptions: [
        {
          text: "Windows",
          value: "1",
        },
        {
          text: "Linux",
          value: "2",
        },
      ],
      // 是否展示重置密码弹窗
      isShowRePass: false,
      // 救援模式弹窗数据
      rescueData: {
        type: "1",
        temp_pass: "",
      },
      // 是否展示救援模式弹窗
      isShowRescue: false,
      // 是否展示退出救援模式弹窗
      isShowQuit: false,
      ipValue: null,

      /* 升降级相关*/
      // 升降级套餐列表
      upgradeList: [],
      // 升降级表单
      upgradePackageId: "",
      // 当前切换的升降级套餐
      changeUpgradeData: {},
      // 是否展示升降级弹窗
      isShowUpgrade: false,
      // 升降级参数
      upParams: {
        customfield: {
          promo_code: "", // 优惠码
          voucher_get_id: "", // 代金券码
        },
        duration: "", // 周期
        isUseDiscountCode: false, // 是否使用优惠码
        clDiscount: 0, // 用户等级折扣价
        code_discount: 0, // 优惠码折扣价
        cash_discount: 0, // 代金券折扣价
        original_price: 0, // 原价
        totalPrice: 0, // 现价
      },

      // 续费参数
      renewParams: {
        id: 0, //默认选中的续费id
        isUseDiscountCode: false, // 是否使用优惠码
        customfield: {
          promo_code: "", // 优惠码
          voucher_get_id: "", // 代金券码
        },
        duration: "", // 周期
        billing_cycle: "", // 周期时间
        clDiscount: 0, // 用户等级折扣价
        cash_discount: 0, // 代金券折扣价
        code_discount: 0, // 优惠码折扣价
        original_price: 0, // 原价
        base_price: 0,
        totalPrice: 0, // 现价
      },

      // 磁盘 开始
      diskLoading: false,
      isSubmitEngine: false,
      // 实例磁盘列表
      // 过滤后
      diskList: [],
      // 未过滤
      allDiskList: [],
      // 订购/扩容标识
      isOrderOrExpan: true,
      // 订购磁盘参数
      orderDiskData: {
        id: 0,
        remove_disk_id: [],
        add_disk: [],
      },
      // 新增磁盘数据
      moreDiskData: [],
      // 订购磁盘弹窗相关
      isShowDg: false,
      // 其他配置信息
      configData: {},
      systemDiskList: [],
      dataDiskList: [],
      // 磁盘总价格
      moreDiskPrice: 0,
      // 磁盘优惠价格
      moreDiscountkDisPrice: 0,
      // 磁盘优惠码优惠价格
      moreCodePrice: 0,
      // 订购磁盘弹窗 中 当前配置磁盘
      oldDiskList: [],
      oldDiskList2: [],
      orderTimer: null,
      expanTimer: null,
      // 磁盘订单id
      diskOrderId: 0,
      // 订购/扩容标识
      isOrderOrExpan: true,
      // 是否显示扩容弹窗
      isShowExpansion: false,
      // 扩容磁盘参数
      expanOrderData: {
        id: 0,
        resize_data_disk: [],
      },
      // 扩容价格
      expansionDiskPrice: 0,
      // 扩容折扣
      expansionDiscount: 0,
      // 扩容优惠码优惠
      expansionCodePrice: 0,
      // 网络开始
      netLoading: false,
      netDataList: [],
      netParams: {
        page: 1,
        limit: 20,
        pageSizes: [20, 50, 100],
        total: 200,
        orderby: "id",
        sort: "desc",
        keywords: "",
      },
      // 网络流量
      flowData: {},
      // 日志开始
      logDataList: [],
      logParams: {
        page: 1,
        limit: 20,
        pageSizes: [20, 50, 100],
        total: 0,
        orderby: "id",
        sort: "desc",
        keywords: "",
      },
      logLoading: false,
      logFinished: false,
      // 备份与快照开始
      dataList1: [],
      // 备份列表数据
      dataList1: [],
      // 快照列表数据
      dataList2: [],
      self_defined_field: [],
      backLoading: false,
      snapLoading: false,
      backFinshed: false,
      snapFinshed: false,
      params1: {
        page: 1,
        limit: 20,
        pageSizes: [20, 50, 100],
        total: 200,
        orderby: "id",
        sort: "desc",
        keywords: "",
      },
      params2: {
        page: 1,
        limit: 20,
        pageSizes: [20, 50, 100],
        total: 200,
        orderby: "id",
        sort: "desc",
        keywords: "",
      },
      // true 标记为备份  false 标记为快照
      isBs: true,
      // 弹窗表单数据
      createBsData: {
        id: 0,
        name: "",
        disk_id: 0,
      },
      // 实例磁盘列表
      // 是否显示弹窗
      isShwoCreateBs: false,
      cgbsLoading: false,
      isShowhyBs: false,
      safeDialogShow: false,
      // 还原显示数据
      restoreData: {
        restoreId: 0,
        // 实例名称
        cloud_name: "",
        // 创建时间
        time: "",
      },
      // 是否显示删除快照弹窗
      isShowDelBs: false,
      // 删除显示数据
      delData: {
        delId: 0,
        // 实例名称
        cloud_name: "",
        // 创建时间
        time: "",
        // 快照名称
        name: "",
      },
      bsDataLoading: false,
      // 获取快照/备份升降级价格 参数 生成快照/备份数量升降级订单参数
      bsData: {
        id: 0,
        type: "",
        backNum: 0,
        snapNum: 0,
        money: 0,
        moneyDiscount: 0,
        codePrice: 0,
        duration: "月",
      },
      // 是否显示开启备份弹窗
      isShowOpenBs: false,
      countryName: "",
      areImg: "",
      // 快照备份订单id
      bsOrderId: 0,
      chartSelectValue: "1",
      // 统计图表开始
      echartLoading1: false,
      echartLoading2: false,
      client_operate_password: "",
      echartLoading3: false,
      loading6: false,
      echartLoading4: false,
      isShowPowerChange: false,
      powerType: "",
      powerTitle: "",
      diskPriceLoading: false,
      ipPriceLoading: false,
      ipMoney: 0.0,
      ipDiscountkDisPrice: 0.0,
      ipCodePrice: 0.0,
      upgradePriceLoading: false,
      trueDiskLength: 0,
      isShowAutoRenew: false,
      vpcDataList: [],
      vpcLoading: false,
      vpcFinished: false,
      vpcParams: {
        page: 1,
        limit: 20,
        pageSizes: [20, 50, 100],
        total: 200,
        orderby: "id",
        sort: "desc",
        keywords: "",
      },
      isShowengine: false,
      engineID: "",
      curEngineId: "",
      engineSearchLoading: false,
      imageName: "",
      productOptions: [],
      productParams: {
        page: 1,
        limit: 20,
        keywords: "",
        status: "Active",
        orderby: "id",
        sort: "desc",
        data_center_id: "",
      },
      isShowAddVpc: false,
      plan_way: 0,
      vpc_ips: {
        vpc1: {
          tips: lang.range1,
          value: 10,
          select: [10, 172, 192],
        },
        vpc2: 0,
        vpc3: 0,
        vpc3Tips: "",
        vpc4: 0,
        vpc4Tips: "",
        vpc6: {
          value: 16,
          select: [16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28],
        },
        min: 0,
        max: 255,
      },
      vpcName: "",
      ips: "",
      safeOptions: [],
      safeID: "",
      upData: {
        cpuName: "",
      },

      cpuName: "",
      memoryName: "",
      bwName: "",
      flowName: "",
      defenseName: "",
      memoryList: [],
      cpuList: [],
      memoryArr: [], // 范围时内存数组
      activeName1: "custom", // fast, custom
      memoryType: false,
      memoryTip: "",
      params: {
        // 配置参数
        data_center_id: "",
        cpu: "",
        memory: 1,
        image_id: 0,
        system_disk: {
          size: "",
          disk_type: "",
        },
        data_disk: [],
        backup_num: "",
        snap_num: "",
        line_id: "",
        bw: "",
        flow: "",
        peak_defence: "",
        ip_num: "",
        duration_id: "",
        network_type: "normal",
        // 提交购买
        name: "", // 主机名
        ssh_key_id: "",
        /* 安全组 */
        security_group_id: "",
        security_group_protocol: [],
        password: "",
        re_password: "",
        vpc: {
          // 新建-系统分配的时候都不传
          id: "", // 选择已有的vc
          ips: "", // 自定义的时候
        },
        notes: "",
      },
      lineDetail: {}, // 线路详情：bill_type, flow, bw, defence , ip
      /* 2023-3-30 cl */
      systemIcon: "",
      country_name: "",
      configoptions: [], // 配置项
      configForm: {}, // 自定义配置项
      configDetails: [], // 实例详情
      /* 升降级 */
      upgradeLoading: false,
      upLicenseDialogShow: false,
      selectUpIndex: 0,
      buy_id: "",
      upPriceLoading: false,
      licenseActive: "1",
      upData: {
        price: 0,
        clDiscount: 0,
        totalPrice: 0,
      },
      isShowUp: true,
      upBtnLoading: false,
      upgradeHost: {},
      upgradeConfig: [],
      upgradeSon_host: [],
      upgradeList: [],
      basicInfo: {},
      configForm: {},
      upSon: [],
      curCycle: 0,
      curCountry: {},
      firstInfo: [],
      filterCountry: {},
      isShowProUpdate: false, // 展示产品升级
      isShowOptionUpdate: false, // 展示配置升级
      updateProId: "", // 升级产品时新的商品id
      backupNum: 0,
      snapNum: 0,
      showSuspendOption: [
        {
          value: "Expire",
          text: lang.common_cloud_label41,
        },
        {
          value: "Immediate",
          text: lang.common_cloud_label42,
        },
      ],
      // filterCountry: [],
      /* 升降级 end */
      isShowPort: false,
      isShowChart: false,
      ipDetails: {
        dedicate_ip: "",
        assign_ip: "",
        ip_num: 0,
      },
      allIp: [],
      // 流量包
      showPackage: false,
      packageLoading: false,
      packageList: [],
      curPackageId: "",
      upShowImage: false,
      curOsItem: {},
      upImgPick: false,
      curSystem: "",
      calcOsImgList: [],
      systemArr: [],

      chartData: [],
      client_area: [],
      client_button: {},
      osData: {},
      selectOsObj: {},
      reinstallLoading: false,
    };
  },

  methods: {
    async stop_use() {
      this.refundForm.str = "";
      this.refundForm.arr = [];
      this.refundForm.type = "Expire";
      this.refundMoney = "0.00";
      try {
        const res = await getRefund(this.id);
        this.refundDialog = res.data.data;
        // if (!this.refundDialog.allow_refund) {
        //   this.noRefundVisible = true
        //   return false
        // }
        this.refundVisible = true;
      } catch (error) {
        showToast(error.data.msg);
      }
    },
    changeReson(val) {
      const e = val[0].value;
      this.refundMoney =
        e === "Immediate" ? this.refundDialog.host.amount : "0.00";
    },
    formateTime(time) {
      if (time && time !== 0) {
        return formateDate(time * 1000);
      } else {
        return "--";
      }
    },
    // 返回剩余到期时间
    formateDueDay(time) {
      return Math.floor((time * 1000 - Date.now()) / (1000 * 60 * 60 * 24));
    },
    filterMoney(money) {
      if (isNaN(money)) {
        return "0.00";
      } else {
        const temp = `${money}`.split(".");
        return parseInt(temp[0]).toLocaleString() + "." + (temp[1] || "00");
      }
    },
    /* 流量包 */
    handlerPay(id) {
      this.showPackage = false;
      // 调支付弹窗
      this.$refs.topPayDialog.showPayDialog(id, 0);
    },
    cancleDialog() {
      this.showPackage = false;
      this.isShowCashDialog = false;
    },
    buyPackage() {
      this.showPackage = true;
    },
    /* 流量包 end */
    hadelSafeConfirm(val, remember) {
      this[val]("", remember);
    },
    async getIpDetail() {
      try {
        const res = await getHostIpDetails(this.id);
        const temp = res.data.data;
        this.ipDetails = JSON.parse(JSON.stringify(res.data.data));
        this.allIp = (temp.dedicate_ip + "," + temp.assign_ip).split(",");
      } catch (error) {}
    },
    copyIp(ip) {
      if (typeof ip !== "string") {
        ip = ip.join(",");
      }
      const textarea = document.createElement("textarea");
      textarea.value = ip.replace(/,/g, "\n");
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand("copy");
      document.body.removeChild(textarea);
      showToast(lang.index_text32);
    },
    /* 升降级 */
    handelUpLicense(val) {
      if (!this.isShowProUpdate && !this.isShowOptionUpdate) {
        return;
      }
      if (this.upgradeLoading) return;

      this.upgradeLoading = true;
      if (this.isShowProUpdate) {
        this.licenseActive = "1";
      } else {
        this.licenseActive = "2";
      }
      this.selectUpIndex = 0;
      showNotify({
        showClose: true,
        message: lang.common_cloud_text54,
        type: "warning",
        duration: 10000,
      });
      this.handleTabClick({name: this.licenseActive});
      this.curCycle = 0;
    },
    handleTabClick(e) {
      this.selectUpIndex = 0;
      const upApi = this.buy_id ? upAppPage : upgradePage;
      const configApi = this.buy_id ? upgradeAppPage : upgradeConfigPage;
      const id = this.buy_id ? this.buy_host_id : this.id;
      this.removeUpDiscountCode(false);
      this.reRemoveCashCode();
      if (e.name === "1") {
        // 产品升降级
        this.isShowUp = true;
        upApi(id)
          .then((res) => {
            this.upgradeList = res.data.data.host;
            this.upgradeHost = res.data.data.host;
            this.upgradeConfig = res.data.data.configoptions;
            this.upgradeSon_host = res.data.data.son_host;
            this.upgradeLoading = false;
            this.getConfig();
            this.upLicenseDialogShow = true;
          })
          .catch((err) => {
            console.log("error", err);
            showToast(err.data && err.data.msg);
            this.upgradeLoading = false;
          });
      } else {
        // 配置升降级
        configApi(this.id)
          .then((res) => {
            this.upgradeList = res.data.data.host;
            this.upgradeLoading = false;
            this.getConfig();
            this.upLicenseDialogShow = true;
          })
          .catch((err) => {
            showToast(err.data && err.data.msg);
            this.upgradeLoading = false;
          });
      }
    },
    // 更改授权数量拖动
    changeQuantity(val, i) {
      if (i.son_product_id > 0) {
        return;
      }
      let num1 = val * 1;
      let step = i.subs[0]?.qty_change || 1;
      if (num1 % step !== 0) {
        num1 = parseInt(num1 / step) * step;
      }
      this.configForm[i.id] = [num1];
      const fatherArr = this.configoptions.filter((item) => {
        if (
          item.son_product_id === 0 &&
          (item.option_type === "quantity_range" ||
            item.option_type === "quantity")
        ) {
          return item;
        }
      });
      let num = 0;
      const fatherId = fatherArr.map((item) => {
        return item.id;
      });
      fatherId.forEach((item) => {
        num = this.configForm[item][0]
          ? this.configForm[item][0] * 1 + num
          : this.configForm[item] * 1 + num;
      });
      const arr = this.configoptions.filter((item) => item.son_product_id > 0);
      const arr1 = arr.map((item) => {
        return item.id;
      });

      arr1.forEach((item) => {
        this.configForm[item] = [num];
      });
      this.changeConfig();
    },
    qtyChangeNum(val, item) {
      let num1 = val * 1;
      let step = item.subs[0]?.qty_change || 1;
      if (num1 % step !== 0) {
        num1 = parseInt(num1 / step) * step;
      }
      this.configForm[item.id] = [num1];
      const fatherArr = this.configoptions.filter((item) => {
        if (
          item.son_product_id === 0 &&
          (item.option_type === "quantity_range" ||
            item.option_type === "quantity")
        ) {
          return item;
        }
      });
      let num = 0;
      const fatherId = fatherArr.map((item) => {
        return item.id;
      });
      fatherId.forEach((item) => {
        num = this.configForm[item][0]
          ? this.configForm[item][0] * 1 + num
          : this.configForm[item] * 1 + num;
      });
      const arr = this.configoptions.filter((item) => item.son_product_id > 0);
      const arr1 = arr.map((item) => {
        return item.id;
      });
      arr1.forEach((item) => {
        this.configForm[item] = [num];
      });
      setTimeout(() => {
        this.changeConfig();
      }, 300);
    },
    // 切换数量
    changeNum(val, item) {
      let num1 = val.target.value * 1;
      let step = item.subs[0]?.qty_change || 1;
      if (num1 % step !== 0) {
        num1 = parseInt(num1 / step) * step;
      }
      this.configForm[item.id] = [num1];
      const fatherArr = this.configoptions.filter((item) => {
        if (
          item.option_type === "quantity_range" ||
          item.option_type === "quantity"
        ) {
          return item;
        }
      });
      let num = 0;
      const fatherId = fatherArr.map((item) => {
        return item.id;
      });
      fatherId.forEach((item) => {
        num = this.configForm[item][0]
          ? this.configForm[item][0] * 1 + num
          : this.configForm[item] * 1 + num;
      });
      let arr = [];
      this.upSon.forEach((item) => {
        arr = item.configoptions.filter((items) => {
          if (
            item.basicInfo.configoption_id > 0 &&
            (items.option_type === "quantity_range" ||
              items.option_type === "quantity")
          ) {
            return item;
          }
        });
      });
      const arr1 = arr.map((item) => {
        return item.id;
      });
      arr1.forEach((item) => {
        this.sonConfigForm[0][item] = [num];
      });
      setTimeout(() => {
        if (this.upLicenseDialogShow) {
          this.changeConfig();
        } else {
          this.changeSonConfig();
        }
      }, 300);
    },
    // 切换子商品数量
    changeSonNum(val, item) {
      let num = val * 1;
      let step = item.subs[0]?.qty_change || 1;
      if (num % step !== 0) {
        num = parseInt(num / step) * step;
      }
      this.sonConfigForm[item.id] = [num];
      setTimeout(() => {
        if (this.upLicenseDialogShow) {
          this.changeConfig();
        } else {
          this.changeSonConfig();
        }
      }, 300);
    },
    cascaderClick(cid, sub_id, item) {
      this.configForm[item.id] = sub_id;
      this.cascaderObj[cid].id = sub_id;
      this.getCascaderList(cid, sub_id).then((res) => {
        item.sonData = res;
        this.cascaderSon[item.id] = res;
        this.changeConfig();
      });
    },
    cascaderSonClick(cid, sub_id, item) {
      if (this.cascaderObj[cid] === item.id) {
        return;
      }
      this.getCascaderList(cid, item.id).then((res) => {
        item.sonData = res;
        this.cascaderSon[cid] = res;
        this.changeConfig();
      });
    },
    // 切换数据中心
    changeArea(id, e) {
      this.areImg = e[0].country_code;
      this.countryName = e[0].option_name;
      this.configForm[id] = e[0].area[0].id;
      this.changeConfig();
    },
    // 切换国家
    changeCountry(id, index) {
      this.curCountry[id] = index;
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
      if (this.upLicenseDialogShow) {
        this.changeConfig();
      } else {
        this.changeSonConfig();
      }
    },
    // 父商品数据输入
    fatherChange(val, i) {
      let inputNum = val * 1;
      if (i.subs && i.subs[0]) {
        let step = i.subs[0]?.qty_change || 1;
        if (inputNum % step !== 0) {
          inputNum = parseInt(inputNum / step) * step;
        }
        this.configForm[i.id] = [inputNum];
      }
      const fatherArr = this.configoptions.filter((item) => {
        if (
          item.option_type === "quantity_range" ||
          item.option_type === "quantity"
        ) {
          return item;
        }
      });
      let num = 0;
      const fatherId = fatherArr.map((item) => {
        return item.id;
      });
      fatherId.forEach((item) => {
        num = this.configForm[item][0]
          ? this.configForm[item][0] * 1 + num
          : this.configForm[item] * 1 + num;
      });
      let arr = [];
      this.upSon.forEach((item) => {
        arr = item.configoptions.filter((items) => {
          if (
            item.basicInfo.configoption_id > 0 &&
            (items.option_type === "quantity_range" ||
              items.option_type === "quantity")
          ) {
            return item;
          }
        });
      });
      const arr1 = arr.map((item) => {
        return item.id;
      });
      arr1.forEach((item) => {
        this.sonConfigForm[0][item] = [num];
      });
      this.changeConfig();
    },
    // 切换配置选项
    changeItem() {
      if (this.upLicenseDialogShow) {
        this.changeConfig();
      } else {
        this.changeSonConfig();
      }
    },
    async getConfig() {
      this.upSon = [];
      this.buySonData = [];
      this.sonCurCycle = [];
      this.sonCountry = [];
      this.sonConfigForm = [];
      this.sonCycle = [];
      this.sonCurCountry = [];
      try {
        const tabVal = this.licenseActive;
        if (tabVal === "1") {
          this.cycle =
            this.upgradeList[this.selectUpIndex]?.cycle[0]?.billingcycle;
          this.updateProId = this.upgradeList[this.selectUpIndex]?.pid;
        } else {
          const temp = JSON.parse(JSON.stringify(this.upgradeList));
          this.configoptions = temp;
          // 初始化自定义配置参数
          const numType = [4, 7, 9, 11, 14, 15, 16, 17, 18, 19];
          const obj = this.upgradeList.reduce((all, cur) => {
            all[cur.id] = numType.includes(cur.option_type)
              ? cur.qty
              : cur.subid;
            return all;
          }, {});
          this.backups = JSON.parse(JSON.stringify(obj));
          this.configForm = obj;
        }
        this.changeConfig();
      } catch (error) {
        showToast(error.data.msg);
      }
    },
    // 回填处理id
    backfillId(type, id) {
      const temp = this.upgradeConfig.filter((item) => item.id === id);
      if (type === "id") {
        return temp[0]?.configoption_sub_id;
      } else if (type === "quantity_range") {
        return [temp[0]?.qty];
      } else {
        return temp[0]?.qty;
      }
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
    // 切换周期
    changeCycle(item, index) {
      this.cycle = item.billingcycle;
      this.curCycle = index;

      if (
        this.basicInfo.pay_type === "recurring_prepayment" ||
        this.basicInfo.pay_type === "recurring_postpaid"
      ) {
        this.upSon.forEach((el) => {
          this.sonCycle = [];
          this.sonCurCycle = [];
          this.sonCycle.push(el.custom_cycles[index].id);
          this.sonCurCycle.push(index);
        });
      }
      this.changeConfig();
    },
    // 更改配置计算价格
    async changeConfig() {
      const tabVal = this.licenseActive;
      this.upPriceLoading = true;
      try {
        let res = {};
        const temp = this.formatData();
        const sonParams = [];
        // 先提交页面 remf_finance/:id/upgrade_product
        let price = 0;
        this.upgradePriceLoading = true;
        if (tabVal === "1") {
          // 升级产品
          // 提交配置计算价格
          const res = await saveUpgradeHost({
            id: this.id,
            product_id: this.updateProId,
            cycle: this.cycle,
          });
          price = res.data.data.price; // 当前产品的价格
        } else {
          // 升级配置
          const temp1 = this.formatData();
          const params = {
            id: this.id,
            configoption: temp1,
          };
          res = await saveUpgradeCnfig(params);
          price = res.data.data.price;
        }
        if (price < 0) {
          this.upParams.original_price = 0;
          this.upParams.totalPrice = 0;
          this.upgradePriceLoading = false;
          return;
        }
        this.upParams.original_price = price;
        this.upParams.totalPrice = price;
        if (this.isShowLevel) {
          // 计算折扣金额
          await clientLevelAmount({
            id: tabVal === "1" ? this.updateProId : this.product_id,
            amount: price,
          })
            .then((ress) => {
              this.upParams.clDiscount = Number(ress.data.data.discount);
            })
            .catch(() => {
              this.upParams.clDiscount = 0;
            });
        }
        // 开启了优惠码插件
        if (this.isShowPromo) {
          // 更新优惠码
          await applyPromoCode({
            // 开启了优惠券
            scene: "upgrade",
            product_id: this.product_id,
            amount: price,
            billing_cycle_time: this.hostData.billing_cycle_time,
            promo_code: this.upParams.customfield.promo_code,
            host_id: this.id,
          })
            .then((resss) => {
              this.upParams.isUseDiscountCode = true;
              this.upParams.code_discount = Number(resss.data.data.discount);
            })
            .catch((err) => {
              this.upParams.isUseDiscountCode = false;
              this.upParams.customfield.promo_code = "";
              this.upParams.code_discount = 0;
              showToast(err.data.msg);
            });
        }
        this.upParams.totalPrice =
          (price * 1000 -
            this.upParams.clDiscount * 1000 -
            this.upParams.cash_discount * 1000 -
            this.upParams.code_discount * 1000) /
            1000 >
          0
            ? (
                (price * 1000 -
                  this.upParams.cash_discount * 1000 -
                  this.upParams.clDiscount * 1000 -
                  this.upParams.code_discount * 1000) /
                1000
              ).toFixed(2)
            : 0;
        this.upgradePriceLoading = false;
      } catch (error) {
        this.upParams.original_price = 0;
        this.upParams.clDiscount = 0;
        this.upParams.isUseDiscountCode = false;
        this.upParams.customfield.promo_code = "";
        this.upParams.code_discount = 0;
        this.upParams.totalPrice = 0;
        this.upgradePriceLoading = false;
        showToast(error.data.msg);
      }
    },
    formatData() {
      // 处理数量类型的转为数组
      const temp = JSON.parse(JSON.stringify(this.configForm));
      // Object.keys(temp).forEach(el => {
      //   const arr = this.configoptions.filter(item => item.id * 1 === el * 1)
      //   if (arr.length !== 0) {
      //     if (arr[0].option_type === 'quantity'
      //       || arr[0].option_type === 'quantity_range'
      //       || arr[0].option_type === 'multi_select') {
      //       if (typeof (temp[el]) !== 'object') {
      //         temp[el] = [temp[el]]
      //       }
      //     }
      //   }
      // })
      return temp;
    },
    // 点击可升级授权
    selectUpItem(index) {
      this.selectUpIndex = index;
      this.curCycle = 0;
      this.getConfig();
    },
    // 提交升级
    handelUpConfirm() {
      if (this.upBtnLoading) return;
      // this.upBtnLoading = true
      if (this.licenseActive === "1") {
        // 产品升级结算
        upgradeHost({id: this.id})
          .then((res) => {
            this.$refs.payDialog.showPayDialog(res.data.data.orderid);
          })
          .catch((err) => {
            showToast(err.data.msg);
          })
          .finally(() => {
            this.upBtnLoading = false;
            this.upLicenseDialogShow = false;
          });
      } else {
        if (this.isEquivalent(this.backups, this.configForm)) {
          showToast(lang.common_cloud_text241);
          this.upBtnLoading = false;
          return;
        }
        const temp1 = this.formatData();
        params = {configoption: temp1, buy: this.isBuyServe};
        const upConfigApi = this.buy_id ? upgradeAppHost : upgradeConfigHost;
        const id = this.buy_id ? this.buy_host_id : this.id;
        upConfigApi(id, params)
          .then((res) => {
            this.$refs.payDialog.showPayDialog(res.data.data.id);
          })
          .catch((err) => {
            showToast(err.data.msg);
          })
          .finally(() => {
            this.upBtnLoading = false;
            this.upLicenseDialogShow = false;
          });
      }
    },
    // 比较对象是否相等
    isEquivalent(a, b) {
      // a:已有配置  b:当前配置
      // 获取a和b对象的属性名数组
      const aProps = Object.getOwnPropertyNames(a);
      // 遍历对象的每个属性并进行比较
      for (let i = 0; i < aProps.length; i++) {
        const propName = aProps[i];
        // 如果属性值为对象，则递归调用该函数进行比较
        if (typeof a[propName] === "object") {
          if (!this.isEquivalent(a[propName], b[propName])) {
            return false;
          }
        } else {
          if (b.hasOwnProperty(propName)) {
            // 否则，直接比较属性值
            if (a[propName] !== b[propName]) {
              return false;
            }
          }
        }
      }
      // 如果遍历完成则说明两个对象内容相同
      return true;
    },
    /* 升降级 end */
    async getLineDetails() {
      try {
        // 获取线路详情，
        const res = await getLineDetail({
          id: this.product_id,
          line_id: this.cloudData.line.id,
        });
        this.lineDetail = res.data.data;
        // 默认选择带宽
        if (this.lineDetail.bw) {
          if (this.cloudData?.bw !== 0) {
            // 初次回填
            this.params.bw = this.cloudData.bw * 1;
          } else {
            this.params.bw =
              this.lineDetail.bw[0]?.value || this.lineDetail.bw[0]?.min_value;
          }
          this.bwName = this.params.bw + "M";
          // 循环生成带宽可选数组
          const fArr = [];
          this.lineDetail.bw.forEach((item) => {
            fArr.push(...this.createArr([item.min_value, item.max_value]));
          });
          this.bwArr = fArr;
          this.bwTip = this.createTip(fArr);
          this.bwMarks = this.createMarks(this.bwArr);
        }
        // 默认选择流量
        if (this.lineDetail.flow) {
          if (this.cloudData?.flow) {
            // 初次回填
            this.params.flow = this.cloudData.flow * 1;
          } else {
            this.params.flow = this.lineDetail.flow[0]?.value;
          }
          this.flowName =
            this.params.flow > 0 ? this.params.flow + "G" : lang.mf_tip28;
        }
        // 默认选择cpu
        this.params.cpu =
          this.cloudData.cpu !== 0
            ? this.cloudData.cpu
            : this.calcCpuList[0]?.value;
        this.cpuName =
          this.cloudData.cpu !== 0
            ? this.cloudData.cpu + lang.mf_cores
            : this.calcCpuList[0]?.value + lang.mf_cores;
        // 默认选择内存
        if (this.memoryList[0].type === "radio") {
          this.params.memory =
            this.cloudData.memory !== 0
              ? this.cloudData.memory * 1
              : this.calaMemoryList[0]?.value * 1;
        } else {
          this.params.memory =
            this.cloudData.memory !== 0
              ? this.cloudData.memory * 1
              : this.calaMemoryList[0] * 1;
        }
        this.memoryName =
          this.cloudData.memory !== 0
            ? this.cloudData.memory + "G"
            : this.calaMemoryList[0]?.value + "G";
        // 默认选择防御
        this.params.peak_defence =
          this.cloudData.peak_defence !== 0 ? this.cloudData.peak_defence : "";
        this.defenseName = this.params.peak_defence + "G";
        this.changeConfig();
      } catch (error) {
        console.log("####", error);
      }
    },
    changeCpu(e) {
      // 切换cpu，改变内存
      this.params.cpu = e.replace(lang.mf_cores, "");
      setTimeout(() => {
        this.params.memory =
          this.memoryList[0].type === "radio"
            ? this.calaMemoryList[0]?.value
            : this.calaMemoryList[0];
        this.memoryName = this.params.memory + "G";
        this.changeConfig();
      }, 0);
    },
    // 切换防御
    changeDefence(e) {
      this.params.peak_defence = e.replace("G", "");
      setTimeout(() => {
        this.changeConfig();
      }, 0);
    },
    changeBw(e) {
      this.params.bw = e.replace("M", "");
      // 计算价格
      setTimeout(() => {
        this.changeConfig();
      }, 0);
    },
    // 切换流量
    changeFlow(e) {
      if (e === lang.mf_tip28) {
        this.params.flow = 0;
      } else {
        this.params.flow = e.replace("G", "") * 1;
      }

      setTimeout(() => {
        this.changeConfig();
      }, 0);
    },
    // 切换内存
    changeMemory(e) {
      this.params.memory = e.replace("G", "");
      setTimeout(() => {
        this.changeConfig();
      }, 0);
    },
    createArr([m, n]) {
      // 生成数组
      let temp = [];
      for (let i = m; i <= n; i++) {
        temp.push(i);
      }
      return temp;
    },
    createTip(arr) {
      // 生成范围提示
      let tip = "";
      let num = [];
      arr.forEach((item, index) => {
        if (arr[index + 1] - item > 1) {
          num.push(index);
        }
      });
      if (num.length === 0) {
        tip = `${arr[0]}-${arr[arr.length - 1]}`;
      } else {
        tip += `${arr[0]}-${arr[num[0]]},`;
        num.forEach((item, ind) => {
          tip +=
            arr[item + 1] +
            "-" +
            (arr[num[ind + 1]] ? arr[num[ind + 1]] + "," : arr[arr.length - 1]);
        });
      }
      return tip;
    },
    changeBwNum(num) {
      if (!this.bwArr.includes(num)) {
        this.bwArr.forEach((item, index) => {
          if (num > item && num < this.bwArr[index + 1]) {
            this.params.bw =
              num - item > this.bwArr[index + 1] - num
                ? this.bwArr[index + 1]
                : item;
          }
        });
      }
      this.changeConfig();
    },
    createMarks(data) {
      const obj = {
        0: "",
        25: "",
        50: "",
        75: "",
        100: "",
      };
      const range = data[data.length - 1] - data[0];
      obj[0] = `${data[0]}`;
      obj[25] = `${Math.ceil(range * 0.25)}`;
      obj[50] = `${Math.ceil(range * 0.5)}`;
      obj[75] = `${Math.ceil(range * 0.75)}`;
      obj[100] = `${data[data.length - 1]}`;
      return obj;
    },
    changeMem(num) {
      if (!this.calaMemoryList.includes(num)) {
        this.calaMemoryList.forEach((item, index) => {
          if (num > item && num < this.calaMemoryList[index + 1]) {
            this.params.memory =
              num - item > this.calaMemoryList[index + 1] - num
                ? this.calaMemoryList[index + 1]
                : item;
          }
        });
      }
      this.changeConfig();
    },

    changeVpc4() {
      switch (this.vpc_ips.vpc6.value) {
        case 25:
          this.vpc_ips.vpc4 = this.near([0, 128], this.vpc_ips.vpc4);
          break;
        case 26:
          this.vpc_ips.vpc4 = this.near([0, 64, 128, 192], this.vpc_ips.vpc4);
          break;
        case 27:
          this.vpc_ips.vpc4 = this.near(
            [0, ...this.productArr(32, 224)],
            this.vpc_ips.vpc4
          );
          break;
        case 28:
          this.vpc_ips.vpc4 = this.near(
            [0, ...this.productArr(16, 240)],
            this.vpc_ips.vpc4
          );
          break;
      }
    },
    productArr(min, max, step) {
      const arr = [];
      for (let i = min; i < max + 1; i = i + min) {
        arr.push(i);
      }
      return arr;
    },
    near(arr, n) {
      arr.sort(function (a, b) {
        return Math.abs(a - n) - Math.abs(b - n);
      });
      return arr[0];
    },
    changeVpcMask(value) {
      switch (value) {
        case 16:
          this.vpc_ips.vpc3 = 0;
          this.vpc_ips.vpc4 = 0;
          break;
        case 17:
          this.vpc_ips.vpc3 = this.near([0, 128], this.vpc_ips.vpc3);
          this.vpc_ips.vpc3Tips = lang.range2;
          this.vpc_ips.vpc4 = 0;
          break;
        case 18:
          this.vpc_ips.vpc3 = this.near([0, 64, 128, 192], this.vpc_ips.vpc3);
          this.vpc_ips.vpc3Tips = lang.range3;
          this.vpc_ips.vpc4 = 0;
          break;
        case 19:
          this.vpc_ips.vpc3 = this.near(
            [0, ...this.productArr(32, 224)],
            this.vpc_ips.vpc3
          );
          this.vpc_ips.vpc3Tips = lang.range4;
          this.vpc_ips.vpc4 = 0;
          break;
        case 20:
          this.vpc_ips.vpc3 = this.near(
            [0, ...this.productArr(16, 240)],
            this.vpc_ips.vpc3
          );
          this.vpc_ips.vpc3Tips = lang.range5;
          this.vpc_ips.vpc4 = 0;
          break;
        case 21:
          this.vpc_ips.vpc3 = this.near(
            [0, ...this.productArr(8, 248)],
            this.vpc_ips.vpc3
          );
          this.vpc_ips.vpc3Tips = lang.range6;
          this.vpc_ips.vpc4 = 0;
          break;
        case 22:
          this.vpc_ips.vpc3 = this.near(
            [0, ...this.productArr(4, 252)],
            this.vpc_ips.vpc3
          );
          this.vpc_ips.vpc3Tips = lang.range7;
          this.vpc_ips.vpc4 = 0;
          break;
        case 23:
          this.vpc_ips.vpc3 = this.near(
            [0, ...this.productArr(2, 254)],
            this.vpc_ips.vpc3
          );
          this.vpc_ips.vpc3Tips = lang.range8;
          this.vpc_ips.vpc4 = 0;
          break;
        case 24:
          this.vpc_ips.vpc3Tips = lang.range9;
          this.vpc_ips.vpc4 = 0;
          break;
        case 25:
          this.vpc_ips.vpc4 = this.near([0, 128], this.vpc_ips.vpc4);
          this.vpc_ips.vpc4Tips = lang.range2;
          this.vpc_ips.vpc3Tips = lang.range1;
          break;
        case 26:
          this.vpc_ips.vpc4 = this.near([0, 64, 128, 192], this.vpc_ips.vpc4);
          this.vpc_ips.vpc4Tips = lang.range3;
          this.vpc_ips.vpc3Tips = lang.range1;
          break;
        case 27:
          this.vpc_ips.vpc4 = this.near(
            [0, ...this.productArr(32, 224)],
            this.vpc_ips.vpc4
          );
          this.vpc_ips.vpc4Tips = lang.range4;
          this.vpc_ips.vpc3Tips = lang.range1;
          break;
        case 28:
          this.vpc_ips.vpc4 = this.near(
            [0, ...this.productArr(16, 240)],
            this.vpc_ips.vpc4
          );
          this.vpc_ips.vpc4Tips = lang.range12;
          this.vpc_ips.vpc3Tips = lang.range1;
          break;
      }
    },
    /* vpc校验规则 */
    changeVpc3() {
      switch (this.vpc_ips.vpc6.value) {
        case 16:
          this.vpc_ips.vpc3 = 0;
          break;
        case 17:
          this.vpc_ips.vpc3 = this.near([0, 128], this.vpc_ips.vpc3);
          break;
        case 18:
          this.vpc_ips.vpc3 = this.near([0, 64, 128, 192], this.vpc_ips.vpc3);
          break;
        case 19:
          this.vpc_ips.vpc3 = this.near(
            [0, ...this.productArr(32, 224)],
            this.vpc_ips.vpc3
          );
          break;
        case 20:
          this.vpc_ips.vpc3 = this.near(
            [0, ...this.productArr(16, 240)],
            this.vpc_ips.vpc3
          );
          break;
        case 21:
          this.vpc_ips.vpc3 = this.near(
            [0, ...this.productArr(8, 248)],
            this.vpc_ips.vpc3
          );
          break;
        case 22:
          this.vpc_ips.vpc3 = this.near(
            [0, ...this.productArr(4, 252)],
            this.vpc_ips.vpc3
          );
          break;
        case 23:
          this.vpc_ips.vpc3 = this.near(
            [0, ...this.productArr(2, 254)],
            this.vpc_ips.vpc3
          );
          break;
      }
    },
    changeVpcIp() {
      switch (this.vpc_ips.vpc1.value) {
        case 10:
          this.vpc_ips.vpc1.tips = lang.range1;
          this.vpc_ips.min = 0;
          this.vpc_ips.max = 255;
          break;
        case 172:
          this.vpc_ips.vpc1.tips = lang.range10;
          if (this.vpc_ips.vpc2 < 16 || this.vpc_ips.vpc2 > 31) {
            this.vpc_ips.vpc2 = 16;
          }
          this.vpc_ips.min = 16;
          this.vpc_ips.max = 31;
          break;
        case 192:
          this.vpc_ips.vpc1.tips = lang.range11;
          this.vpc_ips.vpc2 = 168;
          this.vpc_ips.min = 168;
          this.vpc_ips.max = 168;
          break;
      }
    },
    // 跳转对应页面
    handleClick() {
      this.getCloudDetail();
      switch (this.activeName) {
        case "1":
          this.chartSelectValue = "1";
          this.getstarttime(1);
          this.getChartList();
          break;
        case "2":
          break;
        case "log":
          this.initLog();
          break;
        default:
          const key = this.client_area[this.activeName * 1 - 3].key;
          configArea({id: this.id, key}).then((res) => {
            this.$nextTick(() => {
              $(`#arae-${this.activeName}`).html(res.data.data.content);
            });
          });
          break;
      }
    },
    getChartList() {
      this.chartData.forEach((items, i) => {
        items.loading = true;
        const params = {
          id: this.id,
          start: this.startTime,
          type: items.type,
          select: items.selectValue,
        };
        chartList(params)
          .then((res) => {
            if (res.data.status === 200) {
              const list = res.data.data.list || [];
              const label = res.data.data.label || [];
              const options = {
                title: {
                  text: items.title,
                },
                tooltip: {
                  show: true,
                  trigger: "axis",
                },
                legend: {
                  data: res.data.data.label,
                },
                grid: {
                  left: "5%",
                  right: "4%",
                  bottom: "5%",
                  containLabel: true,
                },
                xAxis: {
                  type: "category",
                  boundaryGap: false,
                  data: list[0].map((item) => item.time),
                },
                yAxis: {
                  type: "value",
                },
                series: label.map((item, index) => {
                  return {
                    name: item,
                    data: list[index].map((item) => item.value),
                    type: "line",
                    areaStyle: {},
                  };
                }),
              };
              echarts
                .init(document.getElementById(`${i}-echart`))
                .setOption(options);
            }
            items.loading = false;
          })
          .catch((err) => {
            console.log(err);
            items.loading = false;
          });
      });
    },
    // 获取通用配置
    getCommonData() {
      this.commonData = JSON.parse(localStorage.getItem("common_set_before"));
      document.title =
        this.commonData.website_name + "-" + lang.common_cloud_text43;
    },
    // 获取自动续费状态
    getRenewStatus() {
      const params = {
        id: this.id,
      };
      renewStatus(params).then((res) => {
        if (res.data.status === 200) {
          const status = res.data.data.status;
          this.isShowPayMsg = status == 1 ? true : false;
        }
      });
    },
    autoRenewChange() {
      console.log(this.isShowPayMsg);
      this.isShowAutoRenew = true;
    },
    autoRenewDgClose() {
      this.isShowPayMsg = !this.isShowPayMsg;
      this.isShowAutoRenew = false;
    },
    doAutoRenew() {
      const params = {
        id: this.id,
        status: this.isShowPayMsg ? 1 : 0,
      };
      rennewAuto(params)
        .then((res) => {
          if (res.data.status === 200) {
            showToast(lang.common_cloud_text44);
            this.isShowAutoRenew = false;
            this.getRenewStatus();
          }
        })
        .catch((error) => {
          showToast(error.data.msg);
        });
    },
    // 获取产品详情
    getHostDetail() {
      const params = {
        id: this.id,
      };
      hostDetail(params).then((res) => {
        if (res.data.status === 200) {
          this.hostData = res.data.data.host;
          this.self_defined_field = res.data.data.self_defined_field.map(
            (item) => {
              item.hidenPass = false;
              return item;
            }
          );
          this.hostData.status_name =
            this.hostStatus[res.data.data.host.status].text;

          // 判断下次缴费时间是否在十天内
          if (
            (this.hostData.due_time * 1000 - new Date().getTime()) /
              (24 * 60 * 60 * 1000) <=
            10
          ) {
            this.isRead = true;
          }
          this.product_id = this.hostData.product_id;
          // 获取镜像数据
          this.getConfigData();
          //  this.getImage()
          // 获取其它配置
        }
      });
    },
    textRange(el) {
      const targetw = el.getBoundingClientRect().width;
      const range = document.createRange();
      range.setStart(el, 0);
      range.setEnd(el, el.childNodes.length);
      const rangeWidth = range.getBoundingClientRect().width;
      return rangeWidth > targetw;
    },
    checkWidth(e, index) {
      const bol = this.textRange(e.target);
      this.configDetails[index].show = bol;
    },
    hideTip(index) {
      this.configDetails[index].show = false;
    },

    handelConsole(item) {
      this.consoleData = item;
      if (item.func === "crack_pass") {
        this.showRePass();
      }
      if (item.func === "reinstall") {
        this.showReinstall();
      }
      if (item.func === "vnc") {
        this.doGetVncUrl();
      }
    },

    // 获取实例详情
    async getCloudDetail() {
      try {
        const res = await cloudDetail({
          id: this.id,
        });
        this.cloudData = res.data.data;
        this.chartData = res.data.data.module_chart.map((item) => {
          item.selectValue = item.select[0]?.value || "";
          item.loading = true;
          return item;
        });
        this.client_area = res.data.data.module_client_area;
        const btnList = res.data.data.module_button.control.concat(
          res.data.data.module_button.console
        );
        this.consoleList = [];
        this.powerList = [];
        btnList.forEach((item) => {
          if (
            item.func === "crack_pass" ||
            item.func === "reinstall" ||
            item.func === "vnc"
          ) {
            this.consoleList.push(item);
          } else {
            this.powerList.push(item);
          }
        });
        this.powerStatus = this.powerList[0]?.func || "";
        this.configDetails = [];
        if (this.cloudData.system_button.upgrade_option) {
          this.isShowOptionUpdate =
            !this.cloudData.system_button.upgrade_option.disabled;
        }
        const temp = []
          .filter((item) => item.showdetail)
          .reduce((all, cur) => {
            all.push({
              name: cur.fieldname,
              sub_name: cur.value,
            });
            return all;
          }, []);
        this.configDetails = this.cloudData.config_options
          .concat(temp)
          .map((item) => {
            item.show = false;
            return item;
          });

        // 主IP
        this.main_ip = this.cloudData.host_data.dedicatedip;
        // 网络里面的IP列表
        const ipList = this.cloudData.host_data.assignedips || [];
        if (this.main_ip) {
          ipList.unshift(this.main_ip);
          const _tempIp = Array.from(new Set(ipList));
          this.netDataList = _tempIp.reduce((all, cur) => {
            if (cur) {
              all.push({
                ip: cur,
                gateway: "--",
                subnet_mask: "--",
              });
            }
            return all;
          }, []);
        }

        this.osData = res.data.data.os || {};

        if (this.osData.subs && this.osData.subs[0]) {
          this.osData.subs.forEach((item, index) => {
            item.option_name = item.os;
            item.id = index;
            item.version = item.version.map((items) => {
              items.os = item.os;
              return items;
            });
          });
          this.osSelectData = this.osData.subs[0]?.version || [];
          this.osIcon =
            "/plugins/reserver/mf_finance_common/template/clientarea/pc/default/img/remf_finance_common/" +
              this.osData.subs[0]?.os +
              ".svg" || "";
          this.reinstallData.osGroupId = this.osData.subs[0]?.os || "";
          this.reinstallData.osId = this.osData.subs[0].version[0]?.id || "";
          this.selectOsObj = this.osData.subs[0]?.version[0] || {};
          this.initLoading = false;
        }
      } catch (error) {
        console.log(error);
      }
    },
    // 关闭备注弹窗
    notesDgClose() {
      this.isShowNotesDialog = false;
    },
    // 显示 修改备注 弹窗
    doEditNotes() {
      this.isShowNotesDialog = true;
      this.notesValue = this.hostData.notes;
    },
    // 修改备注提交
    subNotes() {
      const params = {
        id: this.id,
        notes: this.notesValue,
      };
      editNotes(params)
        .then((res) => {
          if (res.data.status === 200) {
            // 重新拉取产品详情
            this.getHostDetail();
            showToast(lang.appstore_text359);
            this.isShowNotesDialog = false;
          }
        })
        .catch((err) => {
          showToast(err.data.msg);
        });
    },
    // 返回产品列表页
    goBack() {
      window.history.back();
    },
    // 关闭重装系统弹窗
    reinstallDgClose() {
      this.isShowReinstallDialog = false;
    },
    // 展示重装系统弹窗
    showReinstall() {
      this.errText = "";
      this.reinstallData.password = null;
      this.reinstallData.key = null;
      this.reinstallData.port = null;
      this.isShowReinstallDialog = true;
    },
    // 提交重装系统
    doReinstall() {
      let isPass = true;
      const data = {...this.reinstallData};
      if (!data.osId) {
        isPass = false;
        showToast(lang.common_cloud_text45);
        return false;
      }
      if (!data.port) {
        isPass = false;
        showToast(lang.common_cloud_text46);
      }
      // if (data.type == "pass") {
      //   if (!data.password) {
      //     isPass = false;
      //     showToast(lang.common_cloud_text47);
      //     return false;
      //   }
      // } else {
      //   if (!data.ssh_key_id) {
      //     isPass = false;
      //     showToast(lang.common_cloud_text48);
      //     return false;
      //   }
      // }

      if (!this.client_operate_password) {
        this.$refs.safeRef.openDialog("doReinstall");
        return;
      }
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";
      if (isPass) {
        this.loading6 = true;
        let params = {
          id: this.id,
          os: data.osId,
          port: data.port,
          client_operate_password,
        };
        // if (data.type == "pass") {
        //   params.password = data.password;
        // } else {
        //   params.ssh_key_id = data.ssh_key_id;
        // }
        // 调用重装系统接口
        reinstall(params)
          .then((res) => {
            if (res.data.status == 200) {
              this.getCloudStatus();
              this.isShowReinstallDialog = false;
            }
            showToast(res.data.msg);
            this.loading6 = false;
          })
          .catch((err) => {
            this.loading6 = false;
            showToast(err.data.msg);
          });
      }
    },
    doReinstall() {
      let isPass = true;
      const data = {...this.reinstallData};
      if (!data.osId) {
        isPass = false;
        showToast(lang.common_cloud_text45);
        return false;
      }
      if (!this.client_operate_password) {
        console.log(this.client_operate_password);
        this.$refs.safeRef.openDialog("doReinstall");
        return;
      }
      this.reinstallLoading = true;
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";
      if (isPass) {
        this.errText = "";
        provision({
          id: this.id,
          func: "reinstall",
          os: this.selectOsObj.id,
          os_group: this.selectOsObj.os,
          client_operate_password,
        })
          .then((res) => {
            showToast(res.data.msg);
            this.getCloudDetail();
            this.isShowReinstallDialog = false;
            this.reinstallLoading = false;
          })
          .catch((err) => {
            this.reinstallLoading = false;
            showToast(err.data.msg);
          });
      }
    },
    // 检查产品是否购买过镜像
    doCheckImage() {
      const params = {
        id: this.id,
        image_id: this.reinstallData.osId,
      };
      checkImage(params).then(async (res) => {
        if (res.data.status === 200) {
          const p = Number(res.data.data.price);
          this.isPayImg = p > 0 ? true : false;
          this.payMoney = p;
          if (this.isShowLevel) {
            await clientLevelAmount({
              id: this.product_id,
              amount: res.data.data.price,
            })
              .then((ress) => {
                this.payDiscount = Number(ress.data.data.discount);
              })
              .catch(() => {
                this.payDiscount = 0;
              });
          }
          // 开启了优惠码插件
          if (this.isShowPromo) {
            // 更新优惠码
            await applyPromoCode({
              // 开启了优惠券
              scene: "upgrade",
              product_id: this.product_id,
              amount: p,
              billing_cycle_time: this.hostData.billing_cycle_time,
              promo_code: "",
              host_id: this.id,
            })
              .then((resss) => {
                this.payCodePrice = Number(resss.data.data.discount);
              })
              .catch((err) => {
                showToast(err.data.msg);
                this.payCodePrice = 0;
              });
          }
          this.renewLoading = false;
          this.payMoney =
            (p * 1000 - this.payCodePrice * 1000 - this.payDiscount * 1000) /
              1000 >
            0
              ? (p * 1000 -
                  this.payCodePrice * 1000 -
                  this.payDiscount * 1000) /
                1000
              : 0;
        }
      });
    },
    // 购买镜像
    payImg() {
      const params = {
        id: this.id,
        image_id: this.reinstallData.osId,
      };
      imageOrder(params).then((res) => {
        if (res.data.status === 200) {
          const orderId = res.data.data.id;
          const amount = this.payMoney;
          this.$refs.topPayDialog.showPayDialog(orderId, amount);
        }
      });
    },
    // 获取镜像数据
    getImage() {
      const params = {
        id: this.product_id,
      };
      image(params).then((res) => {
        if (res.data.status === 200) {
          this.osData = res.data.data.list;
          this.osSelectData = this.osData[0].image;
          this.reinstallData.osGroupId = this.osData[0]?.id;
          this.osIcon =
            "/plugins/reserver/mf_cloud/view/img/" +
            this.osData[0].name +
            ".png";
          this.osName = this.osData[0]?.name;
          this.reinstallData.osId = this.osData[0].image[0].id;
          // this.doCheckImage()
        }
      });
    },
    // 镜像分组改变时
    osSelectGroupChange(e) {
      this.osData.map((item) => {
        if (item.id == e) {
          this.osSelectData = this.cloudData.cloud_os.filter(
            (item) => item.group === e
          );
          this.osIcon =
            "/plugins/reserver/mf_finance_common/template/clientarea/pc/default/img/remf_finance/" +
            item.name +
            ".svg";
          this.osName = this.item[0]?.name;
          this.reinstallData.osId = this.osSelectData[0]?.id;
          // this.doCheckImage()
        }
      });
    },
    // 镜像版本改变时
    osSelectChange(e) {
      this.reinstallData.osId = e[1].id;
      this.selectOsObj = e[1];
    },
    getSelectValue(refName) {
      return this.$refs[refName].getSelectedOptions();
    },
    handelSelectImg() {
      const e = this.getSelectValue("selectPopRef");
      this.reinstallData.osId = e[0].id;
      this.osName = e[0].name;
      this.osIcon =
        "/plugins/reserver/mf_finance_common/template/clientarea/mobile/mfm201/img/mf_cloud/" +
        e[0].group +
        ".svg";
      this.showImgPick = false;
      this.isShowImage = false;
    },
    changeImage(item) {
      this.reinstallData.osGroupId = item.id;
      this.osSelectData = this.cloudData.cloud_os.filter(
        (items) => items.group === item.id
      );
      this.showImgPick = true;
    },
    changeUpImage(item) {
      this.curSystem = item.value;
      this.calcOsImgList = this.curOsItem.sub[this.curSystem].child.map(
        (os) => {
          return {
            icon: this.curSystem,
            ...os,
          };
        }
      );
      this.upImgPick = true;
    },
    // 随机生成密码
    autoPass() {
      let pass = randomCoding(1) + 0 + genEnCode(9, 1, 1, 0, 1, 0);
      this.reinstallData.password = pass;
      // 重置密码
      this.rePassData.password = pass;
      // 救援系统密码
      this.rescueData.temp_pass = pass;
    },
    // 随机生成port
    autoPort() {
      const temp = genEnCode(3, 1, 0, 0, 0, 0);
      if (temp[0] * 1 === 0) {
        this.reinstallData.port = Math.ceil(Math.random() * 9) + temp.substr(1);
      } else {
        this.reinstallData.port = temp;
      }
    },
    // 获取SSH秘钥列表
    getSshKey() {
      const params = {
        page: 1,
        limit: 1000,
        orderby: "id",
        sort: "desc",
      };
      sshKey(params).then((res) => {
        if (res.data.status === 200) {
          this.sshKeyData = res.data.data.list;
        }
      });
    },
    // 获取实例状态
    getCloudStatus() {
      const params = {
        id: this.id,
      };
      cloudStatus(params)
        .then((res) => {
          if (res.status === 200) {
            this.status = res.data.data.status;
            this.statusText = res.data.data.des;
            if (this.status == "process" || this.status === "operating") {
              this.getCloudStatus();
            } else {
              this.$emit("getstatus", res.data.data.status);
              let e = this.status;
              if (e == "on") {
                this.powerList = [
                  {
                    id: 2,
                    label: lang.common_cloud_text11,
                    value: "off",
                  },
                  {
                    id: 5,
                    label: lang.common_cloud_text42,
                    value: "hardOff",
                  },
                ];
                this.powerStatus = "off";
              } else if (e == "off") {
                this.powerList = [
                  {
                    id: 1,
                    label: lang.common_cloud_text10,
                    value: "on",
                  },
                ];
                this.powerStatus = "on";
              } else {
                this.powerList = [
                  {
                    id: 1,
                    label: lang.common_cloud_text10,
                    value: "on",
                  },
                  {
                    id: 2,
                    label: lang.common_cloud_text11,
                    value: "off",
                  },
                  {
                    id: 3,
                    label: lang.common_cloud_text13,
                    value: "rebot",
                  },
                  {
                    id: 4,
                    label: lang.common_cloud_text41,
                    value: "hardRebot",
                  },
                  {
                    id: 5,
                    label: lang.common_cloud_text42,
                    value: "hardOff",
                  },
                ];
              }
            }
          }
        })
        .catch((err) => {
          console.log("@@@err", err);
          // showToast   (err)
        });
    },
    // 获取救援模式状态
    getRemoteInfo() {
      const params = {
        id: this.id,
      };
      remoteInfo(params).then((res) => {
        if (res.data.status === 200) {
          // this.rescueStatusData = res.data.data
          // const length = this.rescueStatusData.password.length >= 6 ? 6 : this.rescueStatusData.password.length
          // for (let i = 0; i < length; i++) {
          //   this.passHidenCode += "*"
          // }
          this.isRescue = res.data.data.rescue == 1;
          this.$emit("getrescuestatus", this.isRescue);
        }
      });
    },
    clickOsItem(item) {
      this.curOsItem = item;
      this.upShowImage = true;
    },
    // 控制台点击
    doGetVncUrl() {
      if (!this.client_operate_password) {
        this.$refs.safeRef.openDialog("doGetVncUrl");
        return;
      }
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";
      const params = {
        id: this.id,
        client_operate_password,
      };
      this.loading2 = true;
      const opener = window.open("", "_blank");
      vncUrl(params)
        .then((res) => {
          if (res.data.status === 200) {
            opener.location = res.data.data.url;
          }
          this.loading2 = false;
        })
        .catch((err) => {
          opener.close();
          showToast(err.data.msg);
          this.loading2 = false;
        });
    },
    getVncUrl() {
      this.doGetVncUrl();
    },
    // 获取产品停用信息
    getRefundMsg() {
      const params = {
        id: this.id,
      };
      refundMsg(params)
        .then((res) => {
          if (res.data.status === 200) {
            this.refundData = res.data.data.refund;
          }
        })
        .catch((err) => {
          this.refundData = null;
        });
    },
    // 获取cup/内存使用信息
    getRealData() {
      realData(this.id).then((res) => {
        this.cpu_realData = res.data.data;
      });
    },
    // 支付成功回调
    paySuccess(e) {
      this.getHostDetail();
      this.getCloudDetail();
    },
    // 取消支付回调
    payCancel(e) {
      // console.log(e);
    },
    // 获取优惠码信息
    getPromoCode() {
      const params = {
        id: this.id,
      };
      promoCode(params).then((res) => {
        if (res.data.status === 200) {
          let codes = res.data.data.promo_code;
          let code = "";
          codes.map((item) => {
            code += item + ",";
          });
          code = code.slice(0, -1);
          this.codeString = code;
        }
      });
    },
    // 升降级使用优惠码
    getUpDiscount(data) {
      this.upParams.customfield.promo_code = data[1];
      this.upParams.isUseDiscountCode = true;
      this.upParams.code_discount = Number(data[0]);
      this.changeConfig();
    },
    // 移除升降级优惠码
    removeUpDiscountCode(bol = true) {
      this.upParams.isUseDiscountCode = false;
      this.upParams.customfield.promo_code = "";
      this.upParams.code_discount = 0;
      if (bol) {
        this.changeConfig();
      }
    },
    // 升降级使用代金券
    upUseCash(val) {
      this.cashObj = val;
      const price = val.price ? Number(val.price) : 0;
      this.upParams.cash_discount = price;
      this.upParams.customfield.voucher_get_id = val.id;
      this.changeConfig();
    },

    // 升降级移除代金券
    upRemoveCashCode() {
      this.$refs.cashRef && this.$refs.cashRef.closePopver();
      this.cashObj = {};
      this.upParams.cash_discount = 0;
      this.upParams.customfield.voucher_get_id = "";
      this.upParams.totalPrice =
        (this.upParams.original_price * 1000 -
          this.upParams.clDiscount * 1000 -
          this.upParams.cash_discount * 1000 -
          this.upParams.code_discount * 1000) /
          1000 >
        0
          ? (
              (this.upParams.original_price * 1000 -
                this.upParams.cash_discount * 1000 -
                this.upParams.clDiscount * 1000 -
                this.upParams.code_discount * 1000) /
              1000
            ).toFixed(2)
          : 0;
    },

    // 续费使用代金券
    reUseCash(val) {
      this.cashObj = val;
      const price = val.price ? Number(val.price) : 0;
      this.renewParams.cash_discount = price;
      this.renewParams.customfield.voucher_get_id = val.id;
    },
    // 续费移除代金券
    reRemoveCashCode() {
      this.$refs.cashRe && this.$refs.cashRef.closePopver();
      this.cashObj = {};
      this.renewParams.cash_discount = 0;
      this.renewParams.customfield.voucher_get_id = "";
    },
    // 续费使用优惠码
    async getRenewDiscount(data) {
      this.renewParams.customfield.promo_code = data[1];
      this.renewParams.isUseDiscountCode = true;
      this.renewParams.code_discount = Number(data[0]);
      const price = this.renewParams.base_price;
      const discountParams = {id: this.product_id, amount: price};
      // 开启了等级折扣插件
      if (this.isShowLevel) {
        // 获取等级抵扣价格
        await clientLevelAmount(discountParams)
          .then((res2) => {
            if (res2.data.status === 200) {
              this.renewParams.clDiscount = Number(res2.data.data.discount); // 客户等级优惠金额
            }
          })
          .catch((error) => {
            this.renewParams.clDiscount = 0;
          });
      }
    },
    // 移除续费的优惠码
    removeRenewDiscountCode() {
      this.renewParams.isUseDiscountCode = false;
      this.renewParams.customfield.promo_code = "";
      this.renewParams.code_discount = 0;
      this.renewParams.clDiscount = 0;
      const price = this.renewParams.original_price;
    },

    // 显示续费弹窗
    showRenew() {
      // if (this.renewBtnLoading) return;
      // this.renewBtnLoading = true;
      // 获取续费页面信息
      const params = {
        id: this.id,
      };
      this.isShowRenew = true;
      this.renewLoading = true;
      renewPage(params)
        .then(async (res) => {
          if (res.data.status === 200) {
            this.renewPageData = res.data.data.host;
            this.renewActiveId = 0;
            this.renewParams.billing_cycle =
              this.renewPageData[0].billing_cycle;
            this.renewParams.duration = this.renewPageData[0].duration;
            this.renewParams.original_price = this.renewPageData[0].price;
            this.renewParams.base_price = this.renewPageData[0].base_price;
            this.renewParams.totalPrice = this.renewPageData[0].price;
            this.renewLoading = false;
          }
          this.renewLoading = false;
        })
        .catch((err) => {
          this.renewBtnLoading = false;
          this.renewLoading = false;
          showToast(err.data.msg);
        });
    },
    // 续费弹窗关闭
    renewDgClose() {
      this.renewBtnLoading = false;
      this.isShowRenew = false;
      this.removeRenewDiscountCode();
      this.reRemoveCashCode();
    },
    // 续费提交
    subRenew() {
      const params = {
        id: this.id,
        billing_cycle: this.renewParams.billing_cycle,
        customfield: this.renewParams.customfield,
      };
      renew(params)
        .then((res) => {
          if (res.data.status === 200) {
            if (res.data.code == "Paid") {
              showToast(res.data.msg);
              this.getHostDetail();
              this.isShowRenew = false;
              this.renewBtnLoading = false;
            } else {
              this.isShowRenew = false;
              this.renewOrderId = res.data.data.id;
              const orderId = res.data.data.id;
              const amount = this.renewParams.totalPrice;
              this.renewBtnLoading = false;
              this.$refs.topPayDialog.showPayDialog(orderId, amount);
            }
          }
        })
        .catch((err) => {
          console.log("error", err);
          showToast(err.data.msg);
        });
    },
    // 续费周期点击
    async renewItemChange(item, index) {
      this.reRemoveCashCode();
      this.renewLoading = true;
      this.renewActiveId = index;
      this.renewParams.duration = item.duration;
      this.renewParams.billing_cycle = item.billing_cycle;
      let price = item.price;
      this.renewParams.original_price = item.price;
      this.renewParams.base_price = item.base_price;

      // 开启了优惠码插件
      if (this.isShowPromo && this.renewParams.isUseDiscountCode) {
        const discountParams = {id: this.product_id, amount: item.base_price};
        // 开启了等级折扣插件
        if (this.isShowLevel) {
          // 获取等级抵扣价格
          await clientLevelAmount(discountParams)
            .then((res2) => {
              if (res2.data.status === 200) {
                this.renewParams.clDiscount = Number(res2.data.data.discount); // 客户等级优惠金额
              }
            })
            .catch((error) => {
              this.renewParams.clDiscount = 0;
            });
        }
        // 更新优惠码
        await applyPromoCode({
          // 开启了优惠券
          scene: "renew",
          product_id: this.product_id,
          amount: item.base_price,
          billing_cycle_time: this.renewParams.duration,
          promo_code: this.renewParams.customfield.promo_code,
        })
          .then((resss) => {
            price = item.base_price;
            this.renewParams.isUseDiscountCode = true;
            this.renewParams.code_discount = Number(resss.data.data.discount);
          })
          .catch((err) => {
            showToast(err.data.msg);
            this.removeRenewDiscountCode();
          });
      }

      this.renewLoading = false;
    },
    // 升降级点击
    showUpgrade() {
      this.getLineDetails();
      this.isShowUpgrade = true;
      showNotify({
        showClose: true,
        message: lang.common_cloud_text54,
        type: "warning",
        duration: 10000,
      });
    },
    // 关闭升降级弹窗
    upgradeDgClose() {
      this.upLicenseDialogShow = false;
      this.removeUpDiscountCode(false);
      this.reRemoveCashCode();
    },
    // 获取升降级价格
    getCycleList() {
      this.upgradePriceLoading = true;
      const params = {
        id: this.id,
        cpu: this.params.cpu,
        memory: this.params.memory,
        bw: this.params.bw,
        flow: this.params.flow,
        peak_defence: this.params.peak_defence,
      };
      upgradePackagePrice(params)
        .then(async (res) => {
          if (res.data.status == 200) {
            let price = res.data.data.price; // 当前产品的价格
            if (price < 0) {
              this.upParams.original_price = 0;
              this.upParams.totalPrice = 0;
              this.upgradePriceLoading = false;
              return;
            }
            this.upParams.original_price = price;
            this.upParams.totalPrice = price;
            // 开启了等级优惠
            if (this.isShowLevel) {
              await clientLevelAmount({id: this.product_id, amount: price})
                .then((ress) => {
                  this.upParams.clDiscount = Number(ress.data.data.discount);
                })
                .catch(() => {
                  this.upParams.clDiscount = 0;
                });
            }
            // 开启了优惠码插件
            if (this.isShowPromo) {
              // 更新优惠码
              await applyPromoCode({
                // 开启了优惠券
                scene: "upgrade",
                product_id: this.product_id,
                amount: price,
                billing_cycle_time: this.hostData.billing_cycle_time,
                promo_code: this.upParams.customfield.promo_code,
                host_id: this.id,
              })
                .then((resss) => {
                  this.upParams.isUseDiscountCode = true;
                  this.upParams.code_discount = Number(
                    resss.data.data.discount
                  );
                })
                .catch((err) => {
                  this.upParams.isUseDiscountCode = false;
                  this.upParams.customfield.promo_code = "";
                  this.upParams.code_discount = 0;
                  showToast(err.data.msg);
                });
            }
            this.upParams.totalPrice =
              (price * 1000 -
                this.upParams.clDiscount * 1000 -
                this.upParams.cash_discount * 1000 -
                this.upParams.code_discount * 1000) /
                1000 >
              0
                ? (
                    (price * 1000 -
                      this.upParams.cash_discount * 1000 -
                      this.upParams.clDiscount * 1000 -
                      this.upParams.code_discount * 1000) /
                    1000
                  ).toFixed(2)
                : 0;
            this.upgradePriceLoading = false;
          } else {
            this.upParams.original_price = 0;
            this.upParams.clDiscount = 0;
            this.upParams.isUseDiscountCode = false;
            this.upParams.customfield.promo_code = "";
            this.upParams.code_discount = 0;
            this.upParams.totalPrice = 0;
            this.upgradePriceLoading = false;
          }
        })
        .catch((error) => {
          this.upParams.original_price = 0;
          this.upParams.clDiscount = 0;
          this.upParams.isUseDiscountCode = false;
          this.upParams.customfield.promo_code = "";
          this.upParams.code_discount = 0;
          this.upParams.totalPrice = 0;
          this.upgradePriceLoading = false;
        });
    },
    // 升降级提交
    upgradeSub() {
      const params = {
        id: this.id,
        customfield: {
          promo_code: this.upParams.customfield.promo_code,
          voucher_get_id: this.upParams.customfield.voucher_get_id,
        },
      };
      const type =
        this.licenseActive === "1" ? "upgrade_product" : "upgrade_config";
      upgradeOrder(type, params)
        .then((res) => {
          if (res.data.status === 200) {
            showToast(lang.common_cloud_text56);
            this.isShowUpgrade = false;
            const orderId = res.data.data.id;
            this.upLicenseDialogShow = false;
            // 调支付弹窗
            this.$refs.topPayDialog.showPayDialog(orderId, 0);
          } else {
            showToast(err.data.msg);
          }
        })
        .catch((err) => {
          showToast(err.data.msg);
        });
    },
    // 升降级弹窗 套餐选择框变化
    upgradeSelectChange(e) {
      this.upgradeList.map((item) => {
        if (item.id == e) {
          // 获取当前套餐的周期
          let duration = this.cloudData.duration;
          // 该周期新套餐的价格
          let money = item[duration];
          switch (duration) {
            case "month_fee":
              duration = lang.appstore_text54;
              break;
            case "quarter_fee":
              duration = lang.appstore_text55;
              break;
            case "year_fee":
              duration = lang.appstore_text57;
              break;
            case "two_year":
              duration = lang.biennially;
              break;
            case "three_year":
              duration = lang.triennially;
              break;
            case "onetime_fee":
              duration = lang.onetime;
              break;
          }
          this.changeUpgradeData = {
            id: item.id,
            money,
            duration,
            description: item.description,
          };
        }
      });
      this.reRemoveCashCode();
      this.changeConfig();
    },

    // 取消停用
    quitRefund() {
      if (!this.client_operate_password) {
        this.$refs.safeRef.openDialog("quitRefund");
        return;
      }
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";

      const params = {
        id: this.refundData.id,
        client_operate_password,
      };
      cancel(params)
        .then((res) => {
          if (res.data.status == 200) {
            showToast(lang.common_cloud_text57);
            this.getRefundMsg();
          }
        })
        .catch((err) => {
          if (err.data.data) {
            if (
              !client_operate_password &&
              err.data.data.operate_password === 1
            ) {
              return;
            } else {
              return showToast(err.data.msg);
            }
          }
          showToast(err.data.msg);
        });
    },

    showRefund() {
      const params = {
        host_id: this.id,
      };
      this.refundParams.suspend_reason = "";
      this.isShowRefund = true;
      // 获取停用页面信息
      refundPage(params).then((res) => {
        if (res.data.status == 200) {
          this.refundPageData = res.data.data;
          this.refundPageData.configs =
            this.refundPageData.config_option.data.reduce((all, cur) => {
              all.push({
                name: cur.name,
                value: this.configDetails.filter(
                  (el) =>
                    el.id ===
                    cur.field.replace("configoption[", "").replace("]", "") * 1
                )[0]?.sub_name,
              });
              return all;
            }, []);
        }
      });
    },
    // 关闭停用弹窗
    refundDgClose() {
      this.isShowRefund = false;
    },

    goPay() {
      if (this.hostData.status === "Unpaid") {
        this.$refs.topPayDialog.showPayDialog(this.hostData.order_id);
      }
    },
    // 停用弹窗提交
    subRefund() {
      const params = {
        host_id: this.id,
        suspend_reason: this.refundParams.suspend_reason,
        type: this.refundParams.type,
      };
      if (!params.suspend_reason) {
        showToast(lang.common_cloud_text58);
        return false;
      }
      if (!params.type) {
        showToast(lang.common_cloud_text59);
        return false;
      }
      if (!this.client_operate_password) {
        this.$refs.safeRef.openDialog("subRefund");
        return;
      }
      params.client_operate_password = this.client_operate_password;
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";
      this.refundLoading = true;
      refund(params)
        .then((res) => {
          if (res.data.status == 200) {
            showToast(lang.common_cloud_text60);
            this.isShowRefund = false;
            this.refundLoading = false;
            this.getRefundMsg();
          }
        })
        .catch((err) => {
          this.refundLoading = false;
          if (err.data.data) {
            if (
              !client_operate_password &&
              err.data.data.operate_password === 1
            ) {
              return;
            } else {
              return showToast(err.data.msg);
            }
          }
          showToast(err.data.msg);
        });
    },
    // 管理开始
    // 电源相关
    toChangePower() {
      if (this.loading1) {
        return;
      }
      if (!this.client_operate_password) {
        this.$refs.safeRef.openDialog("toChangePower");
        return;
      }
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";
      this.loading1 = true;
      provision({
        id: this.id,
        func: this.powerStatus,
        client_operate_password,
      })
        .then((res) => {
          showToast(res.data.msg);
          this.loading1 = false;
          this.isShowPowerChange = false;
          this.getDetail();
        })
        .catch((err) => {
          showToast(err.data.msg);
          this.loading1 = false;
        });
    },
    powerDgClose() {
      this.isShowPowerChange = false;
    },
    // 重置密码点击
    showRePass() {
      this.errText = "";
      this.rePassData = {
        password: "",
        checked: false,
      };
      this.isShowRePass = true;
    },
    // 关闭重置密码弹窗
    rePassDgClose() {
      this.isShowRePass = false;
    },
    // 重置密码提交
    rePassSub() {
      const data = this.rePassData;
      if (!data.password) {
        showToast(lang.common_cloud_text61);
        return false;
      }
      if (!this.client_operate_password) {
        this.$refs.safeRef.openDialog("rePassSub");
        return;
      }
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";

      this.loading5 = true;
      const params = {
        id: this.id,
        func: "crack_pass",
        password: data.password,
        force: "on",
        client_operate_password,
      };
      provision(params)
        .then((res) => {
          if (res.data.status === 200) {
            this.isShowRePass = false;
            this.getDetail();
          }
          showToast(res.data.msg);
          this.loading5 = false;
        })
        .catch((error) => {
          showToast(error.data.msg);
          this.loading5 = false;
        });
    },
    // 救援模式点击
    showRescueDialog() {
      this.errText = "";
      this.rescueData = {
        type: "1",
        temp_pass: "",
      };
      this.isShowRescue = true;
    },
    // 关闭救援模式弹窗
    rescueDgClose() {
      this.isShowRescue = false;
    },
    // 救援模式提交按钮
    rescueSub() {
      let isPass = true;
      if (!this.rescueData.type) {
        isPass = false;
        showToast(lang.common_cloud_text64);

        return false;
      }
      if (!this.rescueData.temp_pass) {
        isPass = false;
        showToast(lang.common_cloud_text65);

        return false;
      }
      if (!this.client_operate_password) {
        this.$refs.safeRef.openDialog("rescueSub");
        return;
      }
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";
      if (isPass) {
        this.loading3 = true;
        // 调用救援系统接口
        const params = {
          id: this.id,
          type: this.rescueData.type,
          temp_pass: this.rescueData.temp_pass,
          client_operate_password,
        };
        rescue(params)
          .then((res) => {
            if (res.data.status === 200) {
              this.isShowRescue = false;
              this.getCloudStatus();
            }
            showToast(res.data.msg);
            this.loading3 = false;
          })
          .catch((err) => {
            showToast(err.data.msg);
            this.loading3 = false;
          });
      }
    },
    // 显示退出救援模式确认框
    showQuitRescueDialog() {
      this.isShowQuit = true;
    },
    quitDgClose() {
      this.isShowQuit = false;
    },
    // 执行退出救援模式
    reQuitSub() {
      if (!this.client_operate_password) {
        this.$refs.safeRef.openDialog("reQuitSub");
        return;
      }
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";

      const params = {
        id: this.id,
        client_operate_password,
      };
      exitRescue(params)
        .then((res) => {
          if (res.data.status === 200) {
            showToast(res.data.msg);
            this.getCloudStatus();
            this.isShowQuit = false;
          }
        })
        .catch((err) => {
          if (err.data.data) {
            if (
              !client_operate_password &&
              err.data.data.operate_password === 1
            ) {
              return;
            } else {
              return showToast(err.data.msg);
            }
          }
          showToast(err.data.msg);
        });
    },

    // 获取磁盘列表
    doGetDiskList() {
      this.diskLoading = true;
      const params = {
        id: this.id,
      };
      getDiskList(params)
        .then((res) => {
          this.diskList = res.data.data.list || [];
          this.allDiskList = res.data.data.list;
          this.trueDiskLength = res.data.data.list.filter((item) => {
            return item.type2 !== "system";
          }).length;
          this.diskLoading = false;
        })
        .catch((err) => {
          this.diskLoading = false;
        });
    },
    // 显示扩容弹窗
    showExpansion() {
      // 标记打开扩容弹窗
      this.isOrderOrExpan = false;
      this.expansionDiskPrice = 0.0;
      this.expansionDiscount = 0.0;
      this.expansionCodePrice = 0.0;
      this.oldDiskList = [];
      this.diskList.forEach((item) => {
        if (item.type2 !== "system") {
          item.selectList = [];
          this.dataDiskList.forEach((items) => {
            if (
              items.other_config.disk_type === item.type &&
              (items.type === "step" || items.type === "total")
            ) {
              item.selectList.push(items);
              item.max_value = items.max_value;
            }
            if (
              items.other_config.disk_type === item.type &&
              items.type === "radio"
            ) {
              if (items.value >= item.size) {
                item.selectList.push(items);
              }
            }
          });
          item.min_value = item.size;
          this.oldDiskList.push(JSON.parse(JSON.stringify(item)));
        }
      });
      this.isShowExpansion = true;
    },
    // 显示订购磁盘弹窗
    showDg() {
      // 标记打开订购磁盘弹窗
      this.isOrderOrExpan = true;
      this.oldDiskList2 = this.diskList.map((item) => ({...item}));
      this.orderDiskData = {
        id: 0,
        remove_disk_id: [],
        add_disk: [],
      };
      this.moreDiskData = [];
      this.addMoreDisk();
      this.isShowDg = true;
    },
    addTypeChange(val, item) {
      item.size = item.selectList[0][item.type][0].value;
    },
    changeType(val, item) {
      item.size = item.selectList[0][item.type].min_value;
    },
    goSSHpage(id) {
      location.href = `/security_ssh.htm`;
    },
    // 新增磁盘项目
    addMoreDisk() {
      // 最多存在的磁盘数目
      const max = 16;
      // 已有磁盘的数目
      const oldNum = this.oldDiskList2.filter((item) => {
        return item.type2 !== "system";
      }).length;
      // 已新增磁盘的数目
      const newNum = this.moreDiskData.length;
      if (newNum + oldNum < max) {
        const diskData = [...this.moreDiskData];
        const itemData = {};
        let max_value = 0;
        const obj = {
          disk_typeList: [],
        };
        const arr = this.dataDiskList.map((item) => {
          return JSON.parse(JSON.stringify(item));
        });
        arr.forEach((items) => {
          if (arr[0].type === "radio") {
            if (items.max_value > max_value) {
              max_value = items.max_value;
            }
            obj.type = "radio";
            if (items.other_config.disk_type === "") {
              items.other_config.disk_type = lang.mf_no;
            }
            if (!obj.disk_typeList.includes(items.other_config.disk_type)) {
              const type = items.other_config.disk_type;
              obj.disk_typeList.push(type);
              obj[type] = [];
            }
            obj[items.other_config.disk_type].push({
              label: items.value + "G",
              value: items.value,
            });
          } else {
            obj.type = "input";
            if (items.other_config.disk_type === "") {
              items.other_config.disk_type = lang.mf_no;
            }
            if (!obj.disk_typeList.includes(items.other_config.disk_type)) {
              const type = items.other_config.disk_type;
              obj.disk_typeList.push(type);
              obj[type] = {
                config: [],
                min_value: 0,
                max_value: 0,
                tips: "",
              };
            }
            obj[items.other_config.disk_type].config.push([
              items.min_value,
              items.max_value,
            ]);
          }
        });
        obj.disk_typeList.forEach((item) => {
          const arr = [];
          const arr1 = [];
          obj[item].config.forEach((is) => {
            arr.push(...this.createArr([is[0], is[1]]));
            arr1.push(...is);
          });
          obj[item].min_value = Math.min.apply(Math, arr1);
          obj[item].max_value = Math.max.apply(Math, arr1);
          obj[item].tips = this.createTip(arr);
        });
        if (this.dataDiskList.length !== 0) {
          itemData.size =
            this.dataDiskList[0].type === "radio"
              ? this.dataDiskList[0].other_config.disk_type
              : obj[obj.disk_typeList[0]]?.min_value;
          itemData.disk_type = this.dataDiskList[0].other_config.data_disk_type;
          itemData.selectList = [obj];
          itemData.min_value =
            this.dataDiskList[0].type === "radio"
              ? 0
              : obj[obj.disk_typeList[0]]?.min_value;
          itemData.max_value =
            this.dataDiskList[0].type === "radio"
              ? 0
              : obj[obj.disk_typeList[0]]?.max_value;
          itemData.type =
            this.dataDiskList[0].type === "radio"
              ? obj.disk_typeList[0]
              : obj.disk_typeList[0];
        }
        diskData.push(itemData);
        diskData.map((item, index) => {
          item.index = index + 1;
        });
        this.moreDiskData = diskData;
        this.handlerType(this.moreDiskData, "data");
      } else {
        showToast(lang.mf_tip29);
      }
    },
    // 初始化处理系统盘，数据盘类型
    handlerType(data, type) {},
    // 获取其他配置
    getConfigData() {
      const params = {
        id: this.product_id,
      };
      getOrderConfig(params).then((res) => {
        // 升降级配置数据
        // 3-30订购页面配置： 老财务这边接口返回的数据和之前的不一样，需要特殊处理
        if (res.data.status === 200) {
          //   this.configData = res.data.data
          //    this.systemDiskList = res.data.data.system_disk
          //    this.dataDiskList = res.data.data.data_disk
          //  this.memoryList = res.data.data.memory
          //   this.cpuList = res.data.data.cpu
          //   this.configLimitList = res.data.data.config_limit
          //   this.configObj = res.data.data.config
          //   this.backup_config = res.data.data.backup_config
          //   this.snap_config = res.data.data.snap_config
          //   if (res.data.data.memory.length > 0 && res.data.data.memory[0].type !== 'radio') { // 范围的时候生成默认范围数组
          //     this.memoryArr = res.data.data.memory.reduce((all, cur) => {
          //       all.push(...this.createArr([cur.min_value, cur.max_value]))
          //       return all
          //     }, [])
          //   }
          //   if (this.memoryList.length > 0) {
          //     if (this.memoryList[0].type === 'radio') {
          //       this.memoryType = true
          //     } else {
          //       this.memoryType = false
          //     }
          //   }
        }
      });
    },
    // 关闭订购页面弹窗
    dgClose() {
      this.isShowDg = false;
    },
    // 删除当前的磁盘项
    delOldSize(id) {
      this.oldDiskList = this.oldDiskList.filter((item) => {
        return item.id != id;
      });
      this.orderDiskData.remove_disk_id.push(id);
    },
    delOldSize2(id) {
      this.oldDiskList2 = this.oldDiskList2.filter((item) => {
        return item.id != id;
      });
      this.orderDiskData.remove_disk_id.push(id);
    },
    // 删除新增的磁盘项
    delMoreDisk(id) {
      const diskData = this.moreDiskData.filter((item) => {
        return item.index != id;
      });
      this.moreDiskData = diskData.map((item, index) => {
        item.index = index + 1;
        return item;
      });
    },
    selectIpValue(val) {
      if (this.ipValue !== val) {
        this.ipValue = val;
        this.getIpPrice();
      }
    },
    // 获取附加ip价格
    getIpPrice() {
      this.ipPriceLoading = true;
      ipPrice({id: this.id, ip_num: this.ipValue}).then(async (res) => {
        if (this.isShowLevel) {
          await clientLevelAmount({
            id: this.product_id,
            amount: res.data.data.price,
          })
            .then((ress) => {
              this.ipDiscountkDisPrice = Number(ress.data.data.discount);
            })
            .catch(() => {
              this.ipDiscountkDisPrice = 0;
            });
        }
        // 开启了优惠码插件
        if (this.isShowPromo) {
          // 更新优惠码
          await applyPromoCode({
            // 开启了优惠券
            scene: "upgrade",
            product_id: this.product_id,
            amount: res.data.data.price,
            billing_cycle_time: this.hostData.billing_cycle_time,
            promo_code: "",
            host_id: this.id,
          })
            .then((resss) => {
              this.ipCodePrice = Number(resss.data.data.discount);
            })
            .catch((err) => {
              showToast(err.data.msg);
              this.ipCodePrice = 0;
            });
        }
        this.ipMoney =
          (res.data.data.price * 1000 -
            this.ipDiscountkDisPrice * 1000 -
            this.ipCodePrice * 1000) /
          1000;
        this.ipPriceLoading = false;
      });
    },
    // 提交创建磁盘
    toCreateDisk() {
      // 新增磁盘容量数组
      let newSize = [];
      this.moreDiskData.map((item) => {
        newSize.push({
          size: item.size,
          type: item.type === lang.mf_no ? "" : item.type,
        });
      });
      this.orderDiskData.add_disk = newSize;

      // 获取磁盘价格
      const params = {
        id: this.id,
        remove_disk_id: this.orderDiskData.remove_disk_id,
        add_disk: this.orderDiskData.add_disk,
      };

      // 调用生成购买磁盘订单
      diskOrder(params)
        .then((res) => {
          if (res.data.status === 200) {
            const orderId = res.data.data.id;
            this.diskOrderId = orderId;
            const amount = this.moreDiskPrice;
            this.isShowDg = false;
            this.$refs.topPayDialog.showPayDialog(orderId, amount);
          }
        })
        .catch((err) => {
          showToast(err.data.msg);
        });
    },
    changeNumStepper() {
      if (window.timer1) {
        clearTimeout(window.timer1);
        window.timer1 = null;
      }
      window.timer1 = setTimeout(() => {
        try {
          this.changeConfig();
        } catch (error) {
          console.log("error", error);
        }
      }, 800);
    },
    // 变化监听
    sliderChange(val, item) {
      const arr = [];
      item.selectList.forEach((i) => {
        arr.push([i.min_value, i.max_value]);
      });
      item.size = this.mapToRange(val, arr, item.min_value);
    },
    changeDataNum(val, item) {
      // 数据盘数量改变计算价格
      item.size = this.mapToRange(
        val,
        item.selectList[0][item.type].config,
        item.selectList[0][item.type].config[0]
      );
    },
    // 磁盘挂载
    handelMount(id) {
      this.$confirm(lang.mf_tip30)
        .then(() => {
          mount({id: this.id, disk_id: id})
            .then((res) => {
              showToast(res.data.msg);
              this.doGetDiskList();
            })
            .catch((err) => {
              showToast(err.data.msg);
            });
        })
        .catch((_) => {});
    },
    goSecurityPage() {
      location.href = "/security_group.htm";
    },
    getSafeList() {
      securityGroup({page: 1, limit: 9999}).then((res) => {
        this.safeOptions = res.data.data.list;
      });
    },
    handelSafeOpen() {
      this.safeDialogShow = true;
    },
    subAddSafe() {
      if (this.safeID === "") {
        showToast(lang.mf_tip31);
        return;
      }
      addSafe({id: this.safeID, host_id: this.id})
        .then((res) => {
          showToast(res.data.msg);
          this.safeDialogShow = false;
          this.getCloudDetail();
        })
        .catch((err) => {
          showToast(err.data.msg);
        });
    },
    // 磁盘卸载
    handelUnload(id) {
      this.$confirm(lang.mf_tip32)
        .then(() => {
          unmount({id: this.id, disk_id: id})
            .then((res) => {
              showToast(res.data.msg);
              this.doGetDiskList();
            })
            .catch((err) => {
              showToast(err.data.msg);
            });
        })
        .catch((_) => {});
    },
    mapToRange(value, rangeArray, deflute) {
      for (let i = 0; i < rangeArray.length; i++) {
        const range = rangeArray[i];
        if (value >= range[0] && value <= range[1]) {
          return value;
        }
        if (value < range[0] && i === 0) {
          return range[0];
        }
        if (value > range[1] && i === rangeArray.length - 1) {
          return range[1];
        }
        if (value > range[1] && value < rangeArray[i + 1][0]) {
          return range[1];
        }
        if (value < range[0] && value > rangeArray[i - 1][1]) {
          return range[0];
        }
      }
      return deflute; // 如果没有找到最近的区间，则返回默认最小值
    },
    // 计算订购磁盘页的价格
    getOrderDiskPrice() {
      if (this.orderTimer) {
        clearTimeout(this.orderTimer);
      }
      this.orderTimer = setTimeout(() => {
        this.diskPriceLoading = true;
        // 新增磁盘容量数组
        let newSize = [];
        this.moreDiskData.map((item) => {
          newSize.push({
            size: item.size,
            type: item.type === lang.mf_no ? "" : item.type,
          });
        });
        this.orderDiskData.add_disk = newSize;

        // 获取磁盘价格
        const params = {
          id: this.id,
          remove_disk_id: this.orderDiskData.remove_disk_id,
          add_disk: this.orderDiskData.add_disk,
        };
        diskPrice(params)
          .then(async (res) => {
            const price = Number(res.data.data.price);
            this.moreDiskPrice = price;
            if (this.isShowLevel) {
              await clientLevelAmount({
                id: this.product_id,
                amount: res.data.data.price,
              })
                .then((ress) => {
                  this.moreDiscountkDisPrice = Number(ress.data.data.discount);
                })
                .catch(() => {
                  this.moreDiscountkDisPrice = 0;
                });
            }
            // 开启了优惠码插件
            if (this.isShowPromo) {
              // 更新优惠码
              await applyPromoCode({
                // 开启了优惠券
                scene: "upgrade",
                product_id: this.product_id,
                amount: price,
                billing_cycle_time: this.hostData.billing_cycle_time,
                promo_code: "",
                host_id: this.id,
              })
                .then((resss) => {
                  this.moreCodePrice = Number(resss.data.data.discount);
                })
                .catch((err) => {
                  showToast(err.data.msg);
                  this.moreCodePrice = 0;
                });
            }
            this.moreDiskPrice =
              (price * 1000 -
                this.moreDiscountkDisPrice * 1000 -
                this.moreCodePrice * 1000) /
                1000 >
              0
                ? (price * 1000 -
                    this.moreDiscountkDisPrice * 1000 -
                    this.moreCodePrice * 1000) /
                  1000
                : 0;
            this.diskPriceLoading = false;
          })
          .catch((error) => {
            this.diskPriceLoading = false;
          });
      }, 500);
    },
    // 计算扩容磁盘页的价格
    getExpanDiskPrice() {
      if (this.orderTimer) {
        clearTimeout(this.orderTimer);
      }
      this.orderTimer = setTimeout(() => {
        this.diskPriceLoading = true;
        // 新增磁盘容量数组
        let newSize = [];
        this.oldDiskList.forEach((item) => {
          newSize.push({
            id: item.id,
            size: item.size,
          });
        });
        this.expanOrderData.resize_data_disk = newSize;

        // 获取磁盘价格
        const params = {
          id: this.id,
          resize_data_disk: this.expanOrderData.resize_data_disk,
        };
        expanPrice(params)
          .then(async (res) => {
            const price = res.data.data.price;
            this.expansionDiskPrice = price;
            if (this.isShowLevel) {
              await clientLevelAmount({
                id: this.product_id,
                amount: res.data.data.price,
              })
                .then((ress) => {
                  this.expansionDiscount = Number(ress.data.data.discount);
                })
                .catch(() => {
                  this.expansionDiscount = 0;
                });
            }
            // 开启了优惠码插件
            if (this.isShowPromo) {
              // 更新优惠码
              await applyPromoCode({
                // 开启了优惠券
                scene: "upgrade",
                product_id: this.product_id,
                amount: price,
                billing_cycle_time: this.hostData.billing_cycle_time,
                promo_code: "",
                host_id: this.id,
              })
                .then((resss) => {
                  this.expansionCodePrice = Number(resss.data.data.discount);
                })
                .catch((err) => {
                  showToast(err.data.msg);
                  this.expansionCodePrice = 0;
                });
            }
            this.expansionDiskPrice =
              (price * 1000 -
                this.moreDiscountkDisPrice * 1000 -
                this.expansionCodePrice * 1000) /
                1000 >
              0
                ? (price * 1000 -
                    this.moreDiscountkDisPrice * 1000 -
                    this.expansionCodePrice * 1000) /
                  1000
                : 0;
            this.diskPriceLoading = false;
          })
          .catch((err) => {
            this.expansionDiskPrice = 0.0;
            this.diskPriceLoading = false;
          });
      }, 500);
    },
    // 打开新增Ip弹窗
    showIpDia() {
      getLineConfig({
        id: this.product_id,
        line_id: this.cloudData.line.id,
      }).then((res) => {
        if (res.data.data.ip && res.data.data.ip.length > 0) {
          this.ipValueData = res.data.data.ip.filter((item) => {
            return item.value !== this.netDataList.length - 1;
          });
          this.ipValue = this.ipValueData[0].value;
          this.getIpPrice();
          this.isShowIp = true;
        } else {
          showToast(lang.mf_tip33);
        }
      });
    },
    initVpcNet() {
      this.vpcParams.page = 1;
      this.vpcDataList = [];
      this.vpcLoading = true;
      this.getVpcNetwork();
    },
    // 获取vpc网络列表
    getVpcNetwork() {
      this.vpcLoading = true;
      vpcNetwork({id: this.id, ...this.vpcParams})
        .then((res) => {
          this.vpcDataList = this.vpcDataList.concat(res.data.data.list);
          this.vpcParams.total = res.data.data.count;
          this.vpcParams.page++;
          this.vpcLoading = false;
          if (this.vpcDataList.length >= res.data.data.count) {
            this.vpcFinished = true;
          } else {
            this.vpcFinished = false;
          }
        })
        .catch((err) => {
          this.vpcLoading = false;
          this.vpcFinished = true;
          showToast(err.msg.data);
        });
    },
    handDelVpc(id) {
      this.$confirm(lang.mf_tip34)
        .then(() => {
          delVpc({id: this.id, vpc_network_id: id})
            .then((res) => {
              showToast(res.data.msg);
              this.initVpcNet();
            })
            .catch((err) => {
              showToast(err.data.msg);
            });
        })
        .catch((_) => {});
    },
    handelAddVpc() {
      this.vpcName = "VPC-" + this.generateRandomString(8);
      this.isShowAddVpc = true;
    },
    // 随机生成字符串
    generateRandomString(length) {
      let result = "";
      const characters =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
      const charactersLength = characters.length;
      for (let i = 0; i < length; i++) {
        result += characters.charAt(
          Math.floor(Math.random() * charactersLength)
        );
      }
      return result;
    },
    subAddVpc() {
      addVpcNet({
        id: this.id,
        name: this.vpcName,
        ips: this.plan_way === 1 ? this.ips : "",
      })
        .then((res) => {
          showToast(res.data.msg);
          this.isShowAddVpc = false;
          this.initVpcNet();
        })
        .catch((err) => {
          showToast(err.data.msg);
        });
    },
    // 关闭扩容弹窗
    krClose() {
      this.isShowExpansion = false;
    },
    // 关闭新增IP弹窗
    ipClose() {
      this.isShowIp = false;
      this.ipValue = null;
    },
    handelEngine(row) {
      this.curEngineId = row.id;
      this.isShowengine = true;
      this.remoteMethod("");
    },
    engineClose() {
      this.isShowengine = false;
    },
    safeClose() {
      this.safeDialogShow = false;
    },
    addVpcClose() {
      this.plan_way = 0;
      this.isShowAddVpc = false;
    },
    subAddEngine() {
      if (this.isSubmitEngine) {
        return;
      }
      this.isSubmitEngine = true;
      changeVpc({id: this.engineID, vpc_network_id: this.curEngineId})
        .then((res) => {
          showToast(res.data.msg);
          this.isShowengine = false;
          this.isSubmitEngine = false;
          this.initVpcNet();
        })
        .catch((err) => {
          this.isSubmitEngine = false;
          showToast(err.data.msg);
        });
    },
    remoteMethod(query) {
      this.engineID = "";
      this.engineSearchLoading = true;
      if (query !== "") {
        this.productParams.keywords = query;
      } else {
        this.productParams.keywords = "";
      }
      cloudList(this.productParams).then((res) => {
        this.productOptions = res.data.data.list;
        this.engineSearchLoading = false;
      });
    },
    // 提交新增IP
    subAddIp() {
      ipOrder({id: this.id, ip_num: this.ipValue})
        .then((res) => {
          const orderId = res.data.data.id;
          this.isShowIp = false;
          this.$refs.topPayDialog.showPayDialog(orderId);
        })
        .catch((err) => {
          showToast(err.data.msg);
        });
    },
    // 提交扩容
    subExpansion() {
      let newSize = [];
      this.oldDiskList.forEach((item) => {
        newSize.push({
          id: item.id,
          size: item.size,
        });
      });
      this.expanOrderData.resize_data_disk = newSize;

      // 获取磁盘价格
      const params = {
        id: this.id,
        resize_data_disk: this.expanOrderData.resize_data_disk,
      };
      // 调用扩容接口
      diskExpanOrder(params)
        .then((res) => {
          this.diskOrderId = res.data.data.id;
          const amount = this.expansionDiskPrice;
          this.isShowExpansion = false;
          this.$refs.topPayDialog.showPayDialog(this.diskOrderId, amount);
        })
        .catch((err) => {
          showToast(err.data.msg);
        });
    },
    ipListInit() {
      this.netDataList = [];
      this.netParams.page = 1;
      this.netLoading = true;
      this.getIpList();
    },
    // 网络开始
    // 获取ip列表
    getIpList() {
      const params = {
        id: this.id,
        ...this.netParams,
      };
      this.netLoading = true;
      ipList(params).then((res) => {
        if (res.data.status === 200) {
          this.netParams.total = res.data.data.count;
          this.netDataList = res.data.data.list;
        }
        this.netLoading = false;
      });
    },

    // 获取网络流量
    doGetFlow() {
      const params = {
        id: this.id,
      };
      getFlow(params).then((res) => {
        if (res.data.status === 200) {
          this.flowData = res.data.data;
        }
      });
    },
    initLog() {
      this.logDataList = [];
      this.logParams.page = 1;
      this.logLoading = true;
      this.getLogList();
    },
    getLogList() {
      this.logLoading = true;
      const params = {
        ...this.logParams,
        id: this.id,
      };
      getLog(params)
        .then((res) => {
          if (res.data.status === 200) {
            this.logParams.total = res.data.data.count;
            this.logDataList = this.logDataList.concat(res.data.data.list);
            this.logParams.page++;
            this.logLoading = false;
            if (this.logDataList.length >= res.data.data.count) {
              this.logFinished = true;
            } else {
              this.logFinished = false;
            }
          }
        })
        .catch((error) => {
          this.logLoading = false;
        });
    },
    // 备份与快照 开始
    // 备份列表
    getBackupList() {
      this.backLoading = true;
      this.snapLoading = true;
      const params = {
        id: this.id,
        ...this.params1,
      };
      backupList(params)
        .then((res) => {
          if (res.data.status === 200) {
            const temp = JSON.parse(JSON.stringify(res.data.data));
            this.allDiskList = temp.disk || [];
            this.backupNum = temp.backup_num;
            this.snapNum = temp.snap_num;
            this.dataList1 = (temp.list || []).filter(
              (item) => item.type === "backup"
            );
            this.dataList2 = (temp.list || []).filter(
              (item) => item.type === "snap"
            );
            this.backLoading = false;
            this.snapLoading = false;
            this.params1.total = res.data.data.count;
            this.backFinshed = true;
            this.snapFinshed = true;
          }
        })
        .catch((err) => {
          this.backLoading = false;
          this.snapLoading = false;
        });
    },
    // 快照列表
    getSnapshotList() {
      this.snapLoading = true;
      const params = {
        id: this.id,
        ...this.params2,
      };
      snapshotList(params)
        .then((res) => {
          if (res.data.status === 200) {
            const temp = JSON.parse(JSON.stringify(res.data.data.list));
            this.dataList1 = temp.filter((item) => item.type === "backup");
            this.dataList2 = temp.filter((item) => item.type === "snap");
            this.params2.total = res.data.data.count;
          }
          this.snapLoading = false;
        })
        .catch((err) => {
          this.snapLoading = false;
        });
    },
    // 展示创建备份、快照弹窗
    showCreateBs(type) {
      if (type == "back") {
        this.isBs = true;
      } else {
        this.isBs = false;
      }
      this.errText = "";
      this.createBsData = {
        id: this.id,
        name: "",
        disk_id: this.diskList[0] ? this.diskList[0].id : "",
      };
      this.isShwoCreateBs = true;
    },
    // 创建备份/生成快照弹窗 关闭
    bsCgClose() {
      this.isShwoCreateBs = false;
    },
    // 创建备份、快照弹窗提交
    subCgBs() {
      const data = this.createBsData;
      let isPass = true;
      if (!data.name) {
        isPass = false;
        this.errText = lang.appstore_text160;
        return false;
      }
      if (!data.disk_id) {
        isPass = false;
        this.errText = lang.common_cloud_text70;
        return false;
      }
      if (isPass) {
        this.errText = "";
        const params = {
          ...this.createBsData,
        };
        this.cgbsLoading = true;
        if (this.isBs) {
          // 调用创建备份接口
          createBackup(params)
            .then((res) => {
              if (res.data.status === 200) {
                this.getBackupList();
              }
              this.isShwoCreateBs = false;
              showToast(res.data.msg);
              this.cgbsLoading = false;
            })
            .catch((err) => {
              showToast(err.data.msg);
              this.cgbsLoading = false;
            });
        } else {
          // 调用创建磁盘接口
          createSnapshot(params)
            .then((res) => {
              if (res.data.status === 200) {
                this.getBackupList();
              }
              showToast(res.data.msg);
              this.isShwoCreateBs = false;
              this.cgbsLoading = false;
            })
            .catch((err) => {
              showToast(err.data.msg);
              this.cgbsLoading = false;
            });
        }
      }
    },
    // 还原快照、备份 弹窗关闭
    bshyClose() {
      this.isShowhyBs = false;
      this.loading3 = false;
    },
    // 还原备份、快照 提交
    subhyBs() {
      this.loading3 = true;
      if (this.isBs) {
        // 调用还原备份
        const params = {
          id: this.id,
          backup_id: this.restoreData.restoreId,
        };
        restoreBackup(params)
          .then((res) => {
            if (res.data.status === 200) {
              showToast(res.data.msg);
              this.isShowhyBs = false;
            }
            this.loading3 = false;
          })
          .catch((err) => {
            showToast(err.data.msg);
            this.loading3 = false;
          });
      } else {
        // 调用还原快照
        const params = {
          id: this.id,
          snapshot_id: this.restoreData.restoreId,
        };
        restoreSnapshot(params)
          .then((res) => {
            if (res.data.status === 200) {
              showToast(res.data.msg);
              this.isShowhyBs = false;
            }
            this.loading3 = false;
          })
          .catch((err) => {
            showToast(err.data.msg);
            this.loading3 = false;
          });
      }
    },
    // 关闭 删除备份、快照弹窗显示
    delBsClose() {
      this.isShowDelBs = false;
      this.loading4 = false;
    },
    // 删除备份、快照弹窗 提交
    subDelBs() {
      this.loading4 = true;
      if (this.isBs) {
        // 调用删除备份
        const params = {
          id: this.id,
          backup_id: this.delData.delId,
        };
        delBackup(params)
          .then((res) => {
            if (res.data.status === 200) {
              showToast(res.data.msg);
              this.isShowDelBs = false;
              this.getBackupList();
            }
            this.loading4 = false;
          })
          .catch((err) => {
            showToast(err.data.msg);
            this.loading4 = false;
          });
      } else {
        // 调用删除快照
        const params = {
          id: this.id,
          snapshot_id: this.delData.delId,
        };
        delSnapshot(params)
          .then((res) => {
            if (res.data.status === 200) {
              showToast(res.data.msg);
              this.isShowDelBs = false;
              this.getBackupList();
            }
            this.loading4 = false;
          })
          .catch((err) => {
            showToast(err.data.msg);
            this.loading4 = false;
          });
      }
    },
    // 还原快照、备份 弹窗显示
    showhyBs(type, item) {
      if (type == "back") {
        this.isBs = true;
      } else {
        this.isBs = false;
      }
      this.restoreData.restoreId = item.id;
      this.restoreData.time = item.create_time;
      this.restoreData.cloud_name = this.hostData.name;
      this.isShowhyBs = true;
    },
    // 删除备份、快照弹窗显示
    showDelBs(type, item) {
      if (type == "back") {
        this.isBs = true;
      } else {
        this.isBs = false;
      }
      this.delData.delId = item.id;
      this.delData.time = item.create_time;
      this.delData.name = item.name;
      this.delData.cloud_name = this.hostData.name;
      this.isShowDelBs = true;
    },
    // 开启备份/快照 弹窗
    openBs(type) {
      if (type == "back") {
        this.isBs = true;
      } else {
        this.isBs = false;
      }
      this.bsData.backNum = this.backup_config[0]
        ? this.backup_config[0].num
        : "";
      this.bsData.snapNum = this.snap_config[0] ? this.snap_config[0].num : "";
      this.isShowOpenBs = true;
      this.getBsPrice();
    },
    // 关闭 开启备份/快照弹窗
    bsopenDgClose() {
      this.isShowOpenBs = false;
    },
    // 开启备份、弹窗提交
    bsopenSub() {
      const params = {
        id: this.id,
        type: this.isBs ? "backup" : "snap",
        num: this.isBs ? this.bsData.backNum : this.bsData.snapNum,
      };
      backupOrder(params)
        .then((res) => {
          if (res.data.status === 200) {
            const orderId = res.data.data.id;
            this.bsOrderId = orderId;
            const amount = this.bsData.money;
            this.isShowOpenBs = false;
            this.$refs.topPayDialog.showPayDialog(orderId, amount);
          }
        })
        .catch((err) => {
          showToast(err.data.msg);
        });
    },
    bsSelectChange() {
      this.getBsPrice();
    },
    // 获取开启备份/快照的价格
    getBsPrice() {
      this.bsDataLoading = true;
      const params = {
        id: this.id,
        type: this.isBs ? "backup" : "snap",
        num: this.isBs ? this.bsData.backNum : this.bsData.snapNum,
      };
      backupConfig(params)
        .then(async (res) => {
          if (res.data.status === 200) {
            const price = Number(res.data.data.price);
            this.bsData.money = price;
            if (this.isShowLevel) {
              clientLevelAmount({
                id: this.product_id,
                amount: res.data.data.price,
              })
                .then((ress) => {
                  this.bsData.moneyDiscount = Number(ress.data.data.discount);
                })
                .catch(() => {
                  this.bsData.moneyDiscount = 0;
                });
            }
            // 开启了优惠码插件
            if (this.isShowPromo) {
              // 更新优惠码
              await applyPromoCode({
                // 开启了优惠券
                scene: "upgrade",
                product_id: this.product_id,
                amount: price,
                billing_cycle_time: this.hostData.billing_cycle_time,
                promo_code: "",
                host_id: this.id,
              })
                .then((resss) => {
                  this.bsData.codePrice = Number(resss.data.data.discount);
                })
                .catch((err) => {
                  showToast(err.data.msg);
                  this.bsData.codePrice = 0;
                });
            }
            this.bsData.money =
              (price * 1000 -
                this.bsData.moneyDiscount * 1000 -
                this.bsData.codePrice * 1000) /
                1000 >
              0
                ? (price * 1000 -
                    this.bsData.moneyDiscount * 1000 -
                    this.bsData.codePrice * 1000) /
                  1000
                : 0;
            this.bsDataLoading = false;
          }
        })
        .catch((error) => {
          this.bsDataLoading = false;
        });
    },
    // 统计图表开始
    // 获取cpu用量数据
    getCpuList() {
      this.echartLoading1 = true;
      const params = {
        id: this.id,
        start: this.startTime,
        end: new Date().getTime(),
        type: "cpu",
      };
      chartList(params)
        .then((res) => {
          if (res.data.status === 200) {
            const list = res.data.data.list[0];
            let x = [];
            let y = [];
            // list.forEach(item => {
            //   x.push(formateDate(item.time * 1000))
            //   y.push((item.value).toFixed(2))
            // });
            list.forEach((item) => {
              x.push(item.time);
              y.push(item.value.toFixed(2));
            });

            const cpuOption = {
              title: {
                text: lang.common_cloud_text73,
              },
              tooltip: {
                show: true,
                trigger: "axis",
              },
              grid: {
                left: "6%",
                right: "4%",
                bottom: "6%",
                containLabel: true,
              },
              xAxis: {
                type: "category",
                boundaryGap: false,
                data: x,
                showSymbol: false,
              },
              yAxis: {
                type: "value",
              },
              series: [
                {
                  name: lang.common_cloud_text74,
                  data: y,
                  type: "line",
                  showSymbol: false,
                  symbol: "none",
                },
              ],
            };

            var CpuChart = echarts.init(document.getElementById("cpu-echart"));
            CpuChart.setOption(cpuOption);
          }
          this.echartLoading1 = false;
        })
        .catch((err) => {
          this.echartLoading1 = false;
        });
    },
    // 获取磁盘IO
    getBwList() {
      this.echartLoading2 = true;
      const params = {
        id: this.id,
        start: this.startTime,
        end: new Date().getTime(),
        type: "disk",
      };
      chartList(params)
        .then((res) => {
          if (res.data.status === 200) {
            const list = res.data.data.list[0];
            const list1 = res.data.data.list[1];
            if (list.length > 0) {
              this.isShowChart = true;
            }
            let xAxis = [];
            let yAxis = [];
            let yAxis2 = [];

            list.forEach((item, index) => {
              xAxis.push(item.time);
              yAxis.push(item.value);
              yAxis2.push(list1[index].value);
            });

            const options = {
              title: {
                text: lang.common_cloud_text289,
              },
              tooltip: {
                show: true,
                trigger: "axis",
              },
              grid: {
                left: "6%",
                right: "4%",
                bottom: "6%",
                containLabel: true,
              },
              xAxis: {
                type: "category",
                boundaryGap: false,
                data: xAxis,
                showSymbol: false,
              },
              yAxis: {
                type: "value",
              },
              series: [
                {
                  name: lang.common_cloud_text290,
                  data: yAxis,
                  type: "line",
                  symbol: "none",
                },
                {
                  name: lang.common_cloud_text291,
                  data: yAxis2,
                  type: "line",
                  symbol: "none",
                },
              ],
            };

            var bwChart = echarts.init(document.getElementById("bw-echart"));

            bwChart.setOption(options);
          }
          this.echartLoading2 = false;
        })
        .catch((err) => {
          this.echartLoading2 = false;
        });
    },
    // 获取内存用量
    getMemoryList() {
      this.echartLoading4 = true;
      const params = {
        id: this.id,
        start: this.startTime,
        end: new Date().getTime(),
        type: "memory",
      };
      chartList(params)
        .then((res) => {
          if (res.data.status === 200) {
            const list = res.data.data.list[0];
            const list1 = res.data.data.list[1];
            const label = res.data.data.label;
            let xAxis = [];
            let yAxis = [];
            let yAxis2 = [];

            list.forEach((item, index) => {
              xAxis.push(item.time);
              yAxis.push(item.value);
              yAxis2.push(list1[index].value);
            });
            const options = {
              title: {
                text: lang.common_cloud_text83,
              },
              tooltip: {
                show: true,
                trigger: "axis",
              },
              grid: {
                left: "6%",
                right: "4%",
                bottom: "6%",
                containLabel: true,
              },
              xAxis: {
                type: "category",
                boundaryGap: false,
                data: xAxis,
              },
              yAxis: {
                type: "value",
              },
              series: [
                {
                  name: label[1],
                  data: yAxis2,
                  type: "bar",
                },
                {
                  name: label[0],
                  data: yAxis,
                  type: "bar",
                  itemStyle: {
                    normal: {
                      color: "#ccc",
                    },
                  },
                  barGap: "-100%",
                  z: "-1",
                },
              ],
            };
            var memoryChart = echarts.init(
              document.getElementById("memory-echart")
            );
            memoryChart.setOption(options);
          }
          this.echartLoading4 = false;
        })
        .catch((err) => {
          this.echartLoading4 = false;
        });
    },
    // 网卡
    getDiskLIoList() {
      this.echartLoading3 = true;
      const params = {
        id: this.id,
        start: this.startTime,
        end: new Date().getTime(),
        type: "flow",
      };

      chartList(params)
        .then((res) => {
          if (res.data.status === 200) {
            const list = res.data.data.list[0];
            const list1 = res.data.data.list[1];

            let xAxis = [];
            let yAxis = [];
            let yAxis2 = [];
            let yAxis3 = [];
            let yAxis4 = [];

            list.forEach((item, index) => {
              xAxis.push(item.time);
              yAxis.push(item.value);
              yAxis2.push(list1[index].value);
            });

            const options = {
              title: {
                text: lang.common_cloud_text292,
              },
              tooltip: {
                show: true,
                trigger: "axis",
              },
              grid: {
                left: "6%",
                right: "6%",
                bottom: "5%",
                containLabel: true,
              },
              xAxis: {
                type: "category",
                boundaryGap: false,
                data: xAxis,
              },
              yAxis: {
                // name: "单位（B/s）",
                type: "value",
              },
              series: [
                {
                  name: lang.common_cloud_text293,
                  data: yAxis,
                  type: "line",
                  symbol: "none",
                },
                {
                  name: lang.common_cloud_text294,
                  data: yAxis2,
                  type: "line",
                  symbol: "none",
                },
              ],
            };

            var diskIoChart = echarts.init(
              document.getElementById("disk-io-echart")
            );
            var bw2Chart = echarts.init(document.getElementById("bw2-echart"));
            diskIoChart.setOption(options);
            bw2Chart.setOption(options);
          }
          this.echartLoading3 = false;
        })
        .catch((err) => {
          this.echartLoading3 = false;
        });
    },

    getstarttime(type) {
      // 1: 过去24小时 2：过去三天 3：过去七天
      let nowtime = parseInt(new Date().getTime() / 1000);
      if (type == 1) {
        this.startTime = nowtime - 24 * 60 * 60;
      } else if (type == 2) {
        this.startTime = nowtime - 24 * 60 * 60 * 3;
      } else if (type == 3) {
        this.startTime = nowtime - 24 * 60 * 60 * 7;
      }
    },
    // 时间选择框
    chartSelectChange(e) {
      // 计算开始时间
      this.getstarttime(e[0].value);
      // 重新拉取图表数据
      this.getChartList();
    },
    copyPass(text) {
      if (navigator.clipboard && window.isSecureContext) {
        // navigator clipboard 向剪贴板写文本
        showToast(lang.index_text32);
        return navigator.clipboard.writeText(text);
      } else {
        // 创建text area
        const textArea = document.createElement("textarea");
        textArea.value = text;
        // 使text area不在viewport，同时设置不可见
        document.body.appendChild(textArea);
        // textArea.focus()
        textArea.select();
        showToast(lang.index_text32);
        return new Promise((res, rej) => {
          // 执行复制命令并移除文本框
          document.execCommand("copy") ? res() : rej();
          textArea.remove();
        });
      }
    },
    // 显示电源操作确认弹窗
    showPowerDialog() {
      this.powerTitle = this.powerList.filter(
        (item) => item.func === this.powerStatus
      )[0].name;
      this.powerType = this.powerStatus;
      this.isShowPowerChange = true;
    },
  },
});
window.directiveInfo.forEach((item) => {
  app2.directive(item.name, item.fn);
});
app2.use(vant).mount("#template2");
