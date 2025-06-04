(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("log")[0];
    Vue.prototype.lang = window.lang;
    Vue.prototype.moment = window.moment;
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
          hover: true,
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
              title: lang.detail,
              width: 700,
              ellipsis: true,
              className: "log-description-width",
            },
            {
              colKey: "create_time",
              title: lang.time,
              width: 200,
              ellipsis: true,
            },
            {
              colKey: "ip",
              title: "IP" + lang.address,
              width: 100,
              ellipsis: true,
            },
            {
              colKey: "user_name",
              title: lang.operator,
              width: 100,
              ellipsis: true,
            },
          ],
          params: {
            keywords: "",
            page: 1,
            limit: 10,
            orderby: "id",
            sort: "desc",
          },
          id: "",
          total: 0,
          pageSizeOptions: [10, 20, 50, 100],
          loading: false,
          title: "",
          delId: "",
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
      mounted() {
        document.title =
          lang.log + "-" + localStorage.getItem("back_website_name");
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
        // 获取用户详情
        async getUserDetail() {
          try {
            const res = await getClientDetail(this.id);
            this.clientDetail = res.data.data.client;
          } catch (error) {}
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
          location.href = `client_log.htm?id=${this.id}`;
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
        changePage(e) {
          this.params.page = e.current;
          this.params.limit = e.pageSize;
          this.getClientList();
        },
        async getClientList() {
          try {
            this.loading = true;
            const res = await getLog(this.id, this.params);
            this.data = res.data.data.list;
            this.total = res.data.data.count;
            this.loading = false;
          } catch (error) {
            this.loading = false;
            this.$message.error(error.data.msg);
          }
        },
      },
      created() {
        this.id = location.href.split("?")[1].split("=")[1] * 1;
        this.getClientList();
        // this.getClintList()
        this.getPlugin();
        this.getUserDetail();
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
