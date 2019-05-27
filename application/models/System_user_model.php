<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 系统用户表
 */
class System_user_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->table_name = 'system_user';
	}

    public function get_by_id($id){
        $field = '*';
        $where = array();
        $where['id'] = $id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_system_user($user_name, $password = ''){
        $field = '*';
        $where = array();
        $where['user_name'] = $user_name;
        if(!empty($password)){
            $where['password'] = $password;
        }
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_system_user_by_param($user_name = '', $name = '', $phone = '', $email = ''){
        $field = '*';
        $where = array();
        if(!empty($user_name)){
            $where['user_name'] = $user_name;
        }
        if(!empty($name)){
            $where['name'] = $name;
        }
        if(!empty($phone)){
            $where['phone'] = $phone;
        }
        if(!empty($email)){
            $where['email'] = $email;
        }
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

}

?>
