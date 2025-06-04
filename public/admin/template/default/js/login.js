function captchaCheckSuccsss(bol, captcha, token, login) {
  vm.captchaBol = bol;
  vm.formData.captcha = captcha;
  vm.formData.token = token;
  vm.direct_login = login;
}
(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const login = document.getElementById("login");
    Vue.prototype.lang = window.lang;
    if (localStorage.getItem("backJwt")) {
      const host = location.origin;
      const fir = location.pathname.split("/")[1];
      const str = `${host}/${fir}/`;
      location.href = str;
      return;
    }
    const vm = new Vue({
      data() {
        return {
          check: false,
          type: "password",
          loading: false,
          formData: {
            name: "",
            password: "",
            remember_password: 0,
            token: "",
            captcha: "",
          },
          captcha: "",
          rules: {
            name: [
              {
                required: true,
                message: lang.input + lang.acount,
                type: "error",
              },
            ],
            password: [
              {
                required: true,
                message: lang.input + lang.password,
                type: "error",
              },
            ],
            captcha: [{ required: true, message: lang.captcha, type: "error" }],
          },
          captcha_admin_login: 0, // 登录是否需要验证码
          website_name: "",
          direct_login: false, // 是否验证通过直接登录
          admin_allow_remember_account: 0,
        };
      },
      created() {
        this.getLoginInfo();

        if (!localStorage.getItem("lang")) {
          localStorage.setItem("lang", "zh-cn");
        }
      },
      watch: {
        captcha_admin_login(val) {
          if (val == 1) {
            this.getCaptcha();
          }
        },
        direct_login(bol) {
          if (bol) {
            this.submitLogin();
          }
        },
      },
      methods: {
        async getCaptcha() {
          try {
            const res = await getCaptcha();
            const temp = res.data.data.html;
            $("#admin-captcha").html(temp);
          } catch (error) {}
        },
        getSetPassword() {},
        async getLoginInfo() {
          try {
            const res = await getLoginInfo();
            this.captcha_admin_login = res.data.data.captcha_admin_login;
            this.admin_allow_remember_account =
              res.data.data.admin_allow_remember_account;
            if (res.data.data.admin_allow_remember_account == 1) {
              this.formData = {
                name: localStorage.getItem("name") || "",
                password: localStorage.getItem("password") || "",
                remember_password: 0,
                token: localStorage.getItem("backToken") || "",
                captcha: localStorage.getItem("backCaptcha") || "",
              };
              this.check = true;
            }
            localStorage.setItem(
              "back_website_name",
              res.data.data.website_name
            );
            localStorage.setItem("backLang", res.data.data.lang_admin);
            document.title = lang.login + "-" + res.data.data.website_name;
          } catch (error) {}
        },
        // 发起登录
        async submitLogin() {
          try {
            this.loading = true;
            this.formData.remember_password = this.check === true ? 1 : 0;
            const params = { ...this.formData };
            if (!this.captcha_admin_login) {
              delete params.token;
              delete params.captcha;
            }
            const res = await logIn(params);
            localStorage.setItem("backJwt", res.data.data.jwt);
            // 记住账号
            if (this.formData.remember_password) {
              localStorage.setItem("name", this.formData.name);
              localStorage.setItem("password", this.formData.password);
            } else {
              // 未勾选记住
              localStorage.removeItem("name");
              localStorage.removeItem("password");
            }
            localStorage.setItem("userName", this.formData.name);
            await this.getCommonSetting();
            // 获取权限
            const auth = await getAuthRole();
            const authTemp = auth.data.data.auth;
            // const authList = auth.data.data.list;
            localStorage.setItem("backAuth", JSON.stringify(authTemp));
            //  localStorage.setItem("authList", JSON.stringify(authList));
            // 获取导航
            const menus = await getMenus();
            const menulist = menus.data.data.menu;
            localStorage.setItem(
              "backMenus",
              JSON.stringify(menus.data.data.menu)
            );
            let login_url = menus.data.data.url;
            let menu_id = menus.data.data.menu_id;
            sessionStorage.clear();
            if (menulist.length === 0) {
              return (location.href = "");
            }
            if (login_url === "") {
              if (!menulist[0].child) {
                login_url = menulist[0].url;
                menu_id = menulist[0].id;
              } else {
                login_url = menulist[0].child[0].url;
                menu_id = menulist[0].child[0].id;
              }
            }
            localStorage.setItem("curValue", menu_id);
            this.$message.success(res.data.msg);
            this.loading = false;
            location.href = login_url;
          } catch (error) {
            this.captcha_admin_login == 1 && this.getCaptcha();
            this.$message.error(error.data.msg);
            this.loading = false;
          }
        },
        // 提交按钮
        async onSubmit({ validateResult, firstError }) {
          if (validateResult === true) {
            // 开启验证码的时候
            if (this.captcha_admin_login === "1" && !this.captchaBol) {
              this.$message.warning(lang.input + lang.correct_code);
              return;
            }
            this.submitLogin();
          } else {
            console.log("Errors: ", validateResult);
            this.$message.warning(firstError);
          }
        },
        // 获取通用配置
        async getCommonSetting() {
          try {
            const res = await getCommon();
            localStorage.setItem("common_set", JSON.stringify(res.data.data));
          } catch (error) {}
        },
      },
    }).$mount(login);
    window.vm = vm;
    typeof old_onload == "function" && old_onload();
  };
})(window);
