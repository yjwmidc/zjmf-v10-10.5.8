(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("configuration-system")[0];
    Vue.prototype.lang = window.lang;
    Vue.prototype.moment = window.moment;
    const host = location.origin;
    const fir = location.pathname.split("/")[1];
    const str = `${host}/${fir}/`;
    new Vue({
      components: {
        comConfig,
      },
      data() {
        return {
          newList: [],
          isLoading: false,
          betaTipDia: false,
          columns: [
            {
              colKey: "title",
              title: lang.title,
              className: "table-row",
            },
            {
              className: "table-row",
              colKey: "create_time",
              title: lang.release_time,
            },
          ],
          total: 0,
          pagination: {
            current: 1,
            pageSize: 10,
            pageSizeOptions: [10, 20, 50, 100],
            showJumper: true,
          },
          adminArr: JSON.parse(localStorage.getItem("common_set")).lang_admin,
          homeArr: JSON.parse(localStorage.getItem("common_set")).lang_home,
          // 系统版本信息
          systemData: {},
          // 更新信息
          updateContent: {},
          isDown: false,
          updateData: {
            progress: "0.00%",
          },
          newListParams: {
            limit: 10,
            page: 1,
            parent_id: 3,
          },
          isShowProgress: false,
          timer: null,
          hasUpdate: false,
          isCanUpdata: sessionStorage.isCanUpdata === "true",
          /* 更换识别码 */
          syncVisible: false,
          updateCode: false,
          changeCode: false,
          changeForm: {
            license: "",
          },
          rules: {
            license: [
              {
                required: true,
                message: `${lang.input}${lang.upload_text14}`,
                type: "error",
              },
            ],
          },
          domain: location.host,
          submitLoading: false,
          hasController: true,
        };
      },
      methods: {
        async getActivePlugin() {
          const res = await getActiveAddon();
          this.hasController = (res.data.data.list || [])
            .map((item) => item.name)
            .includes("TemplateController");
        },
        async handlerUpdate() {
          try {
            this.updateCode = true;
            const res = await updateLicense();
            this.updateCode = false;
            this.$message.success(res.data.msg);
            this.getVersion();
          } catch (error) {
            this.updateCode = false;
            this.$message.error(error.data.msg);
          }
        },
        handlerChange() {
          this.syncVisible = true;
          this.changeForm.license = "";
          this.$refs.codeForm && this.$refs.codeForm.clearValidate();
        },
        async submitUpdate({validateResult, firstError}) {
          if (validateResult === true) {
            try {
              this.submitLoading = true;
              const res = await changeLicense(this.changeForm);
              this.$message.success(res.data.msg);
              this.submitLoading = false;
              this.syncVisible = false;
              this.getVersion();
            } catch (error) {
              this.submitLoading = false;
              this.$message.error(error.data.msg);
            }
          } else {
            console.log("Errors: ", validateResult);
            this.$message.warning(firstError);
          }
        },
        changeType() {
          this.betaTipDia = true;
        },
        changeBeta() {
          this.submitLoading = true;
          changeVersionType({
            system_version_type: this.systemData.system_version_type,
          })
            .then((res) => {
              this.submitLoading = false;
              this.$message.success(res.data.msg);
              this.getVersion();
              this.betaTipDia = false;
            })
            .catch((error) => {
              this.submitLoading = false;
              this.$message.error(error.data.msg);
            });
        },
        cancelChange() {
          this.systemData.system_version_type =
            this.systemData.system_version_type === "stable"
              ? "beta"
              : "stable";
          this.betaTipDia = false;
        },
        // 获取版本信息
        async getVersion() {
          try {
            const res = await version();
            this.systemData = res.data.data;
            this.systemData.updataTime = new Date().getTime();
            if (this.systemData.is_download == 1) {
              this.isDown = true;
            }
            // 判断版本是否可以更新
            this.hasUpdate = this.checkVersion(
              this.systemData.version,
              this.systemData.last_version
            );
            this.isCanUpdata = this.hasUpdate;
            localStorage.setItem("systemData", JSON.stringify(this.systemData));
            sessionStorage.setItem(
              "versionData",
              JSON.stringify(this.systemData)
            );
            sessionStorage.setItem("isCanUpdata", this.hasUpdate);
          } catch (error) {}
        },
        checkVersion(nowStr, lastStr) {
          const nowArr = nowStr.split(".").map(Number);
          const lastArr = lastStr.split(".").map(Number);
          for (let i = 0; i < Math.max(nowArr.length, lastArr.length); i++) {
            const num1 = nowArr[i] || 0;
            const num2 = lastArr[i] || 0;
            if (num1 === num2) continue;
            return num1 < num2;
          }
          return false;
        },
        /**
         *
         * @param {string} nowStr 当前版本
         * @param {string} lastStr 最新版本
         */
        // checkVersion(nowStr, lastStr) {
        //   const nowArr = nowStr.split(".");
        //   const lastArr = lastStr.split(".");
        //   let hasUpdate = false;
        //   const nowLength = nowArr.length;
        //   const lastLength = lastArr.length;

        //   const length = Math.min(nowLength, lastLength);
        //   for (let i = 0; i < length; i++) {
        //     if (lastArr[i] - nowArr[i] > 0) {
        //       hasUpdate = true;
        //     }
        //   }
        //   if (!hasUpdate && lastLength - nowLength > 0) {
        //     hasUpdate = true;
        //   }
        //   return hasUpdate;
        // },
        // 获取更新信息
        getUpContent() {
          upContent().then((res) => {
            if (res.data.status == 200) {
              this.updateContent = res.data.data;
              localStorage.setItem(
                "updateContent",
                JSON.stringify(this.updateContent)
              );
            }
          });
        },
        // BaseTable 中只有 page-change 事件，没有 change 事件
        onPageChange(pageInfo) {
          this.pagination.current = pageInfo.current;
          this.pagination.pageSize = pageInfo.pageSize;
          this.newListParams.limit = pageInfo.pageSize;
          this.newListParams.page = pageInfo.current;
          this.fetchData();
        },
        async fetchData() {
          try {
            this.isLoading = true;
            // 请求可能存在跨域问题
            await newsList(this.newListParams).then((res) => {
              this.newList = res.data.data.list;
              this.newList.forEach((item) => {
                item.create_time = moment(item.create_time * 1000).format(
                  "YYYY-MM-DD HH:mm"
                );
              });
              this.pagination.total = res.data.data.count;
            });
            // 数据加载完成，设置数据总条数
          } catch (err) {
            this.data = [];
          }
          this.isLoading = false;
        },
        // 跳转到升级页面
        toUpdate() {
          location.href = "/upgrade/update.html";
          // location.href = 'update.htm'
        },
        onRowClick(item) {
          // window.open(`https://www.idcsmart.com/news_cont2/${item.row.id}.html`)
          window.open(
            `https://my.idcsmart.com/plugin/21/news_detail.htm?id=${item.row.id}`
          );
        },
        onRowMouseover(item) {},
        // 开始下载
        beginDown() {
          if (this.systemData.last_version == this.systemData.version) {
            this.$message.warning(lang.invoice_text27);
            return false;
          }

          this.isShowProgress = true;
          upDown()
            .then((res) => {
              if (res.data.status === 200) {
              }
            })
            .catch((error) => {
              this.$message.warning(error.data.msg);
            });

          // 轮询下载进度
          if (this.timer) {
            clearInterval(timer);
          }
          this.timer = setInterval(() => {
            upProgress()
              .then((res) => {
                if (res.data.status === 200) {
                  this.updateData = res.data.data;
                  if (this.updateData.progress == "100.00%") {
                    clearInterval(this.timer);
                    this.isShowProgress = false;
                    this.isDown = true;
                  }
                }
              })
              .catch((error) => {
                console.log(error.data.data);
                if (error.data.data == lang.invoice_text28) {
                  this.isShowProgress = false;
                  clearInterval(this.timer);
                }
              });
          }, 2000);
        },
      },
      created() {
        this.getActivePlugin();
        this.getVersion();
        this.getUpContent();
        this.fetchData();
        document.title =
          lang.system_upgrade + "-" + localStorage.getItem("back_website_name");
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
