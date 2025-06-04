{include file="header"}
<!-- =======内容区域======= -->
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/setting.css">
<div id="content" class="configuration-security" v-cloak>
  <com-config>
    <t-card class="list-card-container">
      <ul class="common-tab">
        <li class="active" v-permission="'auth_system_configuration_captcha_configuration_captcha_configuration_view'">
          <a href="javascript:;">{{lang.captcha_setting}}</a>
        </li>
        <li v-permission="'auth_system_configuration_captcha_configuration_captcha_interface_view'">
          <a :href="`captcha.htm`">{{lang.captcha_manage}}</a>
        </li>
      </ul>
      <div class="box">
        <t-form :data="formData" :label-width="80" label-align="top" :rules="rules" ref="formValidatorStatus"
          @submit="onSubmit">
          <t-row :gutter="{ xs: 8, sm: 16, md: 24, lg: 32, xl: 32, xxl: 60 }">
            <t-col>
              <t-form-item :label="lang.choose + lang.code + lang.interface" class="code">
                <t-select v-model="formData.captcha_plugin" :placeholder="lang.select+lang.code + lang.interface"
                  :popup-props="popupProps">
                  <t-option v-for="item in captchaList" :value="item.name" :label="item.title" :key="item.name">
                  </t-option>
                </t-select>
              </t-form-item>
            </t-col>
          </t-row>
          <t-row :gutter="{ xs: 8, sm: 16, md: 24, lg: 32, xl: 32, xxl: 60 }">
            <t-col>
              <t-form-item :label="lang.enable_code" class="code">
                <t-checkbox v-model="formData.captcha_client_register">{{lang.user_register}}</t-checkbox>
                <t-checkbox v-model="formData.captcha_client_login">{{lang.user_login}}</t-checkbox>
                <t-checkbox v-model="formData.captcha_admin_login">{{lang.admin_login}}</t-checkbox>
                <t-checkbox v-model="formData.captcha_client_verify">{{lang.captcha_client_verify}}</t-checkbox>
                <t-checkbox v-model="formData.captcha_client_update">{{lang.captcha_client_update}}</t-checkbox>
                <t-checkbox
                  v-model="formData.captcha_client_password_reset">{{lang.captcha_client_password_reset}}</t-checkbox>
                <t-checkbox v-model="formData.captcha_client_oauth">{{lang.captcha_client_oauth}}</t-checkbox>
              </t-form-item>
              <div class="tip">
                <t-icon name="error-circle" size="18"></t-icon>
                <div>
                  <p>{{lang.tip1}}</p>
                  <p>{{lang.tip2}}</p>
                </div>
              </div>
              <t-divider></t-divider>
            </t-col>
          </t-row>
          <t-row :gutter="{ xs: 8, sm: 16, md: 24, lg: 32, xl: 32, xxl: 60 }">
            <t-col>
              <t-form-item :label="lang.error_choose_code">
                <t-radio-group v-model="formData.captcha_client_login_error" :disabled="!formData.captcha_client_login">
                  <t-radio value="0">{{lang.always_show}}</t-radio>
                  <t-radio value="1">{{lang.fail_three_show}}</t-radio>
                </t-radio-group>
              </t-form-item>
              <div class="tip">
                <t-icon name="error-circle" size="18"></t-icon>
                <div>
                  <p>{{lang.tip3}}</p>
                  <p>{{lang.tip4}}</p>
                  <p>{{lang.tip5}}</p>
                </div>
              </div>
            </t-col>
          </t-row>
          <t-form-item class="submit">
            <t-button theme="primary" type="submit" style="margin-right: 10px" :loading="submitLoading"
              v-permission="'auth_system_configuration_captcha_configuration_captcha_configuration_save_configuration'">{{lang.hold}}</t-button>
          </t-form-item>
        </t-form>
      </div>
    </t-card>
  </com-config>
</div>
<!-- =======页面独有======= -->

<script src="/{$template_catalog}/template/{$themes}/api/setting.js"></script>
<script src="/{$template_catalog}/template/{$themes}/js/configuration_security.js"></script>
{include file="footer"}
