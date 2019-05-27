<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 商品规格表
 */
class Shop_goods_spec_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->table_name = 'shop_goods_spec';
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
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_all_spec($goods_id){
        $field = '*';
        $where = array();
        $where['goods_id'] = $goods_id;
        $where['is_delete'] = 0;
        return $this->query_all($field, $where, 'id asc');
    }

    public function get_price($goods_id){
        $sql = 'SELECT min(goods_price) as goods_price,min(discount_price) as discount_price from `shop_goods_spec` WHERE goods_id='.$goods_id;

        return $this->query_sql_row($sql);
    }

}

?>
