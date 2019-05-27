<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 店铺订单明细表
 */
class Shop_order_detail_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->table_name = 'shop_order_detail';
	}

    public function get_by_id($id){
        $field = '*';
        $where = array();
        $where['id'] = $id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_order_details($order_no){
        $field = '*';
        $where = array();
        $where['order_no'] = $order_no;
        $where['is_delete'] = 0;
        $orderby = 'create_time desc';
        return $this->query_all($field, $where);
    }

}

?>
