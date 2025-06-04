<?php
namespace app\admin\controller;

use app\common\model\LocalImageGroupModel;
use app\admin\validate\LocalImageGroupValidate;

/**
 * @title 本地镜像分组
 * @desc 本地镜像分组
 * @use app\admin\controller\LocalImageGroupController
 */
class LocalImageGroupController extends AdminBaseController
{
    public function initialize()
    {
        parent::initialize();
        $this->validate = new LocalImageGroupValidate();
    }

    /**
     * 时间 2024-04-02
     * @title 本地镜像分组列表
     * @desc 本地镜像分组列表
     * @author theworld
     * @version v1
     * @url /admin/v1/local_image_group
     * @method  GET
     * @return array list -  分组
     * @return int list[].id - 分组ID
     * @return string list[].name - 名称
     */
    public function list()
    {
        // 接收参数
        $param = $this->request->param();

        // 实例化模型类
        $LocalImageGroupModel = new LocalImageGroupModel();

        // 导航列表
        $data = $LocalImageGroupModel->groupList($param);

        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data
        ];
        return json($result);
    }

    /**
     * 时间 2024-04-02
     * @title 创建本地镜像分组
     * @desc 创建本地镜像分组
     * @author theworld
     * @version v1
     * @url /admin/v1/local_image_group
     * @method  POST
     * @param string name - 名称 required
     * @param string icon - 图标 required
     */
    public function create()
    {
        // 接收参数
        $param = $this->request->param();

        // 参数验证
        if (!$this->validate->scene('create')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        // 实例化模型类
        $LocalImageGroupModel = new LocalImageGroupModel();
        
        // 创建本地镜像分组
        $result = $LocalImageGroupModel->createGroup($param);

        return json($result);
    }

    /**
     * 时间 2024-04-02
     * @title 编辑本地镜像分组
     * @desc 编辑本地镜像分组
     * @author theworld
     * @version v1
     * @url /admin/v1/local_image_group/:id
     * @method  PUT
     * @param int id - 分组ID required
     * @param string name - 名称 required
     * @param string icon - 图标 required
     */
    public function update()
    {
        // 接收参数
        $param = $this->request->param();

        // 参数验证
        if (!$this->validate->scene('update')->check($param)){
            return json(['status' => 400 , 'msg' => lang($this->validate->getError())]);
        }

        // 实例化模型类
        $LocalImageGroupModel = new LocalImageGroupModel();
        
        // 编辑本地镜像分组
        $result = $LocalImageGroupModel->updateGroup($param);

        return json($result);
    }

    /**
     * 时间 2024-04-02
     * @title 删除本地镜像分组
     * @desc 删除本地镜像分组
     * @author theworld
     * @version v1
     * @url /admin/v1/local_image_group/:id
     * @method  DELETE
     * @param int id - 分组ID required
     */
    public function delete()
    {
        // 接收参数
        $param = $this->request->param();

        // 实例化模型类
        $LocalImageGroupModel = new LocalImageGroupModel();
        
        // 删除本地镜像分组
        $result = $LocalImageGroupModel->deleteGroup($param['id']);

        return json($result);
    }

    /**
     * 时间 2024-04-02
     * @title 本地镜像分组排序
     * @desc 本地镜像分组排序
     * @author theworld
     * @version v1
     * @url /admin/v1/local_image_group/order
     * @method  PUT
     * @param array id - 分组ID required
     */
    public function order()
    {
        // 接收参数
        $param = $this->request->param();

        // 实例化模型类
        $LocalImageGroupModel = new LocalImageGroupModel();
        
        // 本地镜像分组排序
        $result = $LocalImageGroupModel->groupOrder($param);

        return json($result);
    }
}