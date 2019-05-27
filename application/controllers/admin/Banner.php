<?php

class Banner extends SYS_Controller {

    function __construct() {
        parent::__construct();
        //登录判断
        if(!$this->session->userdata('user')){
            redirect($this->config->item('domain_www').'/admin');
        }
        $this->load->model('banner_model');
        $this->load->library('banner_service');
        $this->my_module = BANNER;
    }

    /**
    * Banner设置-列表
    */
    function banner_list() {
        $data = array();
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        $field = '*';
        $data = $where = array();
        $where['is_delete'] = 0;
        $orderby = 'id asc';
        $limit = 10;

        $data['banner_list'] = $this->banner_model->query_list($field, $where, $orderby, $limit, $limit * $page - $limit);
        $data['total'] = $this->banner_model->query_list_count($where);
        $data['per_page'] = $limit;

        $url = '';
        $pagination_config = array(
            'base_url' => !empty($url) ? $this->config->item('domain_www').'/admin/banner/banner_list'.$url : $this->config->item('domain_www').'/admin/banner/banner_list',
            'total_rows' => $data['total'], //数据总数
            'per_page' => $limit, //每页条数
            'num_links' => 4,
            'uri_segment' => 3,
            'use_page_numbers' => true,
            'page_query_string' => true
        );
        $data['pagination'] = $this->pages($pagination_config);

        $this->seo_admin_title = 'Banner列表 - '.WEB_SITE;
        $this->view('/admin/banner/index', $data);
    }

    /**
    * Banner设置-详细页
    */
    public function banner_detail() {
        $data = array();
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        if ($id) {
            $data['banner'] = $this->banner_model->get_by_id($id);
        }

        $this->seo_admin_title = 'Banner设置 - '.WEB_SITE;
        $this->view('/admin/banner/detail', $data);
    }

    /**
    * Banner设置-添加更新
    */
    public function update_banner() {
        $id = isset($_POST['id']) ? $_POST['id'] : 0;

        $picture = isset($_POST['pic']) ? $_POST['pic'] : '';
        $pic = $this->get_pic("userfile");
        if(empty($pic) && !empty($picture)){
            $image_url = $picture;
        }elseif(!empty($pic)){
            $image_url = '/user_guide/upload/pic/'.$pic['file_name'];
        }else{
            $image_url = '';
        }

        $sort = isset($_POST['sort']) ? $_POST['sort'] : 0;

        $result = $this->banner_service->update_banner($id, $image_url, $sort);
        redirect($this->config->item('domain_www').'/admin/banner/banner_list');
    }

    /**
    * 上传文件
    */
    public function uploadfile(){
        $pic = $this->get_pic();
        echo json_encode($pic);exit;
    }

    /**
    * Banner设置-删除操作
    */
    public function delete_banner() {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;

        $this->banner_service->delete_banner($id);
        redirect($this->config->item('domain_www').'/admin/banner/banner_list');
    }


}