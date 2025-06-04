{include file="header"}
<!-- =======内容区域======= -->
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/client.css">
<style>
  .t-popup {
    white-space: pre-wrap;
  }
</style>
<div id="content" class="client-detail hasCrumb" v-cloak>
  <com-config class="no-bg">
    <!-- crumb -->
    <div class="com-crumb">
      <span>{{lang.user_manage}}</span>
      <t-icon name="chevron-right"></t-icon>
      <a href="client.htm" v-permission="'auth_user_list_view'">{{lang.user_list}}</a>
      <t-icon name="chevron-right" v-permission="'auth_user_list_view'"></t-icon>
      <span class="cur">{{lang.personal}}</span>
    </div>
    <t-card class="list-card-container">
      <div class="com-h-box">
        <ul class="common-tab" :class="{ stop: data.status===0}">
          <li class="active" v-permission="'auth_user_detail_personal_information_view'">
            <a>{{lang.personal}}</a>
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
          <li v-permission="'auth_user_detail_info_record_view'">
            <a :href="`${baseUrl}/client_records.htm?id=${id}`">{{lang.info_records}}</a>
          </li>
        </ul>
        <!-- 顶部右侧选择用户 -->
        <com-choose-user :cur-info="data" @changeuser="changeUser" class="com-clinet-choose">
        </com-choose-user>
      </div>
    </t-card>

    <div class="info-card">
      <t-card :class="{ stop: data.status===0}">
        <h3>{{lang.user_text1}}</h3>
        <div class="header-btn">
          <div class="left">
            <!-- 充值按钮 -->
            <t-button theme="primary" @click="showRecharge"
              v-permission="'auth_user_detail_personal_information_recharge'">{{lang.Recharge}}</t-button>
            <!-- 强制变更 -->
            <t-button theme="default" @click="changeMoney('recharge')"
              v-permission="'auth_user_detail_personal_information_change_credit'">{{lang.force_change}}</t-button>
            <div class="com-transparent change_log" @click="changeLog"
              v-permission="'auth_user_detail_personal_information_change_credit_log'">
              <t-button theme="primary">{{lang.change_log}}</t-button>
              <span class="txt">{{lang.change_log}}</span>
            </div>
          </div>
          <t-button theme="primary" type="submit" :disabled="data.status===0" @click="loginByUser"
            v-permission="'auth_user_detail_personal_information_user_login'">{{lang.login_as_user}}</t-button>
        </div>
        <div class="info-box">
          <t-row align="middle">
            <t-col :span="6">
              <span>{{lang.user_text2}}</span>{{thousandth(data.credit)}}{{currency_suffix}}
            </t-col>
            <t-col :span="6">
              <span>{{lang.user_text3}}</span>{{data.host_num}}{{lang.one}}
            </t-col>
          </t-row>
          <t-row align="middle">
            <t-col :span="6">
              <span>{{lang.user_text4}}</span>{{thousandth(data.consume)}}{{currency_suffix}}
            </t-col>
            <t-col :span="6">
              <span>{{lang.user_text5}}</span>{{data.host_active_num}}{{lang.one}}
            </t-col>
          </t-row>
          <t-row align="middle">
            <t-col :span="6">
              <span>{{lang.user_text6}}</span>{{thousandth(calcRefund)}}{{currency_suffix}}
            </t-col>
            <t-col :span="6">
              <span>{{lang.user_text7}}</span>{{moment(data.register_time * 1000).format('YYYY-MM-DD HH:mm:ss')}}
            </t-col>
          </t-row>
          <t-row align="middle">
            <t-col :span="6">
              <span>{{lang.user_text8}}</span>{{thousandth(data.withdraw)}}{{currency_suffix}}
            </t-col>
            <t-col :span="6" v-if="hasCertification">
              <span>{{lang.user_text9}}</span>
              <div v-if="data.certification === false">
                <t-tooltip :content="lang.user_text10" theme="light" :show-arrow="false" placement="top-right">
                  <span style="display: flex; align-items: center;">{{lang.user_text11}}<img
                      src="/{$template_catalog}/template/{$themes}/img/icon/no_authentication.png" alt=""></span>
                </t-tooltip>
              </div>
              <div
                v-else-if="data.certification && data.certification_detail && data.certification_detail.company?.status === 1">
                <t-tooltip :content="lang.user_text12" theme="light" :show-arrow="false" placement="top-right">
                  <span
                    style="display: flex; align-items: center;">{{data.username}}({{data.certification_detail.company.company}})<img
                      src="/{$template_catalog}/template/{$themes}/img/icon/enterprise_authentication.png"
                      alt=""></span>
                </t-tooltip>
              </div>
              <div
                v-else-if="data.certification && data.certification_detail && data.certification_detail.person?.status === 1">
                <t-tooltip :content="lang.user_text13" theme="light" :show-arrow="false" placement="top-right">
                  <span style="display: flex; align-items: center;">{{data.certification_detail.person.card_name}}<img
                      src="/{$template_catalog}/template/{$themes}/img/icon/personal_authentication.png" alt=""></span>
                </t-tooltip>
              </div>
            </t-col>
          </t-row>
          <t-row align="middle" v-if="hasMpWeixinNotice || oauth.length > 0">
            <t-col :span="6" v-if="hasMpWeixinNotice">
              <span>{{lang.product_set_text101}}</span>
              <t-tooltip :content="mp_weixin_notice == 1  ? lang.product_set_text102 : lang.product_set_text103"
                :show-arrow="false" theme="light">
                <div style="display: flex; align-items: center;">
                  <img style="width: 22px; height: 22px;"
                    :src="mp_weixin_notice == 1 ? '/{$template_catalog}/template/{$themes}/img/weixin_notice.svg' : '/{$template_catalog}/template/{$themes}/img/weixin_notice_unbind.svg'"
                    :alt="mp_weixin_notice == 1 ? lang.product_set_text102 : lang.product_set_text103">
                </div>
              </t-tooltip>
            </t-col>
            <t-col :span="6" v-if="oauth.length > 0">
              <span>{{lang.product_set_text100}}</span>
              <div
                style="width: 100%;display: flex; flex-wrap: wrap; align-items: center; row-gap: 5px; column-gap: 5px;">
                <template v-for="item in oauth">
                  <img style="width: 22px; height: 22px;" :src="item" alt="">
                </template>
              </div>
            </t-col>
          </t-row>
        </div>
        <div class="receive-box">
          <div class="item">
            <span class="name">{{lang.whether_receive_sms}}</span>
            <t-switch v-model="formData.receive_sms" :custom-value="[1,0]"
              @change="handleChangeReceive($event, 'receive_sms')">
            </t-switch>
            <t-tooltip :content="lang.receive_sms_tip" :show-arrow="false" theme="light">
              <t-icon name="help-circle" size="18px"></t-icon>
            </t-tooltip>
          </div>
          <div class="item">
            <span class="name">{{lang.whether_receive_mail}}</span>
            <t-switch v-model="formData.receive_email" :custom-value="[1,0]"
              @change="handleChangeReceive($event, 'receive_email')">
            </t-switch>
            <t-tooltip :content="lang.receive_mail_tip" :show-arrow="false" theme="light">
              <t-icon name="help-circle" size="18px"></t-icon>
            </t-tooltip>
          </div>
        </div>
      </t-card>
      <t-card>
        <h3>{{lang.user_text14}}</h3>
        <t-table row-key="1" class="ip-table" :data="data.login_logs" size="medium" :bordered="true"
          :columns="logColumns" :hover="hover" :loading="loading" :table-layout="tableLayout ? 'auto' : 'fixed'">
          <template #login_time="slotProps">
            {{ moment(slotProps.row.login_time * 1000).format('YYYY-MM-DD HH:mm:ss') }}<span
              v-if="slotProps.rowIndex === 0">({{lang.user_text15}})</span>
          </template>
          <template #ip="slotProps">
            {{ slotProps.row.ip }}<span v-if="slotProps.rowIndex === 0">({{lang.user_text15}})</span>
          </template>
        </t-table>
      </t-card>
    </div>

    <t-card class="user-info">
      <h3>{{lang.user_text16}}</h3>
      <t-form :data="formData" label-align="top" layout="inline" :rules="rules" ref="userInfo">
        <t-form-item :label="`${lang.name}${calcDevloper}`" name="username">
          <t-input v-model="formData.username" :placeholder="lang.name"></t-input>
        </t-form-item>
        <t-form-item :label="lang.clinet_level" name="username" v-if="hasPlugin">
          <t-select v-model="formData.level_id" :placeholder="lang.clinet_level" clearable>
            <t-option v-for="item in levelList" :value="item.id" :label="item.name" :key="item.name">
            </t-option>
          </t-select>
        </t-form-item>
        <t-form-item :label="lang.finance_search_text33" name="username" v-if="hasIdcsmart_sale">
          <t-select v-model="idcsmart_sale_id" :placeholder="lang.finance_search_text33" clearable>
            <t-option v-for="item in idcsmart_sale_list" :value="item.id" :label="item.name" :key="item.name">
            </t-option>
          </t-select>
        </t-form-item>

        <t-form-item :label="lang.phone" name="phone" :rules="formData.email ?
          [{ required: false},{pattern: /^\d{0,11}$/, message: lang.verify11 }]:
          [{ required: true,message: lang.input + lang.phone, type: 'error' },
          {pattern: /^\d{0,11}$/, message: lang.verify11 }]">
          <t-select v-model="formData.phone_code" filterable style="width: 100px" :placeholder="lang.phone_code">
            <t-option v-for="item in country" :value="item.phone_code" :label="item.name_zh + '+' + item.phone_code"
              :key="item.name">
            </t-option>
          </t-select>
          <t-input :placeholder="lang.phone" v-model="formData.phone" style="width: calc(100% - 100px);" />
        </t-form-item>
        <t-form-item :label="lang.email" name="email" :rules="formData.phone ?
              [{ required: false },
              {pattern: /^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z_])*@(([0-9a-zA-Z])+([-\w]*[0-9a-zA-Z])*\.)+[a-zA-Z]{1,9})$/,
              message: lang.email_tip, type: 'warning' }]:
              [{ required: true,message: lang.input + lang.email, type: 'error'},
              {pattern: /^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z_])*@(([0-9a-zA-Z])+([-\w]*[0-9a-zA-Z])*\.)+[a-zA-Z]{1,9})$/,
              message: lang.email_tip, type: 'warning' }
              ]">
          <t-input v-model="formData.email" :placeholder="lang.email"></t-input>
        </t-form-item>
        <t-form-item :label="lang.setting_text14" name="operate_password">
          <t-input v-model="formData.operate_password" :placeholder="lang.setting_text14"
            :type="formData.operate_password ? 'password' : 'text'" autocomplete="off"></t-input>
        </t-form-item>
        <t-form-item :label="lang.country" name="country">
          <t-select v-model="formData.country_id" filterable style="width: 100%" :placeholder="lang.country">
            <t-option v-for="item in country" :value="item.id" :label="item.name_zh" :key="item.id">
            </t-option>
          </t-select>
        </t-form-item>
        <t-form-item :label="lang.address" name="address">
          <t-input v-model="formData.address" :placeholder="lang.address" :maxlength="255" show-limit-number></t-input>
        </t-form-item>
        <t-form-item :label="lang.company" name="company">
          <t-input v-model="formData.company" :placeholder="lang.company"></t-input>
        </t-form-item>
        <t-form-item :label="lang.language" name="language">
          <t-select v-model="formData.language" :placeholder="lang.select+lang.language">
            <t-option v-for="item in langList" :value="item.display_lang" :label="item.display_name"
              :key="item.display_lang">
            </t-option>
          </t-select>
        </t-form-item>
        <t-form-item :label="lang.password" name="password">
          <t-input v-model="formData.password" :placeholder="lang.password"
            :type="formData.password ? 'password' : 'text'" autocomplete="off"></t-input>
        </t-form-item>
        <template v-for="item in clientCustomList">
          <t-form-item :label="item.name">
            <t-input v-if="item.type === 'text' || item.type === 'password' || item.type === 'link'"
              v-model="item.value" :placeholder="item.description"
              :type="item.type === 'password' ? 'password' : 'text'">
            </t-input>
            <t-select v-model="item.value" :placeholder="item.description" v-if="item.type === 'dropdown'">
              <t-option v-for="items in item.options" :value="items" :label="items" :key="items">
              </t-option>
            </t-select>
            <t-input-group separate v-if="item.type === 'dropdown_text'" style="width: 100%;">
              <t-select style="width: 100px; flex-shrink: 0;" v-model="item.select_select">
                <t-option v-for="items in item.options" :value="items" :label="items" :key="items">
                </t-option>
              </t-select>
              <t-input v-model="item.select_text" :placeholder="item.description"></t-input>
            </t-input-group>
            <t-checkbox v-if="item.type === 'tickbox'" v-model="item.value">{{item.description}}</t-checkbox>
            <t-textarea v-if="item.type === 'textarea'" :placeholder="lang.description" v-model="item.value" />
          </t-form-item>
        </template>
        <t-form-item :label="lang.notes" name="notes" class="textarea" :maxlength="10000" show-limit-number>
          <t-textarea :placeholder="lang.notes" v-model="formData.notes" />
        </t-form-item>
      </t-form>
      <!-- 底部操作按钮 -->
      <div class="footer-btn">
        <t-button theme="primary" :loading="submitLoading" @click="updateUserInfo" type="submit"
          v-permission="'auth_user_detail_personal_information_save_user_info'">{{lang.hold}}</t-button>
        <div class="com-transparent" @click="changeStatus"
          v-permission="'auth_user_detail_personal_information_deactivate_enable_user'">
          <t-button theme="primary" variant="base">
            <span>{{data.status===0 ? lang.enable :lang.deactivate}}</span>
          </t-button>
          <span class="txt">{{data.status===0 ? lang.enable : lang.deactivate}}</span>
        </div>
        <t-button theme="default" variant="base" @click="deleteUser"
          v-permission="'auth_user_detail_personal_information_delete_user'">{{lang.delete}}</t-button>
      </div>
    </t-card>

    <t-card class="user-info" v-show="childList.length > 0">
      <h3>{{lang.user_text17}}</h3>
      <!-- 子账户 -->
      <div class="login-log chlid-box" style="margin-bottom:40px">
        <t-table row-key="1" :data="childList" size="medium" :bordered="true" :columns="childColumns" :hover="hover"
          :loading="loading" table-layout="auto">
          <template #last_action_time="{row}">
            {{ row.last_action_time>0 ? moment(row.last_action_time * 1000).format('YYYY-MM-DD HH:mm:ss') : '--'}}
          </template>
          <template #caozuo="{row}">
            <span class="edit-text" @click="goEdit(row.id)">{{lang.user_text18}}</span>
          </template>
        </t-table>
        <t-pagination show-jumper v-if="total" :total="total" :page-size="params.limit"
          :page-size-options="logSizeOptions" :on-change="changePage" />
      </div>
    </t-card>

    <!-- 充值弹窗 -->
    <t-dialog :visible.sync="visibleRecharge" :header="lang.Recharge" :footer="false" @close="closeRechorge">
      <t-form :data="rechargeData" :rules="rechargeRules" ref="rechargeRef" :label-width="80" @submit="confirmRecharge"
        v-if="visibleRecharge">
        <!-- 支付方式 -->
        <t-form-item :label="lang.pay_way" name="gateway">
          <t-select v-model="rechargeData.gateway" filterable style="width: 100%"
            :placeholder="lang.select+lang.pay_way">
            <t-option v-for="item in gatewayList" :value="item.name" :label="item.title" :key="item.id">
            </t-option>
          </t-select>
        </t-form-item>
        <!-- 充值金额 -->
        <t-form-item :label="lang.Recharge+lang.money" name="amount">
          <t-input v-model="rechargeData.amount" :placeholder="lang.Recharge+lang.money" :label="currency_prefix">
          </t-input>
        </t-form-item>
        <t-form-item :label="lang.flow">
          <t-input v-model="rechargeData.transaction_number" :placeholder="lang.flow">
          </t-input>
        </t-form-item>
        <t-form-item :label="lang.notes">
          <t-input v-model="rechargeData.notes" :placeholder="lang.notes">
          </t-input>
        </t-form-item>
        <div class="submit-btn">
          <t-button theme="primary" type="submit" :loading="submitLoading">{{lang.sure+lang.Recharge}}</t-button>
          <t-button theme="default" variant="base" @click="closeRechorge">{{lang.cancel}}</t-button>
        </div>
      </t-form>
    </t-dialog>
    <!-- 充值/扣费弹窗 -->
    <t-dialog :header="lang.force_change + lang.money" :visible.sync="visibleMoney" :footer="false" @close="closeMoney">
      <t-form :data="moneyData" :rules="moneyRules" ref="moneyRef" :label-width="80" @submit="confirmMoney"
        v-if="visibleMoney">
        <t-form-item :label="lang.type" name="type">
          <t-select v-model="moneyData.type" :placeholder="lang.select+lang.type">
            <t-option value="recharge" :label="lang.add_money" key="recharge"></t-option>
            <t-option value="deduction" :label="lang.sub_money" key="deduction"></t-option>
          </t-select>
        </t-form-item>
        <t-form-item :label="lang.money" name="amount">
          <t-input v-model="moneyData.amount" :placeholder="lang.money" :label="inputLabel">
          </t-input>
        </t-form-item>
        <t-form-item :label="lang.notes">
          <t-textarea v-model="moneyData.notes" :placeholder="lang.notes" />
        </t-form-item>
        <div class="submit-btn">
          <t-button theme="primary" type="submit" :loading="submitLoading">{{lang.submit}}</t-button>
          <t-button theme="default" variant="base" @click="closeMoney">{{lang.cancel}}</t-button>
        </div>
      </t-form>
    </t-dialog>
    <!-- 变更记录 -->
    <t-dialog :visible="visibleLog" :header="lang.change_log" :footer="false" :on-close="closeLog" width="1000">
      <div slot="body">
        <t-table row-key="change_log" :data="logData" size="medium" :columns="columns" :hover="hover"
          :loading="moneyLoading" table-layout="fixed" max-height="350">
          <template #type="{row}">
            {{lang[row.type]}}
          </template>
          <template #amount="{row}">
            <span>
              <span v-if="row.amount * 1 > 0">+</span>{{row.amount}}
            </span>
          </template>
          <template #create_time="{row}">
            {{moment(row.create_time * 1000).format('YYYY/MM/DD HH:mm')}}
          </template>
          <template #admin_name="{row}">
            {{row.admin_name ? row.admin_name : formData.username}}
          </template>
        </t-table>
        <t-pagination show-jumper v-if="logCunt" :total="logCunt" :page-size="moneyPage.limit"
          :page-size-options="pageSizeOptions" :on-change="changePage" />
      </div>
    </t-dialog>
    <!-- 删除弹窗 -->
    <t-dialog theme="warning" :header="lang.sureDelete" :visible.sync="delVisible">
      <template slot="footer">
        <t-button theme="primary" @click="sureDelUser" :loading="submitLoading">{{lang.sure}}</t-button>
        <t-button theme="default" @click="delVisible=false">{{lang.cancel}}</t-button>
      </template>
    </t-dialog>
    <!-- 启用/停用 -->
    <t-dialog theme="warning" :header="statusTip" :visible.sync="statusVisble">
      <template slot="footer">
        <t-button theme="primary" @click="sureChange" :loading="submitLoading">{{lang.sure}}</t-button>
        <t-button theme="default" @click="statusVisble=false">{{lang.cancel}}</t-button>
      </template>
    </t-dialog>
    <safe-confirm ref="safeRef" :password.sync="admin_operate_password" @confirm="hadelSafeConfirm"></safe-confirm>

  </com-config>
</div>
<!-- =======页面独有======= -->
<script src="/{$template_catalog}/template/{$themes}/components/comChooseUser/comChooseUser.js"></script>
<script src="/{$template_catalog}/template/{$themes}/components/safeConfirm/safeConfirm.js"></script>
<script src="/{$template_catalog}/template/{$themes}/api/common.js"></script>
<script src="/{$template_catalog}/template/{$themes}/api/client.js"></script>
<script src="/{$template_catalog}/template/{$themes}/js/client_detail.js"></script>
{include file="footer"}
