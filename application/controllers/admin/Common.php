<?php

class Common extends SYS_Controller {

    function __construct() {
        parent::__construct();
        //登录判断
        if(!$this->session->userdata('user')){
            redirect('/admin');
        }
    }

    /**
    * 图片上传
    */
    public function upload_img(){
        $data = $this->get_pic("userfile");
        if(isset($data['file_name'])){
            $image = $this->config->item('domain_www').'/user_guide/upload/pic/'.$data['file_name'];
            exit ($image);
        }else{
            //上传失败返回error图片
            $image = '/user_guide/admin/swfupload/error.jpg';
            exit ($image);
        }
    }

}