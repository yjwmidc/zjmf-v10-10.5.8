<?php
namespace app\admin\controller;

use app\admin\model\NoticeModel;

/**
 * @title 消息通知
 * @desc 消息通知
 * @use app\admin\controller\NoticeController
 */
class NoticeController extends AdminBaseController
{
    /**
     * 时间 2024-12-12
     * @title 异步请求，获取官方通知，更新本地通知信息
     * @desc 异步请求，获取官方通知，更新本地通知信息
     * @author wyh
     * @version v1
     * @url /admin/v1/notice/sync
     * @method GET
     */
    public function sync()
    {
        $NoticeModel = new NoticeModel();

        $result = $NoticeModel->sync();

        return json($result);
    }

    /**
     * 时间 2024-12-12
     * @title 通知列表
     * @desc 通知列表
     * @author wyh
     * @version v1
     * @url /admin/v1/notice
     * @method  get
     * @param string keywords - 关键字搜索,搜索范围:标题，内容
     * @param int read - 是否已读：0未读消息，1已读消息
     * @param string type - 消息类型：idcsmart官方通知，system系统通知
     * @param int page - 页数
     * @param int limit - 每页条数
     * @param string orderby - 排序字段，默认id
     * @param string sort - 排序(desc,asc)
     * @return object list - 通知列表
     * @return int list[].id - ID
     * @return string list[].title - 标题
     * @return string list[].content - 内容
     * @return string list[].attachment - 附件，逗号分隔
     * @return int list[].accept_time - 接收时间
     * @return int list[].read - 是否已读：1是，0否
     * @return string list[].type - 消息类型：idcsmart官方通知，system系统通知
     * @return int list[].rel_id - 关联ID，消息类型是idcsmart时，表示官方消息ID
     * @return int count - 总数
     * @return int total_count - 所有消息未读总数
     */
    public function list()
    {
        $param = array_merge($this->request->param(), ['page' => $this->request->page, 'limit' => $this->request->limit, 'sort' => $this->request->sort]);

        $NoticeModel = new NoticeModel();

        $result = $NoticeModel->noticeList($param);

        return json($result);
    }

    /**
     * 时间 2024-12-12
     * @title 通知详情
     * @desc 通知详情
     * @author wyh
     * @version v1
     * @url /admin/v1/notice/:id
     * @method  get
     * @param int id - 通知ID
     * @return object notice - 通知列表
     * @return int notice.id - ID
     * @return string notice.title - 标题
     * @return string notice.content - 内容
     * @return string notice.attachment - 附件，逗号分隔
     * @return int notice.accept_time - 接收时间
     * @return int notice.read - 是否已读：1是，0否
     * @return string notice.type - 消息类型：idcsmart官方通知，system系统通知
     * @return int notice.rel_id - 关联ID，消息类型是idcsmart时，表示官方消息ID
     * @return int count - 总数
     * @return object before - 上一条
     * @return object next - 下一条
     */
    public function detail()
    {
        $param = $this->request->param();

        $NoticeModel = new NoticeModel();

        $result = $NoticeModel->detail($param);

        return json($result);
    }

    /**
     * 时间 2024-12-12
     * @title 标记已读
     * @desc 标记已读
     * @author wyh
     * @version v1
     * @url /admin/v1/notice/mark_read
     * @method  post
     * @param array ids - 通知ID，数组
     * @param int all - 是否全部标记为已读：1是，0否
     */
    public function markRead()
    {
        $param = $this->request->param();

        $NoticeModel = new NoticeModel();

        $result = $NoticeModel->markRead($param);

        return json($result);
    }

    /**
     * 时间 2024-12-12
     * @title 删除通知
     * @desc 删除通知
     * @author wyh
     * @version v1
     * @url /admin/v1/notice
     * @method  delete
     * @param array ids - 通知ID，数组
     */
    public function delete()
    {
        $param = $this->request->param();

        $NoticeModel = new NoticeModel();

        $result = $NoticeModel->noticeDelete($param);

        return json($result);
    }

}