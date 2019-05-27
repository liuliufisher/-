<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 购物车表
 */
class Cart_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->table_name = 'cart';
	}

    public function get_by_id($id){
        $field = '*';
        $where = array();
        $where['id'] = $id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_by_cid($user_id, $cart_id){
        $field = '*';
        $where = array();
        $where['user_id'] = $user_id;
        $where['id'] = $cart_id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_by_gid($user_id, $goods_id){
        $field = '*';
        $where = array();
        $where['user_id'] = $user_id;
        $where['goods_id'] = $goods_id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_by_uid($user_id, $shop_domain){
        $field = '*';
        $where = array();
        $where['user_id'] = $user_id;
        $where['shop_domain'] = $shop_domain;
        $where['is_delete'] = 0;
        return $this->query_all($field, $where);
    }

    public function get_carts($user_id, $shop_domain, $page, $per_page){
        $field = 'c.id as cart_id,c.num as cart_num,c.spec_id, sg.id as goods_id, sg.goods_name, sg.goods_image,sd.spec,sd.goods_price';
        $where = 'c.is_delete=0 and c.shop_domain="'.$shop_domain.'"';

        if(!empty($user_id)){
            $where .= ' and c.user_id='.$user_id;
        }

        $limit = $per_page * $page - $per_page;
        $offset = $per_page;

        $sql = 'SELECT '.$field.' from cart c 
        LEFT JOIN shop_goods sg ON c.goods_id=sg.id
        LEFT JOIN shop_goods_spec sd ON c.spec_id=sd.id
        WHERE '.$where.' order by c.create_time desc limit '.$limit.', '.$offset;

        return $this->query_sql($sql);
    }

    public function get_carts_count($user_id, $shop_domain){
        $field = 'count(*) as count';
        $where = 'c.is_delete=0 and c.shop_domain="'.$shop_domain.'"';

        if(!empty($user_id)){
            $where .= ' and c.user_id='.$user_id;
        }

        $sql = 'SELECT '.$field.' from cart c 
        LEFT JOIN shop_goods sg ON c.goods_id=sg.id
        LEFT JOIN shop_goods_spec sd ON c.spec_id=sd.id
        WHERE '.$where;

        return $this->query_sql_row($sql);
    }

}

?>
