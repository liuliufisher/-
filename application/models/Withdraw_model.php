<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 提现表
 */
class Withdraw_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->table_name = 'withdraw';
    }

    public function get_by_id($id){
        $field = '*';
        $where = array();
        $where['id'] = $id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_withdraw_list($page, $per_page){
        $field = 'w.*,u.nick_name';
        $where = 'w.is_delete=0';
        $orderby = 'w.create_time desc';

        $limit = $per_page * $page - $per_page;
        $offset = $per_page;

        $sql = 'SELECT '.$field.' from `withdraw` w 
        left join user u on w.user_id=u.id
        WHERE '.$where.' order by '.$orderby.' limit '.$limit.', '.$offset;

        return $this->query_sql($sql);
    }

    public function get_withdraw_list_count(){
        $field = 'count(*) as count';
        $where = 'w.is_delete=0';

        $sql = 'SELECT '.$field.' from `withdraw` w 
        left join user u on w.user_id=u.id
        WHERE '.$where;

        return $this->query_sql_row($sql);
    }

    public function get_withdraw_count($user_id){
        $field = 'count(*) as count';
        $today_start = date('Y-m-d').' 00:00:00';
        $today_end = date('Y-m-d').' 23:59:59';
        $where = 'is_delete=0 and user_id='.$user_id.' and create_time>="'.$today_start.'" and create_time<="'.$today_end.'"';

        $sql = 'SELECT '.$field.' from `withdraw`  
        WHERE '.$where;

        return $this->query_sql_row($sql);
    }

}

?>
