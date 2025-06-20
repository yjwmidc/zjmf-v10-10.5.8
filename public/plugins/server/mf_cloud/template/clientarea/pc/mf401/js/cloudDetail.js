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

const template = document.getElementById("product_detail_cloud");
Vue.prototype.lang = Object.assign(window.lang, window.module_lang);
const clientOperateVue = new Vue({
  components: {
    asideMenu,
    topMenu,
    payDialog,
    pagination,
    discountCode,
    cashCoupon,
    cashBack,
    flowPacket,
    safeConfirm,
    captchaDialog,
    hostStatus,
    autoRenew,
    ipDefase,
  },
  created() {
    // 获取产品id
    const params = getUrlParams();
    this.id = params.id * 1;
    this.showUp = params.showUp == 1;
    this.showIp = params.ip;

    // 获取通用信息
    this.getCommonData();
    // 获取IP详情，左上角多ip显示
    this.getIpDetail();
    // 获取产品详情
    this.getHostDetail();
    // 获取实例详情
    // this.getCloudDetail();
    // 获取实例状态
    this.getCloudStatus();

    this.getSshKey();
    // 获取cpu 使用信息
    this.getRealData();
    // 获取救援模式状态
    this.getRemoteInfo();
    // 获取该实例的磁盘
    this.doGetDiskList();
    this.getstarttime(1);
    this.getRenewPrice();
  },
  mixins: [mixin],
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
      this.isRefundPlugin = true;
      this.getRefundMsg();
    }
    if (arr.includes("IdcsmartRenew")) {
      // 开启了代金券
      this.isRenewPlugin = true;
      this.getRenewStatus();
    }
    window.addEventListener("resize", this.handleResize);
  },
  beforeDestroy() {
    window.removeEventListener("resize", this.handleResize);
  },
  destroyed() {
    clearInterval(this.codeTimer);
    this.codeTimer = null;
  },
  updated() {
    // // 关闭loading
    // document.getElementById('mainLoading').style.display = 'none';
    // document.getElementById('product_detail_cloud').style.display = 'block'
    // document.getElementsByClassName('product_detail_cloud')[0].style.display = 'block'
  },
  computed: {
    calcDelNum () {
      return type => {
        let num = 0;
        if (type === 'ipv4') {
          if (this.ipValue < this.cloudData.ip_num) {
            num = this.cloudData.ip_num - this.ipValue;
          }
        } else {
          if (this.ipv6Value < this.cloudData.ipv6_num) {
            num = this.cloudData.ipv6_num - this.ipv6Value;
          }
        }
        return num;
      };
    },
    isShowAppendItem () {
      return item => {
        const bol = item.selectList.map(sub => sub.value).includes(this.freeDataObj.size)
        return item.is_free === 1 && !bol;
      }
    },
    isShowSafeOpt() {
      return this.cloudData?.security_group?.id || 0;
    },
    // 处理右上角更多操作
    calcOptList() {
      if (this.cloudConfig.manual_resource_control_mode !== "not_support") {
        if (this.isRescue) {
          return this.otherOptArr.filter((item) => item.value !== "rescue");
        } else {
          return this.otherOptArr.filter((item) => item.value !== "quitRescue");
        }
      } else {
        // 手动资源不支持控制方式的时候，同时显示救援，和退出救援
        return this.otherOptArr;
      }
    },
    calcImageList() {
      let temp = JSON.parse(JSON.stringify(this.osData));
      /* 限制只针对自定义，不支持套餐 */
      if (this.configLimitList.length > 0 && !this.isPackage) {
        let tempLimit = JSON.parse(JSON.stringify(this.configLimitList))
          .reduce((all, cur) => {
            if (cur.result.image) {
              all.push(cur);
            }
            return all;
          }, [])
          .filter(
            (item) =>
              (!item.rule.data_center ||
                (item.rule.data_center.opt === "eq"
                  ? item.rule.data_center.id.includes(
                      this.params.data_center_id
                    )
                  : !item.rule.data_center.id.includes(
                      this.params.data_center_id
                    ))) &&
              (!item.rule.cpu ||
                (item.rule.cpu.opt === "eq"
                  ? item.rule.cpu.value.includes(this.params.cpu)
                  : !item.rule.cpu.value.includes(this.params.cpu))) &&
              (!item.rule.memory ||
                (item.rule.memory.opt === "eq"
                  ? this.handleRange(item.rule, "memory")
                  : !this.handleRange(item.rule, "memory")))
          );
        const allImageId = this.osData.reduce((all, cur) => {
          all.push(...cur.image.map((item) => item.id));
          return all;
        }, []);
        const imageId = tempLimit.reduce((all, cur) => {
          // 改版过后同规则多条数据求并集
          const tempImage = cur.result.image.reduce((sum, pre) => {
            sum.push(...pre.id);
            return sum;
          }, []);
          if (cur.result.image[0].opt === "eq") {
            all.push(tempImage);
          } else {
            let result = allImageId.filter((item) => !tempImage.includes(item));
            all.push(result);
          }
          return all;
        }, []);
        // 求交集
        let resultImage = this.handleMixed(...imageId);
        if (resultImage.length === 0) {
          resultImage = allImageId;
        }
        if (tempLimit.length > 0) {
          temp = temp
            .map((item) => {
              item.image = item.image.filter((el) =>
                resultImage.includes(el.id)
              );
              return item;
            })
            .filter((item) => item.image.length > 0);
        }
      }
      return temp;
    },
    showFlowBw() {
      if (!this.lineDetail.flow && this.lineDetail.bill_type !== "bw") {
        return;
      }
      return this.lineDetail.flow.filter(
        (item) => item.value === this.params.flow
      )[0]?.other_config?.out_bw;
    },
    calcProtocol() {
      return (protocol) => {
        return this.protocolArr.filter((item) => item.value === protocol)[0]
          ?.label;
      };
    },
    calcNat() {
      if (this.cloudData.nat_acl_limit && this.cloudData.nat_web_limit) {
        return `${lang.nat_acl}${lang.nat_web}`;
      } else if (
        this.cloudData.nat_acl_limit &&
        !this.cloudData.nat_web_limit
      ) {
        return lang.nat_acl;
      } else {
        return lang.nat_web;
      }
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
    calcCpuList() {
      if (this.activeName === "fast") {
        return;
      }
      if (this.configLimitList.length === 0) {
        this.params.cpu = this.cpuList[0]?.value;
        return this.cpuList;
      }
      // 1.找到结果有关于cpu的限制
      const temp = JSON.parse(JSON.stringify(this.configLimitList))
        .reduce((all, cur) => {
          if (cur.result.cpu) {
            all.push(cur);
          }
          return all;
        }, [])
        .filter(
          // 2.筛选当前配置全部符合条件的限制
          (item) =>
            (!item.rule.data_center ||
              (item.rule.data_center.opt === "eq"
                ? item.rule.data_center.id.includes(this.params.data_center_id)
                : !item.rule.data_center.id.includes(
                    this.params.data_center_id
                  ))) &&
            (!item.rule.memory ||
              (item.rule.memory.opt === "eq"
                ? this.handleRange(item.rule, "memory")
                : !this.handleRange(item.rule, "memory"))) &&
            (!item.rule.image ||
              (item.rule.image.opt === "eq"
                ? item.rule.image.id.includes(this.params.image_id)
                : !item.rule.image.id.includes(this.params.image_id)))
        );
      let temCpu = [];
      if (temp.length > 0) {
        // 结果求交集
        let cpuArr = temp.reduce((all, cur) => {
          const tempCpu = cur.result.cpu.reduce((sum, pre) => {
            sum.push(...pre.value);
            return sum;
          }, []);
          if (cur.result.cpu[0].opt === "eq") {
            all.push(tempCpu);
          } else {
            const allCpu = this.cpuList.reduce((all, cur) => {
              all.push(cur.value);
              return all;
            }, []);
            const result = allCpu.filter((item) => !tempCpu.includes(item));
            all.push(result);
          }
          return all;
        }, []);
        cpuArr = Array.from(new Set(cpuArr));
        cpuOpt = this.handleMixed(...cpuArr);
        if (cpuOpt.length === 0) {
          // 没有交集的时候取全部
          temCpu = this.cpuList;
          this.params.cpu = temCpu[0]?.value * 1;
        } else {
          temCpu = this.cpuList.filter((item) => {
            return Array.from(new Set(cpuOpt)).includes(item.value);
          });
        }
      } else {
        temCpu = this.cpuList;
      }
      return temCpu;
    },
    calaMemoryList() {
      // 计算可选内存，根据 cpu + 区域
      if (this.activeName === "fast") {
        return;
      }
      if (this.configLimitList.length === 0) {
        if (this.memoryList[0]?.type === "radio") {
          return this.memoryList;
        } else {
          this.memoryTip = this.createTip(this.memory_arr);
          this.memMarks = this.createMarks(this.memory_arr); // data 原数据，目标marks
          return this.memory_arr;
        }
      }
      let temp = JSON.parse(JSON.stringify(this.configLimitList))
        .reduce((all, cur) => {
          if (cur.result.memory) {
            all.push(cur);
          }
          return all;
        }, [])
        .filter((item) => {
          return (
            (!item.rule.data_center ||
              (item.rule.data_center.opt === "eq"
                ? item.rule.data_center.id.includes(this.params.data_center_id)
                : !item.rule.data_center.id.includes(
                    this.params.data_center_id
                  ))) &&
            (!item.rule.cpu ||
              (item.rule.cpu.opt === "eq"
                ? item.rule.cpu.value.includes(this.params.cpu)
                : !item.rule.cpu.value.includes(this.params.cpu))) &&
            (!item.rule.image ||
              (item.rule.image.opt === "eq"
                ? item.rule.image.id.includes(this.params.image_id)
                : !item.rule.image.id.includes(this.params.image_id)))
          );
        });
      let ruleResult = [];
      if (temp.length === 0) {
        if (this.memoryList[0]?.type === "radio") {
          return this.memoryList;
        } else {
          this.memoryTip = this.createTip(this.memory_arr);
          this.memMarks = this.createMarks(this.memory_arr); // data 原数据，目标marks
          return this.memory_arr;
        }
      } else {
        ruleResult = temp;
      }
      // 内存原始范围
      let originmemory_arr = [];
      if (this.memoryList[0]?.type === "radio") {
        originmemory_arr = this.memoryList.map((item) => item.value);
      } else {
        this.memoryList.forEach((item) => {
          originmemory_arr.push(
            ...this.createArr([item.min_value, item.max_value])
          );
        });
      }
      // 最小，最大值求交集
      const memoryMax = this.memory_arr[this.memory_arr.length - 1];
      let memory_arr = ruleResult.reduce((all, cur) => {
        // 根据 eq,neq判断是否取反
        let radioArr = [],
          rangeArr = [],
          min = "",
          max = "";
        if (this.memoryList[0]?.type === "radio") {
          radioArr = Array.from(
            new Set(
              cur.result.memory.reduce((sum, pre) => {
                sum.push(...pre.value);
                return sum;
              }, [])
            )
          );
        } else {
          rangeArr = Array.from(
            new Set(
              cur.result.memory.reduce((sum, pre) => {
                sum.push(pre.min, pre.max);
                return sum;
              }, [])
            )
          ).sort((a, b) => a - b);
          min = rangeArr[0] === "" ? rangeArr[1] : rangeArr[0];
          max =
            rangeArr[rangeArr.length - 1] > memoryMax
              ? memoryMax
              : rangeArr[rangeArr.length - 1];
        }
        if (cur.result.memory[0].opt === "eq") {
          if (this.memoryList[0]?.type === "radio") {
            all.push(radioArr);
          } else {
            // 内部求并集
            let _temp = [];
            cur.result.memory.forEach((m) => {
              _temp.push(
                ...this.createArr([
                  m.min * 1,
                  m.max === "" ? memoryMax : m.max * 1,
                ])
              );
            });
            all.push(_temp);
          }
        } else {
          let result = [],
            _temp = [];
          if (this.memoryList[0]?.type === "radio") {
            _temp = radioArr;
          } else {
            cur.result.memory.forEach((m) => {
              _temp.push(
                ...this.createArr([
                  m.min * 1,
                  m.max === "" ? memoryMax : m.max * 1,
                ])
              );
            });
          }
          result = this.memory_arr.filter((item) => !_temp.includes(item));
          all.push(result);
        }
        return all;
      }, []);
      let filterMemory = [];
      let memoryOpt = this.handleMixed(...memory_arr);
      if (memoryOpt.length === 0) {
        memoryOpt = this.memory_arr;
      }
      if (this.memoryList[0]?.type === "radio") {
        originmemory_arr = originmemory_arr.filter((item) =>
          memoryOpt.includes(item)
        );
        filterMemory = this.memoryList.filter((item) =>
          originmemory_arr.includes(item.value)
        );
      } else {
        filterMemory = memoryOpt.filter((item) =>
          originmemory_arr.includes(item)
        );
        this.memoryTip = this.createTip(filterMemory);
      }
      return filterMemory;
    },
    showRenewPrice() {
      let p = this.hostData.renew_amount;
      this.renewPriceList.forEach((item) => {
        if (
          item.billing_cycle === this.hostData.billing_cycle_name &&
          this.hostData.renew_amount * 1 < item.price * 1
        ) {
          p = item.price * 1;
        }
      });
      return p;
    },
  },
  watch: {
    "singleForm.protocol"(val) {
      switch (val) {
        case "ssh":
          return (this.singleForm.port = "22");
        case "telnet":
          return (this.singleForm.port = "23");
        case "http":
          return (this.singleForm.port = "80");
        case "https":
          return (this.singleForm.port = "443");
        case "mssql":
          return (this.singleForm.port = "1433");
        case "oracle":
          return (this.singleForm.port = "1521");
        case "mysql":
          return (this.singleForm.port = "3306");
        case "rdp":
          return (this.singleForm.port = "3389");
        case "postgresql":
          return (this.singleForm.port = "5432");
        case "redis":
          return (this.singleForm.port = "6379");
        case "tcp":
        case "udp":
          return (this.singleForm.port = "");
        default:
          return (this.singleForm.port = "1-65535");
      }
    },
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
    "reinstallData.osGroupId"(id) {
      const curGroupName = this.osData.filter((item) => item.id === id)[0]
        ?.name;
      if (curGroupName === "Windows") {
        if (this.configObj.rand_ssh_port !== 2) {
          this.reinstallData.port = 3389;
        } else {
          this.reinstallData.port = this.configObj.rand_ssh_port_windows;
        }
      } else {
        if (this.configObj.rand_ssh_port !== 2) {
          this.reinstallData.port = 22;
        } else {
          this.reinstallData.port = this.configObj.rand_ssh_port_linux;
        }
      }
    },
  },
  data() {
    // 验证规则
    const validatePort = (rule, value, callback) => {
      if (value === "") {
        callback(
          new Error(`${lang.placeholder_pre1}${lang.common_cloud_label13}`)
        );
      } else {
        const reg = /^[0-9-]*$/;
        if (reg.test(value)) {
          callback();
        } else {
          callback(
            new Error(
              `${lang.placeholder_pre1}${lang.security_tip8}${lang.common_cloud_label13}`
            )
          );
        }
      }
    };
    const validatIp = (rule, value, callback) => {
      if (value === "") {
        callback(new Error(`${lang.placeholder_pre1}${lang.auth_ip}`));
      } else {
        const val = value.split("/");
        if (
          /^((25[0-5]|2[0-4]\d|[01]?\d\d?)($|(?!\.$)\.)){4}$/.test(val[0]) &&
          val.length === 1
        ) {
          callback();
        } else if (
          /^((25[0-5]|2[0-4]\d|[01]?\d\d?)($|(?!\.$)\.)){4}$/.test(val[0]) &&
          val.length === 2 &&
          parseInt(val[1]) <= 65535 &&
          parseInt(val[1]) >= 0
        ) {
          callback();
        } else {
          callback(
            new Error(
              `${lang.placeholder_pre1}${lang.security_tip8}${lang.auth_ip}`
            )
          );
        }
      }
    };
    return {
      showUp: false,
      showIp: "",
      client_operate_password: "",
      addonsArr: [],
      isRefundPlugin: false,
      isRenewPlugin: false,
      initLoading: true,
      commonData: {
        currency_prefix: "",
        currency_suffix: "",
      },
      activeName: "1",
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
      self_defined_field: [],
      cloudConfig: {},
      // 实例详情
      cloudData: {
        support_apply_for_suspend: 0,
        data_center: {
          iso: "CN",
        },
        image: {
          icon: "",
        },
        config: {
          reinstall_sms_verify: 0,
          reset_password_sms_verify: 0,
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
      isShowPayMsg: 0,
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
        code: "",
        type: "pass",
        format_data_disk: false,
      },
      // 镜像数据
      osData: [],
      // 镜像版本选择框数据
      osSelectData: [],
      // 镜像图片地址
      osIcon: "",
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
      codeString: "",
      isShowIp: false,
      renewLoading: false, // 续费计算折扣loading
      // 停用信息
      refundData: {},
      // 停用状态
      refundStatus: {
        Pending: lang.common_cloud_text234,
        Suspending: lang.common_cloud_text235,
        Suspend: lang.common_cloud_text236,
        Suspended: lang.common_cloud_text237,
        Refund: lang.common_cloud_text238,
        Reject: lang.common_cloud_text239,
        Cancelled: lang.common_cloud_text240,
      },

      // 停用相关
      // 是否显示停用弹窗
      // 停用页面信息
      refundPageData: {
        host: {
          create_time: 0,
          first_payment_amount: 0,
        },
      },
      // 停用页面参数
      refundParams: {
        host_id: 0,
        suspend_reason: null,
        type: "Expire",
      },

      addons_js_arr: [], // 插件列表
      isShowPromo: false, // 是否开启优惠码
      isShowLevel: false, // 是否开启等级优惠
      // 续费
      // 显示续费弹窗
      isShowRenew: false, // 续费的总计loading
      renewBtnLoading: false, // 续费按钮的loading
      // 续费页面信息
      renewPageData: [],
      renewPriceList: [],
      renewActiveId: "",
      renewOrderId: 0,
      isShowRefund: false,
      hostStatus: {
        Unpaid: {
          text: lang.common_cloud_text88,
          color: "#F64E60",
          bgColor: "#FFE2E5",
        },
        Pending: {
          text: lang.common_cloud_text89,
          color: "#3699FF",
          bgColor: "#E1F0FF",
        },
        Active: {
          text: lang.common_cloud_text90,
          color: "#1BC5BD",
          bgColor: "#C9F7F5",
        },
        Suspended: {
          text: lang.common_cloud_text91,
          color: "#F0142F",
          bgColor: "#FFE2E5",
        },
        Deleted: {
          text: lang.common_cloud_text92,
          color: "#9696A3",
          bgColor: "#F2F2F7",
        },
        Failed: {
          text: lang.common_cloud_text93,
          color: "#FFA800",
          bgColor: "#FFF4DE",
        },
      },
      isRead: false,
      isShowPass: false,
      isShowPanelPass: false,
      passHidenCode: "",
      rescueStatusData: {},

      // 管理开始
      // 开关机状态
      powerStatus: "on",
      powerList: [
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
      ],
      loading1: false,
      loading2: false,
      loading3: false,
      loading4: false,
      loading5: false,
      ipValueData: [],
      ipv6ValueData: [],
      ipv4Tip: "",
      ipv4Arr: [],
      ipv6Tip: "",
      ipv6Arr: [],
      ipv4Select: [], // 可降级的ipv4
      ipv6Select: [], // 可降级的ipv6
      ipv4DelArr: [],
      ipv6DelArr: [],
      // 重置密码弹窗数据
      rePassData: {
        password: "",
        code: "",
        checked: false,
      },
      codeTimer: null,
      sendTime: 60,
      isSendCodeing: false,
      sendFlag: false,

      // 是否展示重置密码弹窗
      isShowRePass: false,
      // 救援模式弹窗数据
      rescueData: {
        type: "1",
        password: "",
      },
      // 是否展示救援模式弹窗
      isShowRescue: false,
      // 是否展示退出救援模式弹窗
      isShowQuit: false,
      ipValue: null,
      ipv6Value: null,
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
      /* 弹性磁盘 */
      elasticDisk: [],
      elasticDiskLoading: false,
      connectType: "",
      connectWay: "",
      /* 弹性磁盘 end */
      /* 网络开始 */
      netLoading: false,
      netDataList: [],
      netParams: {
        page: 1,
        limit: 20,
        pageSizes: [20, 50, 100],
        total: 0,
      },
      // ipv6
      ipv6Loading: false,
      ipv6DataList: [],
      ipv6Params: {
        page: 1,
        limit: 20,
        pageSizes: [20, 50, 100],
        total: 0,
      },
      elasticLoading: false,
      elasticParams: {
        page: 1,
        limit: 20,
        pageSizes: [20, 50, 100],
        total: 0,
      },
      elasticList: [],
      // 网络流量
      flowData: {},
      // 日志开始
      logDataList: [],
      logParams: {
        page: 1,
        limit: 20,
        pageSizes: [20, 50, 100],
        total: 200,
        orderby: "id",
        sort: "desc",
        keywords: "",
      },
      logLoading: false,

      // 备份与快照开始
      dataList1: [],
      // 备份列表数据
      dataList1: [],
      // 快照列表数据
      dataList2: [],
      backLoading: false,
      snapLoading: false,
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
        duration: lang.common_cloud_text110,
      },
      // 是否显示开启备份弹窗
      isShowOpenBs: false,
      backupType: "",
      // 快照备份订单id
      bsOrderId: 0,
      chartSelectValue: "1",
      // 统计图表开始
      echartLoading1: false,
      echartLoading2: false,
      echartLoading3: false,
      downloadLoading: false,
      echartLoading4: false,
      isShowPowerChange: false,
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
      productOptions: [],
      productParams: {
        page: 1,
        limit: 100,
        keywords: "",
        // status: "Active",
        // orderby: "id",
        // sort: "desc",
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
      memory_arr: [],
      cpuList: [],
      memory_arr: [], // 范围时内存数组
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
      memory_unit: "",
      // 流量包
      showPackage: false,
      packageLoading: false,
      packageList: [],
      curPackageId: "",
      /* 转发建站 */
      aclLoading: false,
      webLoading: false,
      aclList: [],
      webList: [],
      protocolArr: [
        {value: 1, label: "TCP"},
        {value: 2, label: "UDP"},
        {value: 3, label: "TCP+UDP"},
      ],
      natDialog: false,
      natType: "", // acl, web
      natForm: {
        name: "",
        int_port: undefined,
        protocol: "",
        domain: "",
      },
      submitLoaing: false,
      natRules: {
        name: [
          {
            required: true,
            message: `${lang.placeholder_pre1}${lang.security_label1}`,
            trigger: "blur",
          },
        ],
        domain: [
          {
            required: true,
            message: `${lang.placeholder_pre1}${lang.domain}`,
            trigger: "blur",
          },
        ],
        int_port: [
          {
            required: true,
            message: `${lang.placeholder_pre1}${lang.int_port}`,
            trigger: "blur",
          },
        ],
        protocol: [
          {
            required: true,
            message: `${lang.placeholder_pre2}${lang.protocol}`,
            trigger: "change",
          },
        ],
      },

      /* 关联弹性IP，磁盘 */
      isShowConnect: false,
      calcDes: "",
      curId: "",
      curIp: "",
      connectCheck: false,
      /* 套餐 */
      isPackage: false,
      recommend_config: {},
      recommendList: [],
      recommend_config_id: "",
      /* 模拟物理机运行 */
      physicalVisible: false,
      physicalTitle: "",
      physicalChecked: false,
      ipDetails: {
        dedicate_ip: "",
        assign_ip: "",
        ip_num: 0,
      },
      allIp: [],
      bwArr: [],
      flowArr: [],
      customManualField: [],
      captcha: "",
      token: "",
      isShowCaptcha: false,
      isSync: false, // 本地商品代理
      // 改版管理里面的其他操作
      otherType: "password",
      otherOptArr: [
        {value: "password", label: lang.common_cloud_btn12, disabled: false},
        {value: "rescue", label: lang.common_cloud_btn13, disabled: false},
        {value: "quitRescue", label: lang.common_cloud_btn14, disabled: false},
        {value: "reinstall", label: lang.common_cloud_btn15, disabled: false},
        // { value: "upgrade", label: lang.common_cloud_tip24, disabled: false },
        {value: "startup", label: lang.common_cloud_btn17, disabled: true},
        {value: "iso", label: lang.common_cloud_btn18, disabled: true},
        {value: "delete", label: lang.common_cloud_btn19, disabled: true},
      ],
      // 安全组
      safeList: [],
      safeLoading: false,
      safeParams: {
        page: 1,
        limit: 20,
        pageSizes: [20, 50, 100],
        total: 0,
        orderby: "id",
        sort: "desc",
        keywords: "",
      },
      isShowDel: false,
      delName: "",
      delTile: "",
      delId: "",
      submitLoading: false,
      isShowCj: false,
      createForm: {
        id: "",
        name: "",
        description: "",
      },
      errText: "",
      optType: "",
      // 安全组详情
      isShowSafeDetail: false,
      isShowSafDialog: false,
      optTitle: "",
      safeTab: "in",
      inList: [],
      inList: [], // 入方向数据
      outList: [],
      inParams: {
        id: "",
        keywords: "",
        page: 1,
        limit: 20,
        pageSizes: [20, 50, 100],
        total: 0,
        orderby: "id",
        sort: "desc",
        direction: "in",
      },
      outParams: {
        id: "",
        keywords: "",
        page: 1,
        limit: 20,
        pageSizes: [20, 50, 100],
        total: 0,
        orderby: "id",
        sort: "desc",
        direction: "out",
      },
      multipleSelection: [],
      batchVisible: false,
      checkedRules: [],
      singleForm: {
        // 单个添加规则
        id: "",
        description: "",
        protocol: "",
        ip: "",
        port: "",
        direction: "", // in,out
      },
      rules: {
        protocol: [
          {
            required: true,
            message: `${lang.placeholder_pre2}${lang.protocol}`,
            trigger: "change",
          },
        ],
        port: [
          {
            required: true,
            message: `${lang.placeholder_pre1}${lang.common_cloud_label13}`,
            trigger: "blur",
          },
          {validator: validatePort, trigger: "blur"},
        ],
        ip: [
          {
            required: true,
            message: `${lang.placeholder_pre1}${lang.auth_ip}`,
            trigger: "blur",
          },
          {validator: validatIp, trigger: "blur"},
        ],
      },
      batchForm: {
        // 批量增加规则
        id: "",
        ip: "",
        description: "",
      },
      batchRules: {
        ip: [
          {
            required: true,
            message: `${lang.placeholder_pre1}${lang.auth_ip}`,
            trigger: "blur",
          },
          {validator: validatIp, trigger: "blur"},
        ],
      },
      // 批量规则
      batchArr: [
        {
          tit: lang.remote_login,
          check: false,
          child: [
            {
              tit: "SSH",
              protocol: "ssh",
              port: 22,
              check: false,
            },
            {
              tit: "RDP",
              protocol: "rdp",
              port: 3389,
              check: false,
            },
            {
              tit: "Telnet",
              protocol: "telnet",
              port: 23,
              check: false,
            },
            {
              tit: "ICMP",
              protocol: "icmp",
              port: 0,
              check: false,
            },
          ],
        },
        {
          tit: lang.web_server,
          check: false,
          child: [
            {
              tit: "HTTP",
              protocol: "http",
              port: 80,
              check: false,
            },
            {
              tit: "HTTPS",
              protocol: "https",
              port: 443,
              check: false,
            },
          ],
        },
        {
          tit: lang.database,
          check: false,
          child: [
            {
              tit: "MySQL",
              protocol: "mysql",
              port: 3306,
              check: false,
            },
            {
              tit: "MS SQL",
              protocol: "mssql",
              port: 1433,
              check: false,
            },
            {
              tit: "PostgreSQL",
              protocol: "postgresql",
              port: 5432,
              check: false,
            },
            {
              tit: "Oracle",
              protocol: "oracle",
              port: 1521,
              check: false,
            },
            {
              tit: "Redls",
              protocol: "redis",
              port: 6379,
              check: false,
            },
          ],
        },
      ],
      protocol: [
        // 协议选项
        {
          label: "all",
          value: "all",
        },
        {
          label: "all_tcp",
          value: "all_tcp",
        },
        {
          label: "all_udp",
          value: "all_udp",
        },
        {
          label: "tcp",
          value: "tcp",
        },
        {
          label: "udp",
          value: "udp",
        },
        {
          label: "icmp",
          value: "icmp",
        },
        {
          label: "ssh",
          value: "ssh",
        },
        {
          label: "telnet",
          value: "telnet",
        },
        {
          label: "http",
          value: "http",
        },
        {
          label: "https",
          value: "https",
        },
        {
          label: "mssql",
          value: "mssql",
        },
        {
          label: "Oracle",
          value: "oracle",
        },
        {
          label: "mysql",
          value: "mysql",
        },
        {
          label: "rdp",
          value: "rdp",
        },
        {
          label: "postgresql",
          value: "postgresql",
        },
        {
          label: "redis",
          value: "redis",
        },
      ],
      delType: "",
      loading: false,
      safeGroupId: null,
      cpuChart: null,
      memoryChart: null,
      diskIoChart: null,
      bwChart: null,
      cpuData: [],
      memoryData: [],
      diskIoData: [],
      bwData: [],
      cpuUseage: 0.0,
      memoryUseage: 0.0,
      isEn: localStorage.getItem("lang") === "en-us",
      freeDataObj: {
        id: "",
        size: ""
      },
      submitLoading: false
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
    // 返回剩余到期时间
    formateDueDay(time) {
      return Math.floor((time * 1000 - Date.now()) / (1000 * 60 * 60 * 24));
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
    getLogNum(num) {
      if (num > 0) {
        const power =
          parseInt(Math.log2(num) / 10) >= 1
            ? parseInt(Math.log2(num) / 10)
            : 0;
        const divisor = Math.pow(1024, power);
        return {
          power,
          divisor,
        };
      } else {
        return {
          power: 0,
          divisor: 1,
        };
      }
    },
    convertUnit(power) {
      if (power === 0) {
        unit = "bps";
      } else if (power === 1) {
        unit = "Kbps";
      } else if (power === 2) {
        unit = "Mbps";
      } else if (power === 3) {
        unit = "Gbps";
      } else if (power === 4) {
        unit = "Tbps";
      }
      return unit;
    },
    /* 改版 */
    init() {
      this.chartSelectValue = "1";
      this.getCpuList();
      this.getBwList();
      this.getDiskLIoList();
      this.getMemoryList();
      this.doGetFlow();
    },
    handleBaseClick() {
      if (this.status === "on") {
       // this.doPowerOff();
       this.chooseItem("off");
      } else {
        // this.doPowerOn();
        this.chooseItem("on");
      }
    },
    // 实例开关机等操作
    chooseItem(type) {
      if (type == "on") {
        this.powerTitle = lang.common_cloud_text38;
      }
      if (type == "off") {
        this.powerTitle = lang.common_cloud_text39;
      }
      if (type == "rebot") {
        this.powerTitle = lang.common_cloud_text13;
      }
      if (type == "hardOff") {
        this.powerTitle = lang.common_cloud_text42;
      }
      if (type == "hardRebot") {
        this.powerTitle = lang.common_cloud_text41;
      }
      this.powerType = type;
      this.isShowPowerChange = true;
      // switch (type) {
      //   case "on":
      //     return this.doPowerOn();
      //   case "off":
      //     return this.doPowerOff();
      //   case "hardOff":
      //     return this.doHardOff();
      //   case "rebot":
      //     return this.doReboot();
      //   case "hardRebot":
      //     return this.doHardReboot();
      // }
    },
    // 重装、重置密码登操作
    changeOpt(type) {
      switch (type) {
        case "password":
          return this.showRePass();
        case "rescue":
          return this.showRescueDialog();
        case "quitRescue":
          return this.reQuitSub();
        case "reinstall":
          return this.showReinstall();
        case "upgrade":
          return this.showUpgrade();
      }
    },
    /* 安全组 */
    async getGroupList() {
      try {
        this.safeLoading = true;
        const res = await getGroup(this.safeParams);
        this.safeList = res.data.data.list;
        this.safeParams.total = res.data.data.count;
        this.safeLoading = false;
      } catch (error) {
        this.safeLoading = false;
        this.$message.error(error.data.msg);
      }
    },
    // 删除安全组
    deleteItem(row, type) {
      this.delName = row.name;
      this.delId = row.id;
      let tit = "";
      switch (type) {
        case "in":
          tit = `${lang.referral_title9}${lang.in_rules}`;
          break;
        case "out":
          tit = `${lang.referral_title9}${lang.out_rules}`;
          break;
        case "group":
          tit = lang.del_group;
          break;
      }
      this.delTile = tit;
      this.delType = type;
      this.isShowDel = true;
    },
    delSub() {
      switch (this.delType) {
        case "group":
          return this.delSafeGroup();
        case "in":
        case "out":
          return this.delOne();
        case "batch":
          return this.batchDel();
        case "join":
          return this.submitRelation();
        case "quit":
          return this.delRelation();
      }
    },
    // 加入安全组
    handleSafe(type) {
      this.delTile = type === "join" ? lang.mf_safe_tip1 : lang.mf_safe_tip2;
      this.delType = type;
      this.isShowDel = true;
    },
    async submitRelation() {
      try {
        this.submitLoading = true;
        const params = {
          id: this.safeGroupId,
          host_id: [this.id],
        };
        const res = await apiLinkGroup(params);
        this.submitLoading = false;
        this.relationVisible = false;
        const arr = res.data.data;
        const tips = arr
          .map((item) => {
            return `<p>${lang.cloud_menu_1}：${item.name}：${item.msg}</p>`;
          })
          .join("");
        this.$notify({
          title: lang.cart_tip_text25,
          message: tips,
          duration: 0,
          dangerouslyUseHTMLString: true,
        });
        this.isShowDel = false;
        this.getCloudDetail();
      } catch (error) {
        this.submitLoading = false;
        this.$message.error(error.data.msg);
      }
    },
    // 退出安全組
    async delRelation() {
      try {
        const params = {
          id: this.safeGroupId,
          host_id: this.id,
        };
        this.submitLoading = true;
        const res = await cancelConcatCloud(params);
        this.$message.success(lang.delete_cloud_success);
        this.submitLoading = false;
        this.isShowDel = false;
        this.getCloudDetail();
        this.getGroupList();
        this.isShowSafeDetail = false;
      } catch (error) {
        this.submitLoading = false;
        this.$message.error(error.data.msg);
      }
    },

    async delSafeGroup() {
      try {
        this.submitLoading = true;
        const res = await deleteGroup(this.delId);
        if (res.data.status === 200) {
          this.$message.success(res.data.msg);
          this.getGroupList();
          this.isShowDel = false;
          this.submitLoading = false;
        }
      } catch (error) {
        this.submitLoading = false;
        this.$message.error(error.data.msg);
      }
    },
    async delOne() {
      try {
        this.submitLoading = true;
        const res = await deleteGroupRules(this.delId);
        if (res.data.status === 200) {
          this.$message.success(res.data.msg);
          this.isShowDel = false;
          this.submitLoading = false;
          if (this.safeTab === "in") {
            this.inList = this.inList.filter((item) => item.id !== this.delId);
            if (this.inList.length === 0 && this.inParams.page > 1) {
              this.inParams.page -= 1;
            }
            this.getInRules();
          } else if (this.safeTab === "out") {
            this.outList = this.outList.filter(
              (item) => item.id !== this.delId
            );
            if (this.outParams.length === 0 && this.outParams.page > 1) {
              this.outParams.page -= 1;
            }
            this.getOutRules();
          }
        }
      } catch (error) {
        this.submitLoading = false;
        this.$message.error(error.data.msg);
      }
    },
    // 添加安全组
    createSecurity() {
      this.isShowCj = true;
      this.optType = "add";
      this.createForm.name = "";
      this.createForm.description = "";
      this.errText = "";
    },
    // 创建API秘钥 提交
    async cjSub() {
      let isPass = true;
      if (!this.createForm.name) {
        this.errText = `${lang.placeholder_pre1}${lang.security_label1}`;
        isPass = false;
      }
      if (isPass) {
        this.errText = "";
        const params = JSON.parse(JSON.stringify(this.createForm));
        if (this.optType === "add") {
          delete params.id;
        }
        try {
          this.submitLoading = true;
          const res = await addAndUpdateGroup(this.optType, params);
          if (res.data.status === 200) {
            // 关闭弹窗
            this.isShowCj = false;
            // 获取返回信息 并在新弹窗进行展示
            this.apiData = res.data.data;
            this.getGroupList();
            this.submitLoading = false;
          }
        } catch (error) {
          this.submitLoading = false;
          this.errText = error.data.msg;
        }
      }
    },
    editItem(row) {
      this.optType = "update";
      this.createForm = JSON.parse(JSON.stringify(row));
      this.isShowCj = true;
      this.errText = "";
    },
    goSafeDetail(id) {
      this.safeGroupId = this.inParams.id = this.outParams.id = id;
      this.safeTab = "in";
      this.isShowSafeDetail = true;
      this.getInRules();
    },
    cjClose() {
      this.isShowCj = false;
    },
    delClose() {
      this.isShowDel = false;
    },
    // 每页展示数改变
    sizeChange(e) {
      this.safeParams.limit = e;
      this.safeParams.page = 1;
      // 获取列表
      this.getGroupList();
    },
    // 当前页改变
    currentChange(e) {
      this.safeParams.page = e;
      this.getGroupList();
    },
    // 安全组详情
    // 每页展示数改变
    sizeRuleChange(e) {
      switch (this.safeTab) {
        case "in":
          this.inParams.limit = e;
          this.inParams.page = 1;
          this.getInRules();
          break;
        case "out":
          this.outParams.limit = e;
          this.outParams.page = 1;
          this.getOutRules();
          break;
      }
    },
    // 当前页改变
    currentRuleChange(e) {
      switch (this.safeTab) {
        case "in":
          this.inParams.page = e;
          this.getInRules();
          break;
        case "out":
          this.outParams.page = e;
          this.getOutRules();
          break;
      }
    },
    handleSafeClick() {
      this.multipleSelection = [];
      this.$refs.multipleTable.clearSelection();
      switch (this.safeTab) {
        case "in":
          this.getInRules();
          break;
        case "out":
          this.getOutRules();
          break;
      }
    },
    // 入规则
    async getInRules() {
      try {
        this.loading = true;
        this.inParams.id = this.inParams.id || this.safeGroupId;
        const res = await getGroupRules(this.inParams);
        this.inList = res.data.data.list;
        this.inParams.total = res.data.data.count;
        this.loading = false;
      } catch (error) {
        this.$message.error(error.data.msg);
        this.loading = false;
      }
    },
    handleSelectionChange(val) {
      this.multipleSelection = val;
    },
    // 批量删除
    batchDelete() {
      if (this.multipleSelection.length === 0) {
        return this.$message.warning(`${lang.placeholder_pre2}${lang.rules}`);
      }
      this.delType = "batch";
      if (this.safeTab === "in") {
        this.delTile = `${lang.batch_delete}${lang.in_rules}`;
      } else if (this.safeTab === "out") {
        this.delTile = `${lang.batch_delete}${lang.out_rules}`;
      }
      this.isShowDel = true;
    },
    // 批量删除
    batchDel() {
      let delArr = [];
      this.multipleSelection.forEach((item, index) => {
        delArr[index] = deleteGroupRules(item.id);
      });
      this.submitLoading = true;
      const idTemp = this.multipleSelection.reduce((all, cur) => {
        all.push(cur.id);
        return all;
      }, []);
      Promise.all(delArr)
        .then((res) => {
          this.$message.success(`${lang.referral_tips4}`);
          this.isShowDel = false;
          this.submitLoading = false;
          if (this.safeTab === "in") {
            this.inList = this.inList.filter(
              (item) => !idTemp.includes(item.id)
            );
            if (this.inList.length === 0 && this.inParams.page > 1) {
              this.inParams.page -= 1;
            }
            this.getInRules();
          } else if (this.safeTab === "out") {
            this.outList = this.outList.filter(
              (item) => !idTemp.includes(item.id)
            );
            if (this.outList.length === 0 && this.outParams.page > 1) {
              this.outParams.page -= 1;
            }
            this.getOutRules();
          }
        })
        .catch((error) => {
          this.submitLoading = false;
          this.$message.error(error.data.msg);
        });
    },
    // 出方向规则
    async getOutRules() {
      try {
        this.loading = true;
        this.outParams.id = this.outParams.id || this.safeGroupId;
        const res = await getGroupRules(this.outParams);
        this.outList = res.data.data.list;
        this.outParams.total = res.data.data.count;
        this.loading = false;
      } catch (error) {
        this.$message.error(error.data.msg);
        this.loading = false;
      }
    },
    // 创建规则
    createSecurityRule() {
      this.isShowSafDialog = true;
      this.optType = "add";
      this.singleForm.id = this.inParams.id || this.cloudData.security_group.id;
      this.singleForm.protocol = "tcp";
      this.singleForm.ip = "";
      this.singleForm.port = "";
      this.singleForm.description = "";
      this.optTitle = `${lang.com_config.add}${lang.rules}`;
      this.$refs.singleForm && this.$refs.singleForm.clearValidate();
    },
    // 点击勾选子项
    changePar(e, index) {
      const temp = JSON.parse(JSON.stringify(this.batchArr));
      temp[index].check = e;
      temp[index].child = temp[index].child.map((item) => {
        item.check = e;
        return item;
      });
      this.batchArr = temp;
    },
    // 子项点击
    changeChild(e, index, ind) {
      const temp = JSON.parse(JSON.stringify(this.batchArr));
      temp[index].child[ind].check = e;
      const len = temp[index].child.length;
      const checkNum = temp[index].child.reduce((all, cur) => {
        if (cur.check === true) {
          all += 1;
        }
        return all;
      }, 0);
      if (len === checkNum) {
        temp[index].check = true;
      } else {
        temp[index].check = false;
      }
      this.batchArr = temp;
    },
    editRuleItem(row) {
      this.optType = "update";
      this.optTitle = `${lang.edit}${lang.rules}`;
      this.singleForm = JSON.parse(JSON.stringify(row));
      setTimeout(() => {
        this.singleForm.port = row.port;
      }, 10);
      this.isShowSafDialog = true;
    },
    submitForm() {
      this.$refs.singleForm.validate(async (valid) => {
        if (valid) {
          try {
            this.submitLoading = true;
            const params = JSON.parse(JSON.stringify(this.singleForm));
            params.direction = this.safeTab;
            const res = await addAndUpdateGroupRules(this.optType, params);
            this.submitLoading = false;
            this.isShowSafDialog = false;
            this.$message.success(res.data.msg);
            if (this.safeTab === "in") {
              this.getInRules();
            } else if (this.safeTab === "out") {
              this.getOutRules();
            }
          } catch (error) {
            this.submitLoading = false;
            this.$message.error(error.data.msg);
          }
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    // 批量添加规则
    batchCreateSecurity() {
      this.batchVisible = true;
      this.optTitle = lang.batch_add_rules;
      this.batchForm.id = this.inParams.id || this.cloudData.security_group.id;
      this.batchForm.ip = "0.0.0.0/0";
      this.batchForm.description = "";
      let temp = JSON.parse(JSON.stringify(this.batchArr));
      temp = temp.map((item) => {
        item.check = false;
        item.child = item.child.map((el) => {
          el.check = false;
          return el;
        });
        return item;
      });
      this.batchArr = temp;
    },
    batchSubmitForm() {
      this.$refs.batchForm.validate(async (valid) => {
        if (valid) {
          try {
            let arr = [];
            arr = this.batchArr.reduce((all, cur) => {
              all = all.concat(cur.child.flat());
              return all;
            }, []);
            arr = arr.filter((item) => item.check === true);
            arr.map((item) => {
              item.protocol = item.protocol;
              item.direction = this.safeTab;
              item.ip = this.batchForm.ip;
              item.description = this.batchForm.description;
            });
            const params = {
              id: this.batchForm.id,
              rule: arr,
            };
            if (params.rule.length === 0) {
              return this.$message.warning(
                lang.placeholder_pre2 + lang.common_port
              );
            }
            this.submitLoading = true;
            const res = await batchRules(params);
            this.submitLoading = false;
            this.batchVisible = false;
            this.$message.success(res.data.msg);
            if (this.safeTab === "in") {
              this.getInRules();
            } else if (this.safeTab === "out") {
              this.getOutRules();
            }
          } catch (error) {
            console.log("error", error);
            this.submitLoading = false;
            this.$message.error(error.data.msg);
          }
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    /* 安全组 end */

    /* 改版 end */

    // 验证码 关闭
    captchaCancel() {
      this.isShowCaptcha = false;
    },
    // 验证码验证成功后的回调
    getData(captchaCode, token) {
      this.token = token;
      this.captcha = captchaCode;
      this.isShowCaptcha = false;
      this.sendCode();
    },
    hadelSafeConfirm(val, remember) {
      this[val]("", remember);
    },
    changeCustomPassword(el) {
      const index = this.customManualField.findIndex(
        (item) => item.id === el.id
      );
      this.$set(this.customManualField[index], "hidenPass", !el.hidenPass);
      this.$forceUpdate();
    },
    downloadRpd() {
      if (this.downloadLoading) return;
      this.downloadLoading = true;
      apiDownloadRdp({id: this.id})
        .then((res) => {
          this.downloadLoading = false;
          const content = res.data.data.content;
          const blob = new Blob([content], {type: "text/plain;charset=utf-8"});
          const a = document.createElement("a");
          document.body.appendChild(a);
          a.style.display = "none";
          a.download = res.data.data.name;
          a.href = URL.createObjectURL(blob);
          a.click();
          document.body.removeChild(a);
        })
        .catch((err) => {
          this.$message.error(err.data.msg);
          this.downloadLoading = false;
        });
    },
    copyLoginInfo() {
      const loginInfo = {};
      loginInfo[lang.common_cloud_label14] = this.rescueStatusData.username;
      if (this.cloudData.ssh_key?.id) {
        loginInfo[lang.security_tab1] = this.cloudData.ssh_key?.name;
      } else {
        loginInfo[lang.common_cloud_label7] = this.rescueStatusData.password;
      }
      if (this.ipDetails.dedicate_ip) {
        loginInfo["IP"] = this.allIp.join("\n");
      }
      loginInfo[lang.common_cloud_label13] = this.rescueStatusData.port;

      const copyValue = Object.keys(loginInfo)
        .map((key) => `${key}：${loginInfo[key]}`)
        .join("\n");
      copyText(copyValue);
    },
    // 处理结果的交集
    handleMixed(...arr) {
      if (arr.length === 0) {
        return [];
      }
      let resultArr = new Set(arr[0]);
      for (let i = 1; i < arr.length; i++) {
        const curArr = arr[i];
        if (!curArr || !curArr.length) {
          return [];
        }
        const newArr = new Set();
        for (const element of resultArr) {
          if (curArr.includes(element)) {
            newArr.add(element);
          }
        }
        resultArr = newArr;
        if (resultArr.size === 0) {
          return [];
        }
      }
      return Array.from(resultArr);
    },
    handleRange(item, type) {
      // 处理范围内的是否包含当前参数: memory,system_disk,data_disk,bw,flow,ipv4_num,ipv6_num
      // 初始化的时候，需要处理各参数的最大范围
      let target = "";
      if (type === "system_disk") {
        target = this.params.system_disk.size;
      } else if (type === "data_disk") {
        // 当没有选择数据盘的时候， 值为 "", 会过滤掉设置了数据盘的规则
        target = this.params.data_disk[0]?.size;
        if (!target) {
          return true;
        }
      } else {
        target = this.params[type];
      }
      let rangeMax = this[`${type}_arr`][this[`${type}_arr`].length - 1];
      let tempArr = [];
      // 内存 改为单选和范围
      if (item[type].value) {
        const tempValue = item[type].value.sort((a, b) => a - b);
        tempArr = [tempValue[0], tempValue[tempValue.length - 1]];
      } else {
        tempArr = [
          item[type].min * 1,
          item[type].max === ""
            ? rangeMax
            : item[type].max * 1 >= rangeMax
            ? rangeMax
            : item[type].max * 1,
        ];
      }
      return this.createArr(tempArr).includes(target);
    },
    /* ipv4/ipv6 */
    changeIpv4() {
      this.getCycleList();
    },
    changeIpv6() {
      this.getCycleList();
    },
    async getIpDetail () {
      try {
        const res = await getHostIpDetails(this.id);
        const temp = res.data.data;
        this.ipDetails = JSON.parse(JSON.stringify(res.data.data));
        this.allIp = (temp.dedicate_ip + "," + temp.assign_ip).split(",").filter(item => item !== '');
        this.handleSplitIp();
      } catch (error) { }
    },
     // 拆分IPv4,IPv6
     handleSplitIp () {
      const ipv4Regex = /^(25[0-5]|2[0-4][0-9]|1[0-9][0-9]?|[1-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|1[0-9][0-9]?|[1-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|1[0-9][0-9]?|[1-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|1[0-9][0-9]?|[1-9]?[0-9])$/;
      const ipv6Regex = /^(?:[0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}|(?:[0-9a-fA-F]{1,4}:){1,7}:|(?:[0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|(?:[0-9a-fA-F]{1,4}:){1,5}(?::[0-9a-fA-F]{1,4}){1,2}|(?:[0-9a-fA-F]{1,4}:){1,4}(?::[0-9a-fA-F]{1,4}){1,3}|(?:[0-9a-fA-F]{1,4}:){1,3}(?::[0-9a-fA-F]{1,4}){1,4}|(?:[0-9a-fA-F]{1,4}:){1,2}(?::[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:(?::[0-9a-fA-F]{1,4}){1,6}|:((?::[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(?::[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]{0,1}[0-9]?)\.){3}(25[0-5]|(2[0-4]|1{0,1}[0-9]{0,1}[0-9]?))|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]{0,1}[0-9]?)\.){3}(25[0-5]|(2[0-4]|1{0,1}[0-9]{0,1}[0-9]?))|[0-9a-fA-F]{1,4}|:))$/;
      const ipv4 = [];
      const ipv6 = [];
      this.allIp.forEach(ip => {
        if (ipv4Regex.test(ip)) {
          ipv4.push(ip);
        } else if (ipv6Regex.test(ip)) {
          ipv6.push(ip);
        }
      });
      this.ipv4Select = ipv4;
      this.ipv6Select = ipv6;
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
      this.$message.success(lang.index_text32);
    },
    /* 模拟物理机运行 */
    physicalChange() {
      this.physicalVisible = true;
      this.physicalChecked = false;
      if (this.rescueStatusData.simulate_physical_machine) {
        this.physicalTitle = `${lang.mf_close}${lang.simulate_physical}`;
      } else {
        this.physicalTitle = `${lang.mf_open}${lang.simulate_physical}`;
      }
    },
    async handlePhysical() {
      try {
        if (!this.physicalChecked && this.powerStatus == "off") {
          this.errText = lang.common_cloud_text62;
          return false;
        }
        const params = {
          id: this.id,
          simulate_physical_machine: this.rescueStatusData
            .simulate_physical_machine
            ? 0
            : 1,
        };
        this.submitLoaing = true;
        const res = await changeSimulatePhysical(params);
        this.$message.success(res.data.msg);
        this.getRemoteInfo();
        this.physicalVisible = false;
        this.submitLoaing = false;
      } catch (error) {
        this.submitLoaing = false;
        this.physicalVisible = false;
        this.$message.error(error.data.msg);
      }
    },
    /* 模拟物理机运行 end */
    /* 转发建站 */
    async getNatAclList() {
      try {
        this.aclLoading = true;
        const res = await getNatAcl({id: this.id});
        this.aclList = res.data.data.list;
        this.aclLoading = false;
      } catch (error) {}
    },
    async getNatWebList() {
      try {
        this.webLoading = true;
        const res = await getNatWeb({id: this.id});
        this.webList = res.data.data.list;
        this.webLoading = false;
      } catch (error) {}
    },
    handDelacl(row) {
      this.$confirm(`${lang.security_btn9}${row.name}？`)
        .then(() => {
          delNatAcl({id: this.id, nat_acl_id: row.id})
            .then((res) => {
              this.$message.success(res.data.msg);
              this.getNatAclList();
            })
            .catch((err) => {
              this.$message.error(err.data.msg);
            });
        })
        .catch((_) => {});
    },
    handDelweb(row) {
      this.$confirm(`${lang.security_btn9}${row.domain}？`)
        .then(() => {
          delNatWeb({id: this.id, nat_web_id: row.id})
            .then((res) => {
              this.$message.success(res.data.msg);
              this.getNatWebList();
            })
            .catch((err) => {
              this.$message.error(err.data.msg);
            });
        })
        .catch((_) => {});
    },
    showCreateNat(type) {
      this.natType = type;
      this.natDialog = true;
      this.natForm = {
        name: "",
        int_port: undefined,
        protocol: "",
        domain: "",
      };
      this.$refs.natForm && this.$refs.natForm.clearValidate();
    },
    changeIntPort(e) {
      this.natForm.int_port = e;
    },
    submitNat() {
      this.$refs.natForm.validate((valid) => {
        if (valid) {
          this.submitLoaing = true;
          const params = JSON.parse(JSON.stringify(this.natForm));
          params.id = this.id;
          if (this.natType === "acl") {
            delete params.domain;
            this.handlerAcl(params);
          } else {
            delete params.name;
            delete params.protocol;
            this.handlerWeb(params);
          }
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    async handlerAcl(params) {
      try {
        const res = await addNatAcl(params);
        this.submitLoaing = false;
        this.$message.success(res.data.msg);
        this.natDialog = false;
        this.getNatAclList();
      } catch (error) {
        this.submitLoaing = false;
        this.$message.error(error.data.msg);
      }
    },
    async handlerWeb(params) {
      try {
        const res = await addNatWeb(params);
        this.submitLoaing = false;
        this.$message.success(res.data.msg);
        this.natDialog = false;
        this.getNatWebList();
      } catch (error) {
        this.submitLoaing = false;
        this.$message.error(error.data.msg);
      }
    },
    /* 转发建站 end */

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
    /* 获取线路详情 */
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
        this.params.cpu = this.cloudData.cpu * 1;
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
        // 默认选择防御
        this.params.peak_defence = this.cloudData.peak_defence;
        this.defenseName =
          this.params.peak_defence == 0
            ? lang.no_defense
            : this.params.peak_defence + "G";
        this.getCycleList();
      } catch (error) {
        console.log("####", error);
      }
    },
    changeCpu(e) {
      // 切换cpu，改变内存
      setTimeout(() => {
        this.getCycleList();
      }, 0);
    },
    // 切换防御
    changeDefence(e) {
      if (e === lang.no_defense) {
        this.params.peak_defence = 0;
      } else {
        this.params.peak_defence = e.replace("G", "");
      }
      setTimeout(() => {
        this.getCycleList();
      }, 0);
    },
    changeBw(e) {
      this.params.bw = e.replace("M", "");
      // 计算价格
      setTimeout(() => {
        this.getCycleList();
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
        this.getCycleList();
      }, 0);
    },
    // 切换内存
    changeMemory(e) {
      setTimeout(() => {
        this.getCycleList();
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
      this.getCycleList();
    },
    createMarks(data) {
      data = data || [];
      const obj = {
        0: "",
        // 25: '',
        // 50: '',
        // 75: '',
        100: "",
      };
      const range = data[data.length - 1] - data[0];
      obj[0] = `${data[0]}`;
      // obj[25] = `${Math.ceil(range * 0.25)}`
      // obj[50] = `${Math.ceil(range * 0.5)}`
      // obj[75] = `${Math.ceil(range * 0.75)}`
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
      this.getCycleList();
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
    handleClick(val) {
      if (this.cloudData.security_group.id !== 0) {
        this.isShowSafeDetail = true;
      } else {
        this.isShowSafeDetail = false;
      }
      switch (this.activeName) {
        case "1":
          this.chartSelectValue = "1";
          this.getstarttime(1);
          this.getCpuList();
          this.getBwList();
          this.getDiskLIoList();
          this.getMemoryList();
          this.doGetFlow();
          break;
        case "2":
          break;
        case "3":
          this.doGetDiskList();
          this.getAloneDiskList();
          break;
        case "4": // 网络
          this.chartSelectValue = "1";
          this.getIpList();
          this.getIpv6List();
          this.getElasticIpList();
          // this.doGetFlow();
          this.getVpcNetwork();
          this.getSafeList();
          this.getstarttime(1);
          this.getBwList();
          break;
        case "5":
          this.getBackupList();
          this.getSnapshotList();
          break;
        case "safe":
          if (this.safeGroupId) {
            this.inParams.id = this.safeGroupId;
            this.getInRules();
          } else {
            this.getGroupList();
          }
          break;
        case "6":
          this.getLogList();
          break;
        case "nat":
          this.getNatAclList();
          this.getNatWebList();
      }
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
          this.isShowPayMsg = status;
        }
      });
    },
    autoRenewChange() {
      this.isShowAutoRenew = true;
    },
    autoRenewDgClose() {
      this.isShowPayMsg = !this.isShowPayMsg;
      this.isShowAutoRenew = false;
    },
    doAutoRenew() {
      const params = {
        id: this.id,
        status: this.isShowPayMsg,
      };
      rennewAuto(params)
        .then((res) => {
          if (res.data.status === 200) {
            this.$message.success(lang.common_cloud_text44);
            this.isShowAutoRenew = false;
            this.getRenewStatus();
          }
        })
        .catch((error) => {
          this.$message.error(error.data.msg);
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
          this.isSync = this.hostData.mode === "sync";
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
          this.getCloudDetail();
          // 获取其它配置
        }
      });
    },
    // 获取实例详情
    getCloudDetail() {
      const params = {
        id: this.id,
      };
      cloudDetail(params).then((res) => {
        if (res.data.status === 200) {
          this.cloudData = res.data.data;
          this.safeGroupId = this.cloudData.security_group.id;
          if (this.safeGroupId !== 0) {
            this.isShowSafeDetail = true;
          }
          this.customManualField = res.data.data.custom_show.map(
            (item, index) => {
              item.id = "cus" + index;
              item.isShowTooltip = false;
              return item;
            }
          );
          this.recommend_config = this.cloudData.recommend_config;
          this.isPackage = this.cloudData.recommend_config?.id ? true : false;
          this.params.data_center_id = this.productParams.data_center_id =
            res.data.data.data_center.id;
          this.cloudConfig = res.data.data.config;
          this.$emit("getclouddetail", this.cloudData);
          // 获取镜像数据
          this.getConfigData();
          if (
            this.cloudConfig.manual_resource_control_mode === "cloud_client"
          ) {
            this.getManualOs();
          } else {
            this.getImage();
          }
          if (this.activeName == "1") {
            this.init();
          }
          setTimeout(() => {
            this.initLoading = false;
            if (this.showUp && this.cloudData.line?.sync_firewall_rule == 1) {
              this.activeName = "ip_defance";
              this.showUp = false;
            }
          }, 300);
        }
      });
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
            this.$message({
              message: lang.appstore_text359,
              type: "success",
            });
            this.isShowNotesDialog = false;
          }
        })
        .catch((err) => {
          this.$message.error(err.data.msg);
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
      this.reinstallData.image_id = null;
      this.reinstallData.ssh_key_id = null;
      this.reinstallData.format_data_disk = false;
      // this.reinstallData.port = null;
      this.reinstallData.code = "";
      this.reinstallData.type = "pass";
      this.isShowReinstallDialog = true;
      this.params.cpu = this.cloudData.cpu * 1;
      this.params.memory = this.cloudData.memory * 1;
      this.params.data_center_id = this.cloudData.data_center.id * 1;
      // 手动资源管理
      if (this.cloudConfig.manual_resource_control_mode === "cloud_client") {
        this.getManualOs();
      } else {
        this.getImage();
      }
      // 处理指定端口
      const curGroupName = this.osData.filter(
        (item) => item.id === this.reinstallData.osGroupId
      )[0]?.name;
      if (curGroupName === "Windows") {
        if (this.configObj.rand_ssh_port !== 2) {
          this.reinstallData.port = 3389;
        } else {
          this.reinstallData.port = this.configObj.rand_ssh_port_windows;
        }
      } else {
        if (this.configObj.rand_ssh_port !== 2) {
          this.reinstallData.port = 22;
        } else {
          this.reinstallData.port = this.configObj.rand_ssh_port_linux;
        }
      }
    },
    getManualOs() {
      manualResourceOs(this.id).then((res) => {
        if (res.data.status === 200) {
          this.osData = res.data.data.os
            .filter((items) => {
              return items.os.length > 0;
            })
            .map((item) => {
              return {
                icon: item.name,
                image: item.os.map((items) => {
                  return {
                    image_group_id: items.group_id,
                    price: 0,
                    ...items,
                  };
                }),
                ...item,
              };
            });

          this.osSelectData = this.osData[0]?.image;
          this.reinstallData.osGroupId = this.osData[0]?.id;
          this.osIcon =
            "/plugins/server/mf_cloud/template/clientarea/pc/default/img/mf_cloud/" +
            this.osData[0]?.icon +
            ".svg";

          const filterImageId = this.calcImageList.reduce((all, cur) => {
            all.push(cur.image.map((item) => item.id));
            return all;
          }, []);
          let curImage = this.calcImageList.filter(
            (item) =>
              item.image.findIndex(
                (el) => el.id === this.cloudData.image.id
              ) !== -1
          );
          if (!filterImageId.includes(this.cloudData.image.id)) {
            this.reinstallData.osId = curImage[0]?.image[0].id;
          } else {
            this.reinstallData.osId = this.cloudData.image.id;
          }
          // this.reinstallData.osId = this.osData[0].image[0].id;
          if (this.osData.length === 0) {
            return;
          }
          this.doCheckImage();
        } else {
          this.$message.error(res.data.msg);
        }
      });
    },
    // 提交重装系统
    doReinstall(e, remember_operate_password = 0) {
      let isPass = true;
      const data = {...this.reinstallData};
      if (!data.osId) {
        isPass = false;
        this.errText = lang.common_cloud_text45;
        return false;
      }
      if (!data.port) {
        isPass = false;
        this.errText = lang.common_cloud_text46;
      }
      if (data.type == "pass") {
        if (!data.password) {
          isPass = false;
          this.errText = lang.common_cloud_text47;
          return false;
        }
      } else {
        if (!data.ssh_key_id) {
          isPass = false;
          this.errText = lang.common_cloud_text48;
          return false;
        }
      }

      if (!data.code && this.cloudConfig.reinstall_sms_verify === 1) {
        isPass = false;
        this.errText = lang.account_tips33;
      }
      // if (!this.client_operate_password) {
      //   this.$refs.safeRef.openDialog("doReinstall");
      //   return;
      // }
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";
      if (isPass) {
        this.errText = "";
        let params = {
          id: this.id,
          image_id: data.osId,
          port: data.port,
          code: data.code,
          client_operate_password,
          client_operate_methods: "doReinstall",
          remember_operate_password,
          format_data_disk: data.format_data_disk ? 1 : 0,
        };
        if (data.type == "pass") {
          params.password = data.password;
        } else {
          params.ssh_key_id = data.ssh_key_id;
        }
        // 调用重装系统接口
        reinstall(params)
          .then((res) => {
            if (res.data.status == 200) {
              this.$message.success(res.data.msg);
              this.isShowReinstallDialog = false;
              this.getCloudStatus();
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
                return this.$message.error(err.data.msg);
              }
            }
            this.errText = err.data.msg;
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
                this.$message.error(err.data.msg);
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
          let curImage = this.calcImageList.filter(
            (item) =>
              item.image.findIndex(
                (el) => el.id === this.cloudData.image.id
              ) !== -1
          );
          // 升降级过后再重装，原系统被限制不能重装的情况
          if (curImage.length === 0) {
            curImage = [this.calcImageList[0]];
          }
          this.reinstallData.osGroupId = curImage[0]?.id;
          this.osSelectData = curImage[0]?.image;
          this.osIcon =
            "/plugins/server/mf_cloud/template/clientarea/pc/default/img/mf_cloud/" +
            curImage[0]?.icon +
            ".svg";

          const filterImageId = this.calcImageList.reduce((all, cur) => {
            all.push(cur.image.map((item) => item.id));
            return all;
          }, []);

          if (!filterImageId.includes(this.cloudData.image.id)) {
            this.reinstallData.osId = curImage[0]?.image[0].id;
          } else {
            this.reinstallData.osId = this.cloudData.image.id;
          }
          this.doCheckImage();
        }
      });
    },
    // 镜像分组改变时
    osSelectGroupChange(e) {
      this.calcImageList.map((item) => {
        if (item.id == e) {
          this.osSelectData = item.image;
          this.osIcon =
            "/plugins/server/mf_cloud/template/clientarea/pc/default/img/mf_cloud/" +
            item.icon +
            ".svg";
          this.reinstallData.osId = item.image[0].id;
          this.doCheckImage();
        }
      });
    },
    // 镜像版本改变时
    osSelectChange(e) {
      this.doCheckImage();
    },
    // 随机生成密码
    autoPass() {
      let pass = randomCoding(1) + 0 + genEnCode(9, 1, 1, 0, 1, 0);
      this.reinstallData.password = pass;
      // 重置密码
      this.rePassData.password = pass;
      // 救援系统密码
      this.rescueData.password = pass;
    },
    // 点击发送验证码
    sendCode() {
      if (this.codeTimer || this.sendFlag) {
        return;
      }
      /* 根据后台是否开启图形验证码 */
      if (this.commonData.captcha_client_verify == 1 && !this.captcha) {
        this.isShowCaptcha = true;
        this.$refs.captcha.doGetCaptcha();
        return;
      }
      this.sendFlag = true;
      const params = {
        action: "verify",
        captcha: this.captcha,
        token: this.token,
      };
      phoneCode(params)
        .then((res) => {
          if (res.data.status === 200) {
            this.codeTimer = setInterval(() => {
              this.isSendCodeing = true;
              this.sendTime--;
              if (this.sendTime === 0) {
                this.isSendCodeing = false;
                this.sendTime = 60;
                clearInterval(this.codeTimer);
                this.codeTimer = null;
              }
            }, 1000);
            this.sendFlag = false;
          }
        })
        .catch((err) => {
          this.sendFlag = false;
          this.errText = err.data.msg;
        })
        .finally(() => {
          this.token = "";
          this.captcha = "";
        });
    },
    // 随机端口
    randomNum() {
      const min = this.configObj.rand_ssh_port_start * 1;
      const max = this.configObj.rand_ssh_port_end * 1;
      const range = max - min + 1;
      const num = Math.floor(Math.random() * range) + min;
      return num;
    },
    // 随机生成port
    autoPort() {
      if (this.configObj.rand_ssh_port === 2) {
        return;
      }
      this.reinstallData.port = this.randomNum();
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
            this.statusText = res.data.data.desc;
            if (this.status == "operating") {
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
                ];
                this.powerStatus = "off";
              } else if (e == "off") {
                this.powerList = [
                  {
                    id: 1,
                    label: lang.common_cloud_text10,
                    value: "on",
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
              this.getRemoteInfo();
            }
          }
        })
        .catch((err) => {
          this.getCloudStatus();
        });
    },
    // 获取救援模式状态
    getRemoteInfo() {
      const params = {
        id: this.id,
      };
      this.passHidenCode = "";
      remoteInfo(params).then((res) => {
        if (res.data.status === 200) {
          this.rescueStatusData = res.data.data;
          const length =
            this.rescueStatusData.password.length >= 6
              ? 6
              : this.rescueStatusData.password.length;
          for (let i = 0; i < length; i++) {
            this.passHidenCode += "*";
          }
          this.isRescue = res.data.data.rescue == 1;
          this.$emit("getrescuestatus", this.isRescue);
        }
      });
    },
    // 控制台点击
    doGetVncUrl(e, remember_operate_password = 0) {
      // if (!this.client_operate_password) {
      //   this.$refs.safeRef.openDialog("doGetVncUrl");
      //   return;
      // }
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";
      const params = {
        id: this.id,
        client_operate_password,
        client_operate_methods: "doGetVncUrl",
        remember_operate_password,
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
          this.loading2 = false;
          if (err.data.data) {
            if (
              !client_operate_password &&
              err.data.data.operate_password === 1
            ) {
              return;
            } else {
              return this.$message.error(err.data.msg);
            }
          }
          this.$message.error(err.data.msg);
        });
    },
    getVncUrl() {
      this.doGetVncUrl();
    },
    // 开机
    doPowerOn(e, remember_operate_password = 0) {
      // if (!this.client_operate_password) {
      //   this.$refs.safeRef.openDialog("doPowerOn");
      //   return;
      // }
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";
      this.onOffvisible = false;
      const params = {
        id: this.id,
        client_operate_password,
        client_operate_methods: "doPowerOn",
        remember_operate_password,
      };
      this.loading1 = true;
      powerOn(params)
        .then((res) => {
          if (res.data.status === 200) {
            this.$message.success(res.data.msg);
            this.status = "operating";
            this.getCloudStatus();
            this.loading1 = false;
          }
        })
        .catch((err) => {
          this.loading1 = false;
          if (err.data.data) {
            if (
              !client_operate_password &&
              err.data.data.operate_password === 1
            ) {
              return;
            } else {
              return this.$message.error(err.data.msg);
            }
          }
          this.$message.error(err.data.msg);
        });
    },
    // 关机
    doPowerOff(e, remember_operate_password = 0) {
      // if (!this.client_operate_password) {
      //   this.$refs.safeRef.openDialog("doPowerOff");
      //   return;
      // }
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";
      this.onOffvisible = false;
      const params = {
        id: this.id,
        client_operate_password,
        client_operate_methods: "doPowerOff",
        remember_operate_password,
      };
      this.loading1 = true;
      powerOff(params)
        .then((res) => {
          if (res.data.status === 200) {
            this.$message.success(res.data.msg);
            this.status = "operating";
            this.getCloudStatus();
          }
          this.loading1 = false;
        })
        .catch((err) => {
          this.loading1 = false;
          if (err.data.data) {
            if (
              !client_operate_password &&
              err.data.data.operate_password === 1
            ) {
              return;
            } else {
              return this.$message.error(err.data.msg);
            }
          }
          this.$message.error(err.data.msg);
        });
    },
    // 重启
    doReboot(e, remember_operate_password = 0) {
      // if (!this.client_operate_password) {
      //   this.$refs.safeRef.openDialog("doReboot");
      //   return;
      // }
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";
      this.rebotVisibel = false;
      const params = {
        id: this.id,
        client_operate_password,
        client_operate_methods: "doReboot",
        remember_operate_password,
      };
      this.loading1 = true;
      reboot(params)
        .then((res) => {
          if (res.data.status === 200) {
            this.$message.success(res.data.msg);
            this.status = "operating";
            this.getCloudStatus();
          }
          this.loading1 = false;
        })
        .catch((err) => {
          this.loading1 = false;
          if (err.data.data) {
            if (
              !client_operate_password &&
              err.data.data.operate_password === 1
            ) {
              return;
            } else {
              return this.$message.error(err.data.msg);
            }
          }
          this.$message.error(err.data.msg);
        });
    },
    // 强制重启
    doHardReboot(e, remember_operate_password = 0) {
      // if (!this.client_operate_password) {
      //   this.$refs.safeRef.openDialog("doHardReboot");
      //   return;
      // }
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";
      const params = {
        id: this.id,
        client_operate_password,
        client_operate_methods: "doHardReboot",
        remember_operate_password,
      };
      this.loading1 = true;
      hardReboot(params)
        .then((res) => {
          if (res.data.status === 200) {
            this.$message.success(res.data.msg);
            this.status = "operating";
            this.getCloudStatus();
          }
          this.loading1 = false;
        })
        .catch((err) => {
          this.loading1 = false;
          if (err.data.data) {
            if (
              !client_operate_password &&
              err.data.data.operate_password === 1
            ) {
              return;
            } else {
              return this.$message.error(err.data.msg);
            }
          }
          this.$message.error(err.data.msg);
        });
    },
    // 强制关机
    doHardOff(e, remember_operate_password = 0) {
      // if (!this.client_operate_password) {
      //   this.$refs.safeRef.openDialog("doHardOff");
      //   return;
      // }
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";
      const params = {
        id: this.id,
        client_operate_password,
        client_operate_methods: "doHardOff",
        remember_operate_password,
      };
      this.loading1 = true;
      hardOff(params)
        .then((res) => {
          if (res.data.status === 200) {
            this.$message.success(res.data.msg);
            this.status = "operating";
            this.getCloudStatus();
          }
          this.loading1 = false;
        })
        .catch((err) => {
          this.loading1 = false;
          if (err.data.data) {
            if (
              !client_operate_password &&
              err.data.data.operate_password === 1
            ) {
              return;
            } else {
              return this.$message.error(err.data.msg);
            }
          }
          this.$message.error(err.data.msg);
        });
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
      if (e == this.renewOrderId) {
        // 刷新实例详情
        this.getHostDetail();
        return true;
      }
      if (e == this.diskOrderId) {
        this.doGetDiskList();
      }
      if (e == this.bsOrderId) {
        this.getConfigData();
        this.getBackupList();
        this.getSnapshotList();
        this.getCloudDetail();
      }
      this.getIpList();
      this.getIpv6List();
      this.getCloudDetail();
      this.doGetDiskList();
      this.getConfigData();
      this.getHostDetail();
      // 重新检查当前选择镜像是否购买
      this.doCheckImage();
      this.getIpDetail();
      // 刷新流量
      if (this.activeName == "1") {
        this.doGetFlow();
        this.getIpDetail();
      }
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
      this.getCycleList();
    },
    // 移除升降级优惠码
    removeUpDiscountCode(flag = true) {
      this.upParams.isUseDiscountCode = false;
      this.upParams.customfield.promo_code = "";
      this.upParams.code_discount = 0;
      if (flag) {
        this.getCycleList();
      }
    },
    // 升降级使用代金券
    upUseCash(val) {
      this.cashObj = val;
      const price = val.price ? Number(val.price) : 0;
      this.upParams.cash_discount = price;
      this.upParams.customfield.voucher_get_id = val.id;
      this.getCycleList();
    },

    // 升降级移除代金券
    upRemoveCashCode() {
      this.$refs.cashRef.closePopver();
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
    // 选中/取消防御
    chooseDefence(e, c) {
      this.defenseName = c.desc;
      this.params.peak_defence = c.value;
      setTimeout(() => {
        this.getCycleList();
      }, 0);
      e.preventDefault();
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
      this.$refs.cashRef.closePopver();
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
      if (this.renewBtnLoading) return;
      this.renewBtnLoading = true;
      // 获取续费页面信息
      const params = {
        id: this.id,
      };
      this.isShowRenew = true;
      this.renewLoading = true;
      renewPage(params)
        .then(async (res) => {
          if (res.data.status === 200) {
            this.renewBtnLoading = false;
            this.renewPageData = res.data.data.host;
            this.renewActiveId = this.renewPageData[0].id;
            this.renewParams.billing_cycle =
              this.renewPageData[0].billing_cycle;
            this.renewParams.duration = this.renewPageData[0].duration;
            this.renewParams.original_price = this.renewPageData[0].price;
            this.renewParams.base_price = this.renewPageData[0].base_price;
          }
          this.renewLoading = false;
        })
        .catch((err) => {
          this.renewBtnLoading = false;
          this.renewLoading = false;
          this.$message.error(err.data.msg);
        });
    },
    getRenewPrice() {
      renewPage({id: this.id})
        .then(async (res) => {
          if (res.data.status === 200) {
            this.renewPriceList = res.data.data.host;
          }
        })
        .catch((err) => {
          this.renewPriceList = [];
        });
    },
    // 续费弹窗关闭
    renewDgClose() {
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
              this.$message.success(res.data.msg);
              this.getHostDetail();
            } else {
              this.isShowRenew = false;
              this.renewOrderId = res.data.data.id;
              const orderId = res.data.data.id;
              const amount = this.renewParams.totalPrice;
              this.$refs.topPayDialog.showPayDialog(orderId, amount);
            }
          }
        })
        .catch((err) => {
          this.$message.error(err.data.msg);
        });
    },
    // 续费周期点击
    async renewItemChange(item) {
      this.reRemoveCashCode();
      this.renewLoading = true;
      this.renewActiveId = item.id;
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
            this.$message.error(err.data.msg);
            this.removeRenewDiscountCode();
          });
      }
      this.renewLoading = false;
    },
    // 升降级点击
    showUpgrade() {
      if (this.isPackage) {
        // 套餐版
        this.getPackageInfo();
      } else {
        this.getLineDetails();
        this.isShowUpgrade = true;
        this.params.cpu = this.cloudData.cpu * 1;
        this.params.memory = this.cloudData.memory * 1;
        this.params.image_id = this.cloudData.image.id * 1;
        this.$message({
          showClose: true,
          message: lang.common_cloud_text54,
          type: "warning",
          duration: 10000,
        });
      }
    },
    // 获取可升级套餐
    async getPackageInfo() {
      try {
        const res = await getPackageList({id: this.id});
        this.recommendList = res.data.data.list;
        if (this.recommendList.length === 0) {
          return this.$message.error(lang.no_upgrade);
        }
        if (this.recommendList.length > 0) {
          this.recommend_config_id = this.recommendList[0].id;
          this.isShowUpgrade = true;
          this.$message({
            showClose: true,
            message: lang.common_cloud_text54,
            type: "warning",
            duration: 10000,
          });
          this.getCycleList();
        }
      } catch (error) {
        this.$message.error(error.data.msg);
      }
    },
    changeRecommend(item) {
      this.recommend_config_id = item.id;
      this.getCycleList();
    },
    // 关闭升降级弹窗
    upgradeDgClose() {
      this.isShowUpgrade = false;
      this.removeUpDiscountCode(false);
      this.reRemoveCashCode();
    },
    // 获取升降级价格
    getCycleList() {
      this.upgradePriceLoading = true;
      let type = "";
      const params = {
        id: this.id,
      };
      if (this.isPackage) {
        params.recommend_config_id = this.recommend_config_id;
        type = "package";
      } else {
        type = "custom";
        params.cpu = this.params.cpu;
        params.memory = this.params.memory;
        params.bw = this.params.bw;
        params.flow = this.params.flow;
        params.peak_defence = this.params.peak_defence;
      }
      // const params = {
      //   id: this.id,
      //   memory: this.params.memory,
      //   bw: this.params.bw,
      //   flow: this.params.flow,
      //   peak_defence: this.params.peak_defence,
      // };

      upgradePackagePrice(type, params)
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
                  this.$message.error(err.data.msg);
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
      let type = "";
      const params = {
        id: this.id,
      };
      if (this.isPackage) {
        params.recommend_config_id = this.recommend_config_id;
        params.customfield = this.upParams.customfield;
        type = "package";
      } else {
        type = "custom";
        params.cpu = this.params.cpu;
        params.memory = this.params.memory;
        params.bw = this.params.bw;
        params.flow = this.params.flow;
        params.peak_defence = this.params.peak_defence;
        params.customfield = this.upParams.customfield;
      }
      // const params = {
      //   id: this.id,
      //   cpu: this.params.cpu,
      //   memory: this.params.memory,
      //   bw: this.params.bw,
      //   flow: this.params.flow,
      //   peak_defence: this.params.peak_defence,
      //   customfield: this.upParams.customfield,
      // };
      this.loading4 = true;
      upgradeOrder(type, params)
        .then((res) => {
          if (res.data.status === 200) {
            this.$message.success(lang.common_cloud_text56);
            this.isShowUpgrade = false;
            const orderId = res.data.data.id;
            // 调支付弹窗
            this.$refs.topPayDialog.showPayDialog(orderId, 0);
          } else {
            this.$message.error(err.data.msg);
          }
        })
        .catch((err) => {
          this.$message.error(err.data.msg);
        })
        .finally(() => {
          this.loading4 = false;
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
      this.getCycleList();
    },

    // 取消停用
    quitRefund(e, remember_operate_password = 0) {
      // if (!this.client_operate_password) {
      //   this.$refs.safeRef.openDialog("quitRefund");
      //   return;
      // }
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";
      const params = {
        id: this.refundData.id,
        client_operate_password,
        client_operate_methods: "quitRefund",
        remember_operate_password,
      };
      cancel(params)
        .then((res) => {
          if (res.data.status == 200) {
            this.$message.success(lang.common_cloud_text57);
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
              return this.$message.error(err.data.msg);
            }
          }
          this.$message.error(err.data.msg);
        });
    },
    // 关闭停用
    refundDgClose() {},
    // 删除实例点击
    showRefund() {
      const params = {
        host_id: this.id,
      };
      // refundMsg(params).then(res => {
      //     if (res.data.status === 200) {
      //         console.log(res);
      //     }
      // })
      // 获取停用页面信息
      refundPage(params).then((res) => {
        if (res.data.status == 200) {
          this.refundPageData = res.data.data;
          // if (this.refundPageData.allow_refund === 0) {
          //     this.$message.warning("不支持退款")
          // } else {
          //     this.isShowRefund = true
          // }
          this.isShowRefund = true;
        }
      });
    },
    // 关闭停用弹窗
    refundDgClose() {
      this.isShowRefund = false;
    },
    // 停用弹窗提交
    subRefund(e, remember_operate_password = 0) {
      const params = {
        host_id: this.id,
        suspend_reason: this.refundParams.suspend_reason,
        type: this.refundParams.type,
        client_operate_password: "",
        client_operate_methods: "subRefund",
        remember_operate_password,
      };
      if (!params.suspend_reason) {
        this.$message.error(lang.common_cloud_text58);
        return false;
      }
      if (!params.type) {
        this.$message.error(lang.common_cloud_text59);
        return false;
      }
      // if (!this.client_operate_password) {
      //   this.$refs.safeRef.openDialog("subRefund");
      //   return;
      // }
      params.client_operate_password = this.client_operate_password;
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";
      refund(params)
        .then((res) => {
          if (res.data.status == 200) {
            this.$message.success(lang.common_cloud_text60);
            this.isShowRefund = false;
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
              return this.$message.error(err.data.msg);
            }
          }
          this.$message.error(err.data.msg);
        });
    },
    // 管理开始
    // 进行开关机
    toChangePower() {
      if (this.powerStatus == "on") {
        this.doPowerOn();
      }
      if (this.powerStatus == "off") {
        this.doPowerOff();
      }
      if (this.powerStatus == "rebot") {
        this.doReboot();
      }
      if (this.powerStatus == "hardRebot") {
        this.doHardReboot();
      }
      if (this.powerStatus == "hardOff") {
        this.doHardOff();
      }
      this.isShowPowerChange = false;
    },
    // 重置密码点击
    showRePass() {
      this.errText = "";
      this.rePassData = {
        password: "",
        code: "",
        checked: false,
      };
      this.isShowRePass = true;
    },
    // 关闭重置密码弹窗
    rePassDgClose() {
      this.isShowRePass = false;
    },
    // 重置密码提交
    rePassSub(e, remember_operate_password = 0) {
      const data = this.rePassData;
      let isPass = true;
      if (!data.password) {
        isPass = false;
        this.errText = lang.common_cloud_text61;
        return false;
      }
      if (!data.code && this.cloudConfig.reset_password_sms_verify === 1) {
        isPass = false;
        this.errText = lang.account_tips33;
        return false;
      }
      if (!data.checked && this.powerStatus == "off") {
        isPass = false;
        this.errText = lang.common_cloud_text62;
        return false;
      }
      // if (!this.client_operate_password) {
      //   this.$refs.safeRef.openDialog("rePassSub");
      //   return;
      // }
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";
      if (isPass) {
        this.loading5 = true;
        this.errText = "";
        const params = {
          id: this.id,
          password: data.password,
          code: data.code,
          client_operate_password,
          client_operate_methods: "rePassSub",
          remember_operate_password,
        };
        resetPassword(params)
          .then((res) => {
            if (res.data.status === 200) {
              this.$message.success(res.data.msg);
              this.isShowRePass = false;
            }
            this.getCloudStatus();
            this.loading5 = false;
          })
          .catch((err) => {
            this.loading5 = false;
            if (err.data.data) {
              if (
                !client_operate_password &&
                err.data.data.operate_password === 1
              ) {
                return;
              } else {
                return this.$message.error(err.data.msg);
              }
            }
            this.errText = err.data.msg;
          });
      }
    },
    // 救援模式点击
    showRescueDialog() {
      this.errText = "";
      this.rescueData = {
        type: "1",
        password: "",
      };
      this.isShowRescue = true;
    },
    // 关闭救援模式弹窗
    rescueDgClose() {
      this.isShowRescue = false;
    },
    // 救援模式提交按钮
    rescueSub(e, remember_operate_password = 0) {
      let isPass = true;
      if (!this.rescueData.type) {
        isPass = false;
        this.errText = lang.common_cloud_text64;
        return false;
      }
      if (!this.rescueData.password) {
        isPass = false;
        this.errText = lang.common_cloud_text65;
        return false;
      }
      // if (!this.client_operate_password) {
      //   this.$refs.safeRef.openDialog("rescueSub");
      //   return;
      // }
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";
      if (isPass) {
        this.errText = "";
        this.loading3 = true;
        // 调用救援系统接口
        const params = {
          id: this.id,
          type: this.rescueData.type,
          password: this.rescueData.password,
          client_operate_password,
          client_operate_methods: "rescueSub",
          remember_operate_password,
        };
        rescue(params)
          .then((res) => {
            if (res.data.status === 200) {
              this.$message.success(res.data.msg);
              this.getRemoteInfo();
              this.status = "operating";
              this.getCloudStatus();
            }
            this.isShowRescue = false;
            this.loading3 = false;
          })
          .catch((err) => {
            this.loading3 = false;
            if (err.data.data) {
              if (
                !client_operate_password &&
                err.data.data.operate_password === 1
              ) {
                return;
              } else {
                return this.$message.error(err.data.msg);
              }
            }
            this.errText = err.data.msg;
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
    reQuitSub(e, remember_operate_password = 0) {
      // if (!this.client_operate_password) {
      //   this.$refs.safeRef.openDialog("reQuitSub");
      //   return;
      // }
      const client_operate_password = this.client_operate_password;
      this.client_operate_password = "";
      const params = {
        id: this.id,
        client_operate_password,
        client_operate_methods: "reQuitSub",
        remember_operate_password,
      };

      exitRescue(params)
        .then((res) => {
          if (res.data.status === 200) {
            this.$message.success(res.data.msg);
            this.getRemoteInfo();
            this.isShowQuit = false;
            this.status = "operating";
            this.getCloudStatus();
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
              return this.$message.error(err.data.msg);
            }
          }
          this.$message.error(err.data.msg);
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
              if (item.is_free === 1) {
                this.freeDataObj = {
                  id: item.id,
                  size: item.size
                };
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
      if (item.selectList[0].type === "radio") {
        item.size = item.selectList[0][item.type][0]?.value;
      } else {
        item.size = item.selectList[0][item.type].min_value;
      }
    },
    goSSHpage(id) {
      location.href = `/security_ssh.htm`;
    },
    // 新增磁盘项目
    addMoreDisk() {
      // 最多存在的磁盘数目
      const max = this.configObj.disk_limit_num;
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
            // 磁盘类型传的名字，商品多语言需处理显示
            if (
              !obj.disk_typeList
                .map((type) => type.value)
                .includes(items.other_config.disk_type)
            ) {
              const type = items.other_config.disk_type;
              const showName =
                items.customfield?.multi_language?.other_config?.disk_type ||
                type;
              obj.disk_typeList.push({
                value: type,
                label: showName,
              });
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
            if (
              !obj.disk_typeList
                .map((type) => type.value)
                .includes(items.other_config.disk_type)
            ) {
              const type = items.other_config.disk_type;
              const showName =
                items.customfield?.multi_language?.other_config?.disk_type ||
                type;
              obj.disk_typeList.push({
                value: type,
                label: showName,
              });
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
        obj.disk_typeList
          .map((type) => type.value)
          .forEach((item) => {
            const arr = [];
            const arr1 = [];
            if (obj[item].config) {
              obj[item].config.forEach((is) => {
                arr.push(...this.createArr([is[0], is[1]]));
                arr1.push(...is);
              });
            }
            obj[item].min_value = Math.min.apply(Math, arr1);
            obj[item].max_value = Math.max.apply(Math, arr1);
            obj[item].tips = this.createTip(arr);
          });
        if (this.dataDiskList.length !== 0) {
          const dataType = obj.disk_typeList.map((type) => type.value);
          itemData.size =
            this.dataDiskList[0].type === "radio"
              ? this.dataDiskList[0]?.value || ""
              : obj[dataType[0]]?.min_value;

          itemData.disk_type = this.dataDiskList[0].other_config.data_disk_type;
          itemData.selectList = [obj];
          itemData.min_value =
            this.dataDiskList[0].type === "radio"
              ? 0
              : obj[dataType[0]]?.min_value;
          itemData.max_value =
            this.dataDiskList[0].type === "radio"
              ? 0
              : obj[dataType[0]]?.max_value;
          itemData.type =
            this.dataDiskList[0].type === "radio" ? dataType[0] : dataType[0];
        }
        diskData.push(itemData);
        diskData.map((item, index) => {
          item.index = index + 1;
        });
        this.moreDiskData = diskData;
        this.handlerType(this.moreDiskData, "data");
      } else {
        this.$message({
          message:
            lang.mf_tip29 + this.configObj.disk_limit_num + lang.mf_tip36,
          type: "warning",
        });
      }
    },
    // 初始化处理系统盘，数据盘类型
    handlerType(data, type) {},

    /* 弹性磁盘 */
    async getAloneDiskList() {
      try {
        this.elasticDiskLoading = true;
        const res = await getConnectDisk({
          id: this.productParams.data_center_id,
        });
        this.elasticDisk = res.data.data.list;
        this.elasticDiskLoading = false;
      } catch (error) {
        this.elasticDiskLoading = false;
        this.$message.error(error.data.msg);
      }
    },
    // 获取其他配置
    getConfigData() {
      const params = {
        id: this.product_id,
      };
      getOrderConfig(params)
        .then((res) => {
          if (res.data.status === 200) {
            this.configData = res.data.data;
            this.memory_unit = this.configData.config.memory_unit;
            this.systemDiskList = res.data.data.system_disk;
            this.dataDiskList = res.data.data.data_disk;
            this.memoryList = res.data.data.memory;
            this.cpuList = res.data.data.cpu;
            this.configLimitList = res.data.data.limit_rule;
            this.configObj = res.data.data.config;
            this.backup_config = res.data.data.backup_config;
            this.snap_config = res.data.data.snap_config;

            if (this.memoryList.length > 0) {
              if (this.memoryList[0].type === "radio") {
                this.memoryType = true;
                this.memory_arr = this.memoryList.map((item) => item.value);
              } else {
                // 范围的时候生成默认范围数组
                this.memory_arr = this.memoryList.reduce((all, cur) => {
                  all.push(...this.createArr([cur.min_value, cur.max_value]));
                  return all;
                }, []);
                this.memoryType = false;
              }
            }
          }
        })
        .catch((err) => {
          console.log("error", err);
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
      // if (
      //   this.cloudData.line?.sync_firewall_rule == 1 &&
      //   val * 1 < this.cloudData.ip_num * 1
      // ) {
      //   return;
      // }
      this.ipv4DelArr = [];
      if (this.ipValue !== val) {
        this.ipValue = val;
        this.getIpPrice();
      }
    },
    selectIpv6Value(val) {
      this.ipv6DelArr = [];
      if (this.ipv6Value !== val) {
        this.ipv6Value = val;
        this.getIpPrice();
      }
    },
    // 获取附加ip价格
    getIpPrice() {
      this.ipPriceLoading = true;
      ipPrice({
        id: this.id,
        ip_num: this.ipValue,
        ipv6_num: this.ipv6Value,
      })
        .then(async (res) => {
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
                this.$message.error(err.data.msg);
                this.ipCodePrice = 0;
              });
          }
          this.ipMoney =
            (res.data.data.price * 1000 -
              this.ipDiscountkDisPrice * 1000 -
              this.ipCodePrice * 1000) /
            1000;
          this.ipPriceLoading = false;
        })
        .catch((err) => {
          this.ipPriceLoading = false;
          this.ipMoney = 0;
          // this.$message.error(err.data.msg);
        });
    },
    goPay() {
      if (this.hostData.status === "Unpaid") {
        this.$refs.topPayDialog.showPayDialog(this.hostData.order_id);
      }
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
          this.$message.error(err.data.msg);
        });
    },
    // 变化监听
    sliderChange(val, item) {
      const arr = [];
      item.selectList.forEach((i) => {
        arr.push([i.min_value, i.max_value]);
      });
      item.size = this.mapToRange(val, arr, item.min_value, val > item.size);
    },
    changeDataNum(val, oldval, item) {
      // 数据盘数量改变计算价格
      item.size = this.mapToRange(
        val,
        item.selectList[0][item.type].config,
        item.selectList[0][item.type].config[0],
        val > oldval
      );
    },
    // 磁盘挂载
    handelMount(id) {
      this.$confirm(lang.mf_tip30)
        .then(() => {
          mount({id: this.id, disk_id: id})
            .then((res) => {
              this.$message.success(res.data.msg);
              this.doGetDiskList();
            })
            .catch((err) => {
              this.$message.error(err.data.msg);
            });
        })
        .catch((_) => {});
    },
    copyPass(text) {
      if (navigator.clipboard && window.isSecureContext) {
        // navigator clipboard 向剪贴板写文本
        this.$message.success(lang.index_text32);
        return navigator.clipboard.writeText(text);
      } else {
        // 创建text area
        const textArea = document.createElement("textarea");
        textArea.value = text;
        // 使text area不在viewport，同时设置不可见
        document.body.appendChild(textArea);
        // textArea.focus()
        textArea.select();
        this.$message.success(lang.index_text32);
        return new Promise((res, rej) => {
          // 执行复制命令并移除文本框
          document.execCommand("copy") ? res() : rej();
          textArea.remove();
        });
      }
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
        this.$message.error(lang.mf_tip31);
        return;
      }
      addSafe({id: this.safeID, host_id: this.id})
        .then((res) => {
          this.$message.success(res.data.msg);
          this.safeDialogShow = false;
          this.getCloudDetail();
        })
        .catch((err) => {
          this.$message.error(err.data.msg);
        });
    },
    // 磁盘卸载
    handelUnload(id) {
      this.$confirm(lang.mf_tip32)
        .then(() => {
          unmount({id: this.id, disk_id: id})
            .then((res) => {
              this.$message.success(res.data.msg);
              this.doGetDiskList();
            })
            .catch((err) => {
              this.$message.error(err.data.msg);
            });
        })
        .catch((_) => {});
    },
    mapToRange(value, rangeArray, deflute, isAdd = true) {
      for (let i = 0; i < rangeArray.length; i++) {
        const range = rangeArray[i];
        // 在范围内 直接返回
        if (value >= range[0] && value <= range[1]) {
          return value;
        }
        // 超出范围 小于最小值 取最小值
        if (value < range[0] && i === 0) {
          return range[0];
        }
        // 超出范围 大于最大值 取最大值
        if (value > range[1] && i === rangeArray.length - 1) {
          return range[1];
        }
        // 超出范围 在两个区间之间 取最近的区间
        if (value > range[1] && value < rangeArray[i + 1][0]) {
          return isAdd ? rangeArray[i + 1][0] : range[1];
        }
        if (value < range[0] && value > rangeArray[i - 1][1]) {
          return rangeArray[i - 1][1];
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
                  this.$message.error(err.data.msg);
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
            this.$message.error(error.data.msg);
            this.moreDiscountkDisPrice = 0;
            this.moreDiskPrice = 0;
            this.moreCodePrice = 0;
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
          // item.is_free === 0 &&
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
              this.expansionDiscount = 0;
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
                  this.$message.error(err.data.msg);
                  this.expansionCodePrice = 0;
                });
            }
            this.expansionDiskPrice =
              (price * 1000 -
                this.expansionDiscount * 1000 -
                this.expansionCodePrice * 1000) /
                1000 >
              0
                ? (price * 1000 -
                    this.expansionDiscount * 1000 -
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
      try {
        getLineConfig({
          id: this.product_id,
          line_id: this.cloudData.line.id,
        }).then((res) => {
          if (
            (res.data.data.ip && res.data.data.ip.length > 0) ||
            (res.data.data.ipv6 && res.data.data.ipv6.length > 0)
          ) {
            this.ipValueData = res.data.data.ip || [];
            this.ipv6ValueData = res.data.data.ipv6 || [];
            if (
              this.ipValueData.length === 0 &&
              this.ipv6ValueData.length === 0
            ) {
              return this.$message.error(lang.mf_tip35);
            }
            this.ipValue = this.cloudData.ip_num;
            this.ipv6Value = this.cloudData.ipv6_num;
            if (res.data.data.ip && res.data.data.ip.length > 0) {
              const fArr = [];
              this.ipValueData.forEach((item) => {
                fArr.push(...this.createArr([item.min_value, item.max_value]));
              });
              this.ipv4Arr = fArr;
              this.ipv4Tip = this.createTip(fArr);
            }
            if (res.data.data.ipv6 && res.data.data.ipv6.length > 0) {
              const fArr = [];
              this.ipv6ValueData.forEach((item) => {
                fArr.push(...this.createArr([item.min_value, item.max_value]));
              });
              this.ipv6Arr = fArr;
              this.ipv6Tip = this.createTip(fArr);
            }
            this.getIpPrice();
            this.isShowIp = true;
          } else {
            this.$message.error(lang.mf_tip33);
          }
        });
      } catch (error) {
        console.log("error", error);
      }
    },
    changeIpNum(type, num) {
      if (type === "ipv4") {
        this.ipv4DelArr = [];
      } else {
        this.ipv6DelArr = [];
      }
      const val = type === "ipv4" ? "ipValue" : "ipv6Value";
      if (!this[`${type}Arr`].includes(num)) {
        this[`${type}Arr`].forEach((item, index) => {
          if (num > item && num < this[`${type}Arr`][index + 1]) {
            this[`${val}`] =
              num - item > this[`${type}Arr`][index + 1] - num
                ? this[`${type}Arr`][index + 1]
                : item;
          }
        });
      }
      this.getIpPrice();
    },
    // 获取vpc网络列表
    getVpcNetwork() {
      this.vpcLoading = true;
      vpcNetwork({id: this.id, ...this.vpcParams})
        .then((res) => {
          this.vpcDataList = res.data.data.list;
          this.vpcParams.total = res.data.data.count;
          this.vpcLoading = false;
        })
        .catch((err) => {
          this.vpcLoading = false;
          this.$message.error(err.msg.data);
        });
    },
    handDelVpc(id) {
      this.$confirm(lang.mf_tip34)
        .then(() => {
          delVpc({id: this.id, vpc_network_id: id})
            .then((res) => {
              this.$message.success(res.data.msg);
              this.getVpcNetwork();
            })
            .catch((err) => {
              this.$message.error(err.data.msg);
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
          this.$message.success(res.data.msg);
          this.isShowAddVpc = false;
          this.getVpcNetwork();
        })
        .catch((err) => {
          this.$message.error(err.data.msg);
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
          this.$message.success(res.data.msg);
          this.isShowengine = false;
          this.isSubmitEngine = false;
          this.getVpcNetwork();
        })
        .catch((err) => {
          this.isSubmitEngine = false;
          this.$message.error(err.data.msg);
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
      // cloudList: 之前的接口返回的数据很多不可用，现改为 getUsableVpcNetwork
      getUsableVpcNetwork(this.productParams).then((res) => {
        this.productOptions = res.data.data.list;
        this.engineSearchLoading = false;
      });
    },
    // 提交新增IP
    async subAddIp () {
      try {
        if ((this.ipv4DelArr.length === 0 || this.ipv4DelArr.length === this.calcDelNum('ipv4'))
          && (this.ipv6DelArr.length === 0 || this.ipv6DelArr.length === this.calcDelNum('ipv6'))) { }
        else {
          return this.$message.error(lang.ip_down_tip3);
        }
        this.submitLoaing = true;
        const res = await ipOrder({
          id: this.id,
          ip_num: this.ipValue,
          ipv6_num: this.ipv6Value,
          ip: this.ipv4DelArr,
          ipv6: this.ipv6DelArr
        });
        const orderId = res.data.data.id;
        this.isShowIp = false;
        this.submitLoaing = false;
        this.$refs.topPayDialog.showPayDialog(orderId);
      } catch (error) {
        this.submitLoaing = false;
        this.$message.error(error.data.msg);
      }
    },
    // 提交扩容
    subExpansion() {
      let newSize = [];
      this.oldDiskList.forEach((item) => {
        // item.is_free === 0 &&
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
          this.$message.error(err.data.msg);
        });
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
    // ipv6列表
    getIpv6List() {
      const params = {
        id: this.id,
        ...this.ipv6Params,
      };
      this.ipv6Loading = true;
      ipv6List(params).then((res) => {
        if (res.data.status === 200) {
          this.ipv6Params.total = res.data.data.count;
          this.ipv6DataList = res.data.data.list;
        }
        this.ipv6Loading = false;
      });
    },
    // 获取弹性IP列表
    async getElasticIpList() {
      try {
        this.elasticLoading = true;
        const res = await getConnectList({
          id: this.productParams.data_center_id,
          ...this.elasticParams,
        });
        this.elasticList = res.data.data.list;
        this.elasticLoading = false;
        this.elasticParams.total = res.data.data.count;
      } catch (error) {
        this.elasticLoading = false;
        this.$message.error(data.msg);
      }
    },
    /* 关联/取消关联按钮 type： ip disk  */
    handlerConnect(type, way, row) {
      this.connectType = type;
      this.connectWay = way;
      this.curId = row.id;
      this.isShowConnect = true;
      this.connectCheck = false;
      this.errText = "";
      if (way === "add") {
        this.calcDes = lang.connect_tip2;
      } else {
        this.calcDes = lang.connect_tip1;
      }
      this.curIp = this.cloudData.ip;
    },
    async submitConnect() {
      try {
        if (
          !this.connectCheck &&
          this.powerStatus === "off" &&
          this.connectType === "mf_cloud_ip"
        ) {
          return (this.errText = lang.common_cloud_text62);
        }
        const params = {
          id: this.curId,
        };
        if (this.connectWay === "add") {
          params.host_id = this.id;
        }
        this.loading5 = true;
        const res = await handlerConnectResource(
          this.connectType,
          this.connectWay,
          params
        );
        this.loading5 = false;
        this.isShowConnect = false;
        if (this.connectType === "mf_cloud_ip") {
          this.getIpList();
          this.getIpv6List();
          this.getElasticIpList();
        } else {
          this.getAloneDiskList();
        }
        this.$message.success(res.data.msg);
      } catch (error) {
        this.loading5 = false;
        this.$message.error(error.data.msg);
      }
    },
    elasticSizeChange(e) {
      this.elasticParams.limit = e;
      this.elasticParams.page = 1;
      // 获取列表
      this.getElasticIpList();
    },
    elasticCurrentChange(e) {
      this.elasticParams.page = e;
      this.getElasticIpList();
    },
    netSizeChange(e) {
      this.ipv6Params.limit = e;
      this.ipv6Params.page = 1;
      this.getIpList();
    },
    ipv6SizeChange(e) {
      this.netParams.limit = e;
      this.netParams.page = 1;
      this.getIpv6List();
    },
    netCurrentChange(e) {
      this.netParams.page = e;
      this.getIpList();
    },
    vpcSizeChange(e) {
      this.vpcParams.limit = e;
      this.vpcParams.page = 1;
      // 获取列表
      this.getVpcNetwork();
    },
    vpcCurrentChange(e) {
      this.vpcParams.page = e;
      this.getVpcNetwork();
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
    // 日志开始
    logSizeChange(e) {
      this.logParams.limit = e;
      this.logParams.page = 1;
      // 获取列表
      this.getLogList();
    },
    logCurrentChange(e) {
      this.logParams.page = e;
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
            this.logDataList = res.data.data.list;
          }
          this.logLoading = false;
        })
        .catch((error) => {
          this.logLoading = false;
        });
    },
    // 备份与快照 开始
    // 备份列表
    getBackupList() {
      this.backLoading = true;
      const params = {
        id: this.id,
        ...this.params1,
      };
      backupList(params)
        .then((res) => {
          if (res.data.status === 200) {
            this.dataList1 = res.data.data.list;
            this.params1.total = res.data.data.count;
          }
          this.backLoading = false;
        })
        .catch((err) => {
          this.backLoading = true;
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
            this.dataList2 = res.data.data.list;
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
      this.natDialog = false;
      this.isShwoCreateBs = false;
    },
    // 创建备份、快照弹窗提交
    subCgBs() {
      const data = this.createBsData;
      let isPass = true;
      if (!data.name) {
        isPass = false;
        this.errText = lang.security_tips12;
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
                this.$message.success(lang.common_cloud_text71);
                this.isShwoCreateBs = false;
                this.getBackupList();
              }
              this.cgbsLoading = false;
            })
            .catch((err) => {
              this.errText = err.data.msg;
              this.cgbsLoading = false;
            });
        } else {
          // 调用创建磁盘接口
          createSnapshot(params)
            .then((res) => {
              if (res.data.status === 200) {
                this.$message.success(lang.common_cloud_text72);
                this.isShwoCreateBs = false;
                this.getSnapshotList();
              }
              this.cgbsLoading = false;
            })
            .catch((err) => {
              this.errText = err.data.msg;
              this.cgbsLoading = false;
            });
        }
      }
    },
    // 还原快照、备份 弹窗关闭
    bshyClose() {
      this.isShowhyBs = false;
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
              this.$message.success(res.data.msg);
              this.isShowhyBs = false;
            }
            this.loading3 = false;
          })
          .catch((err) => {
            this.$message.error(err.data.msg);
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
              this.$message.success(res.data.msg);
              this.isShowhyBs = false;
            }
            this.loading3 = false;
          })
          .catch((err) => {
            this.$message.error(err.data.msg);
            this.loading3 = false;
          });
      }
    },
    // 关闭 删除备份、快照弹窗显示
    delBsClose() {
      this.isShowDelBs = false;
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
              this.$message.success(res.data.msg);
              this.isShowDelBs = false;
              this.getBackupList();
            }
            this.loading4 = false;
          })
          .catch((err) => {
            this.$message.error(err.data.msg);
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
              this.$message.success(res.data.msg);
              this.isShowDelBs = false;
              this.getSnapshotList();
            }
            this.loading4 = false;
          })
          .catch((err) => {
            this.$message.error(err.data.msg);
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
      this.backupType = "add";
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
    // 备份快照升降级
    handleBackUpgrade (type) {
      this.backupType = "update";
      if (type == "back") {
        this.isBs = true;
      } else {
        this.isBs = false;
      }
      this.bsData.backNum = this.cloudData.backup_num;
      this.bsData.snapNum = this.cloudData.snap_num;
      this.isShowOpenBs = true;
      this.getBsPrice(true);
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
      this.submitLoading = true;
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
          this.$message.error(err.data.msg);
        }).finally(() => {
          this.submitLoading = false;
        });
    },
    bsSelectChange() {
      this.getBsPrice();
    },
    // 获取开启备份/快照的价格
    async getBsPrice (bol = false) {
      try {
        if (bol) {
          this.$message({
            showClose: true,
            message: lang.common_cloud_text54,
            type: "warning",
            duration: 10000,
          });
        }
        this.bsDataLoading = true;
        const params = {
          id: this.id,
          type: this.isBs ? "backup" : "snap",
          num: this.isBs ? this.bsData.backNum : this.bsData.snapNum,
        };
        const res = await backupConfig(params);
        const price = Number(res.data.data.price);
        this.bsData.money = price;
        if (this.isShowLevel) {
          await clientLevelAmount({
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
              this.$message.error(err.data.msg);
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
      } catch (error) {
        this.bsData.money = 0;
        this.bsDataLoading = false;
        if (!bol) {
          this.$message.error(error.data.msg);
        }
      }
    },
    handleResize() {
      if (this.cpuChart) {
        this.cpuChart.resize();
      }
      if (this.memoryChart) {
        this.memoryChart.resize();
      }
      if (this.diskIoChart) {
        this.diskIoChart.resize();
      }
      if (this.bwChart) {
        this.bwChart.resize();
      }
    },
    // 统计图表开始
    // 获取cpu用量数据
    getCpuList() {
      this.echartLoading1 = true;
      const params = {
        id: this.id,
        start_time: this.startTime,
        type: "cpu",
      };
      chartList(params)
        .then((res) => {
          if (res.data.status === 200) {
            const list = res.data.data.list || [];
            this.cpuData = list;
            if (list.length > 0) {
              this.cpuUseage = list[list.length - 1].value.toFixed(2);
            }
            let x = [];
            let y = [];
            list.forEach((item) => {
              x.push(formateDate(item.time * 1000));
              y.push(item.value.toFixed(2));
            });
            const cpuOption = {
              title: {
                //text: lang.common_cloud_text73,
              },
              tooltip: {
                show: true,
                trigger: "axis",
              },
              grid: {
                top: "5%",
                left: "6%",
                right: "5%",
                bottom: "5%",
                containLabel: true,
              },
              xAxis: {
                type: "category",
                boundaryGap: false,
                data: x,
                axisLabel: {
                  formatter: (value) => {
                    if (this.chartSelectValue === "1") {
                      return value.split(" ")[1];
                    }
                    return value;
                  },
                },
              },
              yAxis: {
                type: "value",
                min: 0,
                max: (value) => {
                  if (value.max < 10) {
                    return 10;
                  } else {
                    return null;
                  }
                },
              },
              series: [
                {
                  name: lang.common_cloud_text74,
                  data: y,
                  type: "line",
                  smooth: true,
                  showSymbol: false,
                  // areaStyle: {},
                },
              ],
            };
            if (!this.cpuChart) {
              this.cpuChart = echarts.init(
                document.getElementById("cpu-echart")
              );
            }
            this.cpuChart.setOption(cpuOption);
            setTimeout(() => {
              this.cpuChart.resize();
            }, 0);
          }
          this.echartLoading1 = false;
        })
        .catch((err) => {
          this.echartLoading1 = false;
        });
    },
    // 获取网络宽度
    getBwList() {
      this.echartLoading2 = true;
      const params = {
        id: this.id,
        start_time: this.startTime,
        type: "bw",
      };
      chartList(params)
        .then((res) => {
          if (res.data.status === 200) {
            const list = res.data.data.list || [];
            this.bwData = list;
            let xAxis = [];
            let yAxis = [];
            let yAxis2 = [];
            const arr = list
              .reduce((all, cur) => {
                all.push(cur.in_bw, cur.out_bw);
                return all;
              }, [])
              .sort((a, b) => a - b);
            let logObj = {
              power: 1,
              divisor: 1024,
            };
            logObj = this.getLogNum(arr[arr.length - 1]);
            const unit = this.convertUnit(logObj.power);
            list.forEach((item) => {
              xAxis.push(formateDate(item.time * 1000));
              yAxis.push((item.in_bw / logObj.divisor).toFixed(2));
              yAxis2.push((item.out_bw / logObj.divisor).toFixed(2));
            });

            const options = {
              // title: {
              //   text: lang.common_cloud_text75,
              // },
              tooltip: {
                show: true,
                trigger: "axis",
              },
              grid: {
                top: "5%",
                left: "6%",
                right: "5%",
                bottom: "5%",
                containLabel: true,
              },
              xAxis: {
                type: "category",
                boundaryGap: false,
                data: xAxis,
                axisLabel: {
                  formatter: (value) => {
                    if (this.chartSelectValue === "1") {
                      return value.split(" ")[1];
                    }
                    return value;
                  },
                },
              },
              yAxis: {
                type: "value",
                max: (value) => {
                  if (value.max < 10) {
                    return 10;
                  } else {
                    return null;
                  }
                },
              },
              series: [
                {
                  name: `${lang.common_cloud_text76}(${unit})`,
                  data: yAxis,
                  type: "line",
                  smooth: true,
                  showSymbol: false,
                },
                {
                  name: `${lang.common_cloud_text77}(${unit})`,
                  data: yAxis2,
                  type: "line",
                  smooth: true,
                  showSymbol: false,
                },
              ],
            };
            if (!this.bwChart) {
              this.bwChart = echarts.init(document.getElementById("bw-echart"));
            }
            this.bwChart.setOption(options);
            setTimeout(() => {
              this.bwChart.resize();
            }, 0);
          }
          this.echartLoading2 = false;
        })
        .catch((err) => {
          this.echartLoading2 = false;
        });
    },
    // 获取磁盘IO
    getDiskLIoList() {
      this.echartLoading3 = true;
      const params = {
        id: this.id,
        start_time: this.startTime,
        type: "disk_io",
      };

      chartList(params)
        .then((res) => {
          if (res.data.status === 200) {
            const list = res.data.data.list || [];
            this.diskIoData = list;
            let xAxis = [];
            let yAxis = [];
            let yAxis2 = [];
            let yAxis3 = [];
            let yAxis4 = [];

            list.forEach((item) => {
              xAxis.push(formateDate(item.time * 1000));
              yAxis.push((item.read_bytes / 1024 / 1024).toFixed(2));
              yAxis2.push(item.read_iops.toFixed(2));
              yAxis3.push((item.write_bytes / 1024 / 1024).toFixed(2));
              yAxis4.push(item.write_iops.toFixed(2));
            });

            const options = {
              // title: {
              //   text: lang.common_cloud_text78,
              // },
              tooltip: {
                show: true,
                trigger: "axis",
              },
              grid: {
                top: "5%",
                left: "6%",
                right: "5%",
                bottom: "5%",
                containLabel: true,
              },
              xAxis: {
                type: "category",
                boundaryGap: false,
                data: xAxis,
                axisLabel: {
                  formatter: (value) => {
                    if (this.chartSelectValue === "1") {
                      return value.split(" ")[1];
                    }
                    return value;
                  },
                },
              },
              yAxis: {
                // name: "单位（B/s）",
                type: "value",
                smooth: true,
                showSymbol: false,
                max: (value) => {
                  if (value.max < 10) {
                    return 10;
                  } else {
                    return null;
                  }
                },
              },
              series: [
                {
                  name: lang.common_cloud_text79,
                  data: yAxis,
                  type: "line",
                  smooth: true,
                  showSymbol: false,
                  // areaStyle: {},
                },
                {
                  name: lang.common_cloud_text80,
                  data: yAxis2,
                  type: "line",
                  smooth: true,
                  showSymbol: false,
                  // areaStyle: {},
                },
                {
                  name: lang.common_cloud_text81,
                  data: yAxis3,
                  type: "line",
                  smooth: true,
                  showSymbol: false,
                  // areaStyle: {},
                },
                {
                  name: lang.common_cloud_text82,
                  data: yAxis4,
                  type: "line",
                  smooth: true,
                  showSymbol: false,
                  //areaStyle: {},
                },
              ],
            };
            if (!this.diskIoChart) {
              this.diskIoChart = echarts.init(
                document.getElementById("disk-io-echart")
              );
            }
            this.diskIoChart.setOption(options);
            setTimeout(() => {
              this.diskIoChart.resize();
            }, 0);
          }
          this.echartLoading3 = false;
        })
        .catch((err) => {
          this.echartLoading3 = false;
        });
    },
    // 获取内存用量
    getMemoryList() {
      this.echartLoading4 = true;
      const params = {
        id: this.id,
        start_time: this.startTime,
        type: "memory",
      };
      chartList(params)
        .then((res) => {
          if (res.data.status === 200) {
            const list = res.data.data.list || [];
            this.memoryData = list;
            if (list.length > 0) {
              const lastData = list[list.length - 1];
              this.memoryUseage =
                lastData.total == 0
                  ? "0.00"
                  : ((lastData.used / lastData.total) * 100).toFixed(2);
            }
            let xAxis = [];
            let yAxis = [];
            let yAxis2 = [];

            list.forEach((item) => {
              xAxis.push(formateDate(item.time * 1000));
              yAxis.push((item.total / 1024 / 1024 / 1024).toFixed(2));
              yAxis2.push((item.used / 1024 / 1024 / 1024).toFixed(2));
            });
            const options = {
              // title: {
              //   text: lang.common_cloud_text83,
              // },
              tooltip: {
                show: true,
                trigger: "axis",
              },
              grid: {
                top: "5%",
                left: "6%",
                right: "5%",
                bottom: "5%",
                containLabel: true,
              },
              xAxis: {
                type: "category",
                boundaryGap: false,
                data: xAxis,
                axisLabel: {
                  formatter: (value) => {
                    if (this.chartSelectValue === "1") {
                      return value.split(" ")[1];
                    }
                    return value;
                  },
                },
              },
              yAxis: {
                type: "value",
                max: (value) => {
                  if (value.max < 10) {
                    return 10;
                  } else {
                    return null;
                  }
                },
              },
              series: [
                {
                  name: lang.common_cloud_text84,
                  data: yAxis,
                  type: "line",
                  smooth: true,
                  showSymbol: false,
                },
                {
                  name: lang.common_cloud_text85,
                  data: yAxis2,
                  type: "line",
                  smooth: true,
                  showSymbol: false,
                },
              ],
            };
            if (!this.memoryChart) {
              this.memoryChart = echarts.init(
                document.getElementById("memory-echart")
              );
            }
            this.memoryChart.setOption(options);
            setTimeout(() => {
              this.memoryChart.resize();
            }, 0);
          }
          this.echartLoading4 = false;
        })
        .catch((err) => {
          this.echartLoading4 = false;
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
      this.getstarttime(e);

      // 重新拉取图表数据
      if (this.activeName === "1") {
        this.getCpuList();
        this.getDiskLIoList();
        this.getMemoryList();
      }
      this.getBwList();
    },
    powerDgClose() {
      this.isShowPowerChange = false;
    },
    // 显示电源操作确认弹窗
    showPowerDialog() {
      const type = this.powerStatus;
      if (type == "on") {
        this.powerTitle = lang.common_cloud_text38;
      }
      if (type == "off") {
        this.powerTitle = lang.common_cloud_text39;
      }
      if (type == "rebot") {
        this.powerTitle = lang.common_cloud_text13;
      }
      if (type == "hardOff") {
        this.powerTitle = lang.common_cloud_text42;
      }
      if (type == "hardRebot") {
        this.powerTitle = lang.common_cloud_text41;
      }
      this.powerType = type;
      this.isShowPowerChange = true;
    },
  },
}).$mount(template);
window.clientOperateVue = clientOperateVue;

window.clientOperateVue = clientOperateVue;
