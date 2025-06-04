<?php
namespace app\common\model;

use app\common\logic\ResModuleLogic;
use think\db\Query;
use think\Model;
use think\Db;
use app\common\logic\ModuleLogic;
use app\common\model\NoticeSettingModel;
use app\admin\model\PluginModel;
use app\admin\model\AdminViewModel;

/**
 * @title 产品模型
 * @desc 产品模型
 * @use app\common\model\HostModel
 */
class HostModel extends Model
{
	protected $name = 'host';

    // 设置字段信息
    protected $schema = [
        'id'                    => 'int',
        'client_id'             => 'int',
        'order_id'              => 'int',
        'product_id'            => 'int',
        'server_id'             => 'int',
        'name'                  => 'string',
        'status'                => 'string',
        'suspend_type'          => 'string',
        'suspend_reason'        => 'string',
        'suspend_time'          => 'int',
        'gateway'               => 'string',
        'gateway_name'          => 'string',
        'first_payment_amount'  => 'float',
        'renew_amount'          => 'float',
        'billing_cycle'         => 'string',
        'billing_cycle_name'    => 'string',
        'billing_cycle_time'    => 'int',
        'notes'                 => 'string',
        'client_notes'          => 'string',
        'active_time'           => 'int',
        'due_time'              => 'int',
        'termination_time'      => 'int',
        'create_time'           => 'int',
        'update_time'           => 'int',
        'downstream_info'       => 'string',
        'downstream_host_id'    => 'int',
        'base_price'            => 'float',
        'ratio_renew'           => 'int',
        'is_delete'             => 'int',  // 逻辑删除
        'delete_time'           => 'int',
        'base_renew_amount'     => 'float',
        'base_info'             => 'string',
        'transfer_time'         => 'int',
        'failed_action'         => 'string',
        'failed_action_times'   => 'int',
        'failed_action_need_handle' => 'int',
        'failed_action_reason'  => 'string',
        'failed_action_trigger_time' => 'int',
        'base_config_options'   => 'string',
        'is_ontrial'            => 'int',
        'first_payment_ontrial' => 'int',
        'is_sub'                => 'int',
    ];

    /**
     * 时间 2022-05-13
     * @title 产品列表
     * @desc 产品列表
     * @author theworld
     * @version v1
     * @param string param.keywords - 关键字,搜索范围:产品ID,商品名称,标识,用户名,邮箱,手机号
     * @param string param.billing_cycle - 付款周期
     * @param int param.client_id - 用户ID
     * @param string param.status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败
     * @param string param.due_time - 到期时间(today=今天内three=最近三天seven=最近七天month=最近一个月custom=自定义expired=已到期)
     * @param int param.start_time - 开始时间
     * @param int param.end_time - 结束时间
     * @param int param.product_id - 商品ID
     * @param string param.name - 标识
     * @param string param.username - 用户名
     * @param string param.email - 邮箱
     * @param string param.phone - 手机号
     * @param int param.server_id - 接口ID
     * @param string param.first_payment_amount - 订购金额
     * @param string param.ip - IP
     * @param string param.tab - 状态using使用中expiring即将到期overdue已逾期deleted已删除
     * @param int param.view_id - 视图ID
     * @param int param.page - 页数
     * @param int param.limit - 每页条数
     * @param string param.orderby id 排序(id,renew_amount,due_time,first_payment_amount,active_time,client_id,reg_time)
     * @param string param.sort - 升/降序 asc,desc
     * @param string module - 搜索:模块
     * @return array list - 产品
     * @return int list[].id - 产品ID 
     * @return int list[].client_id - 用户ID 
     * @return int list[].client_name - 用户名 
     * @return string list[].email - 邮箱 
     * @return string list[].phone_code - 国际电话区号 
     * @return string list[].phone - 手机号 
     * @return string list[].company - 公司 
     * @return int list[].product_id - 商品ID 
     * @return string list[].product_name - 商品名称 
     * @return string list[].name - 标识 
     * @return int list[].active_time - 开通时间 
     * @return int list[].due_time - 到期时间
     * @return string list[].first_payment_amount - 金额
     * @return string list[].billing_cycle - 周期
     * @return string list[].status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败
     * @return string list[].renew_amount - 续费金额
     * @return string list[].client_notes - 用户备注
     * @return int list[].ip_num - IP数量
     * @return string list[].dedicate_ip - 主IP
     * @return string list[].assign_ip - 附加IP(英文逗号分隔)
     * @return string list[].server_name - 商品接口,前台接口调用时不返回
     * @return string list[].admin_notes - 管理员备注,前台接口调用时不返回
     * @return string list[].base_price - 当前周期原价,前台接口调用时不返回
     * @return int list[].client_status - 用户是否启用0:禁用,1:正常,前台接口调用时不返回
     * @return int list[].reg_time - 用户注册时间,前台接口调用时不返回
     * @return string list[].country - 国家,前台接口调用时不返回
     * @return string list[].address - 地址,前台接口调用时不返回
     * @return string list[].language - 语言,前台接口调用时不返回
     * @return string list[].notes - 备注,前台接口调用时不返回
     * @return bool list[].certification - 是否实名认证true是false否(显示字段有certification返回)
     * @return string list[].certification_type - 实名类型person个人company企业(显示字段有certification返回)
     * @return string list[].client_level - 用户等级(显示字段有client_level返回)
     * @return string list[].client_level_color - 用户等级颜色(显示字段有client_level返回)
     * @return string list[].sale - 销售(显示字段有sale返回)
     * @return string list[].addon_client_custom_field_[id] - 用户自定义字段(显示字段有addon_client_custom_field_[id]返回,[id]为用户自定义字段ID)
     * @return string list[].self_defined_field_[id] - 商品自定义字段(显示字段有self_defined_field_[id]返回,[id]为商品自定义字段ID)
     * @return string list[].base_info - 产品基础信息
     * @return int count - 产品总数
     * @return int using_count - 使用中产品数量
     * @return int expiring_count - 即将到期产品数量
     * @return int overdue_count - 已逾期产品数量
     * @return int deleted_count - 已删除产品数量
     * @return int all_count - 全部产品数量
     * @return string total_renew_amount - 总续费金额
     * @return string page_total_renew_amount - 当前页总续费金额
     * @return int failed_action_count - 手动处理产品数量
     */
    public function hostList($param)
    {
        // 获取当前应用
        $app = app('http')->getName();
        $selectField = [];
        $selectDataRange = [];
        if($app=='home'){
            $param['client_id'] = get_client_id();
            if(empty($param['client_id'])){
                return ['list' => [], 'count' => 0];
            }
        }else{
            $param['client_id'] = isset($param['client_id']) ? intval($param['client_id']) : 0;

            // 用户内页列表不使用视图
            if(empty($param['client_id'])){
                // 获取当前显示字段
                $AdminViewModel = new AdminViewModel();
                $adminView = $AdminViewModel->adminViewIndex(['id' => $param['view_id'] ?? 0, 'view'=>'host']);
                if(isset($adminView['status']) && $adminView['status']==1){
                    $selectField = $adminView['select_field'];
                    $selectField = array_flip($selectField);
                    $selectDataRange = $adminView['data_range_switch']==1 ? $adminView['select_data_range'] : [];
                }
                unset($adminView);
            }
            
            $param['product_id'] = isset($param['product_id']) ? intval($param['product_id']) : 0;
        }

        $param['keywords'] = $param['keywords'] ?? '';
        $param['billing_cycle'] = $param['billing_cycle'] ?? '';
        $param['status'] = $param['status'] ?? '';
        $param['due_time'] = $param['due_time'] ?? '';
        $param['start_time'] = intval($param['start_time'] ?? 0);
        $param['end_time'] = intval($param['end_time'] ?? 0);
        $param['host_id'] = intval($param['host_id'] ?? 0);
        $param['name'] = $param['name'] ?? '';
        $param['username'] = $param['username'] ?? '';
        $param['email'] = $param['email'] ?? '';
        $param['phone'] = $param['phone'] ?? '';
        $param['server_id'] = intval($param['server_id'] ?? 0);
        $param['ip'] = $param['ip'] ?? '';
        $param['tab'] = $param['tab'] ?? '';
        $param['orderby'] = isset($param['orderby']) && in_array($param['orderby'], ['id','renew_amount','due_time','first_payment_amount','active_time','client_id','reg_time','status']) ? $param['orderby'] : 'id';
        // 排序字段映射
        $orderReal = [
            'id'                    => 'h.id',
            'renew_amount'          => 'h.renew_amount',
            'due_time'              => 'h.due_time',
            'first_payment_amount'  => 'h.first_payment_amount',
            'active_time'           => 'h.active_time',
            'client_id'             => 'h.client_id',
            'reg_time'              => 'c.id',
            'status'                => 'h.status',
        ];

        $res = hook('get_client_host_id', ['client_id' => get_client_id(false)]);
        $res = array_values(array_filter($res ?? []));
        foreach ($res as $key => $value) {
            if(isset($value['status']) && $value['status']==200){
                $hostId = $value['data']['host'];
                if(empty($hostId)){
                    return ['list' => [], 'count' => 0];
                }
            }
        }
        $param['client_host_id'] = $hostId ?? [];

        $self = isset($param['scene']) && $param['scene']=='ticket' && class_exists('server\idcsmart_common_finance\model\IdcsmartCommonSonHost');

        $where = function (Query $query) use ($param, $app, $selectDataRange, $self){
            // 子产品不显示
            $query->where('h.is_sub',0);
            if($app=='home'){
                $query->where('h.status', '<>', 'Cancelled');

                // 自用判断
                if ($self){
                    $query->where('p.product_id','>',0);
                    $query->whereIn('s.module',['idcsmart_common_finance','idcsmart_common_dcim','idcsmart_common_cloud','idcsmart_common_business']);
                    $query->whereOr("p.id=225 and h.status <> 'Deleted' and h.client_id=".$param['client_id']);
                }
                if(isset($param['scene']) && $param['scene'] == 'security_group'){
                    $query->whereIn('s.module', ['mf_cloud','common_cloud','cloudpods','huawei_cloud'])
                          ->whereNotIn('h.product_id', function($q){
                              $q->name('upstream_product')->field('product_id')->where('mode', 'sync')->select();
                          });
                }
            }
            if(!empty($param['client_id'])){
                $query->where('h.client_id', (int)$param['client_id']);
            }
            if(!empty($param['product_id'])){
                $query->where('h.product_id', (int)$param['product_id']);
            }
            if(!empty($param['keywords'])){
                $query->where('h.id|p.name|h.name|c.username|c.email|c.phone|h.first_payment_amount|hi.dedicate_ip|hi.assign_ip', 'like', "%{$param['keywords']}%");
            }
            if(!empty($param['billing_cycle'])){
                $query->where('h.billing_cycle_name', 'like', "%{$param['billing_cycle']}%");
            }
            if(!empty($param['status'])){
                if($app=='home' && $param['status']=='Pending'){
                    $query->whereIn('h.status', ['Pending', 'Failed']);
                }else{
                    $query->where('h.status', $param['status']);
                }
            }
            if(!empty($param['tab'])){
                if($param['tab']=='using'){
                    $query->whereIn('h.status', ['Pending', 'Active']);
                }else if($param['tab']=='expiring'){
                    $time = time();
                    $renewalFirstDay = configuration('cron_due_renewal_first_day');
                    $timeRenewalFirst = strtotime(date('Y-m-d 23:59:59', $time+$renewalFirstDay*24*3600));
                    $query->whereIn('h.status', ['Pending', 'Active'])->where('h.due_time', '>', $time)->where('h.due_time', '<=', $timeRenewalFirst)->where('h.billing_cycle', '<>', 'free')->where('h.billing_cycle', '<>', 'onetime');
                }else if($param['tab']=='overdue'){
                    $time = time();
                    $query->whereIn('h.status', ['Pending', 'Active', 'Suspended', 'Failed'])->where('h.due_time', '<=', $time)->where('h.billing_cycle', '<>', 'free')->where('h.billing_cycle', '<>', 'onetime');
                }else if($param['tab']=='deleted'){
                    $time = time();
                    $query->where('h.status', 'Deleted');
                }
            }
            if(!empty($param['host_id'])){
                $query->where('h.id', (int)$param['host_id']);
            }
            if(!empty($param['client_host_id'])){
                $query->whereIn('h.id', $param['client_host_id']);
            }
            // if(!empty($param['due_time'])){
            //     if($param['due_time']=='today'){
            //         $query->where('h.due_time', '>=', strtotime(date('Y-m-d')))->where('h.due_time', '<=', strtotime(date('Y-m-d 23:59:59')));
            //     }else if($param['due_time']=='three'){
            //         $query->where('h.due_time', '>=', strtotime(date('Y-m-d')))->where('h.due_time', '<=', strtotime(date('Y-m-d 23:59:59'))+2*24*3600);
            //     }else if($param['due_time']=='seven'){
            //         $query->where('h.due_time', '>=', strtotime(date('Y-m-d')))->where('h.due_time', '<=', strtotime(date('Y-m-d 23:59:59'))+6*24*3600);
            //     }else if($param['due_time']=='month'){
            //         $query->where('h.due_time', '>=', strtotime(date('Y-m-d')))->where('h.due_time', '<=', strtotime(date('Y-m-d 23:59:59'))+29*24*3600);
            //     }else if($param['due_time']=='custom'){
            //         $query->where('h.due_time', '>=', strtotime(date('Y-m-d', $param['start_time'])))->where('h.due_time', '<=', strtotime(date('Y-m-d 23:59:59', $param['end_time'])));
            //     }else if($param['due_time']=='expired'){
            //         $query->where('h.due_time', '<=', time());
            //     }
            // }
            if(!empty($param['start_time']) && !empty($param['end_time'])){
                $query->where('h.due_time', '>=', strtotime(date('Y-m-d', $param['start_time'])))->where('h.due_time', '<=', strtotime(date('Y-m-d 23:59:59', $param['end_time'])));
            }
            // 右下角搜索      // TODO
            if(!empty($param['name'])){
                $query->where('h.name|hi.dedicate_ip', 'like', "%{$param['name']}%");
            }
            if(!empty($param['username'])){
                $query->where('c.username', 'like', "%{$param['username']}%");
            }
            if(!empty($param['email'])){
                $query->where('c.email', 'like', "%{$param['email']}%");
            }
            if(!empty($param['phone'])){
                $query->where('c.phone', 'like', "%{$param['phone']}%");
            }
            if(!empty($param['server_id'])){
                $query->where('h.server_id', $param['server_id']);
            }
            if(!empty($param['ip'])){
                $query->where('hi.dedicate_ip|hi.assign_ip', 'like', "%{$param['ip']}%");
            }
            if(!empty($param['first_payment_amount'])){
                if(strpos($param['first_payment_amount'],'.')!==false){
                    $query->where('h.first_payment_amount', $param['first_payment_amount']);
                }else{
                    $query->where('h.first_payment_amount', 'like', "{$param['first_payment_amount']}.%");
                }
            }
            $query->where('h.is_delete', 0);

            // 模块筛选
            if(!empty($param['module'])){
                $query->where('s.module', $param['module']);
            }

            // 数据范围筛选
            if(!empty($selectDataRange)){
                // 数据范围映射
                $dataRangeReal = [
                    'id'                    => 'h.id',
                    'product_name'          => 'p.id',
                    'host_status'           => 'h.status',
                    'host_name'             => 'h.name',
                    'renew_amount'          => 'h.renew_amount',
                    'due_time'              => 'h.due_time',
                    'server_name'           => 'h.server_id',
                    'admin_notes'           => 'h.notes',
                    'first_payment_amount'  => 'h.first_payment_amount',
                    'billing_cycle_name'    => 'h.billing_cycle_name',
                    'base_price'            => 'h.base_price',
                    'billing_cycle'         => 'h.billing_cycle',
                    'active_time'           => 'h.active_time',
                    'client_id'             => 'h.client_id',
                    'username'              => 'c.username',
                    'company'               => 'c.company',
                    'phone'                 => 'c.phone',
                    'email'                 => 'c.email',
                    'client_status'         => 'c.status',
                    'reg_time'              => 'c.create_time',
                    'country'               => 'c.country_id',
                    'address'               => 'c.address',
                    'language'              => 'c.language',
                    'notes'                 => 'c.notes',
                ];

                foreach ($selectDataRange as $v) {
                    if(in_array($v['key'], ['id', 'renew_amount', 'first_payment_amount', 'base_price', 'client_id'])){
                        if($v['rule']=='equal'){
                            $query->where($dataRangeReal[$v['key']], $v['value']);
                        }else if($v['rule']=='not_equal'){
                            $query->where($dataRangeReal[$v['key']], '<>', $v['value']);
                        }else if($v['rule']=='include'){
                            $query->where($dataRangeReal[$v['key']], 'like', "%{$v['value']}%");
                        }else if($v['rule']=='not_include'){
                            $query->where($dataRangeReal[$v['key']], 'not like', "%{$v['value']}%");
                        }else if($v['rule']=='empty'){
                            $query->whereRaw("{$dataRangeReal[$v['key']]}=0 OR {$dataRangeReal[$v['key']]} is null");
                        }else if($v['rule']=='not_empty'){
                            $query->whereRaw("{$dataRangeReal[$v['key']]}!=0 AND {$dataRangeReal[$v['key']]} is not null");
                        }
                    }else if(in_array($v['key'], ['host_name', 'admin_notes', 'billing_cycle_name', 'username', 'company', 'phone', 'email', 'address', 'notes'])){
                        if($v['rule']=='equal'){
                            $query->where($dataRangeReal[$v['key']], $v['value']);
                        }else if($v['rule']=='not_equal'){
                            $query->where($dataRangeReal[$v['key']], '<>', $v['value']);
                        }else if($v['rule']=='include'){
                            $query->where($dataRangeReal[$v['key']], 'like', "%{$v['value']}%");
                        }else if($v['rule']=='not_include'){
                            $query->where($dataRangeReal[$v['key']], 'not like', "%{$v['value']}%");
                        }else if($v['rule']=='empty'){
                            $query->whereRaw("{$dataRangeReal[$v['key']]}='' OR {$dataRangeReal[$v['key']]} is null");
                        }else if($v['rule']=='not_empty'){
                            $query->whereRaw("{$dataRangeReal[$v['key']]}!='' AND {$dataRangeReal[$v['key']]} is not null");
                        }
                    }else if(in_array($v['key'], ['product_name', 'host_status', 'server_name', 'billing_cycle', 'language'])){
                        if($v['rule']=='equal'){
                            $query->whereIn($dataRangeReal[$v['key']], $v['value']);
                        }else if($v['rule']=='not_equal'){
                            $query->whereNotIn($dataRangeReal[$v['key']], $v['value']);
                        }
                    }else if(in_array($v['key'], ['client_status'])){
                        if($v['rule']=='equal'){
                            $query->where($dataRangeReal[$v['key']], $v['value']);
                        }
                    }else if(in_array($v['key'], ['country'])){
                        if($v['rule']=='equal'){
                            $query->whereIn($dataRangeReal[$v['key']], $v['value']);
                        }else if($v['rule']=='not_equal'){
                            $query->whereNotIn($dataRangeReal[$v['key']], $v['value']);
                        }else if($v['rule']=='empty'){
                            $query->where($dataRangeReal[$v['key']], 0);
                        }else if($v['rule']=='not_empty'){
                            $query->where($dataRangeReal[$v['key']], '<>', 0);
                        }
                    }else if(in_array($v['key'], ['due_time', 'active_time', 'reg_time'])){
                        if($v['rule']=='equal'){
                            $query->where($dataRangeReal[$v['key']], '>=', strtotime($v['value']))->where($dataRangeReal[$v['key']], '<=', strtotime(date("Y-m-d 23:59:59", strtotime($v['value']))));
                        }else if($v['rule']=='interval'){
                            $query->where($dataRangeReal[$v['key']], '>=', strtotime($v['value']['start']))->where($dataRangeReal[$v['key']], '<=', strtotime(date("Y-m-d 23:59:59", strtotime($v['value']['end']))));
                        }else if($v['rule']=='dynamic'){
                            if($v['value']['condition1']=='now'){
                                $day1 = strtotime(date("Y-m-d"));
                            }else if($v['value']['condition1']=='ago'){
                                $day1 = strtotime(date("Y-m-d"))-$v['value']['day1']*24*3600;
                            }else if($v['value']['condition1']=='later'){
                                $day1 = strtotime(date("Y-m-d"))+$v['value']['day1']*24*3600;
                            }
                            if($v['value']['condition2']=='now'){
                                $day2 = strtotime(date("Y-m-d"));
                            }else if($v['value']['condition2']=='ago'){
                                $day2 = strtotime(date("Y-m-d"))-$v['value']['day2']*24*3600;
                            }else if($v['value']['condition2']=='later'){
                                $day2 = strtotime(date("Y-m-d"))+$v['value']['day2']*24*3600;
                            }
                            if($day1>$day2){
                                $start = $day2;
                                $end = strtotime(date("Y-m-d 23:59:59", $day1));
                            }else{
                                $start = $day1;
                                $end = strtotime(date("Y-m-d 23:59:59", $day2));
                            }
                            $query->where($dataRangeReal[$v['key']], '>=', $start)->where($dataRangeReal[$v['key']], '<=', $end);
                            unset($start,$end,$day1,$day2);
                        }else if($v['rule']=='empty'){
                            $query->where($dataRangeReal[$v['key']], 0);
                        }else if($v['rule']=='not_empty'){
                            $query->where($dataRangeReal[$v['key']], '<>', 0);
                        }
                    }else if($v['key']=='ip'){
                        if($v['rule']=='equal'){
                            $hostIp = HostIpModel::field('host_id,dedicate_ip,assign_ip')->select()->toArray();
                            foreach ($hostIp as $kk=>$vv) {
                                $vv['assign_ip'] = array_filter(explode(',', $vv['assign_ip']));
                                if($vv['dedicate_ip']==$v['value'] || in_array($v['value'], $vv['assign_ip'])){
                                }else{
                                    unset($hostIp[$kk]);
                                }
                            }
                            $searchHostId =  isset($searchHostId) ? array_intersect($searchHostId, array_column(array_values($hostIp), 'host_id')) : array_column(array_values($hostIp), 'host_id');
                            unset($hostIp);
                        }else if($v['rule']=='not_equal'){
                            $hostIp = HostIpModel::field('host_id,dedicate_ip,assign_ip')->select()->toArray();
                            foreach ($hostIp as $kk=>$vv) {
                                $vv['assign_ip'] = array_filter(explode(',', $vv['assign_ip']));
                                if($vv['dedicate_ip']==$v['value'] || in_array($v['value'], $vv['assign_ip'])){
                                    unset($hostIp[$kk]);
                                }
                            }
                            $searchHostId =  isset($searchHostId) ? array_intersect($searchHostId, array_column(array_values($hostIp), 'host_id')) : array_column(array_values($hostIp), 'host_id');
                            unset($hostIp);
                        }else if($v['rule']=='include'){
                            $query->where('hi.dedicate_ip|hi.assign_ip', 'like', "%{$v['value']}%");
                        }else if($v['rule']=='not_include'){
                            $query->where('hi.dedicate_ip|hi.assign_ip', 'not like', "%{$v['value']}%");
                        }else if($v['rule']=='empty'){
                            $query->whereRaw('hi.`ip_num`=0 OR hi.`ip_num` is null');
                        }else if($v['rule']=='not_empty'){
                            $query->where('hi.ip_num', '>', 0);
                        }
                    }else if($v['key']=='certification'){
                        $certificationHookResult = hook_one('get_certification_list');
                        $personCertification = [];
                        $companyCertification = [];
                        foreach ($certificationHookResult as $kk => $vv) {
                            if($vv=='person'){
                                $personCertification[] = $kk;
                            }else{
                                $companyCertification[] = $kk;
                            }
                        }
                        unset($certificationHookResult);
                        if($v['rule']=='equal'){
                            if(in_array('', $v['value']) && in_array('person', $v['value']) && in_array('company', $v['value'])){

                            }else if(in_array('', $v['value']) && in_array('person', $v['value']) && !in_array('company', $v['value'])){
                                $query->whereNotIn('h.client_id', $companyCertification);
                            }else if(in_array('', $v['value']) && !in_array('person', $v['value']) && in_array('company', $v['value'])){
                                $query->whereNotIn('h.client_id', $personCertification);
                            }else if(in_array('', $v['value']) && !in_array('person', $v['value']) && !in_array('company', $v['value'])){
                                $query->whereNotIn('h.client_id', array_merge($personCertification, $companyCertification));
                            }else if(!in_array('', $v['value']) && in_array('person', $v['value']) && in_array('company', $v['value'])){
                                $query->whereIn('h.client_id', array_merge($personCertification, $companyCertification));
                            }else if(!in_array('', $v['value']) && in_array('person', $v['value']) && !in_array('company', $v['value'])){
                                $query->whereIn('h.client_id', $personCertification);
                            }else if(!in_array('', $v['value']) && !in_array('person', $v['value']) && in_array('company', $v['value'])){
                                $query->whereIn('h.client_id', $companyCertification);
                            }else if(!in_array('', $v['value']) && !in_array('person', $v['value']) && !in_array('company', $v['value'])){
                                $searchClientId = [];
                            }
                        }else if($v['rule']=='not_equal'){
                            if(in_array('', $v['value']) && in_array('person', $v['value']) && in_array('company', $v['value'])){
                                $searchClientId = [];
                            }else if(in_array('', $v['value']) && in_array('person', $v['value']) && !in_array('company', $v['value'])){
                                $query->whereIn('h.client_id', $companyCertification);
                            }else if(in_array('', $v['value']) && !in_array('person', $v['value']) && in_array('company', $v['value'])){
                                $query->whereIn('h.client_id', $personCertification);
                            }else if(in_array('', $v['value']) && !in_array('person', $v['value']) && !in_array('company', $v['value'])){
                                $query->whereIn('h.client_id', array_merge($personCertification, $companyCertification));
                            }else if(!in_array('', $v['value']) && in_array('person', $v['value']) && in_array('company', $v['value'])){
                                $query->whereNotIn('h.client_id', array_merge($personCertification, $companyCertification));
                            }else if(!in_array('', $v['value']) && in_array('person', $v['value']) && !in_array('company', $v['value'])){
                                $query->whereNotIn('h.client_id', $personCertification);
                            }else if(!in_array('', $v['value']) && !in_array('person', $v['value']) && in_array('company', $v['value'])){
                                $query->whereNotIn('h.client_id', $companyCertification);
                            }else if(!in_array('', $v['value']) && !in_array('person', $v['value']) && !in_array('company', $v['value'])){

                            }
                        }
                        
                        unset($personCertification,$companyCertification);
                    }else if($v['key']=='client_level'){
                        if($v['rule']=='equal'){
                            $hosts = $this->alias('h')
                                ->field('h.id')
                                ->leftjoin('client c', 'h.client_id=c.id')
                                ->leftjoin('addon_idcsmart_client_level_client_link a', 'a.client_id=c.id')
                                ->whereIn('a.addon_idcsmart_client_level_id', $v['value'])
                                ->select()
                                ->toArray();
                        }else if($v['rule']=='not_equal'){
                            $hosts = $this->alias('h')
                                ->field('h.id')
                                ->leftjoin('client c', 'h.client_id=c.id')
                                ->leftjoin('addon_idcsmart_client_level_client_link a', 'a.client_id=c.id')
                                ->whereNotIn('a.addon_idcsmart_client_level_id', $v['value'])
                                ->select()
                                ->toArray();
                        }else if($v['rule']=='empty'){
                            $hosts = $this->alias('h')
                                ->field('h.id')
                                ->leftjoin('client c', 'h.client_id=c.id')
                                ->leftjoin('addon_idcsmart_client_level_client_link a', 'a.client_id=c.id')
                                ->whereRaw("a.addon_idcsmart_client_level_id=0 OR a.addon_idcsmart_client_level_id is null")
                                ->select()
                                ->toArray();
                        }else if($v['rule']=='not_empty'){
                            $hosts = $this->alias('h')
                                ->field('h.id')
                                ->leftjoin('client c', 'h.client_id=c.id')
                                ->leftjoin('addon_idcsmart_client_level_client_link a', 'a.client_id=c.id')
                                ->where('a.addon_idcsmart_client_level_id', '>', 0)
                                ->select()
                                ->toArray();
                        }
                        $searchHostId =  isset($searchHostId) ? array_intersect($searchHostId, array_column($hosts, 'id')) : array_column($hosts, 'id');
                        unset($hosts); 
                    }else if($v['key']=='sale'){
                        if($v['rule']=='equal'){
                            $hosts = $this->alias('h')
                                ->field('h.id')
                                ->leftjoin('client c', 'h.client_id=c.id')
                                ->leftjoin('addon_idcsmart_sale_client_bind a', 'a.client_id=c.id')
                                ->whereIn('a.sale_id', $v['value'])
                                ->select()
                                ->toArray();
                        }else if($v['rule']=='not_equal'){
                            $hosts = $this->alias('h')
                                ->field('h.id')
                                ->leftjoin('client c', 'h.client_id=c.id')
                                ->leftjoin('addon_idcsmart_sale_client_bind a', 'a.client_id=c.id')
                                ->whereNotIn('a.sale_id', $v['value'])
                                ->select()
                                ->toArray();
                        }else if($v['rule']=='empty'){
                            $hosts = $this->alias('h')
                                ->field('h.id')
                                ->leftjoin('client c', 'h.client_id=c.id')
                                ->leftjoin('addon_idcsmart_sale_client_bind a', 'a.client_id=c.id')
                                ->whereRaw("a.sale_id=0 OR a.sale_id is null")
                                ->select()
                                ->toArray();
                        }else if($v['rule']=='not_empty'){
                            $hosts = $this->alias('h')
                                ->field('h.id')
                                ->leftjoin('client c', 'h.client_id=c.id')
                                ->leftjoin('addon_idcsmart_sale_client_bind a', 'a.client_id=c.id')
                                ->where('a.sale_id', '>', 0)
                                ->select()
                                ->toArray();
                        }
                        $searchHostId =  isset($searchHostId) ? array_intersect($searchHostId, array_column($hosts, 'id')) : array_column($hosts, 'id');
                        unset($hosts); 
                    }else if(strpos($v['key'], 'self_defined_field_')===0){
                        $id = intval(str_replace('self_defined_field_', '', $v['key']));
                        if($v['rule']=='equal'){
                            if(is_array($v['value'])){
                                $hosts = $this->alias('h')
                                    ->field('h.id')
                                    ->leftjoin('self_defined_field_value s', 's.relid=h.id AND s.self_defined_field_id='.$id)
                                    ->whereIn('s.value', $v['value'])
                                    ->select()
                                    ->toArray();
                            }else{
                                if($v['value']===0){
                                    $hosts = $this->alias('h')
                                        ->field('h.id')
                                        ->leftjoin('self_defined_field_value s', 's.relid=h.id AND s.self_defined_field_id='.$id)
                                        ->whereRaw("s.value='{$v['value']}' OR s.value is null")
                                        ->select()
                                        ->toArray();
                                }else{
                                    $hosts = $this->alias('h')
                                        ->field('h.id')
                                        ->leftjoin('self_defined_field_value s', 's.relid=h.id AND s.self_defined_field_id='.$id)
                                        ->where('s.value', $v['value'])
                                        ->select()
                                        ->toArray();
                                }
                                
                            }
                        }else if($v['rule']=='not_equal'){
                            if(is_array($v['value'])){
                                $hosts = $this->alias('h')
                                    ->field('h.id')
                                    ->leftjoin('self_defined_field_value s', 's.relid=h.id AND s.self_defined_field_id='.$id)
                                    ->whereNotIn('s.value', $v['value'])
                                    ->whereRaw("s.value not in ('".implode("','", $v['value'])."') OR s.value is null")
                                    ->select()
                                    ->toArray();
                            }else{
                                $hosts = $this->alias('h')
                                    ->field('h.id')
                                    ->leftjoin('self_defined_field_value s', 's.relid=h.id AND s.self_defined_field_id='.$id)
                                    ->whereRaw("s.value!='{$v['value']}' OR s.value is null")
                                    ->select()
                                    ->toArray();
                            }
                        }else if($v['rule']=='include'){
                            $hosts = $this->alias('h')
                                ->field('h.id')
                                ->leftjoin('self_defined_field_value s', 's.relid=h.id AND s.self_defined_field_id='.$id)
                                ->where('s.value', 'like', "%{$v['value']}%")
                                ->select()
                                ->toArray();
                        }else if($v['rule']=='not_include'){
                            $hosts = $this->alias('h')
                                ->field('h.id')
                                ->leftjoin('self_defined_field_value s', 's.relid=h.id AND s.self_defined_field_id='.$id)
                                ->whereRaw("s.value not like '%{$v['value']}%' OR s.value is null")
                                ->select()
                                ->toArray();
                        }else if($v['rule']=='empty'){
                            $hosts = $this->alias('h')
                                ->field('h.id')
                                ->leftjoin('self_defined_field_value s', 's.relid=h.id AND s.self_defined_field_id='.$id)
                                ->whereRaw("s.value='' OR s.value is null")
                                ->select()
                                ->toArray();
                        }else if($v['rule']=='not_empty'){
                            $hosts = $this->alias('h')
                                ->field('h.id')
                                ->leftjoin('self_defined_field_value s', 's.relid=h.id AND s.self_defined_field_id='.$id)
                                ->whereRaw("s.value!='' AND s.value is not null")
                                ->select()
                                ->toArray();
                        }
                        $searchHostId =  isset($searchHostId) ? array_intersect($searchHostId, array_column(array_values($hosts), 'id')) : array_column(array_values($hosts), 'id');
                        unset($hosts);
                    }else if(strpos($v['key'], 'addon_client_custom_field_')===0){
                        $id = intval(str_replace('addon_client_custom_field_', '', $v['key']));
                        if($v['rule']=='equal'){
                            if(is_array($v['value'])){
                                $hosts = $this->alias('h')
                                    ->field('h.id')
                                    ->leftjoin('client c', 'h.client_id=c.id')
                                    ->leftjoin('addon_client_custom_field_value a', 'a.client_id=c.id AND a.custom_field_id='.$id)
                                    ->whereIn('a.value', $v['value'])
                                    ->select()
                                    ->toArray();
                            }else{
                                if($v['value']===0){
                                    $hosts = $this->alias('h')
                                        ->field('h.id')
                                        ->leftjoin('client c', 'h.client_id=c.id')
                                        ->leftjoin('addon_client_custom_field_value a', 'a.client_id=c.id AND a.custom_field_id='.$id)
                                        ->whereRaw("a.value='{$v['value']}' OR a.value is null")
                                        ->select()
                                        ->toArray();
                                }else{
                                    $hosts = $this->alias('h')
                                        ->field('h.id')
                                        ->leftjoin('client c', 'h.client_id=c.id')
                                        ->leftjoin('addon_client_custom_field_value a', 'a.client_id=c.id AND a.custom_field_id='.$id)
                                        ->where('a.value', $v['value'])
                                        ->select()
                                        ->toArray();
                                }
                            }
                        }else if($v['rule']=='not_equal'){
                            if(is_array($v['value'])){
                                $hosts = $this->alias('h')
                                    ->field('h.id')
                                    ->leftjoin('client c', 'h.client_id=c.id')
                                    ->leftjoin('addon_client_custom_field_value a', 'a.client_id=c.id AND a.custom_field_id='.$id)
                                    ->whereRaw("a.value not in ('".implode("','", $v['value'])."') OR a.value is null")
                                    ->select()
                                    ->toArray();
                            }else{
                                $hosts = $this->alias('h')
                                    ->field('h.id')
                                    ->leftjoin('client c', 'h.client_id=c.id')
                                    ->leftjoin('addon_client_custom_field_value a', 'a.client_id=c.id AND a.custom_field_id='.$id)
                                    ->whereRaw("a.value!='{$v['value']}' OR a.value is null")
                                    ->select()
                                    ->toArray();
                            }
                        }else if($v['rule']=='include'){
                            $hosts = $this->alias('h')
                                ->field('h.id')
                                ->leftjoin('client c', 'h.client_id=c.id')
                                ->leftjoin('addon_client_custom_field_value a', 'a.client_id=c.id AND a.custom_field_id='.$id)
                                ->where('a.value', 'like', "%{$v['value']}%")
                                ->select()
                                ->toArray();
                        }else if($v['rule']=='not_include'){
                            $hosts = $this->alias('h')
                                ->field('h.id')
                                ->leftjoin('client c', 'h.client_id=c.id')
                                ->leftjoin('addon_client_custom_field_value a', 'a.client_id=c.id AND a.custom_field_id='.$id)
                                ->whereRaw("a.value not like '%{$v['value']}%' OR a.value is null")
                                ->select()
                                ->toArray();
                        }else if($v['rule']=='empty'){
                            $hosts = $this->alias('h')
                                ->field('h.id')
                                ->leftjoin('client c', 'h.client_id=c.id')
                                ->leftjoin('addon_client_custom_field_value a', 'a.client_id=c.id AND a.custom_field_id='.$id)
                                ->whereRaw("a.value='' OR a.value is null")
                                ->select()
                                ->toArray();
                        }else if($v['rule']=='not_empty'){
                            $hosts = $this->alias('h')
                                ->field('h.id')
                                ->leftjoin('client c', 'h.client_id=c.id')
                                ->leftjoin('addon_client_custom_field_value a', 'a.client_id=c.id AND a.custom_field_id='.$id)
                                ->whereRaw("a.value!='' AND a.value is not null")
                                ->select()
                                ->toArray();
                        }
                        $searchHostId =  isset($searchHostId) ? array_intersect($searchHostId, array_column(array_values($hosts), 'id')) : array_column(array_values($hosts), 'id');
                        unset($hosts);
                    }
                }
                unset($selectDataRange);
                unset($dataRangeReal);
                if(isset($searchHostId)){
                    $query->whereIn('h.id', $searchHostId);
                }
                unset($searchHostId);
            }
            hook('host_list_where_query_append', ['param'=>$param, 'app'=>$app, 'query'=>$query]);
        };

        $count = $this->alias('h')
            ->field('h.id')
            ->leftjoin('product p', 'p.id=h.product_id')
            ->leftjoin('client c', 'c.id=h.client_id')
            ->leftjoin('order o', 'o.id=h.order_id')
            ->leftJoin('server s','s.id=h.server_id')
            ->leftjoin('host_ip hi', 'h.id=hi.host_id')
            ->where($where)
            ->group('h.id')
            ->count();

        if($app!='home'){
            $totalRenewAmount = $this->alias('h')
                ->field('h.renew_amount')
                ->leftjoin('product p', 'p.id=h.product_id')
                ->leftjoin('client c', 'c.id=h.client_id')
                ->leftjoin('order o', 'o.id=h.order_id')
                ->leftJoin('server s','s.id=h.server_id')
                ->leftjoin('host_ip hi', 'h.id=hi.host_id')
                ->where($where)
                ->group('h.id')
                ->select()
                ->toArray();
            $totalRenewAmount = amount_format(array_sum(array_column($totalRenewAmount, 'renew_amount')));
        }

        if($app == 'home'){
            $language = get_client_lang();
        }else{
            $language = get_system_lang(true);
        }
        $countryField = ['en-us'=> 'nicename'];
        $countryName = $countryField[ $language ] ?? 'name_zh';

        $hosts = $this->alias('h')
            ->field('h.id,h.client_id,c.username client_name,c.email,c.phone_code,c.phone,c.company,h.product_id,p.name product_name,h.name,h.create_time,h.active_time,h.due_time,h.first_payment_amount,h.billing_cycle,h.billing_cycle_name,h.status,o.pay_time,h.renew_amount,h.client_notes,hi.dedicate_ip,hi.ip_num,hi.assign_ip,s.name server_name,s.module,h.notes admin_notes,h.base_price,c.status client_status,c.create_time reg_time,co.'.$countryName.' country,c.address,c.language,c.notes,h.base_info,p.show_base_info')
            ->leftjoin('product p', 'p.id=h.product_id')
            ->leftjoin('client c', 'c.id=h.client_id')
            ->leftjoin('order o', 'o.id=h.order_id')
            ->leftJoin('server s','s.id=h.server_id')
            ->leftjoin('host_ip hi', 'h.id=hi.host_id')
            ->leftJoin('country co', 'co.id=c.country_id')
            ->withAttr('product_name', function($val) use ($app) {
                if($app == 'home'){
                    $multiLanguage = hook_one('multi_language', [
                        'replace' => [
                            'product_name' => $val,
                        ],
                    ]);
                    if(isset($multiLanguage['product_name'])){
                        $val = $multiLanguage['product_name'];
                    }
                }
                return $val;
            })
            ->withAttr('base_info', function($val,$data){
                if (!empty($data['show_base_info'])){
                    return $val;
                }else{
                    return "";
                }
            })
            ->withAttr('ip_num', function($val){
                return $val ?? 0;
            })
            ->withAttr('dedicate_ip', function($val){
                return $val ?? '';
            })
            ->withAttr('assign_ip', function($val){
                return $val ?? '';
            })
            ->where($where)
            ->limit($param['limit'])
            ->page($param['page'])
            ->order($orderReal[$param['orderby']], $param['sort'])
            ->group('h.id')
            ->select()
            ->toArray();

        $clientId = array_column($hosts, 'client_id');
        $hostId = array_column($hosts, 'id');

        // 用户实名状态
        if(isset($selectField['certification'])){
            $certificationHookResult = hook_one('get_certification_list');
        }
        // 获取用户等级
        if(isset($selectField['client_level'])){
            $clientLevel = hook_one('get_client_level_list', ['client_id'=>$clientId]);
        }
        // 获取销售
        if(isset($selectField['sale'])){
            $sale = hook_one('get_sale_list', ['client_id'=>$clientId]);
        }
        // 获取用户自定义字段
        $clientCustomFieldIdArr = [];
        foreach($selectField as $k=>$v){
            if(stripos($k, 'addon_client_custom_field_') === 0){
                $clientCustomFieldId = (int)str_replace('addon_client_custom_field_', '', $k);
                $clientCustomFieldIdArr[ $clientCustomFieldId ] = 1;
            }
        }
        if(!empty($clientCustomFieldIdArr)){
            $clientCustomField = hook_one('get_client_custom_field_list', ['client_id'=>$clientId]);
        }

        // 开发者
        $developer = hook_one('get_developer_list', ['client_id'=>$clientId]);

        $selfDefinedFieldIdArr = [];
        foreach($selectField as $k=>$v){
            if(stripos($k, 'self_defined_field_') === 0){
                $selfDefinedFieldId = (int)str_replace('self_defined_field_', '', $k);
                $selfDefinedFieldIdArr[ $selfDefinedFieldId ] = 1;
            }
        }
        if(!empty($selfDefinedFieldIdArr) && !empty($hostId)){
            $selfDefinedFieArr = SelfDefinedFieldValueModel::alias('sdfv')
                ->field('sdf.id,sdf.field_type,sdfv.relid,sdfv.value')
                ->join('self_defined_field sdf', 'sdfv.self_defined_field_id=sdf.id')
                ->where('sdf.type', 'product')
                ->whereIn('sdfv.relid', $hostId)
                ->select()
                ->toArray();
            $tickbox = [
                lang('self_defined_field_tickbox_no_check'),
                lang('self_defined_field_tickbox_check'),
            ];
            foreach($selfDefinedFieArr as $v){
                if($v['field_type'] == 'tickbox'){
                    $v['value'] = $tickbox[ $v['value'] ] ?? $tickbox[0];
                }
                $selfDefinedField[$v['relid']][$v['id']] = $v['value'];
            }
        }

        $renewalFirstDay = configuration('cron_due_renewal_first_day');

        $pageTotalRenewAmount = amount_format(array_sum(array_column($hosts, 'renew_amount')));

        foreach ($hosts as $key => $host) {
            // TODO
            // $hosts[$key]['name'] = !empty($host['dedicate_ip'])?$host['dedicate_ip']:$host['name'];
            $hosts[$key]['first_payment_amount'] = amount_format($host['first_payment_amount']); // 处理金额格式

            if($app=='home'){
                $hosts[$key]['billing_cycle'] = $host['billing_cycle']!='onetime' ? $host['billing_cycle_name'] : '';
            }

            // 前台接口去除字段
            if($app=='home'){
                $hosts[$key]['renewal_first_day_time'] = $renewalFirstDay*3600*24;
                $hosts[$key]['status'] = $host['status']=='Failed' ? 'Pending' : $host['status'];
                // wyh 20240718 自用软件
                if ($self){
                    if ($host['module']=='idcsmart_common_business') {
                        $IdcsmartCommonSonHost = new \server\idcsmart_common_business\model\IdcsmartCommonSonHost();
                    } elseif ($host['module']=='idcsmart_common_finance'){
                        $IdcsmartCommonSonHost = new \server\idcsmart_common_finance\model\IdcsmartCommonSonHost();
                    } elseif ($host['module']=='idcsmart_common_dcim'){
                        $IdcsmartCommonSonHost = new \server\idcsmart_common_dcim\model\IdcsmartCommonSonHost();
                    } elseif ($host['module']=='idcsmart_common_cloud'){
                        $IdcsmartCommonSonHost = new \server\idcsmart_common_cloud\model\IdcsmartCommonSonHost();
                    }
                    if (!empty($IdcsmartCommonSonHost)){
                        $hosts[$key]['parent_host_id'] = $IdcsmartCommonSonHost->where('son_host_id',$host['id'])->value('host_id')??0;
                    }
                }
                unset($hosts[$key]['client_id'], $hosts[$key]['client_name'], $hosts[$key]['email'], $hosts[$key]['phone_code'], $hosts[$key]['phone'], $hosts[$key]['company'], $hosts[$key]['server_name'], $hosts[$key]['admin_notes'], $hosts[$key]['base_price'], $hosts[$key]['client_status'], $hosts[$key]['reg_time'], $hosts[$key]['country'], $hosts[$key]['address'], $hosts[$key]['language'], $hosts[$key]['notes'],$hosts[$key]['billing_cycle_name']);
            }

            unset($hosts[$key]['create_time'], $hosts[$key]['pay_time']);

            if(isset($selectField['certification'])){
                // 实名认证字段
                $hosts[$key]['certification'] = isset($certificationHookResult[$host['client_id']]) && $certificationHookResult[$host['client_id']];
                $hosts[$key]['certification_type'] = $certificationHookResult[$host['client_id']]??'person';
            }
            // 用户等级字段
            if(isset($selectField['client_level'])){
                $hosts[$key]['client_level'] = $clientLevel[ $host['client_id'] ]['name'] ?? '';
                $hosts[$key]['client_level_color'] = $clientLevel[ $host['client_id'] ]['background_color'] ?? '';
            }
            // 销售字段
            if(isset($selectField['sale'])){
                $hosts[$key]['sale'] = $sale[ $host['client_id'] ]['name'] ?? '';
            }
            // 开发者
            $hosts[$key]['developer_type'] = $developer[$host['client_id']]['type']??0;
            // 用户自定义字段
            if(!empty($clientCustomFieldIdArr)){
                foreach($clientCustomFieldIdArr as $kk=>$vv){
                    $hosts[$key]['addon_client_custom_field_'.$kk] = $clientCustomField[$host['client_id']][$kk] ?? '';
                }
            }
            if(!empty($selfDefinedFieldIdArr)){
                foreach($selfDefinedFieldIdArr as $kk=>$vv){
                    $hosts[$key]['self_defined_field_'.$kk] = $selfDefinedField[$host['id']][$kk] ?? '';
                }
            }

        }

        if($app=='home'){
            return ['list' => $hosts, 'count' => $count];
        }else{
            $usingCount = $this->usingCount();
            $expiringCount = $this->expiringCount();
            $overdueCount = $this->overdueCount();
            $deletedCount = $this->deletedCount();
            $allCount = $this->allCount();
            $failedActionCount = $this->failedActionCount();

            return ['list'=>$hosts, 'count'=>$count, 'using_count'=>$usingCount, 'expiring_count'=>$expiringCount, 'overdue_count'=>$overdueCount, 'deleted_count'=>$deletedCount, 'all_count'=>$allCount, 'failed_action_count'=>$failedActionCount, 'total_renew_amount' => $totalRenewAmount, 'page_total_renew_amount' => $pageTotalRenewAmount,];
        }    
    }

    /**
     * 时间 2022-10-13
     * @title 会员中心首页产品列表
     * @desc 会员中心首页产品列表
     * @author theworld
     * @version v1
     * @param int param.page - 页数
     * @return array list - 产品
     * @return int list[].id - 产品ID 
     * @return int list[].product_id - 商品ID 
     * @return string list[].product_name - 商品名称 
     * @return string list[].name - 标识 
     * @return int list[].due_time - 到期时间
     * @return string list[].status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败
     * @return string list[].client_notes - 用户备注
     * @return string list[].type - 类型 
     * @return int count - 产品总数
     */
    public function indexHostList($param)
    {
        $param['client_id'] = get_client_id();
        if(empty($param['client_id'])){
            return ['list' => [], 'count' => 0];
        }
        $res = hook('get_client_host_id', ['client_id' => get_client_id(false)]);
        $res = array_values(array_filter($res ?? []));
        foreach ($res as $key => $value) {
            if(isset($value['status']) && $value['status']==200){
                $hostId = $value['data']['host'];
                if(empty($hostId)){
                    return ['list' => [], 'count' => 0];
                }
            }
        }
        $param['host_id'] = $hostId ?? [];

        $where = function (Query $query) use($param) {
            $query->whereIn('h.status', ['Active']);
            if(!empty($param['client_id'])){
                $query->where('h.client_id', (int)$param['client_id']);
            }
            if(!empty($param['host_id'])){
                $query->whereIn('h.id', $param['host_id']);
            }
            $query->where('h.is_delete', 0);
        };

        $count = $this->alias('h')
            ->field('h.id')
            ->leftjoin('product p', 'p.id=h.product_id')
            ->leftjoin('client c', 'c.id=h.client_id')
            ->where($where)
            ->group('h.id')
            ->count();
        $hosts = $this->alias('h')
            ->field('h.id,h.product_id,p.name product_name,h.name,h.due_time,h.status,h.client_notes,s.module,ss.module module1')
            ->leftjoin('product p', 'p.id=h.product_id')
            ->leftjoin('server s','p.type=\'server\' AND p.rel_id=s.id')
            ->leftjoin('server_group sg','p.type=\'server_group\' AND p.rel_id=sg.id')
            ->leftjoin('server ss','ss.server_group_id=sg.id')
            ->where($where)
            ->withAttr('product_name', function($val){
                $multiLanguage = hook_one('multi_language', [
                    'replace' => [
                        'product_name' => $val,
                    ],
                ]);
                if(isset($multiLanguage['product_name'])){
                    $val = $multiLanguage['product_name'];
                }
                return $val;
            })
            ->limit(10)
            ->page($param['page'])
            ->orderRaw('h.due_time>0 desc')
            ->order('h.due_time', 'asc')
            ->group('h.id')
            ->select()
            ->toArray();

        $ModuleLogic = new ModuleLogic();

        $moduleList = $ModuleLogic->getModuleList();
        $moduleList = array_column($moduleList, 'display_name', 'name');

        foreach ($hosts as $key => $host) {
            $hosts[$key]['status'] = $host['status']=='Failed' ? 'Pending' : $host['status'];
            $host['module'] = !empty($host['module']) ? $host['module'] : $host['module1'];
            $hosts[$key]['type'] = $moduleList[$host['module']] ?? $host['module'];
            unset($hosts[$key]['module'], $hosts[$key]['module1']);
        }

        return ['list' => $hosts, 'count' => $count];
    }

    /**
     * 时间 2022-05-13
     * @title 产品详情
     * @desc 产品详情
     * @author theworld
     * @version v1
     * @param int id - 产品ID required
     * @return int id - 产品ID 
     * @return int order_id - 订单ID 
     * @return int product_id - 商品ID 
     * @return int server_id - 接口ID 
     * @return string name - 标识 
     * @return string notes - 备注 
     * @return string first_payment_amount - 订购金额
     * @return string renew_amount - 续费金额
     * @return string billing_cycle - 计费周期
     * @return string billing_cycle_name - 模块计费周期名称
     * @return string billing_cycle_time - 模块计费周期时间,秒
     * @return int active_time - 开通时间 
     * @return int due_time - 到期时间
     * @return string status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败
     * @return string suspend_type - 暂停类型,overdue到期暂停,overtraffic超流暂停,certification_not_complete实名未完成,other其他
     * @return string suspend_reason - 暂停原因
     * @return string client_notes - 用户备注
     * @return int ratio_renew - 是否开启比例续费:0否,1是
     * @return string base_price - 购买周期原价
     * @return string product_name - 商品名称
     * @return int agent - 代理产品0否1是
     * @return string upstream_host_id - 上游产品ID
     * @return string mode - 商品代理模式：only_api仅调用接口，sync同步商品
     * @return string base_info - 产品基础信息
     * @return int addition.country_id - 国家ID
     * @return string addition.city - 城市
     * @return string addition.area - 区域
     * @return string addition.image_icon - 镜像图标(Windows,CentOS,Ubuntu,Debian,ESXi,XenServer,FreeBSD,Fedora,ArchLinux,Rocky,AlmaLinux,OpenEuler,RedHat,其他)
     * @return string addition.image_name - 镜像名称
     * @return string addition.username - 实例用户名
     * @return string addition.password - 实例密码
     * @return int addition.port - 端口
     */
    public function indexHost($id)
    {
        // 获取当前应用
        $app = app('http')->getName();

        $host = $this->field('id,order_id,product_id,server_id,name,notes,first_payment_amount,renew_amount,billing_cycle,billing_cycle_name,billing_cycle_time,active_time,due_time,status,client_id,suspend_type,suspend_reason,client_notes,ratio_renew,base_price,base_info')->where('is_delete', 0)->find($id);
        if (empty($host)){
            return (object)[]; // 转换为对象
        }

        // 插件用户限制,限制可查看的用户数据
        $res = hook('plugin_check_client_limit', ['client_id' => $host['client_id']]);
        foreach ($res as $value){
            if (isset($value['status']) && $value['status']==400){
                return (object)[]; // 转换为对象
            }
        }

        $product = ProductModel::find($host['product_id']);
        $upstreamHost = UpstreamHostModel::where('host_id', $host['id'])->find();

        // 产品的用户ID和前台用户不一致时返回空对象
        if($app=='home'){
            $client_id = get_client_id();
            if($host['client_id']!=$client_id || $host['status']=='Cancelled'){
                return (object)[]; // 转换为对象
            }
            $host['notes'] = $host['client_notes'];
            unset($host['server_id'], $host['client_notes']);

            $host['status'] = $host['status'] != 'Failed' ? $host['status'] : 'Pending';

            // 多语言
            $multiLanguage = hook_one('multi_language', [
                'replace' => [
                    'billing_cycle_name' => $host['billing_cycle_name'],
                ],
            ]);
            if(isset($multiLanguage['billing_cycle_name'])){
                $host['billing_cycle_name'] = $multiLanguage['billing_cycle_name'];
            }
        }

        $host['first_payment_amount'] = amount_format($host['first_payment_amount']); 
        $host['renew_amount'] = amount_format($host['renew_amount']);
        $host['product_name'] = $product['name'] ?? '';
        $host['upstream_host_id'] = $upstreamHost['upstream_host_id']??0;
        $host['agent'] = !empty($upstreamHost) ? 1 : 0;
        $UpstreamProductModel = new UpstreamProductModel();
        $upstreamProduct = $UpstreamProductModel->where('product_id',$host['product_id'])->find();
        if (!empty($upstreamProduct)){
            $host['mode'] = $upstreamProduct['mode'];
        }
        unset($host['client_id']);

        // 获取产品附加表内容
        $addition = HostAdditionModel::field('country_id,city,area,image_icon,image_name,username,password,port')->where('host_id', $id)->find();
        if(!empty($addition)){
            $host['addition'] = $addition->toArray();
        }else{
            $host['addition'] = [];
        }

        //$host['other_params'] = (new ModuleLogic())->hostOtherParams($host);

        return $host;
    }

    /**
     * 时间 2022-07-22
     * @title 搜索产品
     * @desc 搜索产品
     * @author theworld
     * @version v1
     * @param string keywords - 关键字,搜索范围:产品ID,标识,商品名称
     * @return array list - 产品
     * @return int list[].id - 产品ID 
     * @return string list[].name - 标识
     * @return string list[].product_name - 商品名称
     * @return int list[].client_id - 用户ID
     */
    public function searchHost($keywords)
    {   
        // 获取当前应用
        $app = app('http')->getName();

        $resultHook = hook('before_search_host', ['keywords' => $keywords]);
        $resultHook = array_values(array_filter($resultHook ?? []));
        $hostIdArr = [];
        foreach ($resultHook as $key => $value) {
            if(isset($value['host_id']) && !empty($value['host_id']) && is_array($value['host_id'])){
                $hostIdArr = array_merge($hostIdArr, $value['host_id']);
            }
        }
        $hostIdArr = array_unique($hostIdArr);
        
        //全局搜索
        $hosts = $this->alias('h')
            ->field('h.id,h.name,p.name product_name,h.client_id')
            ->leftjoin('product p', 'p.id=h.product_id')
            ->where(function ($query) use($keywords, $app) {
                if($app=='home'){
                    $clientId = get_client_id();
                    $query->where('h.client_id', $clientId)->where('h.status', '<>', 'Cancelled');
                }
                if(!empty($keywords)){
                    $query->where('h.id|h.name|p.name', 'like', "%{$keywords}%");
                }
                $query->where('h.is_delete', 0);
            })
            ->select()
            ->toArray();
        if(!empty($hostIdArr)){
            $hostIdArr = array_merge($hostIdArr, array_column($hosts, 'id'));
            $hostIdArr = array_unique($hostIdArr);
            $hosts = $this->alias('h')
                ->field('h.id,h.name,p.name product_name,h.client_id')
                ->leftjoin('product p', 'p.id=h.product_id')
                ->whereIn('h.id', $hostIdArr)
                ->where('h.is_delete', 0)
                ->select()
                ->toArray();
        }

        if($app=='home'){
            foreach ($hosts as $key => $value) {
                unset($hosts[$key]['client_id']);

                $multiLanguage = hook_one('multi_language', [
                    'replace' => [
                        'product_name' => $value['product_name'],
                    ],
                ]);
                if(isset($multiLanguage['product_name'])){
                    $hosts[$key]['product_name'] = $multiLanguage['product_name'];
                }
            }
        }
        return ['list' => $hosts];
    }

    /**
     * 时间 2022-05-13
     * @title 修改产品
     * @desc 修改产品
     * @author theworld
     * @version v1
     * @param int param.id - 产品ID required
     * @param int param.product_id - 商品ID required
     * @param int param.server_id - 接口
     * @param string param.name - 标识
     * @param string param.notes - 备注
     * @param string param.upstream_host_id - 上游产品ID
     * @param float param.first_payment_amount - 订购金额 required
     * @param float param.renew_amount - 续费金额 required
     * @param string param.billing_cycle - 计费周期 required
     * @param string param.active_time - 开通时间
     * @param string param.due_time - 到期时间
     * @param string param.status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败
     * @param object param.self_defined_field - 自定义字段({"5":"123"},5是自定义字段ID,123是填写的内容)
     * @param int param.host.ratio_renew - 是否开启比例续费:0否,1是
     * @param float param.host.base_price - 购买周期原价
     * @param object param.customfield - 自定义字段
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function updateHost($param)
    {
        // 验证产品ID
        $host = $this->find($param['id']);
        if (empty($host) || $host['is_delete']){
            return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
        }

        // 验证商品ID
        $product = ProductModel::find($param['product_id']);
        if (empty($product)){
            return ['status'=>400, 'msg'=>lang('product_is_not_exist')];
        }
        $SelfDefinedFieldModel = new SelfDefinedFieldModel();
        if($host['product_id'] == $param['product_id'] && isset($param['self_defined_field'])){
            $selfDefinedFieldFormat = $SelfDefinedFieldModel->adminHostUpdateFormat([
                'host_id'           => $host->id,
                'self_defined_field'=> $param['self_defined_field'],
            ]);
            if($selfDefinedFieldFormat['status'] != 200){
                return $selfDefinedFieldFormat;
            }
        }

        $param['server_id'] = $param['server_id'] ?? 0;
        $param['name'] = $param['name'] ?? '';
        $param['notes'] = $param['notes'] ?? '';
        $param['first_payment_amount'] = $param['first_payment_amount'] ?? 0;
        $param['renew_amount'] = $param['renew_amount'] ?? 0;
        $param['active_time'] = isset($param['active_time']) ? strtotime($param['active_time']) : 0;
        $param['due_time'] = isset($param['due_time']) ? strtotime($param['due_time']) : 0;
        // 计费周期为一次性和免费的产品没有到期时间和续费金额,其他的使用传入的到期时间和续费金额
        if($param['billing_cycle']=='onetime'){
            $param['due_time'] = 0;
            $param['renew_amount'] = 0;
        }else if($param['billing_cycle']=='free'){
            $param['renew_amount'] = 0;
        }

        # 日志详情
        $description = [];
        if ($host['product_id'] != $param['product_id']){
            $oldProduct = ProductModel::find($host['product_id']);
            $oldProduct = $oldProduct['name'] ?? '';
            $newProduct = ProductModel::find($param['product_id']);
            $newProduct = $newProduct['name'] ?? '';

            $description[] = lang('old_to_new',['{old}'=>lang('host_product').$oldProduct, '{new}'=>$newProduct]);
        }
        if ($host['server_id'] != $param['server_id']){
            $oldServer = ServerModel::find($host['server_id']);
            $oldServer = $oldServer['name'] ?? '';
            $newServer = ServerModel::find($param['server_id']);
            $newServer = $newServer['name'] ?? '';

            $description[] = lang('old_to_new',['{old}'=>lang('host_server').$oldServer, '{new}'=>$newServer]);
        }
        if ($host['name'] != $param['name']){
            $description[] = lang('old_to_new',['{old}'=>lang('host_name').$host['name'], '{new}'=>$param['name']]);
        }
        if ($host['notes'] != $param['notes']){
            $description[] = lang('old_to_new',['{old}'=>lang('host_notes').$host['notes'], '{new}'=>$param['notes']]);
        }
        if ($host['first_payment_amount'] != $param['first_payment_amount']){
            $description[] = lang('old_to_new',['{old}'=>lang('host_first_payment_amount').$host['first_payment_amount'], '{new}'=>$param['first_payment_amount']]);
        }
        if ($host['renew_amount'] != $param['renew_amount']){
            $description[] = lang('old_to_new',['{old}'=>lang('host_renew_amount').$host['renew_amount'], '{new}'=>$param['renew_amount']]);
        }
        if ($host['billing_cycle'] != $param['billing_cycle']){
            $description[] = lang('old_to_new',['{old}'=>lang('host_billing_cycle').lang('host_billing_cycle_'.$host['billing_cycle']), '{new}'=>lang('host_billing_cycle_'.$param['billing_cycle'])]);
        }
        if ($host['active_time'] != $param['active_time']){
            $description[] = lang('old_to_new',['{old}'=>lang('host_active_time').date("Y-m-d H:i:s", $host['active_time']), '{new}'=>date('Y-m-d H:i:s',$param['active_time'])]);
        }
        if ($host['due_time'] != $param['due_time']){
            $description[] = lang('old_to_new',['{old}'=>lang('host_due_time').date("Y-m-d H:i:s", $host['due_time']), '{new}'=>date("Y-m-d H:i:s", $param['due_time'])]);
        }
        if ($host['status'] != $param['status']){
            $description[] = lang('old_to_new',['{old}'=>lang('host_status').lang('host_status_'.$host['status']), '{new}'=>lang('host_status_'.$param['status'])]);
        }
        if(isset($selfDefinedFieldFormat)){
            $description = array_merge($description, array_column($selfDefinedFieldFormat['data'], 'log'));
        }
        $description = implode(',', $description);

        $this->startTrans();
        try {
            
            $this->update([
                'product_id' => $param['product_id'],
                'server_id' => $param['server_id'],
                'name' => $param['name'],
                'notes' => $param['notes'],
                'first_payment_amount' => $param['first_payment_amount'],
                'renew_amount' => $param['renew_amount'],
                'billing_cycle' => $param['billing_cycle'],
                'active_time' => $param['active_time'],
                'due_time' => $param['due_time'],
                'status' => $param['status'],
                'update_time' => time(),
                'ratio_renew' => $param['ratio_renew']??0,
                'base_price' => $param['base_price']??0,
            ], ['id' => $param['id']]);

            if (isset($param['upstream_host_id']) && $param['upstream_host_id']){

                $UpstreamHostModel = new UpstreamHostModel();

                $upstreamHost = $UpstreamHostModel->where('host_id',$host['id'])->find();

                $UpstreamProductModel = new UpstreamProductModel();
                $upstreamProduct = $UpstreamProductModel->where('product_id',$host['product_id'])->find();

                if (isset($upstreamProduct['res_module']) && in_array($upstreamProduct['res_module'],['mf_finance','mf_finance_dcim'])){
                    $upstreamHostUpstream = $UpstreamHostModel->where('host_id',$upstreamHost['upstream_host_id'])->find();
                    $SupplierModel = new SupplierModel();
                    $supplier = $SupplierModel->find($upstreamProduct['supplier_id']);
                    if (!empty($supplier) && $supplier['type']=='default'){
                        $res = idcsmart_api_curl($upstreamHost['supplier_id'], 'console/v1/host/'.$param['upstream_host_id'], [], 30, 'GET');
                        if (!isset($res['data']['host']) || empty($res['data']['host'])){
                            return ['status'=>400, 'msg'=>lang('upstream_host_is_not_exist')];
                        }
                    }else{
                        $res = idcsmart_api_curl($upstreamHost['supplier_id'], 'host/header', ['host_id'=>$param['upstream_host_id']], 30, 'GET');
                        if (!isset($res['data']['host_data'])){
                            return ['status'=>400, 'msg'=>lang('upstream_host_is_not_exist')];
                        }
                    }
                }else if (isset($upstreamProduct['res_module']) && in_array($upstreamProduct['res_module'], ['whmcs_cloud', 'whmcs_dcim'])){
                    $result = idcsmart_api_curl($upstreamHost['supplier_id'], 'host_detail', ['hosting_id' => $param['upstream_host_id']], 30, 'POST');
                    if (!isset($result['data'])){
                        return ['status'=>400, 'msg'=>lang('upstream_host_is_not_exist')];
                    }
                }else{
                    $res = idcsmart_api_curl($upstreamHost['supplier_id'], 'console/v1/host/'.$param['upstream_host_id'], [], 30, 'GET');
                    if (!isset($res['data']['host']) || empty($res['data']['host'])){
                        return ['status'=>400, 'msg'=>lang('upstream_host_is_not_exist')];
                    }
                }

                $UpstreamHostModel->update([
                    'upstream_host_id' => $param['upstream_host_id']
                ],['host_id'=>$host['id']]);


            }

            if($host['product_id'] != $param['product_id']){
                $SelfDefinedFieldValueModel = new SelfDefinedFieldValueModel();
                $SelfDefinedFieldValueModel->withDelete([
                    'type'  => 'product',
                    'relid' => $host['id'],
                ]);
            }else{
                if(isset($selfDefinedFieldFormat)){
                    $SelfDefinedFieldModel->adminHostUpdateSave($selfDefinedFieldFormat);
                }
            }

            if(!empty($description)) active_log(lang('admin_modify_host', ['{admin}'=>request()->admin_name, '{host}'=>'host#'.$host->id.'#'.$param['name'].'#', '{description}'=>$description]), 'host', $host->id);

            $this->commit();
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return ['status' => 400, 'msg' => lang('update_fail').$e->getMessage()];
        }

        $upstreamProduct = UpstreamProductModel::where('product_id', $param['product_id'])->find();
        if($upstreamProduct){
            // $ResModuleLogic = new ResModuleLogic($upstreamProduct);
            // $result = $ResModuleLogic->adminField($host);
        }else{
            $ModuleLogic = new ModuleLogic();
            $result = $ModuleLogic->hostUpdate($this->find($host->id), $param['customfield']['module_admin_field'] ?? []);
            if(isset($result['status']) && $result['status'] == 400){
                return $result;
            }
        }
        upstream_sync_host($param['id'], 'update_host');

        hook('after_host_edit',['id'=>$param['id'],'customfield'=>$param['customfield']??[]]);

        return ['status' => 200, 'msg' => lang('update_success')];
    }

    /**
     * 时间 2022-05-13
     * @title 删除产品
     * @desc 删除产品
     * @author theworld
     * @version v1
     * @param int param.id - 产品ID required
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function deleteHost($param)
    {
        $id = $param['id']??0;
        // 验证产品ID
        $host = $this->find($id);
        if (empty($host) || $host['is_delete']){
            return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
        }
        if($host['status']=='Pending'){
            return ['status'=>400, 'msg'=>lang('host_opening_cannot_delete')];
        }
        $module = $host->getModule();

        $this->startTrans();
        try {
            $client = ClientModel::find($host->client_id);
            if(empty($client)){
                $clientName = '#'.$host->client_id;
            }else{
                $clientName = 'client#'.$client->id.'#'.$client->username.'#';
            }
            # 记录日志
            active_log(lang('admin_delete_user_host', ['{admin}'=>request()->admin_name, '{client}'=>$clientName, '{host}'=>$host['name']]), 'host', $host->id);

            $order = OrderModel::find($host['order_id']);
            if(!empty($order) && in_array($order['status'], ['Unpaid','WaitUpload','WaitReview','ReviewFail'])){
                OrderItemModel::where('host_id', $host['id'])->delete();
                $count = OrderItemModel::where('order_id', $order['id'])->count();
                if($count==0){
                    OrderModel::destroy($host['order_id']);
                }else{
                    $amount = OrderItemModel::where('order_id', $order['id'])->sum('amount');
                    OrderModel::update(['amount'=>$amount],['id'=>$host['order_id']]);
                }
            }
            UpstreamHostModel::where('host_id', $id)->delete();
            HostIpModel::where('host_id', $id)->delete();
            HostAdditionModel::where('host_id', $id)->delete();

            upstream_sync_host($id, 'delete_host');

            // 产品删除后，增加商品库存
//            $product = ProductModel::find($host['product_id']);
//            if ($product['stock_control']==1 && $host['status']!='Deleted'){
//
//                $product->save([
//                    'qty' => $product['qty']+1,
//                    'update_time' => time(),
//                ]);
//                $description = lang('log_delete_host_stock', [
//                    '{host}'    => 'host#'.$host->id.'#'.$host['name'].'#',
//                    '{product}'  => 'product#'.$product['id'].'#'.$product['name'].'#',
//                    '{client}'  => 'client#'.$client->id.'#'.$client['username'].'#',
//                    '{qty}' => $product['qty']+1,
//                ]);
//                active_log($description, 'host', $host->id);
//            }

            $this->destroy($id);
            $this->commit();
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return ['status' => 400, 'msg' => lang('delete_fail')];
        }

        hook('after_host_delete',['id'=>$id,'product_id'=>$host['product_id'],'module'=>$module]);

        return ['status' => 200, 'msg' => lang('delete_success')];
    }

    /**
     * 时间 2023-01-30
     * @title 批量删除产品
     * @desc 批量删除产品
     * @author theworld
     * @version v1
     * @param array param.id - 产品ID required
     * @param int module_delete - 是否执行模块删除，1是0否 required
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function batchDeleteHost($param)
    {
        $id = $param['id']??[];
        // 验证产品ID
        $host = $this->whereIn('id', $id)->where('is_delete', 0)->select()->toArray();
        if (empty($host)){
            return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
        }
        if(count($host)!=count($id)){
            return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
        }

        $client = ClientModel::whereIn('id', array_column($host, 'client_id'))->select()->toArray();
        $clientArr = [];
        foreach ($client as $key => $value) {
            $clientArr[$value['id']] = $value;
        }
        
        $this->startTrans();
        try {
            foreach ($host as $key => $value) {
                $module = $this->getModule([
                    'server_id' => $value['server_id'],
                    'product_id'=> $value['product_id'],
                ]);

                $client = $clientArr[$value['client_id']] ?? [];
                if(empty($client)){
                    $clientName = '#'.$value['client_id'];
                }else{
                    $clientName = 'client#'.$client['id'].'#'.$client['username'].'#';
                }
                # 记录日志
                active_log(lang('admin_batch_delete_user_host', ['{admin}'=>request()->admin_name, '{client}'=>$clientName, '{host}'=>$value['name']]), 'host', $value['id']);

                upstream_sync_host($value['id'], 'delete_host');

                if (isset($param['module_delete']) && $param['module_delete']==1){
                    $this->terminateAccount($value['id']);
                }

                UpstreamHostModel::where('host_id', $value['id'])->delete();

                $this->destroy($value['id']);

                hook('after_host_delete',['id'=>$value['id'],'product_id'=>$value['product_id'],'module'=>$module]);
            }
            
            $this->commit();
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return ['status' => 400, 'msg' => lang('delete_fail').$e->getMessage() ];
        }

        return ['status' => 200, 'msg' => lang('delete_success')];
    }

    /**
     * 时间 2022-05-26
     * @title 获取通用模块参数
     * @desc 获取通用模块参数
     * @author hh
     * @version v1
     * @return  HostModel host - 当前产品类
     * @return  ClientModel client - 所属用户类
     * @return  ProductModel product - 所属商品类
     * @return  ServerModel server - 关联接口类
     */
    public function getModuleParams()
    {
        $result = [];
        $result['host'] = $this;
        $result['client'] = ClientModel::find($this->getAttr('client_id'));
        $result['product'] = ProductModel::find($this->getAttr('product_id'));
        $result['server'] = ServerModel::find($this->getAttr('server_id'));
        if(!empty($result['server'])){
            $result['server']['password'] = aes_password_decode($result['server']['password']);
        }
        return $result;
    }

    /**
     * 时间 2022-05-28
     * @title 获取当前产品关联模块类型(实例化后不需要传入参数)
     * @desc 获取当前产品关联模块类型
     * @author hh
     * @version v1
     * @param   int param.server_id 产品模型接口ID 接口ID
     * @param   int param.product_id 产品模型商品ID 商品ID
     * @return  string
     */
    public function getModule($param = [])
    {
        $server = ServerModel::find($param['server_id'] ?? $this->getAttr('server_id'));
        if(!empty($server)){
            $module = $server['module'];
        }else{
            // 获取商品的模块
            $ProductModel = ProductModel::findOrEmpty($param['product_id'] ?? $this->getAttr('product_id'));
            $module = $ProductModel->getModule();
        }
        return $module;
    }

    /**
     * 时间 2022-05-28
     * @title 产品开通
     * @desc 产品开通
     * @author hh
     * @version v1
     * @param   int id - 产品ID require
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     */
    public function createAccount($id)
    {
        $host = $this->find($id);
        if(empty($host) || $host['is_delete']){
            return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
        }
        if($host['status'] == 'Active'){
            return ['status'=>400, 'msg'=>lang('host_is_active')];
        }
        if($host['status'] == 'Suspended'){
            return ['status'=>400, 'msg'=>lang('host_is_suspended')];
        }
        $lock = $this->getCreateAccountLock($id);
        if($lock['status'] == 400){
            return $lock;
        }

        hook('before_host_create',['id'=>$id]);

        if($host['billing_cycle']=='onetime'){
            $due_time = 0;
        }else if($host['billing_cycle']=='free' && $host['billing_cycle_time']==0){
            $due_time = 0;
        }else{
            $due_time = time() + $host['billing_cycle_time'];
        }
        $this->update([
            'active_time' => time(),
            'due_time' => $due_time,
            'update_time' => time(),
        ], ['id'=>$id]);

        $upstreamProduct = UpstreamProductModel::where('product_id', $host['product_id'])->find();
        if($upstreamProduct){
            $ResModuleLogic = new ResModuleLogic($upstreamProduct);
            if ($host['status']!='Active'){
                $res = $ResModuleLogic->createAccount($host);
            }
        }else{
            $ModuleLogic = new ModuleLogic();
            if ($host['status']!='Active'){
                $res = $ModuleLogic->createAccount($host);
            }
        }
        if($res['status'] == 200){
            hook('after_host_create_success',['id'=>$id]);

            /*if($host['billing_cycle']=='onetime'){
                $due_time = 0;
            }else if($host['billing_cycle']=='free' && $host['billing_cycle_time']==0){
                $due_time = 0;
            }else{
                $due_time = time() + $host['billing_cycle_time'];
            }*/
            $this->update([
                'status'      => 'Active',
                /*'active_time' => time(),
                'due_time' => $due_time,*/
                'update_time' => time(),
            ], ['id'=>$id]);

            system_notice([
                'name' => 'host_active',
                'email_description' => lang('host_create_success_send_mail'),
                'sms_description' => lang('host_create_success_send_sms'),
                'task_data' => [
                    'client_id' => $host['client_id'],
                    'host_id'   => $id,
                ],
            ]);

            $description = lang('log_module_create_account_success', [
                '{host}'=> 'host#'.$host->id.'#'.$host['name'].'#',
            ]);
        }else{
            hook('after_host_create_fail',['id'=>$id]);

            $this->update([
                'status'      => 'Failed',
                'update_time' => time(),
            ], ['id'=>$id]);

            $description = lang('log_module_create_account_failed', [
                '{host}'=>'host#'.$host->id.'#'.$host['name'].'#',
                '{reason}'=>$res['msg'] ?? '',
            ]);

            if($upstreamProduct){
                system_notice([
                    'name'                  => 'updownstream_action_failed_notice',
                    'email_description'     => lang('updownstream_action_failed_notice_send_mail'),
                    'sms_description'       => lang('updownstream_action_failed_notice_send_sms'),
                    'task_data' => [
                        'client_id' => $host['client_id'],
                        'host_id'   => $id,
                        'template_param'=>[
                            'action' => lang('module_create_account'),
                        ],
                    ],
                ]);
            }
        }
        // 上游开通后自动同步信息
        if($upstreamProduct){
            $this->syncAccount($id);
        }
        $this->clearCreateAccountLock($id);

        upstream_sync_host($id, 'module_create');
        active_log($description, 'host', $host->id);
        return $res;
    }

    /**
     * 时间 2022-05-28
     * @title 产品暂停
     * @desc 产品暂停
     * @author hh
     * @version v1
     * @param int param.id - 产品ID require
     * @param string param.suspend_type overdue 暂停类型(overdue=到期暂停,overtraffic=超流暂停,certification_not_complete=实名未完成,other=其他,downstream=下游暂停)
     * @param string param.suspend_reason - 暂停原因
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     */
    public function suspendAccount($param)
    {
        $id = (int)$param['id'];
        $param['suspend_reason'] = $param['suspend_reason'] ?? '';
        $param['suspend_type'] = $param['suspend_type'] ?? 'overdue';

        $host = $this->find($id);
        if(empty($host) || $host['is_delete']){
            return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
        }
        if($host['status'] == 'Suspended'){
            // 状态先200,这样如果上下游不会失败
            return ['status'=>200, 'msg'=>lang('host_is_suspended')];
        }
        if($host['status'] != 'Active'){
            return ['status'=>400, 'msg'=>lang('host_is_not_active_cannot_suspend')];
        }
        $upstreamProduct = UpstreamProductModel::where('product_id', $host['product_id'])->find();

        hook('before_host_suspend',['id'=>$id]);

        if($upstreamProduct){
            $ResModuleLogic = new ResModuleLogic($upstreamProduct);
            $res = $ResModuleLogic->suspendAccount($host, $param);
        }else{
            $ModuleLogic = new ModuleLogic();
            $res = $ModuleLogic->suspendAccount($host, $param);
        }

        if($res['status'] == 200){
            if (!empty($host['is_sub'])){
                return $res;
            }

            hook('after_host_suspend_success',['id'=>$id]);

            $this->update([
                'status'         => 'Suspended',
                'suspend_type'   => $param['suspend_type'],
                'suspend_reason' => $param['suspend_reason'],
                'suspend_time'   => time(),
                'update_time'    => time(),
            ], ['id'=>$id]);
            // 与‘上游推送信息至本地，本地产品非暂停时，发送邮件和短信’冲突
            if ($host['status']!='Suspended'){
                system_notice([
                    'name'                  => 'host_suspend',
                    'email_description'     => lang('host_suspend_send_mail'),
                    'sms_description'       => lang('host_suspend_send_sms'),
                    'task_data' => [
                        'client_id' => $host['client_id'],
                        'host_id'   => $id,
                    ],
                ]);
            }

            $suspendType = [
                'overdue'=>lang('suspend_type_overdue'),
                'overtraffic'=>lang('suspend_type_overtraffic'),
                'certification_not_complete'=>lang('suspend_type_certification_not_complete'),
                'other'=>lang('suspend_type_other'),
            ];

            upstream_sync_host($id, 'module_suspend');

            $description = lang('log_module_suspend_account_success', [
                '{host}'=>'host#'.$host->id.'#'.$host['name'].'#',
                '{type}'=>$suspendType[ $param['suspend_type'] ] ?? $suspendType['overdue'],
                '{reason}'=>$param['suspend_reason'],
            ]);

        }else{
            hook('after_host_suspend_fail',['id'=>$id,'fail_reason'=>$res['msg']??'']);

            $description = lang('log_module_suspend_account_failed', [
                '{host}'=>'host#'.$host->id.'#'.$host['name'].'#',
                '{reason}'=>$res['msg'] ?? '',
            ]);

            if($upstreamProduct){
                system_notice([
                    'name'                  => 'updownstream_action_failed_notice',
                    'email_description'     => lang('updownstream_action_failed_notice_send_mail'),
                    'sms_description'       => lang('updownstream_action_failed_notice_send_sms'),
                    'task_data' => [
                        'client_id' => $host['client_id'],
                        'host_id'   => $id,
                        'template_param'=>[
                            'action' => lang('module_suspend_account'),
                        ],
                    ],
                ]);
            }
        }
        active_log($description, 'host', $host->id);
        return $res;
    }

    /**
     * 时间 2022-05-28
     * @title 产品解除暂停
     * @desc 产品解除暂停
     * @author hh
     * @version v1
     * @param   int id - 产品ID require
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     */
    public function unsuspendAccount($id)
    {
        $host = $this->find($id);
        if(empty($host) || $host['is_delete']){
            return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
        }
        if($host['status'] == 'Active'){
            // 状态先200,这样如果上下游不会失败
            return ['status'=>200, 'msg'=>lang('host_is_already_unsuspend')];
        }
        if($host['status'] != 'Active' && $host['status'] != 'Suspended'){
            return ['status'=>400, 'msg'=>lang('host_status_not_need_unsuspend')];
        }
        if($host['suspend_type'] == 'upstream'){
            return ['status'=>400, 'msg'=>lang('cannot_unsuspend_from_upstream')];
        }

        $upstreamProduct = UpstreamProductModel::where('product_id', $host['product_id'])->find();

        hook('before_host_unsuspend',['id'=>$id]);

        if($upstreamProduct){
            $ResModuleLogic = new ResModuleLogic($upstreamProduct);
            $res = $ResModuleLogic->unsuspendAccount($host);
        }else{
            $ModuleLogic = new ModuleLogic();
            $res = $ModuleLogic->unsuspendAccount($host);
        }
        if($res['status'] == 200){

            hook('after_host_unsuspend_success',['id'=>$id]);

            $this->update([
                'status'         => 'Active',
                'suspend_reason' => '',
                'suspend_time'   => 0,
                'update_time'    => time(),
            ], ['id'=>$id]);
			if(configuration('cron_due_unsuspend_swhitch')==1){
                if ($host['status']!='Active'){
                    system_notice([
                        'name'                  => 'host_unsuspend',
                        'email_description'     => lang('host_unsuspend_send_mail'),
                        'sms_description'       => lang('host_unsuspend_send_sms'),
                        'task_data' => [
                            'client_id' => $host['client_id'],
                            'host_id'   => $id,
                        ],
                    ]);
                }
			}
            upstream_sync_host($id, 'module_unsuspend');

            $description = lang('log_module_unsuspend_account_success', [
                '{host}'=>'host#'.$host->id.'#'.$host['name'].'#',
            ]);
        }else{
            hook('after_host_unsuspend_fail',['id'=>$id,'fail_reason'=>$res['msg']??'']);

            $description = lang('log_module_unsuspend_account_failed', [
                '{host}'=>'host#'.$host->id.'#'.$host['name'].'#',
                '{reason}'=>$res['msg'] ?? '',
            ]);

            if($upstreamProduct){
                system_notice([
                    'name'                  => 'updownstream_action_failed_notice',
                    'email_description'     => lang('updownstream_action_failed_notice_send_mail'),
                    'sms_description'       => lang('updownstream_action_failed_notice_send_sms'),
                    'task_data' => [
                        'client_id' => $host['client_id'],
                        'host_id'   => $id,
                        'template_param'=>[
                            'action' => lang('module_unsuspend_account'),
                        ],
                    ],
                ]);
            }
        }
        active_log($description, 'host', $host->id);
        return $res;
    }

    /**
     * 时间 2022-05-28
     * @title 产品删除
     * @desc 产品删除
     * @author hh
     * @version v1
     * @param int id - 产品ID require
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     */
    public function terminateAccount($id)
    {
        $host = $this->find($id);
        if(empty($host) || $host['is_delete']){
            return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
        }
        $client = ClientModel::find($host['client_id']);

        $upstreamProduct = UpstreamProductModel::where('product_id', $host['product_id'])->find();

        hook('before_host_terminate',['id'=>$id]);

        // 暂不判断状态,所有状态应该都能删除
        if($upstreamProduct){
            $ResModuleLogic = new ResModuleLogic($upstreamProduct);
            $res = $ResModuleLogic->terminateAccount($host);
            // 记录产品信息
            if ($res['status']==200){
                $HostIpModel = new HostIpModel();
                $hostIp = $HostIpModel->getHostIp([
                    'host_id'   => $id,
                ]);
                $HostAdditionModel = new HostAdditionModel();
                $hostAddition = $HostAdditionModel->where('host_id',$id)->find();

                $UpstreamHostModel = new UpstreamHostModel();
                $upstreamHost = $UpstreamHostModel->where('host_id',$id)->find();

                $notes = [
                    '产品标识：'.$host['name'],
                    'IP地址：'.$hostIp['dedicate_ip'],
                    '操作系统：'.($hostAddition['image_name']??''),
                    '上游ID：'.($upstreamHost['upstream_host_id']??0),
                ];
                $host->save([
                    'notes' => implode("\r\n", $notes),
                ]);
            }
        }else{
            $ModuleLogic = new ModuleLogic();
            $res = $ModuleLogic->terminateAccount($host);
        }
        if($res['status'] == 200){

            $this->update([
                'status'           => 'Deleted',
                'termination_time' => time(),
                'update_time'      => time(),
            ], ['id'=>$id]);

            // 产品删除后，增加商品库存
            $product = ProductModel::find($host['product_id']);
            if ($product['stock_control']==1){
                $product->save([
                    'qty' => $product['qty']+1,
                    'update_time' => time(),
                ]);
                $description = lang('log_module_terminate_account_stock', [
                    '{host}'    => 'host#'.$host->id.'#'.$host['name'].'#',
                    '{product}'  => 'product#'.$product['id'].'#'.$product['name'].'#',
                    '{client}'  => 'client#'.$client->id.'#'.$client['username'].'#',
                    '{qty}' => $product['qty'],
                ]);
                active_log($description, 'host', $host->id);
            }

            hook('after_host_terminate_success',['id'=>$id]);

            if ($host['status']!='Deleted'){
                system_notice([
                    'name'                  => 'host_terminate',
                    'email_description'     => lang('host_delete_send_mail'),
                    'sms_description'       => lang('host_delete_send_sms'),
                    'task_data' => [
                        'client_id' => $host['client_id'],
                        'host_id'   => $id,
                    ],
                ]);
            }

            upstream_sync_host($id, 'module_terminate');

            $description = lang('log_module_terminate_account_success', [
                '{host}'    => 'host#'.$host->id.'#'.$host['name'].'#',
                '{client}'  => 'client#'.$client->id.'#'.$client['username'].'#',
            ]);
        }else{
            hook('after_host_terminate_fail',['id'=>$id,'fail_reason'=>$res['msg']??'']);

            $description = lang('log_module_terminate_account_failed', [
                '{host}'    => 'host#'.$host->id.'#'.$host['name'].'#',
                '{reason}'  => $res['msg'] ?? '',
                '{client}'  => 'client#'.$client->id.'#'.$client['username'].'#',
            ]);

            if($upstreamProduct){
                system_notice([
                    'name'                  => 'updownstream_action_failed_notice',
                    'email_description'     => lang('updownstream_action_failed_notice_send_mail'),
                    'sms_description'       => lang('updownstream_action_failed_notice_send_sms'),
                    'task_data' => [
                        'client_id' => $host['client_id'],
                        'host_id'   => $id,
                        'template_param'=>[
                            'action' => lang('module_terminate_account'),
                        ]
                    ],
                ]);
            }
        }
        active_log($description, 'host', $host->id);
        return $res;
    }

    /**
     * 时间 2022-05-28
     * @title 后台产品内页模块输出
     * @desc 后台产品内页模块输出
     * @author hh
     * @version v1
     * @param int id - 产品ID require
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     * @return string data.content - 内页模块输出
     */
    public function adminArea($id)
    {
        $host = $this->find($id);
        if(empty($host) || $host['is_delete']){
            return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
        }
        $upstreamProduct = UpstreamProductModel::where('product_id', $host['product_id'])
            ->where('mode','only_api')
            ->find();

        if($upstreamProduct){
            $ResModuleLogic = new ResModuleLogic($upstreamProduct);
            $content = $ResModuleLogic->adminArea($host);
        }else{
            $ModuleLogic = new ModuleLogic();
            $content = $ModuleLogic->adminArea($host);
        }
        
        $result = [
            'status' => 200,
            'msg'    => lang('success_message'),
            'data'   => [
                'content' => $content,
            ]
        ];
        return $result;
    }

    /**
     * 时间 2022-10-13
     * @title 自定义导航产品列表
     * @desc 自定义导航产品列表
     * @author hh
     * @version v1
     * @param int id - 导航ID require
     * @return int status - 状态码,200=成功,400=失败
     * @return string msg - 提示信息
     * @return string data.content - 列表页模块输出
     */
    public function menuHostList($id)
    {
        $menu = MenuModel::find($id);
        if(empty($menu) || (empty($menu['module']) && empty($menu['res_module']))){
            return ['status'=>400, 'msg'=>lang('fail_message')];
        }
        $param['product_id'] = json_decode($menu['product_id'], true);

        /*$upstreamProduct = UpstreamProductModel::where('product_id', $param['product_id'][0] ?? 0)->find();*/

        if(!empty($menu['module'])){
            $ModuleLogic = new ModuleLogic();
            $content = $ModuleLogic->hostList($menu['module'], $param);
        }else{
            // 可多选的res模块
            $resCloud = ['whmcs_cloud','mf_cloud','mf_finance'];
            $resDcim  = ['whmcs_dcim','mf_dcim','mf_finance_dcim'];

            $menu['res_module'] = json_decode($menu['res_module'], true) ?? [];
            // res_module是数组,云和DCIM单独判断
            if(count($menu['res_module']) > 1){
                if(!empty(array_intersect($menu['res_module'], $resCloud))){
                    // if(in_array('mf_cloud', $menu['res_module'])){
                        $ModuleLogic = new ModuleLogic();
                        $content = $ModuleLogic->hostList('mf_cloud', $param);
                    // }else{
                    //     $ResModuleLogic = new ResModuleLogic();
                    //     $content = $ResModuleLogic->hostList('mf_finance', $param);
                    // }
                }else if(!empty(array_intersect($menu['res_module'], $resDcim))){
                    $ModuleLogic = new ModuleLogic();
                    $content = $ModuleLogic->hostList('mf_dcim', $param);
                }
            }else{
                $ResModuleLogic = new ResModuleLogic();
                $content = $ResModuleLogic->hostList($menu['res_module'][0], $param);
            }
        }
        
        $result = [
            'status' => 200,
            'msg'    => lang('success_message'),
            'data'   => [
                'content' => $content ?? '',
            ]
        ];
        return $result;
    }

    /**
     * 时间 2022-05-28
     * @title 前台产品内页模块输出
     * @desc 前台产品内页模块输出
     * @author hh
     * @version v1
     * @param int id - 产品ID require
     * @return int status - 状态码,200=成功,400=失败
     * @return string msg - 提示信息
     * @return string data.content - 内页模块输出
     */
    public function clientArea($id)
    {
        $host = $this->find($id);
        if(empty($host) || $host['client_id'] != get_client_id() || $host['is_delete']){
            return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
        }
        $res = hook('get_client_host_id', ['client_id' => get_client_id(true)]);
        $res = array_values(array_filter($res ?? []));
        foreach ($res as $key => $value) {
            if(isset($value['status']) && $value['status']==200){
                $hostId = $value['data']['host'];
            }
        }
        if(isset($hostId) && !in_array($id, $hostId)){
            return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
        }
        $upstreamProduct = UpstreamProductModel::where('product_id', $host['product_id'])
            ->where('mode','only_api')
            ->find();
        
        if($upstreamProduct){
            $ResModuleLogic = new ResModuleLogic($upstreamProduct);
            $content = $ResModuleLogic->clientArea($host);
        }else{
            $ModuleLogic = new ModuleLogic();
            $content = $ModuleLogic->clientArea($host);
        }
        
        $result = [
            'status' => 200,
            'msg'    => lang('success_message'),
            'data'   => [
                'content' => $content,
            ]
        ];
        return $result;
    }

    /**
     * 时间 2022-05-28
     * @title 升降级
     * @desc 升降级
     * @author hh
     * @version v1
     * @param int id - upgrade表ID require
     * @return int status - 状态码,200=成功,400=失败
     * @return string msg - 提示信息
     */
    public function upgradeAccount($id)
    {
        $UpgradeModel = new UpgradeModel();
        $UpgradeModel->startTrans();
        $upgrade = $UpgradeModel->where('id',$id)->lock(true)->find();
        if (empty($upgrade) || $upgrade['status']=='Completed'){
            $UpgradeModel->commit();
            return ['status'=>200, 'msg'=>lang('success_message')];
        }
        $host = $this->find($upgrade['host_id']);
        if (empty($host)){
            $UpgradeModel->commit();
            return ['status'=>400,'msg'=>lang("host_is_not_exist")];
        }
        $upstreamProduct = UpstreamProductModel::where('product_id', $host['product_id'])
            ->where('mode','only_api')
            ->find();
        # 升降级
        if($upgrade['type']=='product'){
            // 获取接口
            /*$product = ProductModel::find($upgrade['rel_id']);
            if($product['type']=='server_group'){
                $server = ServerModel::where('server_group_id', $product['rel_id'])->where('status', 1)->find();
                $serverId = $server['id'] ?? 0;
            }else{
                $serverId = $product['rel_id'];
            }

            $host = $this->find($upgrade['host_id']);
            // wyh 20210109 改 一次性/免费可升级后
            if($host['billing_cycle']=='onetime'){
                if ($product['pay_type']=='onetime'){
                    $hostDueTime = 0;
                }elseif ($product['pay_type']=='free' && $upgrade['billing_cycle_time']==0){
                    $hostDueTime = 0;
                }else{
                    $hostDueTime = time()+$upgrade['billing_cycle_time'];
                }
            }else if($host['billing_cycle']=='free' && $host['billing_cycle_time']==0){
                if ($product['pay_type']=='onetime'){
                    $hostDueTime = 0;
                }elseif ($product['pay_type']=='free' && $upgrade['billing_cycle_time']==0){
                    $hostDueTime = 0;
                }else{
                    $hostDueTime = time()+$upgrade['billing_cycle_time'];
                }
            }else{
                if ($product['pay_type']=='onetime'){
                    $hostDueTime = 0;
                }elseif ($product['pay_type']=='free' && $upgrade['billing_cycle_time']==0){
                    $hostDueTime = 0;
                }else{ # 周期到周期,不变更
                    $hostDueTime = $host['due_time'];
                }
            }

            $this->update([
                'product_id' => $upgrade['rel_id'],
                'server_id' => $serverId,
                'first_payment_amount' => $upgrade['price'],
                'renew_amount' => ($product['pay_type']=='recurring_postpaid' || $product['pay_type']=='recurring_prepayment') ? $upgrade['renew_price'] : 0,
                'billing_cycle' => $product['pay_type'],
                'billing_cycle_name' => $upgrade['billing_cycle_name'],
                'billing_cycle_time' => $upgrade['billing_cycle_time'],
                'due_time' => $hostDueTime,
            ],['id' => $upgrade['host_id']]);*/
            $upgradeData = json_decode($upgrade['data'], true);
            //$upgradeData['order_id'] = $upgrade['order_id'];
            if($upstreamProduct){
                $ResModuleLogic = new ResModuleLogic($upstreamProduct);
                $ResModuleLogic->changeProduct($host, $upgradeData, $upgrade['order_id']);
            }else{
                $ModuleLogic = new ModuleLogic();
                $ModuleLogic->changeProduct($host, $upgradeData, $upgrade['order_id']);
            }

            // 删除原来的自定义字段
            $SelfDefinedFieldValueModel = new SelfDefinedFieldValueModel();
            $SelfDefinedFieldValueModel->withDelete([
                'type'  => 'product',
                'relid' => $host['id'],
            ]);

        }
        else if($upgrade['type']=='config_option'){
            /*$host = $this->find($upgrade['host_id']);
            $this->update([
                'first_payment_amount' => $upgrade['price'],
                'renew_amount' => ($host['billing_cycle']=='recurring_postpaid' || $host['billing_cycle']=='recurring_prepayment') ? $upgrade['renew_price'] : 0,
            ],['id' => $upgrade['host_id']]);*/
            $upgradeData = json_decode($upgrade['data'], true);
            //$upgradeData['order_id'] = $upgrade['order_id'];
            if($upstreamProduct){
                $ResModuleLogic = new ResModuleLogic($upstreamProduct);
                $ResModuleLogic->changePackage($host, $upgradeData, $upgrade['order_id']);
            }else{
                $ModuleLogic = new ModuleLogic();
                $ModuleLogic->changePackage($host, $upgradeData, $upgrade['order_id']);
            }
        }

        $ProductModel = new ProductModel();
        $product = $ProductModel->find($host['product_id']);

        $upgrade->save([
            'status' => 'Completed',
            'update_time' => time()
        ]);
        $UpgradeModel->commit();

        system_notice([
            'name'                  => 'host_upgrad',
            'email_description'     => lang('host_upgrade_send_mail'),
            'sms_description'       => lang('host_upgrade_send_sms'),
            'task_data' => [
                'client_id' => $host['client_id'],
                'host_id'   => $host['id'],
                'template_param'=>[
                    'product_name' => $product['name']??''
                ],
            ],
        ]);
        upstream_sync_host($host['id'], 'update_host',$upgradeData['type']??'',$upgrade['renew_price']);
        return ['status'=>200, 'msg'=>lang('success_message')];
    }

    /**
     * 时间 2022-08-11
     * @title 修改产品备注
     * @desc 修改产品
     * @author theworld
     * @version v1
     * @param int param.id - 产品ID required
     * @param string param.notes - 备注
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function updateHostNotes($param)
    {
        $clientId = get_client_id();
        // 验证产品ID
        $host = $this->find($param['id']);
        if (empty($host) || $host['is_delete']){
            return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
        }

        if($clientId!=$host['client_id']){
            return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
        }


        $this->startTrans();
        try {
            $this->update([
                'client_notes' => $param['notes'] ?? '',
                'update_time' => time()
            ], ['id' => $param['id']]);
            
            $this->commit();
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return ['status' => 400, 'msg' => lang('update_fail')];
        }
        return ['status' => 200, 'msg' => lang('update_success')];
    }

    /**
     * 时间 2022-10-26
     * @title 获取用户所有产品
     * @desc 获取用户所有产品
     * @author theworld
     * @version v1
     * @return array list - 产品
     * @return int list[].id - 产品ID 
     * @return int list[].product_id - 商品ID 
     * @return string list[].product_name - 商品名称 
     * @return string list[].name - 标识 
     * @return string list[].status - 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败Cancelled已取消
     * @return int count - 产品总数
     */
    public function clientHost($param)
    {
        // 获取当前应用
        $app = app('http')->getName();
        if($app=='home'){
            $param['client_id'] = get_client_id();
        }else{
            $param['client_id'] = isset($param['id']) ? intval($param['id']) : 0;
        }
        if(empty($param['client_id'])){
            return ['list' => [], 'count' => 0];
        }

        $where = function (Query $query) use($param) {
            $query->where('h.status', '<>', 'Cancelled');
            if(!empty($param['client_id'])){
                $query->where('h.client_id', (int)$param['client_id']);
            }
            $query->where('h.is_delete', 0);
        };

        $count = $this->alias('h')
            ->field('h.id')
            ->leftjoin('product p', 'p.id=h.product_id')
            ->where($where)
            ->count();
        $hosts = $this->alias('h')
            ->field('h.id,h.product_id,p.name product_name,h.name,h.status')
            ->leftjoin('product p', 'p.id=h.product_id')
            ->where($where)
            ->withAttr('product_name', function($val) use ($app) {
                if($app == 'home'){
                    $multiLanguage = hook_one('multi_language', [
                        'replace' => [
                            'product_name' => $val,
                        ],
                    ]);
                    if(isset($multiLanguage['product_name'])){
                        $val = $multiLanguage['product_name'];
                    }
                }
                return $val;
            })
            ->select()
            ->toArray();

        return ['list' => $hosts, 'count' => $count];
    }

    /**
     * 时间 2023-01-31
     * @title 模块按钮输出
     * @desc 模块按钮输出
     * @author hh
     * @version v1
     * @param   int param.id - 产品ID require
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     * @return  string data.button[].type - 按钮类型(暂时都是default)
     * @return  string data.button[].func - 按钮功能(create=开通,suspend=暂停,unsuspend=解除暂停,terminate=删除,renew=续费)
     * @return  string data.button[].name - 名称
     */
    public function moduleAdminButton($param)
	{
        $result = [
            'status' => 200,
            'msg'    => lang('success_message'),
            'data'   => [
                'button' => [],
            ],
        ];
        $host = $this->find($param['id']);
        if(empty($host) || $host['is_delete']){
            return $result;
        }
        $button = [];
        if(in_array($host['status'], ['Unpaid','Pending','Active','Suspended','Deleted','Failed'])){
            $button[] = [
                'type' => 'default',
                'func' => 'create',
                'name' => lang('module_button_create'),
            ];
        }
        if(in_array($host['status'], ['Pending','Active'])){
            $button[] = [
                'type' => 'default',
                'func' => 'suspend',
                'name' => lang('module_button_suspend'),
            ];
        }
        if(in_array($host['status'], ['Suspended'])){
            $button[] = [
                'type' => 'default',
                'func' => 'unsuspend',
                'name' => lang('module_button_unsuspend'),
            ];
        }
        if(in_array($host['status'], ['Pending','Active','Suspended','Failed'])){
            $button[] = [
                'type' => 'default',
                'func' => 'terminate',
                'name' => lang('module_button_terminate'),
            ];
        }
        if(in_array($host['status'], ['Active','Suspended'])){
            // 判断下续费插件
            $renew = PluginModel::where('name', 'IdcsmartRenew')->where('status', 1)->value('id');
            if($renew){
                $button[] = [
                    'type' => 'default',
                    'func' => 'renew',
                    'name' => lang('module_button_renew'),
                ];
            }
        }

        $UpstreamHostModel = new UpstreamHostModel();
        $upstreamHost = $UpstreamHostModel->where('host_id',$param['id'])->find();
        if (!empty($upstreamHost)){
            $button[] = [
                'type' => 'default',
                'func' => 'sync',
                'name' => lang('module_button_sync'),
            ];
        }else{
            $ModuleLogic = new ModuleLogic();
            $module = $host->getModule();
            $exist = $ModuleLogic->moduleMethodExist($module, 'syncAccount');
            if($exist){
                $button[] = [
                    'type' => 'default',
                    'func' => 'sync',
                    'name' => lang('module_button_sync'),
                ];
            }
        }

        $result['data']['button'] = $button;

        return $result;
    }

    /**
     * @title 上游同步产品信息到下游
     * @desc  上游同步产品信息到下游
     * @author theworld
     * @version v1
     * @param   int    id 财务产品ID
     * @param   string action  动作module_create模块开通module_suspend模块暂停module_unsuspend模块解除暂停module_terminate模块删除update_host修改产品delete_host删除产品host_renew产品续费
     */
    public function upstreamSyncHost($id, $action = '', $type = '', $renewPrice = 0)
    {
        $host = $this->find($id);
        if(empty($host) || $host['is_delete']){
            return false;
        }
        // 子产品不验证
        if ($host['is_sub']){

        }else{
            if(empty($host['downstream_host_id'])){
                return false;
            }
            $downstreamInfo = json_decode($host['downstream_info'], true) ?? [];
            if(empty($downstreamInfo)){
                return false;
            }
        }

        // 魔方财务
        if (isset($downstreamInfo['type']) && $downstreamInfo['type']=='finance'){

            $data = $this->syncDownStreamHost($host);

            $sign = create_sign(['id'=>$host['downstream_host_id']],$downstreamInfo['token']);

            $data = array_merge($data,$sign);

            $res = curl(rtrim($downstreamInfo['url'],'/').'/api/host/sync', $data, 30, 'POST');
        }else{
            $moduleInfo = [];
            $otherInfo = [];
            $downParentHostId = 0;
            $parentHostId = 0;
            hook('push_downstream_module_info',['id'=>$id,
                'module_info'=>&$moduleInfo,
                'host_ip'=>&$hostIp,
                'other_info'=>&$otherInfo,
                'down_parent_host_id'=>&$downParentHostId,
                'parent_host_id'=>&$parentHostId,
            ]);
            if (!empty($parentHostId)){
                $parentHost = $this->find($parentHostId);
                $downstreamInfo = json_decode($parentHost['downstream_info']??'', true) ?? [];
            }
            if(empty($downstreamInfo)){
                return false;
            }

            $api = ApiModel::find($downstreamInfo['api'] ?? 0);
            if(empty($api)){
                return false;
            }

            // 获取IP信息
            $HostIpModel = new HostIpModel();
            // wyh 20240522 改，留了个大坑，多级代理根本推送不下去ip
            //$hostIp = $HostIpModel->getHostIp(['host_id'=>$id]);
            $hostIp = $HostIpModel->getHostIp(['host_id'=>$id,'client_id'=>$host['client_id']]);
            // 获取附加信息表,退到下游
            $hostAddition = HostAdditionModel::field('country_id,city,area,power_status,image_icon,image_name,username,password,port')->where('host_id', $id)->find();
            if($hostAddition){
                $hostAddition = $hostAddition->toArray();
            }else{
                $hostAddition = [];
            }

            $api['public_key'] = aes_password_decode($api['public_key']);

            $data = json_encode(['action' => $action, 'host' => $host, 'host_ip'=>$hostIp, 'host_addition'=>$hostAddition,'module_info'=>$moduleInfo,'type'=>$type,'other_info'=>$otherInfo,'renew_price'=>$renewPrice]);

            $crypto = '';

            foreach (str_split($data, 117) as $chunk) {

                openssl_public_encrypt($chunk, $encryptData, $api['public_key']);

                $crypto .= $encryptData;
            }

            $data = base64_encode($crypto);

            $res = curl(rtrim($downstreamInfo['url'],'/').'/console/v1/upstream/sync', ['host_id' => $host['downstream_host_id'],'down_parent_host_id'=>$downParentHostId, 'data' => $data], 30, 'POST');

        }

        active_log(lang("upstream_host_downstream_update",['{id}'=>$id,"{downstream_id}"=>$host['downstream_host_id'],"{result}"=>json_encode($res,JSON_UNESCAPED_UNICODE)]),"host",$id);

        return true;
    }


    /**
     * 时间 2023-04-14
     * @title 产品内页模块输入框输出
     * @desc 产品内页模块输入框输出
     * @author hh
     * @version v1
     * @param   int id - 产品ID require
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     * @return  string data[].name - 配置小标题
     * @return  string data[].field[].name - 名称
     * @return  string data[].field[].key - 标识(不要重复)
     * @return  string data[].field[].value - 当前值
     * @return  bool   data[].field[].disable - 状态(false=可修改,true=不可修改)
     */
    public function moduleField($id)
    {
        $host = $this->find($id);
        if(empty($host) || $host['is_delete']){
            return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
        }
        $upstreamProduct = UpstreamProductModel::where('product_id', $host['product_id'])->where('mode','only_api')->find();

        if($upstreamProduct){
            $ResModuleLogic = new ResModuleLogic($upstreamProduct);
            $result = $ResModuleLogic->adminField($host);
        }else{
            $ModuleLogic = new ModuleLogic();
            $result = $ModuleLogic->adminField($host);
        }

        return $result;
    }

    // 同步数据至下游（魔方财务）
    public function syncDownStreamHost(HostModel $host)
    {
        $id = $host['id'];
        // 状态Unpaid未付款Pending开通中Active已开通Suspended已暂停Deleted已删除Failed开通失败Cancelled已取消
        if ($host['status']=='Unpaid' || $host['status']=='Pending'){
            $domainstatus = 'Pending';
        }elseif ($host['status']=='Suspended'){
            $domainstatus = 'Suspended';
        }elseif ($host['status']=='Deleted'){
            $domainstatus = 'Deleted';
        }elseif ($host['status']=='Failed'){
            $domainstatus = 'Failed';
        }elseif ($host['status']=='Cancelled'){
            $domainstatus = 'Cancelled';
        }elseif ($host['status']=='Active'){
            $domainstatus = 'Active';
        }else{
            $domainstatus = 'Pending';
        }

        $ProductModel = new ProductModel();
        $product = $ProductModel->where('id',$host['product_id'])->find();
        if ($product['type']=='server'){
            $ServerModel = new ServerModel();
            $server = $ServerModel->where('id',$product['rel_id'])->find();
        }else{
            $ServerGroupModel = new ServerGroupModel();
            $serverGroup = $ServerGroupModel->where('id',$product['rel_id'])->find();
            $ServerModel = new ServerModel();
            $server = $ServerModel->where('server_group_id',$serverGroup['id'])->find();
        }
        
        // 获取IP信息
        $HostIpModel = new HostIpModel();
        $hostIp = $HostIpModel->getHostIp(['host_id'=>$id]);

        $data = [
            'id' => $host['downstream_host_id'],
            'domain' => $host['name']??"", // wyh 20240308 修改bug,同步主机名至魔方财务
            'username' => $username??"",
            'password' => $password??"",
            'os' => $image??"",
            'os_url' => "",
            'dedicatedip' => $hostIp['dedicate_ip'] ?? '',
            'assignedips' => $hostIp['assign_ip'] ?? '',
            'port' => $port??"",
            'suspendreason' => $host['suspend_reason'],
            'nextduedate' => $host['due_time'],
            'domainstatus' => $domainstatus,
        ];

        return $data;
    }

    /**
     * 时间 2024-01-19
     * @title 获取产品开通锁
     * @desc  获取产品开通锁,防止重复开通
     * @author hh
     * @version v1
     * @param   int $id - 产品ID require
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     */
    public function getCreateAccountLock($id)
    {
        $cacheKey = 'HOST_DEFAULT_ACTION_CREATE_' . $id;
        if(!empty(cache($cacheKey))){
            return ['status'=>400, 'msg'=>lang('host_is_creating_please_wait_and_retry')];
        }
        cache($cacheKey, 1, 180);
        return ['status'=>200, 'msg'=>lang('success_message')];
    }

    /**
     * 时间 2024-01-19
     * @title 清除产品开通锁
     * @desc  清除产品开通锁
     * @author hh
     * @version v1
     * @param   int id - 产品ID require
     */
    public function clearCreateAccountLock($id)
    {
        $cacheKey = 'HOST_DEFAULT_ACTION_CREATE_' . $id;
        cache($cacheKey, NULL);
    }

    /**
     * 时间 2024-05-20
     * @title 后台产品内页实例操作输出
     * @desc  后台产品内页实例操作输出
     * @author hh
     * @version v1
     * @param   int id - 产品ID require
     * @return  int status - 状态(200=成功,400=失败)
     * @return  string msg - 信息
     * @return  string data.content - 后台产品内页实例操作输出
     */
    public function adminAreaModuleOperate($id)
    {
        $host = $this->find($id);
        if(empty($host) || $host['is_delete']){
            return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
        }
        $upstreamProduct = UpstreamProductModel::where('product_id', $host['product_id'])->where('mode','only_api')->find();

        if($upstreamProduct){
            $ResModuleLogic = new ResModuleLogic($upstreamProduct);
            $content = $ResModuleLogic->adminAreaModuleOperate($host);
        }else{
            $ModuleLogic = new ModuleLogic();
            $content = $ModuleLogic->adminAreaModuleOperate($host);
        }
        
        $result = [
            'status' => 200,
            'msg'    => lang('success_message'),
            'data'   => [
                'content' => $content,
            ]
        ];
        return $result;
    }

    /**
     * 时间 2024-06-06
     * @title 拉取上游信息
     * @desc 拉取上游信息,同步模块数据到系统表
     * @author wyh
     * @version v1
     * @param   int id - 产品ID required
     */
    public function syncAccount($id)
    {
        $host = $this->find($id);

        if(empty($host) || $host['is_delete']){
            return ['status'=>400, 'msg'=>lang('host_is_not_exist')];
        }

        $client = ClientModel::find($host['client_id']);

        $upstreamProduct = UpstreamProductModel::where('product_id', $host['product_id'])->find();
        if(!empty($upstreamProduct)){
            $ResModuleLogic = new ResModuleLogic($upstreamProduct);
            $res = $ResModuleLogic->syncAccount($host);
        }else{
            $ModuleLogic = new ModuleLogic();
            $res = $ModuleLogic->syncAccount($host);
        }
        if($res['status'] == 200){
            $update = [
                'update_time'   => time(),
                'base_info'     => $res['data']['base_info'] ?? '',
            ];
            // 更新产品
            if(isset($res['data']['status'])){
                $update['status'] = $res['data']['status'];
            }
            if(!empty($upstreamProduct)){
                $hostSyncDueTimeOpen = configuration('host_sync_due_time_open');
                $hostSyncDueTimeApplyRange = configuration('host_sync_due_time_apply_range');
                if($hostSyncDueTimeApplyRange==1){
                    $hostSyncDueTimeProductIds = array_filter(explode(',', configuration('host_sync_due_time_product_ids')));
                    if(!in_array($host['product_id'], $hostSyncDueTimeProductIds)){
                        $hostSyncDueTimeOpen = 0;
                    }
                }
                if($hostSyncDueTimeOpen==1 && isset($res['data']['due_time'])){
                    $update['due_time'] = $res['data']['due_time'];
                }
            }

            $host->save($update);

            // 同步IP信息
            if(isset($res['data']['dedicate_ip']) && isset($res['data']['assign_ip'])){
                $HostIpModel = new HostIpModel();
                $HostIpModel->hostIpSave([
                    'host_id'       => $id,
                    'dedicate_ip'   => $res['data']['dedicate_ip'],
                    'assign_ip'     => $res['data']['assign_ip'],
                    'ip_num'        => $res['data']['ip_num'] ?? 0,
                ]);
            }

            // 同步附加表
            if(isset($res['data']) && is_array($res['data'])){
                $HostAdditionModel = new HostAdditionModel();
                $HostAdditionModel->hostAdditionSave($id, $res['data']);
            }

            if (isset($res['data']['other_params']) && $res['data']['other_params']){
                (new ModuleLogic())->syncHostOtherParams($host,$res['data']['other_params']);
            }

            if ($host->getModule()=='mf_cloud'){
                upstream_sync_host($id, 'update_host','upgrade_ip_num');
            }else{
                upstream_sync_host($id, 'update_host','upgrade_common_config');
            }

            $description = lang('log_module_sync_account_success', [
                '{host}'    => 'host#'.$host->id.'#'.$host['name'].'#',
                '{client}'  => 'client#'.$client->id.'#'.$client['username'].'#',
            ]);
        }else{
            $description = lang('log_module_sync_account_failed', [
                '{host}'    => 'host#'.$host->id.'#'.$host['name'].'#',
                '{reason}'  => $res['msg'] ?? '',
                '{client}'  => 'client#'.$client->id.'#'.$client['username'].'#',
            ]);
        }
        active_log($description, 'host', $host->id);
        return $res;
    }

    /**
     * 时间 2024-06-18
     * @title 前台产品列表页(云/DCIM)
     * @desc  前台产品列表页(云/DCIM)
     * @author hh
     * @version v1
     * @param   int param.page 1 页数
     * @param   int param.limit - 每页条数
     * @param   string param.orderby - 排序(id,due_time,status)
     * @param   string param.sort - 升/降序
     * @param   string param.keywords - 关键字搜索:商品名称/产品名称/IP
     * @param   int param.country_id - 搜索:国家ID
     * @param   string param.city - 搜索:城市
     * @param   string param.area - 搜索:区域
     * @param   string param.status - 产品状态(Unpaid=未付款,Pending=开通中,Active=已开通,Suspended=已暂停,Deleted=已删除)
     * @param   string param.tab - 状态using使用中expiring即将到期overdue已逾期deleted已删除
     * @return  array data.list - 列表数据
     * @return  int data.list[].id - 产品ID
     * @return  int data.list[].product_id - 商品ID
     * @return  string data.list[].name - 产品标识
     * @return  string data.list[].status - 产品状态(Unpaid=未付款,Pending=开通中,Active=已开通,Suspended=已暂停,Deleted=已删除)
     * @return  int data.list[].active_time - 开通时间
     * @return  int data.list[].due_time - 到期时间
     * @return  string data.list[].client_notes - 用户备注
     * @return  string data.list[].product_name - 商品名称
     * @return  string data.list[].country - 国家
     * @return  string data.list[].country_code - 国家代码
     * @return  int data.list[].country_id - 国家ID
     * @return  string data.list[].city - 城市
     * @return  string data.list[].area - 区域
     * @return  string data.list[].power_status - 电源状态(on=开机,off=关机,operating=操作中,fault=故障)
     * @return  string data.list[].image_name - 镜像名称
     * @return  string data.list[].image_icon - 镜像图标(Windows,CentOS,Ubuntu,Debian,ESXi,XenServer,FreeBSD,Fedora,ArchLinux,Rocky,AlmaLinux,OpenEuler,RedHat,其他)
     * @return  int data.list[].ip_num - IP数量
     * @return  string data.list[].dedicate_ip - 主IP
     * @return  string data.list[].assign_ip - 附加IP(英文逗号分隔)
     * @return  string data.list[].base_info - 产品基础信息
     * @return  array|object data.list[].self_defined_field - 自定义字段值(键是自定义字段ID,值是填的内容)
     * @return  int data.list[].show_base_info - 产品列表是否展示基础信息：1是默认，0否
     * @return  int data.list[].is_auto_renew - 是否自动续费(0=否,1=是)
     * @return  int data.count - 总条数
     * @return  int data.using_count - 使用中产品数量
     * @return  int data.expiring_count - 即将到期产品数量
     * @return  int data.overdue_count - 已逾期产品数量
     * @return  int data.deleted_count - 已删除产品数量
     * @return  int data.all_count - 全部产品数量
     * @return  int data.data_center[].country_id - 国家ID
     * @return  string data.data_center[].city - 城市
     * @return  string data.data_center[].area - 区域
     * @return  string data.data_center[].country_name - 国家
     * @return  string data.data_center[].country_code - 国家代码
     * @return  string data.data_center[].customfield.multi_language.city - 城市多语言
     * @return  string data.data_center[].customfield.multi_language.city - 区域多语言
     * @return  int data.self_defined_field[].id - 自定义字段ID
     * @return  string data.self_defined_field[].field_name - 自定义字段名称
     * @return  string data.self_defined_field[].field_type - 字段类型(text=文本框,link=链接,password=密码,dropdown=下拉,tickbox=勾选框,textarea=文本区)
     */
    public function homeHostList($param)
    {
        $result = [
            'status' => 200,
            'msg'    => lang('success_message'),
            'data'   => [
                'list'                  => [],
                'count'                 => 0,
                'expiring_count'        => 0,
                'data_center'           => [],
                'self_defined_field'    => [],
            ]
        ];

        $clientId = get_client_id();

        if(empty($clientId)){
            return $result;
        }
        
        $param['page'] = isset($param['page']) ? ($param['page'] ? (int)$param['page'] : 1) : 1;
        $param['limit'] = isset($param['limit']) ? ($param['limit'] ? (int)$param['limit'] : config('idcsmart.limit')) : config('idcsmart.limit');
        $param['sort'] = isset($param['sort']) ? ($param['sort'] ?: config('idcsmart.sort')) : config('idcsmart.sort');
        $param['orderby'] = isset($param['orderby']) && in_array($param['orderby'], ['id','due_time','status']) ? $param['orderby'] : 'id';
        $param['orderby'] = 'h.'.$param['orderby'];  

        $where = [];
        $whereDataCenter = [];
        $whereOr = [];

        $where[] = ['h.client_id', '=', $clientId];
        $whereDataCenter[] = ['h.client_id', '=', $clientId];
        $where[] = ['h.status', '<>', 'Cancelled'];
        $whereDataCenter[] = ['h.status', '<>', 'Cancelled'];

        if (!empty($param['sub_host_ids']) && is_array($param['sub_host_ids'])){
            $where[] = ['h.id','not in',$param['sub_host_ids']];
        }

        // 前台是否展示已删除产品
        $homeShowDeletedHost = configuration('home_show_deleted_host');
        if($homeShowDeletedHost!=1){
            $where[] = ['h.status', '<>', 'Deleted'];
            $whereDataCenter[] = ['h.status', '<>', 'Cancelled'];
        }
        
        if(isset($param['m']) && !empty($param['m'])){
            $menu = MenuModel::where('id', $param['m'])->find();
            if(!empty($menu)){
                $menu['product_id'] = json_decode($menu['product_id'], true);
                if(!empty($menu['product_id'])){
                    $productId = $menu['product_id'];
                }else{
                    $productId = [];
                    // 根据模块/res模块来获取商品ID
                    if(!empty($menu['module'])){
                        $productId = ProductModel::alias('p')
                                    ->leftjoin('server s','p.type=\'server\' AND p.rel_id=s.id')
                                    ->leftjoin('server_group sg','p.type=\'server_group\' AND p.rel_id=sg.id')
                                    ->leftjoin('server ss','ss.server_group_id=sg.id')
                                    ->where('s.module|ss.module', $menu['module'])
                                    ->column('p.id');
                    }
                    // 验证res模块
                    $menu['res_module'] = json_decode($menu['res_module'], true);
                    if(!empty($menu['res_module'])){
                        $productId = array_merge($productId, UpstreamProductModel::alias('up')
                                ->join('product p','up.product_id=p.id')
                                ->whereIn('up.res_module', $menu['res_module'])
                                ->column('p.id'));
                    }
                }
                if(!empty($productId)){
                    $where[] = ['h.product_id', 'IN', $productId ];
                    $whereDataCenter[] = ['h.product_id', 'IN', $productId ];
                }else{
                    // 没有商品时
                    $where[] = ['h.product_id', '=', 0 ];
                    $whereDataCenter[] = ['h.product_id', '=', 0 ];
                }
            }
        }

        // 获取子账户可见产品
        $res = hook('get_client_host_id', ['client_id' => get_client_id(false)]);
        $res = array_values(array_filter($res ?? []));
        foreach ($res as $key => $value) {
            if(isset($value['status']) && $value['status']==200){
                $hostId = $value['data']['host'];
            }
        }
        if(isset($hostId) && !empty($hostId)){
            $where[] = ['h.id', 'IN', $hostId];
            $whereDataCenter[] = ['h.id', 'IN', $hostId];
        }
        $where[] = ['h.is_delete', '=', 0];
        $whereDataCenter[] = ['h.is_delete', '=', 0];
        
        $language = get_client_lang();
        $countryField = ['en-us'=> 'nicename'];
        $countryName = $countryField[ $language ] ?? 'name_zh';

        // theworld 20240401 获取即将到期数量
        // $expiringCount = $this
        //     ->alias('h')
        //     ->leftJoin('product pro', 'h.product_id=pro.id')
        //     ->leftJoin('host_addition ha', 'h.id=ha.host_id')
        //     ->where($where)
        //     ->where(function($query){
        //         $time = time();
        //         $renewalFirstDay = configuration('cron_due_renewal_first_day');
        //         $timeRenewalFirst = strtotime(date('Y-m-d 23:59:59', $time+$renewalFirstDay*24*3600));
        //         $query->whereIn('h.status', ['Pending', 'Active'])->where('h.due_time', '>', $time)->where('h.due_time', '<=', $timeRenewalFirst)->where('billing_cycle', '<>', 'free')->where('billing_cycle', '<>', 'onetime');
        //     })
        //     ->count();
        $usingCount = $this->usingCount($where);
        $expiringCount = $this->expiringCount($where);
        $overdueCount = $this->overdueCount($where);
        $deletedCount = $this->deletedCount($where);
        $allCount = $this->allCount($where);

        // theworld 20240401 列表过滤条件移动  
        if(isset($param['keywords']) && trim($param['keywords']) !== ''){
            $whereOr[] = ['pro.name|h.name|hi.dedicate_ip|h.client_notes', 'LIKE', '%'.$param['keywords'].'%'];
            try{
                $language = get_client_lang();

                $id = ProductModel::alias('p')
                    ->leftJoin('addon_multi_language ml', 'p.name=ml.name')
                    ->leftJoin('addon_multi_language_value mlv', 'ml.id=mlv.language_id AND mlv.language="'.$language.'"')
                    ->whereLike('p.name|mlv.value', '%'.$param['keywords'].'%')
                    ->limit(200)
                    ->column('p.id');
                if(!empty($id)){
                    $whereOr[] = ['pro.id', 'IN', $id];
                }
            }catch(\Exception $e){
                
            }
        }
        // 数据中心搜索改为country_id+city+area
        if(isset($param['country_id']) && is_numeric($param['country_id'])){
            $where[] = ['ha.country_id', '=', $param['country_id']];
        }
        if(isset($param['city']) && $param['city'] !== ''){
            $where[] = ['ha.city', '=', $param['city']];
        }
        if(isset($param['area']) && $param['area'] !== ''){
            $where[] = ['ha.area', '=', $param['area']];
        }
        if(isset($param['status']) && !empty($param['status'])){
            if($param['status'] == 'Pending'){
                $where[] = ['h.status', 'IN', ['Pending','Failed']];
            }else if(in_array($param['status'], ['Unpaid','Active','Suspended','Deleted'])){
                $where[] = ['h.status', '=', $param['status']];
            }
        }
        if(isset($param['tab']) && !empty($param['tab'])){
            if($param['tab']=='using'){
                $where[] = ['h.status', 'IN', ['Pending','Active']];
            }else if($param['tab']=='expiring'){
                $time = time();
                $renewalFirstDay = configuration('cron_due_renewal_first_day');
                $timeRenewalFirst = strtotime(date('Y-m-d 23:59:59', $time+$renewalFirstDay*24*3600));

                $where[] = ['h.status', 'IN', ['Pending','Active']];
                $where[] = ['h.due_time', '>', $time];
                $where[] = ['h.due_time', '<=', $timeRenewalFirst];
                $where[] = ['h.billing_cycle', '<>', 'free'];
                $where[] = ['h.billing_cycle', '<>', 'onetime'];
            }else if($param['tab']=='overdue'){
                $time = time();

                $where[] = ['h.status', 'IN', ['Pending', 'Active', 'Suspended', 'Failed']];
                $where[] = ['h.due_time', '<=', $time];
                $where[] = ['h.billing_cycle', '<>', 'free'];
                $where[] = ['h.billing_cycle', '<>', 'onetime'];
            }else if($param['tab']=='deleted'){
                $time = time();
                $where[] = ['h.status', '=', 'Deleted'];
            }
        }

        $count = $this
            ->alias('h')
            ->leftJoin('product pro', 'h.product_id=pro.id')
            ->leftJoin('host_ip hi', 'h.id=hi.host_id')
            ->leftJoin('host_addition ha', 'h.id=ha.host_id')
            ->where($where)
            ->where(function($query) use ($whereOr){
                if(!empty($whereOr)){
                    $query->whereOr($whereOr);
                }
            })
            ->group('h.id')
            ->count();

        $host = $this
            ->alias('h')
            ->field('h.id,h.product_id,h.name,h.status,h.active_time,h.due_time,h.client_notes,pro.name product_name,c.'.$countryName.' country,c.iso country_code,ha.country_id, ha.city,ha.area,ha.power_status,ha.image_name,ha.image_icon,hi.ip_num,hi.dedicate_ip,hi.assign_ip,h.base_info,pro.show_base_info')
            ->leftJoin('product pro', 'h.product_id=pro.id')
            ->leftJoin('host_ip hi', 'h.id=hi.host_id')
            ->leftJoin('host_addition ha', 'h.id=ha.host_id')
            ->leftJoin('country c', 'ha.country_id=c.id')
            ->where($where)
            ->where(function($query) use ($whereOr){
                if(!empty($whereOr)){
                    $query->whereOr($whereOr);
                }
            })
            ->withAttr('status', function($val){
                return $val == 'Failed' ? 'Pending' : $val;
            })
            ->withAttr('product_name', function($val){
                if(!empty($val)){
                    $multiLanguage = hook_one('multi_language', [
                        'replace' => [
                            'product_name' => $val,
                        ],
                    ]);
                    if(isset($multiLanguage['product_name'])){
                        $val = $multiLanguage['product_name'];
                    }
                }
                return $val;
            })
            ->withAttr('city', function($val){
                $multiLanguage = hook_one('multi_language', [
                    'replace' => [
                        'city' => $val,
                    ],
                ]);
                if(isset($multiLanguage['city'])){
                    $val = $multiLanguage['city'];
                }
                return $val;
            })
            ->withAttr('area', function($val){
                $multiLanguage = hook_one('multi_language', [
                    'replace' => [
                        'area' => $val,
                    ],
                ]);
                if(isset($multiLanguage['area'])){
                    $val = $multiLanguage['area'];
                }
                return $val;
            })
            ->withAttr('image_name', function($val){
                $multiLanguage = hook_one('multi_language', [
                    'replace' => [
                        'image_name' => $val,
                    ],
                ]);
                if(isset($multiLanguage['image_name'])){
                    $val = $multiLanguage['image_name'];
                }
                return $val;
            })
            ->withAttr('ip_num', function($val){
                return $val ?? 0;
            })
            ->withAttr('dedicate_ip', function($val){
                return $val ?? '';
            })
            ->withAttr('assign_ip', function($val){
                return $val ?? '';
            })
            ->limit($param['limit'])
            ->page($param['page'])
            ->order($param['orderby'], $param['sort'])
            ->group('h.id')
            ->select()
            ->toArray();

        if(!empty($host)){
            $hostId = array_column($host, 'id');
            $productId = array_column($host, 'product_id');

            $SelfDefinedFieldModel = new SelfDefinedFieldModel();
            $selfDefinedField = $SelfDefinedFieldModel->getHostListSelfDefinedFieldValue([
                'product_id' => $productId,
                'host_id'    => $hostId,
            ]);

            $autoRenewHostId = hook_one('get_auto_renew_host_id', ['host_id'=>$hostId]);
            $autoRenewHostId = $autoRenewHostId ? array_flip($autoRenewHostId) : [];
        }
        foreach($host as $k=>$v){
            $host[$k]['self_defined_field'] = $selfDefinedField['self_defined_field_value'][ $v['id'] ] ?? (object)[];
            $host[$k]['is_auto_renew'] = isset($autoRenewHostId[ $v['id'] ]) ? 1 : 0;
        }
        // 获取所有可用数据中心
        $dataCenter = $this
                    ->alias('h')
                    ->field('ha.country_id,ha.city,ha.area,c.'.$countryName.' country_name,c.iso country_code')
                    ->join('host_addition ha', 'h.id=ha.host_id')
                    ->leftJoin('product pro', 'h.product_id=pro.id')
                    ->leftJoin('host_ip hi', 'h.id=hi.host_id')
                    ->leftJoin('country c', 'ha.country_id=c.id')
                    ->where($whereDataCenter)
                    ->where(function($query) use ($whereOr){
                        if(!empty($whereOr)){
                            $query->whereOr($whereOr);
                        }
                    })
                    ->where('ha.country_id', '>', 0)
                    ->group('ha.country_id,ha.city,ha.area')
                    ->select()
                    ->toArray();

        foreach($dataCenter as $k=>$v){
            $multiLanguage = hook_one('multi_language', [
                'replace' => [
                    'city' => $v['city'],
                    'area' => $v['area'],
                ],
            ]);
            if(!empty($multiLanguage)){
                $dataCenter[$k]['customfield']['multi_language'] = $multiLanguage;
            }
        }

        $result['data']['list']  = $host;
        $result['data']['count'] = $count;
        $result['data']['using_count'] = $usingCount;
        $result['data']['expiring_count'] = $expiringCount;
        $result['data']['overdue_count'] = $overdueCount;
        $result['data']['deleted_count'] = $deletedCount;
        $result['data']['all_count'] = $allCount;
        $result['data']['data_center'] = $dataCenter;
        $result['data']['self_defined_field'] = $selfDefinedField['self_defined_field'] ?? [];
        return $result;
    }

    // 根据配置计算产品base_price(仅考虑本地产品,test)
    public function updateHostBasePrice($hostId)
    {
        $basePrice = 0;
        $HostModel = new \app\common\model\HostModel();
        $host = $HostModel->where('id', $hostId)->find();
        if(!$host){
            return $basePrice;
        }
        $product = (new \app\common\model\ProductModel())->where('id', $host['product_id'])->find();
        if(!$product){
            return $basePrice;
        }
        $module = $product->getModule();
        if(!$module){
            return $basePrice;
        }
        $ModuleLogic = new \app\common\logic\ModuleLogic();
        if ($module=='mf_cloud'){
            $HostLinkModel = new \server\mf_cloud\model\HostLinkModel();
            $result = $ModuleLogic->cartCalculatePrice($product, $HostLinkModel->currentConfig($hostId),1,'cal_price');
            if (isset($result['data']['base_price'])){
                $HostModel->where('id', $hostId)->update(['base_price'=>(float)$result['data']['base_price']]);
            }
        }elseif ($module=='mf_dcim'){
            $HostLinkModel = new \server\mf_dcim\model\HostLinkModel();
            $result = $ModuleLogic->cartCalculatePrice($product, $HostLinkModel->currentConfig($hostId),1,'cal_price');
            if (isset($result['data']['base_price'])){
                $HostModel->where('id', $hostId)->update(['base_price'=>(float)$result['data']['base_price']]);
            }
        }

        return $basePrice;
    }

    public function hostUpdateDownstream($param)
    {
        $host = $this->find($param['id']);

        if (empty($host)){
            return ['status'=>400,'msg'=>lang('host_is_not_exist')];
        }

        if (empty($param['token']) || empty($param['downstream_url'])){
            return ['status'=>400,'msg'=>lang('error_param')];
        }
//
//        if ($host['product_id']!=$param['product_id']){
//            return ['status'=>400,'msg'=>lang('host_not_the_same_product')];
//        }
//
//        $oldHostDownstreamInfo = json_decode($oldHost['downstream_info'],true);
//        if ($oldHostDownstreamInfo['token']!=$param['token']){
//            return ['status'=>400,'msg'=>lang('host_token_error')];
//        }
//
//        if (strpos($param['downstream_url'], $oldHostDownstreamInfo['url']) === false){
//            return ['status'=>400,'msg'=>lang('error_param')];
//        }

        if ($host['client_id'] != get_client_id()){
            return ['status'=>400,'msg'=>lang('client_is_not_exist')];
        }

        $this->startTrans();

        $info = json_encode([
            'url' => $param['downstream_url'],
            'token' => $param['token'],
            'api' => request()->api_id??0,
            'type' => 'finance',
        ]);

        // {"url":"http:\/\/w2.test.idcsmart.com","token":"9707d4ba774c8553c2a4842797c922e2","api":100232,"type":"finance"}
        try {
            $host->save([
                'downstream_info' => $info,
                'downstream_host_id' => $param['downstream_host_id'],
            ]);

            $this->commit();
        }catch (\Exception $e){
            $this->rollback();
            return ['status'=>400,'msg'=>$e->getMessage()];
        }

        return ['status'=>200,'msg'=>lang('success_message')];
    }

    /**
     * @时间 2024-12-09
     * @title 获取产品具体信息
     * @desc  获取产品具体信息,目前用于续费开关
     * @author hh
     * @version v1
     * @param   int id - 产品ID
     * @return  int id - 产品ID
     * @return  string name - 产品标识
     * @return  string renew_amount - 续费金额
     * @return  string billing_cycle_name - 模块计费周期名称
     * @return  int due_time - 到期时间
     * @return  int ip_num - IP数量
     * @return  string dedicate_ip - 主IP
     * @return  string assign_ip - 附加IP(英文逗号分隔)
     * @return  string country - 国家
     * @return  string country_code - 国家代码
     * @return  int country_id - 国家ID
     * @return  string city - 城市
     * @return  string area - 区域
     */
    public function hostSpecificInfo($id)
    {
        $clientId = get_client_id();

        // 插件用户限制,限制可查看的用户数据
        $res = hook('plugin_check_client_limit', ['client_id' => $clientId ]);
        foreach ($res as $value){
            if (isset($value['status']) && $value['status']==400){
                return [];
            }
        }

        $language = get_client_lang();
        $countryField = ['en-us'=> 'nicename'];
        $countryName = $countryField[ $language ] ?? 'name_zh';

        $host = $this
                ->alias('h')
                ->field('h.id,h.name,h.renew_amount,h.billing_cycle_name,h.due_time,hi.ip_num,hi.dedicate_ip,hi.assign_ip,c.'.$countryName.' country,c.iso country_code,ha.country_id,ha.city,ha.area')
                ->leftJoin('host_ip hi', 'h.id=hi.host_id')
                ->leftJoin('host_addition ha', 'h.id=ha.host_id')
                ->leftJoin('country c', 'ha.country_id=c.id')
                ->where('h.id', $id)
                ->where('h.client_id', $clientId)
                ->find();

        if(!empty($host)){
            $host['ip_num'] = $host['ip_num'] ?? 0;
            $host['dedicate_ip'] = $host['dedicate_ip'] ?? '';
            $host['assign_ip'] = $host['assign_ip'] ?? '';
            $host['country'] = $host['country'] ?? '';
            $host['country_code'] = $host['country_code'] ?? '';
            $host['country_id'] = $host['country_id'] ?? 0;
            $host['city'] = $host['city'] ?? '';
            $host['area'] = $host['area'] ?? '';

            $multiLanguage = hook_one('multi_language', [
                'replace' => [
                    'billing_cycle_name' => $host['billing_cycle_name'],
                    'city' => $host['city'],
                    'area' => $host['area'],
                ],
            ]);

            $host['billing_cycle_name'] = $multiLanguage['billing_cycle_name'] ?? $host['billing_cycle_name'];
            $host['city'] = $multiLanguage['city'] ?? $host['city'];
            $host['area'] = $multiLanguage['area'] ?? $host['area'];
        }

        return $host ?? [];
    }

    /**
     * @时间 2024-12-09
     * @title 记录失败动作
     * @desc  记录失败动作
     * @author hh
     * @version v1
     * @param   int param.host_id - 产品ID require
     * @param   string param.action - 动作(create=开通,suspend=暂停,terminate=删除,renew=续费) require
     * @param   string param.msg - 失败原因 require
     * @return  bool
     */
    public function failedActionHandle($param)
    {
        $param['action'] = $param['action'] ?? '';
        if(!in_array($param['action'], ['create','suspend','terminate','renew'])){
            return false;
        }
        $host = $this->find($param['host_id']);
        if(empty($host) || $host['is_delete'] == 1){
            return false;
        }
        // 排除特殊情况
        if($param['action'] == 'create' && $param['msg'] == '产品已开通'){
            return false;
        }
        if($param['action'] != $host['failed_action']){
            $update = [
                'failed_action'             => $param['action'],
                'failed_action_times'       => in_array($param['action'], ['suspend','terminate']) ? ($host['failed_action_times'] + 1) : 1,
                // 'failed_action_need_handle' => $param['action'] == 'create' ? 1 : 0,
                'failed_action_reason'      => $param['msg'] ?? '',
            ];
        }else{
            $update = [
                'failed_action_times'       => $host['failed_action_times'] + 1,
                // 'failed_action_need_handle' => $host['failed_action_times'] >= 2 ? 1 : 0,
                'failed_action_reason'      => $param['msg'] ?? '',
            ];
        }
        if($param['action'] == 'create' || $param['action'] == 'renew'){
            $update['failed_action_need_handle'] = 1;
        }else{
            $update['failed_action_need_handle'] = $host['failed_action_times'] >= 2 ? 1 : 0;
        }
        if($update['failed_action_need_handle'] == 1){
            $update['failed_action_trigger_time'] = time();
        }
        // 防止溢出
        $update['failed_action_times'] = min($update['failed_action_times'], 6);

        $a = $this->where('id', $host['id'])->update($update);
        if($update['failed_action_need_handle'] == 1){
            system_notice([
                'name'                  => 'host_failed_action',
                'email_description'     => lang('host_failed_action_send_mail'),
                'task_data' => [
                    'template_param' => [
                        'wait_handle_host_num' => $this->failedActionCount(),
                    ],
                ],
            ]);
        }
        return true;
    }

    /**
     * @时间 2024-12-10
     * @title 手动处理产品列表
     * @desc  手动处理产品列表
     * @author hh
     * @version v1
     * @param   int param.page - 页数
     * @param   int param.limit - 每页条数
     * @param   string param.action - 搜索:失败动作(create=开通失败,suspend=暂停失败,terminate=删除失败)
     * @param   string param.keywords - 关键字:产品ID,商品名称,产品标识,IP地址
     * @param   string param.orderby failed_action_trigger_time 排序(id,due_time,failed_action_trigger_time)
     * @return  int list[].id - 产品ID
     * @return  string list[].name - 产品标识
     * @return  int list[].product_id - 商品ID
     * @return  string list[].product_name - 商品名称
     * @return  int list[].client_id - 用户ID
     * @return  string list[].status - 产品状态(Unpaid=未付款,Pending=开通中,Active=已开通,Suspended=已暂停,Deleted=已删除)
     * @return  string list[].failed_action - 失败动作(create=开通失败,suspend=暂停失败,terminate=删除失败)
     * @return  string list[].failed_action_reason - 失败原因
     * @return  string list[].renew_amount - 续费金额
     * @return  string list[].billing_cycle - 计费方式(免费free，一次onetime，周期先付recurring_prepayment,周期后付recurring_postpaid)
     * @return  string list[].billing_cycle_name - 模块计费周期名称
     * @return  int list[].due_time - 到期时间
     * @return  string list[].client_name - 用户名
     * @return  string list[].email - 邮箱
     * @return  int list[].phone_code - 区号
     * @return  string list[].phone - 手机号
     * @return  int list[].failed_action_trigger_time - 触发时间
     * @return  int count - 总条数
     * @return  int dusing_count - 使用中产品数量
     * @return  int expiring_count - 即将到期产品数量
     * @return  int overdue_count - 已逾期产品数量
     * @return  int deleted_count - 已删除产品数量
     * @return  int all_count - 全部产品数量
     * @return  int failed_action_count - 手动处理产品数量
     */
    public function failedActionHostList($param)
    {
        if(empty($param['orderby']) || !in_array($param['orderby'], ['id','due_time'])){
            $param['orderby'] = 'failed_action_trigger_time';
        }
        $param['orderby'] = 'h.'.$param['orderby'];

        $where = function($query) use ($param){

            $query->where('h.failed_action_need_handle', 1);
            $query->where('h.is_delete', 0);
            $query->where('h.is_sub', 0);

            if(!empty($param['action'])){
                $query->where('h.failed_action', $param['action']);
            }
            if(isset($param['keywords']) && $param['keywords'] !== ''){
                $query->where('h.id|h.name|p.name|hi.dedicate_ip|hi.assign_ip', 'LIKE', '%'.$param['keywords'].'%');
            }
        };

        $list = $this
                ->alias('h')
                ->field('h.id,h.name,h.product_id,p.name product_name,h.client_id,h.status,h.failed_action,h.failed_action_reason,h.renew_amount,h.billing_cycle,h.billing_cycle_name,h.due_time,c.username client_name,c.email,c.phone_code,c.phone,c.company,hi.ip_num,hi.dedicate_ip,hi.assign_ip,h.failed_action_trigger_time')
                ->leftJoin('product p', 'h.product_id=p.id')
                ->leftJoin('client c', 'h.client_id=c.id')
                ->leftJoin('host_ip hi', 'h.id=hi.host_id')
                ->where($where)
                ->page((int)$param['page'], (int)$param['limit'])
                ->order($param['orderby'], $param['sort'])
                ->select()
                ->toArray();

        $count = $this
                ->alias('h')
                ->leftJoin('product p', 'h.product_id=p.id')
                ->leftJoin('client c', 'h.client_id=c.id')
                ->leftJoin('host_ip hi', 'h.id=hi.host_id')
                ->where($where)
                ->count();

        foreach($list as $k=>$v){
            $list[$k]['product_name'] = $v['product_name'] ?? '';
            $list[$k]['client_name'] = $v['client_name'] ?? '';
            $list[$k]['email'] = $v['email'] ?? '';
            $list[$k]['phone_code'] = $v['phone_code'] ?? 0;
            $list[$k]['phone'] = $v['phone_code'] ?? '';
            $list[$k]['company'] = $v['company'] ?? '';
            $list[$k]['ip_num'] = $v['ip_num'] ?? 0;
            $list[$k]['dedicate_ip'] = $v['dedicate_ip'] ?? '';
            $list[$k]['assign_ip'] = $v['assign_ip'] ?? '';
        }

        $usingCount = $this->usingCount();
        $expiringCount = $this->expiringCount();
        $overdueCount = $this->overdueCount();
        $deletedCount = $this->deletedCount();
        $allCount = $this->allCount();
        $failedActionCount = $this->failedActionCount();

        return ['list'=>$list, 'count'=>$count, 'using_count'=>$usingCount, 'expiring_count'=>$expiringCount, 'overdue_count'=>$overdueCount, 'deleted_count'=>$deletedCount, 'all_count'=>$allCount, 'failed_action_count'=>$failedActionCount ];
    }

    /**
     * @时间 2024-12-10
     * @title 获取即将到期数量
     * @desc  获取即将到期数量
     * @author hh
     * @version v1
     * @return  int
     */
    public function expiringCount($where = ''){
        $where = function($query) use ($where){
            if(!empty($where)){
                $query->where($where);
            }
            $time = time();
            $renewalFirstDay = configuration('cron_due_renewal_first_day');
            $timeRenewalFirst = strtotime(date('Y-m-d 23:59:59', $time+$renewalFirstDay*24*3600));
            $query->whereIn('h.status', ['Pending', 'Active'])->where('h.due_time', '>', $time)->where('h.due_time', '<=', $timeRenewalFirst)->where('billing_cycle', '<>', 'free')->where('billing_cycle', '<>', 'onetime');
            $query->where('h.is_delete', 0);
        };

        $expiringCount = $this
                        ->alias('h')
                        ->field('h.id')
                        ->leftjoin('product p', 'p.id=h.product_id')
                        ->leftjoin('client c', 'c.id=h.client_id')
                        ->leftJoin('server s','s.id=h.server_id')
                        ->leftJoin('upstream_product up', 'p.id=up.product_id')
                        ->where($where)
                        ->count();
        return $expiringCount;
    }

    /**
     * @时间 2025-01-20
     * @title 获取使用中数量
     * @desc  获取使用中数量
     * @author theworld
     * @version v1
     * @return  int
     */
    public function usingCount($where = ''){
        $where = function($query) use ($where){
            if(!empty($where)){
                $query->where($where);
            }
            $query->whereIn('h.status', ['Pending', 'Active']);
            $query->where('h.is_delete', 0);
        };

        $usingCount = $this
                        ->alias('h')
                        ->field('h.id')
                        ->leftjoin('product p', 'p.id=h.product_id')
                        ->leftjoin('client c', 'c.id=h.client_id')
                        ->leftJoin('server s','s.id=h.server_id')
                        ->leftJoin('upstream_product up', 'p.id=up.product_id')
                        ->where($where)
                        ->count();
        return $usingCount;
    }

    /**
     * @时间 2025-01-20
     * @title 获取全部数量
     * @desc  获取全部数量
     * @author theworld
     * @version v1
     * @return  int
     */
    public function allCount($where = '')
    {
        $where = function($query) use ($where){
            if(!empty($where)){
                $query->where($where);
            }
            $query->where('h.is_delete', 0);
        };

        $allCount = $this
                        ->alias('h')
                        ->field('h.id')
                        ->leftjoin('product p', 'p.id=h.product_id')
                        ->leftjoin('client c', 'c.id=h.client_id')
                        ->leftJoin('server s','s.id=h.server_id')
                        ->leftJoin('upstream_product up', 'p.id=up.product_id')
                        ->where($where)
                        ->count();
        return $allCount;
    }

    /**
     * @时间 2025-01-20
     * @title 获取已逾期数量
     * @desc  获取已逾期数量
     * @author theworld
     * @version v1
     * @return  int
     */
    public function overdueCount($where = '')
    {
        $where = function($query) use ($where){
            if(!empty($where)){
                $query->where($where);
            }
            $time = time();
            $query->whereIn('h.status', ['Pending', 'Active', 'Suspended', 'Failed'])->where('h.due_time', '<=', $time)->where('h.billing_cycle', '<>', 'free')->where('h.billing_cycle', '<>', 'onetime');
            $query->where('h.is_delete', 0);
        };

        $overdueCount = $this
                        ->alias('h')
                        ->field('h.id')
                        ->leftjoin('product p', 'p.id=h.product_id')
                        ->leftjoin('client c', 'c.id=h.client_id')
                        ->leftJoin('server s','s.id=h.server_id')
                        ->leftJoin('upstream_product up', 'p.id=up.product_id')
                        ->where($where)
                        ->count();
        return $overdueCount;
    }

    /**
     * @时间 2025-01-20
     * @title 获取已删除数量
     * @desc  获取已删除数量
     * @author theworld
     * @version v1
     * @return  int
     */
    public function deletedCount($where = '')
    {
        $where = function($query) use ($where){
            if(!empty($where)){
                $query->where($where);
            }
            $query->where('h.status', 'Deleted');
            $query->where('h.is_delete', 0);
        };

        $deletedCount = $this
                        ->alias('h')
                        ->field('h.id')
                        ->leftjoin('product p', 'p.id=h.product_id')
                        ->leftjoin('client c', 'c.id=h.client_id')
                        ->leftJoin('server s','s.id=h.server_id')
                        ->leftJoin('upstream_product up', 'p.id=up.product_id')
                        ->where($where)
                        ->count();
        return $deletedCount;
    }

    /**
     * @时间 2024-12-10
     * @title 获取手动处理数量
     * @desc  获取手动处理数量
     * @author hh
     * @version v1
     * @return  int
     */
    public function failedActionCount()
    {
        $count = $this->where('failed_action_need_handle', 1)->where('is_sub',0)->where('is_delete', 0)->count();
        return $count;
    }

    /**
     * @时间 2024-12-10
     * @title 标记已处理
     * @desc  标记已处理
     * @author hh
     * @version v1
     * @param   int id - 产品ID require
     * @return  int status - 状态码(200=成功,400=失败)
     * @return  string msg - 信息
     */
    public function failedActionMarkProcessed($id)
    {
        $host = $this->find($id);
        if(empty($host) || $host['is_delete'] ){
            return ['status'=>400, 'msg'=>lang('host_is_not_exist') ];
        }
        if($host['failed_action_need_handle'] == 0){
            return ['status'=>200, 'msg'=>lang('success_message') ];
        }

        $update = $this
                ->where('id', $id)
                ->where('failed_action_need_handle', 1)
                ->update([
                    'failed_action'                 => '',
                    'failed_action_times'           => 0,
                    'failed_action_need_handle'     => 0,
                    'failed_action_reason'          => '',
                    'failed_action_trigger_time'    => 0,
                ]);

        if($update){
            $description = lang('log_host_failed_action_mark_processed', [
                '{host}'    => 'host#'.$host->id.'#'.$host['name'].'#',
                '{action}'  => lang('host_failed_action_' . $host['failed_action']),
            ]);
            active_log($description, 'host', $host->id);
        }

        return ['status'=>200, 'msg'=>lang('success_message') ];
    }

    /**
     * @时间 2025-01-21
     * @title 批量同步
     * @desc  批量同步
     * @author hh
     * @version v1
     * @param   array param.product_id - 商品ID require
     * @param   array param.host_status - 产品状态(Active已开通Suspended已暂停) require
     * @return  int status - 状态码(200=成功,400=失败)
     * @return  string msg - 信息
     */
    public function batchSyncAccount($param)
    {
        // 检查任务
        $TaskModel = new TaskModel();
        $exist = $TaskModel
                ->where('type', 'batch_host_sync')
                ->whereIn('status', ['Wait','Exec'])
                ->find();
        if(!empty($exist)){
            return ['status'=>400, 'msg'=>lang('host_batch_sync_task_not_complete') ];
        }

        $host = $this
                ->whereIn('product_id', $param['product_id'])
                ->whereIn('status', $param['host_status'])
                ->where('is_delete', 0)
                ->column('id');
        if(empty($host)){
            return ['status'=>400, 'msg'=>lang('host_is_not_eligible') ];
        }

        // 分开添加任务
        $limit = 50;
        $maxPage = ceil(count($host)/$limit);
        for($page = 1; $page <= $maxPage; $page++){
            add_task([
                'type'          => 'batch_host_sync',
                'description'   => lang('host_batch_sync_account'),
                'task_data'     => [
                    'host_id'   => array_slice($host, ($page-1)*$limit, $limit),
                ],
            ]);
        }

        $result = [
            'status' => 200,
            'msg'    => lang('host_batch_sync_add_task_success'),
        ];

        return $result;
    }
}
