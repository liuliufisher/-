<?php
/**
 * Banner设置业务层
 * @author dingxuehuan
 */
class Banner_service {

	private $CI;

	function __construct() {
		$this->CI = & get_instance ();
        $this->CI->load->model('banner_model');
	}

    /**
     * Banner设置-添加更新
     * @author dingxuehuan
    */
    public function update_banner($id, $image_url, $sort){
        $data = array();
        
        $banner_data = array(
            'id' => $id,
            'image_url' => $image_url,
            'sort' => $sort
        );

        if (empty($id)) {
            $banner_data['create_time'] = date("Y-m-d H:i:s");
            $this->CI->banner_model->query_insert($banner_data);
        } else {
            $where = array();
            $where['id'] = $id;
            $banner_data['update_time'] = date("Y-m-d H:i:s");
            $this->CI->banner_model->query_update($where, $banner_data);
        }

        $data = array(
            'error_code' => 0,
            'error_msg' => '成功'
        );
        return $data;
    }

    /**
    * Banner设置-删除操作
    */
    public function delete_banner($id) {
        $data = array();
        $user = $this->CI->session->userdata('user');
        if (empty($user)) {
            $data = array(
                'error_code' => 1,
                'error_msg' => '请先登录'
            );
        }

        $banner_data = $where = array();
        $where['id'] = $id;
        $banner_data['is_delete'] = 1;
        $banner_data['update_time'] = date("Y-m-d H:i:s");
        $banner_data['delete_time'] = date("Y-m-d H:i:s");
        $this->CI->banner_model->query_update($where, $banner_data);

        $data = array(
            'error_code' => 0,
            'error_msg' => '成功'
        );
        return $data;
    }

}