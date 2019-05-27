<?php

class Welcome extends SYS_Controller {

    public function __construct() {
        parent::__construct();
        //登录判断
        if(!$this->session->userdata('user')){
            redirect('/admin');
        }
    }

    public function index() {
    	$data = array();
    	$this->seo_admin_title = '首页 | '.WEB_SITE;
        $this->view('/admin/welcome/index', $data);
    }

}

?>
