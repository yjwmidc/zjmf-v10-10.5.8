(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("log-notice-email")[0];
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
          hasNewTicket: false,
          hasRecommend: false,
          hover: true,
          columns: [
            {
              colKey: "id",
              title: "ID",
              width: 120,
              sortType: "all",
              sorter: true,
            },
            {
              colKey: "subject",
              title: lang.title,
              width: 500,
              ellipsis: true,
            },
            {
              colKey: "to",
              title: lang.email,
              width: 500,
              ellipsis: true,
            },
            {
              colKey: "create_time",
              title: lang.time,
              width: 200,
              ellipsis: true,
            },
            // {
            //   colKey: 'user_name',
            //   title: lang.receiver,
            //   width: 200,
            //   ellipsis: true
            // }
          ],
          params: {
            client_id: "",
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
          messageVisable: false,
          messagePop: "",
          emailTitle: "",
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
          authList: JSON.parse(
            JSON.stringify(localStorage.getItem("backAuth"))
          ),
          clientDetail: {},
          searchLoading: false,
        };
      },
      created() {
        const query = location.href.split("?")[1].split("&");
        this.id = this.params.client_id = Number(this.getQuery(query[0]));
        this.getNoticeEmail();
        // this.getClintList();
        this.getPlugin();
        this.getUserDetail();
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
          lang.notice_log + "-" + localStorage.getItem("back_website_name");
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
          location.href = `client_notice_email.htm?id=${this.id}`;
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
        getQuery(val) {
          return val.split("=")[1];
        },
        jump() {
          location.href = `client_notice_sms.htm?id=${this.params.client_id}`;
        },
        changePage(e) {
          this.params.page = e.current;
          this.params.limit = e.pageSize;
          this.getNoticeEmail();
        },
        async getNoticeEmail() {
          try {
            this.loading = true;
            const res = await getEmailLog(this.params);
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
          this.getNoticeEmail();
        },
        clearKey() {
          this.params.keywords = "";
          this.search();
        },
        search() {
          this.getNoticeEmail();
        },
        // 显示邮件详情
        showMessage(row) {
          this.messageVisable = true;
          this.emailTitle = row.subject;
          this.messagePop = row.message;
        },
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
