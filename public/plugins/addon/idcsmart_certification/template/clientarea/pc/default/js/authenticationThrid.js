(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("template")[0];
    Vue.prototype.lang = Object.assign(window.lang, window.plugin_lang);
    new Vue({
      components: {
        asideMenu,
        topMenu,
      },
      created() {
        this.rzType = location.href.split("?")[1].split("=")[1];
        this.getCommonData();
      },
      mounted() {
        this.getCertificationAuth();
      },
      updated() {
        // 关闭loading
        document.getElementById("mainLoading").style.display = "none";
        document.getElementsByClassName("template")[0].style.display = "block";
      },
      destroyed() {
        clearInterval(this.timer1);
        this.timer1 = null;
      },
      data() {
        return {
          commonData: {},
          timer1: null,
          contentBox: null,
          rzType: "", // 1 个人   2 企业
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
        // 返回按钮
        backTicket() {
          location.href = "/account.htm";
        },
        goSelect() {
          history.back();
        },
        // 获取基础信息
        getCertificationInfo() {
          certificationInfo().then((res) => {
            this.certificationInfoObj = res.data.data;
          });
        },
        // 获取状态
        grtCertificationStatus() {
          certificationStatus().then((res) => {
            if (res.data.status === 400) {
              clearInterval(this.timer1);
              this.timer1 = null;
              location.href = "authentication_select.htm";
            }
            if (res.data.status === 200) {
              if (!(res.data.data.code == 2 && res.data.data.refresh == 0)) {
                if(res.data.data.code == 1){
                  clearInterval(this.timer1);
                  this.timer1 = null;
                  location.href = `authentication_status.htm?type=3`;
                }else{
                  clearInterval(this.timer1);
                  this.timer1 = null;
                  location.href = `authentication_status.htm?type=${this.rzType}`;
                }
              }
            }
          });
        },
        getCertificationAuth() {
          certificationAuth().then((res) => {
            if (res.data.status === 400) {
              if (res.data.data.code === 10000) {
                location.href = "authentication_select.htm";
              } else if (res.data.data.code === 10001) {
                location.href = `authentication_status.htm?type=${this.rzType}`;
              }
            } else if (res.data.status === 200) {
              this.contentBox = res.data.data.html;
              $("#third-box").html(this.contentBox);
              // wimdow.isCodeFinshed  为验证接口返回成功的标志 值为true时轮询接口验证状态
              if (window.isCodeFinshed === false) {
                this.$nextTick(() => {
                  window.codeTimer = setInterval(() => {
                    if (window.isCodeFinshed) {
                      clearInterval(window.codeTimer);
                      window.codeTimer = null;
                      this.timer1 = setInterval(() => {
                        this.grtCertificationStatus();
                      }, 2000);
                    }
                  }, 200);
                });
              } else {
                // 这里的elses是为了兼容三方开发者写的实名接口
                setTimeout(() => {
                  this.timer1 = setInterval(() => {
                    this.grtCertificationStatus();
                  }, 2000);
                }, 4000);
              }
            }
          });
        },

        // 获取通用配置
        getCommonData() {
          getCommon().then((res) => {
            if (res.data.status === 200) {
              this.commonData = res.data.data;
              localStorage.setItem(
                "common_set_before",
                JSON.stringify(res.data.data)
              );
              document.title =
                this.commonData.website_name + "-" + lang.realname_text81;
            }
          });
        },
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
