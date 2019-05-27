<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 用户表
 */
class User_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->table_name = 'user';
    }

    public function get_by_id($id){
        $field = '*';
        $where = array();
        $where['id'] = $id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_by_openid($openid){
        $field = '*';
        $where = array();
        $where['openid'] = $openid;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_by_mobile($mobile, $type = ''){
        $field = '*';
        $where = 'is_delete=0 and mobile='.$mobile;

        if (!empty($type)) {
            $where .= ' and type in ('.$type.')';
        }

        $sql = 'SELECT '.$field.' from user
        WHERE '.$where;

        return $this->query_sql_row($sql);
    }

    public function get_user_list($keyword, $page, $per_page){
        $field = '*';
        $where = 'is_delete=0';

        if (!empty($keyword)) {
            $where .= ' and (mobile ="' . $keyword . '" or nick_name ="'.$keyword.'")';
        }
        $orderby = 'create_time desc';

        $limit = $per_page * $page - $per_page;
        $offset = $per_page;

        $sql = 'SELECT '.$field.' from `user` 
        WHERE '.$where.' order by '.$orderby.' limit '.$limit.', '.$offset;

        return $this->query_sql($sql);
    }

    public function get_user_list_count($keyword){
        $field = 'count(*) as count';
        $where = 'is_delete=0';

        if (!empty($keyword)) {
            $where .= ' and (mobile ="' . $keyword . '" or nick_name ="'.$keyword.'")';
        }

        $sql = 'SELECT '.$field.' from `user` 
        WHERE '.$where;

        return $this->query_sql_row($sql);
    }

    public function get_users($field='*'){
        $where = array();
        $where['is_delete'] = 0;
        return $this->query_all($field, $where);
    }

    public function get_users_count($time='',$start_time='',$end_time=''){
        $where = array();
        $where['is_delete'] = 0;
        if (!empty($time)) {
            $where['create_time >='] = $time.' 00:00:00';
            $where['create_time <='] = $time.' 23:59:59';
        }
        if (!empty($start_time)) {
            $where['create_time >='] = $start_time.' 00:00:00';
        }
        if (!empty($end_time)) {
            $where['create_time <='] = $end_time.' 23:59:59';
        }
        return $this->query_list_count($where);
    }

    public function get_expire_users($field='*'){
        $time = date('Y-m-d');
        $sql = 'SELECT '.$field.' from `user` WHERE expire_time < "'.$time.'" and is_delete=0 and remaid_ratio>0';
        return $this->query_sql($sql);
    }

    public function get_all_superior_user($superior_user_id){
        $sql = 'SELECT count(*) as count  FROM user
                WHERE superior_user_id='.$superior_user_id.' and is_delete=0';

        return $this->query_sql_row($sql);
    }

    public function get_visitors($user_id, $type, $page=1, $per_page=20, $field='*'){
        $limit = $per_page * $page - $per_page;
        $offset = $per_page;

        $where = 'is_delete = 0 and superior_user_id='.$user_id;

        if($type == 1){
            $where .= ' and open_shop_time is not null';
        }
        $orderby = 'create_time desc';

        $sql = 'SELECT '.$field.' FROM user 
                WHERE '.$where.' order by '.$orderby.' limit '.$limit.', '.$offset;

        return $this->query_sql($sql);
    }

    public function get_visitors_count($user_id, $type, $field='count(*) as count'){

        $where = 'is_delete = 0 and superior_user_id='.$user_id;

        if($type == 1){
            $where .= ' and open_shop_time is not null';
        }

        $sql = 'SELECT '.$field.' FROM user 
                WHERE '.$where;

        return $this->query_sql_row($sql);
    }

}

?>
