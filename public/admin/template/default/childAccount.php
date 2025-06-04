{include file="header"}
<!-- =======内容区域======= -->
<link rel="stylesheet" href="/{$template_catalog}/template/{$themes}/css/childAccount.css">
<div class="content-box">
  <div class="childAccount-box">
    <com-config>
      <t-form ref="form" :rules="rules" label-width="120" @submit="saveBtn" :data="formData" label-align="left">
        <p class="title"> {{lang.sub_account_text1}} </p>
        <div class="top">
          <t-form-item :label="lang.sub_account_text2" name="username" label-width="60">
            <t-input v-model="formData.username"></t-input>
          </t-form-item>
          <t-form-item label-width="60" :label="lang.phone" name="phone" :rules="formData.email ?
                        [{ required: false},{pattern: /^\d{0,11}$/, message: lang.verify11 }]:
                        [{ required: true,message: lang.input + lang.phone, type: 'error' },
                        {pattern: /^\d{0,11}$/, message: lang.verify11 }]">
            <t-select v-model="formData.phone_code" filterable style="width: 100px">
              <t-option v-for="item in country" :value="item.phone_code" :label="item.name_zh + '+' + item.phone_code" :key="item.name">
              </t-option>
            </t-select>
            <t-input v-model="formData.phone" style="width: calc(100% - 100px);" />
          </t-form-item>


          <!-- <t-form-item label="手机"  name="phone" >
                            <t-select v-model="formData.phone_code" filterable style="width: 100px" :placeholder="lang.phone_code">
                                <t-option v-for="item in country" :value="item.phone_code" :label="item.name_zh + '+' + item.phone_code" :key="item.name">
                                </t-option>
                            </t-select>
                            <t-input v-model="formData.phone"></t-input>
                        </t-form-item> -->

          <t-form-item :label="lang.sub_account_text3" name="email" label-width="60">
            <t-input v-model="formData.email"></t-input>
          </t-form-item>
          <!-- <t-form-item label="区号" name="phone_code" v-if="formData.phone">
                            <t-input v-model="formData.phone_code"></t-input>
                        </t-form-item> -->
        </div>
        <div class="bom">
          <t-form-item :label="lang.sub_account_text4" v-if="false">
            <t-input v-model="formData.account"></t-input>
          </t-form-item>
          <t-form-item :label="lang.sub_account_text5">
            <t-select v-model="formData.visible_product" style="width:130px">
              <t-option v-for="item in options" :value="item.value" :label="item.label" :key="item.value"></t-option>
            </t-select>
            <!-- 模块 -->
            <t-select :min-collapsed-num="1" multiple v-model="module" multiple v-if="formData.visible_product == 'module' " style="width:550px">
              <t-option v-for="item in moduleList" :value="item.name" :label="item.display_name" :key="item.name"></t-option>
            </t-select>
            <!-- 产品 -->
            <t-select :min-collapsed-num="1" multiple v-model="host_id" v-else style="width:550px">
              <t-option v-for="item in productList" :value="item.id" :label="item.product_name + '(' + item.name + ')'" :key="item.id"></t-option>
            </t-select>
          </t-form-item>
        </div>
        <p class="title"> {{lang.sub_account_text6}} </p>
        <t-form-item :label="lang.sub_account_text7" name="course">
          <t-checkbox-group v-model="formData.notice" :options="noticeOptions"></t-checkbox-group>
        </t-form-item>

        <div class="tree-box">
          <t-form-item :label="lang.sub_account_text8" name="course">
            <div class="tree">
              <div class="tree-left">
                <t-tree :data="leftTreeData" hover expand-all :checkable="true" @change="onChange" ref="leftTree" v-model="leftAuth" :keys="{value: 'id', label:'title', children:'child'}" />
              </div>
              <div class="tree-right">
                <t-tree :data="rightTreeData" hover ref="rightTree" @change="onChangeRight" v-model="rightAuth" expand-all :checkable="true" :keys="{value: 'id', label:'title', children:'child'}" />
              </div>
            </div>
          </t-form-item>
        </div>
        <footer>
          <t-button theme="primary" type="submit" :loading="submitLoading">{{lang.sub_account_text9}}</t-button>
          <t-button theme="default" @click="back">{{lang.sub_account_text10}}</t-button>
        </footer>
      </t-form>
    </com-config>
  </div>
</div>
<!-- =======页面独有======= -->

<script src="/{$template_catalog}/template/{$themes}/api/common.js"></script>
<script src="/{$template_catalog}/template/{$themes}/api/childAccount.js"></script>
<script src="/{$template_catalog}/template/{$themes}/js/childAccount.js"></script>
{include file="footer"}
