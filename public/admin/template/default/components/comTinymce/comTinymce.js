/* 富文本 */
const str = `${location.origin}/${location.pathname.split("/")[1]}/`;
const comTinymce = {
  template: `<textarea :id="id" name="content" :placeholder="prePlaceholder" v-html="calStr"></textarea>`,
  data() {
    return {
      content: "",
    };
  },
  computed: {},
  mounted() {
    this.initTemplate();
  },
  props: {
    readonly: {
      default() {
        return false;
      },
    },
    prePlaceholder: {
      default() {
        return "";
      },
    },
    id: {
      default() {
        return "tiny";
      },
    },
    height: {
      default() {
        return 400;
      },
    },
  },
  computed: {
    calStr() {
      const temp = this.content && this.content.replace(/&amp;/g, "");
      return temp;
    },
  },
  watch: {
    content(val) {
      tinymce.editors[this.id].setContent(val);
    },
  },
  created() {},
  beforeDestroy() {
    // 防止富文本在弹窗中第二次 渲染失败！！！
    tinymce.editors[this.id].destroy();
  },
  methods: {
    /* 父组件调用 */
    // 获取富文本内容
    getContent() {
      return tinyMCE.activeEditor.getContent();
    },
    // 设置富文本内容
    setContent(content) {
      this.content = content;
    },
    /* 父组件调用 end */
    initTemplate() {
      let curLang = "";
      switch (localStorage.getItem("backLang")) {
        case "zh-cn":
          curLang = "zh_CN";
          break;
        case "zh-hk":
          curLang = "zh_HK";
          break;
        default:
          curLang = "en_US";
      }
      tinymce.init({
        selector: `#${this.id}`,
        language_url: `/tinymce/langs/${curLang}.js`,
        language: curLang,
        min_height: this.height,
        width: "100%",
        plugins:
          "link lists formatpainter  code table colorpicker textcolor wordcount contextmenu fullpage paste fullscreen image",
        toolbar:
          "bold italic formatpainter underline strikethrough | fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent blockquote | undo redo | link unlink image fullpage code | removeformat | fullscreen",
        images_upload_url: str + "v1/upload",
        readonly: this.readonly,
        convert_urls: false,
        deprecation_warnings: false, // 去除控制台版本警告
        end_container_on_empty_block: true,
        powerpaste_word_import: "propmt", // 参数可以是propmt, merge, clear，效果自行切换对比
        powerpaste_html_import: "propmt", // propmt, merge, clear
        powerpaste_allow_local_images: true,
        paste_data_images: true,
        forced_root_block: "",
        images_upload_handler: this.handlerAddImg,
        init_instance_callback: () => {
          // 初始化完成执行设置内容
          // tinymce.editors["tiny"].setContent(this.content);
        },
      });
    },
    handlerAddImg(blobInfo, success, failure) {
      return new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append("file", blobInfo.blob());
        axios
          .post(str + "v1/upload", formData, {
            headers: {
              Authorization: "Bearer" + " " + localStorage.getItem("backJwt"),
            },
            params: {
              move: 1,
            },
          })
          .then((res) => {
            const json = {};
            if (res.status !== 200) {
              failure("HTTP Error: " + res.data.msg);
              return;
            }
            // json = JSON.parse(res)
            json.location = res.data.data?.image_url;
            if (!json || typeof json.location !== "string") {
              failure("Error:" + res.data.msg);
              return;
            }
            success(json.location);
          });
      });
    },
  },
};
