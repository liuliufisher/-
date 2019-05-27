<?php

class Shop extends SYS_Controller {

    function __construct() {
        parent::__construct();
        //登录判断
        if(!$this->session->userdata('user')){
            redirect('/admin');
        }
        $this->load->model('shop_model');
        $this->load->library('shop_service');
        $this->my_module = SHOP;
    }

    /**
    * 店铺管理-列表
    */
    function shop_list() {
        $data = array();

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;

        $data['shop_list'] = $this->shop_model->get_shop_list($page, $limit);
        $count = $this->shop_model->get_shop_list_count();
        $data['total'] = $count->count;
        $data['per_page'] = $limit;

        $url = '?';
        if (!empty($limit)) {
            $url.='&limit=' . $limit;
        }

        $pagination_config = array(
            'base_url' => !empty($url) ? '/admin/shop/shop_list'.$url : '/admin/shop/shop_list',
            'total_rows' => $data['total'], //数据总数
            'per_page' => $limit, //每页条数
            'num_links' => 4,
            'uri_segment' => 3,
            'use_page_numbers' => true,
            'page_query_string' => true
        );
        $data['pagination'] = $this->pages($pagination_config);

        $this->seo_admin_title = '店铺列表 - '.WEB_SITE;
        $this->view('/admin/shop/index', $data);
    }

}