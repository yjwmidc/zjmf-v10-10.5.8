<?php
namespace app\admin\controller;

use app\admin\model\ProductDurationGroupPresetsModel;
use app\admin\validate\ProductDurationGroupPresetsValidate;

/**
 * @title 商品周期预设管理
 * @desc 商品周期预设管理
 * @use app\admin\controller\ProductDurationGroupPresetsController
 */
class ProductDurationGroupPresetsController extends AdminBaseController
{
    private $validate;
    public function initialize()
    {
        parent::initialize();
        $this->validate = new ProductDurationGroupPresetsValidate();
    }

    /**
     * 时间 2024-10-23
     * @title 预设列表
     * @desc 预设列表
     * @url /admin/v1/product_duration_group_presets
     * @method  GET
     * @return array list - 预设列表
     * @return int list[].id - 分组id
     * @return string list[].name - 分组名称
     * @return array list[].duration_info - 周期信息
     * @return string list[].duration_info[].name - 周期名称
     * @return int list[].duration_info[].num - 周期时长
     * @return string list[].duration_info[].unit - 周期单位(hour=小时,day=天,month=自然月)
     * @return int list[].ratio_open - 是否开启周期比例
     * @return array list[].ration_info - 周期比例信息
     * @return string list[].ration_info[].name - 周期名称
     * @return float list[].ration_info[].ratio - 周期比例
     * @return int count - 预设总数
     * @author wyh
     * @version v1
     */
    public function presetsList()
    {
        # 合并分页参数
        $param = array_merge($this->request->param(),['page'=>$this->request->page,'limit'=>$this->request->limit,'sort'=>$this->request->sort]);
        
        $result = [
            'status'=>200,
            'msg'=>lang('success_message'),
            'data' =>(new ProductDurationGroupPresetsModel())->presetsList($param)
        ];
       return json($result);
    }

    /**
     * 时间 2024-10-23
     * @title 获取周期预设信息
     * @desc 获取周期预设信息
     * @url /admin/v1/product_duration_group_presets/:id
     * @method  GET
     * @param int id - 周期分组预设ID required
     * @return object presets - 预设信息
     * @return int presets.id - 分组预设ID
     * @return string presets.name - 分组名称
     * @return int presets.ratio_open -
     * @return array presets.durations - 周期信息
     * @return array presets.durations[].id - 周期ID
     * @return string presets.durations[].name - 周期名称
     * @return int presets.durations[].num - 周期时长
     * @return string presets.durations[].unit - 周期单位(hour=小时,day=天,month=自然月
     * @return float presets.durations[].ratio - 周期比例
     * @author wyh
     * @version v1
     */
    public function index()
    {
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('index')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        $result = [
            'status'=>200,
            'msg'=>lang('success_message'),
            'data' =>[
                'presets' => (new ProductDurationGroupPresetsModel())->indexPresets(intval($param['id']))
            ]
        ];
        return json($result);
    }

    /**
     * 时间 2024-10-23
     * @title 新建周期配置组
     * @desc 新建周期配置组
     * @url /admin/v1/product_duration_group_presets
     * @method  post
     * @author wyh
     * @version v1
     * @param string name - 分组名称 required
     * @param int ratio_open - 周期比例开关(0=关,1=开) required
     * @param array durations - 周期信息 required
     * @param string durations[].name - 周期名称 required
     * @param int durations[].num - 周期时长 required
     * @param string durations[].unit - 周期单位(hour=小时,day=天,month=自然月) required
     * @param float durations[].ratio - 周期比例，可默认传0 required
     */
    public function create()
    {
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('create')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        $result = (new ProductDurationGroupPresetsModel())->createPresets($param);

        return json($result);
    }

    /**
     * 时间 2024-10-23
     * @title 修改周期配置组
     * @desc 修改周期配置组
     * @url /admin/v1/product_duration_group_presets/:id
     * @method  put
     * @param int id 1 周期分组预设ID required
     * @param string name - 分组名称 required
     * @param int ratio_open - 周期比例开关(0=关,1=开) required
     * @param array durations - 周期信息 required
     * @param string durations[].name - 周期名称 required
     * @param int durations[].num - 周期时长 required
     * @param string durations[].unit - 周期单位(hour=小时,day=天,month=自然月) required
     * @param float durations[].ratio - 周期比例，可默认传0 required
     * @author wyh
     * @version v1
     */
    public function update()
    {
        $param = $this->request->param();
        //参数验证
        if (!$this->validate->scene('update')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }
        $result = (new ProductDurationGroupPresetsModel())->updatePresets($param);

        return json($result);
    }

    /**
     * 时间 2024-10-23
     * @title 删除周期配置组
     * @desc 删除周期配置组
     * @url /admin/v1/product_duration_group_presets/:id
     * @method  delete
     * @author wyh
     * @version v1
     * @param int id 1 周期分组预设ID required
     */
    public function delete()
    {
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('delete')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        $result = (new ProductDurationGroupPresetsModel())->deletePresets($param);

        return json($result);
    }

    /**
     * 时间 2024-10-23
     * @title 周期配置组复制
     * @desc 周期配置组复制
     * @url /admin/v1/product_duration_group_presets/:id/copy
     * @method  post
     * @author wyh
     * @version v1
     * @param int id 1 周期分组预设ID required
     */
    public function copy()
    {
        $param = $this->request->param();

        //参数验证
        if (!$this->validate->scene('copy')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        $result = (new ProductDurationGroupPresetsModel())->copyPresets($param);

        return json($result);
    }

}

