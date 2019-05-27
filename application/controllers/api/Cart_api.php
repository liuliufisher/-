<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');

class Cart_api extends MY_Controller {

	function __construct() {
        parent::__construct();
        $this->load->library('cart_service');
    }

    /**
     * 保存购物车
     */
    public function save_cart() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $shop_domain = isset($_POST['shop_domain']) ? trim($_POST['shop_domain']) : ''; //店铺域名
        $goods_id = isset($_POST['goods_id']) ? trim($_POST['goods_id']) : ''; //商品id
        $spec_id = isset($_POST['spec_detail_id']) ? trim($_POST['spec_detail_id']) : ''; //规格id
        $num = isset($_POST['num']) ? trim($_POST['num']) : 1; //数量

        $result = $this->cart_service->save_cart($token, $shop_domain, $goods_id, $spec_id, $num);
        echo json_encode($result);exit;
    }

    /**
     * 购物车增加数量
     */
    public function add_cart_num() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $cart_id = isset($_POST['cart_id']) ? trim($_POST['cart_id']) : ''; //店铺域名
        $num = isset($_POST['num']) ? trim($_POST['num']) : 1; //数量

        $result = $this->cart_service->add_cart_num($token, $cart_id, $num);
        echo json_encode($result);exit;
    }

    /**
     * 获取购物车列表
     */
    public function get_carts() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $shop_domain = isset($_POST['shop_domain']) ? trim($_POST['shop_domain']) : ''; //店铺域名
        $page = isset($_POST['page']) ? trim($_POST['page']) : 1;
        $per_page = isset($_POST['per_page']) ? trim($_POST['per_page']) : 10;

        $result = $this->cart_service->get_carts($token, $shop_domain, $page, $per_page);
        echo json_encode($result);exit;
    }

    /**
     * 删除购物车
     */
    public function del_cart() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $cart_id = isset($_POST['cart_id']) ? trim($_POST['cart_id']) : ''; //购物车id

        $result = $this->cart_service->del_cart($token, $cart_id);
        echo json_encode($result);exit;
    }

    /**
     * 获取购物车数量
     */
    public function get_cart_num() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $shop_domain = isset($_POST['shop_domain']) ? trim($_POST['shop_domain']) : ''; //店铺域名

        $result = $this->cart_service->get_cart_num($token, $shop_domain);
        echo json_encode($result);exit;
    }

}
