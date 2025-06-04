<?php
namespace app\admin\validate;

use think\Validate;

/**
 * 订单管理验证
 */
class OrderValidate extends Validate
{
	protected $rule = [
		'id' 		    				=> 'require|integer|gt:0',
		'type' 							=> 'require|in:new,renew,upgrade,artificial',
		'amount' 						=> 'requireIf:type,artificial|float|gt:0',
        'client_id'     				=> 'require|integer|gt:0',
        'description'                   => 'requireIf:type,artificial|max:1000',
        'delete_host'                   => 'require|in:0,1',
        'products'                      => 'requireIf:type,new|checkProducts:thinkphp',
        'host_id'                       => 'requireIf:type,upgrade|integer|gt:0',
        'product'                       => 'requireIf:type,upgrade|checkProduct:thinkphp',
        'voucher'                       => 'require|array|checkVoucher:thinkphp',
        'pass'                          => 'require|in:0,1',
        'review_fail_reason'            => 'requireIf:pass,0|max:255',
        'transaction_number'            => 'requireIf:pass,1|alphaNum|max:255',
    ];

    protected $message  =   [
    	'id.require'     				=> 'id_error',
    	'id.integer'     				=> 'id_error',
        'id.gt'                         => 'id_error',
    	'type.require'        			=> 'please_select_order_type',
    	'type.in'        				=> 'order_type_error',
        'amount.requireIf'              => 'please_enter_amount',
    	'amount.require'    			=> 'please_enter_amount',
        'amount.float'    				=> 'amount_formatted_incorrectly',
        'amount.gt' 					=> 'amount_formatted_incorrectly',
        'client_id.require'     		=> 'please_select_client',
    	'client_id.integer'     		=> 'client_id_error',
        'client_id.gt'                  => 'client_id_error',
        'description.requireIf'         => 'please_enter_description',
        'description.require'           => 'please_enter_description',
        'description.max'               => 'description_cannot_exceed_1000_chars',
        'delete_host.require'           => 'please_select_order_delete_host',
        'delete_host.in'                => 'param_error',
        'products.requireIf'            => 'please_select_product',
        'products.checkProducts'        => 'param_error',
        'host_id.require'               => 'please_select_host',
        'host_id.requireIf'             => 'please_select_host',
        'host_id.integer'               => 'host_id_error',
        'host_id.gt'                    => 'host_id_error',
        'product.require'               => 'please_select_product',
        'product.requireIf'             => 'please_select_product',
        'product.checkProduct'          => 'param_error',
        'voucher.require'               => 'please_upload_order_voucher',
        'voucher.array'                 => 'please_upload_order_voucher',
        'pass.require'                  => 'order_pass_param_require',
        'pass.in'                       => 'order_pass_param_require',
        'review_fail_reason.requireIf'  => 'order_review_fail_reason_require',
        'review_fail_reason.max'        => 'order_review_fail_reason_max',
        'transaction_number.requireIf'  => 'transaction_number_require',
        'transaction_number.alphaNum'   => 'transaction_number_formatted_incorrectly',
        'transaction_number.max'        => 'transaction_number_length_error',
    ];

    protected $scene = [
        'create' => ['type', 'amount', 'client_id', 'description', 'products', 'host_id', 'product'],
        'delete' => ['id', 'delete_host'],
        'paid' => ['id', 'use_credit'],
        'refund' => ['id', 'amount'],
        'apply' => ['id', 'amount'],
        'remove' => ['id', 'amount'],
        'upload_voucher' => ['id','voucher'],
        'review' => ['id','pass','review_fail_reason','transaction_number'],
    ];

    # 修改金额验证
    public function sceneAmount()
    {
        return $this->only(['id', 'amount', 'description'])
            ->remove('amount', 'gt|requireIf')
            ->append('amount', 'require')
            ->remove('description', 'requireIf')
            ->append('description', 'require');
    }

    # 获取升降级订单金额
    public function sceneUpgrade()
    {
        return $this->only(['client_id', 'host_id', 'product'])
            ->remove('host_id', 'requireIf')
            ->append('host_id', 'require')
            ->remove('product', 'requireIf')
            ->append('product', 'require');
    }

    public function checkProducts($products)
    {
        if(is_array($products)){
            foreach ($products as $key => $value) {
                if(!isset($value['product_id']) || !is_integer($value['product_id']) || $value['product_id']<=0){
                    return false;
                }
                if(isset($value['config_options']) && !is_array($value['config_options'])){
                    return false;
                }
                if(!isset($value['qty']) || !is_integer($value['qty']) || $value['qty']<=0){
                    return false;
                }
                if(isset($value['price']) && (!is_numeric($value['price']) || $value['price']<0)){
                    return false;
                }
            }
        }else{
            return false;
        }
        return true;
    }

    public function checkProduct($product)
    {
        if(is_array($product)){
            if(!isset($product['product_id']) || !is_integer($product['product_id']) || $product['product_id']<=0){
                return false;
            }
            if(isset($product['config_options']) && !is_array($product['config_options'])){
                return false;
            }
            if(isset($product['price']) && (!is_numeric($product['price']) || $product['price']<0)){
                return false;
            }
        }else{
            return false;
        }
        return true;
    }

    protected function checkVoucher($value)
    {
        $allowSuffix = ['jpg','jpeg','png','pdf'];
        if(count($value) > 10){
            return 'order_voucher_max';
        }
        foreach($value as $file){
            $file = explode('.', $file);
            $suffix = end($file);
            if(!in_array($suffix, $allowSuffix)){
                return 'order_voucher_format_error';
            }
        }
        return true;
    }

}