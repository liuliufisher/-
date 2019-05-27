<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 图片验证码表
 */
class Pic_code_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->table_name = 'pic_code';
    }

    public function get_by_id($id){
        $field = '*';
        $where = array();
        $where['id'] = $id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_pic_code($time, $code) {
        $field = '*';
        $where = array();
        $where['time'] = $time;
        $where['code'] = $code;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

}

?>
