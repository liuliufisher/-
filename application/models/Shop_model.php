<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 店铺表
 */
class Shop_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->table_name = 'shop';
	}

    public function get_by_id($id){
        $field = '*';
        $where = array();
        $where['id'] = $id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_by_domain($domain){
        $field = '*';
        $where = array();
        $where['shop_domain'] = $domain;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_by_mobile($mobile){
        $field = '*';
        $where = array();
        $where['mobile'] = $mobile;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_by_uid($user_id){
        $field = '*';
        $where = array();
        $where['user_id'] = $user_id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_carts($user_id, $page, $per_page){
        $field = 'c.id as cart_id,c.num as cart_num, sg.id as goods_id, sg.goods_name,sd.spec_json,sd.goods_price';
        $where = 'c.is_delete=0 and c.shop_domain="'.$shop_domain.'"';

        if(!empty($user_id)){
            $where .= ' and c.user_id='.$user_id;
        }

        $limit = $per_page * $page - $per_page;
        $offset = $per_page;

        $sql = 'SELECT '.$field.' from cart c 
        LEFT JOIN shop_goods sg ON c.goods_id=sg.id
        LEFT JOIN shop_goods_spec_detail sd ON c.spec_detail_id=sd.id
        WHERE '.$where.' order by c.create_time desc limit '.$limit.', '.$offset;

        return $this->query_sql($sql);
    }

    public function get_shops(){
        $field = '*';
        $time = date('Y-m-d H:i:s');
        $where = 'is_delete=0 and expire_time>"'.$time.'"';

        $sql = 'SELECT '.$field.' from shop 
        WHERE '.$where.' order by create_time desc';

        return $this->query_sql($sql);
    }

    public function get_shop_list($page, $per_page){
        $field = '*';
        $where = 'is_delete=0';
        $orderby = 'create_time desc';

        $limit = $per_page * $page - $per_page;
        $offset = $per_page;

        $sql = 'SELECT '.$field.' from shop 
        WHERE '.$where.' order by '.$orderby.' limit '.$limit.', '.$offset;

        return $this->query_sql($sql);
    }

    public function get_shop_list_count(){
        $field = 'count(*) as count';
        $where = 'is_delete=0';

        $sql = 'SELECT '.$field.' from shop 
        WHERE '.$where;

        return $this->query_sql_row($sql);
    }

}

?>
