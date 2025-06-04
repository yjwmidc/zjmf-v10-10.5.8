<?php
namespace addon\idcsmart_ticket\model;

use addon\idcsmart_ticket\logic\IdcsmartTicketLogic;
use app\admin\model\AdminModel;
use app\common\model\ClientModel;
use think\Exception;
use think\Model;

/*
 * @author wyh
 * @time 2022-06-20
 */
class IdcsmartTicketReplyModel extends Model
{
    protected $name = 'addon_idcsmart_ticket_reply';

    # 设置字段信息
    protected $schema = [
        'id'                               => 'int',
        'ticket_id'                        => 'int',
        'type'                             => 'string',
        'rel_id'                           => 'int',
        'content'                          => 'string',
        'attachment'                       => 'string',
        'create_time'                      => 'int',
        'update_time'                      => 'int',
        'is_downstream'                    => 'int',
        'downstream_ticket_reply_id'       => 'int',
        'upstream_ticket_reply_id'         => 'int',
    ];

    /**
     * 时间 2022-09-23
     * @title 修改工单回复
     * @desc 修改工单回复
     * @author wyh
     * @version v1
     * @param int id - 工单回复ID required
     * @param int content - 内容 required
     */
    public function ticketReplyUpdate($param)
    {
        $this->startTrans();

        try{
            $ticketReply = $this->where('id',$param['id'])->find();
            if (empty($ticketReply)){
                throw new \Exception(lang_plugins('ticket_reply_is_not_exist'));
            }

            if (!IdcsmartTicketLogic::checkUpstreamTicket($ticketReply['ticket_id'])){
                throw new \Exception(lang_plugins('ticket_upstream_cannot_operate'));
            }

            $ticketReply->save([
                'content'=>$param['content']??'',
                'update_time'=>time()
            ]);

            # 记录日志
            $ticketId = $ticketReply['ticket_id'];
            if ($ticketReply['type']=='Admin'){
                $AdminModel = new AdminModel();
                $admin = $AdminModel->find($ticketReply['rel_id']);
                $name = $admin['name'];
            }else{
                $ClientModel = new ClientModel();
                $client = $ClientModel->find($ticketReply['rel_id']);
                $name = $client['username'];
            }

            active_log(lang_plugins('ticket_log_admin_update_ticket_reply', ['{admin}'=>'admin#'.request()->admin_id.'#' .request()->admin_name.'#','{name}'=>$name]), 'addon_idcsmart_ticket', $ticketId);
            $this->commit();
        }catch (\Exception $e){
            $this->rollback();
            return ['status'=>400,'msg'=>$e->getMessage()];
        }

        $IdcsmartTicketModel = new IdcsmartTicketModel();
        $ticket = $IdcsmartTicketModel->find($ticketReply['ticket_id']);

        IdcsmartTicketLogic::pushTicketReply($ticket,$ticketReply,$param);

        return ['status'=>200,'msg'=>lang_plugins('success_message')];
    }

    /**
     * 时间 2022-09-23
     * @title 删除工单回复
     * @desc 删除工单回复
     * @author wyh
     * @version v1
     * @param int id - 工单回复ID required
     */
    public function ticketReplyDelete($param)
    {
        $this->startTrans();

        try{
            $ticketReply = $this->where('id',$param['id'])->find();
            if (empty($ticketReply)){
                throw new \Exception(lang_plugins('ticket_reply_is_not_exist'));
            }
            if (!IdcsmartTicketLogic::checkUpstreamTicket($ticketReply['ticket_id'])){
                throw new \Exception(lang_plugins('ticket_upstream_cannot_operate'));
            }
            $ticketReply->delete();
            # 记录日志
            $ticketId = $ticketReply['ticket_id'];
            if ($ticketReply['type']=='Admin'){
                $AdminModel = new AdminModel();
                $admin = $AdminModel->find($ticketReply['rel_id']);
                $name = $admin['name'];
            }else{
                $ClientModel = new ClientModel();
                $client = $ClientModel->find($ticketReply['rel_id']);
                $name = $client['username'];
            }
            active_log(lang_plugins('ticket_log_admin_delete_ticket_reply', ['{admin}'=>'admin#'.request()->admin_id.'#' .request()->admin_name.'#','{name}'=>$name]), 'addon_idcsmart_ticket', $ticketId);
            $this->commit();
        }catch (\Exception $e){
            $this->rollback();
            return ['status'=>400,'msg'=>$e->getMessage()];
        }

        $IdcsmartTicketModel = new IdcsmartTicketModel();
        $ticket = $IdcsmartTicketModel->find($ticketReply['ticket_id']);

        IdcsmartTicketLogic::pushTicketReplyDelete($ticket,$ticketReply,$param);

        return ['status'=>200,'msg'=>lang_plugins('success_message')];
    }

    /**
     * @时间 2024-12-09
     * @title 获取工单管理员最后回复时间
     * @desc  获取工单管理员最后回复时间,没回复返回0
     * @author hh
     * @version v1
     * @param   int $id - 工单ID
     * @return  int
     */
    public function getAdminLastReplyTime($id)
    {
        $lastReplyTime = $this
                        ->where('ticket_id', $id)
                        ->where('type', 'Admin')
                        ->order('id', 'desc')
                        ->value('create_time');
                        
        return $lastReplyTime ?? 0;
    }

}
