<?php
namespace app\admin\controller;

use app\common\model\SelfDefinedFieldModel;
use app\common\model\SelfDefinedFieldLinkModel;
use app\common\validate\SelfDefinedFieldValidate;

/**
 * @title 自定义字段管理
 * @desc 自定义字段管理
 * @use app\admin\controller\SelfDefinedFieldController
 */
class SelfDefinedFieldController extends AdminBaseController
{
	public function initialize()
    {
        parent::initialize();
    }

    /**
     * 时间 2024-01-02
     * @title 自定义字段列表
     * @desc  自定义字段列表
     * @url /admin/v1/self_defined_field
     * @method  GET
     * @author hh
     * @version v1
     * @param   string type product 类型(product=商品,product_group商品组)
     * @param   int relid - 关联ID(商品ID) require
     * @return  int list[].id - 自定义字段ID
     * @return  string list[].field_name - 字段名称
     * @return  int list[].is_required - 是否必填(0=否,1=是)
     * @return  string list[].field_type - 字段类型(text=文本框,link=链接,password=密码,dropdown=下拉,tickbox=勾选框,textarea=文本区,explain=说明)
     * @return  string list[].description - 字段描述
     * @return  string list[].regexpr - 验证规则
     * @return  string list[].field_option - 下拉选项
     * @return  int list[].show_order_page - 订单页可见(0=否,1=是)
     * @return  int list[].show_order_detail - 订单详情可见(0=否,1=是)
     * @return  int list[].show_client_host_detail - 前台产品详情可见(0=否,1=是)
     * @return  int list[].show_admin_host_detail - 后台产品详情可见(0=否,1=是)
     * @return  int list[].show_client_host_list - 会员中心列表显示(0=否,1=是)
     * @return  int list[].show_admin_host_list - 后台产品列表显示(0=否,1=是)
     * @return  int list[].upstream_id - 上游ID(大于0不能修改删除)
     * @return  string list[].explain_content - 说明内容
     * @return  array list[].product_group - 关联商品分组,类型为商品组时返回
     * @return  int list[].product_group[].id - 关联商品分组ID
     * @return  string list[].product_group[].first_group_name - 一级分组名称
     * @return  string list[].product_group[].name - 关联商品分组名称
     * @return  int count - 总条数
     */
	public function selfDefinedFieldList()
    {
        $param = $this->request->param();
        
        $SelfDefinedFieldModel = new SelfDefinedFieldModel();

        $data = $SelfDefinedFieldModel->selfDefinedFieldList($param);

        $result = [
            'status' => 200,
            'msg'    => lang('success_message'),
            'data'   => $data
        ];
        return json($result);
	}

    /**
     * 时间 2024-01-02
     * @title 添加自定义字段
     * @desc 添加自定义字段
     * @url /admin/v1/self_defined_field
     * @method  POST
     * @author hh
     * @version v1
     * @param   string type - 类型(product=商品,product_group商品组) require
     * @param   int relid - 关联ID(商品ID) 类型为product时必填
     * @param   string field_name - 字段名称 require
     * @param   int is_required - 是否必填(0=否,1=是) require
     * @param   string field_type - 字段类型(text=文本框,link=链接,password=密码,dropdown=下拉,tickbox=勾选框,textarea=文本区,explain=说明) require
     * @param   string description - 字段描述
     * @param   string regexpr - 验证规则
     * @param   string field_option - 下拉选项 field_type=dropdown,require
     * @param   int show_order_page - 订单页可见(0=否,1=是) require
     * @param   int show_order_detail - 订单详情可见(0=否,1=是) require
     * @param   int show_client_host_detail - 前台产品详情可见(0=否,1=是) require
     * @param   int show_admin_host_detail - 后台产品详情可见(0=否,1=是) require
     * @param   int show_client_host_list - 会员中心列表显示(0=否,1=是) require
     * @param   int show_admin_host_list - 后台产品列表显示(0=否,1=是) require
     * @param   string explain_content - 说明内容 field_type=explain可用
     * @return  int id - 自定义字段ID
     */
	public function create()
    {
		$param = $this->request->param();

        $SelfDefinedFieldValidate = new SelfDefinedFieldValidate();
        if(isset($param['field_type']) && $param['field_type'] == 'explain'){
            // 说明验证
            if (!$SelfDefinedFieldValidate->scene('explain_create')->check($param)){
                return json(['status' => 400 , 'msg' => lang($SelfDefinedFieldValidate->getError())]);
            }
        }else{
            if (!$SelfDefinedFieldValidate->scene('create')->check($param)){
                return json(['status' => 400 , 'msg' => lang($SelfDefinedFieldValidate->getError())]);
            }
        }

        $SelfDefinedFieldModel = new SelfDefinedFieldModel();
        
        $result = $SelfDefinedFieldModel->selfDefinedFieldCreate($param);
        return json($result);
	}

    /**
     * 时间 2024-01-02
     * @title 修改自定义字段
     * @desc  修改自定义字段
     * @url /admin/v1/self_defined_field/:id
     * @method  PUT
     * @author hh
     * @version v1
     * @param   int id - 自定义字段ID require
     * @param   string field_name - 字段名称 require
     * @param   int is_required - 是否必填(0=否,1=是) require
     * @param   string field_type - 字段类型(text=文本框,link=链接,password=密码,dropdown=下拉,tickbox=勾选框,textarea=文本区,explain=说明) require
     * @param   string description - 字段描述
     * @param   string regexpr - 验证规则
     * @param   string field_option - 下拉选项 field_type=dropdown,require
     * @param   int show_order_page - 订单页可见(0=否,1=是) require
     * @param   int show_order_detail - 订单详情可见(0=否,1=是) require
     * @param   int show_client_host_detail - 前台产品详情可见(0=否,1=是) require
     * @param   int show_admin_host_detail - 后台产品详情可见(0=否,1=是) require
     * @param   int show_client_host_list - 会员中心列表显示(0=否,1=是) require
     * @param   int show_admin_host_list - 后台产品列表显示(0=否,1=是) require
     * @param   string explain_content - 说明内容 field_type=explain可用
     */
    public function update()
    {
        $param = $this->request->param();

        $SelfDefinedFieldValidate = new SelfDefinedFieldValidate();
        if(isset($param['field_type']) && $param['field_type'] == 'explain'){
            // 说明验证
            if (!$SelfDefinedFieldValidate->scene('explain_update')->check($param)){
                return json(['status' => 400 , 'msg' => lang($SelfDefinedFieldValidate->getError())]);
            }
        }else{
            if (!$SelfDefinedFieldValidate->scene('update')->check($param)){
                return json(['status' => 400 , 'msg' => lang($SelfDefinedFieldValidate->getError())]);
            }
        }

        $SelfDefinedFieldModel = new SelfDefinedFieldModel();
        
        $result = $SelfDefinedFieldModel->selfDefinedFieldUpdate($param);
        return json($result);
    }

    /**
    * 时间 2024-01-02
    * @title 删除自定义字段
    * @desc  删除自定义字段
    * @url /admin/v1/self_defined_field/:id
    * @method  DELETE
    * @author hh
    * @version v1
    * @param   int id - 自定义字段ID require
    */
	public function delete()
    {
        $param = $this->request->param();

        $SelfDefinedFieldModel = new SelfDefinedFieldModel();
        
        $result = $SelfDefinedFieldModel->selfDefinedFieldDelete($param);
        return json($result);
	}

    /**
     * 时间 2024-01-02
     * @title 拖动排序
     * @desc 拖动排序
     * @url /admin/v1/self_defined_field/:id/drag
     * @method  PUT
     * @author hh
     * @version v1
     * @param   int prev_id - 前一个自定义字段ID(0=表示置顶) require
     * @param   int id - 当前自定义字段ID require
     */
    public function dragToSort()
    {
        $param = request()->param();

        $SelfDefinedFieldValidate = new SelfDefinedFieldValidate();
        if (!$SelfDefinedFieldValidate->scene('drag')->check($param)){
            return json(['status' => 400 , 'msg' => lang($SelfDefinedFieldValidate->getError())]);
        }        
        $SelfDefinedFieldModel = new SelfDefinedFieldModel();

        $result = $SelfDefinedFieldModel->dragToSort($param);
        return json($result);
    }

    /**
     * 时间 2024-10-23
     * @title 关联商品组
     * @desc 关联商品组
     * @author theworld
     * @version v1
     * @url /admin/v1/self_defined_field/:id/related_product_group
     * @method  PUT
     * @param   int id - 自定义字段ID(仅限类型为product_group) require
     * @param   array product_group_id - 二级商品分组ID require
     */
    public function relatedProductGroup()
    {
        $param = request()->param();

        $SelfDefinedFieldValidate = new SelfDefinedFieldValidate();
        if (!$SelfDefinedFieldValidate->scene('related')->check($param)){
            return json(['status' => 400 , 'msg' => lang($SelfDefinedFieldValidate->getError())]);
        }        
        $SelfDefinedFieldLinkModel = new SelfDefinedFieldLinkModel();

        $result = $SelfDefinedFieldLinkModel->relatedProductGroup($param);
        return json($result);
    }

}