<?php
/**
 * 会员订单业务层
 * @author dingxuehuan
 */
class User_order_service {

	private $CI;

	function __construct() {
		$this->CI = & get_instance ();
        $this->CI->load->model('user_model');
        $this->CI->load->model('product_model');
        $this->CI->load->model('user_order_model');
        $this->CI->load->model('shop_order_model');
        $this->CI->load->model('shop_model');
        $this->CI->load->model('capital_flow_model');
	}

    /**
     * 小程序统一下单
     */
    function unifiedorder_user($token, $product_id, $form_id){
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($product_id)){
            return output(1001,'参数错误');
        }

        $result = $this->create_user_order($product_id, $user_id, $form_id);
        $user_order_id = 0;
        $openid = '';
        if($result['code'] == 0){
            $user_order_id = $result['data']['user_order_id'];
            $openid = $result['data']['openid'];
        }else{
            return $result;
        }
        if(empty($user_order_id)){
            return output(1001,'该支付订单不存在');
        }

        $user_order = $this->CI->user_order_model->get_by_id($user_order_id);
        if(empty($user_order)){
            return output(1001,'该支付订单不存在');
        }elseif(!empty($user_order) && $user_order->status != 1){
            return output(1001,'该支付订单无须支付');
        }

        include_once APPPATH.'libraries/wechat_pay/src/wxh5pay/Order.php';
        include_once APPPATH.'libraries/wechat_pay/src/wxh5pay/lib/Core.php';
        include_once APPPATH.'libraries/wechat_pay/src/wxh5pay/lib/Sign.php';

        $order = new \wxh5pay\Order();
        $order->setParams([
            'appid' => WECHAT_APPID,
            'body' => '金芒杂货铺-店铺续费',
            'out_trade_no' => $user_order->order_no,
            'total_fee' => $user_order->price*100,
            'trade_type' => 'JSAPI',
            'device_info' => DEVICE_INFO,
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            'notify_url' => NOTIFY_URL,
            'openid' => $openid
        ]);
        $res = $order->unifiedorder();
        if(!$res){
            return output(1001,$order->errMsg);
        }else{
            //更新prepay_id
            $where = $user_order_data = array();
            $where['id'] = $user_order_id;
            $user_order_data['prepay_id'] = $res['prepay_id'];
            $user_order_data['update_time'] = date("Y-m-d H:i:s");
            $this->CI->user_order_model->query_update($where, $user_order_data);

            $parameters = array(
                'appId' => WECHAT_APPID, //小程序ID
                'timeStamp' => '' . time() . '', //时间戳
                'nonceStr' => \wxh5pay\lib\Core::genRandomString(), //随机串
                'package' => 'prepay_id=' . $res['prepay_id'], //数据包
                'signType' => 'MD5'//签名方式
            );
            //签名
            $parameters['paySign'] = \wxh5pay\lib\Sign::makeSign($parameters, PAY_APIKEY);
            return output(0,'成功',['parameters'=>$parameters]);
        }
    }

    /**
     * 创建支付订单
     */
    public function create_user_order($product_id, $user_id, $form_id='') {
        $user = $this->CI->user_model->get_by_id($user_id);

        $product = $this->CI->product_model->get_by_id($product_id);
        if(empty($product)){
            return output(1001,'产品不存在');
        }

        $data = [
            'user_id' => $user_id,
            'order_no' => create_order_code('U'),
            'product_id' => $product_id,
            'price' => $product->price,
            'status' => 1,
            'create_time' => date('Y-m-d H:i:s')
        ];
        $user_order_id = $this->CI->user_order_model->query_insert($data);
        if($user_order_id > 0){
            return output(0,'成功',['user_order_id'=>$user_order_id,'openid'=>$user->openid]);
        }else{
            return output(1,'失败');
        }
    }

    /**
     * 微信支付回调
     */
    function pay(){
        include_once APPPATH.'libraries/wechat_pay/src/wxh5pay/Order.php';
        include_once APPPATH.'libraries/wechat_pay/src/wxh5pay/lib/Core.php';
        include_once APPPATH.'libraries/wechat_pay/src/wxh5pay/lib/Sign.php';

        $xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents('php://input');
        $resArray = \wxh5pay\lib\Core::xmlToArray($xml);

        //记录回调接口返回数据
        if (!is_dir(APPPATH . '/logs/wxpay'))
            mkdir(APPPATH . '/logs/wxpay', 777);
        $fp = fopen(APPPATH . "/logs/wxpay/wxpay_h5_log_" . date('Ymd') . ".txt", "a");
        fwrite($fp, "res return" . date('Y:m:d H:i:s') . "\r\n---" . json_encode($resArray). "---\r\n");
        fclose($fp);

        if($resArray['return_code'] == 'SUCCESS'){
            //验证签名,判断是否是微信通知
            $sign = $resArray['sign'];
            unset($resArray['sign']);
            $wxSign = \wxh5pay\lib\Sign::makeSign($resArray,PAY_APIKEY);
            if($sign == $wxSign){
                //商户自己的业务
                if(strstr($resArray['out_trade_no'], 'U')){
                    $result = $this->user_order_suc($resArray['transaction_id'], $resArray['total_fee'], $resArray['out_trade_no']);
                }elseif(strstr($resArray['out_trade_no'], 'S')){
                    $result = $this->shop_order_suc($resArray['transaction_id'], $resArray['total_fee'], $resArray['out_trade_no']);
                }
                //记录回调接口返回数据
                if (!is_dir(APPPATH . '/logs/wxpay'))
                    mkdir(APPPATH . '/logs/wxpay', 777);
                $fp = fopen(APPPATH . "/logs/wxpay/wxpay_h5_log_" . date('Ymd') . ".txt", "a");
                fwrite($fp, "res return" . date('Y:m:d H:i:s') . "\r\n---" . json_encode($result). "---\r\n");
                fclose($fp);
                if($result['code'] == 0){
                    //返回微信通知成功
                    echo \wxh5pay\lib\Core::arrayToXml([
                        'return_code' => 'SUCCESS',
                        'return_msg' => 'OK'
                    ]);
                }
                
            }else{
                //记录回调接口返回数据
                if (!is_dir(APPPATH . '/logs/wxpay'))
                    mkdir(APPPATH . '/logs/wxpay', 777);
                $fp = fopen(APPPATH . "/logs/wxpay/wxpay_h5_log_" . date('Ymd') . ".txt", "a");
                fwrite($fp, "sign error" . date('Y:m:d H:i:s') . "\r\n--- sign:" . $sign. " --- wxSign:".$wxSign." ---\r\n");
                fclose($fp);
            }
        }
    }

    /**
     * 支付成功后订单处理
     * transaction_id 微信支付订单号
     * total_fee 订单金额
     * out_trade_no 商户订单号
     */
    function user_order_suc($transaction_id, $total_fee, $out_trade_no){
        if(empty($transaction_id)){
            return output(1001,'transaction_id为空');
        }
        if(empty($total_fee)){
            return output(1001,'total_fee为空');
        }
        if(empty($out_trade_no)){
            return output(1001,'out_trade_no为空');
        }
        $user_order = $this->CI->user_order_model->get_by_no($out_trade_no);
        if(empty($user_order)){
            return output(1001,'该支付订单不存在');
        }else{
            if($user_order->status != 1){
                return output(1001,'该支付订单无须支付');
            }
        }

        $user_order_data = array(
            'order_no' => $out_trade_no,
            'status' => 2,
            'transaction_id' => $transaction_id,
            'pay_time' => date("Y-m-d H:i:s"),
            'update_time' => date("Y-m-d H:i:s")
        );
        $user_order_where = array();
        $user_order_where['order_no'] = $out_trade_no;
        $this->CI->user_order_model->query_update($user_order_where, $user_order_data);

        $check_user = $this->CI->user_model->get_by_id($user_order->user_id);
        if(empty($check_user)){
            return output(1004,'用户不存在');
        }
        $product = $this->CI->product_model->get_by_id($user_order->product_id);
        if(empty($product)){
            return output(1001,'支付产品不存在');
        }

        $shop = $this->CI->shop_model->get_by_uid($user_order->user_id);
        if(empty($shop)){
            return output(1001,'店铺不存在');
        }
        //更新店铺有效期
        $shop_data = array(
            'expire_time' => date("Y-m-d",strtotime("+".$product->num." month",strtotime($shop->expire_time))),
            'update_time' => date("Y-m-d H:i:s")
        );
        $shop_where = array();
        $shop_where['id'] = $shop->id;
        $this->CI->shop_model->query_update($shop_where, $shop_data);

        //更新用户返利比
        $user_data = array(
            'remaid_ratio' => $product->reward,
            'type' => 3,
            'update_time' => date("Y-m-d H:i:s")
        );
        $user_where = array();
        $user_where['id'] = $user_order->user_id;
        $this->CI->user_model->query_update($user_where, $user_data);

        //增加资金流水
        $capital_flow_data = [
            'user_id' => $user_order->user_id,
            'name' => '购买'.$product->num.'个月的店铺会员',
            'price' => $product->price,
            'type' => 3,
            'create_time' => date('Y-m-d H:i:s')
        ];
        $this->CI->capital_flow_model->query_insert($capital_flow_data);

        //返利
        if(empty($check_user->open_shop_time)){//第一次付款有返利
            if(!empty($check_user->superior_user_id)){
                $rebate_user = $this->CI->user_model->get_by_id($check_user->superior_user_id);
                if(!empty($rebate_user)){
                    $rebate_user_data = array(
                        'reward_balance' => $rebate_user->reward_balance + ($user_order->price*$rebate_user->remaid_ratio)/100,
                        'all_reward_price' => $rebate_user->all_reward_price + ($user_order->price*$rebate_user->remaid_ratio)/100,
                        'update_time' => date("Y-m-d H:i:s")
                    );
                    $rebate_user_where = array();
                    $rebate_user_where['id'] = $rebate_user->id;
                    $this->CI->user_model->query_update($rebate_user_where, $rebate_user_data);
                }
            }
            //更新用户付费开店成功的时间
            $user_data = $user_where = array();
            $user_data = array(
                'open_shop_time' => date("Y-m-d H:i:s"),
                'update_time' => date("Y-m-d H:i:s")
            );
            $user_where['id'] = $user_order->user_id;
            $this->CI->user_model->query_update($user_where, $user_data);
        }

        // 店铺会员支付成功小程序提醒
        if($check_user->openid && $user_order->prepay_id){
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.WECHAT_APPID.'&secret='.WECHAT_SECRET;
            $res = json_decode(file_get_contents($url));
            $access_token = $res->access_token;

            $msg_url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
            $end_user_order = $this->CI->user_order_model->get_by_no($out_trade_no);
            $msg_data = [
                'touser' => $check_user->openid,
                'template_id' => 'XQZuBKwnRuzlLspXRJ5q1RctEa8WqbvzxG6Qxl8Hq-o',
                'form_id' => $user_order->prepay_id,
                'data' => [
                    'keyword1' => ['value'=>'店铺开通成功'],
                    'keyword2' => ['value'=>$user_order->order_no],
                    'keyword3' => ['value'=>$user_order->price.'元'],
                    'keyword4' => ['value'=>$end_user_order->pay_time],
                    'keyword5' => ['value'=>'店铺开通成功，店铺有效期至'.date("Y-m-d",strtotime("+".$product->num." month",strtotime($shop->expire_time)))]
                ]
            ];
            curlpost($msg_url, json_encode($msg_data));
        }
        return output(0,'成功');
    }

    /**
     * 支付成功后订单处理
     * transaction_id 微信支付订单号
     * total_fee 订单金额
     * out_trade_no 商户订单号
     */
    function shop_order_suc($transaction_id, $total_fee, $out_trade_no){
        if(empty($transaction_id)){
            return output(1001,'transaction_id为空');
        }
        if(empty($total_fee)){
            return output(1001,'total_fee为空');
        }
        if(empty($out_trade_no)){
            return output(1001,'out_trade_no为空');
        }
        $shop_order = $this->CI->shop_order_model->get_by_orderno($out_trade_no);
        if(empty($shop_order)){
            return output(1001,'该支付订单不存在');
        }else{
            if($shop_order->status != 1){
                return output(1001,'该支付订单无须支付');
            }
        }
        $check_user = $this->CI->user_model->get_by_id($shop_order->user_id);
        if(empty($check_user)){
            return output(1004,'用户不存在');
        }

        $shop_order_data = array(
            'order_no' => $out_trade_no,
            'status' => 2,
            'transaction_id' => $transaction_id,
            'pay_time' => date("Y-m-d H:i:s"),
            'update_time' => date("Y-m-d H:i:s")
        );
        $shop_order_where = array();
        $shop_order_where['order_no'] = $out_trade_no;
        $this->CI->shop_order_model->query_update($shop_order_where, $shop_order_data);

        //库存、销量处理
        $this->CI->load->model('shop_goods_model');
        $this->CI->load->model('shop_order_detail_model');
        $this->CI->load->model('shop_goods_spec_model');
        $details = $this->CI->shop_order_detail_model->get_order_details($out_trade_no);
        $goods_name = '';
        if(!empty($details)){
            foreach($details as $detail){
                $goods = $this->CI->shop_goods_model->get_by_id($detail->goods_id);
                if(!empty($goods)){
                    if(empty($goods_name)){
                        $goods_name = $goods->goods_name;
                    }
                    $detail_data = array(
                        'stock_num' => $goods->stock_num-$detail->num,
                        'sale_num' => $goods->sale_num+$detail->num,
                        'update_time' => date("Y-m-d H:i:s")
                    );
                    $detail_where = array();
                    $detail_where['id'] = $detail->goods_id;
                    $this->CI->shop_goods_model->query_update($detail_where, $detail_data);
                }
                //规格
                $spec = $this->CI->shop_goods_spec_model->get_by_id($detail->spec_id);
                if(!empty($spec)){
                    $spec_data = array(
                        'stock_num' => $goods->stock_num-$detail->num,
                        'update_time' => date("Y-m-d H:i:s")
                    );
                    $spec_where = array();
                    $spec_where['id'] = $detail->spec_id;
                    $this->CI->shop_goods_spec_model->query_update($spec_where, $spec_data);
                }
            }
        }

        //增加购买者资金流水
        $capital_flow_user_data = [
            'user_id' => $shop_order->user_id,
            'name' => '订单购买',
            'price' => $shop_order->price,
            'type' => 4,
            'create_time' => date('Y-m-d H:i:s')
        ];
        $this->CI->capital_flow_model->query_insert($capital_flow_user_data);

        //增加店铺资金流水
        $shop = $this->CI->shop_model->get_by_domain($shop_order->shop_domain);
        $capital_flow_shop_data = [
            'user_id' => $shop->user_id,
            'name' => '订单收入',
            'price' => $shop_order->price,
            'type' => 1,
            'create_time' => date('Y-m-d H:i:s')
        ];
        $this->CI->capital_flow_model->query_insert($capital_flow_shop_data);

        //更新店铺金额
        $shop_user = $this->CI->user_model->get_by_id($shop->user_id);
        $shop_user_data = array(
            'unavailable_price' => $shop_user->unavailable_price + $shop_order->price,
            'update_time' => date("Y-m-d H:i:s")
        );
        $shop_user_where['id'] = $shop_user->id;
        $this->CI->user_model->query_update($shop_user_where, $shop_user_data);

        // 店铺会员支付成功小程序提醒
        if($check_user->openid && $shop_order->prepay_id){
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.WECHAT_APPID.'&secret='.WECHAT_SECRET;
            $res = json_decode(file_get_contents($url));
            $access_token = $res->access_token;

            $msg_url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
            $end_shop_order = $this->CI->shop_order_model->get_by_orderno($out_trade_no);
            $msg_data = [
                'touser' => $check_user->openid,
                'template_id' => '6S46KSNCwQlN_k-of7JtS0O-DORqlLjq_TfYLBrEHv0',
                'form_id' => $shop_order->prepay_id,
                'data' => [
                    'keyword1' => ['value'=>$goods_name],
                    'keyword2' => ['value'=>$shop_order->order_no],
                    'keyword3' => ['value'=>$shop_order->price.'元'],
                    'keyword4' => ['value'=>$end_shop_order->pay_time],
                    'keyword5' => ['value'=>'支付成功']
                ]
            ];
            curlpost($msg_url, json_encode($msg_data));
        }

        return output(0,'成功');
    }

}