<?php

defined('BASEPATH') or die('No direct script access allowed');

/**
 * 商品表
 */
class Shop_goods_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->table_name = 'shop_goods';
	}

    public function get_by_id($id){
        $field = '*';
        $where = array();
        $where['id'] = $id;
        $where['is_delete'] = 0;
        return $this->query_result($field, $where);
    }

    public function get_index_goods($user_id){
        $field = 'sg.id as goods_id, sg.goods_name,sg.goods_image,sg.cate_id,sgc.cate_name';
        $time = date('Y-m-d');

        $sql = 'SELECT '.$field.' FROM shop_goods sg
                LEFT JOIN shop s ON sg.user_id = s.user_id
                LEFT JOIN shop_goods_cate sgc ON sg.cate_id = sgc.id
                WHERE sg.is_delete = 0 AND sg.is_hot = 1 and sg.status != 3 and s.expire_time>="'.$time.'" and sg.user_id='.$user_id;

        return $this->query_sql($sql);
    }

    public function get_cate_goods($user_id, $cate_id='', $keyword = '', $page=1, $per_page=20, $field='*'){
        $limit = $per_page * $page - $per_page;
        $offset = $per_page;
        $time = date('Y-m-d');

        $where = 'sg.is_delete = 0 and sg.status = 1 and s.expire_time>="'.$time.'" and sg.user_id='.$user_id;
        if(!empty($cate_id)){
            $where .= ' and sg.cate_id='.$cate_id;
        }
        if (!empty($keyword)) {
            $where .= ' and sg.goods_name like "%' . $keyword . '%"';
        }

        $orderby = 'sg.create_time desc';

        $sql = 'SELECT '.$field.' FROM shop_goods sg
                LEFT JOIN shop s ON sg.user_id = s.user_id
                LEFT JOIN shop_goods_cate sgc ON sg.cate_id = sgc.id
                WHERE '.$where.' order by '.$orderby.' limit '.$limit.', '.$offset;

        return $this->query_sql($sql);
    }

    public function get_cate_goods_count($user_id, $cate_id='', $keyword = '', $field='count(*) as count'){
        $time = date('Y-m-d');
        $where = 'sg.is_delete = 0 and sg.status = 1 and s.expire_time>="'.$time.'" and sg.user_id='.$user_id;
        if(!empty($cate_id)){
            $where .= ' and sg.cate_id='.$cate_id;
        }
        if (!empty($keyword)) {
            $where .= ' and sg.goods_name like "%' . $keyword . '%"';
        }

        $sql = 'SELECT '.$field.' FROM shop_goods sg
                LEFT JOIN shop s ON sg.user_id = s.user_id
                LEFT JOIN shop_goods_cate sgc ON sg.cate_id = sgc.id
                WHERE '.$where;

        return $this->query_sql_row($sql);
    }

    public function get_shop_goods_list($user_id, $status='', $keyword = '', $page=1, $per_page=20, $field='*'){
        $limit = $per_page * $page - $per_page;
        $offset = $per_page;

        $where = 'is_delete = 0 and user_id='.$user_id.' and status='.$status;
        if (!empty($keyword)) {
            $where .= ' and goods_name like "%' . $keyword . '%"';
        }

        $orderby = 'create_time desc';

        $sql = 'SELECT '.$field.' FROM shop_goods 
                WHERE '.$where.' order by '.$orderby.' limit '.$limit.', '.$offset;

        return $this->query_sql($sql);
    }

    public function get_shop_goods_count($user_id, $status='', $keyword = ''){
        $field = 'count(*) as count';

        $where = 'is_delete = 0 and user_id='.$user_id.' and status='.$status;
        if (!empty($keyword)) {
            $where .= ' and goods_name like "%' . $keyword . '%"';
        }

        $sql = 'SELECT '.$field.' FROM shop_goods 
                WHERE '.$where;

        return $this->query_sql_row($sql);
    }

    public function get_goods_list($keyword, $page, $per_page){
        $field = 'sg.*,u.avatar_url,u.nick_name';
        $where = 'sg.is_delete=0';

        if (!empty($keyword)) {
            $where .= ' and sg.goods_name like "%' . $keyword . '%"';
        }

        $orderby = 'sg.create_time desc';

        $limit = $per_page * $page - $per_page;
        $offset = $per_page;

        $sql = 'SELECT '.$field.' from shop_goods sg
        LEFT JOIN user u on sg.user_id=u.id
        WHERE '.$where.' order by '.$orderby.' limit '.$limit.', '.$offset;

        return $this->query_sql($sql);
    }

    public function get_goods_list_count($keyword){
        $field = 'count(*) as count';
        $where = 'sg.is_delete=0';

        if (!empty($keyword)) {
            $where .= ' and sg.goods_name like "%' . $keyword . '%"';
        }

        $sql = 'SELECT '.$field.' from shop_goods sg
        LEFT JOIN user u on sg.user_id=u.id
        WHERE '.$where;

        return $this->query_sql_row($sql);
    }

}

?>
