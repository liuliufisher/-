<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * MY_Controller
 */
class MY_Controller extends CI_Controller {

    public $template = 'template/default';
    public $seo_title = '';
    public $seo_keywords = '';
    public $seo_description = '';
    public $view_file = null; //视图文件
    public $view_data = array(); //要渲染的数据
    public $styles = array();
    public $scripts = array();
    public $module;
    public $controller;
    public $method;

    public function __construct() {
        parent::__construct();
        $this->benchmark->mark('my_controller_start');
        $this->load->helper('cookie');
        $this->benchmark->mark('my_controller_end');
    }

    public function view($view, $data = null, $return = FALSE) {
        if ($data != null) {
            $this->view_data = array_merge($this->view_data, $data);
        }
        $this->view_data['seo_title'] = $this->seo_title;
        $this->view_data['seo_keywords'] = $this->seo_keywords;
        $this->view_data['seo_description'] = $this->seo_description;

        $this->view_data['template_content'] = APPPATH . 'views' . $view . '.php';
        $this->view_data['debug'] = $this->view_data;
        $this->load->view($this->template, $this->view_data, $return);
    }

    /**
     * 获取图片
     * @param type $pic_name
     * @return string
     */
    public function get_file($pic_name = 'file', $path = './user_guide/upload/pic/') {
        if (empty($_FILES[$pic_name]['tmp_name'])) {
            return '';
        }
        //文件中文名处理成英文名
        $this->load->library('util_pinyin');
        $file_name = $this->util_pinyin->get($_FILES[$pic_name]['name'],'utf-8');
        $_FILES[$pic_name]['name'] = date('YmdHis').$file_name;

        $config['upload_path'] = $path;
        $config['allowed_types'] = 'jpg|jpeg|gif|bmp|png|doc|docx|xls|xlsx|ppt|pptx|pdf|txt|rar|zip|swf|mp4|avi|mov|ram|AVI|webm|MP4';
        $config['max_size'] = '20480';
        $config['max_width'] = '20480';
        $config['max_height'] = '20480';
        $this->load->library('upload', $config);
        $this->upload->do_upload($pic_name);
        $data = $this->upload->data();
        return $data;
    }

}

class SYS_Controller extends MY_Controller {

    public $template = 'admin/default';
    public $seo_admin_title = '';
    public $view_file = null; //视图文件
    public $view_data = array(); //要渲染的数据
    public $system_user = array();
    public $module;
    public $my_module;

    public function __construct() {
        parent::__construct();
        //登录判断
        if(!$this->session->userdata('user')){
            $this->load->view('admin/login/index');
        }
    }

    public function view($view, $data = null, $return = FALSE) {
        if ($data != null) {
            $this->view_data = array_merge($this->view_data, $data);
        }

        //获取后台用户信息
        $user = $this->session->userdata('user');
        $this->load->model('system_user_model');
        $this->system_user = $this->system_user_model->get_by_id($user['user_id']);

        $this->view_data['seo_admin_title'] = $this->seo_admin_title;
        $this->view_data['template_content'] = APPPATH . 'views' . $view . '.php';
        $this->view_data['login_system_user'] = $this->system_user;
        $this->view_data['my_module'] = $this->my_module;
        $this->load->view($this->template, $this->view_data, $return);
    }

    public function pages($pagination_config = array()) {
        $this->load->library('pagination');
        $this->pagination->initialize($pagination_config);
        return $this->pagination->create_links();
    }

    /**
     * 获取图片
     * @param type $pic_name
     * @return string
     */
    public function get_pic($pic_name = 'file') {
        if (empty($_FILES[$pic_name]['tmp_name'])) {
            return '';
        }
        //文件中文名处理成英文名
        $this->load->library('util_pinyin');
        $file_name = $this->util_pinyin->get($_FILES[$pic_name]['name'],'utf-8');
        $_FILES[$pic_name]['name'] = date('YmdHis').$file_name;

        $config['upload_path'] = './user_guide/upload/pic/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = '20480';
        $config['max_width'] = '20480';
        $config['max_height'] = '20480';
        $this->load->library('upload', $config);
        $this->upload->do_upload($pic_name);
        $data = $this->upload->data();
        return $data;
    }

}