(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("task")[0];
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
          urlPath: url,
          columns: [
            {
              colKey: "id",
              title: "ID",
              width: 120,
              sortType: "all",
              sorter: true,
            },
            {
              colKey: "description",
              title: lang.task_description,
              ellipsis: true,
              className: "name-status",
            },
            {
              colKey: "start_time",
              title: lang.start_time,
              width: 170,
            },
            {
              colKey: "finish_time",
              title: lang.end_time,
              width: 170,
            },
            // {
            //   colKey: 'status',
            //   title: lang.task_status,
            //   width: 120,
            //   ellipsis: true
            // },
            {
              colKey: "retry",
              title: lang.operation,
              width: 120,
              fixed: "right",
            },
          ],
          params: {
            keywords: "",
            status: "",
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
          formData: {
            status: "",
            keywords: "",
          },
          statusOpt: [
            {value: "Wait", label: lang.Wait},
            {value: "Exec", label: lang.Exec},
            {value: "Finish", label: lang.Finish},
            {value: "Failed", label: lang.failed},
          ],
          range: [],
        };
      },
      methods: {
        // 搜索
        reset() {
          this.formData.status = "";
          this.formData.keywords = "";
          this.range = [];
        },
        onSubmit() {
          Object.assign(this.params, this.formData);
          this.params.page = 1;
          if (this.range.length > 0) {
            this.params.start_time =
              new Date(this.range[0].replace(/-/g, "/")).getTime() / 1000 || "";
            this.params.end_time =
              new Date(this.range[1].replace(/-/g, "/")).getTime() / 1000 || "";
          } else {
            this.params.start_time = "";
            this.params.end_time = "";
          }
          this.getTaskList();
        },
        // 重试
        async retryFun(id) {
          try {
            const res = await reloadTask(id);
            this.$message.success(res.data.msg);
            this.getTaskList();
          } catch (error) {
            this.$message.error(error.data.msg);
          }
        },
        changePage(e) {
          this.params.page = e.current;
          this.params.limit = e.pageSize;
          this.getTaskList();
        },
        async getTaskList() {
          try {
            this.loading = true;
            const res = await getTask(this.params);
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
          this.getTaskList();
        },
      },
      created() {
        this.getTaskList();
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
