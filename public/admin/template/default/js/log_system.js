(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("log-system")[0];
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
          exportVisible: false,
          exportLoading: false,
          hasExport: false,
          tableLayout: false,
          range: [],
          columns: [
            {
              colKey: "id",
              title: "ID",
              width: 100,
              sortType: "all",
              sorter: true,
            },
            {
              colKey: "description",
              title: lang.description,
              minWidth: 300,
              ellipsis: true,
              className: "log-description-width",
            },
            {
              colKey: "create_time",
              title: lang.time,
              width: 200,
            },
            {
              colKey: "ip",
              title: "IP" + lang.address,
              width: 200,
              ellipsis: true,
            },
            {
              colKey: "user_name",
              title: lang.operator,
              width: 150,
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
          loading: false,
          title: "",
          delId: "",
          maxHeight: "",
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
      computed: {
        calStr() {
          return (str) => {
            const temp =
              str &&
              str
                .replace(/&lt;/g, "<")
                .replace(/&gt;/g, ">")
                .replace(/&quot;/g, '"')
                .replace(/&amp;lt;/g, "<")
                .replace(/&amp;gt;/g, ">")
                .replace(/ &amp;lt;/g, "<")
                .replace(/&amp;gt; /g, ">")
                .replace(/&amp;gt; /g, ">")
                .replace(/&amp;quot;/g, '"')
                .replace(/&amp;amp;nbsp;/g, " ")
                .replace(/&amp;#039;/g, "'");
            return temp;
          };
        },
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
          apiExportSystemlog(params)
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
        changePage(e) {
          this.params.page = e.current;
          this.params.limit = e.pageSize;
          this.getClientList();
        },
        async getClientList() {
          try {
            this.loading = true;
            const res = await getSystemLog(this.params);
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
          this.params.page = 1;
          this.getClientList();
        },
      },
      created() {
        this.getClientList();
        this.getPlugin();
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
