(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("template")[0];
    Vue.prototype.lang = Object.assign(window.lang, window.plugin_lang);
    new Vue({
      components: {
        asideMenu,
        topMenu,
        pagination,
      },
      created() {
        // 获取通用信息
        this.getCommonData();
        this.getOrderConfig();
        // 获取工单类型
        this.getTicketType();
        // 获取工单状态
        this.getTicketStatus();
        // 获取关联产品列表
        this.getHostList();
      },
      mounted() {},
      updated() {
        // // 关闭loading
        document.getElementById("mainLoading").style.display = "none";
        document.getElementsByClassName("template")[0].style.display = "block";
      },
      destroyed() {},
      data() {
        return {
          // 分页
          params: {
            page: 1,
            limit: 20,
            pageSizes: [20, 50, 100],
            total: 200,
            orderby: "id",
            sort: "desc",
            keywords: "",
            status: [3],
            ticket_type_id: "",
          },
          // 通用数据
          commonData: {},
          // 表格数据
          dataList: [],
          // 表格加载
          tableLoading: false,
          // 创建工单弹窗是否显示
          isShowDialog: false,
          // 创建工单弹窗 数据
          formData: {
            title: "",
            ticket_type_id: "",
            host_ids: [],
            content: "",
            attachment: [],
          },
          // 表单错误信息显示
          errText: "",
          // 工单类别
          ticketType: [],
          ticketStatus: [],
          // 关联产品列表
          hostList: [],
          createBtnLoading: false,
          fileList: [],
          configObj: {
            ticket_notice_open: 0,
            ticket_notice_description: "",
          },
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
      },
      methods: {
        async getOrderConfig() {
          try {
            const res = await getOrderConfig();
            this.configObj = res.data.data;
          } catch (error) {}
        },
        // 每页展示数改变
        sizeChange(e) {
          this.params.limit = e;
          this.params.page = 1;
          // 获取列表
          this.getTicketList();
        },
        // 当前页改变
        currentChange(e) {
          this.params.page = e;
          this.getTicketList();
        },
        clearKey() {
          this.params.keywords = "";
          this.inputChange();
        },
        inputChange() {
          this.params.page = 1;
          this.getTicketList();
        },
        // 获取通用配置
        getCommonData() {
          this.commonData = JSON.parse(
            localStorage.getItem("common_set_before")
          );
          document.title =
            this.commonData.website_name + "-" + lang.ticket_label15;
        },
        // 获取工单列表
        getTicketList() {
          this.tableLoading = true;
          ticketList(this.params).then((res) => {
            if (res.data.status === 200) {
              this.dataList = res.data.data.list;
              this.params.total = res.data.data.count;
            }
            this.tableLoading = false;
          });
        },
        // 展示创建弹窗 并初始化弹窗数据
        showCreateDialog() {
          location.href = `addTicket.htm`;
        },
        handelMore() {},
        // 验证表单调用接口执行 创建工单
        doCreateTicket() {
          let isPass = true;
          this.errText = "";
          const formData = this.formData;
          if (!formData.title) {
            isPass = false;
            this.errText = lang.ticket_tips4;
          }

          if (!formData.ticket_type_id) {
            isPass = false;
            this.errText = lang.ticket_tips2;
          }
          if (!formData.host_ids) {
            isPass = false;
            this.errText = lang.ticket_tips5;
          }
          if (!formData.content) {
            isPass = false;
            this.errText = lang.ticket_tips6;
          }

          if (isPass) {
            // 调用创建工单接口
            const params = formData;
            this.createBtnLoading = true;
            createTicket(params)
              .then((res) => {
                if (res.data.status === 200) {
                  // 关闭弹窗
                  this.isShowDialog = false;
                  // 刷新工单列表
                  this.getTicketList();
                }
                this.delFile();
                this.createBtnLoading = false;
              })
              .catch((error) => {
                this.errText = error.data.msg;
                this.createBtnLoading = false;
              });
          }
        },
        closeDialog() {
          this.delFile();
          this.isShowDialog = false;
        },
        hexToRgb(hex) {
          const color = hex.split("#")[1];
          const r = parseInt(color.substring(0, 2), 16);
          const g = parseInt(color.substring(2, 4), 16);
          const b = parseInt(color.substring(4, 6), 16);
          return `rgba(${r},${g},${b},0.12)`;
        },
        // 获取工单类型
        getTicketType() {
          ticketType().then((res) => {
            if (res.data.status === 200) {
              this.ticketType = res.data.data.list;
            }
          });
        },
        // 获取工单状态列表
        getTicketStatus() {
          ticketStatus().then((res) => {
            if (res.data.status === 200) {
              this.params.status = [3];
              this.ticketStatus = res.data.data.list;
              res.data.data.list.forEach((item) => {
                if (item.status === 0) {
                  this.params.status.push(item.id);
                }
              });
              // 获取工单列表
              this.getTicketList();
            }
          });
        },
        // 获取产品列表
        getHostList() {
          const params = {
            keywords: "",
            status: "",
            page: 1,
            limit: 1000,
            orderby: "id",
            sort: "desc",
          };
          hostAll(params).then((res) => {
            if (res.data.status === 200) {
              this.hostList = res.data.data.list;
            }
          });
        },
        // 跳转工单详情
        itemReply(record) {
          const id = record.id;
          location.href = `ticketDetails.htm?id=${id}`;
        },
        // 关闭工单
        itemClose(record) {
          const id = record.id;
          const params = {
            id,
          };
          // 调用关闭工单接口 给出结果
          closeTicket(params)
            .then((res) => {
              if (res.data.status === 200) {
                this.$message({
                  message: res.data.msg,
                  type: "success",
                });
                this.getTicketList();
              }
            })
            .catch((err) => {
              this.$message.error(err.data.msg);
            });
        },
        // 催单
        itemUrge(record) {
          const nowDate = new Date().getTime();
          const last_reply_time = record.last_urge_time * 1000;
          if (nowDate - last_reply_time < 1000 * 60 * 15) {
            this.$message.success(lang.ticket_label16);
            return;
          }
          const id = record.id;
          const params = { id };
          // 调用催单接口 给出结果
          urgeTicket(params)
            .then((res) => {
              if (res.data.status === 200) {
                this.$message.success(res.data.msg);
                this.getTicketList();
              }
            })
            .catch((err) => {
              this.$message.error(err.data.msg);
            });
        },
        // 上传文件相关
        handleSuccess(response, file, fileList) {
          if (response.status === 200) {
            this.formData.attachment.push(response.data.save_name);
          }
        },
        beforeRemove(file, fileList) {
          // 获取到删除的 save_name
          let save_name = file.response.data.save_name;
          this.formData.attachment = this.formData.attachment.filter((item) => {
            return item != save_name;
          });
        },
        // 删除上传文件的文件
        delFile() {
          let uploadFiles = this.$refs["fileupload"].uploadFiles;
          let length = uploadFiles.length;
          uploadFiles.splice(0, length);
        },
        titleClick(record) {
          const id = record.id;
          location.href = `ticketDetails.htm?id=${id}`;
        },
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
