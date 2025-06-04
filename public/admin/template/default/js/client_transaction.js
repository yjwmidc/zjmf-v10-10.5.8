/* 用户管理-交易流水 */
(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("transaction")[0];
    Vue.prototype.lang = window.lang;
    Vue.prototype.moment = moment;
    const host = location.origin;
    const fir = location.pathname.split("/")[1];
    const str = `${host}/${fir}`;
    new Vue({
      components: {
        comConfig,
        comChooseUser,
      },
      data() {
        return {
          baseUrl: str,
          data: [],
          tableLayout: false,
          bordered: true,
          visible: false,
          delVisible: false,
          flowModel: false,
          hover: true,
          page_total_amount: 0,
          total_amount: 0,
          columns: [
            {
              colKey: "id",
              title: "ID",
              width: 125,
              sortType: "all",
              sorter: true,
            },
            {
              colKey: "amount",
              title: lang.money,
              width: 125,
              ellipsis: true,
            },
            {
              colKey: "gateway",
              title: lang.pay_way,
              width: 170,
              ellipsis: true,
            },
            // {
            //   colKey: 'hosts',
            //   title: lang.product_id,
            //   width: 300,
            //   ellipsis: true
            // },
            {
              colKey: "transaction_number",
              title: lang.flow_number,
              width: 300,
              ellipsis: true,
            },
            {
              colKey: "create_time",
              title: lang.trade_time,
              width: 180,
              ellipsis: true,
            },
            {
              colKey: "transaction_notes",
              title: lang.notes,
              width: 180,
              ellipsis: true,
            },
            {
              colKey: "op",
              title: lang.operation,
              width: 100,
              fixed: "right",
              ellipsis: true,
            },
          ],
          params: {
            keywords: "",
            client_id: "",
            page: 1,
            limit: 10,
            orderby: "id",
            sort: "desc",
          },
          id: "",
          total: 0,
          pageSizeOptions: [10, 20, 50, 100],
          loading: false,
          delId: "",
          currency_prefix: "",
          // 新增流水表单
          formData: {
            amount: "",
            gateway: "",
            transaction_number: "",
            client_id: "",
          },
          client_name: "",
          client_id: "",
          addLoading: false,
          rules: {
            amount: [
              {
                required: true,
                message: lang.input + lang.money,
                type: "error",
              },
              {
                pattern: /^-?\d+(\.\d{0,2})?$/,
                message: lang.verify10,
                type: "warning",
              },
              {
                validator: (val) => val != 0,
                message: lang.verify10,
                type: "warning",
              },
            ],
            gateway: [
              {
                required: true,
                message: lang.select + lang.pay_way,
                type: "error",
              },
            ],
            transaction_number: [
              {
                pattern: /^[A-Za-z0-9]+$/,
                message: lang.verify9,
                type: "warning",
              },
            ],
          },
          payList: [],
          maxHeight: "",
          clinetParams: {
            page: 1,
            limit: 20,
            orderby: "id",
            sort: "desc",
          },
          clientList: [], // 用户列表
          popupProps: {
            overlayInnerStyle: (trigger) => ({
              width: `${trigger.offsetWidth}px`,
            }),
          },
          optType: "add",
          optTitle: "",
          hasTicket: false,
          hasNewTicket: false,
          hasRecommend: false,
          authList: JSON.parse(
            JSON.stringify(localStorage.getItem("backAuth"))
          ),
          clientDetail: {},
          searchLoading: false,
        };
      },
      mounted() {
        this.maxHeight = document.getElementById("content").clientHeight - 240;
        let timer = null;
        window.onresize = () => {
          if (timer) {
            return;
          }
          timer = setTimeout(() => {
            this.maxHeight =
              document.getElementById("content").clientHeight - 240;
            clearTimeout(timer);
            timer = null;
          }, 300);
        };
        this.getPlugin();
        document.title =
          lang.flow + "-" + localStorage.getItem("back_website_name");
      },
      computed: {
        calcShow() {
          return (data) => {
            return (
              `#${data.id}-` +
              (data.username
                ? data.username
                : data.phone
                ? data.phone
                : data.email) +
              (data.company ? `(${data.company})` : "")
            );
          };
        },
        isExist() {
          return !this.clientList.find(
            (item) => item.id === this.clientDetail.id
          );
        },
      },
      methods: {
        // 远程搜素
        remoteMethod(key) {
          this.clinetParams.keywords = key;
          this.getClintList();
        },
        filterMethod(search, option) {
          return option;
        },
        async getPlugin() {
          try {
            const res = await getAddon();
            const temp = res.data.data.list.reduce((all, cur) => {
              all.push(cur.name);
              return all;
            }, []);
            this.hasTicket = temp.includes("IdcsmartTicket");
            this.hasNewTicket = temp.includes("TicketPremium");
            this.hasRecommend = temp.includes("IdcsmartRecommend");
          } catch (error) {}
        },

        changeUser(id) {
          this.id = id;
          location.href = `client_transaction.htm?id=${this.id}`;
        },
        // 获取用户详情
        async getUserDetail() {
          try {
            const res = await getClientDetail(this.id);
            this.clientDetail = res.data.data.client;
            const temp = res.data.data.client;
            this.client_name = temp.username;
            this.client_id = temp.id;
          } catch (error) {}
        },
        async getClintList() {
          try {
            this.searchLoading = true;
            const res = await getClientList(this.clinetParams);
            this.clientList = res.data.data.list;
            this.clientTotal = res.data.data.count;
            this.searchLoading = false;
          } catch (error) {
            this.searchLoading = false;
            console.log(error.data.msg);
          }
        },
        changePage(e) {
          this.params.page = e.current;
          this.params.limit = e.pageSize;
          this.getClientList();
        },
        // 获取流水数据
        async getClientList() {
          try {
            this.loading = true;
            this.params.client_id = this.id;
            const res = await getClientOrder(this.params);
            this.data = res.data.data.list;
            this.total = res.data.data.count;
            this.page_total_amount = res.data.data.page_total_amount;
            this.total_amount = res.data.data.total_amount;
            this.loading = false;
            // this.client_name = this.data[0].client_name || ''
            // this.client_id = this.data[0].client_id || ''
          } catch (error) {
            this.loading = false;
          }
        },
        // 新增流水
        addFlow() {
          this.flowModel = true;
          this.formData.amount = "";
          this.formData.gateway = this.payList[0].name;
          this.formData.transaction_number = "";
          this.optTitle = lang.new_flow;
          this.optType = "add";
          // this.$refs.form.reset()
        },
        updateFlow(row) {
          this.flowModel = true;
          this.optTitle = lang.update_flow;
          this.optType = "update";
          this.formData = JSON.parse(JSON.stringify(row));
          this.formData.gateway = this.payList.filter(
            (item) => item.title === row.gateway
          )[0]?.name;
        },
        async onSubmit({validateResult, firstError}) {
          if (validateResult === true) {
            try {
              this.addLoading = true;
              this.formData.client_id = this.client_id;
              await addAndUpdateFlow(this.optType, this.formData)
                .then((res) => {
                  this.$message.success(res.data.msg);
                })
                .finally(() => {
                  this.addLoading = false;
                  this.flowModel = false;
                  this.getClientList();
                });
            } catch (error) {
              this.$message.error(error.data.msg);
            }
          } else {
            console.log("Errors: ", validateResult);
            this.$message.warning(firstError);
          }
        },
        // 获取支付方式
        async getPayway() {
          try {
            const res = await getPayList();
            this.payList = res.data.data.list;
          } catch (error) {
            this.$message.error(error.data.msg);
          }
        },
        // 删除流水
        async sureDelUser() {
          try {
            this.addLoading = true;
            const res = await deleteFlow(this.delId);
            this.$message.success(res.data.msg);
            this.delVisible = false;
            this.getClientList();
            this.addLoading = false;
          } catch (error) {
            this.addLoading = false;
            this.$message.error(error.data.msg);
          }
        },
        delteFlow(row) {
          this.delVisible = true;
          this.delId = row.id;
        },
        // 排序
        sortChange(val) {
          if (!val) {
            this.params.orderby = "id";
            this.params.sort = "desc";
          } else {
            this.params.orderby = val.sortBy;
            this.params.sort = val.descending ? "desc" : "asc";
          }
          this.getClientList();
        },
      },
      created() {
        this.id = this.params.client_id =
          location.href.split("?")[1].split("=")[1] * 1;
        this.getClientList();
        this.getPayway();
        //  this.getClintList();
        this.getUserDetail();
        this.currency_prefix =
          JSON.parse(localStorage.getItem("common_set")).currency_prefix || "¥";
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
