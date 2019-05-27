<?php
/**
 * 用户管理业务层
 * @author dingxuehuan
 */
class User_service {

	private $CI;

	function __construct() {
		$this->CI = & get_instance ();
        $this->CI->load->model('user_model');
        $this->CI->load->model('shop_model');
        $this->CI->load->model('cart_model');
        $this->CI->load->model('shop_order_model');
        $this->CI->load->model('user_order_model');
        $this->CI->load->model('user_address_model');
	}

	/**
     * 小程序登录
     */
    public function wxlogin($code, $nick_name, $avatar_url, $shop_domain, $uid) {
        if(empty($code) || empty($nick_name) || empty($avatar_url)){
            return output(1001,'参数错误');
        }
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.WECHAT_APPID.'&secret='.WECHAT_SECRET.'&js_code='.$code.'&grant_type=authorization_code';
        $weixin = file_get_contents($url);
        $jsondecode = json_decode($weixin);
        $array = get_object_vars($jsondecode);
        $openid = isset($array['openid']) ? $array['openid'] : '';
        
        if(!empty($openid)){
            $user = $this->CI->user_model->get_by_openid($openid);
            $superior_user_id = 0;
            if(empty($user)){
                if(!empty($uid)){
                    $superior_user_id = $uid;
                }
                if(!empty($shop_domain)){
                    $check_domain = $this->CI->shop_model->get_by_domain($shop_domain);
                    if(!empty($check_domain)){
                        $superior_user_id = $check_domain->user_id;
                    }
                }
                $user_data = array(
                    'openid' => $openid,
                    'nick_name' => $nick_name,
                    'avatar_url' => $avatar_url,
                    'superior_user_id' => $superior_user_id,
                    'type' => 1,
                    'remaid_ratio' => 10,
                    'last_login_time' => date('Y-m-d H:i:s'),
                    'create_time' => date('Y-m-d H:i:s')
                );
                $user_id = $this->CI->user_model->query_insert($user_data);
                //个人二维码
                $this->CI->load->library('common_service');
                $qrcode = $this->CI->common_service->get_wxapp_qrcode('uid='.$user_id, 'pages/index/main', $user_id);
                $where = $user_data = array();
                $where['id'] = $user_id;
                $user_data['user_qrcode'] = $qrcode;
                $user_data['update_time'] = date("Y-m-d H:i:s");
                $this->CI->user_model->query_update($where, $user_data);

            }elseif(!empty($user) && $user->is_enable == 2){
                return output(1001,'该账号已禁用');
            }else{
                $where = $user_data = array();
                $where['id'] = $user->id;
                $user_data['openid'] = $openid;
                $user_data['nick_name'] = $nick_name;
                $user_data['avatar_url'] = $avatar_url;
                $user_data['last_login_time'] = date("Y-m-d H:i:s");
                $user_data['update_time'] = date("Y-m-d H:i:s");
                $this->CI->user_model->query_update($where, $user_data);
                $user_id = $user->id;
            }
            $user = $this->CI->user_model->get_by_id($user_id);

            return output(0,'登录成功',[
                'token' => Authorization::generateToken(['user_id'=>$user->id]),
                'nick_name' => $user->nick_name,
                'avatar_url' => $user->avatar_url
            ]);
        }else{
            return output(1001,'openid为空');
        }
    }

    /**
     * 获取用户资料
     */
    public function get_user_info($token) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        $user = $this->CI->user_model->get_by_id($user_id);
        if(empty($user)){
            return output(1004,'用户不存在');
        }

        //获取是否有店铺
        $shop = $this->CI->shop_model->get_by_uid($user_id);
        $is_shop = 0;
        $expire_time = '';
        if(!empty($shop)){
            $is_shop = 1;
            $expire_time = $shop->expire_time;
        }

        $cart_count = $this->CI->cart_model->get_carts_count($user_id, '');
        $pending_count = $this->CI->shop_order_model->get_orders_count($user_id, '', 1);
        $deliver_count = $this->CI->shop_order_model->get_orders_count($user_id, '', 2);
        $gain_count = $this->CI->shop_order_model->get_orders_count($user_id, '', 3);

        return output(0,'成功',[
            'nick_name' => $user->nick_name,
            'avatar_url' => $user->avatar_url,
            'remaid_ratio' => $user->remaid_ratio,
            'reward_balance' => $user->reward_balance,
            'is_enable' => $user->is_enable==1 ? 0 : 1,
            'is_shop' => $is_shop,
            'expire_time' => $expire_time,
            'pending_count' => $pending_count ? $pending_count->count : '',
            'deliver_count' => $deliver_count ? $deliver_count->count : '',
            'gain_count' => $gain_count ? $gain_count->count : '',
            'cart_count' => $cart_count ? $cart_count->count : '',
            'open_bank' => $user->open_bank,
            'card_num' => $user->card_num,
            'name' => $user->name,
            'user_qrcode' => $user->user_qrcode
        ]);
    }

    /**
     * 保存收货地址
     */
    public function save_address($token, $address_id, $name, $mobile, $province_code, $city_code, $area_code, $province_name, $city_name, $area_name, $address) {
        
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        if(empty($name)){
            return output(1001,'请填写收货人姓名');
        }
        if(empty($mobile)){
            return output(1001,'请填写收货人手机号');
        }
        if(empty($province_code) || empty($city_code) || empty($area_code) || empty($province_name) || empty($city_name) || empty($area_name)){
            return output(1001,'请选择区域');
        }
        if(empty($address)){
            return output(1001,'请填写详细地址');
        }

        $address_data = array(
            'id' => $address_id,
            'user_id' => $user_id,
            'name' => $name,
            'mobile' => $mobile,
            'province_code' => $province_code,
            'province_name' => $province_name,
            'city_code' => $city_code,
            'city_name' => $city_name,
            'area_code' => $area_code,
            'area_name' => $area_name,
            'address' => $address,
            'is_default' => 1
        );

        if (empty($address_id)) {
            //先全部设置为未默认
            $this->CI->user_address_model->update_default_address($user_id);

            $address_data['create_time'] = date("Y-m-d H:i:s");
            $this->CI->user_address_model->query_insert($address_data);
        } else {
            $where = array();
            $where['id'] = $address_id;
            $address_data['update_time'] = date("Y-m-d H:i:s");
            $this->CI->user_address_model->query_update($where, $address_data);
        }

        return output(0,'成功');
    }

    /**
     * 获取收货地址
     */
    public function get_addresses($token) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        $user_addresses = $this->CI->user_address_model->get_addresses($user_id);

        return output(0,'成功',['address'=>$user_addresses]);
    }

    /**
     * 获取收货地址详情
     */
    public function get_address_detail($token, $address_id) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($address_id)){
            return output(1001,'参数错误');
        }

        $field = 'id as address_id, name, mobile, province_code, province_name, city_code, city_name, area_code, area_name, address, is_default';
        $address = $this->CI->user_address_model->get_address($address_id, $user_id, $field);

        return output(0,'成功',['address'=>$address]);
    }

    /**
     * 删除收货地址
     */
    public function del_address($token, $address_id) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($address_id)){
            return output(1001,'参数错误');
        }

        $address = $this->CI->user_address_model->get_address($address_id, $user_id);
        if(empty($address)){
            return output(1001,'该收货地址不存在');
        }
        
        $where = array();
        $where['id'] = $address_id;
        $where['user_id'] = $user_id;
        $address_data['is_delete'] = 1;
        $address_data['update_time'] = date("Y-m-d H:i:s");
        $address_data['delete_time'] = date("Y-m-d H:i:s");
        $this->CI->user_address_model->query_update($where, $address_data);

        return output(0,'成功');
    }

    /**
     * 设置默认收货地址
     */
    public function set_default_address($token, $address_id) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($address_id)){
            return output(1001,'参数错误');
        }

        $address = $this->CI->user_address_model->get_address($address_id, $user_id);
        if(empty($address)){
            return output(1001,'该收货地址不存在');
        }
        //先全部设置为未默认
        $this->CI->user_address_model->update_default_address($user_id);
        
        $where = array();
        $where['id'] = $address_id;
        $where['user_id'] = $user_id;
        $address_data['is_default'] = 1;
        $address_data['update_time'] = date("Y-m-d H:i:s");
        $this->CI->user_address_model->query_update($where, $address_data);

        return output(0,'成功');
    }

    /**
     * 设置用户银行卡信息
     */
    public function set_user_bank($token, $open_bank, $card_num, $name) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        $check_user = $this->CI->user_model->get_by_id($user_id);
        if(empty($check_user)){
            return output(1004,'用户不存在');
        }

        $where = $user_data = array();
        $where['id'] = $user_id;
        $user_data['open_bank'] = $open_bank;
        $user_data['card_num'] = $card_num;
        $user_data['name'] = $name;
        $user_data['update_time'] = date("Y-m-d H:i:s");
        $this->CI->user_model->query_update($where, $user_data);

        return output(0,'成功');
    }

    /**
     * 获取个人资产中心信息
     */
    public function get_person_user_info($token) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        $user = $this->CI->user_model->get_by_id($user_id);
        if(empty($user)){
            return output(1004,'用户不存在');
        }

        $yesterday_user_order = $this->CI->user_order_model->get_yesterday_user_order($user_id);
        $all_user_order = $this->CI->user_order_model->get_all_user_order($user_id);
        $invitation_user = $this->CI->user_model->get_all_superior_user($user_id);

        return output(0,'成功',[
            'nick_name' => $user->nick_name,
            'avatar_url' => $user->avatar_url,
            'remaid_ratio' => $user->remaid_ratio,
            'all_reward_price' => $user->all_reward_price,//当前返佣累计收益
            'reward_balance' => $user->reward_balance,//待结算金额=可提现佣金
            'yesterday_income' => $yesterday_user_order->price ? $yesterday_user_order->price : 0,//昨日收益
            'yesterday_reward_balance' => $yesterday_user_order->price ? $yesterday_user_order->price : 0,//昨日结算金额
            'yesterday_user_count' => $yesterday_user_order->count ? $yesterday_user_order->count : 0,//昨日新增客户
            'all_user_count' => $all_user_order->count ? $all_user_order->count : 0,//累计用户
            'all_invitation_count' => $invitation_user->count ? $invitation_user->count : 0//累计邀请
        ]);
    }

    /**
     * 获取店铺中心信息
     */
    public function get_shop_user_info($token) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        $user = $this->CI->user_model->get_by_id($user_id);
        if(empty($user)){
            return output(1004,'用户不存在');
        }

        //获取是否有店铺
        $shop = $this->CI->shop_model->get_by_uid($user_id);
        $expire_time = '';
        if(!empty($shop)){
            $expire_time = $shop->expire_time;
        }

        $shop_order = $this->CI->shop_order_model->get_today_shop_order($shop->shop_domain);
        $delivery_num = $this->CI->shop_order_model->get_delivery_num($shop->shop_domain);
        $pay_order_num = $this->CI->shop_order_model->get_pay_order_num($shop->shop_domain);

        return output(0,'成功',[
            'expire_time' => $expire_time,//店铺有效期
            'today_pay' => $shop_order->today_pay ? $shop_order->today_pay : 0,//今日付款金额
            'visitor_num' => $shop->visitor_num,//浏览人数
            'pay_order_count' => $shop_order->pay_order_count ? $shop_order->pay_order_count : 0,//付款订单数
            'pay_order_num' => $pay_order_num->num ? $pay_order_num->num : 0,//付款件数
            'delivery_num' => $delivery_num->num ? $delivery_num->num : 0,//待发货数量
            'available_balance' => $user->available_balance,//店铺可用余额=待结算金额
            'unavailable_price' => $user->unavailable_price,//不可用金额
            'shop_domain' => $shop->shop_domain
        ]);
    }

    /**
     * 获取资金流水列表
     */
    public function get_capital_flows($token, $page, $per_page) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        $field = 'name,price,type,create_time';
        $this->CI->load->model('capital_flow_model');
        $capital_flows = $this->CI->capital_flow_model->get_capital_flows($user_id, $page, $per_page, $field);
        $result = [];
        if(!empty($capital_flows)){
            foreach($capital_flows as $row){
                $capital_flow = object2array($row);
                $capital_flow['price'] = in_array($capital_flow['type'], [3,4,5]) ? '-'.$capital_flow['price'] : $capital_flow['price'];
                $result[] = array2object($capital_flow);
            }
        }
        $count = $this->CI->capital_flow_model->get_capital_flows_count($user_id);

        return output(0,'成功',['capital_flows'=>$result,'total'=>$count->count]);
    }

    /**
     * 获取我的邀请用户列表
     */
    public function get_visitors($token, $type, $page, $per_page) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        $field = 'nick_name,avatar_url,create_time,open_shop_time';
        $this->CI->load->model('capital_flow_model');
        $users = $this->CI->user_model->get_visitors($user_id, $type, $page, $per_page, $field);
        $result = [];
        if(!empty($users)){
            foreach($users as $row){
                $user = object2array($row);
                $is_shop = 0;
                if($user['open_shop_time']){
                    $is_shop = 1;
                }
                $user['is_shop'] = $is_shop;
                $shop = $this->CI->shop_model->get_by_uid($user_id);
                $qrcode = '';
                if(!empty($shop)){
                    $qrcode = $shop->qrcode;
                }
                $user['qrcode'] = $qrcode;
                $result[] = array2object($user);
            }
        }
        $count = $this->CI->user_model->get_visitors_count($user_id, $type);

        return output(0,'成功',['users'=>$result,'total'=>$count->count]);
    }

    /**
     * 添加formID
     */
    public function add_formid($token, $form_id) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        $user = $this->CI->user_model->get_by_id($user_id);
        if(empty($user)){
            return output(1004,'用户不存在');
        }

        $data = array(
            'user_id' => $user_id,
            'openid' => $user->openid,
            'form_id' => $form_id,
            'create_time' => date("Y-m-d H:i:s")
        );

        $this->CI->load->model('user_formid_model');
        $this->CI->user_formid_model->query_insert($data);

        return output(0,'成功');
    }

}