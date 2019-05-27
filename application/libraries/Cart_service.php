<?php
/**
 * 购物车管理业务层
 * @author dingxuehuan
 */
class Cart_service {

	private $CI;

	function __construct() {
		$this->CI = & get_instance ();
        $this->CI->load->model('cart_model');
        $this->CI->load->model('shop_model');
        $this->CI->load->model('shop_goods_model');
        $this->CI->load->model('shop_goods_image_model');
        $this->CI->load->model('shop_goods_spec_model');
	}

	/**
     * 保存购物车
     */
    public function save_cart($token, $shop_domain, $goods_id, $spec_id, $num) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($shop_domain) || empty($goods_id) || empty($spec_id) || empty($num)){
            return output(1001,'参数错误');
        }

    	$goods = $this->CI->shop_goods_model->get_by_id($goods_id);
    	if(empty($goods)){
    		return output(1001,'该商品不存在');
    	}
        //判断店铺域名
        $shop = $this->CI->shop_model->get_by_domain($shop_domain);
        if(empty($shop)){
            return output(1001,'该店铺域名不存在');
        }

        $shop_goods_spec = $this->CI->shop_goods_spec_model->get_by_id($spec_id);
        if(empty($shop_goods_spec)){
            return output(1001,'该规格不存在');
        }
        if($shop_goods_spec->stock_num < $num){
            return output(1001,'该商品库存不足');
        }

    	$cart = $this->CI->cart_model->get_by_gid($user_id, $goods_id);
    	if(empty($cart)){
    		$cart_data = array(
	            'user_id' => $user_id,
                'shop_domain' => $shop_domain,
	            'goods_id' => $goods_id,
                'spec_id' => $spec_id,
	            'num' => $num,
	            'create_time' => date('Y-m-d H:i:s')
	        );
	        $this->CI->cart_model->query_insert($cart_data);
    	}else{
    		$where = array();
            $where['id'] = $cart->id;
            $cart_data['num'] = $cart->num+$num;
            $cart_data['update_time'] = date("Y-m-d H:i:s");
            $this->CI->cart_model->query_update($where, $cart_data);
    	}

    	return output(0,'成功');
    }

    /**
     * 购物车增加数量
     */
    public function add_cart_num($token, $cart_id, $num) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($cart_id)){
            return output(1001,'参数错误');
        }

        $cart = $this->CI->cart_model->get_by_id($cart_id);
        if(empty($cart)){
            return output(1001,'该购物车不存在');
        }
        if($num > 0){
            $where = array();
            $where['id'] = $cart->id;
            $cart_data['num'] = $num;
            $cart_data['update_time'] = date("Y-m-d H:i:s");
            $this->CI->cart_model->query_update($where, $cart_data);
        }else{
            $where = array();
            $where['id'] = $cart->id;
            $cart_data['is_delete'] = 1;
            $cart_data['update_time'] = date("Y-m-d H:i:s");
            $cart_data['delete_time'] = date("Y-m-d H:i:s");
            $this->CI->cart_model->query_update($where, $cart_data);
        }
        

        return output(0,'成功');
    }


	/**
     * 获取购物车列表
     */
    public function get_carts($token, $shop_domain, $page, $per_page) {
    	$this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        //判断店铺域名
        $shop = $this->CI->shop_model->get_by_domain($shop_domain);
        if(empty($shop)){
            return output(1001,'该店铺域名不存在');
        }

    	$carts = $this->CI->cart_model->get_carts($user_id, $shop_domain, $page, $per_page);
    	$result = [];
    	if(!empty($carts)){
    		foreach($carts as $cart){
                $g['cart_id'] = $cart->cart_id;
                $g['goods_id'] = $cart->goods_id;
                $g['goods_name'] = $cart->goods_name;
                $g['cart_num'] = $cart->cart_num;
                $g['goods_price'] = $cart->goods_price;
                $g['goods_image'] = $cart->goods_image;
                $g['spec_name'] = $cart->spec;
                $g['spec_id'] = $cart->spec_id;

    			$result[] = $g;
    		}
    	}

    	return output(0,'成功',$result);
    }


    /**
     * 删除购物车
     */
    public function del_cart($token, $cart_id) {
    	$this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($cart_id)){
            return output(1001,'参数错误');
        }

    	$cart = $this->CI->cart_model->get_by_cid($user_id, $cart_id);
    	if(empty($cart)){
            return output(1001,'该购物车不存在');
    	}
    	
		$where = array();
        $where['id'] = $cart_id;
        $cart_data['is_delete'] = 1;
        $cart_data['update_time'] = date("Y-m-d H:i:s");
        $cart_data['delete_time'] = date("Y-m-d H:i:s");
        $this->CI->cart_model->query_update($where, $cart_data);

    	return output(0,'成功');
    }

    /**
     * 获取购物车数量
     */
    public function get_cart_num($token, $shop_domain) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($shop_domain)){
            return output(1001,'参数错误');
        }

        $user = $this->CI->user_model->get_by_id($user_id);
        if(empty($user)){
            return output(1004,'用户不存在');
        }

        $cart_count = $this->CI->cart_model->get_carts_count($user_id, $shop_domain);

        return output(0,'成功',['total' => $cart_count ? $cart_count->count : 0]);
    }

}