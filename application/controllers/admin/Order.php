<?php

class Order extends SYS_Controller {

    function __construct() {
        parent::__construct();
        //登录判断
        if(!$this->session->userdata('user')){
            redirect('/admin');
        }
        $this->load->model('user_order_model');
        $this->load->model('shop_order_model');
        $this->load->library('order_service');
        $this->my_module = ORDER;
    }

    /**
    * 订单管理-会员订单列表
    */
    function user_order_list() {
        $this->my_module = USER_ORDER;
        $data = array();

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;

        $data['order_list'] = $this->user_order_model->get_order_list($page, $limit);
        $count = $this->user_order_model->get_order_list_count();
        $data['total'] = $count->count;
        $data['per_page'] = $limit;

        $url = '?';
        if (!empty($limit)) {
            $url.='&limit=' . $limit;
        }

        $pagination_config = array(
            'base_url' => !empty($url) ? '/admin/order/order_list'.$url : '/admin/order/order_list',
            'total_rows' => $data['total'], //数据总数
            'per_page' => $limit, //每页条数
            'num_links' => 4,
            'uri_segment' => 3,
            'use_page_numbers' => true,
            'page_query_string' => true
        );
        $data['pagination'] = $this->pages($pagination_config);

        $this->seo_admin_title = '会员订单列表 - '.WEB_SITE;
        $this->view('/admin/order/user_order_list', $data);
    }

    /**
    * 订单管理-商品订单列表
    */
    function shop_order_list() {
        $this->my_module = SHOP_ORDER;
        $data = array();

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;

        $data['shop_order_list'] = $this->shop_order_model->get_shop_order_list($page, $limit);
        $count = $this->shop_order_model->get_shop_order_list_count();
        $data['total'] = $count->count;
        $data['per_page'] = $limit;

        $url = '?';
        if (!empty($limit)) {
            $url.='&limit=' . $limit;
        }

        $pagination_config = array(
            'base_url' => !empty($url) ? '/admin/order/shop_order_list'.$url : '/admin/order/shop_order_list',
            'total_rows' => $data['total'], //数据总数
            'per_page' => $limit, //每页条数
            'num_links' => 4,
            'uri_segment' => 3,
            'use_page_numbers' => true,
            'page_query_string' => true
        );
        $data['pagination'] = $this->pages($pagination_config);

        $this->seo_admin_title = '会员订单列表 - '.WEB_SITE;
        $this->view('/admin/order/shop_order_list', $data);
    }

}