<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 店铺订单表
 */
class Shop_order_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->table_name = 'shop_order';
	}

    public function get_by_id($id){
        $field = '*';
        $where = array();
        $where['id'] = $id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_by_orderno($order_no){
        $field = '*';
        $where = array();
        $where['order_no'] = $order_no;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_orders($user_id, $shop_domain, $status, $page, $per_page){
        $field = 'order_no,shop_domain,price as all_price,status,logistics_no,freight,pay_time,user_id,remark,shop_remark';
        $where = 'is_delete=0';

        if(!empty($user_id)){
            $where .= ' and user_id='.$user_id;
        }
        if(!empty($shop_domain)){
            $where .= ' and shop_domain="'.$shop_domain.'"';
        }
        if(!empty($status)){
            $where .= ' and status='.$status;
        }

        $limit = $per_page * $page - $per_page;
        $offset = $per_page;

        $sql = 'SELECT '.$field.' from shop_order 
        WHERE '.$where.' order by create_time desc limit '.$limit.', '.$offset;

        return $this->query_sql($sql);
    }

    public function get_orders_count($user_id, $shop_domain, $status){
        $field = 'count(*) as count';
        $where = 'is_delete=0';

        if(!empty($user_id)){
            $where .= ' and user_id='.$user_id;
        }
        if(!empty($shop_domain)){
            $where .= ' and shop_domain="'.$shop_domain.'"';
        }
        if(!empty($status)){
            $where .= ' and status='.$status;
        }

        $sql = 'SELECT '.$field.' from shop_order 
        WHERE '.$where;

        return $this->query_sql_row($sql);
    }

    public function get_today_shop_order($shop_domain){
        $start_time = date('Y-m-d').' 00:00:00';
        $end_time = date('Y-m-d').' 23:59:59';
        $sql = 'SELECT sum(price) as today_pay,count(*) as pay_order_count from shop_order WHERE shop_domain="'.$shop_domain.'" and is_delete=0 and status>1 and pay_time >="'.$start_time.'" and pay_time <="'.$end_time.'"';
        return $this->query_sql_row($sql);
    }

    public function get_delivery_num($shop_domain){
        $sql = 'SELECT count(*) as num from shop_order WHERE shop_domain="'.$shop_domain.'" and is_delete=0 and status=2';
        return $this->query_sql_row($sql);
    }

    public function get_pay_order_num($shop_domain){
        $start_time = date('Y-m-d').' 00:00:00';
        $end_time = date('Y-m-d').' 23:59:59';

        $sql = 'SELECT sum(sod.num) as num FROM shop_order so
                LEFT JOIN shop_order_detail sod ON so.order_no = sod.order_no
                WHERE so.shop_domain="'.$shop_domain.'" and so.is_delete=0 and so.status>1 and so.pay_time >="'.$start_time.'" and so.pay_time <="'.$end_time.'" and sod.is_delete=0';

        return $this->query_sql_row($sql);
    }

    public function get_unpay_orders($status){
        $field = '*';
        $where = 'is_delete=0 and status='.$status;

        $sql = 'SELECT '.$field.' from shop_order 
        WHERE '.$where.' order by create_time desc';

        return $this->query_sql($sql);
    }

    public function get_shop_order_list($page, $per_page){
        $field = 'so.*,u.avatar_url,u.nick_name,s.shop_name';
        $where = 'so.is_delete=0';
        $orderby = 'so.create_time desc';

        $limit = $per_page * $page - $per_page;
        $offset = $per_page;

        $sql = 'SELECT '.$field.' from shop_order so
        LEFT JOIN user u on so.user_id=u.id
        LEFT JOIN shop s on so.shop_domain=s.shop_domain
        WHERE '.$where.' order by '.$orderby.' limit '.$limit.', '.$offset;

        return $this->query_sql($sql);
    }

    public function get_shop_order_list_count(){
        $field = 'count(*) as count';
        $where = 'so.is_delete=0';

        $sql = 'SELECT '.$field.' from shop_order so
        LEFT JOIN user u on so.user_id=u.id
        LEFT JOIN shop s on so.shop_domain=s.shop_domain
        WHERE '.$where;

        return $this->query_sql_row($sql);
    }

}

?>
