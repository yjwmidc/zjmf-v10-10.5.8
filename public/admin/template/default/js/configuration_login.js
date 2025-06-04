(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("configuration-login")[0];
    Vue.prototype.lang = window.lang;
    new Vue({
      components: {
        comConfig,
      },
      data() {
        return {
          submitLoading: false,
          formData: {
            login_phone_verify: "",
            register_email: "",
            login_email_password: "",
            register_phone: "",
            code_client_email_register: "",
            home_login_check_ip: "",
            admin_login_check_ip: "",
            code_client_phone_register: "",
            limit_email_suffix: "",
            email_suffix: "",
            home_login_check_common_ip: "",
            home_login_ip_exception_verify: [],
            home_enforce_safe_method: [],
            admin_enforce_safe_method: [],
            admin_enforce_safe_method_scene: [],
            admin_allow_remember_account: "",
            first_login_method: "code",
            first_password_login_method: "email",
          },
          homeVerifyList: [
            {
              label: lang.setting_text11,
              value: "operate_password",
            },
          ],
          homeSafeMethodList: [
            {
              label: lang.setting_text12,
              value: "phone",
            },
            {
              label: lang.setting_text13,
              value: "email",
            },
            {
              label: lang.setting_text14,
              value: "operate_password",
            },
            {
              label: lang.setting_text15,
              value: "certification",
            },
            {
              label: lang.setting_text16,
              value: "oauth",
            },
          ],
          adminMethodList: [
            {
              label: lang.setting_text14,
              value: "operate_password",
            },
          ],
          rules: {
            home_login_ip_exception_verify: [
              {
                required: false,
                message: lang.select + lang.setting_text9,
                type: "error",
              },
            ],
            admin_enforce_safe_method: [
              {
                required: false,
                message: lang.select + lang.setting_text19,
                type: "error",
              },
            ],
            home_enforce_safe_method: [
              {
                required: false,
                message: lang.select + lang.setting_text19,
                type: "error",
              },
            ],
          },
          isCanUpdata: sessionStorage.isCanUpdata === "true",
          hasController: true,
          adminScene: [
            {
              value: "all",
              label: lang.auth_all,
            },
            {
              value: "client_delete",
              label: lang.setting_text67,
            },
            {
              value: "update_client_status",
              label: lang.setting_text68,
            },
            {
              value: "host_operate",
              label: lang.setting_text69,
            },
            {
              value: "order_delete",
              label: lang.setting_text70,
            },
            {
              value: "clear_order_recycle",
              label: lang.setting_text71,
            },
            {
              value: "plugin_uninstall_disable",
              label: lang.setting_text72,
            },
          ],
          tabValue: "client",
        };
      },
      methods: {
        changeAdmin(val) {
          if (val.length === 0) {
            this.formData.admin_enforce_safe_method_scene = [];
          }
        },
        changeScene(val) {
          if (val.length === 0) {
            return;
          }
          const lastVal = val[val.length - 1];
          if (lastVal === "all") {
            this.formData.admin_enforce_safe_method_scene = ["all"];
          } else {
            this.formData.admin_enforce_safe_method_scene = val.filter(
              (item) => item !== "all"
            );
          }
        },
        async getActivePlugin() {
          const res = await getActiveAddon();
          this.hasController = (res.data.data.list || [])
            .map((item) => item.name)
            .includes("TemplateController");
        },
        async onSubmit({validateResult, firstError}) {
          if (validateResult === true) {
            try {
              const params = JSON.parse(JSON.stringify(this.formData));
              if (
                params.admin_enforce_safe_method.length > 0 &&
                params.admin_enforce_safe_method_scene.length === 0
              ) {
                return this.$message.error(
                  `${lang.select}${lang.setting_text73}`
                );
              }
              this.submitLoading = true;
              const res = await updateLoginOpt(params);
              this.$message.success(res.data.msg);
              this.getSetting();
              this.submitLoading = false;
            } catch (error) {
              error.data?.msg && this.$message.error(error.data.msg);
              this.submitLoading = false;
            }
          } else {
            console.log("Errors: ", validateResult);
            this.$message.warning(firstError);
          }
        },
        async getSetting() {
          try {
            const res = await getLoginOpt();
            this.formData = res.data.data;
          } catch (error) {}
        },
      },
      created() {
        this.getActivePlugin();
        this.getSetting();
        document.title =
          lang.login_setting + "-" + localStorage.getItem("back_website_name");
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
