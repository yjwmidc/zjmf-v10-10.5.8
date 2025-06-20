const {showToast} = vant;
window.lang = Object.assign(window.lang, window.module_lang);
const app2 = Vue.createApp({
  components: {
    topMenu,
    discountCode,
    eventCode,
    customGoods,
    curSelect,
  },
  mounted() {
    this.getConfig();
    this.hasDiscount = havePlugin("PromoCode");
    this.isShowFull = havePlugin("EventPromotion");
    window.addEventListener("message", (event) => this.submitOrder(event));
  },

  data() {
    return {
      lang: window.lang,
      id: "",
      tit: "",
      activeName: "fixed", // fixed flex
      hasDiscount: false,
      commonData: {},
      isShowFull: false,
      eventData: {
        id: "",
        discount: 0,
      },
      country: "",
      countryName: "",
      modelDialogShow: false,
      city: "",
      curImage: 0,
      imageName: "",
      version: "",
      curImageId: "",
      self_defined_field: {},
      dataList: [], // 数据中心
      memMarks: {},
      limitList: [], // 限制
      packageId: "", // 套餐ID
      imageList: [], // 镜像
      filterIamge: [],
      systemDiskList: [], // 系统盘
      dataDiskList: [], // 数据盘
      configLimitList: [], // 限制规则
      cloudIndex: 0,
      cycle: "", // 周期
      cycleList: [],
      qty: 1,
      recommendList: [], // 推荐套餐
      // 区域
      area_name: "",
      isChangeArea: true,
      lineList: [], // 线路
      lineDetail: {}, // 线路详情：bill_type, flow, bw, defence , ip
      lineName: "",
      lineType: "",
      bwName: "",
      defenseName: "",
      cpuName: "",
      memoryName: "",
      bwArr: [],
      flowArr: [],
      bwMarks: {},
      bwTip: "",
      bwType: "",
      params: {
        // 配置参数
        data_center_id: "",
        image_id: 0,
        line_id: "",
        bw: "",
        flow: "",
        peak_defence: "",
        ip_num: "",
        duration_id: "",
        notes: "",
        model_config_id: "", // 型号id
        auto_renew: false,
        optional_memory: [], // 提交转换{id: num}
        optional_disk: [],
        optional_gpu: [],
        package_id: "",
      },
      plan_way: 0,
      root_name: "root",
      hover: false,
      login_way: lang.auto_create, // 登录方式 auto_create
      rules: {
        name: [
          {
            pattern: /^[A-Za-z][a-zA-Z0-9_.-]{5,24}$/,
            message: lang.mf_tip16,
          },
        ],
      },
      sshList: [],
      dis_visible: false,
      // 配置价格
      loadingPrice: false,
      showConfigPage: false,
      totalPrice: 0.0,
      preview: [],
      sonPreview: [],

      discount: 0,
      calcTotalPrice: 0,
      showImgPick: false,
      isShowImage: false,
      duration: "",
      /* 优惠码 */
      promo: {
        scene: "new",
        promo_code: "",
        billing_cycle_time: "",
        event_promotion: "",
      },
      cartDialog: false,
      isInit: true,
      // 回调相关
      isUpdate: false,
      isConfig: false,
      position: 0,
      backfill: {},
      // 公网ip
      ipData: [],
      ipName: "",
      modelArr: [],
      originModel: [],
      filterModle: [], // 根据区域过滤过的的型号数组
      cpuSelect: [], // 处理器选择
      memorySelect: [], // 内存选择
      curCpu: "",
      curMemory: "",
      isLogin: localStorage.getItem("jwt"),
      lineChange: false,
      showErr: false,
      showImage: false,
      isHide: true,
      isChangeAreaId: false,
      levelNum: 0,
      /* 套餐 */
      packageList: [],
      curPackageDetails: {
        optional_memory: [],
        optional_disk: [],
      },
      cloudIndex: 0,
      memorySwitch: false,
      diskSwitch: false,
      gpuSwitch: false,
      /* 增值选配 */
      filterConfig: {
        optional_memory: [],
        optional_disk: [],
        optional_gpu: [],
      },
      isChangeLine: false,
      model_config_id: "",
      ipv4Tip: "",
      ipv4Arr: [],
    };
  },
  created() {
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
    if (this.isUpdate && temp.config_options) {
      this.backfill = temp.config_options;
      this.isChangeArea = false;
      const {
        country,
        countryName,
        city,
        curImage,
        version,
        curImageId,
        cloudIndex,
        imageName,
        peak_defence,
        defenseName,
        activeName,
        memorySwitch,
        diskSwitch,
        gpuSwitch,
      } = this.backfill;
      this.promo = temp.customfield;
      this.self_defined_field = temp.self_defined_field || {};
      this.qty = temp.qty;
      this.position = temp.position;
      this.activeName = activeName;
      this.country = country;
      this.countryName = countryName;
      this.curImage = curImage;
      this.city = city;
      this.version = version;
      this.curImageId = curImageId;
      this.cloudIndex = cloudIndex;
      this.imageName = imageName;
      this.memorySwitch = memorySwitch;
      this.diskSwitch = diskSwitch;
      this.gpuSwitch = gpuSwitch;
      this.defenseName = defenseName;
    }
    this.getGoodsName();
    this.getCommonData();
    this.getIamgeList();
  },
  watch: {
    "params.image_id"(id) {
      if (id) {
        this.showImage = false;
      }
    },
    "params.data_center_id"(id) {
      this.curCpu = "";
      this.curMemory = "";
      if (this.isUpdate && this.isInit) {
        return;
      }
      this.params.peak_defence = "";
      this.defenseName = "";
      this.params.model_config_id = this.calcModel[0]?.id;
    },
    "params.line_id"(id) {
      // 区域改变，线路必定改变，根据线路改变拉取线路详情
      if (id) {
        this.lineChange = true;
        this.lineType = this.lineList.filter(
          (item) => item.id === id
        )[0]?.bill_type;
        this.getLineDetails(id);
      }
    },
    "params.model_config_id"(id) {
      if (this.isUpdate && this.isInit) {
        return;
      }
      // this.isChangeArea = true
      if (!this.isChangeArea) {
        if (this.lineType === "flow") {
          this.params.flow = this.calcFlowList[0].value;
        } else {
          if (this.bwType === "radio") {
            this.params.bw = this.calcBwRange[0].value * 1 || "";
          } else {
            this.params.bw = this.calcBwRange[0] * 1 || "";
          }
          if (this.params.bw === "NC") {
            // 判断
            const isNc = this.calcBwRange.filter((item) => item.value === "NC");
            if (isNc.length > 0 && isNc[0].value_show) {
              this.bwName = isNc[0].value_show;
            } else {
              this.bwName = lang.actual_bw;
            }
          } else {
            this.bwName = this.params.bw + "M";
          }
        }
      }
      // 固定机型处理增值选配
      if (id) {
        this.filterConfig =
          this.originModel.filter(
            (item) => item.id === id && item.support_optional
          )[0] || {};
      }
      if (!this.lineChange && !this.isInit) {
        this.getCycleList();
      }
    },
    filterConfig(val) {
      if (val && val.id) {
        val.optional_memory = (val.optional_memory || []).map((item) => {
          item.checked = false;
          return item;
        });
        val.optional_disk = (val.optional_disk || []).map((item) => {
          item.checked = false;
          return item;
        });
        val.optional_gpu = (val.optional_gpu || []).map((item) => {
          item.checked = false;
          return item;
        });
      } else {
        return [];
      }
    },
  },
  computed: {
    calcOsImgList() {
      const temp =
        this.imageList.filter((item) => item.id === this.curImageId)[0]
          ?.image || [];
      return temp;
    },
    isShowGpu() {
      return this.calcModel.filter((item) => item.gpu).length > 0;
    },
    calcArea() {
      const c = this.dataList.filter((item) => item.id === this.country * 1)[0]
        ?.name;
      return c + this.city;
    },
    calcAreaList() {
      // 计算区域列表
      const temp =
        this.dataList
          .filter((item) => item.id === this.country * 1)[0]
          ?.city.filter((item) => item.name === this.city)[0]?.area || [];
      if (!this.isChangeArea) {
        return temp;
      }
      if (this.isUpdate && this.isInit) {
      } else {
        this.area_name = temp[0]?.name;
        this.params.data_center_id = temp[0]?.id;
      }
      // 根据区域变化，筛选符合条件的机型
      const limitArr = Array.from(
        new Set(
          this.configLimitList
            .filter((item) => item.data_center_id === temp[0]?.id)
            .reduce((all, cur) => {
              all.push(...cur.model_config_id);
              return all;
            }, [])
        )
      );
      this.filterModle = this.modelArr.filter(
        (item) => !limitArr.includes(String(item.id))
      );
      this.cpuSelect = this.modelArr.reduce((all, cur) => {
        const arr = all.filter((item) => item.value === cur.cpu);
        if (arr.length === 0) {
          all.push({
            value: cur.cpu,
            label: cur.cpu,
          });
        }
        return all;
      }, []);
      this.memorySelect = this.modelArr.reduce((all, cur) => {
        const arr = all.filter((item) => item.value === cur.memory);
        if (arr.length === 0) {
          all.push({
            value: cur.memory,
            label: cur.memory,
          });
        }
        return all;
      }, []);
      // 区域改变的时候，也需要计算线路展示
      return temp;
    },
    calcUsable() {
      return this.dataList
        .filter((item) => item.id === this.country * 1)[0]
        ?.city.filter((item) => item.name === this.city)[0]
        ?.area.filter((item) => item.id === this.params.data_center_id)[0]
        ?.name;
    },
    calcLine() {
      return this.dataList
        .filter((item) => item.id === this.country * 1)[0]
        ?.city.filter((item) => item.name === this.city)[0]
        ?.area.filter((item) => item.id === this.params.data_center_id)[0]
        ?.line.filter((item) => item.id === this.params.line_id)[0]?.name;
    },
    calcCartName() {
      return this.isUpdate && !this.isConfig
        ? lang.product_sure_check
        : lang.product_add_cart;
    },
    calcModel() {
      // 需要处理区域下面值设置了机型的时候，代表该机型可选
      let tempData = this.originModel;
      if (this.configLimitList.length === 0) {
        if (this.isInit && this.isUpdate) {
        } else {
          return this.handleLocalFilter(tempData);
        }
      }
      const temp = JSON.parse(JSON.stringify(this.configLimitList))
        .reduce((all, cur) => {
          if (cur.result.model_config) {
            all.push(cur);
          }
          return all;
        }, [])
        .filter(
          (item) =>
            (!item.rule.data_center ||
              (item.rule.data_center.opt === "eq"
                ? item.rule.data_center.id.includes(this.params.data_center_id)
                : !item.rule.data_center.id.includes(
                    this.params.data_center_id
                  ))) &&
            (!item.rule.bw ||
              (item.rule.bw.opt === "eq"
                ? this.handleRange(item.rule, "bw")
                : !this.handleRange(item.rule, "bw"))) &&
            (!item.rule.flow ||
              (item.rule.flow.opt === "eq"
                ? this.handleRange(item.rule, "flow")
                : !this.handleRange(item.rule, "flow"))) &&
            (!item.rule.image ||
              (item.rule.image.opt === "eq"
                ? item.rule.image.id.includes(this.params.image_id)
                : !item.rule.image.id.includes(this.params.image_id)))
        );
      if (temp.length > 0) {
        // 结果求交集
        const modelArr = temp.reduce((all, cur) => {
          const tempModel = cur.result.model_config.reduce((sum, pre) => {
            sum.push(...pre.id);
            return sum;
          }, []);
          if (cur.result.model_config[0].opt === "eq") {
            all.push(tempModel);
          } else {
            const _temp = this.originModel.reduce((all, cur) => {
              all.push(cur.id);
              return all;
            }, []);
            const result = _temp.filter((item) => !tempModel.includes(item));
            all.push(result);
          }
          return all;
        }, []);
        let modelOpt = this.handleMixed(...modelArr);
        if (modelOpt.length === 0) {
          // 没有交集的时候取全部
          tempData = this.originModel;
        } else {
          tempData = this.originModel.filter((item) => {
            return Array.from(new Set(modelOpt)).includes(item.id);
          });
        }
        // 当前机型id不在范围内
        const modelId = tempData.map((item) => item.id);
        if (!modelId.includes(this.params.model_config_id)) {
          this.params.model_config_id = tempData[0]?.id;
        }
      }
      // 根据区域变化，筛选符合条件的机型
      this.cpuSelect = tempData.reduce((all, cur) => {
        const arr = all.filter((item) => item.value === cur.cpu);
        if (arr.length === 0) {
          all.push({
            value: cur.cpu,
            label: cur.cpu,
          });
        }
        return all;
      }, []);
      this.memorySelect = tempData.reduce((all, cur) => {
        const arr = all.filter((item) => item.value === cur.memory);
        if (arr.length === 0) {
          all.push({
            value: cur.memory,
            label: cur.memory,
          });
        }
        return all;
      }, []);
      return this.handleLocalFilter(tempData);
    },
    calcSpecs() {
      return this.modelArr.filter(
        (item) => item.id === this.params.model_config_id
      )[0]?.name;
    },
    calcBwRange() {
      // 根据区域，线路来判断计算可选带宽  范围
      if (this.lineDetail.bill_type === "flow") {
        return [];
      }
      if (this.lineDetail.bw.length === 0) {
        return [];
      }
      this.params.flow = null;
      const temp = JSON.parse(JSON.stringify(this.configLimitList))
        .reduce((all, cur) => {
          if (cur.result.bw) {
            all.push(cur);
          }
          return all;
        }, [])
        .filter(
          (item) =>
            (!item.rule.data_center ||
              (item.rule.data_center.opt === "eq"
                ? item.rule.data_center.id.includes(this.params.data_center_id)
                : !item.rule.data_center.id.includes(
                    this.params.data_center_id
                  ))) &&
            (!item.rule.model_config ||
              (item.rule.model_config.opt === "eq"
                ? item.rule.model_config.id.includes(
                    this.params.model_config_id
                  )
                : !item.rule.model_config.id.includes(
                    this.params.model_config_id
                  ))) &&
            (!item.rule.bw ||
              (item.rule.bw.opt === "eq"
                ? this.handleRange(item.rule, "bw")
                : !this.handleRange(item.rule, "bw"))) &&
            (!item.rule.flow ||
              (item.rule.flow.opt === "eq"
                ? this.handleRange(item.rule, "flow")
                : !this.handleRange(item.rule, "flow"))) &&
            (!item.rule.image ||
              (item.rule.image.opt === "eq"
                ? item.rule.image.id.includes(this.params.image_id)
                : !item.rule.image.id.includes(this.params.image_id)))
        );
      if (temp.length === 0) {
        // 没有匹配到限制条件
        if (this.lineDetail.bw[0]?.type === "radio") {
          return this.lineDetail.bw;
        } else {
          this.bwTip = this.createTip(this.bwArr);
          this.bwMarks = this.createMarks(this.bwArr);
          return this.bwArr || [];
        }
      }
      let fArr = [];
      const maxBw = this.bwArr[this.bwArr.length - 1];
      const bwArr = temp.reduce((all, cur) => {
        let rangeArr = [],
          min = "",
          max = "";
        // dcim限制bw都是范围
        rangeArr = Array.from(
          new Set(
            cur.result.bw.reduce((sum, pre) => {
              sum.push(pre.min, pre.max);
              return sum;
            }, [])
          )
        ).sort((a, b) => a - b);
        min = rangeArr[0] === "" ? rangeArr[1] : rangeArr[0];
        max =
          rangeArr[rangeArr.length - 1] > maxBw
            ? maxBw
            : rangeArr[rangeArr.length - 1];

        if (cur.result.bw[0].opt === "eq") {
          // 内部求并集
          let _temp = [];
          cur.result.bw.forEach((m) => {
            _temp.push(
              ...this.createArr([m.min * 1, m.max === "" ? maxBw : m.max * 1])
            );
          });
          all.push(_temp);
        } else {
          let result = [];
          cur.result.bw.forEach((m) => {
            result.push(
              ...this.createArr([m.min * 1, m.max === "" ? maxBw : m.max * 1])
            );
          });
          result = this.bwArr.filter((item) => !result.includes(item));
          all.push(result);
        }
        return all;
      }, []);
      // 求交集
      let bwOpt = this.handleMixed(...bwArr);
      if (this.lineDetail.bw[0]?.type === "radio") {
        fArr =
          this.lineDetail.bw.filter((item) => bwOpt.includes(item.value * 1)) ||
          [];
        if (fArr.length === 0) {
          fArr = this.lineDetail.bw;
        }
      } else {
        fArr = Array.from(new Set(bwOpt)).sort((a, b) => a - b);
        if (fArr.length === 0) {
          fArr = this.bwArr;
        }
        fArr = bwOpt.filter((item) => this.bwArr.includes(item));
        this.bwTip = this.createTip(fArr);
        this.bwMarks = this.createMarks(fArr);
      }
      let bwId = [];

      if (this.lineDetail.bw[0]?.type === "radio") {
        bwId = fArr.map((item) => item.value * 1);
      } else {
        bwId = fArr;
      }
      bwId = bwId.map((item) => {
        if (isNaN(item)) {
          item = "NC";
        }
        return item;
      });
      if (bwId.length > 0 && !bwId.includes(this.params.bw)) {
        this.params.bw = bwId[0];
      }
      return fArr;
    },
    calcFlowList() {
      if (this.lineDetail.bill_type === "bw") {
        return [];
      }
      this.params.bw = "";
      if (this.configLimitList.length === 0) {
        return this.lineDetail.flow;
      }
      const temp = JSON.parse(JSON.stringify(this.configLimitList))
        .reduce((all, cur) => {
          if (cur.result.flow) {
            all.push(cur);
          }
          return all;
        }, [])
        .filter(
          (item) =>
            (!item.rule.data_center ||
              (item.rule.data_center.opt === "eq"
                ? item.rule.data_center.id.includes(this.params.data_center_id)
                : !item.rule.data_center.id.includes(
                    this.params.data_center_id
                  ))) &&
            (!item.rule.model_config ||
              (item.rule.model_config.opt === "eq"
                ? item.rule.model_config.id.includes(
                    this.params.model_config_id
                  )
                : !item.rule.model_config.id.includes(
                    this.params.model_config_id
                  ))) &&
            (!item.rule.bw ||
              (item.rule.bw.opt === "eq"
                ? this.handleRange(item.rule, "bw")
                : !this.handleRange(item.rule, "bw"))) &&
            (!item.rule.flow ||
              (item.rule.flow.opt === "eq"
                ? this.handleRange(item.rule, "flow")
                : !this.handleRange(item.rule, "flow"))) &&
            (!item.rule.image ||
              (item.rule.image.opt === "eq"
                ? item.rule.image.id.includes(this.params.image_id)
                : !item.rule.image.id.includes(this.params.image_id)))
        );
      let fArr = [];
      if (temp.length > 0) {
        const maxFlow = this.lineDetail.flow
          .map((item) => item.value * 1)
          .sort((a, b) => a - b);
        // 结果求交集
        const flowArr = temp.reduce((all, cur) => {
          let rangeArr = Array.from(
            new Set(
              cur.result.flow.reduce((sum, pre) => {
                sum.push(pre.min, pre.max);
                return sum;
              }, [])
            )
          ).sort((a, b) => a - b);
          const min = rangeArr[0] === "" ? rangeArr[1] : rangeArr[0];
          const max =
            rangeArr[rangeArr.length - 1] > maxFlow[maxFlow.length - 1]
              ? maxFlow[maxFlow.length - 1]
              : rangeArr[rangeArr.length - 1];
          if (cur.result.flow[0].opt === "eq") {
            let _temp = [];
            cur.result.flow.forEach((m) => {
              _temp.push(
                ...this.createArr([m.min * 1, m.max === "" ? max : m.max * 1])
              );
            });
            all.push(_temp);
          } else {
            let result = [],
              _temp = [];
            cur.result.flow.forEach((m) => {
              _temp.push(
                ...this.createArr([m.min * 1, m.max === "" ? max : m.max * 1])
              );
            });
            result = maxFlow.filter((item) => !_temp.includes(item));
            all.push(result);
          }
          return all;
        }, []);
        const flowOpt = this.handleMixed(...flowArr);
        if (flowOpt.length === 0) {
          fArr = this.lineDetail.flow;
        } else {
          fArr = this.lineDetail.flow.filter((item) =>
            flowOpt.includes(item.value * 1)
          );
        }
      } else {
        fArr = this.lineDetail.flow;
      }
      const flowId = fArr.map((item) => item.value * 1);
      if (!flowId.includes(this.params.flow)) {
        this.params.flow = flowId[0];
      }
      return fArr;
    },
    calcLineList() {
      // 区域，机型改变重置线路
      const temp =
        this.dataList
          .filter((item) => item.id === this.country * 1)[0]
          ?.city.filter((item) => item.name === this.city)[0]?.area || [];

      // 如果限制里面对应的线路，流量和带宽均无说明是全限制，则不显示tab
      const areaLimt = this.configLimitList.filter(
        (item) =>
          item.data_center_id === this.params.data_center_id &&
          item.model_config_id.includes(String(this.params.model_config_id)) &&
          item.min_bw === "" &&
          item.min_flow === ""
      );
      let lineId = []; // 限制里面的线路id
      if (areaLimt.length > 0) {
        lineId = Array.from(
          new Set(
            areaLimt.reduce((all, cur) => {
              all.push(cur.line_id);
              return all;
            }, [])
          )
        );
      }
      if (temp.length > 0) {
        this.lineList = temp
          .filter((item) => item.id === this.params.data_center_id)[0]
          ?.line.filter((item) => !lineId.includes(item.id));
        if (!this.isChangeArea) {
          this.lineName = this.lineList.filter(
            (item) => item.id === this.params.line_id
          )[0]?.name;
        } else {
          this.params.line_id = this.lineList[0]?.id;
          this.lineName = this.lineList[0]?.name;
        }
      }
      return this.lineList;
    },
    calcImageList() {
      let temp = JSON.parse(JSON.stringify(this.imageList));
      if (temp.length === 0) {
        return [];
      }
      if (this.configLimitList.length > 0) {
        let tempLimit = this.configLimitList
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
              (!item.rule.model_config ||
                (item.rule.model_config.opt === "eq"
                  ? item.rule.model_config.id.includes(
                      this.params.model_config_id
                    )
                  : !item.rule.model_config.id.includes(
                      this.params.model_config_id
                    ))) &&
              (!item.rule.bw ||
                (item.rule.bw.opt === "eq"
                  ? this.handleRange(item.rule, "bw")
                  : !this.handleRange(item.rule, "bw"))) &&
              (!item.rule.flow ||
                (item.rule.flow.opt === "eq"
                  ? this.handleRange(item.rule, "flow")
                  : !this.handleRange(item.rule, "flow")))
          );
        const allImageId = this.imageList.reduce((all, cur) => {
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
          // image_id 不在可选配置内
          const imageId = temp.reduce((all, cur) => {
            all.push(...cur.image.map((item) => item.id));
            return all;
          }, []);
          if (
            (!this.params.image_id ||
              !imageId.includes(this.params.image_id)) &&
            temp.length > 0
          ) {
            this.curImageId = temp[0]?.id;
            this.imageName = temp[0]?.name;
            this.curImage = "";
            this.version = temp[0].image[0]?.name;
            this.params.image_id = temp[0].image[0]?.id;
            this.isManual = false;
          }
        }
      }
      this.filterIamge = JSON.parse(JSON.stringify(temp));
      return temp;
    },
    calcIpUnit() {
      return (val) => {
        if (val === "NC") {
          const isNc = this.ipData.filter((item) => item.value === "NC");
          return isNc[0].desc || lang.actual_ip;
        }
        if (this.ipData[0]?.type !== "radio") {
          return `${val}${lang.mf_one}`;
        }
        let temp = "";
        if (val.includes("_")) {
          temp = val.split(",").reduce((all, cur) => {
            all += cur.split("_")[0] * 1;
            return all;
          }, 0);
        } else {
          temp = val;
        }
        return `${temp}${lang.mf_one}`;
      };
    },
    calcBwShow() {
      if (this.params.bw === "NC") {
        const isNc =
          this.calcBwRange.filter((item) => item.value === "NC") || [];
        return isNc[0]?.value_show || lang.actual_bw;
      } else {
        return this.params.bw && this.params.bw !== "NC"
          ? this.params.bw + "M"
          : "--";
      }
    },
    /* 灵活 */
    calcIpNum() {
      return this.packageList.filter(
        (item) => item.id === this.params.package_id
      )[0]?.ip_num;
    },
    calcBwNum() {
      return this.packageList.filter(
        (item) => item.id === this.params.package_id
      )[0]?.bw;
    },
    calcMemoryNum() {
      if (!(this.params.optional_memory instanceof Array)) {
        return;
      }
      // 处理显示还可以选择多少条内存，计算槽位
      const total = this.filterConfig.max_memory_num;
      if (this.params.optional_memory.length === 0) {
        return total;
      }
      const cur = this.params.optional_memory.reduce((all, cur) => {
        all += cur.other_config.memory_slot * cur.num;
        return all;
      }, 0);
      return total - cur > 0 ? total - cur : 0;
    },
    // 计算配置项是否可选： 槽位 和 数量限制
    calcConfigDisabled() {
      return (item, curNum) => {
        if (!(this.params.optional_memory instanceof Array)) {
          return false;
        }
        // 槽位超出
        const total = this.filterConfig.max_memory_num;
        const usedSlot = this.params.optional_memory.reduce((all, cur) => {
          all += cur.other_config.memory_slot * cur.num;
          return all;
        }, 0);
        // 数量超出
        const num = this.filterConfig.leave_memory;
        const usedNum = this.params.optional_memory.reduce((all, cur) => {
          all += cur.other_config.memory * cur.num;
          return all;
        }, 0);

        const temp = this.params.optional_memory.reduce((all, cur) => {
          all.push(cur.id);
          return all;
        }, []);
        // 2种情况 1.已选择过的选项 2.没有选择但是容量或者槽位不满足剩余限制
        if (
          usedSlot + item.other_config.memory_slot * curNum > total ||
          usedNum + item.other_config.memory * curNum > num ||
          temp.includes(item.id)
        ) {
          return true;
        } else {
          return false;
        }
      };
    },
    calcMemMax() {
      // 需要兼顾 槽位 和 数量
      return (data) => {
        if (!(this.params.optional_memory instanceof Array)) {
          return;
        }
        const item = this.filterConfig.optional_memory.filter(
          (el) => el.id === data.id * 1
        )[0];
        item.num = data.num;
        // 槽位
        const total = this.filterConfig.max_memory_num;
        const cur =
          this.params.optional_memory.reduce((all, cur) => {
            all += cur.other_config.memory_slot * cur.num;
            return all;
          }, 0) -
          item.num * item.other_config.memory_slot;
        // 容量
        const num = this.filterConfig.leave_memory;
        const usedNum =
          this.params.optional_memory.reduce((all, cur) => {
            all += cur.other_config.memory * cur.num;
            return all;
          }, 0) -
          item.other_config.memory * item.num;

        const slotNum = Math.floor(
          (total - cur) / item.other_config.memory_slot
        );
        const allNum = Math.floor((num - usedNum) / item.other_config.memory);
        return slotNum > allNum ? allNum : slotNum;
      };
    },
    // 是否显示新增内存
    showAddMemory() {
      if (!(this.params.optional_memory instanceof Array)) {
        return;
      }
      const total = this.filterConfig.max_memory_num;
      const num = this.filterConfig.leave_memory;
      // 已使用容量
      const usedNum = this.params.optional_memory.reduce((all, cur) => {
        all += cur.other_config.memory * cur.num;
        return all;
      }, 0);
      // 已使用槽位
      const usedSlot = this.params.optional_memory.reduce((all, cur) => {
        all += cur.other_config.memory_slot * cur.num;
        return all;
      }, 0);
      const checkedId = this.params.optional_memory.reduce((all, cur) => {
        all.push(cur.id);
        return all;
      }, []);
      // 筛选出已选择的，并且去掉超出剩余限制的选项
      const temp =
        this.filterConfig.optional_memory.filter(
          (item) =>
            !checkedId.includes(item.id) &&
            usedNum + item.other_config.memory <= num &&
            usedSlot + item.other_config.memory_slot <= total
        ) || [];
      if (temp.length > 0) {
        if (
          temp[0].other_config.memory > num ||
          temp[0].other_config.memory.memory_slot > total
        ) {
          return false;
        } else {
          return true;
        }
      } else {
        return false;
      }
    },
    calcMemoryOption() {
      return this.filterConfig.optional_memory.map((item) => {
        item.disabled = this.calcConfigDisabled(item, item.num);
        return item;
      });
    },
    calcDiskOption() {
      return this.filterConfig.optional_disk.map((item) => {
        item.disabled = this.calcDiskDisabled(item);
        return item;
      });
    },
    calcGpuOption() {
      return this.filterConfig.optional_gpu.map((item) => {
        item.disabled = this.calcGpuDisabled(item);
        return item;
      });
    },
    // 内存可用容量
    calcMemoryCapacity() {
      if (!(this.params.optional_memory instanceof Array)) {
        return 0;
      }
      const all = this.filterConfig.leave_memory;
      const used = this.params.optional_memory.reduce((all, cur) => {
        all += cur.num * cur.other_config.memory;
        return all;
      }, 0);
      return all - used;
    },
    /* 计算硬盘 */
    calcDiskNum() {
      if (!(this.params.optional_disk instanceof Array)) {
        return;
      }
      const total = this.filterConfig.max_disk_num;
      if (this.params.optional_disk.length === 0) {
        return total;
      }
      const cur = this.params.optional_disk.reduce((all, cur) => {
        all += cur.num;
        return all;
      }, 0);
      return total - cur > 0 ? total - cur : 0;
    },
    calcDiskDisabled() {
      // 只有该选项未被选择时才能被选择
      return (item) => {
        if (!(this.params.optional_disk instanceof Array)) {
          return false;
        }
        const temp = this.params.optional_disk.reduce((all, cur) => {
          all.push(cur.id);
          return all;
        }, []);
        return temp.includes(item.id);
      };
    },
    calcDiskMax() {
      return (item) => {
        if (!(this.params.optional_disk instanceof Array)) {
          return;
        }
        if (this.filterConfig.max_disk_num === 0) {
          return Infinity;
        }
        const num = this.filterConfig.max_disk_num;
        const usedNum =
          this.params.optional_disk.reduce((all, cur) => {
            all += cur.num;
            return all;
          }, 0) - item.num;
        return num - usedNum;
      };
    },
    showAddDisk() {
      if (!(this.params.optional_disk instanceof Array)) {
        return false;
      }
      // 筛选出已选择的，并且去掉超出剩余限制的选项
      const checkedId = this.params.optional_disk.reduce((all, cur) => {
        all.push(cur.id);
        return all;
      }, []);
      const temp =
        this.filterConfig.optional_disk.filter(
          (item) => !checkedId.includes(item.id)
        ) || [];
      const num = this.filterConfig.max_disk_num;
      const usedNum = this.params.optional_disk.reduce((all, cur) => {
        all += cur.num;
        return all;
      }, 0);
      if (temp.length > 0 && num - usedNum > 0) {
        return true;
      }
    },
    /* 计算Gpu */
    calcGpuNum() {
      if (!(this.params.optional_gpu instanceof Array)) {
        return;
      }
      const total = this.filterConfig.max_gpu_num;
      if (this.params.optional_gpu.length === 0) {
        return total;
      }
      const cur = this.params.optional_gpu.reduce((all, cur) => {
        all += cur.num;
        return all;
      }, 0);
      return total - cur > 0 ? total - cur : 0;
    },
    calcGpuDisabled() {
      return (item) => {
        if (!(this.params.optional_gpu instanceof Array)) {
          return;
        }
        const temp = this.params.optional_gpu.reduce((all, cur) => {
          all.push(cur.id);
          return all;
        }, []);
        return temp.includes(item.id);
      };
    },
    calcGpuMax() {
      return (item) => {
        if (!(this.params.optional_gpu instanceof Array)) {
          return;
        }
        if (this.filterConfig.max_gpu_num === 0) {
          return Infinity;
        }
        const num = this.filterConfig.max_gpu_num;
        const usedNum =
          this.params.optional_gpu.reduce((all, cur) => {
            all += cur.num;
            return all;
          }, 0) - item.num;
        return num - usedNum;
      };
    },
    showAddGpu() {
      if (!(this.params.optional_gpu instanceof Array)) {
        return;
      }
      // 筛选出已选择的，并且去掉超出剩余限制的选项
      const checkedId = this.params.optional_gpu.reduce((all, cur) => {
        all.push(cur.id);
        return all;
      }, []);
      const temp =
        this.filterConfig.optional_gpu.filter(
          (item) => !checkedId.includes(item.id)
        ) || [];
      const num = this.filterConfig.max_gpu_num;
      const usedNum = this.params.optional_gpu.reduce((all, cur) => {
        all += cur.num;
        return all;
      }, 0);
      if (temp.length > 0 && num - usedNum > 0) {
        return true;
      }
    },
  },

  methods: {
    // 返回产品列表页
    goBack() {
      window.history.back();
    },
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
      // 处理范围内的是否包含当前参数: bw, flow
      let target = "";
      target = this.params[type];
      let rangeMax = this[`${type}Arr`][this[`${type}Arr`].length - 1];
      return this.createArr([
        item[type].min * 1,
        item[type].max === ""
          ? rangeMax
          : item[type].max * 1 >= rangeMax
          ? rangeMax
          : item[type].max * 1,
      ]).includes(target);
    },
    // 本地筛选机型
    handleLocalFilter(data) {
      const temp = data.reduce((all, cur) => {
        if (!this.curCpu && !this.curMemory) {
          all.push(cur);
        } else if (this.curCpu && !this.curMemory) {
          if (cur.cpu === this.curCpu) {
            all.push(cur);
          }
        } else if (!this.curCpu && this.curMemory) {
          if (cur.memory === this.curMemory) {
            all.push(cur);
          }
        } else {
          if (cur.cpu === this.curCpu && cur.memory === this.curMemory) {
            all.push(cur);
          }
        }
        return all;
      }, []);
      return temp;
    },
    /* 增值选配 */
    changeModelId(e) {
      this.filterConfig = this.originModel.filter(
        (item) => item.id === e && item.support_optional
      )[0];
      if (this.lineDetail.bill_type === "bw") {
        if (this.bwType === "radio") {
          const curValue = this.calcBwRange[0]?.value;
          this.params.bw = curValue === "NC" ? "NC" : curValue * 1 || "";
        } else {
          this.params.bw = this.calcBwRange[0] * 1 || "";
        }
        this.params.flow = "";
      } else {
        this.params.bw = "";
        this.params.flow = this.calcFlowList[0]?.value * 1;
      }
      // 重置开关配置
      this.memorySwitch = false;
      this.diskSwitch = false;
      this.gpuSwitch = false;
      this.params.optional_memory = [];
      this.params.optional_disk = [];
      this.params.optional_gpu = [];
    },
    /* 灵活配置 */
    handleClick() {
      // 重置通用数据
      this.getConfig();
    },
    checkOption(type) {
      // 筛选未被选中的项
      const checkedId = this.params[type].reduce((all, cur) => {
        all.push(cur.id);
        return all;
      }, []);
      const temp = this.filterConfig[type].filter(
        (item) => !checkedId.includes(item.id)
      );
      this.params[type].push({
        ...temp[0],
        num: 1,
      });
      this.changeConfig();
    },
    delConfig(type, index) {
      this.params[type].splice(index, 1);
      this.changeConfig();
    },
    checkOption1(type, item, bol) {
      if (bol) {
        // 不可选
        return;
      }
      item.checked = !item.checked;
      if (item.checked) {
        this.params[type].push({
          ...item,
          num: 1,
        });
      } else {
        const index = this.params[type].findIndex((el) => el.id === item.id);
        this.params[type].splice(index, 1);
      }
      this.changeConfig();
    },
    changeDiskSwitch(e, type) {
      if (!e) {
        this.params[type] = [];
        this.filterConfig[type].forEach((item) => (item.checked = false));
        this.changeConfig();
      }
    },
    /* 灵活配置 end */
    getQuery(name) {
      const reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
      const r = window.location.search.substr(1).match(reg);
      if (r != null) return decodeURI(r[2]);
      return null;
    },
    // 配置数据
    async getConfig() {
      try {
        const params = {
          id: this.id,
        };
        if (this.activeName === "flex") {
          params.scene = "package";
        }
        const res = await getOrderConfig(params);
        const temp = res.data.data;
        // 通用数据处理
        this.dataList = temp.data_center;
        this.originModel = this.modelArr = temp.model_config;
        this.configLimitList = temp.limit_rule;
        this.packageList = temp.package;
        // 如果没有推荐配置，跳转到自定义，重新获取数据
        if (this.originModel.length === 0) {
          return;
        }
        // 初始化数据
        if (!this.isUpdate) {
          // 不是回填
          this.params = {
            data_center_id: "",
            image_id: this.imageList[0]?.image[0]?.id,
            line_id: "",
            bw: "",
            flow: "",
            peak_defence: "",
            ip_num: "",
            duration_id: "",
            notes: "",
            model_config_id: "", // 型号id
            auto_renew: false,
            optional_memory: [], // 提交转换{id: num}
            optional_disk: [],
            optional_gpu: [],
            package_id: "",
          };
          this.qty = 1;
          this.country = String(this.dataList[0]?.id);
          this.countryName = String(this.dataList[0]?.name);
          this.city = String(this.dataList[0]?.city[0]?.name);
          this.cloudIndex = 0;
          if (this.activeName === "flex") {
            this.params.package_id = this.packageList[0]?.id;
          }
        } else {
          // 回填数据
          this.params = this.backfill;
          // 根据区域变化，筛选符合条件的机型
          const filArea =
            this.dataList
              .filter((item) => item.id === this.country * 1)[0]
              ?.city.filter((item) => item.name === this.city)[0]?.area || [];
          const limitArr = Array.from(
            new Set(
              this.configLimitList
                .filter((item) => item.data_center_id === filArea[0]?.id)
                .reduce((all, cur) => {
                  all.push(...cur.model_config_id);
                  return all;
                }, [])
            )
          );
          this.filterModle = this.modelArr.filter(
            (item) => !limitArr.includes(String(item.id))
          );
          this.cpuSelect = this.filterModle.reduce((all, cur) => {
            const arr = all.filter((item) => item.value === cur.cpu);
            if (arr.length === 0) {
              all.push({
                value: cur.cpu,
                label: cur.cpu,
              });
            }
            return all;
          }, []);
          this.memorySelect = this.filterModle.reduce((all, cur) => {
            const arr = all.filter((item) => item.value === cur.memory);
            if (arr.length === 0) {
              all.push({
                value: cur.memory,
                label: cur.memory,
              });
            }
            return all;
          }, []);
        }
        this.totalPrice = 0.0;
        this.filterConfig =
          this.originModel.filter(
            (item) =>
              item.id === this.params.model_config_id && item.support_optional
          )[0] || {};
        this.isInit = true;
        this.handlerCustom();
      } catch (error) {
        console.log("@@@", error);
      }
    },
    // 切换自定义配置
    handlerCustom() {
      if (this.isUpdate) {
        // 回填
        this.area_name = this.calcAreaList.filter(
          (item) => item.id === this.params.data_center_id
        )[0]?.name;
      }
    },
    /* 线路 */
    changeLine(val) {
      this.lineName = val[0].name;
      this.isChangeLine = true;
    },
    async getLineDetails(id) {
      try {
        if (!id) {
          return;
        }
        // 获取线路详情
        const res = await getLineDetail({id: this.id, line_id: id});
        let tempData = res.data.data;
        if (tempData.flow) {
          tempData.flow = tempData.flow.map((item) => {
            item.value = item.value * 1;
            return item;
          });
        }
        this.lineDetail = tempData;
        // 公网IP
        if (this.lineDetail.ip) {
          this.ipData = this.lineDetail.ip;
          if (this.isUpdate && this.isInit) {
          } else {
            this.params.ip_num = this.ipData[0]?.value || 0;
            if (this.ipData[0]) {
              this.params.ip_num =
                this.ipData[0]?.type === "radio"
                  ? this.ipData[0]?.value
                  : this.ipData[0]?.min_value;
            }
          }
          // 循环生成可选数组
          if (this.lineDetail.ip[0]?.type !== "radio") {
            const fArr = [];
            this.lineDetail.ip.forEach((item) => {
              fArr.push(...this.createArr([item.min_value, item.max_value]));
            });
            this.ipv4Arr = fArr;
            this.ipv4Tip = this.createTip(fArr);
          }
        } else {
          this.params.ip_num = "";
        }
        this.ipName = this.calcIpUnit(this.params.ip_num);
        if (this.lineDetail.bw) {
          this.bwType = this.lineDetail.bw[0]?.type;
          if (this.params.bw === "NC") {
            // 判断
            const isNc = this.calcBwRange.filter((item) => item.value === "NC");
            if (isNc.length > 0 && isNc[0].value_show) {
              this.bwName = isNc[0].value_show;
            }
          }
          // 循环生成带宽可选数组
          let fArr = [];
          this.lineDetail.bw.forEach((item) => {
            if (item.type === "radio") {
              if (item.value !== "NC") {
                fArr.push(item.value * 1);
              }
              fArr = Array.from(new Set(fArr));
            } else {
              fArr.push(...this.createArr([item.min_value, item.max_value]));
            }
          });
          this.bwArr = fArr;
          this.bwTip = this.createTip(fArr);
          if (this.isInit && this.isUpdate) {
            // 初次回填
          } else {
            this.params.bw = "";
            if (this.bwType === "radio") {
              this.params.bw = this.calcBwRange[0]?.value || "";
            } else {
              this.params.bw = this.calcBwRange[0] * 1 || "";
            }
            // this.params.bw = this.calcBwRange[0]?.value || this.calcBwRange[0] * 1 || 1
          }
        }
        if (this.lineDetail.flow) {
          this.flowArr = this.lineDetail.flow.map((item) => item.value * 1);
          if (this.isInit && this.isUpdate) {
            // 初次回填
          } else {
            this.params.flow = this.calcFlowList[0]?.value || "";
          }
        }
        this.bwMarks = this.createMarks(this.bwArr);
        if (this.lineDetail.defence) {
          if (this.isInit && this.isUpdate) {
            // 初次回填
          } else {
            this.params.peak_defence = this.lineDetail.defence.find(
              (item) => item.value == this.lineDetail.order_default_defence
            )?.value;
          }
          this.defenseName = this.lineDetail.defence.find(
            (item) => item.value == this.params.peak_defence
          )?.desc;
        } else {
          this.defenseName = "";
          this.params.peak_defence = "";
        }
        // 增值选配

        if (this.isUpdate && this.isInit) {
          const typeArr = ["optional_memory", "optional_disk", "optional_gpu"];
          typeArr.forEach((type) => {
            this.filterConfig[type] = this.filterConfig[type].map((item) => {
              if (
                Object.keys(this.backfill[type] || {}).includes(String(item.id))
              ) {
                item.checked = true;
              } else {
                item.checked = false;
              }
              return item;
            });
          });

          const tempMemory = Array.from(
            Object.keys(this.backfill.optional_memory || {})
          ).reduce((all, cur) => {
            const _temp = this.filterConfig.optional_memory.filter(
              (item) => item.id == cur
            )[0];
            all.push({
              id: cur * 1,
              num: this.backfill.optional_memory[cur] * 1,
              value: _temp.value,
              other_config: _temp.other_config,
              checked: true,
            });
            return all;
          }, []);
          this.params.optional_memory = tempMemory;
          const tempDisk = Array.from(
            Object.keys(this.backfill.optional_disk || {})
          ).reduce((all, cur) => {
            all.push({
              id: cur * 1,
              num: this.backfill.optional_disk[cur] * 1,
              value: this.filterConfig.optional_disk.filter(
                (item) => item.id == cur
              )[0]?.value,
            });
            return all;
          }, []);
          this.params.optional_disk = tempDisk;
          const gpuTemp = Array.from(
            Object.keys(this.backfill.optional_gpu || {})
          ).reduce((all, cur) => {
            all.push({
              id: cur * 1,
              num: this.backfill.optional_gpu[cur] * 1,
              value: this.filterConfig.optional_gpu.filter(
                (item) => item.id == cur
              )[0]?.value,
            });
            return all;
          }, []);
          this.params.optional_gpu = gpuTemp;
        } else {
          if (!this.isChangeLine) {
            this.memorySwitch = false;
            this.diskSwitch = false;
            this.gpuSwitch = false;
            this.params.optional_memory = [];
            this.params.optional_disk = [];
            this.params.optional_gpu = [];
          }
          this.isChangeLine = false;
        }
        this.getCycleList();
      } catch (error) {
        console.log("####", error);
      }
    },
    changeMemory(val, index) {
      const id = val[0].id;
      const temp = JSON.parse(
        JSON.stringify(this.filterConfig.optional_memory)
      ).filter((item) => item.id === id)[0];
      temp.num = 1;
      this.params.optional_memory.splice(index, 1, temp);
      this.changeConfig();
    },
    changeBw(val) {
      const e = val.value === "NC" ? "NC" : val.value * 1;
      // 判读e是否为带NC
      const isNc = this.calcBwRange.filter((item) => item.value_show === e);
      if (isNc.length > 0 || e === lang.actual_bw) {
        this.params.bw = "NC";
      }
      // 计算价格
      this.getCycleList();
    },
    changeBwNum(num) {
      if (!this.calcBwRange.includes(num)) {
        this.calcBwRange.forEach((item, index) => {
          if (num > item && num < this.calcBwRange[index + 1]) {
            this.params.bw =
              num - item > this.calcBwRange[index + 1] - num
                ? this.calcBwRange[index + 1]
                : item;
          }
        });
      }
      this.getCycleList();
    },
    // 选中/取消防御
    chooseDefence(e) {
      this.defenseName = e[0].desc;
      this.params.peak_defence = e[0].value;
      this.getCycleList();
    },
    // 切换流量
    changeFlow(val) {
      // if (e === lang.mf_tip28) {
      //   this.params.flow = 0;
      // } else {
      //   this.params.flow = e.replace("G", "") * 1;
      // }
      this.params.flow = val[0].value;
      this.getCycleList();
    },
    // 切换IP
    changeIp(val) {
      const e = this.calcIpUnit(val[0].value);
      // 判读e是否为带NC
      const isNc = this.ipData.filter(
        (item) => item.desc === e && item.value === "NC"
      );
      if (isNc.length > 0 || e === lang.actual_ip) {
        this.params.ip_num = "NC";
      } else {
        let temp = e.replace(lang.mf_one, "") * 1;
        this.params.ip_num = this.ipData.filter((item) => {
          if (item.value !== "NC") {
            return (
              (item.value,
              item.value.split(",").reduce((all, cur) => {
                all += cur.split("_")[0] * 1;
                return all;
              }, 0)) === temp
            );
          }
        })[0]?.value;
      }
      this.getCycleList();
    },
    changeIpNum(type, num) {
      // const val = type === "ipv4" ? "ip_num" : "ipv6_num";
      if (!this[`${type}Arr`].includes(num)) {
        this[`${type}Arr`].forEach((item, index) => {
          if (num > item && num < this[`${type}Arr`][index + 1]) {
            this.params.ip_num =
              num - item > this[`${type}Arr`][index + 1] - num
                ? this[`${type}Arr`][index + 1]
                : item;
          }
        });
      }
      this.getCycleList();
    },
    createArr([m, n]) {
      // 生成数组
      let temp = [];
      for (let i = m; i <= n; i++) {
        temp.push(i * 1);
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
      obj[25] = `${data[0] + Math.ceil(range * 0.25)}`;
      obj[50] = `${data[0] + Math.ceil(range * 0.5)}`;
      obj[75] = `${data[0] + Math.ceil(range * 0.75)}`;
      obj[100] = `${data[data.length - 1]}`;
      return obj;
    },
    confirmModel() {
      if (this.model_config_id !== this.params.model_config_id) {
        this.params.model_config_id = this.model_config_id;
        this.changeModelId(this.params.model_config_id);
      }
      this.modelDialogShow = false;
    },
    handleModel() {
      this.model_config_id = this.params.model_config_id;
      this.modelDialogShow = true;
    },
    // 选择区域
    changeArea(val) {
      const e = val[0].name;
      this.isChangeArea = false;
      // 手动切换区域不初始化第一个区域
      this.params.data_center_id = this.calcAreaList.filter(
        (item) => item.name === e
      )[0]?.id;
      this.area_name = this.calcAreaList.filter(
        (item) => item.name === e
      )[0]?.name;
      this.lineList = this.calcAreaList.filter(
        (item) => item.name === e
      )[0]?.line;
      this.params.line_id = this.lineList[0].id;
      this.lineName = this.lineList[0].name;
    },
    // 选择先线路
    chooseLine(item) {
      this.params.data_center_id = item.data_center_id;
      this.params.line_id = item.id;
    },
    // 切换城市
    changeCity(e) {
      this.country = e[0].id;
      this.city = e[1].name;
      this.changeCountry();
      this.isChangeArea = true;
      this.cloudIndex = 0;
    },
    tableRowClassName({row, rowIndex}) {
      row.index = rowIndex;
    },
    // 提交前格式化数据
    formatData() {
      // if (!this.params.image_id) {
      //   document.getElementById('image').scrollIntoView({ behavior: "smooth" })
      //   this.showImage = true
      //   return
      // }
      // ssh
      if (this.login_way === lang.security_tab1 && !this.params.ssh_key_id) {
        return showToast(`${lang.placeholder_pre2}${lang.security_tab1}`);
      }
      return true;
    },
    // 立即购买
    async submitOrder(iframeEvent) {
      if (iframeEvent.data && iframeEvent.data.type !== "iframeBuy") {
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
      this.$refs.orderForm.validate().then(async () => {
        const bol = this.formatData();
        if (bol !== true) {
          return;
        }
        const flag = await this.$refs.customGoodRef.getSelfDefinedField();
        if (!flag) return;
        try {
          const params = {
            product_id: this.id,
            config_options: {
              ...this.params,
            },
            qty: this.qty,
            customfield: this.promo,
            self_defined_field: this.self_defined_field,
          };
          if (this.lineDetail.bill_type === "bw") {
            delete params.flow;
          } else {
            delete params.bw;
          }
          if (params.config_options.bw === lang.actual_bw) {
            params.config_options.bw = "NC";
          }
          // 处理灵活配置参数
          if (this.filterConfig.id) {
            params.config_options.optional_memory = this.formatConfig(
              params.config_options.optional_memory
            );
            params.config_options.optional_disk = this.formatConfig(
              params.config_options.optional_disk
            );
            params.config_options.optional_gpu = this.formatConfig(
              params.config_options.optional_gpu
            );
          }
          if (iframeEvent.data && iframeEvent.data.type === "iframeBuy") {
            const postObj = {
              type: "iframeBuy",
              params,
              price: this.calcTotalPrice,
            };
            window.parent.postMessage(postObj, "*");
            return;
          }
          // 直接传配置到结算页面
          sessionStorage.setItem("product_information", JSON.stringify(params));
          location.href = `/cart/settlement.htm?id=${params.product_id}`;
        } catch (error) {
          showToast(error.data.msg);
        }
      });
    },
    handlerCart() {
      if (this.isUpdate && !this.isConfig) {
        this.changeCart();
      } else {
        this.addCart();
      }
    },
    // 加入购物车
    addCart() {
      this.$refs.orderForm.validate().then(async () => {
        const bol = this.formatData();
        if (bol !== true) {
          return;
        }
        const flag = await this.$refs.customGoodRef.getSelfDefinedField();
        if (!flag) return;
        try {
          const params = {
            product_id: this.id,
            config_options: {
              ...this.params,
              // 其他需要回显的页面数据
              activeName: this.activeName,
              country: this.country,
              countryName: this.countryName,
              city: this.city,
              curImage: this.curImage,
              defenseName: this.defenseName,
              curImageId: this.curImageId,
              imageName: this.imageName,
              version: this.version,
              cloudIndex: this.cloudIndex,
              memorySwitch: this.memorySwitch,
              diskSwitch: this.diskSwitch,
              gpuSwitch: this.gpuSwitch,
            },
            qty: this.qty,
            customfield: this.promo,
            self_defined_field: this.self_defined_field,
          };
          if (params.config_options.bw === lang.actual_bw) {
            params.config_options.bw = "NC";
          }
          if (this.lineDetail.bill_type === "bw") {
            delete params.flow;
          } else {
            delete params.bw;
          }
          if (this.activeName === "fixed") {
            delete params.package_id;
          } else {
            delete params.model_config_id;
          }
          // 处理灵活配置参数
          if (this.filterConfig.id) {
            params.config_options.optional_memory = this.formatConfig(
              params.config_options.optional_memory
            );
            params.config_options.optional_disk = this.formatConfig(
              params.config_options.optional_disk
            );
            params.config_options.optional_gpu = this.formatConfig(
              params.config_options.optional_gpu
            );
          }
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
          showToast(error.data.msg);
        }
      });
    },
    // 修改购物车
    changeCart() {
      this.$refs.orderForm.validate().then(async () => {
        const bol = this.formatData();
        if (bol !== true) {
          return;
        }
        const flag = await this.$refs.customGoodRef.getSelfDefinedField();
        if (!flag) return;
        try {
          const params = {
            position: this.position,
            product_id: this.id,
            config_options: {
              ...this.params,
              // 其他需要回显的页面数据
              activeName: this.activeName,
              country: this.country,
              countryName: this.countryName,
              city: this.city,
              curImage: this.curImage,
              defenseName: this.defenseName,
              curImageId: this.curImageId,
              imageName: this.imageName,
              version: this.version,
              cloudIndex: this.cloudIndex,
              login_way: this.login_way,
              memorySwitch: this.memorySwitch,
              diskSwitch: this.diskSwitch,
              gpuSwitch: this.gpuSwitch,
            },
            qty: this.qty,
            customfield: this.promo,
            self_defined_field: this.self_defined_field,
          };
          if (params.config_options.bw === lang.actual_bw) {
            params.config_options.bw = "NC";
          }
          if (this.lineDetail.bill_type === "bw") {
            delete params.flow;
          } else {
            delete params.bw;
          }
          if (this.activeName === "fixed") {
            delete params.package_id;
          } else {
            delete params.model_config_id;
          }
          // 处理灵活配置参数
          if (this.filterConfig.id) {
            params.config_options.optional_memory = this.formatConfig(
              params.config_options.optional_memory
            );
            params.config_options.optional_disk = this.formatConfig(
              params.config_options.optional_disk
            );
            params.config_options.optional_gpu = this.formatConfig(
              params.config_options.optional_gpu
            );
          }
          this.dataLoading = true;
          const res = await updateCart(params);
          showToast(res.data.msg);
          location.href = "/cart/shoppingCar.htm";
          this.dataLoading = false;
        } catch (error) {
          showToast(error.data.msg);
        }
      });
    },
    goToCart() {
      location.href = "/cart/shoppingCar.htm";
      this.cartDialog = false;
    },
    changeCountry() {
      // 切换国家的时候，先重置机型id
      // this.params.model_config_id = this.modelArr[0].id;
      this.countryName = this.dataList.filter(
        (item) => item.id === this.country * 1
      )[0]?.name;
      this.isChangeArea = true;
      this.cloudIndex = 0;
    },
    changQty() {
      this.changeConfig();
    },
    eventChange(evetObj) {
      if (this.eventData.id !== evetObj.id) {
        this.eventData.id = evetObj.id || "";
        this.promo.event_promotion = this.eventData.id;
        if (this.params.data_center_id) {
          this.changeConfig();
        }
      }
    },
    // 使用优惠码
    getDiscount(data) {
      this.promo.promo_code = data[1];
      this.changeConfig();
    },
    removeDiscountCode() {
      this.promo.promo_code = "";
      this.discount = 0;
      this.changeConfig();
    },
    getSelectValue(refName) {
      return this.$refs[refName].getSelectedOptions();
    },
    handelSelectImg() {
      const e = this.getSelectValue("selectPopRef");
      this.params.image_id = e[0].id;
      this.imageName = e[0].name;
      this.curImage = this.calcImageList.findIndex(
        (item) => item.id === e[0].image_group_id
      );
      this.curImageId = e[0].image_group_id;
      this.showImgPick = false;
      this.isShowImage = false;
      this.getCycleList();
    },
    // 获取镜像
    async getIamgeList() {
      try {
        const res = await getSystemList({id: this.id});
        const temp = res.data.data.list;
        this.imageList = temp;
        if (!this.isUpdate) {
          this.imageName = this.version = temp[0]?.image[0]?.name;
          this.curImage = 0;
          this.curImageId = temp[0]?.id;
          this.params.image_id = temp[0]?.image[0]?.id;
        }
      } catch (error) {}
    },
    changeDuration() {
      this.promo.promo_code = "";
      this.discount = 0;
      this.changeConfig();
    },
    // 获取周期
    async getCycleList() {
      try {
        // 防抖
        if (window.getNowIngTimer) {
          clearTimeout(window.getNowIngTimer);
          window.getNowIngTimer = null;
        }
        window.getNowIngTimer = setTimeout(async () => {
          this.lineChange = false;
          const params = JSON.parse(JSON.stringify(this.params));
          params.id = this.id;
          if (this.activeName === "fixed") {
            delete params.package_id;
          } else {
            delete params.model_config_id;
          }
          if (this.filterConfig.id) {
            params.optional_memory = this.formatConfig(params.optional_memory);
            params.optional_disk = this.formatConfig(params.optional_disk);
            params.optional_gpu = this.formatConfig(params.optional_gpu);
          }
          const hasDuration = params.duration_id;
          if (params.bw === lang.actual_bw) {
            params.bw = "NC";
          }
          if (hasDuration) {
            this.changeConfig();
          }
          const res = await getDuration(params);
          this.cycleList = res.data.data;
          this.params.duration_id =
            this.params.duration_id || this.cycleList[0]?.id;
          if (!hasDuration) {
            this.changeConfig();
          }
        }, 200);
      } catch (error) {
        console.log("error", error);
      }
    },
    formatConfig(data) {
      if (data.length === 0) {
        return;
      }
      return data.reduce((all, cur) => {
        all[Number(cur.id)] = cur.num;
        return all;
      }, {});
    },
    // 更改配置计算价格
    async changeConfig() {
      try {
        this.loadingPrice = true;
        const params = {
          id: this.id,
          config_options: {
            ...this.params,
            promo_code: this.promo.promo_code,
            event_promotion: this.promo.event_promotion,
          },
          qty: this.qty,
        };
        if (params.config_options.bw === lang.actual_bw) {
          params.config_options.bw = "NC";
        }
        if (this.activeName === "fixed") {
          delete params.config_options.package_id;
        } else {
          delete params.config_options.model_config_id;
        }
        // 处理灵活配置参数
        if (this.filterConfig.id) {
          params.config_options.optional_memory = this.formatConfig(
            params.config_options.optional_memory
          );
          params.config_options.optional_disk = this.formatConfig(
            params.config_options.optional_disk
          );
          params.config_options.optional_gpu = this.formatConfig(
            params.config_options.optional_gpu
          );
        }
        let timer = null;
        if (!params.config_options.image_id && timer === null) {
          setTimeout(() => {
            this.params.image_id = this.calcImageList[0]?.image[0]?.id;
            this.changeConfig();
          }, 300);
          return;
        } else {
          clearTimeout(timer);
          timer = null;
        }
        const res = await calcPrice(params);
        this.totalPrice = res.data.data.price * 1;
        this.calcTotalPrice = res.data.data.price_total * 1;
        this.eventData.discount =
          res.data.data.price_event_promotion_discount * 1 || 0;
        this.discount = res.data.data.price_promo_code_discount * 1 || 0;
        this.levelNum = res.data.data.price_client_level_discount * 1 || 0;
        this.preview = res.data.data.preview;
        if (res.data.data.sub_host.length > 0) {
          this.sonPreview = res.data.data.sub_host.map((item) => item.preview);
        } else {
          this.sonPreview = [];
        }
        this.duration = res.data.data.duration;
        this.isInit = false;
        this.loadingPrice = false;
      } catch (error) {
        console.log("###error", error);
        this.loadingPrice = false;
        showToast(error.data.msg);
      }
    },
    getGoodsName() {
      productInfo(this.id).then((res) => {
        this.tit = res.data.data.product.name;
        document.title =
          this.commonData.website_name + "-" + res.data.data.product.name;
      });
    },
    // 获取通用配置
    getCommonData() {
      this.commonData = JSON.parse(localStorage.getItem("common_set_before"));
    },
    mouseenter(index) {
      // if (index === this.curImage) {
      //   this.hover = true
      // }
      this.curImage = index;
      this.hover = true;
    },
    changeImage(item) {
      this.curImageId = item.id;
      this.showImgPick = true;
      this.hover = true;
    },
    getSelectValue(refName) {
      return this.$refs[refName].getSelectedOptions();
    },
    chooseVersion() {
      const e = this.getSelectValue("selectPopRef");
      this.imageName = e[0].name;
      this.curImage = this.calcImageList.findIndex(
        (item) => item.id === e[0].image_group_id
      );
      this.curImageId = e[0].image_group_id;
      this.version = e[0].name;
      this.params.image_id = e[0].id;
      this.getCycleList();
      this.showImgPick = false;
      this.isShowImage = false;
    },
  },
});
window.directiveInfo.forEach((item) => {
  app2.directive(item.name, item.fn);
});
app2.use(vant).mount("#template2");
