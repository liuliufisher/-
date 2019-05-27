<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');

class Order_api extends MY_Controller {

	function __construct() {
        parent::__construct();
        $this->load->library('order_service');
    }

    /**
     * 购物车进入的结算页面
     */
    public function settle() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $cart_id_json = isset($_POST['cart_id_json']) ? trim($_POST['cart_id_json']) : '';//购物车id[{"cart_id": "1"},{"cart_id": "2"}]

        $result = $this->order_service->settle($token, $cart_id_json);
        echo json_encode($result);exit;
    }

    /**
     * 直接进入的结算页面
     */
    public function direct_settle() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $goods_id = isset($_POST['goods_id']) ? trim($_POST['goods_id']) : '';//商品ID
        $spec_id = isset($_POST['spec_id']) ? trim($_POST['spec_id']) : '';//规格ID
        $num = isset($_POST['num']) ? trim($_POST['num']) : 1;//规格ID

        $result = $this->order_service->direct_settle($token, $goods_id, $spec_id, $num);
        echo json_encode($result);exit;
    }

    /**
     * 购物车进入的提交订单
     */
    public function submit_order() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $cart_id_json = isset($_POST['cart_id_json']) ? trim($_POST['cart_id_json']) : '';//购物车id[{"cart_id": "1"},{"cart_id": "2"}]
        $user_address_id = isset($_POST['user_address_id']) ? trim($_POST['user_address_id']) : '';//收获地址
        $remark = isset($_POST['remark']) ? trim($_POST['remark']) : '';//买家留言

        $result = $this->order_service->submit_order($token, $cart_id_json, $user_address_id, $remark);
        echo json_encode($result);exit;
    }

    /**
     * 直接进入的提交订单
     */
    public function direct_submit_order() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $goods_id = isset($_POST['goods_id']) ? trim($_POST['goods_id']) : '';//商品ID
        $spec_id = isset($_POST['spec_id']) ? trim($_POST['spec_id']) : '';//规格ID
        $num = isset($_POST['num']) ? trim($_POST['num']) : 1;//规格ID
        $user_address_id = isset($_POST['user_address_id']) ? trim($_POST['user_address_id']) : '';//收获地址
        $remark = isset($_POST['remark']) ? trim($_POST['remark']) : '';//买家留言

        $result = $this->order_service->direct_submit_order($token, $goods_id, $spec_id, $num, $user_address_id, $remark);
        echo json_encode($result);exit;
    }

    /**
     * 我的订单列表
     */
    public function my_orders() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $status = isset($_POST['status']) ? trim($_POST['status']) : '';
        $page = isset($_POST['page']) ? trim($_POST['page']) : 1;
        $per_page = isset($_POST['per_page']) ? trim($_POST['per_page']) : 10;

        $result = $this->order_service->my_orders($token, $status, $page, $per_page);
        echo json_encode($result);exit;
    }

    /**
     * 订单详情
     */
    public function order_detail() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $order_no = isset($_POST['order_no']) ? trim($_POST['order_no']) : '';

        $result = $this->order_service->order_detail($token, $order_no);
        echo json_encode($result);exit;
    }

    /**
     * 小程序商品统一下单
     */
    function unifiedorder(){
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $order_no = isset($_POST['order_no']) ? trim($_POST['order_no']) : '';
        $form_id = isset($_POST['form_id']) ? trim($_POST['form_id']) : '';
        
        $result = $this->order_service->unifiedorder($token, $order_no, $form_id);
        echo json_encode($result);exit;
    }

    /**
     * 店铺订单列表
     */
    public function shop_orders() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $status = isset($_POST['status']) ? trim($_POST['status']) : '';
        $page = isset($_POST['page']) ? trim($_POST['page']) : 1;
        $per_page = isset($_POST['per_page']) ? trim($_POST['per_page']) : 10;

        $result = $this->order_service->shop_orders($token, $status, $page, $per_page);
        echo json_encode($result);exit;
    }

    /**
     * 订单修改（修改金额、备注）
     */
    public function edit_order_price() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $order_no = isset($_POST['order_no']) ? trim($_POST['order_no']) : '';//订单号
        $price = isset($_POST['price']) ? trim($_POST['price']) : '';//金额
        $remark = isset($_POST['remark']) ? trim($_POST['remark']) : '';//备注

        $result = $this->order_service->edit_order_price($token, $order_no, $price, $remark);
        echo json_encode($result);exit;
    }

    /**
     * 添加订单物流单号（发货）
     */
    public function add_logistics_no() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $order_no = isset($_POST['order_no']) ? trim($_POST['order_no']) : '';//订单号
        $logistics_no = isset($_POST['logistics_no']) ? trim($_POST['logistics_no']) : '';//物流单号

        $result = $this->order_service->add_logistics_no($token, $order_no, $logistics_no);
        echo json_encode($result);exit;
    }

    /**
     * 申请提现
     */
    function apply_withdraw(){
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $price = isset($_POST['price']) ? trim($_POST['price']) : '';
        $type = isset($_POST['type']) ? trim($_POST['type']) : '';
        $form_id = isset($_POST['form_id']) ? trim($_POST['form_id']) : '';
        
        $result = $this->order_service->apply_withdraw($token, $price, $type, $form_id);
        echo json_encode($result);exit;
    }

    /**
     * 确认收货
     */
    public function confirm_order() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $order_no = isset($_POST['order_no']) ? trim($_POST['order_no']) : '';//订单号

        $result = $this->order_service->confirm_order($token, $order_no);
        echo json_encode($result);exit;
    }

}
