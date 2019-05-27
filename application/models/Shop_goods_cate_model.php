<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 商品分类表
 */
class Shop_goods_cate_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->table_name = 'shop_goods_cate';
	}

    public function get_by_id($id){
        $field = '*';
        $where = array();
        $where['id'] = $id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_by_pid($pid){
        $field = '*';
        $where = array();
        $where['parent_id'] = $pid;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_by_name($cate_name, $user_id){
        $field = '*';
        $where = array();
        $where['cate_name'] = $cate_name;
        $where['user_id'] = $user_id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_goods_cate($user_id){
        $field = 'id, parent_id, cate_name, image_url, sort';
        $where = 'is_delete=0';

        if(!empty($user_id)){
            $where .= ' and user_id='.$user_id;
        }

        $sql = 'SELECT '.$field.' from shop_goods_cate 
        WHERE '.$where.' order by sort asc, create_time desc';

        return $this->query_sql($sql);
    }

    public function get_goods_one_cate($user_id){
        $field = 'id, cate_name, sort';
        $where = 'is_delete=0 and parent_id=0';

        if(!empty($user_id)){
            $where .= ' and user_id='.$user_id;
        }

        $sql = 'SELECT '.$field.' from shop_goods_cate 
        WHERE '.$where.' order by sort asc, create_time desc';

        return $this->query_sql($sql);
    }

}

?>
