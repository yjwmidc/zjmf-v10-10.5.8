<?php
namespace addon\idcsmart_cloud\controller\clientarea;

use app\event\controller\PluginBaseController;
use addon\idcsmart_cloud\model\IdcsmartSecurityGroupModel;
use addon\idcsmart_cloud\model\IdcsmartSecurityGroupHostLinkModel;
use addon\idcsmart_cloud\validate\IdcsmartSecurityGroupValidate;
use app\common\model\HostModel;

/**
 * @title 安全组管理
 * @desc 安全组管理
 * @use addon\idcsmart_cloud\controller\clientarea\SecurityGroupController
 */
class SecurityGroupController extends PluginBaseController
{
    public function initialize()
    {
        parent::initialize();
        $this->validate = new IdcsmartSecurityGroupValidate();
    }

    /**
     * 时间 2022-06-08
     * @title 安全组列表
     * @desc 安全组列表
     * @author theworld
     * @version v1
     * @url /console/v1/security_group
     * @method  GET
     * @param string keywords - 关键字
     * @param int page - 页数
     * @param int limit - 每页条数
     * @param string orderby - 排序 id
     * @param string sort - 升/降序 asc,desc
     * @return array list - 安全组
     * @return int list[].id - 安全组ID
     * @return string list[].name - 名称 
     * @return string list[].description - 描述 
     * @return int list[].create_time - 创建时间 
     * @return int list[].host_num - 产品数量 
     * @return int list[].rule_num - 规则数量
     * @return int count - 安全组总数
     */
    public function list()
    {
        // 合并分页参数
        $param = array_merge($this->request->param(), ['page' => $this->request->page, 'limit' => $this->request->limit, 'sort' => $this->request->sort]);
        
        // 实例化模型类
        $IdcsmartSecurityGroupModel = new IdcsmartSecurityGroupModel();

        // 获取安全组列表
        $data = $IdcsmartSecurityGroupModel->idcsmartSecurityGroupList($param);

        $result = [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => $data
        ];
        return json($result);
    }

    /**
     * 时间 2022-06-08
     * @title 安全组详情
     * @desc 安全组详情
     * @author theworld
     * @version v1
     * @url /console/v1/security_group/:id
     * @method  GET
     * @param int id - 安全组ID required
     * @return object security_group - 安全组
     * @return int security_group.id - 安全组ID
     * @return string security_group.name - 名称 
     * @return string security_group.description - 描述 
     * @return int security_group.create_time - 创建时间 
     */
    public function index()
    {
        // 接收参数
        $param = $this->request->param();
        
        // 实例化模型类
        $IdcsmartSecurityGroupModel = new IdcsmartSecurityGroupModel();

        // 获取安全组
        $securityGroup = $IdcsmartSecurityGroupModel->indexIdcsmartSecurityGroup($param['id']);

        $result = [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => [
                'security_group' => $securityGroup
            ]
        ];
        return json($result);
    }

    /**
     * 时间 2022-06-08
     * @title 添加安全组
     * @desc 添加安全组
     * @author theworld
     * @version v1
     * @url /console/v1/security_group
     * @method  POST
     * @param string name - 名称 required
     * @param string description - 描述
     */
    public function create()
    {
        // 接收参数
        $param = $this->request->param();

        // 参数验证
        if (!$this->validate->scene('create')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($this->validate->getError())]);
        }
        
        // 实例化模型类
        $IdcsmartSecurityGroupModel = new IdcsmartSecurityGroupModel();

        // 创建安全组
        $result = $IdcsmartSecurityGroupModel->createIdcsmartSecurityGroup($param);

        return json($result);
    }

    /**
     * 时间 2022-06-08
     * @title 修改安全组
     * @desc 修改安全组
     * @author theworld
     * @version v1
     * @url /console/v1/security_group/:id
     * @method  PUT
     * @param int id - 安全组ID required
     * @param string name - 名称 required
     * @param string description - 描述
     */
    public function update()
    {
        // 接收参数
        $param = $this->request->param();

        // 参数验证
        if (!$this->validate->scene('update')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($this->validate->getError())]);
        }
        
        // 实例化模型类
        $IdcsmartSecurityGroupModel = new IdcsmartSecurityGroupModel();

        // 修改安全组
        $result = $IdcsmartSecurityGroupModel->updateIdcsmartSecurityGroup($param);

        return json($result);
    }

    /**
     * 时间 2022-06-08
     * @title 删除安全组
     * @desc 删除安全组
     * @author theworld
     * @version v1
     * @url /console/v1/security_group/:id
     * @method  DELETE
     * @param int id - 安全组ID required
     */
    public function delete()
    {
        // 接收参数
        $param = $this->request->param();
        
        // 实例化模型类
        $IdcsmartSecurityGroupModel = new IdcsmartSecurityGroupModel();

        // 删除安全组
        $result = $IdcsmartSecurityGroupModel->deleteIdcsmartSecurityGroup($param['id']);

        return json($result);
    }

    /**
     * 时间 2022-06-09
     * @title 安全组实例列表
     * @desc 安全组实例列表
     * @author theworld
     * @version v1
     * @url /console/v1/security_group/:id/host
     * @method  GET
     * @param int id - 安全组ID required
     * @param int page - 页数
     * @param int limit - 每页条数
     * @param string orderby - 排序 id
     * @param string sort - 升/降序 asc,desc
     * @return array list - 实例
     * @return int list[].id - 实例ID
     * @return string list[].name - 名称 
     * @return string list[].ip - IP
     * @return int count - 实例总数
     */
    public function securityGroupHostList()
    {
        // 合并分页参数
        $param = array_merge($this->request->param(), ['page' => $this->request->page, 'limit' => $this->request->limit, 'sort' => $this->request->sort]);

        // 实例化模型类
        $IdcsmartSecurityGroupHostLinkModel = new IdcsmartSecurityGroupHostLinkModel();

        // 关联安全组
        $data = $IdcsmartSecurityGroupHostLinkModel->idcsmartSecurityGroupHostList($param);

        $result = [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => $data
        ];
        return json($result);
    }


    /**
     * 时间 2022-09-08
     * @title 关联安全组
     * @desc 关联安全组
     * @author theworld
     * @version v1
     * @url /console/v1/security_group/:id/host/:host_id
     * @method  POST
     * @param int id - 安全组ID required
     * @param int host_id - 产品ID required
     */
    public function linkSecurityGroup()
    {
        $param = $this->request->param();

        // 参数验证
        if (!$this->validate->scene('link')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($this->validate->getError())]);
        }

        // 实例化模型类
        $IdcsmartSecurityGroupHostLinkModel = new IdcsmartSecurityGroupHostLinkModel();

        // 关联安全组
        $result = $IdcsmartSecurityGroupHostLinkModel->linkSecurityGroup($param);

        return json($result);
    }

    /**
     * 时间 2022-09-08
     * @title 取消关联安全组
     * @desc 取消关联安全组
     * @author theworld
     * @version v1
     * @url /console/v1/security_group/:id/host/:host_id
     * @method  DELETE
     * @param int id - 安全组ID required
     * @param int host_id - 产品ID required
     */
    public function unlinkSecurityGroup()
    {
        $param = $this->request->param();

        // 参数验证
        if (!$this->validate->scene('unlink')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($this->validate->getError())]);
        }

        // 实例化模型类
        $IdcsmartSecurityGroupHostLinkModel = new IdcsmartSecurityGroupHostLinkModel();

        // 取消关联安全组
        $result = $IdcsmartSecurityGroupHostLinkModel->unlinkSecurityGroup($param);

        return json($result);
    }

    /**
     * 时间 2024-07-02
     * @title 批量关联安全组
     * @desc  批量关联安全组
     * @author hh
     * @version v1
     * @url /console/v1/security_group/:id/host
     * @method  POST
     * @param   int id - 安全组ID required
     * @param   array host_id - 产品ID required
     * @return  int [].status - 状态码(200=成功,400=失败)
     * @return  string [].msg - 信息
     * @return  string [].name - 标识
     * @return  int [].id - 产品ID
     */
    public function batchLinkSecurityGroup()
    {
        $param = $this->request->param();

        if(!isset($param['host_id']) || !is_array($param['host_id'])){
            return json(['status'=>400, 'msg'=>lang('param_error')]);
        }
        if(empty($param['host_id'])){
            return json(['status'=>400, 'msg'=>lang_plugins('id_error')]);
        }

        $host = HostModel::field('id,name')->whereIn('id', $param['host_id'])->where('client_id', get_client_id() )->where('is_delete', 0)->select()->toArray();
        $host = array_column($host, 'name', 'id');

        // 实例化模型类
        $IdcsmartSecurityGroupHostLinkModel = new IdcsmartSecurityGroupHostLinkModel();

        $result = [
            'status' => 200,
            'msg'    => lang_plugins('success_message'),
            'data'   => [],
        ];

        foreach($param['host_id'] as $hostId){
            if(!isset($host[$hostId])){
                $result['data'][] = [
                    'status'    => 400,
                    'msg'       => lang_plugins('host_is_not_exist'),
                    'name'      => 'ID-#' . $hostId,
                    'id'        => $hostId,
                ];
            }else{
                // 关联安全组
                $linkRes = $IdcsmartSecurityGroupHostLinkModel->linkSecurityGroup([
                    'id'        => $param['id'],
                    'host_id'   => $hostId,
                ]);
                $result['data'][] = [
                    'status'    => $linkRes['status'],
                    'msg'       => $linkRes['msg'],
                    'name'      => $host[$hostId],
                    'id'        => $hostId,
                ];
            }
        }
        return json($result);
    }

}