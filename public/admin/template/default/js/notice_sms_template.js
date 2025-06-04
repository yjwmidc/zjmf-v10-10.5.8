(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("notice-sms-template")[0];
    Vue.prototype.lang = window.lang;
    new Vue({
      components: {
        comConfig,
        comSendParams,
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
          urlPath: url,
          isEn: localStorage.getItem("backLang") === "en-us" ? true : false,
          columns: [
            {
              colKey: "id",
              title: "ID",
              width: 80,
            },
            {
              colKey: "template_id",
              title: `${lang.template}ID`,
              width: 170,
            },
            {
              colKey: "type",
              title: lang.type,
              width: 100,
            },
            {
              colKey: "title",
              title: `${lang.template}${lang.title}`,
              width: 250,
              ellipsis: true,
            },
            {
              colKey: "content",
              title: `${lang.template}${lang.content}`,
              ellipsis: true,
            },
            {
              colKey: "status",
              title: lang.status,
              width: 120,
            },
            {
              colKey: "op",
              title: lang.manage,
              width: 120,
              fixed: "right",
            },
          ],
          hideSortTips: true,
          params: {
            keywords: "",
            page: 1,
            limit: 15,
            orderby: "id",
            sort: "desc",
          },
          total: 0,
          pageSizeOptions: [20, 50, 100],
          formData: {
            // 创建模板
            id: "",
            name: "",
            template_id: "",
            type: "0",
            title: "",
            content: "",
            notes: "",
            status: "",
            product_url: "",
            remark: "",
          },
          testForm: {
            name: "",
            id: "",
            phone_code: 86,
            phone: "",
          },
          rules: {
            template_id: [
              {
                pattern: /^[A-Za-z0-9-_]{0,100}$/,
                message: `${lang.verify21}，${lang.verify3}100`,
                type: "warning",
              },
            ],
            phone: [
              {
                required: true,
                message: `${lang.input}${lang.phone}`,
                type: "error",
              },
            ],
            title: [
              {
                required: true,
                message: `${lang.input}${lang.title}`,
                type: "error",
              },
              {
                validator: (val) => val.length <= 50,
                message: `${lang.verify3}50`,
                type: "warning",
              },
            ],
            content: [
              {
                required: true,
                message: `${lang.input}${lang.content}`,
                type: "error",
              },
              {
                validator: (val) => val.length <= 255,
                message: `${lang.verify3}255`,
                type: "warning",
              },
            ],
            notes: [
              {
                validator: (val) => val.length <= 1000,
                message: `${lang.verify3}1000`,
                type: "warning",
              },
            ],
            status: [
              {
                required: true,
                message: `${lang.select}${lang.template}${lang.status}`,
                type: "error",
              },
            ],
            product_url: [
              {
                required: true,
                message: `${lang.input}${lang.app_scene}`,
                type: "error",
              },
            ],
            remark: [
              {
                required: true,
                message: `${lang.input}${lang.scene_des}`,
                type: "error",
              },
            ],
          },
          country: [],
          loading: false,
          delId: "",
          curStatus: 1,
          statusTip: "",
          addTip: "",
          installTip: "",
          optType: "",
          maxHeight: "",
          delOrSubmit: "",
          delOrSubmitTitle: "",
          name: "", // 插件标识
          type: "", // 安装/卸载
          module: "sms", // 当前模块
          isChina: true, // 是否国内用于短信测试
          submitLoading: false,
        };
      },
      created() {
        this.formData.name = this.name = location.href
          .split("?")[1]
          .split("=")[1];
        this.getSmsList();
        this.getCountry();
        this.getSmsTemplateStatus();
        document.title =
          lang.sms_notice +
          "-" +
          lang.template_manage +
          "-" +
          localStorage.getItem("back_website_name");
      },
      methods: {
        // 获取短信接口状态
        async getSmsTemplateStatus() {
          const res = await getSmsTemplateStatus(this.formData.name);
          if (res.data.status === 200) {
            this.getSmsList();
          }
        },
        // 测试接口
        testHandler(row) {
          this.isChina = row.type === 0 ? true : false;
          this.testForm.name = row.sms_name;
          this.testForm.id = row.id;
          this.statusVisble = true;
        },
        async testSubmit() {
          try {
            this.submitLoading = true;
            const res = await testSmsTemplate(this.testForm);
            this.$message.success(res.data.msg);
            this.statusVisble = false;
            this.submitLoading = false;
          } catch (error) {
            this.submitLoading = false;
            this.$message.error(error.data.msg);
          }
        },
        closeTest() {
          this.statusVisble = false;
          this.testForm.phone = "";
        },
        // 获取国家列表
        async getCountry() {
          try {
            const res = await getCountry();
            this.country = res.data.data.list;
          } catch (error) {}
        },
        back() {
          location.href = "notice_sms.htm";
        },
        // 获取列表
        async getSmsList() {
          try {
            this.loading = true;
            const res = await getSmsTemplate(this.name);
            this.loading = false;
            this.data = res.data.data.list;
            this.total = res.data.data.count;
          } catch (error) {
            this.loading = false;
          }
        },
        // 排序
        sortChange(val) {
          if (!val) {
            return;
          }
          this.params.orderby = val.sortBy;
          this.params.sort = val.descending ? "desc" : "asc";
          this.getSmsList();
        },
        clearKey() {
          this.params.keywords = "";
          this.search();
        },
        search() {
          this.getSmsList();
        },
        close() {
          this.visible = false;
          this.formData.type = "0";
          this.formData.template_id = "";
          this.formData.status = "";
          this.$nextTick(() => {
            this.$refs.createTemp && this.$refs.createTemp.clearValidate();
            this.$refs.createTemp && this.$refs.createTemp.reset();
          });
        },
        // 创建模板
        createTemplate() {
          this.visible = true;
          this.formData.type = "0";
          this.formData.id = "";
          this.formData.name = this.name;
          this.formData.template_id = "";
          this.formData.title = "";
          this.formData.content = "";
          this.formData.notes = "";
          this.formData.status = "";
          this.formData.product_url = "";
          this.formData.remark = "";
          this.optType = "create";
          this.addTip = window.lang.create_template;
        },
        async onSubmit({ validateResult, firstError }) {
          if (validateResult === true) {
            try {
              this.submitLoading = true;
              const res = await createTemplate(this.optType, this.formData);
              this.$message.success(res.data.msg);
              this.getSmsList();
              this.visible = false;
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
        // 编辑
        async updateHandler(row) {
          try {
            if (row.status === 1) {
              return;
            }
            this.optType = "edit";
            this.addTip = window.lang.edit_template;
            const params = {
              name: this.name,
              id: row.id,
            };
            const res = await getSmsTemplateDetail(params);
            const temp = res.data.data;
            this.formData.id = row.id;
            this.formData.type = String(temp.type);
            this.formData.status = String(temp.status);
            this.formData.template_id = temp.template_id;
            this.formData.title = temp.title;
            this.formData.content = temp.content;
            this.formData.notes = temp.notes;
            this.formData.product_url = temp.product_url;
            this.formData.remark = temp.remark;
            this.visible = true;
          } catch (error) {}
        },
        deleteHandler(row) {
          this.delVisible = true;
          this.delId = row.id;
          this.delOrSubmitTitle = lang.sureDelete;
          this.delOrSubmit = "delete";
        },
        // 删除/批量提交确认按钮
        sureHandler() {
          if (this.delOrSubmit === "delete") {
            this.sureDel();
          } else if (this.delOrSubmit === "batch") {
            this.batchSubmitVerify();
          }
        },
        // 删除
        async sureDel() {
          try {
            const params = {
              name: this.name,
              id: this.delId,
            };
            this.submitLoading = true;
            const res = await deleteSmsTemplate(params);
            this.$message.success(res.data.msg);
            this.delVisible = false;
            this.getSmsList();
            this.submitLoading = false;
          } catch (error) {
            this.submitLoading = false;
            this.$message.error(error.data.msg);
          }
        },
        batchSubmit() {
          this.delOrSubmit = "batch";
          this.delVisible = true;
          this.delOrSubmitTitle = lang.sure_batch_submit;
        },
        // 批量提交审核
        async batchSubmitVerify() {
          try {
            const ids = this.data.reduce((all, cur) => {
              all.push(cur.id);
              return all;
            }, []);
            const params = {
              name: this.name,
              ids,
            };
            this.submitLoading = true;
            const res = await batchSubmitById(params);
            this.$message.success(lang.submit_success);
            this.delVisible = false;
            this.updateStatus();
            this.submitLoading = false;
          } catch (error) {
            this.submitLoading = false;
            this.$message.error(error.data.msg);
          }
        },
        // 更新审核状态
        async updateStatus() {
          try {
            await updateTemplateStatus(this.name);
          } catch (error) {
            this.$message.error(error.data.msg);
          }
        },
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
