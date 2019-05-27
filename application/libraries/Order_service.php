<?php
/**
 * 订单管理业务层
 * @author dingxuehuan
 */
class Order_service {

	private $CI;

	function __construct() {
		$this->CI = & get_instance ();
        $this->CI->load->model('user_model');
        $this->CI->load->model('shop_order_model');
        $this->CI->load->model('shop_order_detail_model');
        $this->CI->load->model('cart_model');
        $this->CI->load->model('shop_model');
        $this->CI->load->model('shop_goods_model');
        $this->CI->load->model('shop_goods_cate_model');
        $this->CI->load->model('shop_goods_spec_model');
        $this->CI->load->model('shop_goods_image_model');
        $this->CI->load->model('withdraw_model');
	}

	/**
     * 结算页面
     */
    public function settle($token, $cart_id_json) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($cart_id_json)){
            return output(1001,'参数错误');
        }

        $result = [];

        $cart_id_arr = json_decode($cart_id_json);
        $goods_arr = [];
        $freight = 0;
        $total_price = 0;
        $shop_domain = '';
        if(!empty($cart_id_arr)){
            foreach($cart_id_arr as $row){
                $cart = $this->CI->cart_model->get_by_id($row->cart_id);
                
                $g['num'] = $cart->num;
                $goods = $this->CI->shop_goods_model->get_by_id($cart->goods_id);
                if(!empty($goods)){
                    $g['goods_name'] = $goods->goods_name;
                    $g['goods_image'] = $goods->goods_image;

                    if($freight < $goods->freight){
                        $freight = $goods->freight;
                    }
                }

                $spec = $this->CI->shop_goods_spec_model->get_by_id($cart->spec_id);
                if(!empty($spec)){
                    $g['spec'] = $spec->spec;
                    $g['price'] = $spec->goods_price;

                    $total_price += $cart->num*$spec->goods_price;
                }
                $goods_arr[] = $g;

                $shop_domain = $cart->shop_domain;
            }
        }

        $shop = $this->CI->shop_model->get_by_domain($shop_domain);
        if(empty($shop)){
            return output(1001,'该店铺不存在');
        }

        $result['shop_name'] = $shop->shop_name;
        $result['shop_domain'] = $shop->shop_domain;
        $result['freight'] = $freight;
        $result['total_price'] = $total_price;
        $result['goods'] = $goods_arr;

        //获取默认收货地址
        $this->CI->load->model('user_address_model');
        $user_address = $this->CI->user_address_model->get_default_address($user_id);
        if(!empty($user_address)){
            $result['user_address'] = [
                'address_id' => $user_address->id,
                'name' => $user_address->name,
                'mobile' => $user_address->mobile,
                'address' => $user_address->province_name.$user_address->city_name.$user_address->area_name.$user_address->address,
                'name' => $user_address->name,
            ];
        }else{
            $result['user_address'] = [];
        }

        return output(0,'成功',$result);
    }

    /**
     * 直接进入的结算页面
     */
    public function direct_settle($token, $goods_id, $spec_id, $num) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($goods_id) || empty($spec_id)){
            return output(1001,'参数错误');
        }

        $result = [];
        $goods = $this->CI->shop_goods_model->get_by_id($goods_id);
        if(empty($goods)){
            return output(1001,'商品不存在');
        }

        $spec = $this->CI->shop_goods_spec_model->get_by_id($spec_id);
        if(empty($spec)){
            return output(1001,'规格不存在');
        }

        //获取店铺
        $shop = $this->CI->shop_model->get_by_uid($goods->user_id);
        if(empty($shop)){
            return output(1001,'该店铺不存在');
        }
        $result['shop_name'] = $shop->shop_name;
        $result['shop_domain'] = $shop->shop_domain;
        $result['freight'] = $goods->freight;
        $result['total_price'] = $num*$spec->goods_price;
        
        $result['goods'] = [
            'num' => $num,
            'goods_name' => $goods->goods_name,
            'goods_image' => $goods->goods_image,
            'spec' => $spec->spec,
            'price' => $spec->goods_price
        ];

        //获取默认收货地址
        $this->CI->load->model('user_address_model');
        $user_address = $this->CI->user_address_model->get_default_address($user_id);
        if(!empty($user_address)){
            $result['user_address'] = [
                'address_id' => $user_address->id,
                'name' => $user_address->name,
                'mobile' => $user_address->mobile,
                'address' => $user_address->province_name.$user_address->city_name.$user_address->area_name.$user_address->address,
                'name' => $user_address->name,
            ];
        }else{
            $result['user_address'] = [];
        }

        return output(0,'成功',$result);
    }

    /**
     * 提交订单
     */
    public function submit_order($token, $cart_id_json, $user_address_id, $remark) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($cart_id_json)){
            return output(1001,'参数错误');
        }
        if(empty($user_address_id)){
            return output(1001,'请填写收获地址');
        }

        $order_no = create_order_code('S');
        $shop_domain = '';
        $spec = '';
        $freight = 0;
        $total_price = 0;

        $this->CI->shop_order_detail_model->trans_begin();

        $cart_id_arr = json_decode($cart_id_json);
        if(!empty($cart_id_arr)){
            foreach($cart_id_arr as $row){
                //购物车
                $cart = $this->CI->cart_model->get_by_id($row->cart_id);
                $shop_domain = $cart->shop_domain;

                //规格明细
                $spec_detail = $this->CI->shop_goods_spec_model->get_by_id($cart->spec_id);
                if(!empty($spec_detail)){
                    $spec = $spec_detail->spec;
                    $total_price += $spec_detail->goods_price*$cart->num;
                }else{
                    $this->CI->shop_order_detail_model->trans_rollback();
                    return output(1001,'该规格不存在');
                }
                if($spec_detail->stock_num == 0){
                    $this->CI->shop_order_detail_model->trans_rollback();
                    return output(1001,'该商品库存不足');
                }
                $goods = $this->CI->shop_goods_model->get_by_id($cart->goods_id);
                if(!empty($goods)){
                    if($freight < $goods->freight){
                        $freight = $goods->freight;
                    }
                }

                //添加订单明细
                $shop_order_detail = [
                    'goods_id' => $cart->goods_id,
                    'order_no' => $order_no,
                    'spec' => $spec,
                    'spec_id' => $cart->spec_id,
                    'num' => $cart->num,
                    'price' => $spec_detail->goods_price*$cart->num,
                    'create_time' => date('Y-m-d H:i:s')
                ];
                $this->CI->shop_order_detail_model->query_insert($shop_order_detail);

                //删除购物车
                $cart_where['id'] = $row->cart_id;
                $this->CI->cart_model->query_delete($cart_where);
            }
        }

        //添加订单
        $shop_order = [
            'user_id' => $user_id,
            'order_no' => $order_no,
            'shop_domain' => $shop_domain,
            'price' => $total_price+$freight,
            'user_address_id' => $user_address_id,
            'remark' => $remark,
            'freight' => $freight,
            'create_time' => date('Y-m-d H:i:s')
        ];
        $this->CI->shop_order_model->query_insert($shop_order);

        $this->CI->shop_order_detail_model->trans_commit();

        return output(0,'成功',['order_no'=>$order_no]);
    }

    /**
     * 直接进入的提交订单
     */
    public function direct_submit_order($token, $goods_id, $spec_id, $num, $user_address_id, $remark) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($goods_id) || empty($spec_id)){
            return output(1001,'参数错误');
        }
        if(empty($user_address_id)){
            return output(1001,'请填写收获地址');
        }

        $goods = $this->CI->shop_goods_model->get_by_id($goods_id);
        if(empty($goods)){
            return output(1001,'该商品不存在');
        }
        //获取店铺
        $shop = $this->CI->shop_model->get_by_uid($goods->user_id);
        if(empty($shop)){
            return output(1001,'该店铺不存在');
        }

        $order_no = create_order_code('S');
        $shop_domain = '';
        $spec = '';
        $total_price = 0;

        //规格明细
        $spec_detail = $this->CI->shop_goods_spec_model->get_by_id($spec_id);
        if(!empty($spec_detail)){
            $spec = $spec_detail->spec;
            $total_price = $spec_detail->goods_price*$num;
        }else{
            return output(1001,'该规格不存在');
        }
        if($spec_detail->stock_num == 0){
            return output(1001,'该商品库存不足');
        }

        //添加订单明细
        $shop_order_detail = [
            'goods_id' => $goods_id,
            'order_no' => $order_no,
            'spec' => $spec,
            'spec_id' => $spec_id,
            'num' => $num,
            'price' => $spec_detail->goods_price*$num,
            'create_time' => date('Y-m-d H:i:s')
        ];
        $this->CI->shop_order_detail_model->query_insert($shop_order_detail);

        //添加订单
        $shop_order = [
            'user_id' => $user_id,
            'order_no' => $order_no,
            'shop_domain' => $shop->shop_domain,
            'price' => $total_price+$goods->freight,
            'user_address_id' => $user_address_id,
            'remark' => $remark,
            'freight' => $goods->freight,
            'create_time' => date('Y-m-d H:i:s')
        ];
        $this->CI->shop_order_model->query_insert($shop_order);


        return output(0,'成功',['order_no'=>$order_no]);
    }

    /**
     * 我的订单列表
     */
    public function my_orders($token, $status, $page, $per_page) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        $orders = $this->CI->shop_order_model->get_orders($user_id, '', $status, $page, $per_page);
        $result = [];
        if(!empty($orders)){
            foreach($orders as $val){
                $order = [
                    'order_no' =>  $val->order_no,
                    'shop_domain' =>  $val->shop_domain,
                    'all_price' =>  $val->all_price,
                    'status' =>  $val->status,
                    'logistics_no' =>  $val->logistics_no
                ];
                $shop = $this->CI->shop_model->get_by_domain($val->shop_domain);
                if(!empty($shop)){
                    $order['shop_name'] = $shop->shop_name;
                }
                $order_details = $this->CI->shop_order_detail_model->get_order_details($val->order_no);
                $goods_arr = [];
                if(!empty($order_details)){
                    foreach($order_details as $order_detail){
                        $goods = $this->CI->shop_goods_model->get_by_id($order_detail->goods_id);
                        if(!empty($goods)){
                            $g['goods_name'] = $goods->goods_name;
                            $g['goods_image'] = $goods->goods_image;                        
                        }
                        $spec = '';$price=0;
                        if($val->status == 1){
                            $spec_detail = $this->CI->shop_goods_spec_model->get_by_id($order_detail->spec_id);
                            if(!empty($spec_detail)){
                                $spec = $spec_detail->spec;
                                $price = $spec_detail->goods_price;
                            }
                        }else{
                            $spec = $order_detail->spec;
                            $price = $order_detail->price;
                        }
                        $g['spec'] = $spec;
                        $g['price'] = $price;
                        $g['num'] = $order_detail->num;
                        
                        $goods_arr[] = $g;
                    }
                    $order['goods'] = $goods_arr;
                }

                $result[] = $order;
            }
        }

        $orders = $this->CI->shop_order_model->get_orders_count($user_id, '', $status);

        return output(0,'成功',['orders'=>$result,'total'=>$orders->count]);
    }

    /**
     * 订单详情
     */
    public function order_detail($token, $order_no) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($order_no)){
            return output(1001,'参数错误');
        }

        $user_order = $this->CI->shop_order_model->get_by_orderno($order_no);
        if(empty($user_order)){
            return output(1001,'该订单不存在');
        }
        $order = [
            'order_no' =>  $user_order->order_no,
            'shop_domain' =>  $user_order->shop_domain,
            'all_price' =>  $user_order->price,
            'status' =>  $user_order->status,
            'logistics_no' =>  $user_order->logistics_no,
            'freight' =>  $user_order->freight,
            'remark' =>  $user_order->remark,
            'shop_remark' =>  $user_order->shop_remark,
            'create_time' =>  $user_order->create_time,
            'pay_time' =>  $user_order->pay_time,
            'send_time' =>  $user_order->send_time,
            'complete_time' =>  $user_order->complete_time
        ];
        $shop = $this->CI->shop_model->get_by_domain($user_order->shop_domain);
        if(!empty($shop)){
            $order['shop_name'] = $shop->shop_name;
            $order['mobile'] = $shop->mobile;
            $order['mobile'] = $shop->mobile;

        }
        $order_details = $this->CI->shop_order_detail_model->get_order_details($order_no);
        $goods_arr = [];
        $goods_price = 0;
        if(!empty($order_details)){
            foreach($order_details as $order_detail){
                $goods = $this->CI->shop_goods_model->get_by_id($order_detail->goods_id);
                if(!empty($goods)){
                    $g['goods_name'] = $goods->goods_name;
                    $g['goods_image'] = $goods->goods_image;
                }
                $spec = '';$price=0;
                if($user_order->status == 1){
                    $spec_detail = $this->CI->shop_goods_spec_model->get_by_id($order_detail->spec_id);
                    if(!empty($spec_detail)){
                        $spec = $spec_detail->spec;
                        $price = $spec_detail->goods_price;
                    }
                }else{
                    $spec = $order_detail->spec;
                    $price = $order_detail->price;
                }
                $goods_price += $price;
                $g['spec'] = $spec;
                $g['price'] = $price;
                $g['num'] = $order_detail->num;
                
                $goods_arr[] = $g;
            }
            $order['goods'] = $goods_arr;
        }
        $order['goods_price'] = $goods_price;

        //获取默认收货地址
        $this->CI->load->model('user_address_model');
        $user_address = $this->CI->user_address_model->get_by_id($user_order->user_address_id);
        if(!empty($user_address)){
            $order['user_address'] = [
                'address_id' => $user_address->id,
                'name' => $user_address->name,
                'mobile' => $user_address->mobile,
                'address' => $user_address->province_name.$user_address->city_name.$user_address->area_name.$user_address->address,
                'name' => $user_address->name,
            ];
        }

        return output(0,'成功',$order);
    }

    /**
     * 订单修改（修改金额、备注）
     */
    public function edit_order_price($token, $order_no, $price, $remark) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        $where = $order_data = array();
        $where['order_no'] = $order_no;
        $order_data['price'] = $price;
        $order_data['shop_remark'] = $remark;
        $order_data['update_time'] = date("Y-m-d H:i:s");
        $this->CI->shop_order_model->query_update($where, $order_data);

        return output(0,'成功');
    }

    /**
     * 添加订单物流单号
     */
    public function add_logistics_no($token, $order_no, $logistics_no) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        $shop_order = $this->CI->shop_order_model->get_by_orderno($order_no);//获取订单
        if(!empty($shop_order) && $shop_order->status == 2){
            $where = $order_data = array();
            $where['order_no'] = $order_no;
            $order_data['logistics_no'] = $logistics_no;
            $order_data['status'] = 3;
            $order_data['update_time'] = date("Y-m-d H:i:s");
            $this->CI->shop_order_model->query_update($where, $order_data);

            //更新店铺金额
            $shop = $this->CI->shop_model->get_by_domain($shop_order->shop_domain);
            if(empty($shop)){
                return output(1001,'该店铺不存在');
            }
            $shop_user = $this->CI->user_model->get_by_id($shop->user_id);
            $shop_user_data = array(
                'shop_all_price' => $shop_user->shop_all_price + $shop_order->price,
                'available_balance' => $shop_user->available_balance + $shop_order->price,
                'unavailable_price' => $shop_user->unavailable_price - $shop_order->price,
                'update_time' => date("Y-m-d H:i:s")
            );
            $shop_user_where['id'] = $shop_user->id;
            $this->CI->user_model->query_update($shop_user_where, $shop_user_data);

            //发送消息
            // $formId = $this->CI->common_service->form($shop_order->user_id);
            // $details = $this->shop_order_detail_model->get_order_details($order_no);
            // $goods_name = '';
            // $num = 0;
            // if(!empty($details)){
            //     foreach($details as $detail){
            //         $num += $detail->num;
            //         if(empty($goods_name)){
            //             $goods = $this->shop_goods_model->get_by_id($detail->goods_id);
            //             if(!empty($goods)){
            //                 $goods_name = $goods->goods_name;
            //             }
            //         }
            //     }
            // }

            // $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.WECHAT_APPID.'&secret='.WECHAT_SECRET;
            // $res = json_decode(file_get_contents($url));
            // $access_token = $res->access_token;
            // $msg_url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
            // $msg_data = [
            //     'touser' => $shop_user->openid,
            //     'template_id' => 'lmqOtt8AvNbucFOtF_TLnzSJjLVmdpmEnBWIl1D7yXY',
            //     'form_id' => $formId,
            //     'data' => [
            //         'keyword1' => ['value'=>$goods_name],
            //         'keyword2' => ['value'=>$num],
            //         'keyword3' => ['value'=>$shop_order->price],
            //         'keyword4' => ['value'=>$order_no],
            //         'keyword5' => ['value'=>$logistics_no],
            //         'keyword6' => ['value'=>'已发货'],
            //         'keyword7' => ['value'=>date("Y-m-d H:i:s")],
            //         'keyword8' => ['value'=>'您好，您购买的商品已经发货，请注意查收。']
            //     ]
            // ];
            // curlpost($msg_url, json_encode($msg_data));

            // //更新formId状态
            // $formid_data = array(
            //     'status' => 1,
            //     'update_time' => date("Y-m-d H:i:s")
            // );
            // $where['form_id'] = $formId;
            // $this->CI->load->model('user_formid_model');
            // $this->CI->user_formid_model->query_update($where, $formid_data);
        }

        return output(0,'成功');
    }

    /**
     * 小程序商品统一下单
     */
    function unifiedorder($token, $order_no, $form_id){
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($order_no)){
            return output(1001,'参数错误');
        }

        $user = $this->CI->user_model->get_by_id($user_id);
        if(empty($user)){
            return output(1001,'用户不存在');
        }

        $shop_order = $this->CI->shop_order_model->get_by_orderno($order_no);
        if(empty($shop_order)){
            return output(1001,'该支付订单不存在');
        }elseif(!empty($shop_order) && $shop_order->status != 1){
            return output(1001,'该支付订单无须支付');
        }

        include_once APPPATH.'libraries/wechat_pay/src/wxh5pay/Order.php';
        include_once APPPATH.'libraries/wechat_pay/src/wxh5pay/lib/Core.php';
        include_once APPPATH.'libraries/wechat_pay/src/wxh5pay/lib/Sign.php';

        $order = new \wxh5pay\Order();
        $order->setParams([
            'appid' => WECHAT_APPID,
            'body' => '金芒杂货铺-店铺'.$shop_order->shop_domain.'商品购买',
            'out_trade_no' => $shop_order->order_no,
            'total_fee' => $shop_order->price*100,
            'trade_type' => 'JSAPI',
            'device_info' => DEVICE_INFO,
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            'notify_url' => NOTIFY_URL,
            'openid' => $user->openid
        ]);
        $res = $order->unifiedorder();
        if(!$res){
            return output(1001,$order->errMsg);
        }else{
            //更新prepay_id
            $where = $shop_order_data = array();
            $where['order_no'] = $order_no;
            $shop_order_data['prepay_id'] = $res['prepay_id'];
            $shop_order_data['update_time'] = date("Y-m-d H:i:s");
            $this->CI->shop_order_model->query_update($where, $shop_order_data);

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
     * 店铺订单列表
     */
    public function shop_orders($token, $status, $page, $per_page) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        $shop = $this->CI->shop_model->get_by_uid($user_id);
        if(empty($shop)){
            return output(1004,'店铺不存在');
        }

        $orders = $this->CI->shop_order_model->get_orders('', $shop->shop_domain, $status, $page, $per_page);
        $result = [];
        if(!empty($orders)){
            foreach($orders as $val){
                $order = [
                    'order_no' =>  $val->order_no,
                    'shop_domain' =>  $val->shop_domain,
                    'all_price' =>  $val->all_price,
                    'freight' =>  $val->freight,
                    'status' =>  $val->status,
                    'logistics_no' =>  $val->logistics_no,
                    'pay_time' => $val->pay_time,
                    'remark' => $val->remark,
                    'shop_remark' => $val->shop_remark
                ];
                $user = $this->CI->user_model->get_by_id($val->user_id);
                if(!empty($user)){
                    $order['nick_name'] = $user->nick_name;
                }
                $order_details = $this->CI->shop_order_detail_model->get_order_details($val->order_no);
                $goods_arr = [];
                if(!empty($order_details)){
                    foreach($order_details as $order_detail){
                        $goods = $this->CI->shop_goods_model->get_by_id($order_detail->goods_id);
                        if(!empty($goods)){
                            $g['goods_name'] = $goods->goods_name;
                            $g['goods_image'] = $goods->goods_image;
                        }
                        $spec = '';$price=0;
                        if($val->status == 1){
                            $spec_detail = $this->CI->shop_goods_spec_model->get_by_id($order_detail->spec_id);
                            if(!empty($spec_detail)){
                                $spec = $spec_detail->spec;
                                $price = $spec_detail->goods_price;
                            }
                        }else{
                            $spec = $order_detail->spec;
                            $price = $order_detail->price;
                        }
                        $g['spec'] = $spec;
                        $g['price'] = $price;
                        $g['num'] = $order_detail->num;
                        
                        $goods_arr[] = $g;
                    }
                }
                $order['goods'] = $goods_arr;

                $result[] = $order;
            }
        }

        $order_count = $this->CI->shop_order_model->get_orders_count('', $shop->shop_domain, $status);

        return output(0,'成功',['orders'=>$result,'total'=>$order_count->count]);
    }

    /**
     * 申请提现
     */
    public function apply_withdraw($token, $price, $type, $form_id) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        $check_user = $this->CI->user_model->get_by_id($user_id);
        if(empty($check_user)){
            return output(1004,'用户不存在');
        }
        if(empty($price)){
            return output(1001,'请填写提现金额');
        }

        if($type == 1 && $check_user->reward_balance < $price){
            return output(1001,'可提现余额不足');
        }elseif($type == 2 && $check_user->available_balance < $price){
            return output(1001,'可提现余额不足');
        }
        if(empty($check_user->open_bank) || empty($check_user->card_num) || empty($check_user->name)){
            return output(1001,'请先绑定银行账号');
        }

        $this->CI->db->trans_start();
        $create_time = date('Y-m-d H:i:s');
        $data = [
            'user_id' => $user_id,
            'withdraw_no' => create_order_code('W'),
            'price' => $price,
            'open_bank' => $check_user->open_bank,
            'card_num' => $check_user->card_num,
            'name' => $check_user->name,
            'form_id' => $form_id,
            'status' => 1,
            'type' => $type,
            'create_time' => $create_time
        ];
        $withdraw_id = $this->CI->withdraw_model->query_insert($data);
        if($withdraw_id > 0){
            $keyword7 = $keyword8 = '';
            if($type == 1){//返佣金额
                $user_data = array(
                    'reward_balance' => $check_user->reward_balance - $price,
                    'withdraw_price' => $check_user->withdraw_price + $price,
                    'frozen_price' => $check_user->frozen_price + $price,
                    'update_time' => date("Y-m-d H:i:s")
                );
                $where = array();
                $where['id'] = $user_id;
                $this->CI->user_model->query_update($where, $user_data);
                $keyword7 = '无';
                $keyword8 = $price;
            }elseif($type == 2){//店铺金额
                $user_data = array(
                    'available_balance' => $check_user->available_balance - $price,
                    'shop_reward_price' => $check_user->shop_reward_price + $price,
                    'shop_frozen_price' => $check_user->shop_frozen_price + $price,
                    'update_time' => date("Y-m-d H:i:s")
                );
                $where = array();
                $where['id'] = $user_id;
                $this->CI->user_model->query_update($where, $user_data);
                $keyword7 = '0.60%';
                $keyword8 = round(($price*0.6)/100, 2);
            }
            //增加资金流水
            $name = '';
            if($type == 1){//返佣金额
                $name = '提现返佣金额'.$price.'元';
            }elseif($type == 2){
                $name = '提现店铺金额'.$price.'元';
            }
            $capital_flow_data = [
                'user_id' => $user_id,
                'name' => $name,
                'price' => $price,
                'type' => 5,
                'create_time' => date('Y-m-d H:i:s')
            ];
            $this->CI->load->model('capital_flow_model');
            $this->CI->capital_flow_model->query_insert($capital_flow_data);

            //发送消息
            // $formId = $this->CI->common_service->form($shop->user_id);
            // $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.WECHAT_APPID.'&secret='.WECHAT_SECRET;
            // $res = json_decode(file_get_contents($url));
            // $access_token = $res->access_token;
            // $msg_url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
            // $msg_data = [
            //     'touser' => $check_user->openid,
            //     'template_id' => 'hApdppxSYncCHg_zpNF5xFOBfCV5ExVA9DcLfmKRy18',
            //     'form_id' => $formId,
            //     'data' => [
            //         'keyword1' => ['value'=>$price],
            //         'keyword2' => ['value'=>$create_time],
            //         'keyword3' => ['value'=>$check_user->name],
            //         'keyword4' => ['value'=>$check_user->open_bank],
            //         'keyword5' => ['value'=>$check_user->card_num],
            //         'keyword6' => ['value'=>date('Y-m-d',strtotime("+1 day"))],
            //         'keyword7' => ['value'=>$keyword7],
            //         'keyword8' => ['value'=>$keyword8],
            //         'keyword9' => ['value'=>'提现1个工作日到账金额，遇节假日顺延']
            //     ]
            // ];
            // curlpost($msg_url, json_encode($msg_data));

            // //更新formId状态
            // $formid_data = array(
            //     'status' => 1,
            //     'update_time' => date("Y-m-d H:i:s")
            // );
            // $where['form_id'] = $formId;
            // $this->CI->load->model('user_formid_model');
            // $this->CI->user_formid_model->query_update($where, $formid_data);

            $this->CI->db->trans_complete();
            return output(0,'申请提现成功');
        }else{
            $this->CI->db->trans_rollback();
            return output(1,'申请提现失败');
        }
    }

    /**
     * 确认收货
     */
    public function confirm_order($token, $order_no) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        $shop_order = $this->CI->shop_order_model->get_by_orderno($order_no);
        if(empty($shop_order)){
            return output(1,'该订单不存在');
        }
        if(!empty($shop_order) && $shop_order->status != 3){
            return output(1,'该订单不能确认收货');
        }

        $where = $order_data = array();
        $where['order_no'] = $order_no;
        $order_data['status'] = 4;
        $order_data['update_time'] = date("Y-m-d H:i:s");
        $this->CI->shop_order_model->query_update($where, $order_data);

        return output(0,'成功');
    }

}