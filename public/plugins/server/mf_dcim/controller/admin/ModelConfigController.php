<?php
namespace server\mf_dcim\controller\admin;

use server\mf_dcim\model\ModelConfigModel;
use server\mf_dcim\validate\ModelConfigValidate;

/**
 * @title DCIM(自定义配置)-型号配置
 * @desc DCIM(自定义配置)-型号配置
 * @use server\mf_dcim\controller\admin\ModelConfigController
 */
class ModelConfigController
{
	/**
	 * 时间 2023-01-31
	 * @title 添加型号配置
	 * @desc 添加型号配置
	 * @url /admin/v1/mf_dcim/model_config
	 * @method  POST
	 * @author hh
	 * @version v1
     * @param   int product_id - 商品ID require
     * @param   string name - 配置名称 require
     * @param   int group_id - 销售分组ID require
     * @param   string cpu - 处理器 require
     * @param   string cpu_param - 处理器参数 require
     * @param   string memory - 内存 require
     * @param   string disk - 硬盘 require
     * @param   string gpu - 显卡
     * @param   int support_optional - 允许增值选配(0=不允许,1=允许) require
     * @param   int optional_only_for_upgrade - 增值仅用于升降级(0=关闭,1=开启) requireIf:support_optional=1
     * @param   array optional_memory_id - 可选配内存配置ID
     * @param   int leave_memory - 剩余内存容量
     * @param   int max_memory_num - 可增加内存条数
     * @param   array optional_disk_id - 可选配硬盘配置ID
     * @param   int max_disk_num - 可增加硬盘数量
     * @param   array optional_gpu_id - 可选配显卡配置ID
     * @param   int max_gpu_num - 可增加显卡数量
     * @param   object price - 周期价格(如{"5":"12"},5是周期ID,12是价格) require
     * @param   int ontrial - 是否开启试用：0否默认，1是
     * @param   float ontrial_price - 试用价格
     * @param   int ontrial_stock_control - 试用库存开关：0否，1是
     * @param   int ontrial_qty - 试用库存
     * @return  int id - 型号配置ID
	 */
	public function create()
	{
		$param = request()->param();

		$ModelConfigValidate = new ModelConfigValidate();
		if (!$ModelConfigValidate->scene('create')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($ModelConfigValidate->getError())]);
        }
		$ModelConfigModel = new ModelConfigModel();

		$result = $ModelConfigModel->modelConfigCreate($param);
		return json($result);
	}

	/**
	 * 时间 2023-01-31
	 * @title 型号配置列表
	 * @desc 型号配置列表
	 * @url /admin/v1/mf_dcim/model_config
	 * @method  GET
	 * @param   int page - 页数
     * @param   int limit - 每页条数
     * @param   int product_id - 商品ID
     * @return  int list[].id - 型号配置ID
     * @return  string list[].name - 配置名称
     * @return  int list[].group_id - 销售分组ID
     * @return  string list[].cpu - 处理器
     * @return  string list[].cpu_param - 处理器参数
     * @return  string list[].memory - 内存
     * @return  string list[].disk - 硬盘
     * @return  string list[].gpu - 显卡
     * @return  int list[].support_optional - 允许增值选配(0=不允许,1=允许)
     * @return  int list[].hidden - 是否隐藏(0=否,1=是)
     * @return  int list[].qty - 库存数量
     * @return  int list[].auto_sync_dcim_stock - 自动同步DCIM库存(0=不启用,1=启用)
     * @return  int list[].ontrial - 是否开启试用：0否默认，1是
     * @return  float list[].ontrial_price - 试用价格
     * @return  int list[].ontrial_stock_control - 试用库存开关：0否，1是
     * @return  int list[].ontrial_qty - 试用库存
     * @return  int count - 总条数
     * @author hh
	 * @version v1
     */
	public function list()
	{
		$param = request()->param();

		$ModelConfigModel = new ModelConfigModel();

		$data = $ModelConfigModel->modelConfigList($param);

		$result = [
			'status' => 200,
			'msg'	 => lang_plugins('success_message'),
			'data'	 => $data,
		];
		return json($result);
	}

	/**
	 * 时间 2023-01-31
	 * @title 修改型号配置
	 * @desc 修改型号配置
	 * @url /admin/v1/mf_dcim/model_config/:id
	 * @method  PUT
	 * @param   int id - 型号配置ID require
     * @param   string name - 配置名称 require
     * @param   int group_id - 销售分组ID require
     * @param   string cpu - 处理器 require
     * @param   string cpu_param - 处理器参数 require
     * @param   string memory - 内存 require
     * @param   string disk - 硬盘 require
     * @param   string gpu - 显卡
     * @param   int support_optional - 允许增值选配(0=不允许,1=允许) require
     * @param   int optional_only_for_upgrade - 增值仅用于升降级(0=关闭,1=开启) requireIf:support_optional=1
     * @param   array optional_memory_id - 可选配内存配置ID
     * @param   int leave_memory - 剩余内存容量
     * @param   int max_memory_num - 可增加内存条数
     * @param   array optional_disk_id - 可选配硬盘配置ID
     * @param   int max_disk_num - 可增加硬盘数量
     * @param   array optional_gpu_id - 可选配显卡配置ID
     * @param   int max_gpu_num - 可增加显卡数量
     * @param   object price - 周期价格(如{"5":"12"},5是周期ID,12是价格) require
     * @param   int ontrial - 是否开启试用：0否默认，1是
     * @param   float ontrial_price - 试用价格
     * @param   int ontrial_stock_control - 试用库存开关：0否，1是
     * @param   int ontrial_qty - 试用库存
     * @author hh
     * @version v1
     */
	public function update()
	{
		$param = request()->param();

		$ModelConfigValidate = new ModelConfigValidate();
		if (!$ModelConfigValidate->scene('update')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($ModelConfigValidate->getError())]);
        }        
		$ModelConfigModel = new ModelConfigModel();

		$result = $ModelConfigModel->modelConfigUpdate($param);
		return json($result);
	}

	/**
	 * 时间 2023-01-31
	 * @title 删除型号配置
	 * @desc 删除型号配置
	 * @url /admin/v1/mf_dcim/model_config/:id
	 * @method  DELETE
	 * @author hh
	 * @version v1
	 * @param   int id - 型号配置ID require
	 */
	public function delete()
	{
		$param = request()->param();

		$ModelConfigModel = new ModelConfigModel();

		$result = $ModelConfigModel->modelConfigDelete((int)$param['id']);
		return json($result);
	}

	/**
	 * 时间 2023-01-31
	 * @title 型号配置详情
	 * @desc 型号配置详情
	 * @url /admin/v1/mf_dcim/model_config/:id
	 * @method  GET
	 * @param   int id - 型号配置ID require
     * @return  int id - 型号配置ID
     * @return   string name - 配置名称
     * @return   int group_id - 销售分组ID
     * @return   string cpu - 处理器
     * @return   string cpu_param - 处理器参数
     * @return   string memory - 内存
     * @return   string disk - 硬盘
     * @return   string gpu - 显卡
     * @return   int support_optional - 允许增值选配(0=不允许,1=允许)
     * @return   int leave_memory - 剩余内存容量
     * @return   int max_memory_num - 可增加内存条数
     * @return   int max_disk_num - 可增加硬盘数量
     * @return   int max_gpu_num - 可增加显卡数量
     * @return   int optional_only_for_upgrade - 增值仅用于升降级(0=关闭,1=开启)
     * @return   array optional_memory_id - 可选配内存配置ID
     * @return   array optional_disk_id - 可选配硬盘配置ID
     * @return   array optional_gpu_id - 可选配显卡配置ID
     * @return   int duration[].id - 周期ID
     * @return   string duration[].name - 周期名称
     * @return   string duration[].price - 周期价格
     * @return  int duration[].ontrial - 是否开启试用：0否默认，1是
     * @return  float duration[].ontrial_price - 试用价格
     * @return  int duration[].ontrial_stock_control - 试用库存开关：0否，1是
     * @return  int duration[].ontrial_qty - 试用库存
     * @author hh
     * @version v1
     */
	public function index()
	{
		$param = request()->param();

		$ModelConfigModel = new ModelConfigModel();

		$data = $ModelConfigModel->modelConfigIndex((int)$param['id']);

		$result = [
			'status' => 200,
			'msg'    => lang_plugins('message_success'),
			'data'	 => $data,
		];
		return json($result);
	}

	/**
	 * 时间 2023-12-20
	 * @title 切换订购是否显示
	 * @desc 切换订购是否显示
	 * @url admin/v1/mf_dcim/model_config/:id/hidden
	 * @method  PUT
	 * @author hh
	 * @version v1
	 * @param   int id - 配置型号ID require
	 * @param   int hidden - 状态(0=显示,1=隐藏) require
	 */
	public function updateHidden()
	{
		$param = request()->param();

		$ModelConfigValidate = new ModelConfigValidate();
		if (!$ModelConfigValidate->scene('update_hidden')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($ModelConfigValidate->getError())]);
        }        
		$ModelConfigModel = new ModelConfigModel();

		$result = $ModelConfigModel->updateHidden($param);
		return json($result);
	}

	/**
	 * 时间 2023-12-20
	 * @title 拖动排序
	 * @desc 拖动排序
	 * @url admin/v1/mf_dcim/model_config/:id/drag
	 * @method  PUT
	 * @author hh
	 * @version v1
	 * @param   int id - 当前机型配置ID require
     * @param   int prev_model_config_id - 前一个机型配置ID(0=表示置顶) require
	 */
	public function dragToSort()
	{
		$param = request()->param();

		$ModelConfigValidate = new ModelConfigValidate();
		if (!$ModelConfigValidate->scene('drag')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($ModelConfigValidate->getError())]);
        }        
		$ModelConfigModel = new ModelConfigModel();

		$result = $ModelConfigModel->dragToSort($param);
		return json($result);
	}

    /**
     * 时间 2025-02-14
     * @title 修改试用状态
     * @desc  修改试用状态
     * @url  /admin/v1/mf_cloud/model_config/:id/ontrial
     * @method  PUT
     * @author wyh
     * @version v1
     * @param   int id - 当前机型配置ID require
     * @param   int ontrial - 是否试用(0=否,1=是) require
     */
    public function updateOntrial()
    {
        $param = request()->param();

        $ModelConfigValidate = new ModelConfigValidate();

        if (!$ModelConfigValidate->scene('update_ontrial')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($ModelConfigValidate->getError())]);
        }

        $ModelConfigModel = new ModelConfigModel();

        $result = $ModelConfigModel->updateOntrial($param);

        return json($result);
    }

}