/* 通用treeSelect：用于商品筛选（单/多选） */
const comTreeSelect = {
  template: `
      <t-tree-select
        :data="proData"
        v-model="checkPro"
        :popupProps="popupProps"
        :treeProps="treeProps"
        filterable
        clearable
        :multiple="multiple"
        :minCollapsedNum="1"
        :placeholder="prePlaceholder"
        :disabled="disabled"
        :max="max"
        @change="onChange">
        <template #panelTopContent>
          <t-checkbox v-model="checkAll" @change="chooseAll" class="tree-check-all" v-if="showAll">{{lang.check_all}}</t-checkbox>
        </template>
      </t-tree-select>

      `,
  data() {
    return {
      popupProps: {
        overlayInnerStyle: (trigger) => ({
          width: `${trigger.offsetWidth}px`,
        }),
        overlayInnerClassName: "com-tree-select",
      },
      proData: [],
      checkPro: "",
      isInit: true,
      checkAll: false,
      proList: [],
      secondGroup: [],
      firstGroup: [],
    };
  },
  props: {
    treeProps: {
      default() {
        return {
          valueMode: "onlyLeaf",
          keys: {
            label: "name",
            value: "key",
            children: "children",
          },
        };
      },
    },
    // 是否多选
    multiple: {
      default() {
        return false;
      },
    },
    showAll: {
      // 是否展示全选
      default() {
        return false;
      },
    },
    disabled: {
      // 是否禁用
      default() {
        return false;
      },
    },
    value: {
      // 回显传参
      default() {
        return false;
      },
    },
    max: {
      // 控制多选数量
      default() {
        return 0;
      },
    },

    prePlaceholder: {
      default() {
        return lang.product_id_empty_tip;
      },
    },
    need: {
      // 是否返回商品列表
      default() {
        return false;
      },
    },
    allProducts: {
      default() {
        return [];
      },
    },
    product: {
      default() {
        return [];
      },
    },
    // 需要再商品列表剔除的商品id
    disabledProList: {
      default() {
        return [];
      },
    },
    // 是否只显示分组
    isOnlyGroup: {
      default() {
        return false;
      },
    },
    excludeDomain: {
      //是否排除域名
      default() {
        return 0;
      },
    },
    agent: {
      default() {
        return false;
      },
    },
  },
  watch: {
    value: {
      deep: true,
      immediate: true,
      handler(val) {
        if ((typeof val === "string" || typeof val === "number") && val) {
          this.$nextTick(() => {
            this.checkPro = this.isOnlyGroup ? `s-${val}` : `t-${val}`;
          });
        }
        if (typeof val === "object") {
          this.$nextTick(() => {
            const nowArr = [];
            val.forEach((el) => {
              this.isOnlyGroup
                ? nowArr.push(`s-${el}`)
                : nowArr.push(`t-${el}`);
            });
            const temp = Array.from(new Set(nowArr));
            this.checkPro = temp;
          });
        }
      },
    },
    isCheckAll(val) {
      this.checkAll = val;
    },
  },
  created() {
    this.checkPro = this.multiple ? [] : "";
    if (this.allProducts.length > 0) {
      // 单页面多次引用组件
      this.proData = this.allProducts;
      this.proList = this.product;
      this.initDisabledPro();
      return;
    }
    this.init();
  },
  computed: {
    isCheckAll() {
      return (
        this.showAll &&
        this.checkPro.length ===
          (this.isOnlyGroup ? this.secondGroup.length : this.proList.length)
      );
    },
  },
  methods: {
    chooseAll(e) {
      let arr1 = [];
      if (e) {
        const originList = this.isOnlyGroup ? this.secondGroup : this.proList;
        const arr = originList.map((item) => {
          return this.isOnlyGroup ? `s-${item.id}` : `t-${item.id}`;
        });
        arr1 = originList.map((item) => item.id);
        this.checkPro = arr;
      } else {
        this.checkPro = [];
      }
      if (this.need) {
        this.$emit("choosepro", arr1, this.proList || []);
      } else {
        this.$emit("choosepro", arr1);
      }
    },
    onChange(e) {
      let val = "";
      this.isInit = false;
      if (e instanceof Object) {
        val = this.isOnlyGroup
          ? e.map((item) => Number(String(item).replace("s-", "")))
          : e.map((item) => Number(String(item).replace("t-", "")));
      } else {
        if (e) {
          val = this.isOnlyGroup
            ? Number(String(e).replace("s-", ""))
            : Number(String(e).replace("t-", ""));
        } else {
          val = "";
        }
      }
      if (this.need) {
        this.$emit("choosepro", val, this.proList || []);
      } else {
        this.$emit("choosepro", val);
      }
    },
    // 商品列表
    async getProList() {
      try {
        const res = await getComProduct({
          exclude_domain: this.excludeDomain,
        });
        const temp = res.data.data.list.map((item) => {
          item.key = `t-${item.id}`;
          return item;
        });
        // 过滤没有父级id的商品
        const list = temp.filter((item) => item.product_group_id_second);
        // 过滤是否为代理商品
        this.proList = this.agent
          ? list.filter((item) => item.agent === 1)
          : list;
        return this.proList;
      } catch (error) {}
    },
    // 获取一级分组
    async getFirPro() {
      try {
        const res = await getFirstGroup();
        this.firstGroup = res.data.data.list.map((item) => {
          item.key = `f-${item.id}`;
          return item;
        });
        return this.firstGroup;
      } catch (error) {}
    },
    // 获取二级分组
    async getSecPro() {
      try {
        const res = await getSecondGroup();
        this.secondGroup = res.data.data.list.map((item) => {
          item.key = `s-${item.id}`;
          return item;
        });
        return this.secondGroup;
      } catch (error) {}
    },
    initDisabledPro() {
      // 把禁用的商品id设置为disabled
      this.proData.forEach((item) => {
        item.children.forEach((ele) => {
          ele.children.forEach((child) => {
            child.disabled = this.disabledProList.includes(child.id);
          });
        });
      });
    },
    init() {
      try {
        // 获取商品，一级，二级分组
        Promise.all([
          this.getProList(),
          this.getFirPro(),
          this.getSecPro(),
        ]).then((res) => {
          if (this.isOnlyGroup) {
            // 只显示分组
            this.proData = res[1]
              .map((item) => {
                let secondArr = [];
                res[2].forEach((sItem) => {
                  if (sItem.parent_id === item.id) {
                    secondArr.push(sItem);
                  }
                });
                item.children = secondArr;
                return item;
              })
              .filter((item) => {
                return item.children.length > 0;
              });
          } else {
            // 显示分组和商品
            const fArr = res[1].map((item) => {
              let secondArr = [];
              res[2].forEach((sItem) => {
                if (sItem.parent_id === item.id) {
                  secondArr.push(sItem);
                }
              });
              item.children = secondArr;
              return item;
            });

            setTimeout(() => {
              const temp = fArr.map((item) => {
                item.children.map((ele) => {
                  let temp = [];
                  res[0].forEach((e) => {
                    if (e.product_group_id_second === ele.id) {
                      temp.push(e);
                    }
                  });
                  ele.children = temp;
                  return ele;
                });
                return item;
              });
              // 过滤无子项数据
              this.proData = temp.filter((item) => {
                return (
                  item.children.length > 0 &&
                  item.children.some((el) => {
                    return el.children.length > 0;
                  })
                );
              });
              this.initDisabledPro();
            }, 0);
          }
        });
      } catch (error) {
        this.$message.error(error.data.msg);
      }
    },
  },
};
