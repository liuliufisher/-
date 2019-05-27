<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 轮播图表
 */
class Banner_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->table_name = 'banner';
	}

    public function get_by_id($id){
        $field = '*';
        $where = array();
        $where['id'] = $id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    /**
     * 获取所有Banner
     */
    public function get_banners($user_id = 0){
        $field = 'image_url,url';
        $where = array();
        $where['user_id'] = $user_id;
        $where['is_delete'] = 0;
        $orderby = 'sort asc, create_time desc';
        return $this->query_all($field, $where, $orderby);
    }

}

?>
