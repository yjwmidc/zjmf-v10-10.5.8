{include file="header"}
<!-- =======内容区域======= -->
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/client.css">
<div id="content" class="client-order hasCrumb" v-cloak>
  <com-config>
    <div class="com-crumb">
      <span>{{lang.user_manage}}</span>
      <t-icon name="chevron-right"></t-icon>
      <a href="client.htm" v-permission="'auth_user_list_view'">{{lang.user_list}}</a>
      <t-icon name="chevron-right" v-permission="'auth_user_list_view'"></t-icon>
      <span class="cur">{{lang.order_manage}}</span>
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
          <li class="active" v-permission="'auth_user_detail_order_view'">
            <a href="javascript:;">{{lang.order_manage}}</a>
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
        <div class="right-create-order">
          <t-select v-model="params.host_id" :placeholder="lang.tailorism" clearable @change="getClientList()"
            @clear="getClientList()" style="width: 240px;margin-right: 20px;">
            <t-option v-for="item in hostArr" :value="item.id" :label="item.product_name" :key="item.id"></t-option>
          </t-select>
          <t-button @click="addOrder"
            v-permission="'auth_user_detail_order_create_order'">{{lang.create_order}}</t-button>
        </div>
      </div>
      <t-enhanced-table ref="table" row-key="id" drag-sort="row-handler" :data="data" :columns="columns"
        :tree="{ childrenKey: 'list', treeNodeColumnIndex: 0 }" :loading="loading" class="user-order"
        :hide-sort-tips="true" :key="new Date().getTime()">
        <template slot="sortIcon">
          <t-icon name="caret-down-small"></t-icon>
        </template>
        <template #id="{row}">
          <!--  @click="itemClick(row)"  :class="{'com-no-child': row.order_item_count <= 1}" -->
          <span @click="lookDetail(row.id)" v-if="row.type" class="aHover">
            <!-- <t-icon :name="row.isExpand ? 'caret-up-small' : 'caret-down-small'" v-if="row.order_item_count > 1">
          </t-icon> -->
            {{row.id}}
          </span>
          <span v-else class="child">-</span>
        </template>
        <template #type="{row}">
          {{lang[row.type]}}
        </template>
        <template #create_time="{row}">
          {{row.type ? moment(row.create_time * 1000).format('YYYY/MM/DD HH:mm') : ''}}
        </template>
        <template #icon="{row}">
          <t-tooltip :content="lang[row.type]" theme="light" :show-arrow="false" placement="top-right">
            <img :src="`${rootRul}img/icon/${row.type}.png`" alt="" style="position: relative; top: 3px;">
          </t-tooltip>
        </template>
        <template #product_names={row}>
          <template v-if="row.product_names">
            <div v-if="row.description">
              <t-tooltip theme="light" :show-arrow="false" placement="top-right">
                <div slot="content" class="tool-content">{{row.description}}</div>
                <!--  <span @click="itemClick(row)" class="hover">{{row.product_names[0]}}</span> -->
                <span class="aHover" @click="lookDetail(row.id)">{{row.product_names[0]}}</span>
                <span v-if="row.product_names.length>1"
                  class="hover">{{lang.wait}}{{row.product_names.length}}{{lang.products}}</span>
              </t-tooltip>
            </div>
            <div v-else>
              <span class="aHover" @click="lookDetail(row.id)">{{row.product_names[0]}}</span>
              <span v-if="row.product_names.length>1"
                class="hover">{{lang.wait}}{{row.product_names.length}}{{lang.products}}</span>
            </div>
          </template>
          <span v-else class="child-name">
            <t-tooltip theme="light" :show-arrow="false" placement="top-right">
              <div slot="content" class="tool-content">{{row.description}}</div>
              <!-- <a :href="row.host_id ? `host_detail.htm?client_id=${father_client_id}&id=${row.host_id}` : 'javascript:;'" class="aHover">{{row.product_name ? row.product_name : row.description}}
              <span class="host-name" v-if="row.host_name">({{row.host_name}})</span>
            </a> -->
              <span @click="lookDetail(father_order_id)"
                class="aHover">{{row.product_name ? row.product_name : row.description}}
                <span class="host-name" v-if="row.host_name">({{row.host_name}})</span>
              </span>
            </t-tooltip>
          </span>
        </template>
        <template #amount="{row}">
          {{currency_prefix}}&nbsp;{{row.amount}}
          <!-- 升降机为退款时不显示周期 -->
          <span v-if="row.billing_cycle && Number(row.amount) >= 0">/{{row.billing_cycle}}</span>
        </template>
        <template #status="{row}">
          <t-tag theme="default" variant="light" v-if="(row.status || row.host_status)==='Cancelled'"
            class="canceled order-canceled">{{lang.canceled}}
          </t-tag>
          <t-tag theme="default" variant="light" v-if="(row.status || row.host_status)==='Refunded'"
            class="canceled order-refunded">{{lang.refunded}}
          </t-tag>
          <t-tag theme="warning" variant="light" v-if="(row.status || row.host_status)==='Unpaid'"
            class="order-unpaid">{{lang.Unpaid}}
          </t-tag>
          <t-tag theme="primary" variant="light" v-if="row.status==='Paid'" class="order-paid">{{lang.Paid}}
          </t-tag>
          <t-tag theme="primary" variant="light" v-if="row.host_status === 'Pending'">
            {{lang.Pending}}
          </t-tag>
          <t-tag theme="success" variant="light" v-if="(row.status || row.host_status)==='Active'">{{lang.Active}}
          </t-tag>
          <t-tag theme="danger" variant="light" v-if="(row.status || row.host_status)==='Failed'">{{lang.Failed}}
          </t-tag>
          <t-tag theme="default" variant="light" v-if="(row.status || row.host_status)==='Suspended'">
            {{lang.Suspended}}
          </t-tag>
          <t-tag theme="default" variant="light" v-if="(row.status || row.host_status)==='Deleted'"
            class="delted">{{lang.Deleted}}
          </t-tag>
          <t-tag theme="warning" variant="light" v-if="row.status ==='WaitUpload'">{{lang.order_wait_upload}}</t-tag>
          <t-tag theme="warning" variant="light" v-if="row.status ==='WaitReview'">{{lang.order_wait_review}}</t-tag>
          <t-tag theme="danger" variant="light" v-if="row.status ==='ReviewFail'">{{lang.order_review_fail}}</t-tag>
        </template>
        <template #gateway="{row}">
          <template v-if="row.status === 'Unpaid'">
            --
          </template>
          <template v-else>
            <!-- 其他支付方式 -->
            <template v-if="row.credit == 0">
              {{row.gateway}}
            </template>
            <!-- 混合支付 -->
            <template v-if="row.credit * 1 >0 && row.credit * 1 < row.amount * 1 && row.gateway_sign !== 'credit'">
              <t-tooltip :content="currency_prefix+row.credit" theme="light" placement="bottom-right">
                <span class="theme-color">{{lang.balance_pay}}</span>
              </t-tooltip>
              <span>{{row.gateway ? '+ ' + row.gateway: '' }}</span>
            </template>
            <template v-if="row.gateway_sign === 'credit'">
              <span>{{lang.balance_pay}}</span>
            </template>
          </template>
        </template>
        <template #op="{row}">
          <template v-if="row.type">
            <t-tooltip :content="`${lang.look}${lang.detail}`" :show-arrow="false" theme="light">
              <t-icon name="view-module" class="common-look" @click="lookDetail(row.id)"
                v-permission="'auth_user_detail_order_check_order'"></t-icon>
            </t-tooltip>
            <t-tooltip :content="lang.update_price" :show-arrow="false" theme="light">
              <t-icon name="money-circle" @click="updatePrice(row, 'order')" class="common-look"
                v-if="row.status!=='Paid' && row.status!=='Cancelled' && row.status!=='Refunded' && $checkPermission('auth_user_detail_order_adjust_order_amount')"></t-icon>
            </t-tooltip>
            <t-tooltip :content="lang.delete" :show-arrow="false" theme="light">
              <t-icon name="delete" @click="delteOrder(row)" class="common-look"
                v-permission="'auth_user_detail_order_delete_order'"></t-icon>
            </t-tooltip>
          </template>
          <template v-else>
            <t-tooltip :content="lang.edit" :show-arrow="false" theme="light" v-if="row.edit">
              <t-icon name="edit" size="18px" @click="updatePrice(row, 'sub')" class="common-look"></t-icon>
            </t-tooltip>
          </template>
        </template>
        <template #footer-summary>
          <div class="page-total-amount" v-if="total">
            <div class="amount-item">
              {{lang.page_total_amount}}：<span class="amount-num">{{currency_prefix}}{{page_total_amount}}</span>
            </div>
            <div class="amount-item">
              {{lang.total_amount}}：<span class="amount-num">{{currency_prefix}}{{total_amount}}</span>
            </div>
          </div>
        </template>
      </t-enhanced-table>
      <t-pagination show-jumper v-if="total" :total="total" :page-size="params.limit" :current="params.page"
        :page-size-options="pageSizeOptions" :on-change="changePage" />
    </t-card>
    <!-- 调整价格 -->
    <t-dialog :header="lang.update_price" :visible.sync="priceModel" :footer="false" @close="closePrice">
      <t-form :data="formData" ref="priceForm" @submit="onSubmit" :rules="rules">
        <t-form-item :label="lang.change_money" name="amount">
          <t-input v-model="formData.amount" type="tel" :label="currency_prefix"
            :placeholder="lang.update_price_tip"></t-input>
        </t-form-item>
        <t-form-item :label="lang.description" name="description">
          <t-textarea :placeholder="lang.description" v-model="formData.description" />
        </t-form-item>
        <div class="com-f-btn">
          <t-button theme="primary" type="submit" :loading="submitLoading">{{lang.sure}}</t-button>
          <t-button theme="default" variant="base" @click="priceModel=false">{{lang.cancel}}</t-button>
        </div>
      </t-form>
    </t-dialog>
    <!-- 删除 -->
    <t-dialog :header="lang.deleteOrder" :visible.sync="delVisible" class="delDialog" width="600">
      <template slot="body">
        <p>
          <t-icon name="error-circle" size="18" style="color:var(--td-warning-color);"></t-icon>
          &nbsp;&nbsp;{{lang.sureDelete}}
        </p>
        <div class="check">
          <t-checkbox v-model="delete_host"></t-checkbox>
          <div class="tips">
            <p class="tit">{{lang.deleteOrderTip1}}<span class="com-red">{{lang.deleteOrderTip3}}</span></p>
            <p class="tip">({{lang.deleteOrderTip2}})</p>
          </div>
        </div>
      </template>
      <template slot="footer">
        <div class="common-dialog">
          <t-button @click="onConfirm" :loading="submitLoading">{{lang.sure}}</t-button>
          <t-button theme="default" @click="delVisible=false">{{lang.cancel}}</t-button>
        </div>
      </template>
    </t-dialog>
    <!-- 标记支付 -->
    <t-dialog :header="lang.sign_pay" :visible.sync="payVisible" width="600" class="sign_pay">
      <template slot="body">
        <t-form :data="signForm">
          <t-form-item :label="lang.order_amount">
            <t-input :label="currency_prefix" v-model="signForm.amount" disabled />
          </t-form-item>
          <t-form-item :label="lang.balance_paid">
            <t-input :label="currency_prefix" v-model="signForm.credit" disabled />
          </t-form-item>
          <t-form-item :label="lang.no_paid">
            <t-input :label="currency_prefix" v-model="(signForm.amount * 1).toFixed(2)" disabled />
          </t-form-item>
          <t-checkbox v-model="use_credit" class="checkDelete">{{lang.use_credit}}</t-checkbox>
        </t-form>
      </template>
      <template slot="footer">
        <div class="common-dialog">
          <t-button @click="sureSign" :loading="submitLoading">{{lang.sure}}</t-button>
          <t-button theme="default" @click="payVisible=false">{{lang.cancel}}</t-button>
        </div>
      </template>
    </t-dialog>
    <safe-confirm ref="safeRef" :password.sync="admin_operate_password" @confirm="hadelSafeConfirm"></safe-confirm>
  </com-config>
</div>
<!-- =======页面独有======= -->
<script src="/{$template_catalog}/template/{$themes}/components/comChooseUser/comChooseUser.js"></script>
<script src="/{$template_catalog}/template/{$themes}/components/safeConfirm/safeConfirm.js"></script>
<script src="/{$template_catalog}/template/{$themes}/api/client.js"></script>
<script src="/{$template_catalog}/template/{$themes}/js/client_order.js"></script>
{include file="footer"}
