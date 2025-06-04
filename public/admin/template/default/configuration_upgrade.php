{include file="header"}
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/setting.css">
<div id="content" class="configuration-system configuration-login" v-cloak>
  <com-config>
    <t-card class="list-card-container">
      <ul class="common-tab">
        <li v-permission="'auth_system_configuration_system_configuration_system_configuration_view'">
          <a href="configuration_system.htm">{{lang.system_setting}}</a>
        </li>
        <li v-permission="'auth_system_configuration_system_configuration_debug'">
          <a href="configuration_debug.htm">{{lang.debug_setting}}</a>
        </li>
        <li v-permission="'auth_system_configuration_system_configuration_access_configuration_view'">
          <a href="configuration_login.htm">{{lang.login_setting}}</a>
        </li>
        <li v-permission="'auth_system_configuration_system_configuration_theme_configuration_view'">
          <a href="configuration_theme.htm">{{lang.theme_setting}}</a>
        </li>
        <!-- <li
          v-if="$checkPermission('auth_system_configuration_system_configuration_web_configuration') && !hasController">
          <a href="info_config.htm">{{lang.info_config}}</a>
        </li> -->
        <li v-permission="'auth_system_configuration_system_configuration_oss_management'">
          <a href="configuration_oss.htm">{{lang.oss_setting}}</a>
        </li>
        <li v-permission="'auth_system_configuration_system_configuration_user_api_management'">
          <a href="configuration_api.htm">{{lang.user_api_text1}}</a>
        </li>
        <li class="active" v-permission="'auth_system_configuration_system_configuration_system_info_view'">
          <a style="display: flex; align-items: center;" href="javascript:;">{{lang.system_upgrade}}
            <img v-if="isCanUpdata" style="width: 20px; height: 20px; margin-left: 5px;"
              src="/{$template_catalog}/template/{$themes}/img/upgrade.svg">
          </a>
        </li>
      </ul>
      <div class="upgrade-box">
        <div class="upgrade-contend">
          <div class="msg-item">
            <div class="s-item">
              <div class="msg-item-l">{{lang.upload_text1}}：</div>
              <div class="msg-item-r">{{systemData.last_version}}</div>
              <div class="msg-footer"
                v-if="hasUpdate && $checkPermission('auth_system_configuration_system_configuration_system_info_system_upgrade')">
                <div class="footer-btn" v-if="!isShowProgress">
                  <t-button @click="beginDown" v-show="!isDown">{{lang.upload_text2}}</t-button>
                  <t-button @click="toUpdate" v-show="isDown">{{lang.upload_text3}}</t-button>
                </div>
                <div class="footer-progress" v-else>
                  <div class="progress-text">{{lang.upload_text4}}{{'...(' + updateData.progress + ')'}}</div>
                  <div class="progress">
                    <div :style="'width:'+ updateData.progress" class="down-progress-success"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="s-item">
              <div class="msg-item-l">{{lang.upload_text7}}：</div>
              <div class="msg-item-r">{{systemData.license || lang.upload_text9}}</div>
              <t-button class="auth-btn" @click="handlerChange"
                v-permission="'auth_system_configuration_system_configuration_system_info_change_license'">{{lang.upload_text10}}
              </t-button>
              <t-button class="auth-btn" :loading="updateCode" @click="handlerUpdate"
                v-permission="'auth_system_configuration_system_configuration_system_info_update_license'">{{lang.upload_text11}}
              </t-button>
            </div>
          </div>
          <div class="msg-item no-margin">
            <div class="s-item">
              <div class="msg-item-l">{{lang.upload_text5}}：</div>
              <div class="msg-item-r">{{systemData.version}}</div>
              <t-radio-group style="margin-left: 5px;" variant="primary-filled" default-value="stable"
                v-model="systemData.system_version_type" @change="changeType">
                <t-radio-button value="stable">{{lang.upstream_text99}}</t-radio-button>
                <t-radio-button value="beta">{{lang.upstream_text100}}</t-radio-button>
              </t-radio-group>
            </div>
            <div class="s-item">
              <div class="msg-item-l">{{lang.authorize}}{{lang.due_time}}：</div>
              <div class="msg-item-r">{{systemData.due_time ? systemData.due_time : '--'}}</div>
            </div>
            <div class="s-item">
              <div class="msg-item-l">{{lang.upload_text8}}：</div>
              <div class="msg-item-r">
                {{systemData.service_due_time ? systemData.service_due_time === '0000-00-00 00:00:00' ? lang.not_orderer_service : systemData.service_due_time : lang.not_orderer_service}}
              </div>
            </div>
            <!-- <div class="msg-item">
          <div class="msg-item-l">系统识别码:</div>
          <div class="msg-item-r">{{systemData.version}}
        </div> -->
          </div>
          <div class="upgrade-box-title public-box">
            <span class="upgrade-title-text">{{lang.upload_text6}}</span>
          </div>
          <t-table :data="newList" row-key="id" :columns="columns" bordered hover="hover" table-layout="auto"
            :loading="isLoading" :pagination="pagination" @page-change="onPageChange" cellEmpty-content="-"
            @row-click="onRowClick" class="table-box"></t-table>
        </div>
      </div>
    </t-card>
    <!-- 更换识别码 -->
    <t-dialog :header="lang.upload_text10" :visible.sync="syncVisible" width="600" :footer="false">
      <t-form :data="changeForm" ref="codeForm" @submit="submitUpdate" :rules="rules" label-align="top" :colon="true">
        <t-form-item :label="lang.upload_text12">{{domain}}</t-form-item>
        <t-form-item :label="lang.upload_text13">{{systemData.license || lang.upload_text9}}</t-form-item>
        <t-form-item :label="lang.upload_text14" name="license">
          <t-input v-model="changeForm.license" :placeholder="`${lang.input}${lang.upload_text14}`"></t-input>
        </t-form-item>
        <div class="com-f-btn">
          <t-button theme="primary" type="submit" :loading="submitLoading">{{lang.hold}}</t-button>
          <t-button theme="default" variant="base" @click="syncVisible=false">{{lang.cancel}}</t-button>
        </div>
      </t-form>
      </t-table>
    </t-dialog>
    <t-dialog :header="lang.upstream_text88" @close="cancelChange" :visible.sync="betaTipDia" width="600"
      :footer="false">
      <div class="tips-box" style="line-height: 30px; margin-bottom: 15px;">
        <template v-if="systemData.system_version_type === 'beta'">
          <div class="tips-title">{{lang.upstream_text89}}</div>
          <div class="tips-text">{{lang.upstream_text90}}</div>
          <div class="tips-text">{{lang.upstream_text91}}</div>
          <div class="tips-text">{{lang.upstream_text92}}</div>
          <div class="tips-text">{{lang.upstream_text93}}</div>
          <div class="tips-text">{{lang.upstream_text94}}</div>
        </template>
        <template v-if="systemData.system_version_type === 'stable'">
          <div class="tips-title">{{lang.upstream_text95}}</div>
          <div class="tips-text">{{lang.upstream_text96}}</div>
          <div class="tips-text">{{lang.upstream_text97}}</div>
          <div class="tips-text">{{lang.upstream_text98}}</div>
        </template>
      </div>
      <div class="com-f-btn">
        <t-button theme="primary" @click="changeBeta" :loading="submitLoading">{{lang.hold}}</t-button>
        <t-button theme="default" variant="base" @click="cancelChange">{{lang.cancel}}</t-button>
      </div>
    </t-dialog>

  </com-config>
</div>
<!-- =======页面独有======= -->

<script src="/{$template_catalog}/template/{$themes}/api/setting.js"></script>
<script src="/{$template_catalog}/template/{$themes}/js/configuration_upgrade.js"></script>

{include file="footer"}
