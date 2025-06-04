// css 样式依赖common.css
ELEMENT.Dialog.props.closeOnClickModal.default = false;
const topMenu = {
  template: `
<div>
  <el-drawer :visible.sync="isShowMenu" direction="ltr" :before-close="handleClose" :with-header="false" size="3.8rem"
    custom-class="drawer-menu">
    <div class="drawer-menu-top">
      <a :href="commonData.clientarea_logo_url || '/home.htm'" onclick="return false" class="menu-alink">
        <img class="drawer-menu-logo" @click="goHome" :src="logo"></img>
      </a>
    </div>
    <div class="drawer-menu-list-top">
      <el-menu class="menu-top" :default-active="menuActiveId" @select="handleSelect" background-color="#1E41C9"
        text-color="rgba(255, 255, 255, .6)" active-text-color="#FFF">
        <template v-for="item in menu1">
          <!-- 只有一级菜单 -->
          <el-menu-item v-if="!item.child || item.child?.length === 0" :key="item.id"
            :index="item.id ? item.id + '' : item.url" :id="item.url">
            <a :href="getMenuUrl(item.id || item.url)" onclick="return false" class="menu-alink">
              <i class="iconfont" :class="item.icon"></i>
              <span class="aside-menu-text" slot="title">{{item.name}}</span>
            </a>
          </el-menu-item>
          <!-- 有二级菜单 -->
          <el-submenu v-else :key="item.id" :index="item.id ? item.id + '' : item.url" :id="item.url">
            <template slot="title">
              <i class="iconfont" :class="item.icon"></i>
              <span class="aside-menu-text" slot="title">{{item.name}}</span>
            </template>
            <template v-for="child in item.child">
              <el-menu-item :index="child.id ? child.id + '' : child.url" :key="child.id">
                <a :href="getMenuUrl(child.id || child.url)" onclick="return false" class="menu-alink">
                  {{child.name}}
                </a>

              </el-menu-item>
            </template>
          </el-submenu>
        </template>
      </el-menu>

      <div class="line" v-if="hasSeparate"></div>

      <el-menu class="menu-top" :default-active="menuActiveId " @select="handleSelect" background-color="#1E41C9"
        text-color="rgba(255, 255, 255, .6)" active-text-color="#FFF">
        <template v-for="item in menu2">
          <!-- 只有一级菜单 -->
          <el-menu-item v-if="!item.child || item.child?.length === 0" :key="item.id"
            :index="item.id ? item.id + '' : item.url" :id="item.url">
            <a :href="getMenuUrl(item.id || item.url)" onclick="return false" class="menu-alink">
              <i class="iconfont" :class="item.icon"></i>
              <span class="aside-menu-text" slot="title">{{item.name}}</span>
            </a>
          </el-menu-item>
          <!-- 有二级菜单 -->
          <el-submenu v-else :key="item.id" :index="item.id ? item.id + '' : item.url" :id="item.url">
            <template slot="title">
              <i class="iconfont" :class="item.icon"></i>
              <span class="aside-menu-text" slot="title">{{item.name}}</span>
            </template>
            <template v-for="child in item.child">
              <el-menu-item :index="child.id ? child.id + '' : child.url" :key="child.id">
                <a :href="getMenuUrl(child.id || child.url)" onclick="return false" class="menu-alink">
                  {{child.name}}
                </a>
              </el-menu-item>
            </template>
          </el-submenu>
        </template>
      </el-menu>

    </div>
  </el-drawer>

  <el-header>
    <div class="header-left">
      <img src="${url}/img/common/menu.png" class="menu-img" @click="showMenu">
      <img v-if="isShowMore" src="${url}/img/common/search.png" class="left-img">
      <el-autocomplete v-if="isShowMore" v-model="topInput" :fetch-suggestions="querySearchAsync" placeholder="请输入内容"
        @select="handleSelect">
        <template slot-scope="{ item }">
          <div class="search-value">{{ item.value }}</div>
          <div class="search-name">{{ item.name }}</div>
        </template>
      </el-autocomplete>
    </div>
    <div class="header-right">

      <div class="header-right-item car-item">
        <div v-if="isShowCart" class="right-item" @click="goShoppingCar">
          <el-badge :value="shoppingCarNum" class="item" :max="999" :hidden="shoppingCarNum === 0 ? true : false">
            <img src="${url}/img/common/cart.svg">
          </el-badge>
        </div>
      </div>

      <div class="header-right-item car-item" v-plugin="'ClientCare'">
        <el-popover placement="bottom-start" trigger="hover" :visible-arrow="false">
          <div class="top-msg-box">
            <div class="msg-top">
              <span class="msg-top-left">{{lang.subaccount_text56}}</span>
              <span class="msg-top-right" @click="goAccount">{{lang.subaccount_text57}}</span>
            </div>
            <div class="msg-list" v-if="msgList.length !== 0">
              <div class="msg-item" v-for="item in msgList" :key="item.id" @click="goMsgDetail(item.id)">
                <div class="msg-item-left">
                  <img src="${url}/img/common/msg_tip.png" v-if="item.read === 0">
                  <img src="${url}/img/common/msg_no.png" v-else>
                </div>
                <div class="msg-item-right">
                  <div class="msg-item-right-top">{{item.title}}</div>
                  <div class="msg-item-right-bottom">
                    <span>{{msgType[item.type]}}</span>
                    <span>{{item.create_time | formateTime}}</span>
                  </div>
                </div>
              </div>
            </div>
            <el-empty v-else :description="lang.subaccount_text55"></el-empty>
          </div>
          <div class="right-item" slot="reference">
            <el-badge :value="msgCount" class="item" :max="999" :hidden="msgCount === 0 ? true : false">
              <img src="${url}/img/common/msg.svg">
            </el-badge>
          </div>
        </el-popover>

      </div>

      <div class="header-right-item hg-24">
        <el-dropdown @command="changeLang" trigger="click" :disabled="commonData.lang_home_open * 1 ? false : true">
          <div class="el-dropdown-country">
            <img :src="curSrc" alt="">
            <i class="right-icon el-icon-arrow-down el-icon--right"></i>
          </div>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item v-for="item in commonData.lang_list" :key="item.display_lang"
              :command="item.display_lang">{{item.display_name}}</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>

      <div class="header-right-item cloum-line-item" v-if="isGetData">
        <div class="cloum-line"></div>
      </div>

      <div class="header-right-item" v-show="unLogin && isGetData">
        <div class="un-login" @click="goLogin">
          <img src="${url}/img/common/login_icon.png">{{lang.topMenu_text1}}
        </div>
      </div>

      <div class="header-right-item" v-show="!unLogin && isGetData">
        <el-dropdown @command="handleCommand" trigger="click">
          <div class="el-dropdown-header">
            <div class="right-item head-box" ref="headBoxRef" v-show="firstName">{{firstName}}</div>
            <i class="right-icon el-icon-arrow-down el-icon--right"></i>
          </div>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item command="account">{{lang.topMenu_text2}}</el-dropdown-item>
            <el-dropdown-item command="quit">{{lang.topMenu_text3}}</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>

    </div>
  </el-header>
</div>
    `,
  directives: {
    plugin: {
      inserted: function (el, binding) {
        const addonsDom = document.querySelector("#addons_js");
        let addonsArr = [];
        let arr = [];
        if (addonsDom) {
          addonsArr = JSON.parse(addonsDom.getAttribute("addons_js")) || []; // 插件列表
          // 判断是否安装了某插件
          arr = addonsArr.filter((item) => item.name === binding.value);
          if (arr.length === 0) {
            // 未安装 移除该元素的dom
            el.parentNode.removeChild(el);
          }
        } else {
          el.parentNode.removeChild(el);
        }
      },
    },
  },
  data() {
    return {
      topInput: "",
      // curSrc: url+'/img/common/'+lang_obj.countryImg+'.png' ,
      curSrc: `/upload/common/country/${lang_obj.countryImg}.png`,
      isShowMenu: false,
      logo: `/upload/logo.png`,
      menu1: [],
      menu2: [],
      menuActiveId: "",
      firstName: "",
      hasSeparate: false,
      originMenu: [],
      produclData: [],
      selectValue: "",
      shoppingCarNum: 0,
      headBgcList: [
        "#3699FF",
        "#57C3EA",
        "#5CC2D7",
        "#EF8BA2",
        "#C1DB81",
        "#F1978C",
        "#F08968",
      ],
      commonData: {
        lang_list: [],
      },
      unLogin: true,
      isGetData: false,
      msgList: [],
      msgCount: 0,
      accountData: {},
      msgType: {
        official: lang.subaccount_text54,
        host: lang.finance_info,
        finance: lang.finance_text123,
      },
    };
  },
  props: {
    isShowMore: {
      type: Boolean,
      default: false,
    },
    isShowCart: {
      type: Boolean,
      default: true,
    },
    num: {
      type: Number,
      default: 0,
    },
  },
  watch: {
    num(val) {
      if (val) {
        this.shoppingCarNum = val;
      }
    },
  },
  filters: {
    formateTime(time) {
      if (time && time !== 0) {
        const date = new Date(time * 1000);
        Y = date.getFullYear() + "-";
        M =
          (date.getMonth() + 1 < 10
            ? "0" + (date.getMonth() + 1)
            : date.getMonth() + 1) + "-";
        D = (date.getDate() < 10 ? "0" + date.getDate() : date.getDate()) + " ";
        h =
          (date.getHours() < 10 ? "0" + date.getHours() : date.getHours()) +
          ":";
        m =
          date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
        return Y + M + D + h + m;
      } else {
        return "--";
      }
    },
  },
  created() {
    this.GetIndexData();
    this.doGetMenu();
    this.getCartList();
    this.getCommonSetting();
  },
  mounted() {
    if (this.getPluginId("ClientCare")) {
      this.getMessageList();
    }
  },
  methods: {
    getPluginId(pluginName) {
      const addonsDom = document.querySelector("#addons_js");
      if (addonsDom) {
        const addonsArr = JSON.parse(addonsDom.getAttribute("addons_js")); // 插件列表
        for (let index = 0; index < addonsArr.length; index++) {
          const element = addonsArr[index];
          if (pluginName === element.name) {
            return element.id;
          }
        }
      } else {
        console.log("请检查页面是否有插件dom");
      }
    },
    goAccount() {
      location.href = "/account.htm?type=3";
    },
    getMessageList() {
      messageInfo().then((res) => {
        this.msgList = res.data.data.credit_limit.list;
        this.msgCount = res.data.data.credit_limit.count;
        this.msgType = res.data.data.credit_limit.type.reduce((all,cur) => {
          all[cur.name] = cur.name_lang;
          return all;
        },{});
      });
    },
    goMsgDetail(id) {
      location.href = `/plugin/${getPluginId(
        "ClientCare"
      )}/msgDetail.htm?id=${id}`;
    },
    // 退出登录
    logOut() {
      this.$confirm(lang.topMenu_text4, lang.topMenu_text5, {
        confirmButtonText: lang.topMenu_text6,
        cancelButtonText: lang.topMenu_text7,
        type: "warning",
      })
        .then(() => {
          //const res = await Axios.post('/logout')
          Axios.post("/logout").then((res) => {
            localStorage.removeItem("jwt");
            setTimeout(() => {
              location.href = "/login.htm";
            }, 300);
          });
        })
        .catch(() => {});
    },
    goLogin() {
      location.href = "/login.htm";
    },
    goHome() {
      localStorage.frontMenusActiveId = "";
      const openUrl = this.commonData.clientarea_logo_url || "/home.htm";
      if (this.commonData.clientarea_logo_url_blank == 1) {
        window.open(openUrl);
      } else {
        location.href = openUrl;
      }
    },

    // 获取购物车数量
    getCartList() {
      cartList()
        .then((res) => {
          this.shoppingCarNum = res.data.data.list.filter(
            (item) => item.customfield?.is_domain !== 1
          ).length;
        })
        .catch((err) => {
          this.$message.error(err.data.msg);
        });
    },
    GetIndexData() {
      accountDetail()
        .then((res) => {
          if (res.data.status == 200) {
            this.accountData = res.data.data.account;
            localStorage.lang = res.data.data.account.language || "zh-cn";
            this.firstName = res.data.data.account.username
              .substring(0, 1)
              .toUpperCase();
            this.unLogin = false;
            if (sessionStorage.headBgc) {
              this.$refs.headBoxRef.style.background = sessionStorage.headBgc;
            } else {
              const index = Math.round(
                Math.random() * (this.headBgcList.length - 1)
              );
              this.$refs.headBoxRef.style.background = this.headBgcList[index];
              sessionStorage.headBgc = this.headBgcList[index];
            }
          }
        })
        .finally(() => {
          this.isGetData = true;
        });
    },
    goShoppingCar() {
      localStorage.frontMenusActiveId = "";
      location.href = "/cart/shoppingCar.htm";
    },
    goAccountpage() {
      location.href = "/account.htm";
    },
    // 语言切换
    changeLang(e) {
      if (localStorage.getItem("lang") !== e || !localStorage.getItem("lang")) {
        localStorage.setItem("lang", e);
        sessionStorage.setItem("brow_lang", e);
        this.accountData.language = e;
        this.saveAccount();
      }
    },
    // 编辑基础资料
    saveAccount() {
      const params = {
        ...this.accountData,
      };
      updateAccount(params)
        .then((res) => {
          if (res.data.status === 200) {
            this.$message.success(res.data.msg);
            window.location.reload();
          }
        })
        .catch((error) => {
          this.$message.error(error.data.msg);
        });
    },
    handleCommand(e) {
      if (e == "account") {
        this.goAccountpage();
      }
      if (e == "quit") {
        this.logOut();
      }
      console.log(e);
    },
    // 全局搜索
    querySearchAsync(queryString, cb) {
      if (queryString.length == 0) {
        return false;
      }
      const params = {
        keywords: queryString,
      };
      globalSearch(params).then((res) => {
        if (res.data.status === 200) {
          const data = res.data.data.hosts;
          const result = [];
          data.map((item) => {
            let value = item.product_name + "#/" + item.id;
            result.push({
              id: item.id,
              value,
              name: item.name,
            });
          });
          cb(result);
        }
      });
    },
    /*
     * 获取菜单url
     * @param {Number} id 菜单id 或者url
     * @return {String} url 菜单url
     */
    getMenuUrl(id) {
      const temp =
        this.originMenu.find((item) => item.id == id || item.url == id) || {};
      const reg =
        /^(((ht|f)tps?):\/\/)([^!@#$%^&*?.\s-]([^!@#$%^&*?.\s]{0,63}[^!@#$%^&*?.\s])?\.)+[a-z]{2,6}\/?/;
      let url = "/" + temp.url;
      if (reg.test(temp.url)) {
        if (temp?.second_reminder === 1) {
          url = `/transfer.htm?target=${encodeURIComponent(temp.url)}`;
        } else {
          url = temp.url;
        }
      }
      return url;
    },
    handleSelect(id) {
      localStorage.setItem("frontMenusActiveId", id);
      const temp =
        this.originMenu.find((item) => item.id == id || item.url == id) || {};
      const reg =
        /^(((ht|f)tps?):\/\/)([^!@#$%^&*?.\s-]([^!@#$%^&*?.\s]{0,63}[^!@#$%^&*?.\s])?\.)+[a-z]{2,6}\/?/;
      if (reg.test(temp.url)) {
        if (temp?.second_reminder === 1) {
          return window.open(
            `/transfer.htm?target=${encodeURIComponent(temp.url)}`
          );
        } else {
          return window.open(temp.url);
        }
      }
      location.href = "/" + temp.url;
    },
    showMenu() {
      this.isShowMenu = true;
    },
    handleClose() {
      this.isShowMenu = false;
    },
    // 获取前台导航
    doGetMenu() {
      getMenu().then((res) => {
        if (res.data.status === 200) {
          res.data.data.menu.forEach((item) => {
            if (item.child && item.child.length > 0) {
              this.originMenu.push(...item.child);
            } else {
              this.originMenu.push(item);
            }
          });
          const menu = res.data.data.menu;
          localStorage.setItem("frontMenus", JSON.stringify(menu));
          let index = menu.findIndex((item) => item.name == "分隔符");
          if (index != -1) {
            this.hasSeparate = true;
            this.menu1 = menu.slice(0, index);
            this.menu2 = menu.slice(index + 1);
          } else {
            this.hasSeparate = false;
            this.menu1 = menu;
          }

          this.setActiveMenu();
        }
      });
    },
    // 判断当前菜单激活
    setActiveMenu() {
      const originUrl = location.pathname.slice(1);
      const allUrl = originUrl + location.search;
      let flag = false;
      this.originMenu.forEach((item) => {
        // 当前url下存在和导航菜单对应的路径
        if (!item.child && item.url) {
          const url = String(item.url).split("?");
          if (
            (url.length > 1 && item.url == allUrl) ||
            (url.length == 1 && item.url == originUrl)
          ) {
            this.menuActiveId = item.id + "";
            flag = true;
          }
        }
        // 当前url下存在二级菜单
        if (item.child && item.child.length > 0) {
          item.child.forEach((child) => {
            const url = String(child.url).split("?");
            if (
              (url.length > 1 && child.url == allUrl) ||
              (url.length == 1 && child.url == originUrl)
            ) {
              this.menuActiveId = child.id + "";
              flag = true;
            }
          });
        }
      });
      if (!flag) {
        this.menuActiveId = localStorage.getItem("frontMenusActiveId") || "";
      }
    },
    // 页面跳转
    toPage(e) {
      location.href = "/" + e.url;
    },

    // 获取通用配置
    async getCommonSetting() {
      try {
        if (!localStorage.getItem("common_set_before")) {
          const res = await getCommon();
          this.commonData = res.data.data;
          localStorage.setItem(
            "common_set_before",
            JSON.stringify(res.data.data)
          );
        }
        this.commonData = JSON.parse(localStorage.getItem("common_set_before"));
        this.logo = this.commonData.system_logo;
      } catch (error) {}
    },
  },
};
