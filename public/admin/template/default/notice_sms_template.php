{include file="header"}
<!-- =======内容区域======= -->
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/setting.css">
<div id="content" class="notice-sms-template hasCrumb table" v-cloak>
  <!-- crumb -->
  <com-config>
    <div class="com-crumb">
      <span>{{lang.notice_interface}}</span>
      <t-icon name="chevron-right"></t-icon>
      <a href="notice_sms.htm">{{lang.sms_notice}}</a>
      <t-icon name="chevron-right"></t-icon>
      <span class="cur">{{lang.template_manage}}</span>
    </div>
    <t-card class="list-card-container">
      <div class="common-header">
        <div class="left">
          <t-button @click="createTemplate" class="add" v-permission="'auth_system_interface_sms_notice_sms_template_create_template'">{{lang.create_template}}</t-button>
          <t-button @click="batchSubmit" class="add" v-permission="'auth_system_interface_sms_notice_sms_template_batch_create_template'">{{lang.batch_submit}}</t-button>
          <t-button theme="default" @click="back" class="add">{{lang.back}}</t-button>
        </div>
      </div>
      <t-table row-key="id" :data="data" size="medium" :columns="columns" :hover="hover" :loading="loading" :table-layout="tableLayout ? 'auto' : 'fixed'" @sort-change="sortChange" :hide-sort-tips="hideSortTips">
        <template #type="{row}">
          <span>{{ row.type === 1 ? lang.international : lang.domestic }}</span>
        </template>
        <template #status="{row}">
          <t-tag theme="warning" class="com-status" v-if="row.status===0" variant="light">{{lang.no_submit}}
          </t-tag>
          <t-tag theme="primary" class="com-status" v-if="row.status===1" variant="light">{{lang.under_review}}
          </t-tag>
          <t-tag theme="success" class="com-status" v-if="row.status===2" variant="light">{{lang.pass}}</t-tag>
          <t-tag theme="danger" class="com-status" v-if="row.status===3" variant="light">{{lang.fail}}</t-tag>
        </template>
        <template #op="{row}">
          <t-tooltip :content="lang.edit" :show-arrow="false" theme="light">
            <t-icon name="edit-1" class="common-look" @click="updateHandler(row)" :class="{disable: row.status===1}" v-permission="'auth_system_interface_sms_notice_sms_template_update_template'">
            </t-icon>
          </t-tooltip>
          <t-tooltip :content="lang.test" :show-arrow="false" theme="light">
            <a class="common-look" @click="testHandler(row)" v-if="row.status===2 && $checkPermission('auth_system_interface_sms_notice_sms_template_test_template')">
              <svg class="common-look">
                <use xlink:href="#icon-retry">
                </use>
              </svg>
            </a>
          </t-tooltip>
          <t-tooltip :content="lang.delete" :show-arrow="false" theme="light">
            <t-icon name="delete" class="common-look" @click="deleteHandler(row)" v-permission="'auth_system_interface_sms_notice_sms_template_delete_template'">
            </t-icon>
          </t-tooltip>
        </template>
      </t-table>
    </t-card>

    <!-- 短信模板 -->
    <t-dialog :visible.sync="visible" :header="addTip" :on-close="close" :footer="false" width="600" class="template-dialog">
      <t-form :rules="rules" :data="formData" ref="userDialog" @submit="onSubmit" v-if="visible">
        <t-form-item :label="lang.choose_area" name="type">
          <t-radio-group v-model="formData.type">
            <t-radio value="0">{{lang.domestic}}</t-radio>
            <t-radio value="1">{{lang.international}}</t-radio>
          </t-radio-group>
        </t-form-item>
        <t-form-item :label="lang.template+'ID'" name="template_id">
          <t-input :placeholder="lang.template+'ID'" v-model="formData.template_id" />
        </t-form-item>
        <t-form-item :label="lang.template+lang.status" name="status">
          <t-select v-model="formData.status" :placeholder="lang.template+lang.status">
            <t-option key="0" :label="lang.no_submit_review" value="0"></t-option>
            <t-option key="2" :label="lang.pass_review" value="2"></t-option>
            <t-option key="3" :label="lang.fail_review" value="3"></t-option>
          </t-select>
        </t-form-item>
        <t-form-item :label="lang.title" name="title">
          <t-input :placeholder="lang.title" v-model="formData.title" />
        </t-form-item>
        <t-form-item :label="lang.content" name="content">
          <t-textarea :placeholder="lang.content" v-model="formData.content" />
        </t-form-item>
        <!-- Aliyun 独有 -->
        <template v-if="formData.name === 'Aliyun'">
          <t-form-item :label="lang.app_scene" name="product_url" class="product_url">
            <t-input :placeholder="lang.app_scene_tip1" v-model="formData.product_url"></t-input>
            <p class="s-tip">{{lang.app_scene_tip2}}</p>
          </t-form-item>
          <t-form-item :label="lang.scene_des" name="remark">
            <t-textarea :placeholder="lang.scene_des_tip1" v-model="formData.remark" />
          </t-form-item>
        </template>
        <t-form-item :label="lang.notes" name="notes">
          <t-textarea :placeholder="lang.notes" v-model="formData.notes" />
        </t-form-item>
        <div class="com-f-btn">
          <t-tooltip :show-arrow="false" theme="light" :destroy-on-close="false" overlay-class-name="params-send-pup">
            <template slot="content">
              <com-send-params></com-send-params>
            </template>
            <t-button>{{lang.reference_var}}</t-button>
          </t-tooltip>
          <div class="flex">
            <t-button theme="primary" type="submit" :loading="submitLoading">{{lang.hold}}</t-button>
            <t-button theme="default" variant="base" @click="close">{{lang.cancel}}</t-button>
          </div>
        </div>
      </t-form>
    </t-dialog>

    <!-- 删除弹窗 -->
    <t-dialog theme="warning" :header="delOrSubmitTitle" :visible.sync="delVisible">
      <template slot="footer">
        <t-button theme="primary" @click="sureHandler" :loading="submitLoading">{{lang.sure}}</t-button>
        <t-button theme="default" @click="delVisible=false">{{lang.cancel}}</t-button>
      </template>
    </t-dialog>

    <!-- 测试 -->
    <t-dialog :header="lang.sms_test" :visible.sync="statusVisble" :footer="false" :width="isEn ? 700 : 600" class="test-dialog">
      <t-form :rules="rules" :data="testForm" ref="userDialog" @submit="testSubmit">
        <t-form-item :label="lang.phone" name="phone">
          <t-select v-model="testForm.phone_code" filterable style="width: 150px" :placeholder="lang.phone_code" :disabled="isChina">
            <t-option v-for="item in country" :value="item.phone_code" :label="item.name_zh + '+' + item.phone_code" :key="item.name">
            </t-option>
          </t-select>
          <t-input :placeholder="lang.phone" v-model="testForm.phone" />
        </t-form-item>
        <div class="com-f-btn">
          <t-button theme="primary" type="submit" :loading="submitLoading">{{lang.sure}}</t-button>
          <t-button theme="default" variant="base" @click="closeTest">{{lang.cancel}}</t-button>
        </div>
      </t-form>
    </t-dialog>
  </com-config>
</div>
<!-- =======页面独有======= -->
<script src="/{$template_catalog}/template/{$themes}/components/comSendParams/comSendParams.js"></script>
<script src="/{$template_catalog}/template/{$themes}/api/common.js"></script>
<script src="/{$template_catalog}/template/{$themes}/api/setting.js"></script>
<script src="/{$template_catalog}/template/{$themes}/js/notice_sms_template.js"></script>
{include file="footer"}
