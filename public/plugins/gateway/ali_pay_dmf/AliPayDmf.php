<?php
namespace gateway\ali_pay_dmf;

use app\common\lib\Plugin;
use gateway\ali_pay_dmf\aop\AopClient;
use gateway\ali_pay_dmf\f2fpay\service\AlipayTradeService;
use gateway\ali_pay_dmf\f2fpay\model\builder\AlipayTradePrecreateContentBuilder;
use gateway\ali_pay_dmf\aop\request\AlipayTradeRefundRequest;
use think\facade\Db;

/**
 * @desc 支付宝当面付支付插件主文件
 * @author wyh
 * @version 1.0
 * @time 2022-05-27
 */
class AliPayDmf extends Plugin
{
    // 插件基础信息
    public $info = array(
        'name'        => 'AliPayDmf', // 必填 插件标识(唯一)
        'title'       => '支付宝当面付插件', // 必填 插件显示名称
        'description' => '支付宝当面付插件', // 必填 插件功能描述
        'author'      => '智简魔方', // 必填 插件作者
        'version'     => '2.3.0',  // 必填 插件版本
        'help_url'    => '', // 选填 申请链接
        'author_url'  => '', // 选填 作者链接
        'url'         => '', // 选填 图标地址(可以自定义支付图片地址)
        'description_url'         => 'http://doc.idcsmart.com/%E6%94%AF%E4%BB%98%E5%AE%9D%E5%BD%93%E9%9D%A2%E4%BB%98.html', // 选填 图标地址(可以自定义支付图片地址)
    );

    // 临时订单生成规则,1:毫秒时间戳+8位随机数(21-22位长度订单号,默认规则),2:时间戳+8位随机数(18位长度订单号),3:10位随机数(10位长度订单号)
    public $orderRule=1;

    // 插件安装
    public function install()
    {
        return true;//安装成功返回true，失败false
    }

    // 插件卸载
    public function uninstall()
    {
        return true;//卸载成功返回true，失败false
    }

    public function AliPayDmfHandle($param)
    {
        // (必填) 商户网站订单系统中唯一订单号，64个字符以内，只能包含字母、数字、下划线，
        $outTradeNo = $param['out_trade_no'];

        // (必填) 订单标题，粗略描述用户的支付目的。如“xxx品牌xxx门店当面付扫码消费”
        $subject = isset($param['product'][0])?$param['product'][0]:('订单'.$param['out_trade_no']);
        $body = $subject;

        // (必填) 订单总金额，单位为元，不能超过1亿元
        $totalAmount = $param['finance']['total'];

        // 支付超时，线下扫码交易定义为5分钟
        $timeExpress = "5m";

        // 创建请求builder，设置请求参数
        try{
            $qrPayRequestBuilder = new AlipayTradePrecreateContentBuilder();
            $qrPayRequestBuilder->setOutTradeNo($outTradeNo);
            $qrPayRequestBuilder->setTotalAmount($totalAmount);
            $qrPayRequestBuilder->setTimeExpress($timeExpress);
            $qrPayRequestBuilder->setSubject($subject);
            $qrPayRequestBuilder->setBody($body);
            // 调用qrPay方法获取当面付应答
            $qrPay = new AlipayTradeService($this->Config());
            $qrPayResult = $qrPay->qrPay($qrPayRequestBuilder);
        }catch (\Exception $e){
            return ['status'=>400,'msg'=>'配置错误'];
        }

        require_once 'phpqrcode/phpqrcode.php';



        $response = \QRcode::png($qrPayResult->getResponse()->qr_code,false,0,4,0,false); # 这里需要修改扩展库的代码

        $base64 = 'data:png;base64,' . base64_encode($response->getData());

        return '<img src="'. $base64 .'" alt="" width="120" height="120">';
        //return '<img src="'. $base64 .'" alt="" width="250" height="250">';

        # 方式二、生成图片
        /*$path = WEB_ROOT . "plugins/gateway/ali_pay_dmf/upload/{$param['out_trade_no']}.png";
        $response = \QRcode::png($qrPayResult->getResponse()->qr_code,$path,0,4,5,true);
        $outPath = request()->domain() . "/plugins/gateway/ali_pay_dmf/upload/{$param['out_trade_no']}.png";
        return '<img src="'. $outPath .'" alt="" width="250" height="250">';*/
    }

    // 处理退款
    public function AliPayDmfHandleRefund($param)
    {
        $config = $this->config();

        $aop = new AopClient();
        $aop->gatewayUrl = $config['gatewayUrl'];
        $aop->appId = $config['app_id'];
        $aop->rsaPrivateKey = $config['merchant_private_key'];
        $aop->alipayrsaPublicKey=$config['alipay_public_key'];
        $aop->apiVersion = '1.0';
        $aop->signType = $config['sign_type'];
        $aop->postCharset=$config['charset'];
        $aop->format='json';
        $object = new \stdClass();

        // 支付宝交易流水号
        $object->trade_no = $param['transaction_number'];
        // 退款金额
        $object->refund_amount = $param['amount'];
        // 退款请求号：标识一次退款请求，需要保证在交易号下唯一，如需部分退款，则此参数必传
        if (isset($param['out_request_no']) && !empty($param['out_request_no'])){
            $object->out_request_no = $param['out_request_no'];
        }
        //// 返回参数选项，按需传入
//$queryOptions =[
//   'refund_detail_item_list'
//];
//$object->query_options = $queryOptions;
        $json = json_encode($object);
        $request = new AlipayTradeRefundRequest();
        $request->setBizContent($json);

        $result = $aop->execute($request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            // 退款交易流水号
            return ['status'=>200,'msg'=>$result->$responseNode->msg,'data'=>['trade_no'=>$result->$responseNode->out_trade_no]];
        } else {
            return ['status'=>400,'msg'=>$result->$responseNode->msg??"接口错误"];
        }
    }

    // 获取配置
    public function Config()
    {
        $config = Db::name('plugin')->where('name', $this->info['name'])->value('config');
        if (!empty($config) && $config != "null") {
            $config = json_decode($config, true);
        } else {
            $config = [];
        }
        $con = require dirname(__DIR__).'/ali_pay_dmf/config/config.php';
        $config = array_merge($con,$config);

        return $config;
    }
}