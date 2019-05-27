<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 产品表
 */
class Product_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->table_name = 'product';
    }

    public function get_by_id($id){
        $field = '*';
        $where = array();
        $where['id'] = $id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_products(){
        $field = 'id as product_id,name,price,reward,is_hot,remark';
        $where = array();
        $where['is_delete'] = 0;
        $orderby = 'id asc';
        return $this->query_all($field, $where, $orderby);
    }

}

?>
