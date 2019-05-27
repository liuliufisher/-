<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');

class User_order_api extends MY_Controller {

	function __construct() {
        parent::__construct();
        $this->load->library('user_order_service');
    }

    /**
     * 小程序统一下单
     */
    function unifiedorder_user(){
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $product_id = isset($_POST['product_id']) ? trim($_POST['product_id']) : '';
        $form_id = isset($_POST['form_id']) ? trim($_POST['form_id']) : '';
        
        $result = $this->user_order_service->unifiedorder_user($token, $product_id, $form_id);
        echo json_encode($result);exit;
    }

    /**
     * 微信支付回调
     */
    function pay(){
        $result = $this->user_order_service->pay();
        echo json_encode($result);exit;
    }

}
