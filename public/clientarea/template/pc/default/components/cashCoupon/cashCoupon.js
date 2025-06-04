const cashCoupon = {
  template: `
        <div>
            <el-popover placement="bottom" trigger="click" v-model="visibleShow" class="discount-popover" @show="popSow" :visible-arrow="false">
                <div class="discount-content">
                    <div class="close-btn-img" @click="closePopver">
                        <img src="${url}/img/common/close_icon.png" alt="">
                    </div>
                    <div>
                        <el-select class="discount-input" v-model="value" clearable :placeholder="lang.shoppingCar_tip_text8" value-key="id">
                            <el-option v-for="item in options" :key="item.id" :label=" item.code + '--' + currency_prefix + item.price " :value="item"></el-option>
                        </el-select>
                        <el-button class="discount-btn"  @click="handelApplyPromoCode">{{lang.shoppingCar_tip_text9}}</el-button>
                    </div>
                </div>
                <span slot="reference" class="cash-code">{{lang.shoppingCar_tip_text10}}</span>
            </el-popover>
        </div>
        `,
  data() {
    return {
      visibleShow: false, // 是否显示代金券弹窗
      isLoading: false, // 确认按钮loading
      value: {}, // 选择的对象
      options: [], // 代金券数组
    };
  },
  components: {},
  props: {
    scene: {
      type: String, // new新购,renew续费,upgrade升降级
      required: true,
    },
    product_id: {
      type: Array, // 场景中的所有商品ID
      required: true,
    },
    price: {
      type: Number | String, // 需要支付的原价格
      required: true,
    },
    currency_prefix: {
      type: String,
      default:
        (localStorage.common_set_before &&
          JSON.parse(localStorage.common_set_before)?.currency_prefix) ||
        "￥",
    },
  },
  created() {},
  mounted() {},
  methods: {
    closePopver() {
      this.visibleShow = false;
      this.value = {};
    },
    handelApplyPromoCode() {
      if (!this.value.id) {
        this.$message.warning(lang.shoppingCar_tip_text11);
        return;
      }
      this.$emit("use-cash", this.value);
      this.visibleShow = false;
    },
    popSow() {
      const params = {
        scene: this.scene,
        product_id: this.product_id,
        price: Number(this.price),
      };
      this.getEnableList(params);
    },
    getEnableList(params) {
      enableList(params)
        .then((res) => {
          this.options = res.data.data.list;
        })
        .catch((err) => {
          this.$message.error(err.data.msg);
        })
        .finally(() => {});
    },
  },
};
