(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("gateway")[0];
    Vue.prototype.lang = window.lang;
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
          statusVisble: false,
          hover: true,
          columns: [
            {
              colKey: "drag", // 列拖拽排序必要参数
              title: lang.sort,
              cell: "drag",
              width: 40,
            },
            {
              colKey: "id",
              title: "ID",
              width: 120,
              sortType: "all",
              sorter: true,
            },
            {
              colKey: "title",
              title: lang.interface_name,
              width: 200,
              ellipsis: true,
            },
            {
              colKey: "author",
              title: lang.author,
              width: 200,
              ellipsis: true,
            },
            {
              colKey: "version",
              title: lang.version,
              width: 100,
              ellipsis: true,
              className: "version",
            },
            {
              colKey: "status",
              title: lang.status,
              width: 120,
              ellipsis: true,
            },
            {
              colKey: "op",
              title: lang.operation,
              width: 160,
              fixed: "right",
            },
          ],
          hideSortTips: true,
          params: {
            keywords: "",
            page: 1,
            limit: 10,
            orderby: "id",
            sort: "desc",
          },
          total: 0,
          pageSizeOptions: [10, 20, 50, 100],
          formData: {
            // 添加用户
            username: "",
            email: "",
            phone_code: "",
            phone: "",
            password: "",
            repassword: "",
          },
          rules: {
            username: [{required: true, message: lang.required, type: "error"}],
            password: [{required: true, message: lang.required, type: "error"}],
          },
          loading: false,
          country: [],
          delId: "",
          curStatus: 1,
          statusTip: "",
          installTip: "",
          type: "",
          configVisble: false,
          configTip: "",
          configData: [],
          maxHeight: "",
          urlPath: url,
          module: "gateway", // 当前模块
          upVisible: false,
          curName: "",
          upLoading: false,
          submitLoading: false,
          description_url: "",
        };
      },
      methods: {
        // 获取列表
        async getGatewayList() {
          try {
            this.loading = true;
            const params = {...this.params};
            params.module = "gateway";
            const res = await getMoudle(params);
            this.loading = false;
            this.data = res.data.data.list;
            this.total = res.data.data.count;
            // 获取最新版本
            this.getNewVersion();
          } catch (error) {
            this.loading = false;
          }
        },
        /* 升级 start */
        // 获取最新版本
        async getNewVersion() {
          try {
            const res = await getActiveVersion();
            const temp = res.data.data.list.filter(
              (item) => item.type === this.module
            );
            const arr = temp.reduce((all, cur) => {
              all.push(cur.uuid);
              return all;
            }, []);
            if (arr.length > 0) {
              this.data = this.data.map((item) => {
                item.isUpdate = false;
                if (arr.includes(item.name)) {
                  const cur = temp.filter((el) => el.uuid === item.name)[0];
                  item.isUpdate = this.checkVersion(
                    cur?.old_version,
                    cur?.version
                  );
                }
                return item;
              });
            }
          } catch (error) {}
        },
        /**
         *
         * @param {string} nowStr 当前版本
         * @param {string} lastStr 最新版本
         */
        // 对比版本，是否显示升级
        checkVersion(nowStr, lastStr) {
          const nowArr = nowStr.split(".");
          const lastArr = lastStr.split(".");
          let hasUpdate = false;
          const nowLength = nowArr.length;
          const lastLength = lastArr.length;

          const length = Math.min(nowLength, lastLength);
          for (let i = 0; i < length; i++) {
            if (lastArr[i] - nowArr[i] > 0) {
              hasUpdate = true;
            }
          }
          if (!hasUpdate && lastLength - nowLength > 0) {
            hasUpdate = true;
          }
          return hasUpdate;
        },
        updatePlugin(row) {
          this.upVisible = true;
          this.curName = row.name;
        },
        // 提交升级
        async sureUpgrade() {
          try {
            this.upLoading = true;
            const res = await upgradePlugin({
              module: this.module,
              name: this.curName,
            });
            this.$message.success(res.data.msg);
            this.upVisible = false;
            this.upLoading = false;
            this.getGatewayList();
          } catch (error) {
            this.upLoading = false;
            this.upVisible = false;
            this.$message.error(error.data.msg);
          }
        },
        /* 升级 end */
        addUser() {
          // window.open('https://market.idcsmart.com/shop')
          setToken().then((res) => {
            if (res.data.status == 200) {
              let url = res.data.market_url;
              let getqyinfo = url.split("?")[1];
              let getqys = new URLSearchParams("?" + getqyinfo);
              const from = getqys.get("from");
              const token = getqys.get("token");
              window.open(
                `https://my.idcsmart.com/shop/shop_app.html?from=${from}&token=${token}&appType=gateway`
              );
            }
          });
        },
        // 配置
        handleConfig(row) {
          this.configVisble = true;
          this.description_url = row.description_url || "";
          this.delId = row.name;
          this.getConfig(row.id);
        },
        async getConfig(id) {
          try {
            const params = {
              module: "gateway",
              name: this.delId,
              id,
            };
            const res = await getMoudleConfig(params);
            this.configData = res.data.data.plugin.config;
            this.configTip = res.data.data.plugin.title;
            this.configVisble = true;
          } catch (error) {}
        },
        onDragSort({newData}) {
          this.data = newData;
          this.changefileOrder();
        },
        // 修改排序
        changefileOrder() {
          this.loading = true;
          const idList = this.data
            .filter((item) => item.id)
            .map((item) => item.id);
          apiGatewaySort({
            id: idList,
          })
            .then((res) => {
              this.loading = false;
              this.$message.success(res.data.msg);
            })
            .catch((err) => {
              this.loading = false;
              this.$message.error(err.data.msg);
            });
        },
        // 保存配置
        async onSubmit() {
          try {
            const params = {
              module: "gateway",
              name: this.delId,
              config: {},
            };
            for (const i in this.configData) {
              params.config[this.configData[i].field] =
                this.configData[i].value;
            }
            this.submitLoading = true;
            const res = await saveMoudleConfig(params);
            this.$message.success(res.data.msg);
            this.configVisble = false;
            this.getGatewayList();
            this.submitLoading = false;
          } catch (error) {
            this.submitLoading = false;
            this.$message.error(error.data.msg);
          }
        },
        // 切换分页
        changePage(e) {
          this.params.page = e.current;
          this.params.limit = e.pageSize;
          this.getGatewayList();
        },
        // 排序
        clearKey() {
          this.params.keywords = "";
          this.search();
        },
        search() {
          this.getGatewayList();
        },
        close() {
          this.visible = false;
          this.$refs.userDialog.reset();
        },

        // 停用/启用
        changeStatus(row) {
          this.delId = row.name;
          this.curStatus = row.status;
          this.statusTip = this.curStatus ? lang.sureDisable : lang.sure_Open;
          this.statusVisble = true;
        },
        async sureChange() {
          try {
            let tempStatus = this.curStatus === 1 ? 0 : 1;
            const params = {
              module: "gateway",
              name: this.delId,
              status: tempStatus,
            };
            this.submitLoading = true;
            const res = await changeMoudle(params);
            this.$message.success(res.data.msg);
            this.statusVisble = false;
            this.getGatewayList();
            this.submitLoading = false;
          } catch (error) {
            this.submitLoading = false;
            this.$message.error(res.data.msg);
            this.statusVisble = false;
          }
        },
        closeDialog() {
          this.statusVisble = false;
        },
        // 删除
        deletePay(row) {
          this.delVisible = true;
          this.delId = row.name;
          this.type = row.status === 3 ? "install" : "uninstall";
          this.installTip =
            row.status === 3
              ? window.lang.sureInstall
              : window.lang.sureUninstall;
        },
        async sureDel() {
          try {
            const params = {
              module: "gateway",
              name: this.delId,
            };
            this.submitLoading = true;
            const res = await deleteMoudle(this.type, params);
            this.$message.success(res.data.msg);
            this.params.page =
              this.data.length > 1 ? this.params.page : this.params.page - 1;
            this.delVisible = false;
            this.getGatewayList();
            this.submitLoading = false;
          } catch (error) {
            this.submitLoading = false;
            this.delVisible = false;
            this.$message.error(error.data.msg);
          }
        },
        cancelDel() {
          this.delVisible = false;
        },
      },
      created() {
        this.getGatewayList();
      },
      computed: {
        computedRadio() {
          return function (val) {
            const opt = [];
            Object.keys(val).forEach((key, index) => {
              opt[index] = {
                value: key,
              };
            });
            Object.values(val).forEach((value, index) => {
              opt[index].label = value;
            });
            return opt;
          };
        },
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
