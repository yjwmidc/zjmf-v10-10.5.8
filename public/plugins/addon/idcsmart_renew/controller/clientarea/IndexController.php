<?php
namespace addon\idcsmart_renew\controller\clientarea;

use addon\idcsmart_renew\model\IdcsmartRenewModel;
use addon\idcsmart_renew\model\IdcsmartRenewAutoModel;
use app\event\controller\PluginBaseController;
use addon\idcsmart_renew\validate\IdcsmartRenewValidate;

/**
 * @title 续费(会员中心)
 * @desc 续费(会员中心)
 * @use addon\idcsmart_renew\controller\clientarea\IndexController
 */
class IndexController extends PluginBaseController
{
    public function initialize()
    {
        parent::initialize();
        $this->validate = new IdcsmartRenewValidate();
        app('http')->name('home');
    }

    /**
     * 时间 2022-06-02
     * @title 续费页面
     * @desc 续费页面
     * @author wyh
     * @version v1
     * @url /console/v1/host/:id/renew
     * @method  GET
     * @param int id - 产品ID required
     * @return array host -
     * @return float host[].price 0.01 实际支付的金额
     * @return string host[].billing_cycle 小时 周期
     * @return int host[].duration 3600 周期时间
     * @return float host[].base_price - 基础原价(不包括优惠码，客户等级等折扣)
     * @return int host[].id - 周期比例ID
     * @return string host[].name_show - 周期名字显示
     * @return float host[].prr - 与产品当前周期比例的比值（后台产品内页开启按比例续费的功能会使用）
     * @return float host[].price_save - 保存至数据库的续费金额
     * @return float host[].renew_amount - 续费金额(自有软件使用)
     * @return boolean host[].max_renew - 当前周期，续费金额已经减了客户等级折扣金额，所以不需要再减一次(当前周期为true，其他周期为false，手动输入优惠码时，也为false)
     */
    public function renewPage()
    {
        $param = $this->request->param();

        $IdcsmartRenewModel = new IdcsmartRenewModel();

        $result = $IdcsmartRenewModel->renewPage($param);

        return json($result);
    }

    /**
     * 时间 2022-06-02
     * @title 续费
     * @desc 续费
     * @author wyh
     * @version v1
     * @url /console/v1/host/:id/renew
     * @method  POST
     * @param int id - 产品ID required
     * @param string billing_cycle - 周期(通用产品是中文，云产品是英文;这里要注意，根据续费页面返回的周期来传，不停的模块可能传的不一样) required
     * @param object customfield - 自定义参数,比如优惠码参数传:{"promo_code":["pr8nRQOGbmv5"]}
     * @param string client_operate_password - 操作密码,需要验证时传
     */
    public function renew()
    {
        $param = $this->request->param();

        $IdcsmartRenewModel = new IdcsmartRenewModel();

        $result = $IdcsmartRenewModel->renew($param);

        return json($result);
    }

    /**
     * 时间 2022-06-02
     * @title 批量续费页面
     * @desc 批量续费页面
     * @author wyh
     * @version v1
     * @url /console/v1/host/renew/batch
     * @method  GET
     * @param array ids - 产品ID,数组 required
     * @return array list - 产品
     * @return int list[].id - 产品ID
     * @return int list[].product_id - 商品ID
     * @return string list[].product_name - 商品名称
     * @return string list[].name - 标识
     * @return int list[].active_time - 开通时间
     * @return int list[].due_time - 到期时间
     * @return string list[].first_payment_amount - 金额
     * @return string list[].billing_cycle - 周期
     * @return string list[].status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败
     * @return string list[].billing_cycles - 可续费周期
     * @return string list[].billing_cycles.price - 价格
     * @return string list[].billing_cycles.billing_cycle - 周期
     * @return string list[].billing_cycles.duration - 周期时间
     * @return string list[].billing_cycles.base_price - 基础原价(不包括优惠码，客户等级等折扣)
     * @return string list[].billing_cycles.id - 周期比例ID
     * @return string list[].billing_cycles.name_show - 周期名字显示
     * @return string list[].billing_cycles.prr - 与产品当前周期比例的比值（后台产品内页开启按比例续费的功能会使用）
     * @return string list[].billing_cycles.price_save - 保存至数据库的续费金额
     * @return string list[].billing_cycles.renew_amount - 续费金额(自有软件使用)
     * @return string list[].billing_cycles.max_renew - 当前周期，续费金额已经减了客户等级折扣金额，所以不需要再减一次(当前周期为true，其他周期为false，手动输入优惠码时，也为false)
     */
    public function renewBatchPage()
    {
        $param = $this->request->param();

        $IdcsmartRenewModel = new IdcsmartRenewModel();

        $result = $IdcsmartRenewModel->renewBatchPage($param);

        return json($result);
    }

    /**
     * 时间 2022-06-02
     * @title 批量续费
     * @desc 批量续费
     * @author wyh
     * @version v1
     * @url /console/v1/host/renew/batch
     * @method  POST
     * @param array ids - 产品ID,数组 required
     * @param object billing_cycles - 周期,对象{"id":"小时"} required
     * @param object customfield - 自定义参数,比如优惠码参数传:{"promo_code":["pr8nRQOGbmv5"]}
     * @param string client_operate_password - 操作密码,需要验证时传
     */
    public function renewBatch()
    {
        $param = $this->request->param();

        $IdcsmartRenewModel = new IdcsmartRenewModel();

        $result = $IdcsmartRenewModel->renewBatch($param);

        return json($result);
    }

    /**
     * 时间 2022-10-14
     * @title 获取自动续费设置
     * @desc 获取自动续费设置
     * @author theworld
     * @version v1
     * @url /console/v1/host/:id/renew/auto
     * @method  GET
     * @param int id - 产品ID required
     * @param int status - 自动续费状态1开启,0关闭
     */
    public function renewAutoStatus()
    {
        $param = $this->request->param();

        $IdcsmartRenewAutoModel = new IdcsmartRenewAutoModel();

        $result = $IdcsmartRenewAutoModel->getStatus($param['id']);

        return json($result);
    }
    
    /**
     * 时间 2022-10-14
     * @title 自动续费设置
     * @desc 自动续费设置
     * @author theworld
     * @version v1
     * @url /console/v1/host/:id/renew/auto
     * @method  PUT
     * @param int id - 产品ID required
     * @param int status - 自动续费状态1开启,0关闭 required
     */
    public function updateRenewAutoStatus()
    {
        $param = $this->request->param();

        // 参数验证
        if (!$this->validate->scene('update_status')->check($param)){
            return json(['status' => 400 , 'msg' => lang_plugins($this->validate->getError())]);
        }

        $IdcsmartRenewAutoModel = new IdcsmartRenewAutoModel();

        $result = $IdcsmartRenewAutoModel->updateStatus($param);

        return json($result);
    }
}