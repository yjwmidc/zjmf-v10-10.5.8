(function (window, undefined) {
  var old_onload = window.onload;
  window.onload = function () {
    const template = document.getElementsByClassName("template")[0];
    Vue.prototype.lang = Object.assign(window.lang, window.plugin_lang);
    const host = location.origin;
    const fir = location.pathname.split("/")[1];
    const str = `${host}/${fir}/`;
    new Vue({
      components: {
        comConfig,
        comTinymce,
      },
      data() {
        return {
          // 加载中
          pageLoading: false,
          // 删除对话框
          deleteVisible: false,
          replayVisible: false,
          sendLoading: false,
          isAddNotes: false,
          viewer: null,
          deliveryVisible: false,
          // 添加备注按钮的loading
          addNotesLing: false,
          top: "top",
          currency_prefix:
            JSON.parse(localStorage.getItem("common_set")).currency_prefix ||
            "¥",
          currency_suffix:
            JSON.parse(localStorage.getItem("common_set")).currency_suffix ||
            "元",
          // 工单详情
          isEditing: false, // 是否正在编辑模式
          orderDetailData: {},
          delivery_operate_type: {
            1: lang.order_text81,
            2: lang.order_text82,
            3: lang.order_text83,
          },
          deliveryLoading: false,
          params: {
            selectHostList: [], // 选择的产品
            listStr: "",
            ticket_type: "", // 产品类型
            status: "", // 工单状态
          },
          // 回复内容
          replyData: "",
          editObj: {}, // 正在编辑的对象
          // 产品列表
          hostList: [],
          // 编辑按钮loading
          editLoding: false,
          // 订单类型列表
          order_status_options: [],
          // 回复列表
          replyList: [],
          product_obj_list: [],
          prereplyList: [],
          // 日志列表
          logList: [],
          logVisible: false,
          columns: [
            {
              colKey: "description",
              title: lang.order_text39,
              cell: "description",
              ellipsis: true,
            },
            {
              colKey: "create_time",
              title: lang.order_text40,
              cell: "create_time",
              width: "157",
            },
          ],
          // 工单状态下拉框数据
          orderTypeList: [],
          // 上传附件
          attachmentList: [],
          // 预览图片地址
          preImg: "",
          // 上传附件headers设置
          uploadHeaders: {
            Authorization: "Bearer" + " " + localStorage.getItem("backJwt"),
          },
          uploadTip: "",
          img_visible: false,
          baseURL: url,
          uploadUrl: str + "v1/upload",
          /** 非受控用法：与分页组件对齐（此处注释为非受控用法示例，代码有效，勿删） */
          pagination: {
            current: 1,
            pageSize: 10,
            total: 0,
            showJumper: true,
          },
        };
      },
      computed: {
        avatar() {
          return (type) => {
            return type === "Client"
              ? `${this.baseURL}img/client.png`
              : `${this.baseURL}img/admin.png`;
          };
        },
      },
      methods: {
        formatResponse(res) {
          if (res.status !== 200) {
            this.$message.error(res.msg);
            return {error: res.msg};
          }
          return {save_name: res.data.save_name, url: res.data.image_url};
        },
        // 分页变化时触发该事件
        onPageChange(pageInfo, newData) {
          // 受控用法所需
          this.pagination.current = pageInfo.current;
          this.pagination.pageSize = pageInfo.pageSize;
        },
        // 点击预设回复弹窗
        usePrePlay(item) {
          tinyMCE.editors[0].setContent(item.content);
          this.replayVisible = false;
        },
        // 点击图片
        hanldeImage(event) {
          if (
            event.target.nodeName == "IMG" ||
            event.target.nodeName == "img"
          ) {
            const img = event.target.currentSrc;
            this.preImg = img;
            this.viewer.show();
          }
        },
        // 跳转用户信息页
        goUserPagr() {
          const url =
            str +
            `client_detail.htm?client_id=${this.orderDetailData.client_id}`;
          window.open(url);
        },
        goClientPage(id) {
          if (id) {
            const url = str + `client_detail.htm?client_id=${id}`;
            window.open(url);
          }
        },
        // 获取工单预设回复列表
        getTicketPrereply() {
          ticketPrereply()
            .then((res) => {
              this.prereplyList = res.data.data.list;
            })
            .catch((err) => {
              this.$message.error(err.data.msg);
            });
        },
        goList() {
          window.history.go(-1);
        },
        goProductDetail(item) {
          const url =
            str + `host_detail.htm?client_id=${item.client_id}&id=${item.id}`;
          window.open(url);
        },
        // 确认添加备注
        handelAddNotes() {
          this.addNotesLing = true;
          const content = tinyMCE.editors[0].getContent();
          const params = {
            id: this.orderDetailData.id,
            content: content,
          };
          addTicketNotes(params)
            .then((res) => {
              this.getOrderDetailData();
              tinyMCE.editors[0].setContent("");
              this.isAddNotes = false;
            })
            .catch((error) => {
              this.$message.warning({
                content: error.data.msg,
                placement: "top-right",
              });
            })
            .finally(() => {
              this.addNotesLing = false;
            });
        },
        // 取消添加备注
        cancelAddNotes() {
          tinyMCE.editors[0].setContent("");
          this.isAddNotes = false;
        },
        // 备注列表
        getTicketNotes() {
          const str = location.search.substr(1).split("&");
          const orderId = str[0].split("=")[1];

          ticketNotes(orderId)
            .then((res) => {
              this.notesList = res.data.data.list;
              const arr = this.orderDetailData.replies.concat(this.notesList);
              arr.sort((a, b) => {
                return a.create_time - b.create_time;
              });
              arr.forEach((item) => {
                item.isShowBtn = false;
                if (!item.type) {
                  item.type = "notes";
                }
              });
              const arrEntities = {
                lt: "<",
                gt: ">",
                nbsp: " ",
                amp: "&",
                quot: '"',
              };
              this.replyList = arr.map((item) => {
                item.content = filterXSS(
                  item.content.replace(
                    /&(lt|gt|nbsp|amp|quot);/gi,
                    function (all, t) {
                      return arrEntities[t];
                    }
                  )
                ).replace(/&(lt|gt|nbsp|amp|quot);/gi, function (all, t) {
                  return arrEntities[t];
                });
                item.content = item.content.replaceAll(
                  'http-equiv="refresh"',
                  ""
                );
                return item;
              });
            })
            .catch((err) => {
              console.log(err);
              this.replyList = this.orderDetailData.replies.concat([]);
              this.$message.error(err.data.msg);
            })
            .finally(() => {
              this.pageLoading = false;
              this.$nextTick(() => {
                this.scrollBotton();
              });
            });
        },
        // 编辑消息
        editItem(item) {
          this.editObj = item;
          this.isEditing = true;
          this.replyData = item.content;
          this.$refs.comTinymce.setContent(item.content);
          tinyMCE.editors[0].editorManager.get("tiny").focus();
          this.handleScrollBottom();
        },
        // 聊天列表滚动到底部
        scrollBotton() {
          const listDom = document.querySelector(".reply-list");
          const listBoxDom = document.querySelector(".t-list__inner");
          const h = listBoxDom.scrollHeight;
          listDom.scrollTop = h;
        },
        // 滚动到底部
        handleScrollBottom() {
          const detailDom = document.querySelector(".area");
          detailDom.scrollTop = detailDom.scrollHeight;
        },
        // 确认编辑
        handelEdit() {
          this.editLoding = true;
          const conten = tinyMCE.editors[0].getContent();
          if (this.editObj.type === "notes") {
            const params = {
              ticket_id: this.editObj.ticket_id,
              id: this.editObj.id,
              content: conten,
            };
            notesReplyEdit(params)
              .then((result) => {
                this.getOrderDetailData();
                tinyMCE.editors[0].setContent("");
                this.isEditing = false;
              })
              .catch((error) => {
                this.$message.warning({
                  content: error.data.msg,
                  placement: "top-right",
                });
              })
              .finally(() => {
                this.editLoding = false;
              });
          } else {
            const params = {
              id: this.editObj.id,
              content: conten,
            };
            ticketReplyEdit(this.editObj.id, params)
              .then((result) => {
                this.getOrderDetailData();
                tinyMCE.editors[0].setContent("");
                this.isEditing = false;
              })
              .catch((error) => {
                this.$message.warning({
                  content: error.data.msg,
                  placement: "top-right",
                });
              })
              .finally(() => {
                this.editLoding = false;
              });
          }
        },
        copyIp(row) {
          const allIp = (row.dedicate_ip + "," + row.assign_ip).replace(
            /,/g,
            "\n"
          );
          copyText(allIp);
        },
        // 点击添加备注
        addNotes() {
          this.isAddNotes = true;
          tinyMCE.editors[0].editorManager.get("tiny").focus();
        },
        // 取消编辑
        cancelEdit() {
          this.editObj = {};
          this.replyData = "";
          this.$refs.comTinymce.setContent("");
          tinyMCE.editors[0].setContent("");
          this.isEditing = false;
        },
        // 点击删除按钮
        deleteItem(item) {
          if (this.isEditing) {
            this.$message.error(lang.order_text41);
            return;
          }
          this.editObj = item;
          this.deleteVisible = true;
        },
        // 删除弹窗确认
        handelDelete() {
          if (this.editObj.type === "notes") {
            const params = {
              ticket_id: this.editObj.ticket_id,
              id: this.editObj.id,
            };
            orderNotesDelete(params)
              .then((result) => {
                this.getOrderDetailData();
                this.$message.success({
                  content: result.data.msg,
                  placement: "top-right",
                });
              })
              .catch((err) => {
                this.$message.error(err.data.msg);
              })
              .finally(() => {
                this.deleteVisible = false;
              });
          } else {
            const params = {
              id: this.editObj.id,
            };
            orderReplyDelete(params)
              .then((result) => {
                this.getOrderDetailData();
                this.$message.success({
                  content: result.data.msg,
                  placement: "top-right",
                });
              })
              .catch((err) => {
                this.$message.error(err.data.msg);
              })
              .finally(() => {
                this.deleteVisible = false;
              });
          }
        },
        handelDeliveryOperate() {
          this.deliveryLoading = true;
          const subApi =
            this.orderDetailData.delivery_operate === 1
              ? apiDeliveryManual
              : this.orderDetailData.delivery_operate === 2
              ? apiDeliveryActive
              : this.orderDetailData.delivery_operate === 3
              ? apiDeliveryTerminate
              : apiDeliveryManual;
          subApi({
            id: this.orderDetailData.id,
          })
            .then((res) => {
              this.deliveryLoading = false;
              this.deliveryVisible = false;
              this.$message.success(res.data.msg);
              this.getOrderDetailData();
            })
            .catch((err) => {
              this.deliveryLoading = false;
              this.$message.error(err.data.msg);
            });
        },

        // 提交回复
        submitReply() {
          this.sendLoading = true;
          const conten = tinyMCE.editors[0].getContent();
          const attachmentList = [];
          this.attachmentList.forEach((item) => {
            attachmentList.push(item.response.save_name);
          });
          const params = {
            id: this.orderDetailData.id,
            content: conten,
            attachment: attachmentList,
          };
          replyUserOrder(this.orderDetailData.id, params)
            .then((result) => {
              tinyMCE.editors[0].setContent("");
              this.attachmentList = [];
              this.getOrderDetailData();
            })
            .catch((error) => {
              this.$message.warning({
                content: error.data.msg,
                placement: "top-right",
              });
            })
            .finally(() => {
              this.sendLoading = false;
            });
        },
        goback() {
          location.href = "index.htm";
        },
        // 工单-转内部-关联产品变化
        hostChange() {
          this.$forceUpdate();
        },
        // 上传附件-返回内容
        uploadFormatResponse(res) {
          if (!res || res.status !== 200) {
            return {error: lang.upload_fail};
          }
          return {...res, save_name: res.data.save_name};
        },
        // 修改工单状态
        handelEditOrderStatus() {
          if (this.params.status == "") {
            return this.$message.warning({
              content: lang.order_text42,
              placement: "top-right",
            });
          }
          const str = location.search.substr(1).split("&");
          const orderId = str[0].split("=")[1];
          const obj = {
            id: orderId,
            status: this.params.status,
            ticket_type_id: this.params.ticket_type,
            host_ids: [this.params.listStr],
          };
          this.editLoding = true;
          editOrderStatus(obj)
            .then((result) => {
              if (obj.status == 4) {
                this.goList();
              } else {
                this.$message.success({
                  content: result.data.msg,
                  placement: "top-right",
                });
                this.getOrderDetailData();
              }
              this.editLoding = false;
            })
            .catch((err) => {
              this.editLoding = false;
              this.$message.error(err.data.msg);
            });
        },
        handelLog() {
          this.pagination.current = 1;
          this.pagination.pageSize = 10;
          this.getTicketLog();
          this.logVisible = true;
        },
        // 上传附件-进度
        uploadProgress(val) {
          if (val.percent) {
            this.uploadTip = "uploaded" + val.percent + "%";
            if (val.percent === 100) {
              this.uploadTip = "";
            }
          }
        },
        // 上传附件-成功后
        uploadSuccess(res) {
          if (
            res.fileList.filter((item) => item.name == res.file.name).length > 1
          ) {
            this.$message.warning({
              content: lang.upload_same_name,
              placement: "top-right",
            });
            this.attachmentList.splice(this.attachmentList.length - 1, 1);
          }
          this.$forceUpdate();
        },
        // 删除已上传附件
        removeAttachment(file, i) {
          this.attachmentList.splice(i, 1);
          this.$forceUpdate();
        },
        // 下载文件
        downFile(res, title) {
          let url = res.lastIndexOf("/");
          res = res.substring(url + 1, res.length);
          downloadFile({name: res})
            .then(function (response) {
              const blob = new Blob([response.data]);
              const fileName = title;
              const linkNode = document.createElement("a");
              linkNode.download = fileName; //a标签的download属性规定下载文件的名称
              linkNode.style.display = "none";
              linkNode.href = URL.createObjectURL(blob); //生成一个Blob URL
              document.body.appendChild(linkNode);
              linkNode.click(); //模拟在按钮上的一次鼠标单击
              URL.revokeObjectURL(linkNode.href); // 释放URL 对象
              document.body.removeChild(linkNode);
            })
            .catch(function (error) {
              console.log(error);
              this.$message.error(error.data.msg);
            });
        },
        // 附件下载
        clickFile(item) {
          const name = item.name;
          const url = item.url;
          const type = name.substring(name.lastIndexOf(".") + 1);
          if (
            [
              "png",
              "jpg",
              "jpeg",
              "bmp",
              "webp",
              "PNG",
              "JPG",
              "JPEG",
              "BMP",
              "WEBP",
            ].includes(type)
          ) {
            this.preImg = url;
            this.viewer.show();
          } else {
            const downloadElement = document.createElement("a");
            downloadElement.href = url;
            downloadElement.download = name; // 下载后文件名
            document.body.appendChild(downloadElement);
            downloadElement.click(); // 点击下载
          }
        },
        timeago(time) {
          if (time == 0) {
            return "--";
          }
          // time 毫秒
          const dateTimeStamp = time;
          const minute = 1000 * 60; //把分，时，天，周，半个月，一个月用毫秒表示
          const hour = minute * 60;
          const day = hour * 24;
          const week = day * 7;
          const month = day * 30;
          const year = month * 12;
          const now = new Date().getTime(); //获取当前时间毫秒
          const diffValue = now - dateTimeStamp; //时间差

          let result = "";
          if (diffValue < 0) {
            result = "" + lang.order_text43;
          }
          const minC = diffValue / minute; //计算时间差的分，时，天，周，月
          const hourC = diffValue / hour;
          const dayC = diffValue / day;
          const weekC = diffValue / week;
          const monthC = diffValue / month;
          const yearC = diffValue / year;

          if (yearC >= 1) {
            result = " " + parseInt(yearC) + lang.order_text44;
          } else if (monthC >= 1 && monthC < 12) {
            result = " " + parseInt(monthC) + lang.order_text45;
          } else if (weekC >= 1 && weekC < 5 && dayC > 6 && monthC < 1) {
            result = " " + parseInt(weekC) + lang.order_text46;
          } else if (dayC >= 1 && dayC <= 6) {
            result = " " + parseInt(dayC) + lang.order_text47;
          } else if (hourC >= 1 && hourC <= 23) {
            result = " " + parseInt(hourC) + lang.order_text48;
          } else if (minC >= 1 && minC <= 59) {
            result = " " + parseInt(minC) + lang.order_text49;
          } else if (diffValue >= 0 && diffValue <= minute) {
            result = lang.order_text50;
          }
          return result;
        },
        goBotoom() {
          // .area 平滑的滑到最底部
          const listDom = document.querySelector(".area");
          const h = listDom.scrollHeight;
          listDom.scrollTo({left: 0, top: h, behavior: "smooth"});
        },
        // 工单日志
        getTicketLog() {
          const str = location.search.substr(1).split("&");
          const orderId = str[0].split("=")[1];
          ticketLog(orderId)
            .then((res) => {
              this.logList = res.data.data.list;
              this.pagination.total = res.data.data.list.length;
            })
            .catch((err) => {
              this.$message.error(err.data.msg);
            });
        },
        // 获取工单详情
        async getOrderDetailData() {
          this.pageLoading = true;
          const str = location.search.substr(1).split("&");
          const orderId = str[0].split("=")[1];
          const result = await getUserOrderDetail(orderId);
          if (result.status === 200) {
            this.orderDetailData = result.data.data.ticket;
            this.orderDetailData.distanceTime = this.timeago(
              this.orderDetailData.last_reply_time * 1000
            );
            this.getOrderTypeName();
            this.getHostsName();
            this.getTicketStatus();
            this.getTicketNotes();
          }
        },
        // 获取当前工单类型名称
        getOrderTypeName() {
          getUserOrderType()
            .then((result) => {
              const orderTypeList = result.data.data.list;
              this.orderTypeList = orderTypeList;
              const orderType = orderTypeList.filter(
                (item) => item.id === this.orderDetailData.ticket_type_id
              )[0];
              this.params.ticket_type = orderType ? orderType.id : null;
            })
            .catch((err) => {
              this.$message.error(err.data.msg);
            });
        },
        // 获取当前用户关联产品名称
        getHostsName() {
          getHost({
            client_id: this.orderDetailData.client_id,
            page: 1,
            limit: 999999999,
          })
            .then((result) => {
              const data = result.data.data.list;
              data.forEach((item) => {
                item.showName = item.product_name + "(" + item.name + ")";
              });
              this.hostList = data;
              const arr = [];
              this.product_obj_list = [];
              this.orderDetailData.host_ids.forEach((id) => {
                data.forEach((item) => {
                  if (item.id == id) {
                    arr.push(item.id);
                    this.product_obj_list.push(item);
                  }
                });
              });
              this.params.selectHostList = [...arr];
              this.params.listStr = arr[0] || "";
            })
            .catch((err) => {
              this.$message.error(err.data.msg);
            });
        },
        // 获取工单状态列表
        getTicketStatus() {
          ticketStatus()
            .then((res) => {
              res.data.data.list.forEach((item) => {
                // if (item['default'] === 1) {
                //   this.order_status.push(item.id)
                // }
                if (item.name === this.orderDetailData.status) {
                  this.params.status = item.id;
                }
                delete item["default"];
              });
              this.order_status_options = res.data.data.list;
            })
            .catch((err) => {
              this.$message.error(err.data.msg);
            });
        },
        // 时间格式转换
        formatDate(dateStr) {
          const date = new Date(dateStr * 1000);
          const str1 = [
            date.getFullYear(),
            date.getMonth() + 1,
            date.getDate(),
          ].join("-");
          const str2 = [
            this.formatDateAdd0(date.getHours()),
            this.formatDateAdd0(date.getMinutes()),
          ].join(":");
          return str1 + " " + str2;
        },
        formatDateAdd0(m) {
          return m < 10 ? "0" + m : m;
        },
        initTemplate() {
          tinymce.init({
            selector: "#tiny",
            language_url: "/tinymce/langs/zh_CN.js",
            language:
              localStorage.getItem("backLang") === "zh-cn" ? "zh_CN" : "en_US",
            min_height: 400,
            width: "100%",
            plugins:
              "link lists image code table colorpicker textcolor wordcount contextmenu fullpage paste",
            toolbar:
              "bold italic underline strikethrough | fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent blockquote | undo redo | link unlink image fullpage code | removeformat",
            images_upload_url: str + "v1/upload",
            paste_data_images: true,
            convert_urls: false,
            //粘贴图片后，自动上传
            urlconverter_callback: function (url, node, on_save, name) {
              return url;
            },
            images_upload_handler: this.handlerAddImg,
          });
        },
        handlerAddImg(blobInfo, success, failure) {
          return new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append("file", blobInfo.blob());
            axios
              .post(`${location.protocol}//${str}v1/upload`, formData, {
                headers: {
                  Authorization:
                    "Bearer" + " " + localStorage.getItem("backJwt"),
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
        mouseOver(val) {
          val.isShowBtn = true;
          this.$forceUpdate();
        },
        mouseLeave(val) {
          val.isShowBtn = false;
          this.$forceUpdate();
        },
        initViewer() {
          this.viewer = new Viewer(document.getElementById("viewer"), {
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
      },
      created() {
        this.getOrderDetailData();
        this.getTicketLog();
        this.getTicketPrereply();
      },
      mounted() {
        this.initTemplate();
        this.initViewer();
      },
    }).$mount(template);
    typeof old_onload == "function" && old_onload();
  };
})(window);
