<?php 
namespace widget;

use app\common\lib\Widget;
use app\common\model\TransactionModel;
use app\admin\model\PluginModel;

class TodaySale extends Widget
{
	protected $title = '今日销售额';

    protected $weight = 50;
    
    protected $columns = 1;

    protected $language = [
        'zh-cn' => [
            'title' => '今日销售额',
        ],
        'en-us' => [
            'title' => 'Sales today',
        ],
        'zh-hk' => [
            'title' => '今日銷售額',
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
        
    	$data = [];
    	$data['today_sale_amount'] = amount_format(TransactionModel::where('create_time', '>=', strtotime(date("Y-m-d")))->whereNotIn('id', $withdrawtransactionId)->sum('amount'));
    	return $data;
    }

    public function output(){
    	$data = $this->getData();
        $data['today_sale_amount'] = number_format($data['today_sale_amount'], 2);
        $currencySuffix = configuration('currency_suffix');
        if(!empty($currencySuffix)){
            $currencySuffix = '（'.$currencySuffix.'）';
        }
        $title = $this->lang('title');

    	return <<<HTML
<div class="top-item"><div class="item-nums"><span class="num">{$data['today_sale_amount']}</span></div> <div class="item-title">
          {$title}{$currencySuffix}
        </div></div>
HTML;
    }



}


