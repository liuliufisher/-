<?php

class User extends SYS_Controller {

    function __construct() {
        parent::__construct();
        //登录判断
        if(!$this->session->userdata('user')){
            redirect('/admin');
        }
        $this->load->model('user_model');
        $this->load->library('user_service');
        $this->my_module = USER;
    }

    /**
    * 用户管理-列表
    */
    function user_list() {
        $data = array();

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

        $data['user_list'] = $this->user_model->get_user_list($keyword, $page, $limit);
        $count = $this->user_model->get_user_list_count($keyword);
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
            'base_url' => !empty($url) ? '/admin/user/user_list'.$url : '/admin/user/user_list',
            'total_rows' => $data['total'], //数据总数
            'per_page' => $limit, //每页条数
            'num_links' => 4,
            'uri_segment' => 3,
            'use_page_numbers' => true,
            'page_query_string' => true
        );
        $data['pagination'] = $this->pages($pagination_config);
        $data['keyword'] = $keyword;

        $this->seo_admin_title = '用户列表 - '.WEB_SITE;
        $this->view('/admin/user/index', $data);
    }

}