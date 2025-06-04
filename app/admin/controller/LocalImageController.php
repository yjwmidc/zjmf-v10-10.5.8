<?php
namespace app\admin\controller;

use app\common\model\LocalImageModel;
use app\admin\validate\LocalImageValidate;

/**
 * @title 本地镜像
 * @desc 本地镜像
 * @use app\admin\controller\LocalImageController
 */
class LocalImageController extends AdminBaseController
{
    public function initialize()
    {
        parent::initialize();
        $this->validate = new LocalImageValidate();
    }

    /**
     * 时间 2024-10-23
     * @title 本地镜像列表
     * @desc 本地镜像列表
     * @author theworld
     * @version v1
     * @url /admin/v1/local_image
     * @method  GET
     * @return array list -  镜像
     * @return int list[].id - 镜像ID
     * @return string list[].group_id - 分组ID
     * @return string list[].group_name - 分组名称
     * @return string list[].icon - 图标
     * @return string list[].name - 镜像名称
     */
    public function list()
    {
        // 接收参数
        $param = $this->request->param();
        $param['language'] = $param['language'] ?? 'zh-cn';
        
        // 实例化模型类
        $LocalImageModel = new LocalImageModel();

        // 镜像列表
        $data = $LocalImageModel->imageList($param);

        $result = [
            'status' => 200,
            'msg' => lang('success_message'),
            'data' => $data
        ];
        return json($result);
    }

    /**
     * 时间 2024-10-23
     * @title 创建本地镜像
     * @desc 创建本地镜像
     * @author theworld
     * @version v1
     * @url /admin/v1/local_image
     * @method  POST
     * @param int group_id - 分组ID required
     * @param string name - 名称 required
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
        $LocalImageModel = new LocalImageModel();
        
        // 创建本地镜像
        $result = $LocalImageModel->createImage($param);

        return json($result);
    }

    /**
     * 时间 2024-10-23
     * @title 编辑本地镜像
     * @desc 编辑本地镜像
     * @author theworld
     * @version v1
     * @url /admin/v1/local_image/:id
     * @method  PUT
     * @param int id - 镜像ID required
     * @param int group_id - 分组ID required
     * @param string name - 名称 required
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
        $LocalImageModel = new LocalImageModel();
        
        // 编辑本地镜像
        $result = $LocalImageModel->updateImage($param);

        return json($result);
    }

    /**
     * 时间 2024-10-23
     * @title 删除本地镜像
     * @desc 删除本地镜像
     * @author theworld
     * @version v1
     * @url /admin/v1/local_image/:id
     * @method  DELETE
     * @param int id - 镜像ID required
     */
    public function delete()
    {
        // 接收参数
        $param = $this->request->param();

        // 实例化模型类
        $LocalImageModel = new LocalImageModel();
        
        // 删除本地镜像
        $result = $LocalImageModel->deleteImage($param['id']);

        return json($result);
    }

    /**
     * 时间 2024-10-23
     * @title 本地镜像排序
     * @desc 本地镜像排序
     * @author theworld
     * @version v1
     * @url /admin/v1/local_image/order
     * @method  PUT
     * @param array id - 镜像ID required
     */
    public function order()
    {
        // 接收参数
        $param = $this->request->param();

        // 实例化模型类
        $LocalImageModel = new LocalImageModel();
        
        // 底部栏镜像排序
        $result = $LocalImageModel->imageOrder($param);

        return json($result);
    }
}