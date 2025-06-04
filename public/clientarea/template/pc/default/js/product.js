(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("product")[0];
    Vue.prototype.lang = window.lang;
    new Vue({
      components: {
        asideMenu,
        topMenu,
        pagination,
      },
      created() {
        this.analysisUrl();
        this.getCommonData();
        this.getList();
      },
      mounted() { },
      updated() {
        // // 关闭loading
        document.getElementById("mainLoading").style.display = "none";
        document.getElementsByClassName("product")[0].style.display = "block";
      },
      destroyed() { },
      data() {
        return {
          id: "",
          params: {
            page: 1,
            limit: 20,
            pageSizes: [20, 50, 100],
            total: 200,
            orderby: "id",
            sort: "desc",
            keywords: "",
          },
          commonData: {},
          content: "",
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
        analysisUrl () {
          let url = window.location.href;
          let getqyinfo = url.split("?")[1];
          let getqys = new URLSearchParams("?" + getqyinfo);
          this.id = getqys.get("m");
        },
        async getList() {
          try {
            const res = await getProduct(this.id);
            this.$nextTick(() => {
              // 解决Jquery加载JS会在文件末尾添加时间戳的问题
              $.ajaxSetup({
                cache: true
              })
              $(".config-box .content").html(res.data.data.content);
            });
            this.content = res.data.data.content;
          } catch (error) { }
        },
        // 每页展示数改变
        sizeChange(e) {
          this.params.limit = e;
          this.params.page = 1;
          // 获取列表
        },
        // 当前页改变
        currentChange(e) {
          this.params.page = e;
        },

        // 获取通用配置
        getCommonData() {
          this.commonData = JSON.parse(
            localStorage.getItem("common_set_before")
          );
        },
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
