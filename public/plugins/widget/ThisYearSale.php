<?php
namespace widget;

use app\common\lib\Widget;
use app\common\model\TransactionModel;
use app\admin\model\PluginModel;

class ThisYearSale extends Widget
{
    protected $weight = 10;

    protected $columns = 1;

    protected $language = [
        'zh-cn' => [
            'title' => '本年销售额',
            'yoy' => '同比',
        ],
        'en-us' => [
            'title' => 'Sales this year',
            'yoy' => 'year-on-year',
        ],
        'zh-hk' => [
            'title' => '本年銷售額',
            'yoy' => '同比',
        ],
    ];

    public function __construct(){
        $this->title = $this->lang('title');
    }

    public function getData()
    {
        $PluginModel = new PluginModel();
        $addons = $PluginModel->plugins('addon');
        $addons = array_column($addons['list'], 'name');
        if(in_array('IdcsmartWithdraw', $addons)){
            $IdcsmartWithdrawModel = new \addon\idcsmart_withdraw\model\IdcsmartWithdrawModel();
            $withdrawtransactionId = $IdcsmartWithdrawModel->where('transaction_id', '>', 0)->column('transaction_id');
        }else{
            $withdrawtransactionId = [];
        }

        $start = mktime(0,0,0,1,1,date("Y"));
        //$end = strtotime(date("Y-m-d"));
        $end = time();
        $datetime = $end-strtotime(date("Y-m-d"));

        $thisYearAmount = TransactionModel::where('create_time', '>=', $start)->where('create_time', '<', $end)->whereNotIn('id', $withdrawtransactionId)->sum('amount');

        # 获取去年销售额，截止到去年的昨天同日期
        $start = mktime(0,0,0,1,1,date("Y")-1);
        if(date("m")==2){
            $t = date("t", $start);
            if(date("d")>$t){
                $end = strtotime(date((date("Y")-1)."-m-".$t));
            }else{
                $end = strtotime(date((date("Y")-1)."-m-d"));
            }
        }else{
            $end = strtotime(date((date("Y")-1)."-m-d"));
        }
        $prevYearAmount = TransactionModel::where('create_time', '>=', $start)->where('create_time', '<', $end+$datetime)->whereNotIn('id', $withdrawtransactionId)->sum('amount');

        $thisYearAmountPercent = $prevYearAmount>0 ? bcmul(($thisYearAmount-$prevYearAmount)/$prevYearAmount, 100, 1) : 100;

        $data = [
            'this_year_amount'         => amount_format($thisYearAmount),
            'this_year_amount_percent' => $thisYearAmountPercent
        ];
    	return $data;
    }

    public function output(){
    	$data = $this->getData();
        $data['this_year_amount'] = number_format($data['this_year_amount'], 2);
        $currencySuffix = configuration('currency_suffix');
        if(!empty($currencySuffix)){
            $currencySuffix = '（'.$currencySuffix.'）';
        }
        $title = $this->lang('title');
        $yoy = $this->lang('yoy');
        if($data['this_year_amount_percent'] >= 0){
            return <<<HTML
            <div class="top-item increase-bg"><div class="item-nums"><span class="num">{$data['this_year_amount']}</span> <span class="trend up-green-text">{$yoy} ↑ {$data['this_year_amount_percent']}%</span></div> <div class="item-title">{$title}{$currencySuffix}</div></div>
HTML;
        }else{
            return <<<HTML
            <div class="top-item decline-bg"><div class="item-nums"><span class="num">{$data['this_year_amount']}</span> <span class="trend down-red-text">{$yoy} ↓ {$data['this_year_amount_percent']}%</span></div> <div class="item-title">{$title}{$currencySuffix}</div></div>
HTML;
        }

    }



}
