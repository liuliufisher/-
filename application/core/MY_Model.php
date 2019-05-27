<?php

defined('BASEPATH') or die('No direct script access allowed');

class MY_Model extends CI_Model {

    var $table_name = NULL;

    public function __construct() {
        parent::__construct();
    }

    public function query_list($field = '*', $where = array(), $orderby = 'id DESC', $limit = 20, $offset = 0) {
        $query = $this->db
                ->select($field)
                ->from($this->table_name)
                ->where($where)
                ->limit($limit, $offset)
                ->order_by($orderby)
                ->get()
                ->result();
        return $query;
    }

    public function query_all($field = '*', $where = array(), $orderby = 'id DESC') {
        $query = $this->db
                ->select($field)
                ->from($this->table_name)
                ->where($where)
                ->order_by($orderby)
                ->get()
                ->result();
        return $query;
    }

    public function query_in($field = '*', $where = array(), $orderby = 'id DESC', $in_field = '', $where_array = array()) {
        $query = $this->db
                ->select($field)
                ->from($this->table_name)
                ->where($where)
                ->where_in($in_field, $where_array)
                ->order_by($orderby)
                ->get()
                ->result();
        return $query;
    }

    public function query_list_count($where = array()) {
        $query = $this->db
                ->from($this->table_name)
                ->where($where)
                ->count_all_results();
        return $query;
    }

    public function query_result($field = '*', $where = array(), $orderby = '') {
        $query = $this->db
                ->select($field)
                ->from($this->table_name)
                ->where($where);
        if (!empty($orderby)) {
            $query = $this->db->order_by($orderby);
        }
        $query = $this->db->get()
                ->row();
        return $query;
    }

    public function query_insert($data = array()) {
        $this->db
                ->insert($this->table_name, $data);
        return $this->db->insert_id();
    }

    public function query_update($where = array(), $data = array()) {
        $query = $this->db
                ->where($where)
                ->update($this->table_name, $data);
        return $query;
    }

    public function query_delete($where = array()) {
        $query = $this->db
                ->where($where)
                ->delete($this->table_name);
        return $query;
    }

    public function query_sql($sql) {
        $query = $this->db->query($sql);
        $result = $query->result();
        $query->free_result();
        return $result;
    }

    public function query_sql_update($sql) {
        $this->db->query($sql);
    }

    public function query_sql_delete($sql) {
        $this->db->query($sql);
    }

    public function query_sql_count($sql) {
        $query = $this->db->query($sql);
        return $query->row()->numrows;
    }

    public function query_sql_row($sql) {
        $query = $this->db->query($sql);
        $result = $query->row();
        $query->free_result();
        return $result;
    }

    public function query_list_or($field = '*', $where = array(), $or_where = array(), $orderby = 'id DESC', $limit = 20, $offset = 0) {
        $query = $this->db
            ->select($field)
            ->from($this->table_name)
            ->where($where)
            ->or_where($or_where)
            ->limit($limit, $offset)
            ->order_by($orderby)
            ->get()
            ->result();
        return $query;
    }

    public function query_list_or_count($where = array(), $or_where = array()) {
        $query = $this->db
            ->from($this->table_name)
            ->where($where)
            ->or_where($or_where)
            ->count_all_results();
        return $query;
    }

    /**
     * 手动开始事务
     */
    public function trans_begin(){
        $this->db->trans_begin();
    }

    /**
     * 手动回滚事务
     */
    public function trans_rollback(){
        $this->db->trans_rollback();
    }

    /**
     * 手动提交事务
     */
    public function trans_commit(){
        $this->db->trans_commit();
    }

}