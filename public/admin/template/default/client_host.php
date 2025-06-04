{include file="header"}
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/client.css">
<!-- =======内容区域======= -->
<div id="content" class="host hasCrumb" v-cloak>
  <com-config>
    <!-- crumb -->
    <div class="com-crumb">
      <span>{{lang.user_manage}}</span>
      <t-icon name="chevron-right"></t-icon>
      <a href="client.htm" v-permission="'auth_user_list_view'">{{lang.user_list}}</a>
      <t-icon name="chevron-right" v-permission="'auth_user_list_view'"></t-icon>
      <span class="cur">{{lang.product_info}}</span>
    </div>
    <t-card class="list-card-container">
      <div class="com-h-box">
        <ul class="common-tab">
          <li v-permission="'auth_user_detail_personal_information_view'">
            <a :href="`${baseUrl}/client_detail.htm?id=${id}`">{{lang.personal}}</a>
          </li>
          <li class="active" v-permission="'auth_user_detail_host_info_view'">
            <a href="javascript:;">{{lang.product_info}}</a>
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
        <com-choose-user :cur-info="clientDetail" @changeuser="changeUser" class="com-clinet-choose">
        </com-choose-user>
      </div>
      <div class="common-header">
        <div class="flex">
          <t-button @click="batchRenew" class="add"
            v-if="hasPlugin && $checkPermission('auth_user_detail_host_info_batch_renew')">{{lang.batch_renew}}
          </t-button>
          <t-button @click="batchDel" class="add"
            v-permission="'auth_user_detail_host_info_batch_delete'">{{lang.batch_dele}}
          </t-button>
        </div>
        <div class="right-search" style="margin-top: 0;">
          <div class="flex view-filed" v-show="!isAdvance">
            <t-select v-model="searchType" class="com-list-type" @change="changeType">
              <t-option v-for="item in typeOption" :value="item.value" :label="item.label" :key="item.value">
              </t-option>
            </t-select>
            <t-input v-model="params.keywords" class="search-input" :placeholder="lang.input"
              @keypress.enter.native="search" clearable v-show="searchType !== 'product_id' && searchType !== 'status'"
              @clear="clearKey('keywords')" :maxlength="30" show-limit-number>
            </t-input>
            <com-tree-select v-show="searchType === 'product_id'" :value="params.product_id" @choosepro="choosePro">
            </com-tree-select>
            <t-select v-show="searchType === 'status'" v-model="params.status" :placeholder="lang.client_care_label29"
              clearable>
              <t-option v-for="item in productStatus" :value="item.value" :label="item.label" :key="item.value">
              </t-option>
            </t-select>
            <t-button @click="search">{{lang.query}}</t-button>
          </div>
          <t-button @click="changeAdvance" style="margin-left: 20px;">
            {{isAdvance ? lang.pack_up : lang.advanced_filter}}
          </t-button>
        </div>
      </div>
      <div class="advanced" v-show="isAdvance">
        <div class="search">
          <t-input v-model="params.host_id" class="search-input" :placeholder="`${lang.input}${lang.tailorism}ID`"
            @keypress.enter.native="search" clearable @clear="clearKey('host_id')">
          </t-input>
          <com-tree-select :value="params.product_id" @choosepro="choosePro" class="search-input"
            style="width: auto;"></com-tree-select>
          <t-input v-model="params.name" class="search-input" :placeholder="`${lang.input}${lang.products_token}`"
            @keypress.enter.native="search" clearable @clear="clearKey('name')">
          </t-input>
          <!-- 到期时间 -->
          <t-select v-model="params.due_time" :placeholder="lang.please_choose_due" clearable>
            <t-option v-for="item in dueTimeArr" :value="item.value" :label="item.label" :key="item.value">
            </t-option>
          </t-select>
          <t-input v-model="params.first_payment_amount" class="search-input"
            :placeholder="`${lang.input}${lang.buy_amount}`" @keypress.enter.native="search" clearable
            @clear="clearKey('first_payment_amount')">
          </t-input>
          <t-input v-model="params.ip" class="search-input" :placeholder="`${lang.input}IP`"
            @keypress.enter.native="search" clearable @clear="clearKey('ip')">
          </t-input>
          <!-- 产品状态 -->
          <t-select v-model="params.status" :placeholder="lang.client_care_label29" clearable>
            <t-option v-for="item in productStatus" :value="item.value" :label="item.label" :key="item.value">
            </t-option>
          </t-select>
          <t-button @click="search">{{lang.query}}</t-button>
        </div>
      </div>
      <t-table row-key="id" :data="data" size="medium" :columns="columns" :hover="hover" :loading="loading" resizable
        :table-layout="tableLayout ? 'auto' : 'fixed'" :hide-sort-tips="true" @sort-change="sortChange"
        @select-change="rehandleSelectChange" :selected-row-keys="checkId">
        <template slot="sortIcon">
          <t-icon name="caret-down-small"></t-icon>
        </template>
        <template #id="{row}">
          <span class="aHover" @click="goHostDetail(row)"
            v-if="$checkPermission('auth_user_detail_host_info_host_detail')">{{row.id}}</span>
          <span v-else>{{row.id}}</span>
        </template>
        <template #product_name="{row}">
          <div class="com-pro-name">
            <span class="aHover" @click="goHostDetail(row)"
              v-if="$checkPermission('auth_user_detail_host_info_host_detail')">{{row.product_name}}</span>
            <span v-else>{{row.product_name}}</span>
          </div>
          <span class="com-base-info" v-if="row.base_info">{{row.base_info}}</span>
        </template>
        <template #renew_amount="{row}">
          <template v-if="row.billing_cycle">
            {{currency_prefix}}&nbsp;{{row.renew_amount}}<span>/</span>{{calcCycle(row.billing_cycle_name)}}
          </template>
          <template v-else>
            {{currency_prefix}}&nbsp;{{row.first_payment_amount}}/{{lang.onetime}}
          </template>
        </template>
        <template #status="{row}">
          <t-tag theme="default" variant="light" v-if="row.status==='Cancelled'"
            class="canceled">{{lang.canceled}}</t-tag>
          <t-tag theme="warning" variant="light" v-if="row.status==='Unpaid'">{{lang.Unpaid}}</t-tag>
          <t-tag theme="primary" variant="light" v-if="row.status==='Pending'">{{lang.Pending}}</t-tag>
          <t-tag theme="success" variant="light" v-if="row.status==='Active'">{{lang.Active}}</t-tag>
          <t-tag theme="danger" variant="light" v-if="row.status==='Failed'">{{lang.Failed}}</t-tag>
          <t-tag theme="default" variant="light" v-if="row.status==='Suspended'">{{lang.Suspended}}</t-tag>
          <t-tag theme="default" variant="light" v-if="row.status==='Deleted'" class="delted">{{lang.Deleted}}
          </t-tag>
        </template>
        <template #name="{row}">
          {{row.name}}
        </template>
        <template #ip_num="{row}">
          {{row.allIp[0] || '--'}}
          <t-popup placement="top" trigger="hover">
            <template #content>
              <div class="ips">
                <p v-for="(item,index) in row.allIp" :key="index">
                  {{item}}
                  <svg class="common-look" @click="copyIp(item)">
                    <use xlink:href="#icon-copy">
                    </use>
                  </svg>
                </p>
              </div>
            </template>
            <span v-if="row.ip_num > 1 && $checkPermission('auth_business_host_check_host_detail')" class="showIp">
              ({{row.ip_num}})
            </span>
          </t-popup>
          <svg class="common-look" v-if="row.ip_num > 0 && $checkPermission('auth_business_host_check_host_detail')"
            @click="copyIp(row.allIp)">
            <use xlink:href="#icon-copy">
            </use>
          </svg>
          <span v-if="row.ip_num > 1 && !$checkPermission('auth_business_host_check_host_detail')" class="showIp"
            style="cursor: inherit;">
            ({{row.ip_num}})
          </span>
        </template>
        <template #active_time="{row}">
          <span
            v-if="row.status !== 'Unpaid'">{{row.active_time ===0 ? '-' : moment(row.active_time * 1000).format('YYYY/MM/DD HH:mm')}}</span>
        </template>
        <template #due_time="{row}">
          <span
            v-if="row.status !== 'Unpaid'">{{row.due_time ===0 ? '-' : moment(row.due_time * 1000).format('YYYY/MM/DD HH:mm')}}</span>
        </template>
        <template #op="{row}">
          <a class="common-look" @click="deltePro(row)">{{lang.delete}}</a>
        </template>
        <template #footer-summary>
          <div class="page-total-amount" v-if="total">
            <div class="amount-item">
              {{lang.page_total_renew_amount}}：<span
                class="amount-num">{{currency_prefix}}{{page_total_renew_amount}}</span>
            </div>
            <div class="amount-item">
              {{lang.total_renew_amount}}：<span class="amount-num">{{currency_prefix}}{{total_renew_amount}}</span>
            </div>
          </div>
        </template>
      </t-table>
      <t-pagination show-jumper :total="total" :page-size="params.limit" :current="params.page"
        :page-size-options="pageSizeOptions" :on-change="changePage" />
    </t-card>
    <!-- 删除 -->
    <t-dialog theme="warning" :header="lang.delHostTips" :close-btn="false" :visible.sync="delVisible">
      <t-checkbox v-model="module_delete">{{lang.delHostCheck}}</t-checkbox>
      <template slot="footer">
        <div class="common-dialog">
          <t-button @click="onConfirm" :loading="submitLoading">{{lang.sure}}</t-button>
          <t-button theme="default" @click="delVisible=false">{{lang.cancel}}</t-button>
        </div>
      </template>
    </t-dialog>
    <!-- 批量续费弹窗 -->
    <t-dialog :header="lang.batch_renew" :visible.sync="renewVisible" :footer="false" placement="center"
      @close="cancelRenew" class="renew-dialog">
      <t-table row-key="1" :data="renewList" size="medium" :columns="renewColumns" :hover="hover"
        :table-layout="tableLayout ? 'auto' : 'fixed'" :loading="renewLoading">
        <template slot="sortIcon">
          <t-icon name="caret-down-small"></t-icon>
        </template>
        <template #product_name="{row}">
          {{row.product_name}}({{row.name}})
        </template>
        <template #billing_cycles="{row}">
          <t-select v-model="row.curCycle" :popup-props="popupProps" v-if="row.billing_cycles.length > 0"
            @change="changeCycle(row)">
            <t-option v-for="(item,index) in row.billing_cycles" :value="item.billing_cycle" :key="index"
              :label="item.billing_cycle"></t-option>
          </t-select>
          <span v-else class="no-renew">{{lang.renew_tip}}</span>
        </template>
        <template #renew_amount="{row}">
          {{currency_prefix}}&nbsp;{{row.renew_amount}}
        </template>
      </t-table>

      <div class="com-f-btn">
        <div class="total">{{lang.total}}：<span class="price"><span
              class="symbol">{{currency_prefix}}</span>{{renewTotal}}</span></div>
        <div>
          <t-checkbox v-model="pay">{{lang.mark_Paid}}</t-checkbox>
        </div>
        <t-button theme="primary" @click="submitRenew" :loading="submitLoading"
          :disabled="renewList.length === 0">{{lang.sure_renew}}</t-button>
      </div>
    </t-dialog>
    <safe-confirm ref="safeRef" :password.sync="admin_operate_password" @confirm="hadelSafeConfirm"></safe-confirm>
  </com-config>
</div>
<script src="/{$template_catalog}/template/{$themes}/components/comTreeSelect/comTreeSelect.js"></script>
<script src="/{$template_catalog}/template/{$themes}/components/comChooseUser/comChooseUser.js"></script>
<script src="/{$template_catalog}/template/{$themes}/components/safeConfirm/safeConfirm.js"></script>
<script src="/{$template_catalog}/template/{$themes}/api/client.js"></script>
<script src="/{$template_catalog}/template/{$themes}/js/client_host.js"></script>
{include file="footer"}
