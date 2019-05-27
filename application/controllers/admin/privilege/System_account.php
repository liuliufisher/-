<?php

class System_account extends SYS_Controller {

    function __construct() {
        parent::__construct();
        //登录判断
        if(!$this->session->userdata('user')){
            redirect('/admin');
        }
        $this->load->model('system_user_model');
        $this->load->library('system_user_service');
    }

    /**
    * 个人资料
    */
    function account_info() {
        $this->my_module = ACCOUNT;
    	$data = array();
        $this->seo_admin_title = '个人资料 - '.WEB_SITE;
        $this->view('/admin/privilege/system_account/account_info', $data);
    }

    /**
    * 修改密码页面
    */
    function edit_password() {
        $this->my_module = ACCOUNT;
    	$data = array();
        $this->seo_admin_title = '修改密码 - '.WEB_SITE;
        $this->view('/admin/privilege/system_account/edit_password', $data);
    }

    /**
    * 修改密码
    */
    function ajax_edit_password() {
        $data = array();
        $user = $this->session->userdata('user');
        $old_password = isset($_POST['old_password']) ? $_POST['old_password'] : '';
        $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confim_password = isset($_POST['confim_password']) ? $_POST['confim_password'] : '';
        
        $result = $this->system_user_service->edit_password($user['user_id'], $old_password, $new_password, $confim_password);
        echo json_encode($result);exit;
    }

    /**
    * 账户管理-列表
    */
    function user_list() {
        $this->my_module = ACCOUNT;
        $data = array();

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $user_name = isset($_GET['user_name']) ? $_GET['user_name'] : '';
        $name = isset($_GET['name']) ? $_GET['name'] : '';

        $field = '*';
        $data = $where = array();
        if ($user_name != null && $user_name != "") {
            $where['user_name like'] = '%' . $user_name . '%';
        }
        if ($name != null && $name != "") {
            $where['name like'] = '%' . $name . '%';
        }
        $where['is_delete'] = 0;
        $orderby = 'id asc';
        $limit = 10;

        $data['user_list'] = $this->system_user_model->query_list($field, $where, $orderby, $limit, $limit * $page - $limit);
        $data['total'] = $this->system_user_model->query_list_count($where);
        $data['per_page'] = $limit;

        $url = '?';
        if ($user_name != null && $user_name != "") {
            $url.='&user_name=' . $user_name;
        }
        if ($name != null && $name != "") {
            $url.='&name=' . $name;
        }

        $pagination_config = array(
            'base_url' => !empty($url) ? '/privilege/user_list'.$url : '/privilege/user_list',
            'total_rows' => $data['total'], //数据总数
            'per_page' => $limit, //每页条数
            'num_links' => 4,
            'uri_segment' => 3,
            'use_page_numbers' => true,
            'page_query_string' => true
        );
        $data['pagination'] = $this->pages($pagination_config);
        $data['user_name'] = $user_name;
        $data['name'] = $name;

        $this->seo_admin_title = '账户列表 - '.WEB_SITE;
        $this->view('/admin/privilege/system_user/index', $data);
    }

    /**
    * 账户管理-详细页
    */
    public function user_detail() {
        $this->my_module = ACCOUNT;
        $data = array();
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        if ($id) {
            $data['system_user'] = $this->system_user_model->get_by_id($id);
        }

        $this->seo_admin_title = '添加账户 - '.WEB_SITE;
        $this->view('/admin/privilege/system_user/detail', $data);
    }

    /**
    * 账户管理-添加更新
    */
    public function update_system_user() {
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        $user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
        $enable_status = isset($_POST['enable_status']) ? $_POST['enable_status'] : 0;
        $remark = isset($_POST['remark']) ? $_POST['remark'] : '';

        $result = $this->system_user_service->update_system_user($id, $user_name, $name, $phone, $email, $gender, $enable_status, $remark);
        echo json_encode($result);exit;
    }

    /**
    * 账户管理-禁用启用操作
    */
    public function update_enable_status() {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $enable_status = isset($_GET['enable_status']) ? $_GET['enable_status'] : 0;

        $this->system_user_service->update_enable_status($id, $enable_status);
        redirect('/privilege/user_list');
    }

}