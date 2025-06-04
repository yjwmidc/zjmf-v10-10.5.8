{include file="header"}
<!-- =======内容区域======= -->
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/manage.css">
<div id="content" class="log-system" v-cloak>
  <com-config>
    <t-card class="list-card-container">
      <ul class="common-tab">
        <li class="active" v-permission="'auth_management_log_system_log'">
          <a href="javascript:;">{{lang.system_log}}</a>
        </li>
        <li v-permission="'auth_management_log_notice_log'">
          <a href="log_notice_sms.htm">{{lang.notice_log}}</a>
        </li>
      </ul>

      <div class="export-header">
        <div class="left flex">
          <t-button theme="success" @click="openExportDia"
            v-if="$checkPermission('auth_management_log_system_log_export_excel') && hasExport">
            {{lang.data_export}}
          </t-button>
        </div>
        <div class="right-search">
          <t-input v-model="params.keywords" class="search-input" :placeholder="`${lang.description}`"
            @keypress.enter.native="search" :on-clear="clearKey" clearable>
            <template #suffix-icon>
              <t-icon size="20px" name="search" @click="search" class="com-search-btn" />
            </template>
          </t-input>
        </div>
      </div>
      <t-table row-key="id" :data="data" size="medium" :hide-sort-tips="true" :columns="columns" :hover="hover"
        :loading="loading" :table-layout="tableLayout ? 'auto' : 'fixed'" @sort-change="sortChange">
        <template slot="sortIcon">
          <t-icon name="caret-down-small"></t-icon>
        </template>
        <template #description="{row}">
          <span v-html="calStr(row.description)"></span>
        </template>
        <template #create_time="{row}">
          {{moment(row.create_time * 1000).format('YYYY-MM-DD HH:mm')}}
        </template>
      </t-table>
      <t-pagination show-jumper v-if="total" :total="total" :page-size="params.limit"
        :page-size-options="pageSizeOptions" :on-change="changePage" :current="params.page">
      </t-pagination>
    </t-card>


    <t-dialog :header="lang.data_export" :visible.sync="exportVisible" :footer="false">
      <div style="margin-bottom: 20px;">
        <t-date-range-picker allow-input clearable v-model="range" style="width: 100%;">
        </t-date-range-picker>
        <p style="margin-top: 5px; color: var(--td-text-color-placeholder);">
          <span style="margin-right: var(--td-comp-margin-xs);
            color: var(--td-error-color);
            line-height: var(--td-line-height-body-medium);">*</span>
          {{lang.export_range_tips}}
        </p>
      </div>
      <div class="com-f-btn">
        <t-button theme="primary" type="submit" :loading="exportLoading"
          @click="handelDownload">{{lang.sure}}</t-button>
        <t-button theme="default" variant="base" @click="exportVisible = false">{{lang.cancel}}</t-button>
      </div>
    </t-dialog>



  </com-config>
</div>
<!-- =======页面独有======= -->

<script src="/{$template_catalog}/template/{$themes}/api/manage.js"></script>
<script src="/{$template_catalog}/template/{$themes}/js/log_system.js"></script>
{include file="footer"}
