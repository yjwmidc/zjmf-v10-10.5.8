(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("navigation")[0];
    Vue.prototype.lang = window.lang;
    Vue.prototype.moment = moment;
    new Vue({
      data() {
        return {
          submitLoading: false,
          maxHeight: 0,
          // 菜单列表
          menuList: [],
          // 系统导航列表
          systemList: [],
          // 插件导航列表
          pluginList: [],
          // 模块导航列表
          moduleList: [],
          // 上游列表
          reModuleList: [],
          // 语言
          language: [],
          // 导航类型
          menuType: [
            { id: 1, label: lang.system_page, value: "system" },
            { id: 2, label: lang.plugin, value: "plugin" },
            { id: 3, label: lang.nav_text1, value: "custom" },
            { id: 4, label: lang.nav_text2, value: "module" },
            { id: 5, label: lang.nav_text3, value: "res_module" },
          ],
          // 正在拖拽的导航数据
          draggleItem: 0,
          // 正在拖动的导航的id
          moveId: 0,
          // 目标节点是否为1级导航
          isLv1: true,
          // 目标页面选择列表
          selectList: [],
          // 前台导航loading
          homeMenuLoading: false,
          // 后台导航 loading
          adminMenuLoading: false,
          // 右侧设置框
          isShowSet: false,
          // 激活的导航id
          activeId: 0,
          // 鼠标点下时的坐标
          startXy: {
            x: 0,
            y: 0,
          },
          // 鼠标移动中的坐标
          endXy: {
            x: 0,
            y: 0,
          },
          // 导航 前后导航选择 1前台 2后台
          value: "1",
          // 导航右侧设置表单数据
          formData: {
            type: "",
            url: "",
            icon: "",
            name: "",
            module: "",
            product_id: [],
            language: {},
          },
          // 设置表单
          // setRules: {
          //     type: [
          //         { required: true, message: "页面类型不能为空", type: 'error' },
          //     ],
          //     name: [{ required: true, message: "导航名称不能为空", type: 'error' },],
          // },
          // 新增页面弹窗相关
          // 新增导航页面设置表单数据
          newFormData: {
            id: "",
            type: "",
            url: "",
            second_reminder: false,
            icon: "",
            name: "",
            nav_id: "",
            module: "",
            isChecked: false,
            product_id: [],
            language: {},
          },
          // newRules: {
          //     type: [
          //         { required: true, message: "页面类型不能为空", type: 'error' },
          //     ],
          //     name: [{ required: true, message: "导航名称不能为空", type: 'error' },],
          // },
          // 是否显示新增页面弹窗
          visible: false,
          commonLang: JSON.parse(localStorage.getItem("common_set"))
            .lang_home[0].display_lang,
          iconsData: [],
          popupVisible: false,
          backPopupVisible: false,
          newPopupVisible: false,
          newBackPopupVisible: false,
          manifest: manifest,
          productList: [],
          newProductList: [],
          newModuleSelectLoading: false,
          treeProps: {
            keys: {
              label: "name",
              value: "id",
              children: "child",
            },
          },
          treeKey: {
            label: "name",
            value: "id",
            children: "child",
          },
          isMove: false,
        };
      },
      components: {
        vuedraggable,
        comConfig,
      },
      computed: {
        calcMoudleList() {
          return (type) => {
            return type === "module" ? this.moduleList : this.reModuleList;
          };
        },
      },
      methods: {
        // 获取前台导航
        getHomeMenu() {
          this.homeMenuLoading = true;
          homeMenu()
            .then((res) => {
              if (res.data.status === 200) {
                const menu = res.data.data.menu;
                menu.map((item) => {
                  if (item.type == "system(command)") {
                    item.type = "system";
                  }

                  if (!item.child) {
                    // 若没有child 则将子导航拖进去会失效
                    item.child = [];
                  } else {
                    item.child.map((n) => {
                      if (!n.child) {
                        n.child = [];
                      }
                      if (n.type == "system(command)") {
                        n.type = "system";
                      }
                    });
                  }
                });
                // 前台导航
                this.menuList = menu;
                // 系统默认导航
                this.systemList = res.data.data.system_nav;
                // 插件默认导航
                this.pluginList = res.data.data.plugin_nav;
                // 模块默认导航
                this.moduleList = res.data.data.module;
                // 上游模块默认导航
                this.reModuleList = res.data.data.res_module;
                // 语言列表
                this.language = res.data.data.language;
                this.language.forEach((item) => {
                  this.formData[item.display_lang] = "";
                  this.newFormData[item.display_lang] = "";
                });
                this.homeMenuLoading = false;
              }
            })
            .catch((error) => {
              this.$message.error(error.data.msg);
              this.homeMenuLoading = false;
            });
        },
        // 获取后台导航
        getAdminMenu() {
          this.adminMenuLoading = true;
          adminMenu()
            .then((res) => {
              if (res.data.status === 200) {
                const menu = res.data.data.menu;

                menu.map((item) => {
                  if (item.type == "system(command)") {
                    item.type = "system";
                  }

                  if (!item.child) {
                    // 若没有child 则将子导航拖进去会失效
                    item.child = [];
                  } else {
                    // 若二级导航没有 child 则其变成一级导航时不能为其添加子导航
                    item.child.map((n) => {
                      if (!n.child) {
                        n.child = [];
                      }
                      if (n.type == "system(command)") {
                        n.type = "system";
                      }
                    });
                  }
                });
                // 后台导航
                this.menuList = menu;
                // 系统默认导航
                const systemList = res.data.data.system_nav;
                let num = 0;
                systemList.map((item) => {
                  if (!item.url) {
                    item.url = "menu" + num;
                    num += 1;
                  }
                });
                this.systemList = systemList;
                // 插件默认导航
                const pluginList = res.data.data.plugin_nav;
                pluginList.map((item) => {
                  item.navs.map((n) => {
                    if (!n.url) {
                      n.url = "menu" + num;
                      num += 1;
                    }
                  });
                });
                this.pluginList = pluginList;
                // 语言列表
                this.language = res.data.data.language;
                this.language.forEach((item) => {
                  this.formData[item.display_lang] = "";
                  this.newFormData[item.display_lang] = "";
                });
                this.adminMenuLoading = false;
              }
            })
            .catch((error) => {
              this.$message.error(error.data.msg);
              this.adminMenuLoading = false;
            });
        },
        // 前后台导航切换
        menuChange(value) {
          this.popupVisible = false;
          this.backPopupVisible = false;
          this.newPopupVisible = false;
          (this.newBackPopupVisible = false),
            // 隐藏右侧设置页面
            (this.isShowSet = false);
          this.menuList = [];
          if (this.value == 1) {
            // 获取前台导航
            this.getHomeMenu();
            this.menuType = [
              { id: 1, label: lang.system_page, value: "system" },
              { id: 2, label: lang.plugin, value: "plugin" },
              { id: 3, label: lang.nav_text1, value: "custom" },
              { id: 4, label: lang.nav_text2, value: "module" },
            ];
          }
          if (this.value == 2) {
            // 获取后台导航
            this.getAdminMenu();
            this.menuType = [
              { id: 1, label: lang.system_page, value: "system" },
              { id: 2, label: lang.plugin, value: "plugin" },
              { id: 3, label: lang.nav_text1, value: "custom" },
            ];
          }
        },
        onDragSort() {},
        onStart() {},
        // 二级导航中的拖拽事件
        lv2OnMove(e) {
          this.isMove = true;
          let isOne = false;
          if (e.relatedContext.element !== undefined) {
            const id = e.relatedContext.element.id;
            this.menuList.forEach((item) => {
              if (item.id === id) {
                isOne = true;
              }
            });
            this.isLv1 = isOne;
          } else {
            this.isLv1 = false;
          }
        },
        // 一级导航的拖拽中事件
        onMove(e, e1) {
          this.isMove = true;
          let isOne = false;
          if (e.relatedContext.element !== undefined) {
            const id = e.relatedContext.element.id;
            this.menuList.forEach((item) => {
              if (item.id === id) {
                isOne = true;
              }
            });
            this.isLv1 = isOne;
          } else {
            this.isLv1 = false;
          }

          // 拖拽中的一级节点存在子节点不允许 成为二级节点
          if (
            e.draggedContext.element.child &&
            e.draggedContext.element.child.length > 0
          ) {
            if (!isOne) {
              return false;
            }
          }

          return true;
        },
        // 鼠标左键按下
        getMouseDown(e, item) {
          this.startXy = {
            x: e.clientX,
            y: e.clientY,
          };
          this.draggleItem = item;
          this.moveId = item.id;
        },
        // 鼠标移动
        getMouseMove(e) {
          this.endXy = {
            x: e.clientX,
            y: e.clientY,
          };
        },
        // 松开鼠标左键与 vue.draggleable 的拖拽结束事件冲突 这里用拖拽结束事件
        // 拖拽结束
        onEnd() {
          this.isMove = false;
          this.moveId = 0;
          // y轴上拖动的距离
          let y = this.endXy.y - this.startXy.y;
          // x轴上拖动的距离
          let x = this.endXy.x - this.startXy.x;
          if (-10 < y && y < 10) {
            // 判断endXy 和 startXy的位置
            if (x > 10) {
              // 有子导航不的话不能变成二级导航
              // 没有自导航的话 变成上一个一级导航的二级导航
              if (this.draggleItem.child && this.draggleItem.child.length > 0) {
                this.$message.warning(lang.nav_text4);
              } else {
                let isLevel2 = true;
                // 判断是否是二级导航
                this.menuList.forEach((item) => {
                  if (item.id === this.draggleItem.id) {
                    isLevel2 = false;
                  }
                });
                if (isLevel2) {
                  this.$message.warning(lang.nav_text5);
                } else {
                  // 一级导航，查找其上一个导航 插入到child中
                  let index = this.menuList.findIndex(
                    (item) => item.id === this.draggleItem.id
                  );
                  // 不为数组第一个元素
                  if (index != 0) {
                    this.menuList[index - 1].child.push(this.draggleItem);
                    this.menuList = this.menuList.filter((item) => {
                      return item.id !== this.draggleItem.id;
                    });
                  }
                }
              }
            }
            if (x < -10) {
              // 判断是否为一级导航
              let isLevel1 = false;
              this.menuList.forEach((item) => {
                if (item.id === this.draggleItem.id) {
                  isLevel1 = true;
                }
              });

              if (isLevel1) {
                this.$message.warning(lang.nav_text6);
              } else {
                // 1.查找该二级导航的一级导航的id 并清除该二级导航

                let pId = 0;
                for (let i = 0; i < this.menuList.length; i++) {
                  if (
                    this.menuList[i].child &&
                    this.menuList[i].child.length > 0
                  ) {
                    this.menuList[i].child = this.menuList[i].child.filter(
                      (n) => {
                        if (n.id === this.draggleItem.id) {
                          pId = this.menuList[i].id;
                        }
                        return n.id !== this.draggleItem.id;
                      }
                    );
                  }
                }
                // 查找父导航的下标
                let index = this.menuList.findIndex((item) => item.id === pId);
                // 插入父导航之前
                this.menuList.splice(index, 0, this.draggleItem);
              }
            }
          }
        },
        // 应用导航点击事件
        subMenu() {
          const temp = JSON.parse(JSON.stringify(this.menuList)).map((item) => {
            if (item.type === "custom") {
              item.second_reminder = item.second_reminder ? 1 : 0;
            }
            return item;
          });
          const params = {
            menu: temp,
          };
          this.submitLoading = true;
          if (this.value == 1) {
            // 保存前台的导航
            saveHomeMenu(params)
              .then((res) => {
                if ((res.data.status = 200)) {
                  this.$message.success(res.data.msg);
                }
              })
              .catch((error) => {
                this.$message.error(error.data.msg);
              })
              .finally(() => {
                this.submitLoading = false;
              });
          }
          if (this.value == 2) {
            // 保存后台的导航
            saveAdminMenu(params)
              .then((res) => {
                if (res.data.status === 200) {
                  this.$message.success(res.data.msg);
                  // 调用获取后台导航，保存到locastorage 并刷新页面
                  leftMenu().then((res) => {
                    localStorage.setItem(
                      "backMenus",
                      JSON.stringify(res.data.data.menu)
                    );
                    location.reload();
                  });
                }
              })
              .catch((error) => {
                this.$message.error(error.data.msg);
              })
              .finally(() => {
                this.submitLoading = false;
              });
          }
        },
        // 导航点击事件
        itemClick(item) {
          this.moveId = 0;
          this.activeId = item.id;

          item.second_reminder =
            item.second_reminder === 1 || item.second_reminder === true
              ? true
              : false;
          // 判断是否有子导航
          if (JSON.stringify(item.language) != "{}") {
            item.isChecked = true;
          }

          if (item.product_id && !item.product_id.length) {
            this.formData = { ...item, product_id: [] };
          } else {
            this.formData = { ...item };
          }

          // 判断页面类型 给目标页面选择框赋值
          // 系统页面
          if (this.formData.type === "system") {
            this.selectList = this.systemList;
          }
          // 插件
          if (this.formData.type === "plugin") {
            this.selectList = this.pluginList;
          }
          // 模块
          if (this.formData.module) {
            this.getProduct(this.formData.module);
          }

          this.isShowSet = true;

          // // 判断页面类型 给目标页面选择框赋值
          // // 系统页面
          // if (this.formData.type === 'system') {
          //     this.selectList = this.systemList
          // }
          // // 插件
          // if (this.formData.type === 'plugin') {
          //     this.selectList = this.pluginList
          // }
          // // 模块
          // if (this.formData.module) {
          //     this.getProduct(this.formData.module)
          // }
        },
        // 弹窗相关
        // 取消按钮点击事件
        close() {
          this.visible = false;
          this.popupVisible = false;
          this.backPopupVisible = false;
          this.newPopupVisible = false;
          this.newBackPopupVisible = false;
        },
        // 右侧设置页面 保存按钮点击事件
        saveSet() {
          if (this.formData.type == "custom") {
            if (!this.formData.url) {
              this.$message.warning(lang.nav_text7);
              return false;
            }
          } else if (
            this.formData.type != "module" &&
            this.formData.type != "res_module"
          ) {
            // if (this.value == 1) {
            if (!this.formData.nav_id) {
              this.$message.warning(lang.nav_text8);
              return false;
            }
          }

          if (this.formData.type == "module") {
            if (!this.formData.module) {
              this.$message.warning(lang.nav_text9);
              return false;
            }
          }

          if (!this.formData.name) {
            this.$message.warning(lang.nav_text10);
            return false;
          }

          const id = this.formData.id;
          a: for (let i = 0; i < this.menuList.length; i++) {
            if (this.menuList[i].id === id) {
              if (!this.formData.isChecked) {
                // this.formData.delete
                this.formData.language = {};
              }
              this.menuList[i] = this.formData;
              break a;
            } else {
              if (this.menuList[i].child && this.menuList[i].child.length > 0) {
                for (let j = 0; j < this.menuList[i].child.length; j++) {
                  if (this.menuList[i].child[j].id === id) {
                    if (!this.formData.isChecked) {
                      // this.formData.delete
                      this.formData.language = {};
                    }
                    this.menuList[i].child[j] = this.formData;
                    break a;
                  }
                }
              }
            }
          }
          this.popupVisible = false;
          this.backPopupVisible = false;
          this.newPopupVisible = false;
          this.newBackPopupVisible = false;
          // this.isShowSet = false
          // this.$message.success("保存成功")
        },
        // 右侧设置页面 删除按钮点击事件
        delNav() {
          // 弹框提醒
          const confirmDia = this.$dialog.confirm({
            header: lang.nav_text11,
            confirmBtn: lang.nav_text12,
            cancelBtn: lang.nav_text13,
            onConfirm: ({ e }) => {
              // 导航id
              const id = this.formData.id;
              this.menuList = this.menuList.filter((item) => {
                if (item.child && item.child.length > 0) {
                  item.child = item.child.filter((n) => {
                    return n.id !== id;
                  });
                }
                return item.id !== id;
              });
              this.isShowSet = false;
              // 请求成功后，销毁弹框
              confirmDia.destroy();
            },
            onClose: ({ e, trigger }) => {
              confirmDia.hide();
            },
          });
        },
        // 右侧设置页面 页面类型选择框改变时
        typeChange() {
          // 根据选择的类型 给目标类型选择框赋值
          // 系统
          if (this.formData.type === "system") {
            this.selectList = [...this.systemList];
          }
          // 插件
          if (this.formData.type === "plugin") {
            this.selectList = [...this.pluginList];
          }
          // 模块
          if (this.formData.type === "module") {
            this.selectList = [...this.moduleList];
            this.formData.module = "";
          }
          // 上游模块
          if (this.formData.type === "res_module") {
            this.selectList = [...this.reModuleList];
            this.formData.module = "";
          }
          this.formData.url = "";

          this.formData.url = "";
          this.formData.icon = "";
          this.formData.name = "";
          this.formData.nav_id = "";
          if (this.formData.product_id.length !== 0) {
            this.formData.product_id = [];
          }

          this.saveSet();
        },
        // 新建页面弹窗 页面类型选择框改变时
        newTypeChange() {
          if (this.newFormData.type === "system") {
            this.selectList = [...this.systemList];
          }
          // 插件
          if (this.newFormData.type === "plugin") {
            this.newFormData.nav_id = "";
            this.selectList = [...this.pluginList];
          }

          this.newFormData.url = "";
          this.newFormData.second_reminder = false;
          this.newFormData.icon = "";
          this.newFormData.name = "";
          this.newFormData.nav_id = "";
        },
        // 新增页面弹窗保存按钮点击事件
        confirmNewMenu() {
          this.popupVisible = false;
          this.backPopupVisible = false;
          this.newPopupVisible = false;
          this.newBackPopupVisible = false;

          if (!this.newFormData.name) {
            this.$message.warning(lang.nav_text10);
            return false;
          }

          if (this.newFormData.type == "custom") {
            if (!this.newFormData.url) {
              this.$message.warning(lang.nav_text14);
              return false;
            }
          }

          if (this.newFormData.type == "module") {
            if (!this.newFormData.module) {
              this.$message.warning(lang.nav_text9);
              return false;
            }
          }

          if (
            this.newFormData.type == "system" ||
            this.newFormData.type == "plugin"
          ) {
            if (!this.newFormData.nav_id) {
              this.$message.warning(lang.nav_text8);
              return false;
            }
          }

          // 判断是否是 自定义页面
          if (this.newFormData.type !== "custom") {
            // 不是是自定义页面
            // 通过 目标页面和 页面类型获取nav_id
            this.selectList.forEach((item) => {
              if (item.url === this.newFormData.url) {
                this.newFormData.nav_id = item.id;
              }
            });
          }
          let id = 0;
          this.menuList.forEach((item) => {
            id += Number(item.id);
          });
          // 给一个唯一id
          this.submitLoading = true;
          this.newFormData.id = id;
          let newPage = {
            ...JSON.parse(JSON.stringify(this.newFormData)),
            child: [],
          };
          this.menuList.push(newPage);
          this.visible = false;
          this.submitLoading = false;
          this.itemClick(newPage);
        },
        // confirmNewMenu({ validateResult, firstError }) {
        //     if (validateResult === true) {
        //         // 判断是否是 自定义页面
        //         if (this.newFormData.type !== 'custom') {
        //             // 不是是自定义页面
        //             // 通过 目标页面和 页面类型获取nav_id
        //             this.selectList.forEach(item => {
        //                 if (item.url === this.newFormData.url) {
        //                     this.newFormData.nav_id = item.id
        //                 }
        //             })
        //         }
        //         let id = 0
        //         this.menuList.forEach(item => {
        //             id += Number(item.id)
        //         })
        //         // 给一个唯一id
        //         this.newFormData.id = id
        //         const newPage = { ...this.newFormData, child: [] }
        //         this.menuList.push(newPage)
        //         this.visible = false
        //     } else {
        //         this.$message.warning(firstError);
        //     }

        // },
        // 点击新建页面按钮
        showNewMenuDialog() {
          this.visible = true;
          this.isShowSet = false;
          this.newFormData = {
            id: "",
            type: "system",
            url: "",
            second_reminder: false,
            icon: "",
            name: "",
            nav_id: "",
            module: "",
            isChecked: false,
            product_id: [],
            language: {},
          };
          this.selectList = [...this.systemList];
        },
        // 右侧设置目标页面选择框改变
        urlSelectChange() {
          // const url = this.formData.url
          // // 页面类型为 系统页面
          // if (this.formData.type == 'system') {
          //   this.selectList.forEach(item => {
          //     if (item.url === url) {
          //       this.formData.nav_id = item.id
          //     }
          //   })
          // }
          // // 页面类型为插件
          // if (this.formData.type == 'plugin') {
          //   this.selectList.forEach(list => {
          //     list.navs.forEach(item => {
          //       if (item.url === url) {
          //         this.formData.nav_id = item.id
          //       }
          //     })
          //   })
          // }

          this.$nextTick(() => {
            this.saveSet();
          });
        },
        // 新增页面 目标页面选择框改变
        newUrlSelectChange() {
          const url = this.newFormData.url;
          // 页面类型为 系统页面
          if (this.newFormData.type == "system") {
            this.selectList.forEach((item) => {
              if (item.url === url) {
                this.newFormData.nav_id = item.id;
              }
            });
          }
          // 页面类型为插件
          if (this.newFormData.type == "plugin") {
            this.newFormData.nav_id = "";
            this.selectList.forEach((list) => {
              list.navs.forEach((item) => {
                if (item.url === url) {
                  this.newFormData.nav_id = item.id;
                }
              });
            });
          }
        },
        // 展示所有icon图标
        showIconList() {
          this.popupVisible = true;
        },
        getAllIcon() {
          let url = "/upload/common/iconfont/iconfont.json";
          let _this = this;

          // 申明一个XMLHttpRequest
          let request = new XMLHttpRequest();
          // 设置请求方法与路径
          request.open("get", url);
          // 不发送数据到服务器
          request.send(null);
          //XHR对象获取到返回信息后执行
          request.onload = function () {
            // 解析获取到的数据
            let data = JSON.parse(request.responseText);
            _this.iconsData = data.glyphs;
            _this.iconsData.map((item) => {
              item.font_class = "icon-" + item.font_class;
            });
          };
        },
        // 通过模块获取商品列表
        getProduct(module) {
          this.newModuleSelectLoading = true;
          const params = {
            module,
          };
          const type = this.visible
            ? this.newFormData.type
            : this.formData.type;
          productBymodule(type, params)
            .then((res) => {
              if (res.data.status === 200) {
                this.productList = res.data.data.list;
                this.changeId(this.productList);
              }
              this.newModuleSelectLoading = false;
            })
            .catch((err) => {
              this.newModuleSelectLoading = false;
            });
        },
        // 将关联页面的id改变
        changeId(list) {
          list.map((item) => {
            if (item.child && item.child.length > 0) {
              item.id = item.id + "-" + item.name;
              this.changeId(item.child);
            }
          });
        },
        newModuleChange() {
          const module = this.newFormData.module;
          this.newFormData = {
            ...this.newFormData,
            product_id: [],
          };
          // this.newFormData.product_id = []
          this.getProduct(module);
        },
        moduleChange() {
          const module = this.formData.module;
          this.formData.product_id = [];
          this.getProduct(module);

          this.saveSet();
        },
        urlInputChange() {
          this.saveSet();
        },
        nameInputChange() {
          this.saveSet();
        },
        iconClick(item) {
          this.formData.icon = item.font_class;
          this.saveSet();
        },
        adminIconClick(item) {
          this.formData.icon = item.stem;
          this.saveSet();
        },
        // 2-1 新增保存信息
        changeCheck() {
          this.saveSet();
        },
        changeLanguage() {
          this.saveSet();
        },
      },
      created() {
        this.getAllIcon();
        if (
          !this.$checkPermission(
            "auth_system_configuration_menu_home_menu_view"
          )
        ) {
          this.value = "2";
          this.getAdminMenu();
        } else {
          this.value = "1";
          // 默认拉取前台菜单
          this.getHomeMenu();
        }
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
