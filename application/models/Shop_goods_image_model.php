<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 商品图片表
 */
class Shop_goods_image_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->table_name = 'shop_goods_image';
	}

    public function get_by_id($id){
        $field = '*';
        $where = array();
        $where['id'] = $id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_by_gid($goods_id){
        $field = '*';
        $where = array();
        $where['goods_id'] = $goods_id;
        $where['type'] = 1;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_all_image($goods_id, $type=1){
        $field = '*';
        $where = array();
        $where['goods_id'] = $goods_id;
        $where['type'] = $type;
        $where['is_delete'] = 0;
        return $this->query_all($field, $where);
    }

}

?>
