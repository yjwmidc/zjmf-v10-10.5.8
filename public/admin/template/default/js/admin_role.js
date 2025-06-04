(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("admin-role")[0];
    Vue.prototype.lang = window.lang;
    new Vue({
      components: {
        comConfig,
      },
      data() {
        let checkPwd2 = (val) => {
          if (val !== this.formData.password) {
            return {
              result: false,
              message: window.lang.password_tip,
              type: "error",
            };
          }
          return {result: true};
        };
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
              colKey: "id",
              title: "ID",
              width: 120,
              sortType: "all",
              sorter: true,
            },
            {
              colKey: "name",
              title: lang.group_name,
              width: 200,
              ellipsis: true,
            },
            {
              colKey: "admins",
              title: lang.group_user,
              width: 500,
              ellipsis: true,
            },
            {
              colKey: "description",
              title: lang.group_tip,
              width: 300,
              ellipsis: true,
            },
            {
              colKey: "op",
              title: lang.operation,
              width: 120,
              fixed: "right",
              ellipsis: true,
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
            id: "",
            name: "",
            description: "",
            admins: "",
            auth: [],
            auth_widget: [],
          },
          rules: {
            name: [
              {
                required: true,
                message: lang.input + lang.small_group_name,
                type: "error",
              },
              {
                validator: (val) => val.length <= 50,
                message: lang.verify3 + 50,
                type: "warning",
              },
            ],
            description: [
              {
                validator: (val) => val.length <= 1000,
                message: lang.verify3 + 1000,
                type: "warning",
              },
            ],
            password: [
              {
                required: true,
                message: lang.input + lang.password,
                type: "error",
              },
            ],
            repassword: [
              {
                required: true,
                message: lang.input + lang.surePassword,
                type: "error",
              },
              {validator: checkPwd2, trigger: "blur"},
            ],
            email: [
              {
                required: true,
                message: lang.input + lang.email,
                trigger: "blur",
              },
            ],
            nickname: [
              {
                required: true,
                message: lang.input + lang.nickname,
                trigger: "blur",
              },
            ],
            role_id: [
              {
                required: true,
                message: lang.input + lang.group,
                trigger: "blur",
              },
            ],
          },
          loading: false,
          country: [],
          delId: "",
          curStatus: 1,
          statusTip: "",
          addTip: "",
          langList: [],
          roleTotal: 0,
          roleList: [],
          optType: "create",
          curId: "",
          authArr: [], // 功能权限集合
          widgetAuthArr: [], // 功能权限集合
          checkExpand: false, //功能权限展开
          checkAll: false, //功能权全选
          widgetCheckExpand: false, //挂件权限展开
          widgetCheckAll: false, //挂件权全选
          expandArr: [],
          widgetExpandArr: [],
          allAuthId: [],
          arr: [],
          widgetArr: [],
          isExpand: false,
          submitLoading: false,
        };
      },
      methods: {
        // 切换分页
        changePage(e) {
          this.params.page = e.current;
          this.params.limit = e.pageSize;
          this.getRoleList();
        },
        // 排序
        sortChange(val) {
          if (val === undefined) {
            this.params.orderby = "id";
            this.params.sort = "desc";
          } else {
            this.params.orderby = val.sortBy;
            this.params.sort = val.descending ? "desc" : "asc";
          }
          this.getRoleList();
        },
        clearKey() {
          this.params.keywords = "";
          this.search();
        },
        search() {
          this.getRoleList();
        },
        close() {
          this.visible = false;
          this.$nextTick(() => {
            this.expandArr = [];
            this.$refs.userDialog && this.$refs.userDialog.reset();
          });
        },
        // 添加管理员
        addUser() {
          this.optType = "create";
          this.formData.id = "";
          this.formData.name = "";
          this.formData.description = "";
          this.formData.admins = "";
          this.formData.auth = [];
          this.visible = true;
          this.checkExpand = false;
          this.checkAll = false;
          this.addTip = window.lang.add + window.lang.group;
        },
        async onSubmit({validateResult, firstError}) {
          if (validateResult === true) {
            try {
              // 提交的时候获取半选状态的节点id
              const pId = this.$refs.tree
                .getItems()
                .filter((item) => item.indeterminate)
                .reduce((all, cur) => {
                  all.push(cur.value);
                  return all;
                }, []);
              const params = JSON.parse(JSON.stringify(this.formData));
              params.auth.push(...pId);
              params.auth.sort((a, b) => a - b);
              this.submitLoading = true;
              // 筛选挂件权限
              const _temp = JSON.parse(JSON.stringify(params.auth));
              params.auth = params.auth.filter(
                (item) => typeof item === "number"
              );
              params.auth_widget = _temp.filter(
                (item) => typeof item === "string"
              );
              const res = await createAdminRole(this.optType, params);
              this.$message.success(res.data.msg);
              this.getRoleList();
              this.visible = false;
              this.$refs.userDialog.clearValidate();
              this.submitLoading = false;
            } catch (error) {
              this.submitLoading = false;
              this.$message.error(error.data.msg);
            }
          } else {
            console.log("Errors: ", validateResult);
            this.$message.warning(firstError);
          }
        },
        // 编辑管理员
        updateAdmin(row) {
          this.visible = true;
          this.optType = "update";
          this.formData.id = row.id;
          this.formData.name = row.name;
          this.formData.description = row.description;
          this.checkAll = this.formData.auth.length === this.arr ? true : false;
          this.addTip = lang.update + lang.group;
          // this.$refs.userDialog.reset()
          if (row.id === 1) {
            this.formData.auth = this.arr;
            this.formData.auth_widget = this.widgetArr;
            return;
          }
          this.getGroupDetail(row.id);
        },
        // 获取分组详情
        async getGroupDetail(id) {
          try {
            const res = await getAdminRoleDetail(id);
            const temp = res.data.data.admin_role;
            // 处理掉半选节点的id
            this.formData.auth = [];
            this.formData.auth_widget = [];
            temp.auth.push(...temp.auth_widget);
            temp.auth.forEach((i) => {
              let node = this.$refs.tree.getItem(i);
              // node.isLeaf：判断当前节点是否为子节点
              if (node?.isLeaf()) {
                //如果是子节点，就把状态设置成选中
                this.formData.auth.push(node.value);
              } else {
              }
            });
            this.formData.id = temp.id;
            this.formData.name = temp.name;
            this.formData.description = temp.description;
          } catch (error) {
            console.log("error", error);
          }
        },

        // 全选/全不选
        chooseAll() {
          if (this.checkAll) {
            this.formData.auth = this.arr;
          } else {
            this.formData.auth = [];
          }
        },
        chooseAllWidget() {
          if (this.widgetCheckAll) {
            this.formData.auth_widget = this.widgetArr;
          } else {
            this.formData.auth_widget = [];
          }
        },
        // 选中节点时，不全选的时候会丢失父节点id
        changeCheck(_, node) {
          // console.log('@@@', this.$refs.tree.getItems())
          /* const curId = node.node.value
          // 获取当前节点的全部父节点
          const pId = this.$refs.tree.getParents(curId).reduce((all, cur) => {
            all.push(cur.value)
            return all
          }, [])
           // 获取当前节点的全部子节点
          const childId = this.$refs.tree.getItems(curId).reduce((all, cur) => {
            all.push(cur.value)
            return all
          }, [])
          const temp = [].concat(pId, childId)
          if (_.includes(curId)) {
            this.formData.auth = Array.from(new Set(this.formData.auth.concat(temp)))
          //  console.log(this.formData.auth)
          } else {
            const parArr = this.$refs.tree.getParent(curId)?.getChildren().reduce((all, cur) => {
              all.push(cur.value)
              return all
            }, [])
            // 取消的时候，当子级没有元素的时候去掉父级节点
            if (parArr?.every(item => !this.formData.auth.includes(item))) {
              this.formData.auth = this.formData.auth.filter(item => item !== this.$refs.tree.getParent(curId).value)
            }
            this.formData.auth = this.formData.auth.filter(item => !childId.includes(item))
            // console.log(this.formData.auth)
          }

          event.stopPropagation()
          if (this.formData.auth.length === this.arr.length) {
            this.checkAll = true
          } else {
            this.checkAll = false
          }
          this.isExpand = false */
        },
        // 展开/折叠
        expandAll() {
          this.expandArr = [];
          const {tree} = this.$refs;
          tree.getItems().forEach((item) => {
            this.checkExpand ? this.expandArr.push(item.value) : [];
          });
        },
        expandAllWidget() {
          this.widgetExpandArr = [];
          const {widgetTree} = this.$refs;
          widgetTree.getItems().forEach((item) => {
            this.widgetCheckExpand ? this.widgetExpandArr.push(item.value) : [];
          });
        },
        // 节点点击的时候
        clickNode(e) {
          if (!e.node.expanded) {
            this.expandArr.push(e.node.value);
          } else {
            this.expandArr.splice(this.expandArr.indexOf(e.node.value), 1);
          }
        },
        // 挂件节点点击的时候
        clickNodeWidget(e) {
          if (!e.node.expanded) {
            this.widgetExpandArr.push(e.node.value);
          } else {
            this.widgetExpandArr.splice(
              this.widgetExpandArr.indexOf(e.node.value),
              1
            );
          }
        },
        // 删除分组
        deleteUser(row) {
          if (row.id === 1) {
            return;
          }
          this.delVisible = true;
          this.delId = row.id;
        },
        async sureDel() {
          try {
            this.submitLoading = true;
            const res = await deleteAdminRole(this.delId);
            this.$message.success(res.data.msg);
            this.params.page =
              this.data.length > 1 ? this.params.page : this.params.page - 1;
            this.delVisible = false;
            this.getRoleList();
            this.submitLoading = false;
          } catch (error) {
            this.submitLoading = false;
            this.delVisible = false;
            this.$message.error(error.data.msg);
          }
        },
        // 获取分组
        async getRoleList() {
          try {
            const res = await getAdminRole(this.params);
            const temp = res.data.data;
            this.total = temp.count;
            this.data = temp.list;
          } catch (error) {
            this.$message.error(error.data.msg);
          }
        },
        getIdFun(list) {
          list.map((item) => {
            if (item.child) {
              this.getIdFun(item.child);
            }
            this.arr.push(item.id);
          });
        },
        getIdFunWidget(list) {
          list.map((item) => {
            if (item.child) {
              this.getIdFunWidget(item.child);
            }
            this.widgetArr.push(item.id);
          });
        },
        // 获取权限
        async getAuthList() {
          try {
            const res = await getAllAuthRole();
            let tempAuth = res.data.data.list;
            tempAuth = tempAuth.map((item) => {
              if (item.id === 1) {
                item.child = res.data.data.widget;
              }
              return item;
            });
            const temp = [
              {
                id: "all",
                title: lang.home_widget,
                child: res.data.data.widget,
              },
            ];
            this.widgetAuthArr = temp;
            // 递归获取所有权限的id
            this.getIdFun(res.data.data.list);
            this.getIdFunWidget(res.data.data.widget);
            this.authArr = tempAuth;
          } catch (error) {}
        },
      },
      created() {
        this.getRoleList();
        this.getAuthList();
        document.title =
          lang.group_setting + "-" + localStorage.getItem("back_website_name");
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
