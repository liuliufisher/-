<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 资金流水表
 */
class Capital_flow_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->table_name = 'capital_flow';
    }

    public function get_by_id($id){
        $field = '*';
        $where = array();
        $where['id'] = $id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_capital_flows($user_id, $page=1, $per_page=20, $field='*'){
        $limit = $per_page * $page - $per_page;
        $offset = $per_page;

        $where = 'is_delete = 0 and user_id='.$user_id;
        $orderby = 'create_time desc';

        $sql = 'SELECT '.$field.' FROM capital_flow 
                WHERE '.$where.' order by '.$orderby.' limit '.$limit.', '.$offset;

        return $this->query_sql($sql);
    }

    public function get_capital_flows_count($user_id, $field='count(*) as count'){
        $where = 'is_delete = 0 and user_id='.$user_id;
        $sql = 'SELECT '.$field.' FROM capital_flow 
                WHERE '.$where;

        return $this->query_sql_row($sql);
    }

}

?>
