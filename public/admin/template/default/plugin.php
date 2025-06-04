{include file="header"}
<!-- =======内容区域======= -->
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/addon.css">
<!-- =======内容区域======= -->
<div id="content" class="addon" v-cloak>
  <com-config>
    <t-card class="list-card-container">
      <div class="common-header">
        <div class="top-btn">
          <div class="left">
            <t-button class="add" @click="toMarket"
              v-permission="'auth_app_list_more_app'">{{lang.more_plugins}}</t-button>
            <t-badge dot :count="1" v-if="isNeedUpgrade && $checkPermission('auth_app_list_sync_app')">
              <t-button @click="getSystem" :loading="btnLoading">{{lang.sync_plugin}}</t-button>
            </t-badge>
            <t-button v-if="!isNeedUpgrade && $checkPermission('auth_app_list_sync_app')" @click="getSystem"
              :loading="btnLoading">{{lang.sync_plugin}}</t-button>
          </div>
          <t-button class="add" @click="handleHook" :loading="hookLoading"
            v-permission="'auth_app_list_plugin_hook_order'">{{lang.hook_sort}}</t-button>
        </div>
        <div class="bot-search">
          <t-input :placeholder="lang.plugin_search" clearable v-model="pagination.keywords"
            @keypress.enter.native="localFilter" @clear="clearKey"></t-input>
          <t-select v-model="pagination.status" clearable :placeholder="`${lang.select}${lang.status}`">
            <t-option :value="1" :label="lang.enable" key="enable"></t-option>
            <t-option :value="0" :label="lang.deactivate" key="deactivate"></t-option>
            <t-option :value="3" :label="lang.not_install" key="not_install"></t-option>
          </t-select>
          <t-button class="add" @click="localFilter">{{lang.query}}</t-button>
        </div>
      </div>
      <t-table row-key="id" :data="filterPluginList" size="medium" :columns="columns" :hover="hover" :loading="loading"
        :table-layout="tableLayout ? 'auto' : 'fixed'" @sort-change="sortChange" display-type="fixed-width"
        :hide-sort-tips="true" :pagination="pagination" @page-change="onPageChange">
        <template slot="sortIcon">
          <t-icon name="caret-down-small"></t-icon>
        </template>
        <template #title="{row}">
          <a class="aHover" :href="`${basrUrl}${row.url}`" @contextmenu="jumpMenu($event, row); return true;"
            v-if="row.url" @click="jumpMenu($event, row)">
            {{row.title}}
          </a>
          <span v-else>{{row.title}}</span>
        </template>
        <template #version="{row}">
          {{row.version}}
          <t-tooltip :content="row.error_msg ? lang.upload_text15 : lang.upgrade_plugin" :show-arrow="false"
            theme="light" v-if="row.isUpdate">
            <span class="upgrade" @click="updatePlugin(row)" :class="{'not-allowed': row.error_msg}">
              <svg class="common-look" v-if="row.status !== 3 && $checkPermission('auth_app_list_upgrade')">
                <use xlink:href="#icon-upgrade">
                </use>
              </svg>
            </span>
          </t-tooltip>
        </template>

        <template #type_name="{row}">
          {{typeObj[row.module] || lang.plugin}}
        </template>
        <template #status="{row}">
          <t-tag theme="success" class="com-status" v-if="row.status === 1" variant="light">{{lang.enable}}</t-tag>
          <t-tag theme="danger" class="com-status" v-else-if="row.status === 0"
            variant="light">{{lang.deactivate}}</t-tag>
          <t-tag class="com-status" v-else variant="light">{{lang.not_install}}</t-tag>
        </template>
        <template #op="{row}">
          <template v-if="row.module === 'addon'">
            <t-tooltip :content="enableTitle(row.status)" :show-arrow="false" theme="light">
              <a class="common-look" @click="changeStatus(row)"
                v-if="row.status !== 3 && $checkPermission('auth_app_list_deactivate_enable_app')">
                <svg class="common-look"
                  v-if="row.status === 0 && $checkPermission('auth_app_list_deactivate_enable_app')">
                  <use xlink:href="#icon-enable">
                  </use>
                </svg>
                <svg class="common-look"
                  v-else-if="row.status === 1 && $checkPermission('auth_app_list_deactivate_enable_app')">
                  <use xlink:href="#icon-disable">
                  </use>
                </svg>
              </a>
            </t-tooltip>
            <t-tooltip :content="installTitle(row.status)" :show-arrow="false" theme="light">
              <a class="common-look" @click="installHandler(row)"
                v-if="$checkPermission('auth_app_list_install_uninstall_app')">
                <svg class="common-look"
                  v-if="row.status === 3 && $checkPermission('auth_app_list_install_uninstall_app')">
                  <use xlink:href="#icon-install">
                  </use>
                </svg>
                <svg class="common-look"
                  v-else-if="row.status !== 3 && $checkPermission('auth_app_list_install_uninstall_app')">
                  <use xlink:href="#icon-uninstall">
                  </use>
                </svg>
              </a>
            </t-tooltip>
          </template>
        </template>
      </t-table>
    </t-card>

    <!-- 卸载/安装 -->
    <t-dialog theme="warning" :header="installTip" :visible.sync="delVisible">
      <template slot="footer">
        <t-button theme="primary" @click="sureDel" :loading="submitLoading">{{lang.sure}}</t-button>
        <t-button theme="default" @click="cancelDel">{{lang.cancel}}</t-button>
      </template>
    </t-dialog>

    <!-- 启用/停用 -->
    <t-dialog theme="warning" :header="statusTip" :visible.sync="statusVisble">
      <template slot="footer">
        <t-button theme="primary" @click="sureChange" :loading="submitLoading">{{lang.sure}}</t-button>
        <t-button theme="default" @click="closeDialog">{{lang.cancel}}</t-button>
      </template>
    </t-dialog>
    <!-- 升级弹窗 -->
    <t-dialog theme="warning" :header="`${lang.sure}${lang.upgrade_plugin}？`" :visible.sync="upVisible">
      <template slot="footer">
        <t-button theme="primary" @click="sureUpgrade" :loading="upLoading">{{lang.sure}}</t-button>
        <t-button theme="default" @click="upVisible=false">{{lang.cancel}}</t-button>
      </template>
    </t-dialog>
    <!-- 同步插件 -->
    <t-dialog :header="lang.sync_plugin" :visible.sync="syncVisible" width="800" :footer="false">
      <div class="plugin-search">
        <t-input :placeholder="lang.input + lang.app_name" clearable v-model="searchKey" @change="searchPlugin"
          style="width: 200px;" @clear="searchPlugin">
        </t-input>
        <t-select v-model="searchType" @change="searchPlugin" clearable style="width: 200px;"
          :placeholder="`${lang.select}${lang.application}${lang.type}`">
          <t-option v-for="item in Object.keys(typeObj)" :key="item" :value="item" :label="typeObj[item]"></t-option>
        </t-select>
        <t-button theme="primary" @click="handlerAllUpgrade('upgrade')"
          v-if="$checkPermission('auth_app_list_sync_app_download_upgrade')"
          :loading="isAllLoading">{{lang.custom_tip_text3}}
        </t-button>
        <t-button theme="primary" @click="handlerAllUpgrade('download')"
          v-if="$checkPermission('auth_app_list_sync_app_download_upgrade')"
          :loading="isAllDownloadLoading">{{lang.custom_tip_text4}}
        </t-button>
      </div>
      <t-table row-key="id" :data="showPluginList" size="medium" :columns="pluginColumns" :hover="hover"
        @select-change="rehandleSelectChange" :selected-row-keys="selectedRowKeys" :loading="btnLoading"
        :table-layout="tableLayout ? 'auto' : 'fixed'" @sort-change="pluginSortChange" display-type="fixed-width"
        :hide-sort-tips="true" :max-height="500">
        <template slot="sortIcon">
          <t-icon name="caret-down-small"></t-icon>
        </template>
        <template #type_name="{row}">
          {{typeObj[row.type] || lang.plugin}}
        </template>
        <template #op="{row, rowIndex}">
          <t-icon name="loading" style="color: var(--td-brand-color);" v-if="row.isLoading"></t-icon>
          <template v-else>
            <t-tooltip :content="row.error_msg ? lang.upload_text15 : lang.download" :show-arrow="false" theme="light"
              v-if="row.downloaded * 1 === 0 && $checkPermission('auth_app_list_sync_app_download_upgrade')">
              <a class="common-look" @click="handlerDownload(row)" :class="{'not-allowed': row.error_msg}">
                <t-icon name="arrow-down"></t-icon>
              </a>
            </t-tooltip>
            <t-tooltip :content="row.error_msg ? lang.upload_text15 : lang.upgrade_plugin" :show-arrow="false"
              theme="light"
              v-else-if="row.downloaded * 1 === 1 && row.upgrade*1 === 1 && $checkPermission('auth_app_list_sync_app_download_upgrade')">
              <a class="common-look" @click="handlerDownload(row)" :class="{'not-allowed': row.error_msg}">
                <svg class="common-look" v-if="row.status !== 3">
                  <use xlink:href="#icon-upgrade">
                  </use>
                </svg>
              </a>
            </t-tooltip>
            <span v-else>--</span>
          </template>
        </template>
      </t-table>
    </t-dialog>
    <!-- 同步插件 end -->
    <!-- hook列表 -->
    <t-dialog :header="lang.hook_sort" :visible.sync="hookDialog" width="700" :footer="false">
      <p class="s-tip">{{lang.hook_tip}}</p>
      <t-table row-key="id" :data="hookList" size="medium" :columns="hookColumns" :hover="hover" :loading="hookLoading"
        :table-layout="tableLayout ? 'auto' : 'fixed'" @sort-change="sortChange" display-type="fixed-width"
        :hide-sort-tips="true" :max-height="500" drag-sort="row-handler" @drag-sort="onDragSort">
        <template slot="sortIcon">
          <t-icon name="caret-down-small"></t-icon>
        </template>
        <template #drag="{row}">
          <t-icon name="move"></t-icon>
        </template>
        <template #status="{row}">
          <t-tag theme="success" class="com-status" v-if="row.status === 1" variant="light">{{lang.enable}}</t-tag>
          <t-tag theme="danger" class="com-status" v-else-if="row.status === 0"
            variant="light">{{lang.deactivate}}</t-tag>
        </template>
      </t-table>
    </t-dialog>
    <safe-confirm ref="safeRef" :password.sync="admin_operate_password" @confirm="hadelSafeConfirm"></safe-confirm>
  </com-config>
</div>
<!-- =======页面独有======= -->

<script src="/{$template_catalog}/template/{$themes}/components/safeConfirm/safeConfirm.js"></script>
<script src="/{$template_catalog}/template/{$themes}/api/addon.js"></script>
<script src="/{$template_catalog}/template/{$themes}/js/addon.js"></script>
{include file="footer"}
