const payDialog = {
  template: `
      <div>
        <el-dialog custom-class='pay-dialog' :visible.sync="isShowZf" :show-close="false" @close="zfClose">
          <div class="pc-pay">
            <div class="dia-title">
              <div class="title-text">{{lang.pay_text1}}：ID-{{zfData.orderId}}</div>
              <div class="title-text">{{lang.pay_text2}} <span class="pay-money">{{ commonData.currency_prefix }}<span class="font-26">{{ Number(zfData.amount).toFixed(2)}}</span></span></div>
            </div>
            <div class="dia-content">
              <div class="pay-top">
                <div class="pay-type" ref="payListRef">
                  <div class="type-item" v-for="item in gatewayList" @click="handelSelect(item)" :class="zfData.gateway === item.name ? 'active' : ''" :key="item.id">
                    <img :src="item.url" v-if="item.url" alt="">
                    <span v-else>{{item.title}}</span>
                    <span class="type-dec" v-if="isShowCredit && item.name === 'CreditLimit'">{{lang.pay_text3}}{{ commonData.currency_prefix}}{{creditData.remaining_amount}}</span>
                    <span class="type-dec" v-if="item.name === 'credit'">{{lang.pay_text6}}{{ commonData.currency_prefix}}{{balance}}</span>
                  </div>
                </div>
                <!-- 左右按钮 -->
                <div class="left-btn" @click="handelLeftBtn" v-if="gatewayList.length >= 6">
                  <img src="${url}img/common/left_btn.png" alt="">
                </div>
                <div class="right-btn" @click="handelRightBtn" v-if="gatewayList.length >= 6">
                  <img src="${url}img/common/right_btn.png" alt="">
                </div>
              </div>
              <div class="credit-tip" v-if="zfData.gateway == 'CreditLimit'">{{lang.pay_text4}}</div>
              <div class="QR-box" v-if="zfData.gateway !== 'credit' && zfData.gateway !== 'CreditLimit'">
                <div class="left">
                  <div class="qr-text" v-if="zfData.gateway !== 'UserCustom'">
                    <span v-if="zfData.gateway === 'UserCustom'">{{lang.pay_text7}}：</span>
                    <span v-else>{{lang.pay_text8}}：</span>
                  </div>
                  <div class="qr-content" v-loading="payLoading || loading" v-show="isShowPay && isShowimg && zfData.gateway !== 'UserCustom'" id="payBox"></div>
                  <div class="qr-content" v-loading="loading" v-show="(!isShowPay && zfData.gateway !== 'UserCustom' || zfData.gateway === 'CreditLimit')">
                    <img style="width:100%;height:100%" src="${url}img/common/payloading.png" />
                  </div>
                  <div class="qr-money">
                    <span v-if="zfData.gateway === 'UserCustom'">{{lang.pay_text7}}：</span>
                    <span v-else>{{lang.pay_text8}}：</span>
                    <span class="pay-money">{{ commonData.currency_prefix}}
                      <span class="font-26" v-if="zfData.gateway == 'CreditLimit'">
                        0{{commonData.currency_code}}
                      </span>
                      <span class="font-26" v-else>
                        {{ zfData.checked ? (zfData.amount-balance <=0 ? 0 : zfData.amount-balance).toFixed(2)  : Number(zfData.amount).toFixed(2)}}{{commonData.currency_code}}
                      </span>
                    </span>
                    <!-- 线下支付，不是充值订单-->
                    <span v-if="zfData.gateway === 'UserCustom' && !isCz && balance * 1 > 0">
                      <el-checkbox v-model="zfData.checked" @change="useBalance"></el-checkbox>
                      <span class="blance-text" @click="handelBalance">
                      {{(zfData.checked && balance * 1 > zfData.amount * 1) ? lang.pay_text19 : lang.pay_text21}}
                        <span class="blance-tip">
                          ({{lang.pay_text6}}{{ commonData.currency_prefix}} {{(balance * 1).toFixed(2)}})
                        </span>
                      </span>
                    </span>
                  </div>
                </div>
                <div class="recharge_pay" v-if="commonData.recharge_pay_notice_content && isCz"
                  v-html="commonData.recharge_pay_notice_content">
                </div>
              </div>
              <div class="use-blance" v-if="!isCz && zfData.gateway !== 'credit' && zfData.gateway !== 'CreditLimit'
              && zfData.gateway !== 'UserCustom' && balance * 1 > 0">
              <el-checkbox v-model="zfData.checked" @change="useBalance" :disabled="isCz"></el-checkbox>
              <span class="blance-text"  @click="handelBalance">{{lang.pay_text21}}<span class="blance-tip">({{lang.pay_text6}}{{ commonData.currency_prefix}} {{(balance * 1).toFixed(2)}})</span></span>
            </div>
              <div v-if="zfData.gateway === 'UserCustom'" class="custom-text">
                <div class="qr-content" v-loading="payLoading || loading" v-show="isShowPay" v-html="payHtml" id="payBox"></div>
                <i class="el-icon-document-copy" v-if="isShowPay && payHtml" @click="copyText(payHtml)"></i>
              </div>
              <el-steps :space="200" :active="1" finish-status="success"
              :align-center="true" class="custom-step" v-if="isShowPay && zfData.gateway === 'UserCustom'">
                <el-step :title="lang.finance_custom7"></el-step>
                <el-step :title="lang.finance_custom4"></el-step>
                <el-step :title="lang.finance_custom8"></el-step>
                <el-step :title="lang.finance_custom10"></el-step>
              </el-steps>
            </div>
            <div class="dia-fotter" v-if="!loading">
              <el-button class="confirm-btn" @click="handleOk" v-loading="doPayLoading" :disabled="(balance * 1 < zfData.amount *1) && zfData.gateway !== 'CreditLimit'"
                v-if="!isShowPay || zfData.gateway === 'CreditLimit'">{{lang.pay_text9}}
              </el-button>
              <el-button class="confirm-btn" v-if="isShowPay && zfData.gateway === 'UserCustom'" :loading="submitLoading" @click="handleCustom">{{lang.finance_custom20}}</el-button>
              <el-button class="def-btn" v-if="isShowPay && zfData.gateway !== 'UserCustom' && zfData.gateway !== 'CreditLimit'">{{lang.pay_text11}}</el-button>
              <el-button class="cancel-btn" @click="zfClose">{{lang.pay_text12}}</el-button>
            </div>
          </div>
          <div class="mobile-pay">
            <div class="dia-title">
              <div class="title-text">{{lang.pay_text13}}</div>
              <div class="title-text font-26">{{lang.pay_text14}}:<span class="pay-money">{{ commonData.currency_prefix }}<span class="font-26">{{ Number(zfData.amount).toFixed(2)}}</span></span></div>
            </div>
            <div class="dia-content">
              <div class="order-id">{{lang.pay_text15}}：ID-{{zfData.orderId}}</div>
              <div class="pay-top">
                <div class="pay-type">
                  <div class="type-item" v-for="item in gatewayList" @click="handelSelect(item)" :class="zfData.gateway === item.name ? 'active' : ''" :key="item.id">
                    <img :src="item.url" v-if="item.url" alt="">
                    <span v-else>{{item.title}}</span>
                    <span class="type-dec" v-if="isShowCredit && item.name === 'CreditLimit'">{{lang.pay_text3}}{{ commonData.currency_prefix}}{{creditData.remaining_amount}}</span>
                  </div>
                </div>
              </div>
              <div class="credit-tip" v-if="zfData.gateway == 'CreditLimit'">{{lang.pay_text4}}</div>
              <div v-if="zfData.gateway === 'UserCustom'" class="custom-text">
                <div class="qr-content" v-loading="payLoading || loading" v-show="isShowPay" v-html="payHtml" id="payBox"></div>
                <i class="el-icon-document-copy" v-if="isShowPay && payHtml" @click="copyText(payHtml)"></i>
              </div>
              <div class="qr-money">
                <span v-if="zfData.gateway === 'UserCustom'">{{lang.pay_text7}}：</span>
                  <span v-else>{{lang.pay_text8}}：</span>
                  <span class="pay-money">{{ commonData.currency_prefix}}
                  <span class="font-26" v-if="zfData.gateway == 'CreditLimit'">
                    0.00{{commonData.currency_code}}
                  </span>
                  <span class="font-26" v-else>
                    {{ zfData.checked ? (zfData.amount-balance <=0 ? 0 : zfData.amount-balance).toFixed(2)  : Number(zfData.amount).toFixed(2)}}{{commonData.currency_code}}
                  </span>
                </span>
              </div>
              <div class="QR-box" v-if="zfData.gateway !== 'credit' && zfData.gateway !== 'CreditLimit'">
                <div class="left">
                  <div class="qr-text" v-if="zfData.gateway !== 'UserCustom'">
                    <span v-if="zfData.gateway === 'UserCustom'">{{lang.pay_text7}}：</span>
                    <span v-else>{{lang.pay_text8}}：</span>
                  </div>
                  <div class="qr-content" v-loading="payLoading || loading" v-show="isShowPay && isShowimg && zfData.gateway !== 'UserCustom'" v-html="payHtml" id="payBox"></div>
                  <div class="qr-content" v-loading="loading" v-show="(!isShowPay && zfData.gateway !== 'UserCustom' || zfData.gateway === 'CreditLimit')">
                    <img style="width:100%;height:100%" src="${url}img/common/payloading.png" />
                  </div>
                </div>
                <div class="recharge_pay" v-if="commonData.recharge_pay_notice_content && isCz"
                  v-html="commonData.recharge_pay_notice_content">
                </div>
              </div>
              <div class="use-blance" v-if="!isCz && zfData.gateway !== 'credit' && zfData.gateway !== 'CreditLimit' && balance * 1 > 0">
                <el-checkbox v-model="zfData.checked" @change="useBalance" :disabled="isCz"></el-checkbox>
                <span class="blance-text"  @click="handelBalance">{{lang.pay_text21}}<span class="blance-tip">({{lang.pay_text6}}{{ commonData.currency_prefix}} {{(balance * 1).toFixed(2)}})</span></span>
              </div>
              <div class="dia-fotter">
                <el-button class="confirm-btn" @click="handleOk" v-loading="doPayLoading" :disabled="(balance * 1 < zfData.amount *1) && zfData.gateway !== 'CreditLimit'" v-if="!isShowPay || zfData.gateway === 'CreditLimit'">{{lang.pay_text9}}</el-button>
                <el-button class="confirm-btn" v-if="isShowPay && zfData.gateway === 'UserCustom'" :loading="submitLoading" @click="handleCustom">{{lang.finance_custom20}}</el-button>
                <el-button class="def-btn" v-if="isShowPay && zfData.gateway !== 'UserCustom' && zfData.gateway !== 'CreditLimit'">{{lang.pay_text11}}</el-button>
                <el-button class="cancel-btn" @click="zfClose">{{lang.pay_text12}}</el-button>
              </div>
            </div>
          </div>
        </el-dialog>
        <proof-dialog ref="proof" @refresh="refresh"></proof-dialog>
      </div>
    `,
  // <div class="blue">二维码将在<span class="red">{{time | formateDownTime}}</span>后失效，请及时支付</div>
  created () {
    this.commonData =
      JSON.parse(localStorage.getItem("common_set_before")) || {};
  },
  mounted () {
    // 引入 jquery
    const script = document.createElement("script");
    script.src = `${url}js/common/jquery.mini.js`;
    document.body.appendChild(script);
  },
  components: {
    proofDialog
  },
  destroyed () {
    clearInterval(this.timer);
    clearTimeout(this.balanceTimer);
  },
  props: {
    allowCredit: {
      // 是否允许使用信用额
      type: Boolean,
      default: true,
    },
  },
  data () {
    return {
      // 显示弹窗
      isShowZf: false,
      // 显示底部支付按钮
      isShowPay: true,
      timer: null,
      time: 300000,
      zfData: {
        // 订单id
        orderId: 0,
        // 订单金额
        amount: 0,
        checked: false,
        // 支付方式
        gateway: gatewayList.length > 0 ? gatewayList[0].name : "",
      },
      // 支付方式
      gatewayList: [],
      payLoading: false,
      isShowCredit: false,
      isUseBalance: true,
      cantUseCredit: false,
      isShowimg: true,
      creditData: {},
      // 用户余额
      balance: 0,
      errText: "",
      payHtml: "",
      balanceTimer: null,
      commonData: {
        currency_prefix: "￥",
      },
      isPaySuccess: false,
      isNotPayWay: false,
      isCz: false,
      doPayLoading: false,
      loading: false,
      str: "",
      submitLoading: false,
      isTransfer: false
    };
  },
  filters: {
    formateDownTime (time) {
      let minutes = Math.floor(time / 1000 / 60);
      let seconds = (time / 1000) % 60;
      return minutes + "分" + seconds + "秒";
    },
  },
  methods: {
    async handleCustom () {
      try {
        this.submitLoading = true;
        const res = await submitApplication(this.zfData.orderId);
        this.$message.success(res.data.msg);
        this.submitLoading = false;
        this.isTransfer = true;
        this.$refs.proof.getOrderDetails(this.zfData.orderId);
        setTimeout(() => {
          this.isShowZf = false;
        }, 0);
        // location.href = location.origin + '/finance.htm'
      } catch (error) {
        this.submitLoading = false;
        this.$message.error(error.data.msg);
      }
    },
    refresh (bol, id) {
      if (bol) {
        return this.showPayDialog(id);
      }
      this.$emit("payok", this.zfData.orderId);
    },
    initPay () {
      const addons_js_arr = JSON.parse(
        document.querySelector("#addons_js").getAttribute("addons_js")
      ); // 插件列表
      const arr = addons_js_arr.map((item) => {
        return item.name;
      });
      if (arr.includes("CreditLimit")) {
        // 开启了信用额
        this.isShowCredit = true;
      }
      // 获取支付方式列表
      this.getGateway();
    },
    handelBalance () {
      if (this.isCz) {
        return;
      }
      this.zfData.checked = !this.zfData.checked;
      this.useBalance();
    },
    handelSelect (item) {
      if (item.name !== this.zfData.gateway) {
        if (
          item.name === "CreditLimit" &&
          this.zfData.amount * 1 > this.creditData.remaining_amount * 1
        ) {
          this.$message.error(lang.pay_text16);
          return;
        }
        if (this.zfData.gateway === "CreditLimit") {
          this.zfData.checked = true;
          this.useBalance();
        }
        if (item.name === "CreditLimit") {
          this.zfData.checked = false;
        }
        this.zfData.gateway = item.name;
        this.zfSelectChange();
      }
    },
    copyText (text) {
      if (navigator.clipboard && window.isSecureContext) {
        // navigator clipboard 向剪贴板写文本
        this.$message.success(lang.pay_text17);
        return navigator.clipboard.writeText(text);
      } else {
        // 创建text area
        const textArea = document.createElement("textarea");
        textArea.value = text;
        // 使text area不在viewport，同时设置不可见
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        this.$message.success(lang.pay_text17);
        return new Promise((res, rej) => {
          // 执行复制命令并移除文本框
          document.execCommand("copy") ? res() : rej();
          textArea.remove();
        });
      }
    },
    handelLeftBtn () {
      this.$refs.payListRef.scrollBy({
        top: 0,
        left: -200, // 控制向右滚动的距离
        behavior: "smooth",
      });
    },
    handelRightBtn () {
      this.$refs.payListRef.scrollBy({
        top: 0,
        left: 200, // 控制向右滚动的距离
        behavior: "smooth",
      });
    },
    // 获取账户详情
    getAccount () {
      account().then((res) => {
        if (res.data.status === 200) {
          this.balance = res.data.data.account.credit;
        }
      });
    },
    // 支付关闭
    zfClose () {
      if (!this.isPaySuccess && !this.isTransfer) {
        this.$emit("paycancel", this.zfData.orderId);
      }
      this.isCz = false;
      this.cantUseCredit = false;
      this.isShowZf = false;
      this.isShowPay = true;
      clearInterval(this.timer);
      this.time = 300000;
      if (this.zfData.checked) {
        // 如果勾选了使用余额
        this.zfData.checked = false;
        // 取消使用余额
        const params = {
          id: this.zfData.orderId,
          use: 0,
        };
        creditPay(params)
          .then((res) => { })
          .catch((error) => { });
      }
    },
    //  授信详情
    getCreditDetail () {
      creditDetail().then((res) => {
        if (res.data.status === 200) {
          this.creditData = res.data.data.credit_limit;
        }
      });
    },
    // 获取支付方式列表
    getGateway () {
      gatewayList()
        .then((res) => {
          if (res.data.status === 200) {
            this.gatewayList = res.data.data.list;
            if (this.isShowCredit && this.allowCredit) {
              creditDetail().then((res) => {
                if (res.data.status === 200) {
                  this.creditData = res.data.data.credit_limit;
                  if (
                    this.creditData.status === "Active" &&
                    !this.cantUseCredit && this.creditData.remaining_amount * 1 > 0
                    && !this.isCz
                  ) {
                    this.gatewayList.unshift({
                      id: "141137",
                      name: "CreditLimit",
                      title: lang.pay_text18,
                      url: `${url}img/common/credit_log.svg`,
                    });
                  }
                }
              });
            }
            // 后台没返回支付方式
            if (this.gatewayList.length == 0) {
              this.gatewayList = [
                {
                  id: 0,
                  name: "credit",
                  title: lang.order_text8,
                },
              ];
              this.isNotPayWay = true;
            }
          }
        })
        .catch((error) => {
          this.gatewayList = [];
          // 后台没返回支付方式
          if (this.gatewayList.length == 0) {
            this.gatewayList = [
              {
                id: 0,
                name: "credit",
                title: lang.order_text8,
              },
            ];
            this.isNotPayWay = true;
          }
        });
    },
    // 支付方式切换
    async zfSelectChange () {
      try {
        if (this.zfData.gateway == "credit") {
          this.zfData.checked = true;
          this.useBalance();
          return;
        }
        const balance = Number(this.balance);
        const money = Number(this.zfData.amount);
        this.isShowPay = true;
        // 余额大于等于支付金额 且 勾选了使用余额
        if (balance >= money && this.zfData.checked) {
          this.isShowPay = false;
          return;
        }
        this.payHtml = "";
        this.payLoading = true;
        this.isShowimg = true;
        // 获取第三方支付
        const params = { gateway: this.zfData.gateway, id: this.zfData.orderId };
        if (!this.isCz) {
          await creditPay({ id: this.zfData.orderId, use: this.zfData.checked ? 1 : 0 });
        }
        const res = await pay(params);
        this.errText = "";
        this.payLoading = false;
        this.time = 300000;
        this.payHtml = res.data.data.html;
        $("#payBox").html(res.data.data.html);
      } catch (error) {
        this.isShowimg = false;
        this.payLoading = false;
        this.errText = error.data.msg;
      }
    },
    // 使用余额
    useBalance (bol = true) {
      if (this.zfData.gateway == "CreditLimit") {
        this.zfData.gateway = this.gatewayList[1].name;
      }
      if (bol) {
        this.getAccount();
      }
      if (this.balanceTimer) {
        clearTimeout(this.balanceTimer);
        this.balanceTimer = null;
      }
      this.balanceTimer = setTimeout(() => {
        this.loading = true;
        creditPay({ id: this.zfData.orderId, use: this.zfData.checked ? 1 : 0 })
          .then((res) => {
            // 新的订单id
            const tempId = res.data.data.id;
            this.zfData.orderId = tempId;
            // 获取新订单的详情
            orderDetails(tempId).then((result) => {
              this.loading = false;
              const orderRes = result.data.data.order;
              if (this.zfData.checked) {
                //使用余额
                if (Number(this.balance) >= Number(orderRes.amount)) {
                  this.errText = "";
                  this.isShowPay = false;
                } else {
                  // 账户余额小于 订单金额 重新拉取第三方支付并显示
                  this.isShowPay = true;
                  this.zfSelectChange();
                }
              } else {
                // 取消使用余额
                if (Number(this.balance) >= Number(orderRes.amount)) {
                  this.errText = "";
                  this.isShowPay = true;
                  this.zfSelectChange();
                } else {
                  // 账户余额小于 订单金额 重新拉取第三方支付并显示
                  this.isShowPay = true;
                  this.zfSelectChange();
                }
              }
            });
          })
          .catch((error) => {
            this.errText = error.data.msg;
            this.loading = false;
          });
      }, 50);
    },
    // 确认使用余额支付
    handleOk () {
      this.doPayLoading = true;
      const params = {
        gateway: "credit",
        id: this.zfData.orderId,
      };
      if (this.zfData.gateway == "CreditLimit") {
        payCreditLimit(params)
          .then((res) => {
            this.doPayLoading = false;
          })
          .catch((error) => {
            this.$message.error(error.data.msg);
            this.doPayLoading = false;
          });
      } else {
        pay(params)
          .then((res) => {
            this.doPayLoading = false;
          })
          .catch((error) => {
            this.$message.error(error.data.msg);
            this.doPayLoading = false;
          });
      }
    },
    // 轮循支付状态
    pollingStatus (id) {
      if (this.timer) {
        clearInterval(this.timer);
      }
      this.timer = setInterval(async () => {
        const res = await getPayStatus(id);
        this.time = this.time - 2000;
        if (res.data.code === "Paid") {
          this.$message.success(res.data.msg);
          clearInterval(this.timer);
          this.time = 300000;
          this.isShowCz = false;
          this.isShowZf = false;
          this.getAccount();
          this.isPaySuccess = true;
          this.$emit("payok", this.zfData.orderId);
          return false;
        }
        if (this.time === 0) {
          clearInterval(this.timer);
          // 关闭充值 dialog
          this.isShowCz = false;
          this.isShowZf = false;
          this.$message.error(lang.pay_text20);
        }
      }, 2000);
    },
    czPay (orderId) {
      this.isUseBalance = false;
      this.isCz = true;
      this.cantUseCredit = true;
      this.showPayDialog(orderId);
    },
    creditPay (orderId) {
      this.cantUseCredit = true;
      this.showPayDialog(orderId);
    },
    // 点击去支付
    showPayDialog (orderId, amount, payType) {
      this.initPay();
      if (!this.isCz) {
        this.isUseBalance = true;
      }
      this.isPaySuccess = false;
      if (this.timer) {
        // 清除定时器
        clearInterval(this.timer);
      }
      const params = {
        id: orderId,
        use: 0,
      };
      orderDetails(orderId).then((detailRes) => {
        if (detailRes.data.status === 200) {
          // 获取订单金额 和 订单id
          this.zfData.orderId = Number(orderId);
          this.zfData.amount = detailRes.data.data.order.amount;
          this.isCz = detailRes.data.data.order.type === 'recharge';
          const statusArr = ['WaitUpload','WaitReview','ReviewFail']
          if (statusArr.includes(detailRes.data.data.order.status)) {
            return this.$refs.proof.getOrderDetails(this.zfData.orderId);
          }
          if (this.isCz) {
            this.isUseBalance = false;
          }
          if (Number(this.zfData.amount) > 0 && this.isUseBalance) {
            creditPay(params).then((res) => {
              this.errText = "";
              // 默认不使用余额
              this.zfData.checked = false;
              // 重置支付倒计时5分钟
              this.time = 300000;
              // 获取余额
              // this.getAccount();

              // 展示支付 dialog
              this.isShowZf = true;
              account().then((res) => {
                if (res.data.status === 200) {
                  this.balance = res.data.data.account.credit;
                  // 余额大于订单金额添加余额支付方式
                  if (this.balance * 1 > this.zfData.amount * 1 && this.gatewayList[0].id !== 0) {
                    this.gatewayList.unshift({
                      id: 0,
                      name: "credit",
                      title: lang.order_text8,
                    });
                  }
                  // 默认拉取第一种支付方式
                  this.zfData.gateway = payType
                    ? payType
                    : this.gatewayList[0].name;
                  if (this.zfData.gateway == "CreditLimit") {
                    if (
                      this.zfData.amount * 1 >
                      this.creditData.remaining_amount * 1
                    ) {
                      this.zfData.gateway = payType
                        ? payType
                        : this.gatewayList[1].name;
                      this.isUseBalance = true;
                    } else {
                      this.isUseBalance = false;
                    }
                  } else {
                    this.isUseBalance = true;
                  }
                  if (this.balance > 0 && this.isUseBalance) {
                    this.zfData.checked = true;
                  }
                  this.zfSelectChange();
                  // this.useBalance(false);
                }
              });
              // 轮询支付
              this.pollingStatus(this.zfData.orderId);
            });
          } else {
            this.errText = "";
            // 默认不使用余额
            this.zfData.checked = false;
            // 重置支付倒计时5分钟
            this.time = 300000;
            // 获取余额
            this.getAccount();
            // 展示支付 dialog
            this.isShowZf = true;
            // 默认拉取第一种支付方式
            this.zfData.gateway = payType ? payType : this.gatewayList[0]?.name;
            this.zfSelectChange();
            // 轮询支付
            this.pollingStatus(this.zfData.orderId);
          }
        }
      });
    },
  },
};
