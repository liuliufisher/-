<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * formId表
 */
class User_formid_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->table_name = 'user_formid';
    }

    public function get_by_id($id){
        $field = '*';
        $where = array();
        $where['id'] = $id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_formid($user_id){
        $field = '*';
        $where = array();
        $where['user_id'] = $user_id;
        $where['status'] = 0;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

}

?>
