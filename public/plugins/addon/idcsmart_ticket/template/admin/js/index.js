(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("template")[0];
    Vue.prototype.lang = Object.assign(window.lang, window.plugin_lang);
    const host = location.origin;
    const fir = location.pathname.split("/")[1];
    const str = `${host}/${fir}/`;
    new Vue({
      components: {
        comConfig,
        comChooseUser,
        opinionButton,
      },
      data() {
        return {
          message: "template...",
          activeTab: "first",
          baseUrl: str,
          showIdcsmartTicketInternal: false,
          params: {
            keywords: "", // 关键字
            page: 1,
            limit: 10,
            status: [], // 状态
            ticket_type_id: "", // 工单类型
            client_id: "", // 用户搜索
            admin_id: "", // 跟进人搜索
          },
          userParams: {
            keywords: "",
            page: 1,
            limit: 20,
            orderby: "id",
            sort: "desc",
          },
          popupProps: {
            overlayInnerStyle: (trigger) => ({
              width: `${trigger.offsetWidth}px`,
              "max-height": "362px",
            }),
          },
          searchLoading: false,
          closeRow: null,
          closeOrderVisible: false,
          // 转发弹窗
          forwardDialogVisible: false,
          audio_tip: null,
          playNum: 1,
          isPlayerAudio: false,
          forwardFormData: {
            admin_role_id: "",
            admin_id: "",
            ticket_type_id: "",
            notes: "",
          },
          forwardFormRules: {
            admin_role_id: [
              {
                required: true,
                message: lang.order_designated_department + lang.isRequired,
                type: "error",
              },
            ],
            admin_id: [
              {
                required: true,
                message: lang.order_designated_person + lang.isRequired,
                type: "error",
              },
            ],
            notes: [
              {
                required: true,
                message: lang.order_designated_reson + lang.isRequired,
                type: "error",
              },
            ],
            ticket_type_id: [
              {
                required: true,
                message: lang.order_designated_type + lang.isRequired,
                type: "error",
              },
            ],
          },
          total: 100,
          pageSizeOptions: [10, 20, 50, 100],
          tableHeight: 500,
          // 工单转内部弹窗
          turnInsideDialogVisible: false,
          turnInsideFormData: {},
          userName: localStorage.getItem("userName") || "-",
          turnInsideFormRules: {
            title: [
              {
                required: true,
                message: lang.order_title + lang.isRequired,
                type: "error",
              },
              {
                validator: (val) => val.length >= 1 && val.length <= 150,
                message: lang.verify8 + "1-150",
                type: "warning",
              },
            ],
            priority: [
              {
                required: true,
                message: lang.order_priority + lang.isRequired,
                type: "error",
              },
            ],
            ticket_type_id: [
              {
                required: true,
                message: lang.order_name + lang.isRequired,
                type: "error",
              },
            ],
            admin_role_id: [
              {
                required: true,
                message: lang.order_designated_department + lang.isRequired,
                type: "error",
              },
            ],
            content: [
              {
                validator: (val) => !val || val.length <= 3000,
                message: lang.verify3 + "3000",
                type: "warning",
              },
            ],
          },
          // 指定部门下拉框数据（管理员分组列表数据）
          departmentOptions: [],
          // 指定人员下拉框数据（分组下管理员）
          adminsOptions: [],
          // 所有人员数据
          adminList: [],
          // 关联客户下拉框数据
          clientOptions: [],
          // 关联产品下拉框数据
          hostOptions: [],
          // 工单类型下拉框数据
          orderTypeOptions: [],
          // 工单状态下拉框数据
          order_status_options: [],
          // 默认自动刷新时间
          order_time: 180000,
          timeList: [
            {
              label: "1" + lang.sales_plugin_text73,
              value: 60000,
            },
            {
              label: "3" + lang.sales_plugin_text73,
              value: 180000,
            },
            {
              label: "5" + lang.sales_plugin_text73,
              value: 300000,
            },
            {
              label: "10" + lang.sales_plugin_text73,
              value: 600000,
            },
          ],
          // 紧急程度下拉框数据
          priorityOptions: [
            {
              id: "medium",
              name: lang.order_priority_medium,
            },
            {
              id: "high",
              name: lang.order_priority_high,
            },
          ],
          // 上传文件headers设置
          uploadHeaders: {
            Authorization: "Bearer" + " " + localStorage.getItem("backJwt"),
          },
          uploadUrl: str + "v1/upload",
          uploadTip: "",
          // 用户工单列表
          userOrderTableloading: false,
          userOrderData: [],
          userOrderColumns: [
            {
              align: "left",
              width: "100",
              colKey: "id",
              title: "ID",
            },
            {
              align: "left",
              width: "450",
              colKey: "title",
              title: lang.order_title,
              ellipsis: true,
            },
            {
              align: "left",
              width: "120",
              colKey: "name",
              title: lang.order_name,
            },
            {
              align: "left",
              width: "200",
              colKey: "user",
              title: lang.order_user_people,
              ellipsis: true,
            },
            {
              align: "left",
              width: "150",
              colKey: "last_reply_time",
              title: lang.order_last_reply_time,
            },
            {
              align: "left",
              width: "120",
              colKey: "status",
              title: lang.order_status,
            },
            {
              align: "left",
              width: "120",
              colKey: "op",
              fixed: "right",
              title: lang.operation,
            },
          ],
          // 管理工单类型弹窗相关
          orderTypeMgtTableloading: false,
          orderTypeMgtDialogVisible: false,
          orderTypeMgtData: [],
          orderTypeMgtColumns: [
            {
              align: "left",
              width: 50,
              colKey: "index",
              title: lang.order_index,
            },
            {
              align: "left",
              width: 130,
              colKey: "name",
              title: lang.order_type_name,
            },
            {
              align: "left",
              width: 180,
              colKey: "role_name",
              title: lang.order_default_receive_department,
            },
            {
              align: "left",
              width: 100,
              colKey: "operation",
              title: lang.operation,
              fixed: "right",
            },
          ],
          // 新建工单弹窗
          addOrderDialogVisible: false,
          addOrderFormData: {},
          addOrderFormRules: {
            title: [
              {
                required: true,
                message: lang.order_title + lang.isRequired,
                type: "error",
              },
              {
                validator: (val) => val.length >= 1 && val.length <= 150,
                message: lang.verify8 + "1-150",
                type: "warning",
              },
            ],
            // priority: [{ required: true, message: lang.order_priority + lang.isRequired, type: 'error' }],
            client_id: [
              {
                required: true,
                message: lang.order_text2 + lang.isRequired,
                type: "error",
              },
            ],
            ticket_type_id: [
              {
                required: true,
                message: lang.order_name + lang.isRequired,
                type: "error",
              },
            ],
            // admin_role_id: [{
            //   required: true, message: lang.order_designated_department + lang.isRequired,
            //   type: 'error'
            // }],
            content: [
              {
                validator: (val) => !val || val.length <= 3000,
                message: lang.verify3 + "3000",
                type: "warning",
              },
            ],
          },
          submitLoading: false,
        };
      },
      methods: {
        changeUser(val) {
          this.params.client_id = val;
        },
        // 选择部门
        changeType(type) {
          this.adminsOptions = this.orderTypeOptions.filter(
            (item) => item.id === type
          )[0]?.admin;
          this.forwardFormData.admin_id = "";
        },
        // 获取工单类型数据
        getOrderTypeOptions(id) {
          const params = {
            admin_role_id: id ? id : "",
          };
          getUserOrderType(params)
            .then((result) => {
              this.orderTypeOptions = result.data.data.list;
            })
            .catch();
        },
        rowClick(e) {
          // location.href = `ticket_detail.htm?id=${e.row.id}`;
        },
        // 工单-转发
        internalOrderForward(row) {
          // 清除已选人员数据
          this.forwardFormData.admin_id = "";
          this.forwardFormData.ticket_type_id = "";
          this.forwardFormData.admin_role_id = "";
          this.forwardFormData.id = row.id;
          this.forwardDialogVisible = true;
          this.forwardFormData.notes = "";
        },
        goclient_detail(row) {
          location.href = str + `client_detail.htm?client_id=${row.client_id}`;
        },
        // 工单-转发-提交
        forwardFormSubmit({validateResult, firstError}) {
          if (validateResult === true) {
            const data = this.forwardFormData;
            const params = {
              id: data.id,
              notes: data.notes,
              //  admin_role_id: data.admin_role_id, //指定部门
              ticket_type_id: data.ticket_type_id,
              admin_id: data.admin_id ? data.admin_id : null, //管理员
            };
            this.submitLoading = true;
            forwardTicket(data.id, params)
              .then((result) => {
                this.$message.success({
                  content: result.data.msg,
                  placement: "top-right",
                });
                this.forwardDialogClose();
                this.getUserOrderList();
              })
              .catch((result) => {
                this.$message.warning({
                  content: result.data.msg,
                  placement: "top-right",
                });
              })
              .finally(() => {
                this.submitLoading = false;
              });
          } else {
            this.$message.warning({
              content: firstError,
              placement: "top-right",
            });
          }
        },
        // 工单-转发-弹窗关闭
        forwardDialogClose() {
          this.forwardDialogVisible = false;
        },
        // 获取部门数据
        getDepartmentOptions() {
          getAdminRole({page: 1, limit: 10000})
            .then((result) => {
              this.departmentOptions = result.data.data.list;
            })
            .catch();
        },
        // 获取已激活插件
        getActive_plugin() {
          active_plugin().then((res) => {
            const arr = res.data.data.list.map((item) => {
              return item.name;
            });
            if (arr.includes("IdcsmartTicketInternal")) {
              this.showIdcsmartTicketInternal = true;
              this.$forceUpdate();
            }
          });
        },
        // 获取人员数据
        getAdminList() {
          getAdminList({page: 1, limit: 10000}).then((result) => {
            // 上游
            const obj = [
              {
                id: 0,
                name: lang.order_text80,
                nickname: lang.order_text80,
                phone_code: "86",
                status: 1,
                phone: "",
                email: "",
                roles: [],
              },
            ];
            this.adminList = [...result.data.data.list, ...obj];
          });
        },
        // 获取工单状态列表
        getTicketStatus() {
          ticketStatus().then((res) => {
            res.data.data.list.forEach((item) => {
              if (item["status"] === 0) {
                this.params.status.push(item.id);
              }
              delete item["default"];
            });
            this.order_status_options = res.data.data.list;
            this.isPlayerAudio = true;
            this.getUserOrderList();
          });
        },
        // 选择刷新时间
        selectTimeChange(value) {
          if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
          }
          this.timer = setInterval(() => {
            this.isPlayerAudio = true;
            this.doUserOrderClear();
          }, value);
        },
        askNotificationPermission() {
          // 检查浏览器是否支持通知
          return "Notification" in window;
        },
        // 工单-获取数据
        async getUserOrderList() {
          this.userOrderTableloading = true;
          const userOrderData = await getUserOrder(this.params);
          if (userOrderData && userOrderData.data) {
            userOrderData.data.data.list.forEach((item) => {
              item.newTitle = "#" + item.ticket_num + "-" + item.title;
            });
            const orderStatusNameList = this.order_status_options
              .filter((item) => {
                return item.status === 0 && item.id !== 5;
              })
              .map((item) => item.name);
            const needNotification =
              userOrderData.data.data.list.filter(
                (item) =>
                  orderStatusNameList.includes(item.status) &&
                  (item.admin_name == this.userName || !item.admin_name)
              ).length > 0;
            if (needNotification && this.isPlayerAudio) {
              this.audio_tip.addEventListener("ended", this.palyAudio);
              this.audio_tip.play();
              if (this.askNotificationPermission()) {
                new Notification(lang.work_order_tip);
              }
            }
            this.userOrderData = userOrderData.data.data.list;
            this.total = userOrderData.data.data.count;
            this.userOrderTableloading = false;
          }
        },
        // 播放音频
        palyAudio() {
          this.playNum++;
          if (this.playNum >= 3) {
            this.playNum = 1;
            this.isPlayerAudio = false;
            return;
          }
          this.audio_tip.play();
        },
        // 工单-切换分页
        changePage(e) {
          this.params.page = e.current;
          this.params.limit = e.pageSize;
          this.getUserOrderList();
        },
        // 新建工单
        goAddorder(row) {
          if (row) {
            location.href =
              str +
              `/plugin/idcsmart_ticket_internal/ticket_internal_add.htm?id=${row.id}&client_id=${row.client_id}&host_ids=${row.host_ids}`;
          } else {
            location.href = `ticket_add.htm`;
          }
        },
        // 工单-查询
        doUserOrderSearch(val) {
          if (val === "all") {
            this.params.status = this.order_status_options.map((item) => {
              return item.id;
            });
          }
          this.params.page = 1;
          this.getUserOrderList();
        },
        clearKey() {
          this.params.keywords = "";
          this.doUserOrderSearch();
        },
        // 工单-列表-清空
        doUserOrderClear() {
          this.params.page = 1;
          this.params.keywords = "";
          this.params.status = []; // 状态
          this.params.ticket_type_id = ""; // 工单类型
          this.params.client_id = ""; // 用户搜索
          this.params.admin_id = ""; // 跟进人搜索
          this.getTicketStatus();
        },
        // 工单-转内部
        userOrderTurnInside(row) {
          this.turnInsideFormData = {};
          getUserOrderDetail(row.id)
            .then((result) => {
              const data = result.data.data.ticket;
              if (data.attachment && data.attachment.length > 0) {
                data.attachment.forEach((item, i) => {
                  data.attachment[i] = {response: {}};
                  data.attachment[i].name = item.split("^")[1];
                  data.attachment[i].response.save_name =
                    item.split("upload/")[1];
                });
              }
              this.turnInsideFormData = {...row, ...data};
              // const client = this.clientOptions.filter(item => item.username === row.username)[0];
              // this.turnInsideFormData.client_id = client ? client.id : null;

              if (this.turnInsideFormData.client_id) {
                this.clientChange(this.turnInsideFormData.client_id, true);
              }
              if (!this.turnInsideFormData.ticket_type_id) {
                const orderType = this.orderTypeOptions.filter(
                  (item) => item.name === this.turnInsideFormData.name
                )[0];
                this.turnInsideFormData.ticket_type_id = orderType
                  ? orderType.id
                  : null;
              }
              this.orderTypeChange(this.turnInsideFormData.ticket_type_id);
              // this.turnInsideFormData.attachment = [];
              this.turnInsideDialogVisible = true;
            })
            .catch((error) => {
              console.log(error);
            });
        },
        // 工单-转内部-关联用户变化
        clientChange(val, isFirst) {
          if (!isFirst) {
            // 清除已选产品数据
            this.turnInsideFormData.host_ids = [];
          }
          getHost({client_id: val, page: 1, limit: 10000}).then((result) => {
            this.hostOptions = result.data.data.list;
            this.hostChange();
          });
        },
        // 工单-转内部-关联产品变化
        hostChange() {
          this.$forceUpdate();
        },
        // 工单-转内部-选择部门变化
        departmentChange(val) {
          // 获取部门id对应部门名称
          const department = this.departmentOptions.filter(
            (item) => item.id === val
          )[0];
          const name = department ? department.name : null;
          const optionList = [];
          // 清除已选人员数据
          this.forwardFormData.admin_id = "";
          this.forwardFormData.ticket_type_id = "";
          this.adminList.forEach((item) => {
            if (name && item.roles === name) {
              optionList.push(item);
            }
          });
          this.adminsOptions = optionList;
          this.getOrderTypeOptions(val);
        },
        // 工单-转发-选择人员变化
        adminChange(val) {
          this.$forceUpdate();
        },
        // 工单-转内部-工单类型变化
        orderTypeChange(val) {
          // 获取当前所选数据工单类型对应部门名称
          const type = this.orderTypeOptions.filter(
            (item) => item.id === val
          )[0];
          const admin_role_name = type ? type.role_name : null;
          if (admin_role_name) {
            // 默认设置部门为工单类型对应的部门
            const data = this.departmentOptions.filter(
              (item) => item.name && item.name === admin_role_name
            )[0];
            this.turnInsideFormData.admin_role_id =
              data && data.id ? data.id : null;
            // 获取该部门下人员列表
            this.departmentChange(this.turnInsideFormData.admin_role_id);
          }
          // 清除已选人员数据
          this.turnInsideFormData.admin_id = null;
        },
        // 工单-转内部-上传附件-返回
        uploadFormatResponse(res) {
          if (!res || res.status !== 200) {
            return {error: lang.upload_fail};
          }
          return {...res, save_name: res.data.save_name};
        },
        // 上传附件-进度
        uploadProgress(val) {
          if (val.percent) {
            this.uploadTip = "uploaded" + val.percent + "%";
            if (val.percent === 100) {
              this.uploadTip = "";
            }
          }
        },
        // 上传附件-成功后
        uploadSuccess(res) {
          if (
            res.fileList.filter((item) => item.name == res.file.name).length > 1
          ) {
            this.$message.warning({
              content: lang.upload_same_name,
              placement: "top-right",
            });
            this.turnInsideFormData.attachment.splice(
              this.turnInsideFormData.attachment.length - 1,
              1
            );
          }
          this.$forceUpdate();
        },
        removeAttachment(file, i) {
          this.turnInsideFormData.attachment.splice(i, 1);
          this.$forceUpdate();
        },
        // 工单-转内部-提交
        turnInsideFormSubmit({validateResult, firstError}) {
          if (validateResult === true) {
            const data = this.turnInsideFormData;
            const attachmentList = [];
            data.attachment.forEach((item) => {
              attachmentList.push(item.response.save_name);
            });
            const params = {
              ticket_id: data.id, //工单ID(转内部工单时需要传此参数)
              title: data.title, //内部工单标题
              ticket_type_id: data.ticket_type_id, //内部工单类型ID
              priority: data.priority, //紧急程度:medium一般,high紧急
              client_id: data.client_id ? data.client_id : null, //关联用户
              admin_role_id: data.admin_role_id, //指定部门
              admin_id: data.admin_id ? data.admin_id : null, //管理员ID
              host_ids: data.host_ids ? data.host_ids : [], //关联产品ID,数组
              content: data.content ? data.content : "", //问题描述
              attachment: attachmentList, //附件,数组,取上传文件返回值save_name)
            };
            this.submitLoading = true;
            newInternalOrder(params)
              .then((result) => {
                this.$message.success({
                  content: result.data.msg,
                  placement: "top-right",
                });
                this.turnInsideDialogClose();
                this.getUserOrderList();
              })
              .catch((result) => {
                this.$message.warning({
                  content: result.data.msg,
                  placement: "top-right",
                });
              })
              .finally(() => {
                this.submitLoading = false;
              });
          } else {
            this.$message.warning({
              content: firstError,
              placement: "top-right",
            });
          }
        },
        // 工单-转内部-弹窗关闭
        turnInsideDialogClose() {
          this.turnInsideDialogVisible = false;
        },
        // 工单-接收
        userOrderReceive(row) {
          receiveUserOrder(row.id)
            .then((result) => {
              this.$message.success({
                content: result.data.msg,
                placement: "top-right",
              });
              this.getUserOrderList();
            })
            .catch((result) => {
              this.$message.warning({
                content: result.data.msg,
                placement: "top-right",
              });
            });
        },
        // 工单-回复
        userOrderReply(row) {
          (this.$checkPermission("auth_user_ticket_list_ticket_detail") ||
            this.$checkPermission("auth_user_ticket_detail")) &&
            (location.href = `ticket_detail.htm?id=${row.id}`);
        },
        isClose(row) {
          this.closeRow = row;
          this.closeOrderVisible = true;
        },
        closeDia() {
          this.closeOrderVisible = false;
        },
        // 工单-已解决
        userOrderResolved(row) {
          this.submitLoading = true;
          resolvedUserOrder(row.id)
            .then((result) => {
              this.$message.success({
                content: result.data.msg,
                placement: "top-right",
              });
              this.getUserOrderList();
              this.closeDia();
            })
            .catch((result) => {
              this.$message.warning({
                content: result.data.msg,
                placement: "top-right",
              });
            })
            .finally(() => {
              this.submitLoading = false;
            });
        },
        // 时间格式转换
        formatDate(dateStr) {
          const date = new Date(dateStr * 1000);
          const str1 = [
            date.getFullYear(),
            date.getMonth() + 1,
            date.getDate(),
          ].join("-");
          const str2 = [
            this.formatDateAdd0(date.getHours()),
            this.formatDateAdd0(date.getMinutes()),
            this.formatDateAdd0(date.getSeconds()),
          ].join(":");
          return str1 + " " + str2;
        },
        formatDateAdd0(m) {
          return m < 10 ? "0" + m : m;
        },
        // 工单-工单类型管理-弹窗显示
        orderTypeMgtDialogShow() {
          this.orderTypeMgtTableloading = true;
          this.getOrderTypeMgtList();
          this.orderTypeMgtDialogVisible = true;
        },
        // 工单-工单类型管理-获取工单类型数据
        async getOrderTypeMgtList() {
          const result = await getUserOrderType();
          if (result.status === 200) {
            const data = result.data.data.list;
            data.forEach((item) => {
              if (item.role_name) {
                const department = this.departmentOptions.filter(
                  (op) => op.name === item.role_name
                )[0];
                item.admin_role_id = department ? department.id : null;
              }
            });
            this.orderTypeMgtData = data;
            this.orderTypeMgtTableloading = false;
          }
        },
        // 工单-工单类型管理-关闭弹窗
        orderTypeMgtClose() {
          let mydialog = this.$dialog({
            theme: "warning",
            header: `${lang.sure_cancel}`,
            className: "t-dialog-new-class1 t-dialog-new-class2",
            style: "color: rgba(0, 0, 0, 0.6)",
            confirmBtn: lang.sure,
            cancelBtn: lang.cancel,
            onConfirm: ({e}) => {
              mydialog.hide();
              this.orderTypeMgtDialogVisible = false;
            },
          });
        },
        // 工单-工单类型管理-编辑
        orderTypeMgtEdit(row) {
          const checkResult = this.checkOrderType();
          if (checkResult) {
            for (let i = 0; i < this.orderTypeMgtData.length; i++) {
              if (this.orderTypeMgtData[i].id === row.id) {
                this.orderTypeMgtData.splice(i, 1, {
                  status: "edit",
                  ...row,
                });
              }
            }
          }
        },
        // 工单-工单类型管理-校验当前数据是否都已保存
        checkOrderType() {
          let result = true;
          this.orderTypeMgtData.forEach((item) => {
            if (item.status === "edit" || item.status === "add") {
              this.$message.warning({
                content: lang.order_type_verify3,
                placement: "top-right",
              });
              result = false;
            }
          });
          return result;
        },
        // 工单-工单类型管理-保存
        async orderTypeMgtSave(row) {
          if (!row.admin_role_id || !row.name) {
            this.$message.warning({
              content: lang.order_type_verify1,
              placement: "top-right",
            });
            return;
          }
          const params = {
            admin_role_id: row.admin_role_id,
            name: row.name,
          };
          let result = {};
          if (this.isSubmit) {
            return;
          }
          this.isSubmit = true;
          if (row.status === "edit") {
            params.id = row.id;
            orderTypeEdit(row.id, params)
              .then((result) => {
                this.$message.success({
                  content: result.data.msg,
                  placement: "top-right",
                });
                this.getOrderTypeMgtList();
              })
              .catch((result) => {
                this.$message.warning({
                  content: result.data.msg,
                  placement: "top-right",
                });
              })
              .finally(() => {
                setTimeout(() => {
                  this.isSubmit = false;
                }, 1000);
              });
          } else {
            orderTypeAdd(params)
              .then((result) => {
                this.$message.success({
                  content: result.data.msg,
                  placement: "top-right",
                });
                this.getOrderTypeMgtList();
              })
              .catch((result) => {
                this.$message.warning({
                  content: result.data.msg,
                  placement: "top-right",
                });
              })
              .finally(() => {
                setTimeout(() => {
                  this.isSubmit = false;
                }, 1000);
              });
          }
        },
        // 工单-工单类型管理-删除
        async orderTypeMgtDelete(row) {
          const result = await orderTypeDelete(row.id);
          if (result.status === 200) {
            this.$message.success({
              content: result.data.msg,
              placement: "top-right",
            });
            this.getOrderTypeMgtList();
          } else {
            this.$message.warning({
              content: result.data.msg,
              placement: "top-right",
            });
          }
        },
        // 工单-工单类型管理-新增
        newOrderType() {
          const checkResult = this.checkOrderType();
          if (checkResult) {
            this.orderTypeMgtData.push({
              status: "add",
              id: Math.random(),
              role_name: null,
              name: "",
            });
          }
        },
        // 工单-新建工单-提交
        addOrderFormSubmit({validateResult, firstError}) {
          if (validateResult === true) {
            const data = this.addOrderFormData;
            const attachmentList = [];
            data.attachment.forEach((item) => {
              attachmentList.push(item.response.save_name);
            });
            const params = {
              title: data.title, //内部工单标题
              ticket_type_id: data.ticket_type_id, //内部工单类型ID
              // priority: data.priority, //紧急程度:medium一般,high紧急
              client_id: data.client_id ? data.client_id : null, //关联用户
              // admin_role_id: data.admin_role_id, //指定部门
              // admin_id: data.admin_id ? data.admin_id : null, //管理员ID
              host_ids: data.host_ids ? data.host_ids : [], //关联产品ID,数组
              content: data.content ? data.content : "", //问题描述
              attachment: attachmentList, //附件,数组,取上传文件返回值save_name)
            };
            newUserOrder(params)
              .then((result) => {
                this.$message.success({
                  content: result.data.msg,
                  placement: "top-right",
                });
                this.addOrderDialogClose();
                this.getUserOrderList();
              })
              .catch((result) => {
                this.$message.warning({
                  content: result.data.msg,
                  placement: "top-right",
                });
              });
          } else {
            this.$message.warning({
              content: firstError,
              placement: "top-right",
            });
          }
        },
        // 工单-新建工单-弹窗关闭
        addOrderDialogClose() {
          this.addOrderDialogVisible = false;
        },
        // 工单-新建工单-弹窗显示
        newOrderDialogShow() {
          this.addOrderFormData = {};
          this.addOrderFormData.attachment = [];
          // this.getClientOptions();
          this.getOrderTypeOptions();
          this.getAdminList();
          this.addOrderDialogVisible = true;
        },
      },
      created() {
        const domHeight = template.scrollHeight;
        this.tableHeight = domHeight - 230;
        this.getDepartmentOptions();
        this.getAdminList();
        this.getActive_plugin();
        this.getOrderTypeOptions();
        setTimeout(() => {
          this.selectTimeChange(180000);
        }, 180000);
        window.doUserOrderSearch = this.doUserOrderSearch;
      },
      mounted() {
        this.audio_tip = document.getElementById("audio_tip");
        this.getTicketStatus();
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
