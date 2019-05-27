<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 用户收货地址表
 */
class User_address_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->table_name = 'user_address';
	}

    public function get_by_id($id){
        $field = '*';
        $where = array();
        $where['id'] = $id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_address($id, $user_id, $field = '*'){
        $where = array();
        $where['id'] = $id;
        $where['user_id'] = $user_id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function update_default_address($user_id){
        $data = $where = array();
        $where['user_id'] = $user_id;
        $data['is_default'] = 0;
        return $this->query_update($where, $data);
    }

    public function get_addresses($user_id){
        $field = 'id, name, mobile, province_code, province_name, city_code, city_name, area_code, area_name, address, is_default';
        $where = 'is_delete=0';

        if(!empty($user_id)){
            $where .= ' and user_id='.$user_id;
        }

        $sql = 'SELECT '.$field.' from user_address 
        WHERE '.$where.' order by create_time desc';

        return $this->query_sql($sql);
    }

    public function get_default_address($user_id, $field = '*'){
        $where = array();
        $where['is_default'] = 1;
        $where['user_id'] = $user_id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

}

?>
