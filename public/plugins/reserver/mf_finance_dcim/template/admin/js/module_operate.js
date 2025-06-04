const template = document.getElementsByClassName("operate-div")[0];
Vue.prototype.lang = Object.assign(window.lang, window.module_lang);
const adminOperateVue = new Vue({
  components: {
    comConfig,
    safeConfirm,
  },
  data () {
    return {
      host: location.origin,
      id: "",
      client_id: "",
      osList: [],
      remoteInfo: {
        rescue: 0,
      },
      status: "",
      admin_operate_password: "",
      opName: "",
      btnLoading: false,
      moduleVisible: false,
      moduleLoading: false,
      submitLoading: false,
      optTilte: "",
      rescueModel: false,
      resetPasswordModel: false,
      // 重装配置
      reinstallModel: false,
      selectOsId: "",
      reinstallType: "passworld",
      reinstallData: {
        image_id: "",
        password: "",
        ssh_key_id: "",
        port: "",
        part_type: 0,
      },
      rescueData: {
        type: 1,
        password: "",
      },
      resetPasswordData: {
        password: "",
        checked: false,
      },
      sshList: [],
      configObj: {},
      rules: {
        image_id: [
          {
            required: true,
            message: lang.select + lang.setting_text47,
            type: "error",
          },
        ],
        password: [
          {
            validator: () =>
              (this.reinstallType === "passworld" &&
                this.reinstallData.password != "") ||
              (this.reinstallType === "ssh" &&
                this.reinstallData.ssh_key_id != ""),
            message: this.calcMessage,
            type: "error",
          },
        ],
        port: [
          {
            required: true,
            message: lang.input + lang.setting_text56,
            type: "error",
          },
        ],
      },
      flowObj: {
        total_num: '--',
        leave_num: '--',
        base_flow: '--',
        temp_flow: '--'
      },
      timer: null
    };
  },
  beforeDestroy() {
    clearTimeout(this.timer);
  },
  computed: {
    isWindows () {
      return (
        this.osList.filter((item) => item.id === this.selectOsId)[0]?.name ===
        "Windows" || false
      );
    },
    calcMessage () {
      return this.reinstallType === "passworld"
        ? lang.input + lang.setting_text53
        : lang.select + lang.setting_text54;
    },
    slectOsImg () {
      return (
        this.osList.filter((item) => item.id === this.selectOsId)[0]?.name || ""
      );
    },
    calcImgList () {
      return (
        this.osList.filter((item) => item.id === this.selectOsId)[0]?.image ||
        []
      );
    },
    btnList () {
      return [
        {
          name: lang.setting_text41,
          op: "on",
          isShow: true,
        },
        {
          name: lang.setting_text42,
          op: "off",
          isShow: true,
        },
        {
          name: lang.setting_text43,
          op: "reboot",
          isShow: true,
        },

        {
          name: lang.setting_text46,
          op: "vnc",
          isShow: true,
        },
        {
          name: lang.setting_text47,
          op: "reinstall",
          isShow: true,
        },
        {
          name: lang.setting_text48,
          op: "reset_password",
          isShow: true,
        },
        {
          name: lang.setting_text49,
          op: "rescue",
          isShow: true,
        },
      ];
    },
  },
  watch: {
    selectOsId (id) {
      const curGroupName = this.osList.filter((item) => item.id === id)[0]
        ?.name;
      if (curGroupName === "Windows") {
        this.reinstallData.port = 3389;
      } else {
        this.reinstallData.port = 22;
      }
    },
  },
  created () {
    this.id = getQuery().id;
    this.client_id = getQuery().client_id;
    this.getDetail();
    this.getStatus();
    this.getSshList();
  },
  methods: {
    // 随机生成密码
    autoPass () {
      const pass = randomCoding(1) + 0 + genEnCode(9, 1, 1, 0, 1, 0);
      this.reinstallData.password = pass;
      this.rescueData.password = pass;
      this.resetPasswordData.password = pass;
    },
    // 随机端口
    randomNum () {
      const min = this.configObj.rand_ssh_port_start * 1;
      const max = this.configObj.rand_ssh_port_end * 1;
      const range = max - min + 1;
      return Math.floor(Math.random() * range) + min;
    },
    // 随机生成port
    autoPort () {
      this.reinstallData.port = Math.floor(10000 + Math.random() * 10000);
    },
    osChange () {
      this.reinstallData.image_id = this.calcImgList[0].id;
    },
    sshChange (val) {
      this.reinstallData.password = "";
      this.reinstallData.ssh_key_id = val === "ssh" ? this.sshList[0].id : "";
    },
    getSshList () {
      apiSshList({
        page: 1,
        limit: 999999,
        client_id: this.client_id,
      }).then((res) => {
        this.sshList = res.data.data.list;
      });
    },
    getDetail () {
      apiOperateDetail(this.id)
        .then((res) => {
          this.osList = res.data.data.cloud_os_group.map((item) => {
            item.image = res.data.data.cloud_os.filter((items) => {
              return items.group === item.id;
            });
            return item;
          });
          // 处理流量包
          const { host_data, dcim } = res.data.data;
          let total_num = host_data.bwlimit === 0 ? lang.module_no_limit : Number(host_data.bwlimit).toFixed(2);
          let leave_num = host_data.bwlimit === 0 ? lang.module_no_limit : Number(host_data.bwlimit - host_data.bwusage).toFixed(2);
          let temp_flow = Number(dcim.flow_packet_use_list.reduce((all, cur) => {
            all += cur.capacity;
            return all;
          }, 0)).toFixed(2);
          let base_flow = Number(host_data.bwlimit - temp_flow).toFixed(2);
          this.flowObj = {
            total_num, leave_num, base_flow, temp_flow
          };
        })
        .catch((err) => {
          this.$message.error(err.data.msg);
        });
    },
    confirmModule () {
      this.sureOperate();
    },

    saveReinstall ({ validateResult, firstError }) {
      if (validateResult === true) {
        if (
          this.opName === "reset_password" &&
          !this.resetPasswordData.checked
        ) {
          this.$message.warning(lang.setting_text63);
          return;
        }
        this.sureOperate();
      } else {
        console.log("Errors: ", validateResult);
        this.$message.warning(firstError);
      }
    },

    hadelSafeConfirm (val, remember) {
      this[val]('', remember);
    },

    getRemotInfo () {
      getMfCloudRemote(this.id)
        .then((res) => {
          this.remoteInfo = res.data.data;
        })
        .catch((err) => {
          this.$message.error(err.data.msg);
        });
    },
    getStatus () {
      getInstanceStatus(this.id)
        .then((res) => {
          this.status = res.data.data.status;
          if (res.data.data.status === "operating") {
           this.timer = setTimeout(() => {
              this.getStatus();
            }, 2000)
          } else {
            clearTimeout(this.timer);
          }
        })
        .catch((err) => {
          clearTimeout(this.timer);
          this.$message.error(err.data.msg);
        });
    },

    handelClick (item) {
      this.opName = item.op;
      this.optTilte = lang.setting_text51 + item.name + "?";
      if (item.op === "reinstall") {
        this.selectOsId = "";
        this.reinstallType = "passworld";
        this.reinstallData = {
          image_id: "",
          password: "",
          ssh_key_id: "",
          port: "",
          part_type: 0,
        };
        this.reinstallModel = true;
        setTimeout(() => {
          this.$refs.reinstallForm.reset();
        }, 0);
      } else if (item.op === "rescue") {
        this.rescueData.type = 1;
        this.rescueData.password = "";
        this.rescueModel = true;
        setTimeout(() => {
          this.$refs.rescueForm.reset();
        }, 0);
      } else if (item.op === "reset_password") {
        this.resetPasswordData.password = "";
        this.resetPasswordData.checked = false;
        this.resetPasswordModel = true;
        setTimeout(() => {
          this.$refs.resetForm.reset();
        }, 0);
      } else {
        this.moduleVisible = true;
      }
    },
    async sureOperate (e, remember_operate_password = 0) {
      // if (!this.admin_operate_password) {
      //   this.$refs.safeRef.openDialog("sureOperate");
      //   return;
      // }
      const admin_operate_password = this.admin_operate_password;
      this.admin_operate_password = "";
      this.moduleLoading = true;
      try {
        let params = {
          id: this.id,
          op: this.opName,
          admin_operate_password,
          admin_operate_methods: "sureOperate",
          remember_operate_password,
        };
        // 处理需要传参的操作
        if (this.opName === "reinstall") {
          params = Object.assign(params, this.reinstallData);
          params.os = params.image_id;
          params.part_type = this.isWindows ? params.part_type : 0;
        } else if (this.opName === "rescue") {
          params = Object.assign(params, this.rescueData);
          params.temp_pass = params.password;
        } else if (this.opName === "reset_password") {
          params = Object.assign(params, this.resetPasswordData);
        }
        const res = await handelOperate(params).catch((error) => {
          this.moduleLoading = false;
          if (error.data.data) {
            if (
              !admin_operate_password &&
              error.data.data.operate_password === 1
            ) {
              return;
            }
          }
          this.$message.error(error.data.msg);
        });
        this.moduleLoading = false;
        this.$message.success(res.data.msg);

        if (this.opName === "reinstall") {
          this.reinstallModel = false;
        } else if (this.opName === "rescue") {
          this.rescueModel = false;
        } else if (this.opName === "reset_password") {
          this.resetPasswordModel = false;
        }
        if (this.opName === "vnc") {
          const opener = window.open("", "_blank");
          opener.location = res.data.data.url;
        } else if (this.opName === "rescue" || this.opName === "/rescue/exit") {
          this.getRemotInfo();
        } else {
          this.getStatus();
        }
        this.moduleVisible = false;
      } catch (error) {
        this.moduleLoading = false;
      }
    },
  },
}).$mount(template);
window.adminOperateVue = adminOperateVue;
