<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 会员订单表
 */
class User_order_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->table_name = 'user_order';
	}

    public function get_by_id($id){
        $field = '*';
        $where = array();
        $where['id'] = $id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_by_no($order_no){
        $field = '*';
        $where = array();
        $where['order_no'] = $order_no;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_yesterday_user_order($superior_user_id){
        $start_time = date("Y-m-d",strtotime("-1 day")).' 00:00:00';
        $end_time = date("Y-m-d",strtotime("-1 day")).' 23:59:59';

        $sql = 'SELECT sum(uo.price) as price,count(u.id) as count  FROM user_order uo
                LEFT JOIN user u ON uo.user_id = u.id 
                WHERE u.superior_user_id='.$superior_user_id.' and u.is_delete=0 and uo.status=2 and u.open_shop_time >="'.$start_time.'" and u.open_shop_time <="'.$end_time.'" and uo.is_delete=0';

        return $this->query_sql_row($sql);
    }

    public function get_all_user_order($superior_user_id){
        $sql = 'SELECT count(u.id) as count  FROM user_order uo
                LEFT JOIN user u ON uo.user_id = u.id 
                WHERE u.superior_user_id='.$superior_user_id.' and u.is_delete=0 and uo.status=2 and uo.is_delete=0';

        return $this->query_sql_row($sql);
    }

    public function get_order_list($page, $per_page){
        $field = 'uo.*,u.avatar_url,u.nick_name,p.name as product_name';
        $where = 'uo.is_delete=0 and uo.status=2';
        $orderby = 'uo.create_time desc';

        $limit = $per_page * $page - $per_page;
        $offset = $per_page;

        $sql = 'SELECT '.$field.' from user_order uo
        LEFT JOIN user u on uo.user_id=u.id
        LEFT JOIN product p on uo.product_id=p.id
        WHERE '.$where.' order by '.$orderby.' limit '.$limit.', '.$offset;

        return $this->query_sql($sql);
    }

    public function get_order_list_count(){
        $field = 'count(*) as count';
        $where = 'uo.is_delete=0 and uo.status=2';

        $sql = 'SELECT '.$field.' from user_order uo
        LEFT JOIN user u on uo.user_id=u.id
        LEFT JOIN product p on uo.product_id=p.id
        WHERE '.$where;

        return $this->query_sql_row($sql);
    }

}

?>
