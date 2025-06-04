<?php
namespace addon\idcsmart_file_download\controller\clientarea;

use app\event\controller\PluginBaseController;
use addon\idcsmart_file_download\model\IdcsmartFileModel;
use addon\idcsmart_file_download\model\IdcsmartFileFolderModel;
use app\common\model\ClientModel;

/**
 * @title 文件下载
 * @desc 文件下载
 * @use addon\idcsmart_file_download\controller\clientarea\IndexController
 */
class IndexController extends PluginBaseController
{
    /**
     * 时间 2022-06-22
     * @title 获取文件夹
     * @desc 获取文件夹
     * @author theworld
     * @version v1
     * @url /console/v1/file/folder
     * @method  GET
     * @return array list - 文件夹
     * @return int list[].id - 文件夹ID
     * @return string list[].name - 名称
     * @return int list[].default - 默认文件夹0否1是
     * @return int list[].file_num - 文件数量 
     * @return int count - 全部文件数量 
     */
    public function idcsmartFileFolderList()
    {
        // 实例化模型类
        $IdcsmartFileFolderModel = new IdcsmartFileFolderModel();

        // 获取文件夹
        $data = $IdcsmartFileFolderModel->idcsmartFileFolderList('home');

        $result = [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => $data
        ];
        return json($result);
    }

    /**
     * 时间 2022-06-22
     * @title 文件列表
     * @desc 文件列表
     * @author theworld
     * @version v1
     * @url /console/v1/file
     * @method  GET
     * @param int addon_idcsmart_file_folder_id - 文件夹ID 
     * @return array list - 文件
     * @return int list[].id - 文件ID
     * @return string list[].name - 名称
     * @return string list[].filetype - 文件类型 
     * @return string list[].filesize - 文件大小 
     * @return string list[].create_time - 上传时间 
     * @return string list[].description - 描述 
     * @return int count - 文件总数
     */
    public function idcsmartFileList()
    {
        // 合并分页参数
        $param = array_merge($this->request->param(), ['page' => $this->request->page, 'limit' => $this->request->limit, 'sort' => $this->request->sort]);

        // 实例化模型类
        $IdcsmartFileModel = new IdcsmartFileModel();

        // 获取文件列表
        $data = $IdcsmartFileModel->idcsmartFileList($param, 'home');

        $result = [
            'status' => 200,
            'msg' => lang_plugins('success_message'),
            'data' => $data
        ];
        return json($result);
    }

    /**
     * 时间 2022-06-22
     * @title 下载文件
     * @desc 下载文件
     * @author theworld
     * @version v1
     * @url /console/v1/file/:id/download
     * @method  GET
     * @param int id - 文件ID required
     */
    public function idcsmartFileDownload()
    {
        // 接收参数
        $param = $this->request->param();
        
        $param['jwt'] = $param['jwt'] ?? '';
        $Check = new \app\http\middleware\Check();
        $result = $Check->checkToken($this->request, false, $param['jwt']);
        if ($result['status'] != 200){
            return json($result);
        }else{
            $jwtToken = $result['data']['jwt_token'];
            $this->request->client_id = $jwtToken['id'];
            $this->request->client_name = $jwtToken['name'];
            $this->request->client_remember_password = $jwtToken['remember_password'];
        }

        $clientId = get_client_id();
        $ClientModel = new ClientModel();
        $client = $ClientModel->where('status', 1)->where('id', $clientId)->find();
        if(empty($client)){
            return json(['status' => 400, 'msg' => lang_plugins('fail_message')]);
        }
        
        // 实例化模型类
        $IdcsmartFileModel = new IdcsmartFileModel();

        // 获取文件
        $data = $IdcsmartFileModel->idcsmartFileDetail($param['id'], 'home');

        if(!empty($data)){
            # 记录日志
            $clientId = get_client_id();
            active_log(lang_plugins('log_client_download_file', ['{client}'=>'client#'.$clientId.'#'.request()->client_name.'#','{name}'=>$data['name']]), 'addon_idcsmart_file', $param['id']);
            return json(['status' => 200, 'msg' => lang_plugins('success_message'),'data'=>[
                'url' => getOssUrl([
                    'file_path' => $data['file_path'],
                    'file_name' => $data['file_name']
                ])['url']
            ]]);
        }else{
            return json(['status' => 400, 'msg' => lang_plugins('file_is_not_exist')]);
        }
    }
}