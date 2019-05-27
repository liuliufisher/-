<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 验证码发送日志表
 */
class Sms_code_log_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->table_name = 'sms_code_log';
    }

    public function get_count($mobile){
    	$start_time = date('Y-m-d 00:00:00');
        $end_time = date('Y-m-d 23:59:59');
    	$sql = 'select count(*) as count from sms_code_log where mobile='.$mobile.' and create_time>="'.$start_time.'" and create_time<="'.$end_time.'"';

    	return $this->query_sql_row($sql);
    }

}

?>
