<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');

/**
* 计划任务
*/
class Cron_api extends MY_Controller {

	function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('shop_order_model');
        $this->load->model('user_formid_model');
        $this->load->model('shop_order_detail_model');
        $this->load->model('shop_goods_model');
        $this->load->model('shop_model');
        $this->load->library('common_service');
    }

    /**
     * 待付款提醒，生成待支付订单10分钟发送提醒
     */
    function unpay_remind(){
        $shop_orders = $this->shop_order_model->get_unpay_orders(1);
        if(!empty($shop_orders)){
            foreach($shop_orders as $shop_order){
                if( date('Y-m-d H:i:s',strtotime("+10 minute",strtotime($shop_order->create_time))) < date('Y-m-d H:i:s') ){
                    //发送消息
                    $user = $this->user_model->get_by_id($shop_order->user_id);
                    $formId = $this->common_service->form($shop_order->user_id);

                    $details = $this->shop_order_detail_model->get_order_details($shop_order->order_no);
                    $goods_name = '';
                    if(!empty($details)){
                        foreach($details as $detail){
                            $goods = $this->shop_goods_model->get_by_id($detail->goods_id);
                            if(!empty($goods)){
                                $goods_name = $goods->goods_name;
                                continue;
                            }
                        }
                    }

                    $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.WECHAT_APPID.'&secret='.WECHAT_SECRET;
                    $res = json_decode(file_get_contents($url));
                    $access_token = $res->access_token;

                    $msg_url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
                    $msg_data = [
                        'touser' => $user->openid,
                        'template_id' => 'j6fY242bkBsSOpHXUiyRw3Tmq65tSN_Q6Uy70zXD_Kg',
                        'form_id' => $formId,
                        'data' => [
                            'keyword1' => ['value'=>$goods_name],
                            'keyword2' => ['value'=>$shop_order->order_no],
                            'keyword3' => ['value'=>$shop_order->price.'元'],
                            'keyword4' => ['value'=>$shop_order->create_time],
                            'keyword5' => ['value'=>'待支付'],
                            'keyword6' => ['value'=>'店家为您预留到'.date("Y-m-d H:i:s",strtotime("+30 minute",strtotime($shop_order->create_time))).',再不付款宝贝就被别人买走啦～']
                        ]
                    ];
                    curlpost($msg_url, json_encode($msg_data));

                    //更新formId状态
                    $formid_data = array(
                        'status' => 1,
                        'update_time' => date("Y-m-d H:i:s")
                    );
                    $where['form_id'] = $formId;
                    $this->user_formid_model->query_update($where, $formid_data);
                }
            }
        }

        echo 'done';exit;
    }

    /**
     * 30分钟内没付款，自动取消订单
     */
    function cancel_order(){
        $shop_orders = $this->shop_order_model->get_unpay_orders(1);
        if(!empty($shop_orders)){
            foreach($shop_orders as $shop_order){
                if( date('Y-m-d H:i:s',strtotime("+30 minute",strtotime($shop_order->create_time))) < date('Y-m-d H:i:s') ){
                    $shop_order_data = array(
                        'status' => 6,
                        'update_time' => date("Y-m-d H:i:s")
                    );
                    $where['order_no'] = $shop_order->order_no;
                    $this->shop_order_model->query_update($where, $shop_order_data);
                }
            }
        }

        echo 'done';exit;
    }

    /**
     * 7天自动收货
     */
    function take_over_order(){
        $shop_orders = $this->shop_order_model->get_unpay_orders(3);
        if(!empty($shop_orders)){
            foreach($shop_orders as $shop_order){
                if( date('Y-m-d H:i:s',strtotime("+7 day",strtotime($shop_order->create_time))) < date('Y-m-d H:i:s') ){
                    $shop_order_data = array(
                        'status' => 4,
                        'update_time' => date("Y-m-d H:i:s")
                    );
                    $where['order_no'] = $shop_order->order_no;
                    $this->shop_order_model->query_update($where, $shop_order_data);
                }
            }
        }

        echo 'done';exit;
    }

    /**
     * 店铺会员到期提醒
     */
    function shop_remind(){
        $shops = $this->shop_model->get_shops();
        if(!empty($shops)){
            foreach($shops as $shop){
                if( date('Y-m-d',strtotime("-1 day",strtotime($shop->expire_time))) <= date('Y-m-d') ){
                    //发送消息
                    $user = $this->user_model->get_by_id($shop->user_id);
                    $formId = $this->common_service->form($shop->user_id);

                    $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.WECHAT_APPID.'&secret='.WECHAT_SECRET;
                    $res = json_decode(file_get_contents($url));
                    $access_token = $res->access_token;

                    $msg_url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
                    $msg_data = [
                        'touser' => $user->openid,
                        'template_id' => 'ilAYcoEAHL4MMjZN9VeaVfKajVbQ1EjpsRFv7EdDQtI',
                        'form_id' => $formId,
                        'data' => [
                            'keyword1' => ['value'=>$shop->shop_name.'店铺到期提醒'],
                            'keyword2' => ['value'=>$shop->expire_time]
                        ]
                    ];
                    curlpost($msg_url, json_encode($msg_data));

                    //更新formId状态
                    $formid_data = array(
                        'status' => 1,
                        'update_time' => date("Y-m-d H:i:s")
                    );
                    $where['form_id'] = $formId;
                    $this->user_formid_model->query_update($where, $formid_data);
                }
            }
        }

        echo 'done';exit;
    }

}
