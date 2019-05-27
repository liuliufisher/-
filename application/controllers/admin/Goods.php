<?php

class Goods extends SYS_Controller {

    function __construct() {
        parent::__construct();
        //登录判断
        if(!$this->session->userdata('user')){
            redirect('/admin');
        }
        $this->load->model('shop_goods_model');
        $this->my_module = GOODS;
    }

    /**
    * 商品管理-列表
    */
    function goods_list() {
        $data = array();

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

        $data['goods_list'] = $this->shop_goods_model->get_goods_list($keyword, $page, $limit);
        $count = $this->shop_goods_model->get_goods_list_count($keyword);
        $data['total'] = $count->count;
        $data['per_page'] = $limit;

        $url = '?';
        if (!empty($limit)) {
            $url.='&limit=' . $limit;
        }
        if (!empty($keyword)) {
            $url.='&keyword=' . $keyword;
        }

        $pagination_config = array(
            'base_url' => !empty($url) ? '/admin/goods/goods_list'.$url : '/admin/goods/goods_list',
            'total_rows' => $data['total'], //数据总数
            'per_page' => $limit, //每页条数
            'num_links' => 4,
            'uri_segment' => 3,
            'use_page_numbers' => true,
            'page_query_string' => true
        );
        $data['pagination'] = $this->pages($pagination_config);
        $data['keyword'] = $keyword;

        $this->seo_admin_title = '商品列表 - '.WEB_SITE;
        $this->view('/admin/goods/index', $data);
    }

}