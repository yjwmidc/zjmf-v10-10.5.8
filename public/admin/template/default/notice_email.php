{include file="header"}
<!-- =======内容区域======= -->
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/setting.css">
<div id="content" class="notice-email" v-cloak>
  <com-config>
    <t-card class="list-card-container">
      <div class="common-header">
        <div class="header-left">
          <div class="com-transparent change_log" @click="jump"
            v-permission="'auth_system_interface_email_notice_email_template_view'">
            <t-button theme="primary">{{lang.email_temp_manage}}</t-button>
            <span class="txt">{{lang.email_temp_manage}}</span>
          </div>
          <t-button theme="default" @click="getMore" class="add"
            v-permission="'auth_system_interface_email_notice_get_more_interfaces'">{{lang.get_more_interface}}</t-button>
        </div>
      </div>
      <t-table row-key="id" :data="data" size="medium" :columns="columns" :hover="hover" :loading="loading"
        :table-layout="tableLayout ? 'auto' : 'fixed'" @sort-change="sortChange" :hide-sort-tips="hideSortTips">
        <template #sms_type="{row}">
          <span v-if="row.sms_type.indexOf(1)!==-1">{{lang.international}}</span>
          <span v-if="row.sms_type.indexOf(0)!==-1">/&nbsp;{{lang.domestic}}</span>
        </template>
        <template #version="{row}">
          {{row.version}}
          <t-tooltip :content="lang.upgrade_plugin" :show-arrow="false" theme="light" v-if="row.isUpdate">
            <span class="upgrade" @click="updatePlugin(row)">
              <svg class="common-look">
                <use xlink:href="#icon-upgrade">
                </use>
              </svg>
            </span>
          </t-tooltip>
        </template>
        <template #status="{row}">
          <t-tag theme="success" class="com-status" v-if="row.status===1" variant="light">{{lang.enable}}</t-tag>
          <t-tag theme="warning" class="com-status" v-if="row.status===0" variant="light">{{lang.disable}}</t-tag>
          <t-tag theme="default" class="com-status" v-if="row.status===3" variant="light">{{lang.not_install}}</t-tag>
        </template>
        <template #op="{row}">
          <t-tooltip :content="enableTitle(row.status)" :show-arrow="false" theme="light">
            <a class="common-look" @click="changeStatus(row)"
              v-if="row.status !== 3 && $checkPermission('auth_system_interface_email_notice_deactivate_enable_interface')">
              <svg class="common-look" v-if="row.status === 0">
                <use xlink:href="#icon-enable">
                </use>
              </svg>
              <svg class="common-look" v-else-if="row.status === 1">
                <use xlink:href="#icon-disable">
                </use>
              </svg>
            </a>
          </t-tooltip>
          <t-tooltip :content="lang.apply_interface" :show-arrow="false" theme="light">
            <a class="common-look" v-if="row.help_url && row.status !== 3" :href="row.help_url" target="_blank">
              <t-icon name="link" size="20px"></t-icon>
            </a>
          </t-tooltip>
          <t-tooltip :content="lang.config" :show-arrow="false" theme="light">
            <a class="common-look" @click="handleConfig(row)"
              v-if="row.status!==3 && $checkPermission('auth_system_interface_email_notice_interface_configuration')">
              <t-icon name="tools"></t-icon>
            </a>
          </t-tooltip>
          <t-tooltip :content="installTitle(row.status)" :show-arrow="false" theme="light">
            <a class="common-look" @click="installHandler(row)"
              v-permission="'auth_system_interface_email_notice_install_uninstall_interface'">
              <svg class="common-look" v-if="row.status === 3">
                <use xlink:href="#icon-install">
                </use>
              </svg>
              <svg class="common-look" v-else-if="row.status !== 3">
                <use xlink:href="#icon-uninstall">
                </use>
              </svg>
            </a>
          </t-tooltip>
        </template>
      </t-table>
    </t-card>
    <!-- 配置弹窗 -->
    <t-dialog :header="configTip" :visible.sync="configVisble" :footer="false" width="600">
      <t-form :rules="rules" ref="userDialog" @submit="onSubmit" :label-width="170">
        <t-form-item :label="item.title" v-for="item in configData" :key="item.title" :help="item.tip">
          <!-- text -->
          <t-input v-if="item.type==='text'" v-model="item.value" :placeholder="item.title"></t-input>
          <!-- password -->
          <t-input v-if="item.type==='password'" type="password" v-model="item.value"></t-input>
          <!-- textarea -->
          <t-textarea v-if="item.type==='textarea'" v-model="item.value" :placeholder="item.title">
          </t-textarea>
          <!-- radio -->
          <t-radio-group v-if="item.type==='radio'" v-model="item.value" :options="computedRadio(item.options)">
          </t-radio-group>
          <!-- checkbox -->
          <t-checkbox-group v-if="item.type==='checkbox'" v-model="item.value" :options="item.options">
          </t-checkbox-group>
          <!-- select -->
          <t-select v-if="item.type==='select'" v-model="item.value" :placeholder="lang.select+item.title">
            <t-option v-for="(value,key) in item.options" :value="value" :label="key" :key="key">
            </t-option>
          </t-select>
        </t-form-item>
        <div class="com-f-btn">
          <t-button theme="primary" type="submit" :loading="submitLoading">{{lang.hold}}</t-button>
          <t-button theme="default" variant="base" @click="configVisble=false">{{lang.cancel}}</t-button>
        </div>
      </t-form>
    </t-dialog>
    <!-- 删除弹窗 -->
    <t-dialog theme="warning" :header="installTip" :visible.sync="delVisible">
      <template slot="footer">
        <t-button theme="primary" @click="sureDel" :loading="submitLoading">{{lang.sure}}</t-button>
        <t-button theme="default" @click="delVisible=false">{{lang.cancel}}</t-button>
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
  </com-config>
</div>
<!-- =======页面独有======= -->

<script src="/{$template_catalog}/template/{$themes}/api/setting.js"></script>
<script src="/{$template_catalog}/template/{$themes}/js/notice_email.js"></script>
{include file="footer"}
