const proofDialog = {
  template: `
      <!-- 上传凭证 -->
      <div>
        <el-dialog custom-class='pay-dialog proof-dailog' :class='{look: isLook}' :visible.sync="proofDialog" :show-close="false"
        @close="proofClose">
        <div class="pc-pay">
          <div class="dia-title">
            <div class="title-text" v-if="isLook">{{lang.finance_custom19}}</div>
            <div class="title-text" v-else>{{lang.finance_custom6}}：{{zfData.orderId}}</div>
            <div class="title-text" v-show="!isLook">{{lang.pay_text2}}
              <span class="pay-money">{{ commonData.currency_prefix }}
                <span class="font-26">{{ Number(zfData.amount).toFixed(2)}}</span>
              </span>
              <i class="el-icon-circle-close close" @click="proofDialog = false"></i>
            </div>
          </div>
          <div class="dia-content">
            <div class="item" v-show="!isLook">
              <div class="pay-top">
                <div class="pay-type" ref="payListRef">
                  <div class="type-item active">
                    <img :src="bankImg" alt="" />
                  </div>
                </div>
              </div>
              <div class="qr-money">
                <span>{{lang.finance_custom11}}：</span>
                <span class="pay-money">{{ commonData.currency_prefix}}
                  <span class="font-26">
                    {{orderInfo.amount_unpaid}}{{commonData.currency_code}}
                  </span>
                </span>
              </div>
              <p class="des">
                ({{lang.finance_custom12}}：{{commonData.currency_prefix}}{{orderInfo.credit}}{{commonData.currency_code}})
              </p>
              <div class="custom-text">
                <div class="qr-content" v-loading="payLoading" v-html="payHtml" id="payBox"></div>
                <i class="el-icon-document-copy" v-if="payHtml" @click="copyText(payHtml)"></i>
              </div>
              <el-steps :space="200" :active="stepNum" finish-status="success" :align-center="true"
                class="custom-step">
                <el-step :title="lang.finance_custom7"></el-step>
                <el-step :title="lang.finance_custom4"></el-step>
                <el-step>
                  <template slot="title">
                    <span class="txt" :class="{ fail: orderStatus === 'ReviewFail'}">{{orderStatus === 'ReviewFail'
                      ? lang.finance_custom9 : lang.finance_custom8}}</span>
                    <el-popover placement="top-start" trigger="hover" :title="review_fail_reason">
                      <span class="help" slot="reference" v-if="orderStatus === 'ReviewFail'">?</span>
                    </el-popover>
                  </template>
                </el-step>
                <el-step :title="lang.finance_custom10"></el-step>
              </el-steps>
            </div>
            <div class="item">
              <p v-show="!isLook">{{lang.finance_custom4}}</p>
              <el-upload class="upload-demo" ref="fileupload" drag
                action="/console/v1/upload" :headers="{Authorization: jwt}"
                :before-remove="beforeRemove" multiple :file-list="fileList" :on-success="handleSuccess"
                :on-preview="clickFile"
                :limit="10"
                accept="image/*, .pdf, .PDF" v-if="!isLook">
                <div class="el-upload__text">
                  <p>{{lang.finance_custom16}}<em>{{lang.finance_custom17}}</em></p>
                  <p>{{lang.finance_custom18}}</p>
                </div>
              </el-upload>
              <div v-else class="view-box">
                <p class="item" v-for="(item, index) in fileList" :key="index" @click="clickFile(item)">
                  {{item.name}}
                </p>
              </div>
              <div class="dia-fotter" v-if="!isLook">
                <el-button class="cancel-btn" @click="changeWay">{{lang.finance_custom14}}</el-button>
                <el-button @click="submitProof" :disabled="formData.voucher.length === 0" class="submit-btn" :loading="submitLoading">
                  {{orderStatus === 'WaitUpload' ? lang.finance_custom4 : lang.finance_custom5}}
                </el-button>
              </div>
              <div class="dia-fotter" v-else>
                <el-button class="cancel-btn" @click="proofClose">{{lang.finance_text58}}</el-button>
              </div>
            </div>
          </div>
        </div>
        </el-dialog>
        <!-- 图片预览 -->
        <div style="height: 0;">
          <img id="proofViewer" :src="preImg" alt="">
        </div>
        <!-- 变更支付方式 -->
        <div class="delete-dialog">
          <el-dialog width="4.35rem" :visible.sync="showChangeWay" :show-close=false @close="showChangeWay=false">
            <div class="delete-box">
              <div class="delete-content">{{lang.finance_custom15}}</div>
              <div class="delete-btn">
                <el-button class="confirm-btn btn" @click="handelChangeWay" :loading="changeLoading">{{lang.finance_btn8}}</el-button>
                <el-button class="cancel-btn btn" @click="showChangeWay=false">{{lang.finance_btn7}}</el-button>
              </div>
            </div>
          </el-dialog>
        </div>
      </div>

    `,
  created() {},
  mounted() {
    // 引入 jquery
    // const script = document.createElement("script");
    // script.src = `${url}js/common/jquery.mini.js`;
    // document.body.appendChild(script);
    // this.initViewer();
  },
  // components: {
  //   payDialog
  // },
  computed: {
    srcList() {
      return this.formData.voucher;
    },
  },
  destroyed() {},
  props: {},
  data() {
    return {
      proofDialog: false,
      zfData: {
        orderId: 0,
        amount: 0,
      },
      commonData: {
        currency_prefix: "￥",
      },
      orderInfo: {},
      stepNum: 0,
      orderStatus: "",
      review_fail_reason: "",
      payLoading: false,
      payHtml: "",
      fileList: [],
      jwt: `Bearer ${localStorage.jwt}`,
      formData: {
        id: "",
        voucher: [],
      },
      submitLoading: false,
      showChangeWay: false,
      changeLoading: false,
      viewer: null,
      preImg: "",
      isLook: false,
      bankImg: `data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGoAAAAcCAYAAACJWipLAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJ
      bWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdp
      bj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6
      eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQ1IDc5LjE2
      MzQ5OSwgMjAxOC8wOC8xMy0xNjo0MDoyMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJo
      dHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlw
      dGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAv
      IiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RS
      ZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpD
      cmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTkgKFdpbmRvd3MpIiB4bXBNTTpJbnN0
      YW5jZUlEPSJ4bXAuaWlkOkRDQ0EyQzUzQTNFOTExRUE5OENBOTNGMERBMUM4MkM0IiB4bXBNTTpE
      b2N1bWVudElEPSJ4bXAuZGlkOkRDQ0EyQzU0QTNFOTExRUE5OENBOTNGMERBMUM4MkM0Ij4gPHht
      cE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6RENDQTJDNTFBM0U5MTFF
      QTk4Q0E5M0YwREExQzgyQzQiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6RENDQTJDNTJBM0U5
      MTFFQTk4Q0E5M0YwREExQzgyQzQiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94
      OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz48YXx4AAAEzUlEQVR42uxaO2/TUBR2SiqxoKYS
      MMDQAGKuO9IONQsSYmiQGBBCqvsLcOcMSQWZm1asqC4TEgNhYGGJI9GygOqOFUW4bEyEAYFQUDhH
      fJee3th5tUpi4SMdXz+u7+N85+kkZSjKNy7Q8SbxGWM49Jt4xyil3/BFs9k0EjqkNEC6R8cnxKeH
      vqJ84xUd7xD/TOA5pDESzBVq3ZEA6S/dIi4m0OhAGcZd4vERW9f9BJpWoM6N4LouJtC0ApVQAlRC
      J5n1PSauxHHx1tycyW1te9vvd4z52VmLmoDGCHp8L0ONzYkYvVvXnvG66t2MKcsQ2g+vxfe2tuqt
      QJXS+9TujxwKj7qqoxxiE8wCKqvzsL46oBA2K6lH5w4EH0WuJngW6iq/y8LV+nrIXMshANtyHgLH
      ADg8f5X4Ot5XwOX4WTquroA2kaVmkXgNVlEXIHla9wJxJgQkTwCegfCjqIL3eF7mHPE3fk/MnwFP
      CGs1YF0KTPW+KwA3I7xFRa0xtkCJWusBuKZAIqEUNVAKESBNswYLS7G6mNcG8IqqaHn+eXF/VZzX
      tLEDzJ9Fa4WA5KGfHVugsBG2piUSsisA4M3ZQpMj3RhAWmJtpv58beqxJoxYCag/a/oO8TKsRa6B
      z7N0r9MaLLBu/TmAw3M4Kl6ljXyjDI0cNUp10WeDBLOB8xXh9lytXzXEjZWhsT6uGbBOIHnCqg7g
      5liwLmJPFsIP6LrYIcZFEc9RJ4CKetYXO6JN+GRVl+D+TMSYQAlJCFRpuS5wV1igilFfe1AeG3PL
      uGdi/imxlpYY18ZLqOzpBa6le12JbYwisALaTAA34wlACnpMikivXcSUGlxeKiRtryKGedr9CYCV
      QTKhMj8es9iF2wuj65hvGWP7wjMEcY1RlnRnJKimSCY2kYGxO7+Nc2V5ngBpEVlbr+RDqMqyTIxf
      V+m4sNR/7xB4TgfF81SqrhINvhf3LxNKWAzKLs4dkVGVNXc0j1hiiBR5KaT+6UhsfWxhsLIAABmw
      LgfzFMG+lgkamqv028SpBZQgsY5RLBwPlqW7PhYmB/NNpNFsNct0ryyEbYnis58vGaruMuEGq0gu
      KsKysrC4Tc2aXPRjXmCFof1Y2v4qtLcDjJMbtEW9Jp5sw2+P8RnIQcoshVmHtdVDUuDjUBYgqbjE
      c0wSGFlYmCPcFyc2tmaRgUg6XjIY6lOYcO1FAMpW5QzaoqaMUjq6Tsk39uh4rccxTbgW5jURMwqI
      WTY2vAMLK7f7LoivDraoZ8Jcn6uCPFJwS9VfbLVQGJWo+Fxcy/qMrnMohtfgHj14B0fLDi0oxSq7
      wEFa1GUC41Sb5x/6GDMDjZ6Bm5hQ8YvdG0CxUGPl9M9IEVQAT2PsnuIYWwxc6woSFiek2N5ldwgA
      LdzLiPhkcglCbANQb5AWNQ4N+RjxfK+PMXMqwENbZ3SLwbOiEf7zvhPillI9zO9G1Uf4ghFW5Joi
      AVHx1oHLCzg+afFq4K6P6WoboHr+gh/yc0CvWZx/zP0EfTwP2uwnsigeGwJQxkkB9T8RW9SXAc63
      TnFqvYt+nxNoWi3qGfGvEVvX0wQaHahS+hO1/AfM7yOypufEDxNojlJK1DFn6XiD+PyQ1vKD+B0p
      znu+SP7SfJT+CDAAdKMBqP61EkMAAAAASUVORK5CYII=
      `,
    };
  },
  methods: {
    proofClose() {
      this.proofDialog = false;
      this.viewer && this.viewer.destroy();
    },
    changeWay() {
      this.showChangeWay = true;
    },
    initViewer() {
      this.viewer = new Viewer(document.getElementById("proofViewer"), {
        button: true,
        inline: false,
        zoomable: true,
        title: true,
        tooltip: true,
        minZoomRatio: 0.5,
        maxZoomRatio: 100,
        movable: true,
        interval: 2000,
        navbar: true,
        loading: true,
      });
    },
    // 附件下载
    clickFile(item) {
      const name = item.name;
      const imgUrl = item.url || item.response.data.image_url;
      const type = name.substring(name.lastIndexOf(".") + 1);
      if (
        [
          "png",
          "jpg",
          "jepg",
          "bmp",
          "webp",
          "PNG",
          "JPG",
          "JEPG",
          "BMP",
          "WEBP",
        ].includes(type)
      ) {
        this.preImg = imgUrl;
        if (!this.viewer) {
          this.initViewer();
        }
        setTimeout(() => {
          this.viewer.show();
          $("#proofViewer").attr("src", imgUrl);
        }, 10);
      } else {
        const downloadElement = document.createElement("a");
        downloadElement.href = url;
        downloadElement.download = item.name; // 下载后文件名
        document.body.appendChild(downloadElement);
        downloadElement.click(); // 点击下载
      }
    },
    emitRefresh(isChange = false) {
      this.$emit("refresh", isChange, this.orderInfo.id);
    },
    copyText(text) {
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
    async handelChangeWay() {
      try {
        this.changeLoading = true;
        const res = await changePayType(this.orderInfo.id);
        this.$message.success(res.data.msg);
        this.showChangeWay = false;
        this.proofDialog = false;
        this.changeLoading = false;
        this.emitRefresh(true);
      } catch (error) {
        console.log("error", error);
        this.changeLoading = false;
        this.showChangeWay = false;
        this.$message.error(error.data.msg);
      }
    },
    onProgress(event) {
      console.log(event);
    },
    async submitProof() {
      try {
        if (this.formData.voucher.length === 0) {
          return this.$message.warning(lang.finance_custom13);
        }
        this.submitLoading = true;
        const params = {
          id: this.zfData.orderId,
          voucher: this.formData.voucher,
        };
        const res = await uploadProof(params);
        this.submitLoading = false;
        this.$message.success(res.data.msg);
        this.proofDialog = false;
        this.emitRefresh();
      } catch (error) {
        this.submitLoading = false;
        this.$message.error(error.data.msg);
      }
    },
    beforeRemove(file, fileList) {
      // 获取到删除的 save_name
      let save_name = file.save_name || file.response.data.save_name;
      this.formData.voucher = this.formData.voucher.filter((item) => {
        return item != save_name;
      });
    },
    // 上传文件相关
    handleSuccess(response, file, fileList) {
      if (response.status != 200) {
        this.$message.error(response.msg);
        // 清空上传框
        let uploadFiles = this.$refs["fileupload"].uploadFiles;
        let length = uploadFiles.length;
        uploadFiles.splice(length - 1, length);
      } else {
        this.formData.voucher = [];
        this.formData.voucher = fileList.map(
          (item) => item.response?.data?.save_name || item.save_name
        );
      }
    },
    async getOrderDetails(orderId) {
      try {
        const res = await orderDetails(orderId);
        this.orderInfo = res.data.data.order;

        const {id, amount, status, review_fail_reason} = res.data.data.order;
        this.zfData.orderId = Number(id);
        this.zfData.amount = amount;
        this.proofDialog = true;
        this.orderStatus = status;
        this.review_fail_reason = review_fail_reason;
        this.isLook = status === "Paid" && this.orderInfo.voucher.length > 0;
        if (status === "WaitUpload") {
          this.stepNum = 2;
        } else {
          this.stepNum = 3;
        }
        // 获取转账信息
        this.payLoading = true;
        let result = "";
        if (!this.isLook) {
          result = await pay({
            id,
            gateway: "UserCustom",
          });
          this.payLoading = false;
          this.payHtml = result.data.data.html;
          $("#payBox").html(res.data.data.html);
        }

        this.fileList = this.orderInfo.voucher;
        this.formData.voucher = this.orderInfo.voucher.map(
          (item) => item.save_name
        );
      } catch (error) {
        console.log("error", error);
        this.$message.error(error.data.msg);
        this.payLoading = false;
      }
    },
  },
};
