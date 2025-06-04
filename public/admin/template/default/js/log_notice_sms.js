(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("log-notice-sms")[0];
    Vue.prototype.lang = window.lang;
    Vue.prototype.moment = window.moment;
    new Vue({
      components: {
        comConfig,
      },
      data() {
        return {
          data: [],
          tableLayout: false,
          bordered: true,
          visible: false,
          delVisible: false,
          hover: true,
          hasExport: false,
          tableLayout: false,
          columns: [
            {
              colKey: "id",
              title: "ID",
              width: 80,
              sortType: "all",
              sorter: true,
            },
            {
              colKey: "content",
              title: lang.content,
              ellipsis: true,
              minWidth: 300,
              className: "notice-width",
            },
            {
              colKey: "create_time",
              title: lang.time,
              width: 200,
            },
            {
              colKey: "user_name",
              title: lang.receiver,
              width: 150,
              ellipsis: true,
            },
            {
              colKey: "phone",
              title: lang.phone,
              width: 200,
              ellipsis: true,
            },
          ],
          params: {
            keywords: "",
            page: 1,
            limit: 10,
            orderby: "id",
            sort: "desc",
            start_time: "",
            end_time: "",
          },
          id: "",
          total: 0,
          pageSizeOptions: [10, 20, 50, 100],
          range: [],
          loading: false,
          title: "",
          delId: "",
          maxHeight: "",
          exportVisible: false,
          exportLoading: false,
        };
      },
      mounted() {
        this.maxHeight = document.getElementById("content").clientHeight - 220;
        let timer = null;
        window.onresize = () => {
          if (timer) {
            return;
          }
          timer = setTimeout(() => {
            this.maxHeight =
              document.getElementById("content").clientHeight - 220;
            clearTimeout(timer);
            timer = null;
          }, 300);
        };
      },
      methods: {
        async getPlugin() {
          try {
            const res = await getAddon();
            this.addonArr = res.data.data.list.map((item) => item.name);
            this.hasExport = this.addonArr.includes("IdcsmartExportExcel");
          } catch (error) {}
        },
        openExportDia() {
          this.range = [];
          this.params.start_time = "";
          this.params.end_time = "";
          this.exportVisible = true;
        },
        handelDownload() {
          if (this.range.length === 0) {
            this.$message.error(lang.data_export_tip);
            return;
          }
          const params = JSON.parse(JSON.stringify(this.params));
          if (this.range.length > 0) {
            params.start_time =
              new Date(this.range[0].replace(/-/g, "/")).getTime() / 1000 || "";
            params.end_time =
              new Date(this.range[1].replace(/-/g, "/")).getTime() / 1000 || "";
          } else {
            params.start_time = "";
            params.end_time = "";
          }
          if ((params.end_time - params.start_time) / (3600 * 24) > 31) {
            this.$message.error(lang.export_range_tips);
            return;
          }
          this.exportLoading = true;
          apiExportSmslog(params)
            .then((res) => {
              exportExcelFun(res).finally(() => {
                this.exportLoading = false;
                this.exportVisible = false;
              });
            })
            .catch((err) => {
              this.exportLoading = false;
              this.$message.error(err.data.msg);
            });
        },
        jump() {
          location.href = "log_notice_email.htm";
        },
        changePage(e) {
          this.params.page = e.current;
          this.params.limit = e.pageSize;
          this.getClientList();
        },
        async getClientList() {
          try {
            this.loading = true;
            const res = await getSmsLog(this.params);
            this.data = res.data.data.list;
            this.total = res.data.data.count;
            this.loading = false;
          } catch (error) {
            this.$message.error(error.data.msg);
            this.loading = false;
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
          this.getClientList();
        },
        clearKey() {
          this.params.keywords = "";
          this.search();
        },
        search() {
          this.getClientList();
        },
      },
      created() {
        this.getClientList();
        this.getPlugin();
        document.title =
          lang.sms_notice + "-" + localStorage.getItem("back_website_name");
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
