{include file="header"}
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/common/viewer.min.css">
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/client.css">
<!-- =======内容区域======= -->
<div id="content" class="client_records hasCrumb" v-cloak>
  <com-config>
    <div class="com-crumb">
      <span>{{lang.user_manage}}</span>
      <t-icon name="chevron-right"></t-icon>
      <a href="client.htm" v-permission="'auth_user_list_view'">{{lang.user_list}}</a>
      <t-icon name="chevron-right" v-permission="'auth_user_list_view'"></t-icon>
      <span class="cur">{{lang.operation}}{{lang.log}}</span>
    </div>
    <t-card class="list-card-container">
      <div class="com-h-box">
        <ul class="common-tab">
          <li v-permission="'auth_user_detail_personal_information_view'">
            <a :href="`${baseUrl}/client_detail.htm?id=${id}`">{{lang.personal}}</a>
          </li>
          <li v-permission="'auth_user_detail_host_info_view'">
            <a :href="`${baseUrl}/client_host.htm?id=${id}`">{{lang.product_info}}</a>
          </li>
          <li v-permission="'auth_user_detail_order_view'">
            <a :href="`${baseUrl}/client_order.htm?id=${id}`">{{lang.order_manage}}</a>
          </li>
          <li v-permission="'auth_user_detail_transaction_view'">
            <a :href="`${baseUrl}/client_transaction.htm?id=${id}`">{{lang.flow}}</a>
          </li>
          <li v-permission="'auth_user_detail_operation_log'">
            <a :href="`${baseUrl}/client_log.htm?id=${id}`">{{lang.operation}}{{lang.log}}</a>
          </li>
          <li
            v-if="$checkPermission('auth_user_detail_notification_log_sms_notification') || $checkPermission('auth_user_detail_notification_log_email_notification')">
            <a
              :href="`${baseUrl}/${($checkPermission('auth_user_detail_notification_log_sms_notification') ? 'client_notice_sms' : 'client_notice_email')}.htm?id=${id}`">{{lang.notice_log}}</a>
          </li>
          <li v-if="hasNewTicket && $checkPermission('auth_user_detail_ticket_premium_view')">
            <a :href="`${baseUrl}/plugin/ticket_premium/client_ticket.htm?id=${id}`">{{lang.auto_order}}</a>
          </li>
          <li v-if="!hasNewTicket && hasTicket && $checkPermission('auth_user_detail_ticket_view')">
            <a :href="`${baseUrl}/plugin/idcsmart_ticket/client_ticket.htm?id=${id}`">{{lang.auto_order}}</a>
          </li>
          <li v-if="hasRecommend ">
            <a
              :href="`${baseUrl}/plugin/idcsmart_recommend/client_recommend.htm?id=${id}`">{{lang.data_export_tip9}}</a>
          </li>
          <li class="active" v-permission="'auth_user_detail_info_record_view'">
            <a href="javascript:;">{{lang.info_records}}</a>
          </li>
        </ul>
        <!-- 顶部右侧选择用户 -->
        <com-choose-user :cur-info="clientDetail" @changeuser="changeUser" class="com-clinet-choose">
        </com-choose-user>
      </div>
      <t-button :disabled="totalUpdate" @click="addRecord"
        v-permission="'auth_user_detail_info_record_create_record'">{{lang.add_records}}</t-button>
      <t-loading :loading="recordLoading" size="small">
        <div class="record-list">
          <div class="r-item" v-for="(item,index) in recordsList" :key="index">
            <t-icon name="time" class="time-icon" size="16"></t-icon>
            <div class="top">
              <p class="left">
                <span class="time">{{moment(item.create_time * 1000).format('YYYY-MM-DD HH:mm:ss')}}</span>
                <span class="user">{{item.admin_name}}</span>
              </p>
              <div class="opt" v-if="!(item.edit && optType === 'add')">
                <t-icon name="edit" size="18" class="edit" @click="editItem(item)"
                  v-if="!totalUpdate && $checkPermission('auth_user_detail_info_record_update_record')"></t-icon>
                <t-icon name="delete" size="18" class="del" @click="delItem(item)"
                  v-permission="'auth_user_detail_info_record_delete_record'"></t-icon>
              </div>
            </div>
            <div class="con">
              <t-form :data="recordFrom" :rules="rules" ref="record" @submit="confirmRecord">
                <div class="des" v-show="!item.edit && item.content">{{item.content}}</div>
                <div v-show="item.edit">
                  <t-form-item label=" ">
                    <t-textarea v-model="recordFrom.content" :placeholder="`${lang.client_info}`" name="description"
                      :maxlength="300" :autosize="{ minRows: 3, maxRows: 5 }" />
                  </t-form-item>
                </div>
                <div class="file" v-if="(!item.edit && item.attachment.length > 0) || item.edit">
                  <div class="left">
                    <div v-show="item.edit">{{lang.order_attachment}}：</div>
                    <t-upload theme="custom" multiple v-model="recordFrom.attachment" :before-upload="beforeUploadfile"
                      v-show="item.edit" draggable :action="uploadUrl" :format-response="formatResponse"
                      :headers="uploadHeaders" theme="file" @fail="handleFail" @progress="uploadProgress"
                      @success="uploadSuccess">
                      <div class="upload-tip">
                        <span>{{lang.records_upload_tip}}</span>
                      </div>
                    </t-upload>
                    <div class="f-item" v-for="(el,ind) in item.attachment" :key="ind">
                      <t-icon name="attach" size="16"></t-icon>
                      <span class="name"
                        @click="downloadfile(el,el.split('^')[1])">{{typeof el === 'string' ? el.split('^')[1] :el.response.save_name.split('^')[1] }}</span>
                      <t-icon class="delfile" name="close" size="16" color="#ccc" @click.native="delfiles(index,ind)"
                        v-if="item.edit">
                      </t-icon>
                    </div>
                  </div>
                  <div class="submit" v-show="item.edit">
                    <t-button theme="primary" type="submit" class="submit-btn" :loading="submitLoading"
                      :disabled="!recordFrom.content && recordFrom.attachment.length === 0">{{lang.sure}}</t-button>
                    <t-button theme="default" variant="base" @click="cancelItem(item)">{{lang.cancel}}</t-button>
                  </div>
                </div>
              </t-form>
            </div>
          </div>
        </div>
      </t-loading>
      <p class="loading">{{loadingText}}</p>
      <!-- 删除弹窗 -->
      <t-dialog theme="warning" :header="lang.sureDelete" :visible.sync="delVisible">
        <template slot="footer">
          <t-button theme="primary" @click="sureDelUser" :loading="submitLoading">{{lang.sure}}</t-button>
          <t-button theme="default" @click="delVisible=false">{{lang.cancel}}</t-button>
        </template>
      </t-dialog>
      <!-- 图片预览 -->
      <div>
        <img id="viewer" :src="preImg" alt="">
      </div>
    </t-card>
  </com-config>
</div>
<!-- =======页面独有======= -->
<script src="/{$template_catalog}/template/{$themes}/components/comChooseUser/comChooseUser.js"></script>
<script src="/{$template_catalog}/template/{$themes}/js/common/viewer.min.js"></script>
<script src="/{$template_catalog}/template/{$themes}/api/client.js"></script>
<script src="/{$template_catalog}/template/{$themes}/js/client_records.js"></script>
{include file="footer"}
