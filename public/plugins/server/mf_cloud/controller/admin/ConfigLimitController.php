<?php
namespace server\mf_cloud\controller\admin;

use server\mf_cloud\model\ConfigLimitModel;
use server\mf_cloud\validate\ConfigLimitValidate;

/**
 * @title 魔方云(自定义配置)-配置限制(已废弃)
 * @desc 魔方云(自定义配置)-配置限制
 * @use server\mf_cloud\controller\admin\ConfigLimitController
 */
class ConfigLimitController
{
	/**
	 * 时间 2023-02-01
	 * @title 添加配置限制
	 * @desc 添加配置限制
	 * @url /admin/v1/mf_cloud/config_limit
	 * @method  POST
	 * @author hh
	 * @version v1
     * @param   int product_id - 商品ID require
     * @param   string type - 类型(cpu=CPU与内存限制,data_center=数据中心与计算限制,image=操作系统与计算限制) require
     * @param   int data_center_id - 数据中心ID requireIf,type=data_center
     * @param   array cpu - CPU核心数 require
     * @param   array memory - 内存容量 
     * @param   int min_memory - 内存最小值
     * @param   int max_memory - 内存最大值
     * @param   int image_id - 操作系统ID requireIf,type=image
     * @return  int id - 配置限制ID
	 */
	public function create()
	{
		$param = request()->param();

		$ConfigLimitValidate = new ConfigLimitValidate();
		if (!$ConfigLimitValidate->scene('create')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($ConfigLimitValidate->getError())]);
        }
		$ConfigLimitModel = new ConfigLimitModel();

		$result = $ConfigLimitModel->configLimitCreate($param);
		return json($result);
	}

	/**
	 * 时间 2023-02-01
	 * @title 配置限制列表
	 * @desc 配置限制列表
	 * @url /admin/v1/mf_cloud/config_limit
	 * @method  GET
	 * @author hh
	 * @version v1
     * @param   int page - 页数
     * @param   int limit - 每页条数
     * @param   string orderby - 排序字段(id)
     * @param   string sort - 升降序(asc,desc)
     * @param   int product_id - 商品ID
     * @param   string type - 类型(cpu=CPU与内存限制,data_center=数据中心与计算限制,image=操作系统与计算限制) require
     * @return  int list[].id - 配置限制ID
     * @return  array list[].cpu - CPU
     * @return  string list[].memory - 内存(为空表示范围,多个逗号分隔)
     * @return  int list[].min_memory - 内存最小值
     * @return  int list[].max_memory - 内存最大值
     * @return  int list[].data_center_id - 数据中心ID
     * @return  string list[].data_center - 数据中心名称
     * @return  int list[].country_id - 国家ID
     * @return  string list[].city - 城市
     * @return  string list[].area - 区域
     * @return  int list[].image_id - 操作系统ID
     * @return  string list[].image_name - 操作系统名称
     * @return  int list[].image_group_id - 操作系统分组ID
     * @return  int count - 总条数
	 */
	public function list()
	{
		$param = request()->param();

		$ConfigLimitModel = new ConfigLimitModel();

		$data = $ConfigLimitModel->configLimitList($param);

		$result = [
			'status' => 200,
			'msg'	 => lang_plugins('success_message'),
			'data'	 => $data,
		];
		return json($result);
	}

	/**
	 * 时间 2023-02-01
	 * @title 修改配置限制
	 * @desc 修改配置限制
	 * @url /admin/v1/mf_cloud/config_limit/:id
	 * @method  PUT
	 * @author hh
	 * @version v1
     * @param   int id - 限制配置ID require
     * @param   int data_center_id - 数据中心ID requireIf,type=data_center
     * @param   array cpu - CPU核心数 require
     * @param   array memory - 内存容量 
     * @param   int min_memory - 内存最小值
     * @param   int max_memory - 内存最大值
     * @param   int image_id - 操作系统ID requireIf,type=image
	 */
	public function update()
	{
		$param = request()->param();

		$ConfigLimitValidate = new ConfigLimitValidate();
		if (!$ConfigLimitValidate->scene('update')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($ConfigLimitValidate->getError())]);
        }        
		$ConfigLimitModel = new ConfigLimitModel();

		$result = $ConfigLimitModel->configLimitUpdate($param);
		return json($result);
	}

	/**
	 * 时间 2023-02-01
	 * @title 删除配置限制
	 * @desc 删除配置限制
	 * @url /admin/v1/mf_cloud/config_limit/:id
	 * @method  DELETE
	 * @author hh
	 * @version v1
	 * @param   int id - 配置限制ID require
	 */
	public function delete()
	{
		$param = request()->param();

		$ConfigLimitModel = new ConfigLimitModel();

		$result = $ConfigLimitModel->configLimitDelete((int)$param['id']);
		return json($result);
	}

}