<?php

class Withdraw extends SYS_Controller {

    function __construct() {
        parent::__construct();
        //登录判断
        if(!$this->session->userdata('user')){
            redirect('/admin');
        }
        $this->load->model('withdraw_model');
        $this->load->library('withdraw_service');
        $this->my_module = WITHDRAW;
    }

    /**
    * 提现管理-列表
    */
    function withdraw_list() {
        $data = array();

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;

        $data['withdraw_list'] = $this->withdraw_model->get_withdraw_list($page, $limit);
        $count = $this->withdraw_model->get_withdraw_list_count();
        $data['total'] = $count->count;
        $data['per_page'] = $limit;

        $url = '?';
        if (!empty($limit)) {
            $url.='&limit=' . $limit;
        }
        $pagination_config = array(
            'base_url' => !empty($url) ? '/admin/withdraw/withdraw_list'.$url : '/admin/withdraw/withdraw_list',
            'total_rows' => $data['total'], //数据总数
            'per_page' => $limit, //每页条数
            'num_links' => 4,
            'uri_segment' => 3,
            'use_page_numbers' => true,
            'page_query_string' => true
        );
        $data['pagination'] = $this->pages($pagination_config);

        $this->seo_admin_title = '提现申请列表 - '.WEB_SITE;
        $this->view('/admin/withdraw/index', $data);
    }

    /**
    * 通过
    */
    public function agree() {
        $id = isset($_POST['id']) ? $_POST['id'] : 0;

        $result = $this->withdraw_service->agree($id);
        echo json_encode($result);exit;
    }

    /**
    * 拒绝
    */
    public function refuse() {
        $id = isset($_POST['id']) ? $_POST['id'] : 0;

        $result = $this->withdraw_service->refuse($id);
        echo json_encode($result);exit;
    }

}