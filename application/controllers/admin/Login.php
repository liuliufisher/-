<?php

class Login extends SYS_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('system_user_service');
    }

    function index() {
        if($this->session->userdata('user')){
            $data = array();
            $this->seo_admin_title = '首页 | '.WEB_SITE;
            $this->view('/admin/welcome/index', $data);
        }
    }

    /**
    * 登录
    */
    public function ajax_login(){
        $data = array();
        $user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : 0;

        $result = $this->system_user_service->login($user_name, $password);

        echo json_encode($result);exit;
    }

    /**
     * 退出
     */
    public function login_out(){
        $this->session->unset_userdata('user');
        redirect("/admin");
    }

}