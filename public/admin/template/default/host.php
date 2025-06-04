{include file="header"}
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/client.css">
<!-- =======内容区域======= -->
<div id="content" class="host" v-cloak>
  <com-config>
    <t-card class="list-card-container">
      <ul class="common-tab">
        <li v-for="item in tabList" :key="item.value" :class="params.tab === item.value ? 'active' : ''"
          @click="changeHostTab(item.value)">
          <a href="javascript:;">{{item.label }}</a>
        </li>
      </ul>
      <div class="common-header">
        <div class="flex">
          <template v-if="params.tab !== 'failed'">
            <t-button @click="batchDel" class="add" v-if="$checkPermission('auth_user_detail_host_info_batch_delete')">
              {{lang.batch_dele}}
            </t-button>
            <t-button theme="success" :loading="exportLoading" class="add" @click="exportVisible = true"
              v-if="$checkPermission('auth_business_host_export_excel') && hasExport">
              {{lang.data_export}}
            </t-button>
            <t-button :loading="pullLoading" @click="handlePull">
              {{lang.data_export_tip10}}
            </t-button>
          </template>
        </div>
        <div class="right-search" v-if="params.tab !== 'failed'">
          <div class="flex view-filed" v-if="!isAdvance">
            <!-- 选择视图 -->
            <t-tooltip :show-arrow="false" theme="light" placement="top-left" overlay-class-name="view-change-tip">
              <template slot="content">
                <p>{{lang.field_tip1}}</p>
                <p>{{lang.field_tip2}}</p>
                <p>{{lang.field_tip3}}</p>
                <p>{{lang.field_tip4}}</p>
              </template>
              <t-icon name="help-circle" class="view-tip"></t-icon>
            </t-tooltip>
            <!-- :class="{'not-default': params.view_id !== defaultId}" -->
            <t-select :value="params.view_id" :label="lang.view_filed + ':'" class="choose-view not-default"
              @change="chooseView" :popup-props="{ overlayClassName: `view-select`}">
              <t-option v-for="item in admin_view_list" :value="item.id" :label="item.name" :key="item.id">
              </t-option>
              <div class="bot-opt" slot="panelBottomContent">
                <t-option key="new" value="new" :label="lang.field_add_view"></t-option>
                <t-option key="manage" value="manage" :label="lang.field_manage_view"></t-option>
              </div>
            </t-select>
            <!-- 选择视图 end -->
            <t-select v-model="searchType" class="com-list-type" @change="changeType">
              <t-option v-for="item in calcTypeSelect" :value="item.value" :label="item.label" :key="item.value">
              </t-option>
            </t-select>
            <t-input v-model="params.keywords" class="search-input" :placeholder="lang.input"
              @keypress.enter.native="search" clearable v-show="searchType !== 'product_id' && searchType !== 'sale'"
              @clear="clearKey('keywords')" :maxlength="30" show-limit-number>
            </t-input>
            <input class="com-empty-input"></input>
            <com-tree-select v-show="searchType === 'product_id'" :value="params.product_id" @choosepro="choosePro">
            </com-tree-select>
            <!-- 选择销售 -->
            <t-select v-model="curSaleId" :placeholder="lang.please_choose_sale" clearable
              v-show="hasSale && searchType === 'sale'">
              <t-option v-for="item in allSales" :value="item.id" :label="item.name" :key="item.name">
              </t-option>
            </t-select>
            <t-button @click="search">{{lang.query}}</t-button>
          </div>
          <div class="view-filed" v-if="isAdvance" style="margin-right: -10px;">
            <t-tooltip :show-arrow="false" theme="light" placement="top-left" overlay-class-name="view-change-tip">
              <template slot="content">
                <p>{{lang.field_tip1}}</p>
                <p>{{lang.field_tip2}}</p>
                <p>{{lang.field_tip3}}</p>
                <p>{{lang.field_tip4}}</p>
              </template>
              <t-icon name="help-circle" class="view-tip"></t-icon>
            </t-tooltip>
            <t-select :value="params.view_id" :label="lang.view_filed + ':'" class="choose-view not-default"
              @change="chooseView" :popup-props="{ overlayClassName: `view-select`}">
              <t-option v-for="item in admin_view_list" :value="item.id" :label="item.name" :key="item.id">
              </t-option>
              <div class="bot-opt" slot="panelBottomContent">
                <t-option key="new" value="new" :label="lang.field_add_view"></t-option>
                <t-option key="manage" value="manage" :label="lang.field_manage_view"></t-option>
              </div>
            </t-select>
          </div>
          <t-button @click="changeAdvance" style="margin-left: 8px;">
            {{isAdvance ? lang.pack_up : lang.advanced_filter}}
          </t-button>
          <com-view-filed view="host" @changefield="changeField" ref="customFiled"></com-view-filed>
        </div>
        <!-- 手动处理 -->
        <div class="right-search" v-else>
          <t-select v-model="params.action" :placeholder="lang.choose_failed_action" @change="search" clearable
            @clear="search">
            <t-option v-for="item in failAction" :value="item.value" :label="item.label" :key="item.value">
            </t-option>
          </t-select>
          <t-input v-model="params.keywords" class="failed-input" :placeholder="lang.failed_tip1"
            @keypress.enter.native="search" clearable @clear="clearKey('keywords')" :maxlength="30" show-limit-number>
          </t-input>
          <t-button @click="search">{{lang.query}}</t-button>
        </div>
      </div>
      <div class="advanced" v-show="isAdvance">
        <div class="edit-view">
          <t-button class="add" v-if="viewFiledNum && data_range_switch"
            @click="handleEditView">{{lang.view_data_range}}({{viewFiledNum}})</t-button>
        </div>
        <div class="search">
          <!-- <t-select v-model="searchType" class="com-list-type" @change="changeType">
                    <t-option v-for="item in typeOption" :value="item.value" :label="item.label" :key="item.value"></t-option>
                  </t-select>
                  <t-input v-model="params.keywords" class="search-input" :placeholder="lang.input" @keypress.enter.native="search"
                  clearable v-show="searchType !== 'product_id'" @clear="clearKey('keywords')">
                  </t-input>
                  <com-tree-select class="search-input" v-show="searchType === 'product_id'" :value="params.product_id" @choosepro="choosePro"></com-tree-select>
                  <t-input v-model="params.username" class="search-input" :placeholder="`${lang.input}${lang.username}`"
                  @keypress.enter.native="search" clearable @clear="clearKey('username')">
                  </t-input>
                  <t-input v-model="params.billing_cycle" class="search-input" :placeholder="`${lang.input}${lang.payment_cycle}`"
                  @keypress.enter.native="search" clearable @clear="clearKey('billing_cycle')">
                  </t-input>
                  <t-select v-model="params.status" :placeholder="lang.client_care_label29" clearable v-show="params.tab === 'using' || params.tab === 'overdue' || params.tab === ''">
                    <t-option v-for="item in calcStatus(productStatus)" :value="item.value" :label="item.label" :key="item.value">
                    </t-option>
                  </t-select>
                  <t-select v-model="params.server_id" :placeholder="lang.product_type" clearable>
                    <t-option v-for="item in serverList" :value="item.id" :label="item.name" :key="item.id">
                    </t-option>
                  </t-select>
                  <t-date-range-picker allow-input clearable v-model="range" :placeholder="[`${lang.due_time}`,`${lang.due_time}`]"></t-date-range-picker> -->

          <t-input v-model="params.host_id" class="search-input" :placeholder="`${lang.input}${lang.tailorism}ID`"
            @keypress.enter.native="search" clearable @clear="clearKey('host_id')">
          </t-input>
          <t-input v-model="params.username" class="search-input" :placeholder="`${lang.input}${lang.username}`"
            @keypress.enter.native="search" clearable @clear="clearKey('username')">
          </t-input>
          <com-tree-select :value="params.product_id" @choosepro="choosePro" class="search-input"
            style="width: auto;"></com-tree-select>
          <!-- 选择模块 -->
          <t-select v-model="params.module" :placeholder="lang.nav_text9" clearable @clear="clearKey('module')">
            <t-option v-for="item in moduleList" :value="item.name" :label="item.display_name" :key="item.name">
            </t-option>
          </t-select>
          <!-- 选择模块 end -->
          <t-input v-model="params.name" class="search-input" :placeholder="`${lang.input}${lang.products_token}`"
            @keypress.enter.native="search" clearable @clear="clearKey('name')">
          </t-input>
          <!-- 到期时间 -->
          <!-- <t-select v-model="params.due_time" :placeholder="lang.please_choose_due" clearable>
            <t-option v-for="item in dueTimeArr" :value="it
            em.value" :label="item.label" :key="item.value">
            </t-option>
          </t-select> -->
          <t-date-range-picker allow-input clearable v-model="range" enable-time-picker :presets="presets">
          </t-date-range-picker>
          <t-input v-model="params.first_payment_amount" class="search-input"
            :placeholder="`${lang.input}${lang.buy_amount}`" @keypress.enter.native="search" clearable
            @clear="clearKey('first_payment_amount')">
          </t-input>
          <t-input v-model="params.ip" class="search-input" :placeholder="`${lang.input}IP`"
            @keypress.enter.native="search" clearable @clear="clearKey('ip')">
          </t-input>
          <!-- 选择销售 -->
          <t-select v-model="curSaleId" :placeholder="lang.please_choose_sale" clearable v-show="hasSale">
            <t-option v-for="item in allSales" :value="item.id" :label="item.name" :key="item.name">
            </t-option>
          </t-select>
          <t-button @click="search">{{lang.query}}</t-button>
        </div>
      </div>
      <t-table row-key="id" :data="calcList" size="medium" :columns="params.tab === 'failed' ? manualColumns : columns"
        resizable :hover="hover" :loading="loading" :table-layout="tableLayout ? 'auto' : 'fixed'"
        :hide-sort-tips="true" @sort-change="sortChange" @column-resize-change="resizeChange"
        @select-change="rehandleSelectChange" :selected-row-keys="checkId">
        <template slot="sortIcon">
          <t-icon name="caret-down-small"></t-icon>
        </template>
        <template #sale="{row}">
          <span>{{row.sale || '--'}}</span>
        </template>
        <template #username_company="{row}">
          <t-tooltip :show-arrow="false" :content="calcDevloper(row.developer_type)" theme="light"
            v-if="row.developer_type">
            <svg class="common-look">
              <use xlink:href="/{$template_catalog}/template/{$themes}/img/icon/icons.svg#cus-developer">
              </use>
            </svg>
          </t-tooltip>
          <login-by-user :id="row.client_id" :website_url="website_url">
            <a :href="`client_detail.htm?client_id=${row?.client_id}`" class="aHover">
              <span v-if="row.client_name">{{row.client_name}}</span>
              <span v-if="row.company">({{row.company}})</span>
            </a>
          </login-by-user>
        </template>
        <template #client_id="{row}">
          <a :href="`client_detail.htm?client_id=${row.client_id}`" class="aHover">{{row.client_id}}</a>
        </template>
        <template #certification="{row}">
          <t-tooltip :show-arrow="false" theme="light">
            <span slot="content">{{!row.certification ? lang.real_tip8 : row.certification_type === 'person' ?
                      lang.real_tip9 : lang.real_tip10}}</span>
            <t-icon :class="row.certification ? 'green-icon' : ''"
              :name="!row.certification ? 'user-clear': row.certification_type === 'person' ? 'user' : 'usergroup'" />
          </t-tooltip>
        </template>
        <template #client_status="{row}">
          <t-tag theme="success" class="com-status" v-if="row.status" variant="light">{{lang.enable}}</t-tag>
          <t-tag theme="danger" class="com-status" v-else variant="light">{{lang.deactivate}}</t-tag>
        </template>
        <template #renew_amount_cycle="{row}">
          <template v-if="row.billing_cycle">
            {{currency_prefix}}&nbsp;{{row.renew_amount | filterMoney}}<span>/</span>{{row.billing_cycle_name}}
          </template>
          <template v-else>
            {{currency_prefix}}&nbsp;{{row.first_payment_amount}}/{{lang.onetime}}
          </template>
        </template>
        <template #first_payment_amount="{row}">
          {{currency_prefix}}&nbsp;{{row.first_payment_amount | filterMoney}}
        </template>
        <template #base_price="{row}">
          {{currency_prefix}}&nbsp;{{row.base_price}}
        </template>
        <template #billing_cycle="{row}">
          {{billingCycle[row.billing_cycle]}}
        </template>
        <template #reg_time="{row}">
          {{row.reg_time ? moment(row.reg_time * 1000).format('YYYY-MM-DD HH:mm') : ''}}
        </template>
        <template #product_name_status="{row}">
          <div class="com-pro-name">
            <t-tag theme="default" variant="light" v-if="row.status==='Cancelled'"
              class="canceled">{{lang.canceled}}</t-tag>
            <t-tag theme="warning" variant="light" v-if="row.status==='Unpaid'">{{lang.Unpaid}}</t-tag>
            <t-tag theme="primary" variant="light" v-if="row.status==='Pending'">{{lang.Pending}}</t-tag>
            <t-tag theme="success" variant="light" v-if="row.status==='Active'">{{lang.Active}}</t-tag>
            <t-tag theme="danger" variant="light" v-if="row.status==='Failed'">{{lang.Failed}}</t-tag>
            <t-tag theme="default" variant="light" v-if="row.status==='Suspended'">{{lang.Suspended}}</t-tag>
            <t-tag theme="default" variant="light" v-if="row.status==='Deleted'" class="delted">{{lang.Deleted}}
            </t-tag>
            <a :href="`host_detail.htm?client_id=${row.client_id}&id=${row.id}`" @click="goHostDetail(row)"
              class="aHover" v-if="$checkPermission('auth_business_host_check_host_detail')" style="margin-left: 3px;">
              {{row.product_name}}
            </a>
            <span v-else style="margin-left: 3px;">{{row.product_name}}</span>
          </div>
          <span class="com-base-info" v-if="row.base_info">{{row.base_info}}</span>
        </template>
        <template #ip="{row}">
          {{row.dedicate_ip || '--'}}
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
        <template #host_name="{row}">
          {{row.name}}
        </template>
        <template #id="{row}">
          <a :href="`host_detail.htm?client_id=${row.client_id}&id=${row.id}`" @click="goHostDetail(row)" class="aHover"
            v-if="$checkPermission('auth_business_host_check_host_detail')">
            {{row.id}}
          </a>
          <span v-else>{{row.id}}</span>
        </template>
        <template #active_time="{row}">
          <span>{{row.active_time ===0 ? '-' : moment(row.active_time * 1000).format('YYYY/MM/DD HH:mm')}}</span>
        </template>
        <template #due_time="{row}">
          <span>{{row?.due_time ===0 ? '-' : moment(row?.due_time * 1000).format('YYYY/MM/DD HH:mm')}}</span>
        </template>
        <template #failed_action_trigger_time="{row}">
          <span>{{row?.failed_action_trigger_time ===0 ? '-' : moment(row?.failed_action_trigger_time * 1000).format('YYYY/MM/DD HH:mm')}}</span>
        </template>
        <template #failed_action="{row}">
          {{calcActionName(row.failed_action)}}
        </template>
        <template #op="{row}">
          <a class="common-look" @click="handleMark(row)">{{lang.failed_tip2}}</a>
        </template>
        <template #footer-summary v-if="params.tab !== 'failed'">
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
      <t-pagination show-jumper :total="total" v-if="total" :current="params.page" :page-size="params.limit"
        :page-size-options="pageSizeOptions" :on-change="changePage">
      </t-pagination>
      <t-dialog :header="lang.data_export" :visible.sync="exportVisible" :footer="false">
        <div style="margin-bottom: 20px;">
          <div>{{lang.data_export_tip2}},{{lang.data_export_tip3}}<span
              style="color: var(--td-brand-color);">{{total}}</span>{{lang.data_export_tip4}}</div>
          <div style="margin-top:20px; color: var(--td-text-color-placeholder);">
            <span style="margin-right: var(--td-comp-margin-xs);
              color: var(--td-error-color);
              line-height: var(--td-line-height-body-medium);">*</span>
            {{lang.data_export_tip5}}
          </div>
          <div style="margin-bottom: 15px; color: var(--td-text-color-placeholder);">
            <span style="margin-right: var(--td-comp-margin-xs);
              color: var(--td-error-color);
              line-height: var(--td-line-height-body-medium);">*</span>
            {{lang.data_export_tip6}}
          </div>
        </div>
        <div class="com-f-btn">
          <t-button theme="primary" type="submit" :loading="exportLoading"
            @click="handelDownload">{{ exportLoading ? lang.data_export_tip8 : lang.data_export_tip7}}</t-button>
          <t-button theme="default" variant="base" @click="exportVisible = false">{{lang.cancel}}</t-button>
        </div>
      </t-dialog>
      <safe-confirm ref="safeRef" :password.sync="admin_operate_password" @confirm="hadelSafeConfirm"></safe-confirm>
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
      <!-- 标记已处理 -->
      <t-dialog :header="lang.failed_tip2" :visible.sync="markDialog" class="mark-dialog">
        <div class="host-info">
          <p class="item">
            <span class="label">{{lang.tailorism}}ID：</span>
            <span class="value">{{curMarkObj.id}}</span>
          </p>
          <p class="item">
            <span class="label">{{lang.product_name}}：</span>
            <span class="value">{{curMarkObj.name}}</span>
          </p>
          <p class="item">
            <span class="label">{{lang.status}}：</span>
            <span class="value">{{calcStatusName(curMarkObj.status)}}</span>
          </p>
          <p class="item">
            <span class="label">{{lang.failed_action}}：</span>
            <span class="value">{{calcActionName(curMarkObj.failed_action)}}</span>
          </p>
          <p class="item">
            <span class="label">{{lang.failed_reason}}：</span>
            <span class="value">{{curMarkObj.failed_action_reason}}</span>
          </p>
        </div>
        <template slot="footer">
          <div class="common-dialog">
            <t-button @click="submitMark" :loading="submitLoading">{{lang.sure}}</t-button>
            <t-button theme="default" @click="markDialog=false">{{lang.cancel}}</t-button>
          </div>
        </template>
      </t-dialog>
      <!-- 批量拉取商品 -->
      <t-dialog :header="lang.data_export_tip11" :visible.sync="pullVisible" :footer="false">
        <t-form :data="batchPullForm" :rules="rules" ref="pullForm" @submit="submitPull">
          <t-form-item :label="lang.temp_host" name="product_id">
            <com-tree-select :multiple="true" :value="batchPullForm.product_id" @choosepro="choosePullPro"
              style="width: 100%;">
            </com-tree-select>
          </t-form-item>
          <t-form-item :label="lang.client_care_label29" name="host_status">
            <t-checkbox-group v-model="batchPullForm.host_status">
              <t-checkbox key="Active" value="Active" :label="lang.opened_notice"></t-checkbox>
              <t-checkbox key="Suspended" value="Suspended" :label="lang.Suspended"></t-checkbox>
            </t-checkbox-group>
          </t-form-item>
          <div class="com-f-btn">
            <t-button theme="primary" type="submit" :loading="submitLoading">{{lang.hold}}</t-button>
            <t-button theme="default" variant="base" @click="pullVisible = false">{{lang.cancel}}</t-button>
          </div>
        </t-form>
      </t-dialog>
    </t-card>
  </com-config>
</div>
<script src="/{$template_catalog}/template/{$themes}/components/comViewFiled/comViewFiled.js"></script>
<script src="/{$template_catalog}/template/{$themes}/components/comTreeSelect/comTreeSelect.js"></script>
<script src="/{$template_catalog}/template/{$themes}/components/safeConfirm/safeConfirm.js"></script>
<script src="/{$template_catalog}/template/{$themes}/components/loginByUser/loginByUser.js"></script>
<script src="/{$template_catalog}/template/{$themes}/api/client.js"></script>
<script src="/{$template_catalog}/template/{$themes}/js/host.js"></script>
{include file="footer"}
