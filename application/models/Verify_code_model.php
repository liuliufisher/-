<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 验证码表
 */
class Verify_code_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->table_name = 'verify_code';
    }

    public function get_verify_code($mobile, $code = '') {
        $field = '*';
        $where = array();
        $where['mobile'] = $mobile;
        if(!empty($code)){
            $where['code'] = $code;
        }
        return $this->query_result($field, $where);
    }

}

?>
