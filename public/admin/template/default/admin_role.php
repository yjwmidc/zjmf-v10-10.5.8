{include file="header"}
<!-- =======内容区域======= -->
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/setting.css">
<div id="content" class="admin-role" v-cloak>
  <com-config>
    <t-card class="list-card-container">
      <ul class="common-tab">
        <li v-permission="'auth_system_configuration_admin_management_view'">
          <a href="admin.htm">{{lang.admin_setting}}</a>
        </li>
        <li class="active" v-permission="'auth_system_configuration_admin_group_view'">
          <a href="javascript:;">{{lang.group_setting}}</a>
        </li>
      </ul>
      <div class="common-header">
        <div>
          <t-button @click="addUser" class="add" v-permission="'auth_system_configuration_admin_group_create_group'">{{lang.add}}</t-button>
        </div>
        <div class="com-search">
          <t-input v-model="params.keywords" class="search-input" :placeholder="`ID、${lang.nickname}、${lang.group_tip}`" @keypress.enter.native="search" :on-clear="clearKey" clearable>
          </t-input>
          <t-icon size="20px" name="search" @click="search" class="com-search-btn" />
        </div>
      </div>
      <t-table row-key="id" :data="data" size="medium" :columns="columns" :hover="hover" :loading="loading" :table-layout="tableLayout ? 'auto' : 'fixed'" @sort-change="sortChange" :hide-sort-tips="hideSortTips">
        <template slot="sortIcon">
          <t-icon name="caret-down-small"></t-icon>
        </template>
        <template #phone="{row}">
          <span v-if="row.phone">+{{row.phone_code}}&nbsp;-&nbsp;{{row.phone}}</span>
        </template>
        <template #status="{row}">
          <t-tag theme="success" class="status" v-if="row.status" variant="light">{{lang.enable}}</t-tag>
          <t-tag theme="danger" class="status" v-else variant="light">{{lang.disable}}</t-tag>
        </template>
        <template #op="{row}">
          <t-tooltip :content="lang.update" :show-arrow="false" theme="light">
            <t-icon name="edit-1" class="common-look" @click="updateAdmin(row)" v-permission="'auth_system_configuration_admin_group_update_group'">
            </t-icon>
          </t-tooltip>
          <t-tooltip :content="lang.delete" :show-arrow="false" theme="light">
            <t-icon name="delete" class="common-look" @click="deleteUser(row)" :class="{disable: row.id===1}"
            v-permission="'auth_system_configuration_admin_group_delete_group'">
            </t-icon>
          </t-tooltip>
        </template>
      </t-table>
      <t-pagination show-jumper :total="total" :page-size="params.limit" :page-size-options="pageSizeOptions" :on-change="changePage" :current="params.page" />
    </t-card>

    <!-- 添加管理员 -->
    <t-dialog :visible.sync="visible" :header="addTip" :on-close="close"
    :footer="false" width="600" class="auth-dialog" placement="center">
      <t-form :rules="rules" :data="formData" ref="userDialog" @submit="onSubmit">
        <t-form-item :label="lang.small_group_name" name="name">
          <t-input :placeholder="lang.small_group_name" v-model="formData.name" />
        </t-form-item>
        <t-form-item :label="lang.small_group_tip" name="description">
          <t-textarea v-model="formData.description" :placeholder="lang.small_group_tip"></t-textarea>
        </t-form-item>
        <!-- 挂件权限 -->
        <!-- <t-form-item :label="lang.widget_auth">
          <div class="opt">
            <span>
              <t-checkbox v-model="widgetCheckExpand" @change="expandAllWidget">{{lang.isExpand}}</t-checkbox>
              <t-checkbox v-model="widgetCheckAll" @change="chooseAllWidget" :disabled="formData.id===1">{{lang.isCheckAll}}</t-checkbox>
            </span>
          </div>
          <div class="auth">
            <t-tree :data="widgetAuthArr" checkable activable :line="true" :active-multiple="false" v-model="formData.auth_widget" value-mode="all" :expanded="widgetExpandArr" :keys="{value: 'id', label:'title', children:'child'}" ref="widgetTree" :expand-all="widgetCheckExpand" @click="clickNodeWidget" @change="changeCheck" :expand-on-click-node="false" :indeterminate="true" :disabled="formData.id===1" />
          </div>
        </t-form-item> -->
        <t-form-item :label="lang.function_auth">
          <div class="opt">
            <span>
              <t-checkbox v-model="checkExpand" @change="expandAll">{{lang.isExpand}}</t-checkbox>
              <t-checkbox v-model="checkAll" @change="chooseAll" :disabled="formData.id===1">{{lang.isCheckAll}}</t-checkbox>
            </span>
            <span class="tip">{{lang.tip9}}</span>
          </div>
          <div class="auth">
            <!-- :check-strictly="true" -->
            <t-tree :data="authArr" checkable activable :line="true" :expand-on-click-node="false" :active-multiple="false" v-model="formData.auth" value-mode="all" :expanded="expandArr" :keys="{value: 'id', label:'title', children:'child'}" ref="tree" :expand-all="checkExpand" @click="clickNode" @change="changeCheck" :expand-on-click-node="false" :indeterminate="true" :disabled="formData.id===1" />
          </div>
        </t-form-item>
        <div class="com-f-btn">
          <t-button theme="primary" type="submit" :loading="submitLoading">{{lang.hold}}</t-button>
          <t-button theme="default" variant="base" @click="close">{{lang.cancel}}</t-button>
        </div>
      </t-form>
    </t-dialog>

    <!-- 删除弹窗 -->
    <t-dialog theme="warning" :header="lang.sureDelete" :visible.sync="delVisible">
      <template slot="footer">
        <t-button theme="primary" @click="sureDel" :loading="submitLoading">{{lang.sure}}</t-button>
        <t-button theme="default" @click="delVisible=false">{{lang.cancel}}</t-button>
      </template>
    </t-dialog>
  </com-config>
</div>
<!-- =======页面独有======= -->

<script src="/{$template_catalog}/template/{$themes}/api/setting.js"></script>
<script src="/{$template_catalog}/template/{$themes}/js/admin_role.js"></script>
{include file="footer"}
