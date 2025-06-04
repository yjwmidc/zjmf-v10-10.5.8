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
      },
      data() {
        return {
          baseUrl: str,
          tableHeight: 0,
          listData: [],
          pagination: {
            total: 0,
            pageSize: 10,
            pageSizeOptions: [10, 20, 50, 100],
            showJumper: true,
          },
          params: {
            page: 1,
            limit: 10,
            keywords: "",
            host_status: [],
            status: [],
            refund_record_id: "",
          }, //分页
          endVisible: false,
          obj: null,
          reject_reason: "", //驳回原因
          columns: [
            {
              colKey: "id",
              title: "ID",
              cell: "id",
              width: 90,
              align: "center",
            },
            {
              colKey: "client_name",
              title: lang.proposer,
              width: 120,
              // 对齐方式
              align: "left",
              ellipsis: true,
            },
            {
              minWidth: 200,
              colKey: "product_name",
              title: lang.apply_product,
              // 对齐方式
              align: "left",
              ellipsis: true,
            },
            {
              width: 140,
              colKey: "suspend_reason",
              title: lang.refund_op_text16,
              // 对齐方式
              align: "left",
              ellipsis: true,
            },
            {
              width: 140,
              colKey: "type",
              title: lang.refund_op_text13,
              ellipsis: true,
            },
            {
              width: 110,
              cell: "refund_product_type",
              title: lang.refundable_type,
              // 对齐方式
              ellipsis: true,
            },
            {
              width: 100,
              colKey: "amount",
              cell: "price",
              title: lang.refund_amount,
              // 对齐方式
              align: "left",
              ellipsis: true,
            },

            {
              colKey: "admin_name",
              title: lang.auditor,
              // 对齐方式
              align: "center",
              ellipsis: true,
              width: 150,
            },
            {
              width: 180,
              colKey: "create_time",
              title: lang.time_application,
              // 对齐方式
              align: "left",
              ellipsis: true,
            },
            {
              colKey: "due_time",
              title: lang.due_time,
              // 对齐方式
              align: "left",
              ellipsis: true,
              width: 150,
            },
            {
              width: 120,
              colKey: "host_status",
              title: lang.product_status,
              cell: "status",
              ellipsis: true,
            },
            {
              width: 120,
              colKey: "status",
              title: lang.apply_status,
              cell: "status",
              ellipsis: true,
            },
            {
              colKey: "op",
              title: lang.operation,
              cell: "op",
              width: 130,
              ellipsis: true,
              fixed: "right",
            },
          ],
          formData: {
            reject_reason: "",
          },
          rules: {
            reject_reason: [
              {
                required: true,
                message: lang.input + lang.dismiss_the_reason,
                type: "error",
              },
            ],
          },
          delVisible: false,
          delId: "",
          statusList: [
            {value: "Pending", label: lang.to_audit},
            {value: "Suspending", label: lang.to_stop_using},
            {value: "Suspend", label: lang.stop_using_the},
            {value: "Suspended", label: lang.has_been_discontinued},
            {value: "Refund", label: lang.refunded},
            {value: "Reject", label: lang.review_the_rejected},
            {value: "Cancelled", label: lang.Cancelled},
          ],
          hostStatusList: [
            {value: "Unpaid", label: lang.Unpaid},
            {value: "Pending", label: lang.Pending},
            {value: "Active", label: lang.Active},
            {value: "Suspended", label: lang.Suspended},
            {value: "Deleted", label: lang.Deleted},
            {value: "Failed", label: lang.Failed},
          ],
        };
      },
      methods: {
        // 时间格式转换
        formatDate(date, judge) {
          const str1 = [
            date.getFullYear(),
            this.formatDateAdd0(date.getMonth() + 1),
            this.formatDateAdd0(date.getDate()),
          ].join("/");
          const str2 = [
            this.formatDateAdd0(date.getHours()),
            this.formatDateAdd0(date.getMinutes()),
            this.formatDateAdd0(date.getSeconds()),
          ].join(":");
          if (judge) {
            return str1 + " " + str2;
          } else {
            return str1;
          }
        },
        formatDateAdd0(m) {
          return m < 10 ? "0" + m : m;
        },
        // 切换分页
        onPageChange(pageInfo) {
          this.params.page = pageInfo.current;
          this.params.limit = pageInfo.pageSize;
          this.pagination.pageSize = pageInfo.pageSize;
          this.getList();
        },
        // 输入框-查询
        Search() {
          this.params.page = 1;
          this.getList();
        },
        // 输入框-清空
        Clear() {
          this.params.page = 1;
          this.params.keywords = "";
          this.getList();
        },
        //  取消
        btn_end(data) {
          this.delVisible = true;
          this.delId = data.id;
          // let mydialog = this.$dialog({
          //   theme: "warning",
          //   header: `${lang.canceled_su}`,
          //   className: 't-dialog-new-class1 t-dialog-new-class2',
          //   style: 'color: rgba(0, 0, 0, 0.6)',
          //   confirmBtn: lang.sure,
          //   cancelBtn: lang.cancel,
          //   onConfirm: ({ e }) => {
          //     endRefund(data.id).then((res) => {
          //       this.getList();
          //       this.$message.info({
          //         content: lang.canceled_success,
          //         duration: 3000,
          //         // 层级控制：非当前场景自由控制开关的关键代码，仅用于测试 API 是否运行正常
          //         zIndex: 1001,
          //         // 挂载元素控制：非当前场景自由控制开关的关键代码，仅用于测试 API 是否运行正常
          //         attach: '#t-message-toggle',
          //       });
          //       mydialog.hide();
          //     }).catch(err => {
          //       this.$message.error(err.data.msg)
          //     })
          //   }
          // });
        },
        sureDel() {
          endRefund(this.delId)
            .then((res) => {
              this.getList();
              this.$message.info({
                content: lang.canceled_success,
                duration: 3000,
                // 层级控制：非当前场景自由控制开关的关键代码，仅用于测试 API 是否运行正常
                zIndex: 1001,
                // 挂载元素控制：非当前场景自由控制开关的关键代码，仅用于测试 API 是否运行正常
                attach: "#t-message-toggle",
              });
              //  mydialog.hide();
              this.delVisible = false;
            })
            .catch((err) => {
              console.log("@@@@", err);
              this.$message.error(err.data.msg);
            });
        },

        //通过
        btn_OK(data) {
          okRefund(data.id).then((res) => {
            this.getList();
            this.$message
              .info({
                content: lang.pass_review,
                duration: 3000,
                // 层级控制：非当前场景自由控制开关的关键代码，仅用于测试 API 是否运行正常
                zIndex: 1001,
                // 挂载元素控制：非当前场景自由控制开关的关键代码，仅用于测试 API 是否运行正常
                attach: "#t-message-toggle",
              })
              .catch((err) => {
                this.$message.error(err.data.msg);
              });
          });
        },
        //驳回
        btn_NO(data) {
          this.endVisible = true;
          this.obj = data;
          this.formData.reject_reason = "";
        },

        //驳回确认
        dismissConfirmation({validateResult, firstError}) {
          if (validateResult === true) {
            NoRefund({
              id: this.obj.id,
              reject_reason: this.formData.reject_reason,
            })
              .then((res) => {
                this.getList();
                this.$message.success({
                  content: lang.reject_success,
                  duration: 3000,
                  // 层级控制：非当前场景自由控制开关的关键代码，仅用于测试 API 是否运行正常
                  zIndex: 1001,
                  // 挂载元素控制：非当前场景自由控制开关的关键代码，仅用于测试 API 是否运行正常
                  attach: "#t-message-toggle",
                });
                this.endVisible = false;
              })
              .catch((err) => {
                this.$message.error(err.data.msg);
              });
          } else {
            console.log("Errors: ", validateResult);
            this.$message.warning(firstError);
          }
        },
        // dismissConfirmation () {
        //   if (!this.reject_reason) {
        //     this.$message.info({
        //       content: lang.dismiss_the_reason_null,
        //       duration: 3000,
        //       theme: "warning",
        //       // 层级控制：非当前场景自由控制开关的关键代码，仅用于测试 API 是否运行正常
        //       zIndex: 1001,
        //       // 挂载元素控制：非当前场景自由控制开关的关键代码，仅用于测试 API 是否运行正常
        //       attach: '#t-message-toggles',
        //     });
        //     return
        //   }
        //   NoRefund({
        //     id: this.obj.id,
        //     reject_reason: this.reject_reason
        //   }).then((res) => {
        //     this.getList();
        //     this.$message.success({
        //       content: lang.reject_success,
        //       duration: 3000,
        //       // 层级控制：非当前场景自由控制开关的关键代码，仅用于测试 API 是否运行正常
        //       zIndex: 1001,
        //       // 挂载元素控制：非当前场景自由控制开关的关键代码，仅用于测试 API 是否运行正常
        //       attach: '#t-message-toggle',
        //     });
        //     this.endVisible = false;
        //   });
        // },

        //获取审核列表list
        async getList() {
          let list = await getRefund(this.params);
          list.data.data.list.forEach((item) => {
            item.create_time = Number(item.create_time) * 1000;
            item.create_time = item.create_time
              ? this.formatDate(new Date(item.create_time), true)
              : "";
            item.due_time = Number(item.due_time) * 1000;
            item.due_time = item.due_time
              ? this.formatDate(new Date(item.due_time))
              : "";
            if (!item.admin_name) {
              item.admin_name = "-";
            }
          });
          this.listData = list.data.data.list;

          this.pagination.total = list.data.data.count;
        },
      },
      created() {
        const domHeight = template.scrollHeight;
        this.tableHeight = domHeight - 250;
        const urlParams = getQuery();
        if (urlParams.id) {
          this.params.refund_record_id = urlParams.id;
        }
        this.getList();
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
