const productFilter = {
  template: `
    <div class="product-tab-list">
      <div v-for="(item,index) in tabList" :key="index" :class="select_tab === item.tab ? 'pro-tab-item active': 'pro-tab-item'" @click="handelTab(item)">
        {{item.name}}
        <span  v-if="count[item.countName] > 0">({{count[item.countName]}})</span>
      </div>
    </div>
    `,
  data() {
    return {
      select_tab: "using",
    };
  },
  props: {
    tabList: {
      type: Array,
      required: false,
      default: () => {
        return [
          {
            name: lang.product_list_status1,
            tab: "using",
            countName: "using_count",
          },
          {
            name: lang.product_list_status2,
            tab: "expiring",
            countName: "expiring_count",
          },
          {
            name: lang.product_list_status3,
            tab: "overdue",
            countName: "overdue_count",
          },
          {
            name: lang.product_list_status4,
            tab: "deleted",
            countName: "deleted_count",
          },
          {
            name: lang.finance_btn5,
            tab: "",
            countName: "all_count",
          },
        ];
      },
    },
    tab: {
      type: String,
      required: false,
      default: "",
    },
    count: {
      type: Object,
      required: false,
      default: {
        all_count: 0, // 全部产品数量
        deleted_count: 0, // 已删除产品数量
        expiring_count: 0, // 即将到期产品数量
        overdue_count: 0, // 已逾期产品数量
        using_count: 0, // 正常使用产品数量
      },
    },
  },
  mounted() {
    if (this.tab) {
      this.select_tab = this.tab;
    }
  },
  methods: {
    handelTab(item) {
      if (this.select_tab === item.tab) {
        this.select_tab = "";
      } else {
        this.select_tab = item.tab;
      }
      this.$emit("update:tab", this.select_tab);
      this.$emit("change");
    },
  },
};
