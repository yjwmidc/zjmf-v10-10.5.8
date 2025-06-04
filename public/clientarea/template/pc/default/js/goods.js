var orignalSetItem = localStorage.setItem;
localStorage.setItem = function (key, newValue) {
  var setItemEvent = new Event('setItemEvent');
  setItemEvent.newValue = newValue;
  window.dispatchEvent(setItemEvent);
  orignalSetItem.apply(this, arguments);
};
(function (window, undefined) {
  var old_onload = window.onload
  window.onload = function () {
    const template = document.getElementsByClassName('goods')[0]
    Vue.prototype.lang = window.lang
    window.addEventListener('setItemEvent', function (e) {
      if (e.newValue && String(e.newValue).indexOf('cartNum') !== -1) {
        vm._data.shoppingCarNum = e.newValue.split('-')[1] * 1
      }
    });
    const vm = new Vue({
      components: {
        asideMenu,
        topMenu,
        pagination,
      },
      created() {
        this.id = location.href.split('?')[1].split('=')[1]?.split('&')[0]
        this.getCommonData()
      },
      mounted() {
        if (window.self !== window.top) {
          // 检测到嵌套时该干的事
          this.isIfram = true
        }
        this.getList()
      },
      updated() {
        // // 关闭loading
        document.getElementById('mainLoading').style.display = 'none';
        document.getElementsByClassName('goods')[0].style.display = 'block'
      },
      destroyed() {

      },
      data() {
        return {
          id: '',
          isIfram: false,
          shoppingCarNum: 0,
          params: {
            page: 1,
            limit: 20,
            pageSizes: [20, 50, 100],
            total: 200,
            orderby: 'id',
            sort: 'desc',
            keywords: '',
          },
          commonData: {},
          content: ''
        }
      },
      filters: {
        formateTime(time) {
          if (time && time !== 0) {
            return formateDate(time * 1000)
          } else {
            return "--"
          }
        }
      },
      methods: {
        async getList() {
          try {
            const params = { id: this.id, flag: this.isIfram }
            const res = await getOrederConfig(params)
            this.$nextTick(() => {
              // 解决Jquery加载JS会在文件末尾添加时间戳的问题 
              $.ajaxSetup({
                cache: true
              })
              $('.config-box .content').html(res.data.data.content)
            })
            this.content = res.data.data.content
          } catch (error) {

          }
        },
        // 每页展示数改变
        sizeChange(e) {
          this.params.limit = e
          this.params.page = 1
          // 获取列表
        },
        // 当前页改变
        currentChange(e) {
          this.params.page = e

        },

        // 获取通用配置
        getCommonData() {
          this.commonData = JSON.parse(localStorage.getItem("common_set_before"))
        }
      },

    }).$mount(template)
    typeof old_onload == 'function' && old_onload()
  };
})(window);
