(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("order-details")[0];
    Vue.prototype.lang = window.lang;
    Vue.prototype.moment = moment;
    const host = location.host;
    const fir = location.pathname.split("/")[1];
    const str = `${host}/${fir}/`;
    new Vue({
      components: {
        comConfig,
      },
      data() {
        return {
          id: "",
          data: [],
          baseUrl: str,
          rootRul: url,
          hasCostPlugin: false,
          tableLayout: true,
          bordered: true,
          hover: true,
          total: 0,
          pageSizeOptions: [10, 20, 50, 100],
          params: {
            order_id: "",
            page: 1,
            limit: 10,
            orderby: "id",
            sort: "desc",
          },
          loading: false,
          columns: [
            {
              colKey: "transaction_number",
              title: lang.flow_number,
              ellipsis: true,
            },
            {
              colKey: "amount",
              title: lang.money,
              ellipsis: true,
            },
            {
              colKey: "gateway",
              title: lang.pay_way,
              width: 200,
              ellipsis: true,
            },
            {
              colKey: "create_time",
              title: lang.trade_time,
              width: 200,
              ellipsis: true,
            },
          ],
          orderDetail: {},
        };
      },
      mounted() {
        this.getFlowList();
        this.getOrderDetail();
      },
      methods: {
        async getOrderDetail() {
          try {
            const res = await getOrderDetails({id: this.id});
            this.orderDetail = res.data.data.order;
          } catch (error) {}
        },
        async getAddonList() {
          try {
            const res = await getAddon();
            if (
              res.data.data.list.filter((item) => item.name === "CostPay")
                .length > 0
            ) {
              this.hasCostPlugin = true;
            }
          } catch (error) {
            this.$message.error(error.data.msg);
          }
        },
        goOrder() {
          sessionStorage.removeItem("currentOrderUrl");
          location.href = "order.htm";
        },
        goBack() {
          const url = sessionStorage.currentOrderUrl || "";
          sessionStorage.removeItem("currentOrderUrl");
          if (url) {
            location.href = url;
          } else {
            window.history.back();
          }
        },
        async getFlowList() {
          try {
            const res = await getClientOrder(this.params);
            this.data = res.data.data.list;
            this.total = res.data.data.count;
            this.loading = false;
          } catch (error) {
            this.loading = false;
            this.$message.error(res.data.msg);
          }
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
          this.getFlowList();
        },
        // 分页
        changePage(e) {
          this.params.page = e.current;
          this.params.limit = e.pageSize;
          this.getFlowList();
        },
      },
      created() {
        this.getAddonList();
        this.id = this.params.order_id = location.href
          .split("?")[1]
          .split("=")[1];
        this.currency_prefix =
          JSON.parse(localStorage.getItem("common_set")).currency_prefix || "¥";
        document.title =
          lang.flow + "-" + localStorage.getItem("back_website_name");
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
