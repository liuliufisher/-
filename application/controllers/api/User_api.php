<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');

class User_api extends MY_Controller {

	function __construct() {
        parent::__construct();
        $this->load->library('user_service');
    }

    /**
     * 小程序登录
     */
    public function wxlogin() {
        $code = isset($_POST['code']) ? trim($_POST['code']) : ''; //小程序code
        $nick_name = isset($_POST['nick_name']) ? trim($_POST['nick_name']) : ''; //昵称
        $avatar_url = isset($_POST['avatar_url']) ? trim($_POST['avatar_url']) : ''; //头像
        $shop_domain = isset($_POST['shop_domain']) ? trim($_POST['shop_domain']) : ''; //上级域名
        $uid = isset($_POST['uid']) ? trim($_POST['uid']) : ''; //个人二维码标识

        $result = $this->user_service->wxlogin($code, $nick_name, $avatar_url, $shop_domain, $uid);
        echo json_encode($result);exit;
    }

    /**
     * 获取用户信息
     */
    public function get_user_info(){
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';

        $result = $this->user_service->get_user_info($token);
        echo json_encode($result);exit;
    }

    /**
     * 保存收货地址
     */
    public function save_address() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $address_id = isset($_POST['address_id']) ? trim($_POST['address_id']) : ''; //收货地址id
        $name = isset($_POST['name']) ? trim($_POST['name']) : ''; //收货人姓名
        $mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : ''; //联系方式
        $province_code = isset($_POST['province_code']) ? trim($_POST['province_code']) : ''; //省code
        $city_code = isset($_POST['city_code']) ? trim($_POST['city_code']) : ''; //市code
        $area_code = isset($_POST['area_code']) ? trim($_POST['area_code']) : ''; //区code
        $province_name = isset($_POST['province_name']) ? trim($_POST['province_name']) : ''; //省名称
        $city_name = isset($_POST['city_name']) ? trim($_POST['city_name']) : ''; //市名称
        $area_name = isset($_POST['area_name']) ? trim($_POST['area_name']) : ''; //区名称
        $address = isset($_POST['address']) ? trim($_POST['address']) : ''; //详细地址

        $result = $this->user_service->save_address($token, $address_id, $name, $mobile, $province_code, $city_code, $area_code, $province_name, $city_name, $area_name, $address);
        echo json_encode($result);exit;
    }

    /**
     * 获取收货地址
     */
    public function get_addresses() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';

        $result = $this->user_service->get_addresses($token);
        echo json_encode($result);exit;
    }

    /**
     * 获取收货地址详情
     */
    public function get_address_detail() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $address_id = isset($_POST['address_id']) ? trim($_POST['address_id']) : ''; //地址id

        $result = $this->user_service->get_address_detail($token, $address_id);
        echo json_encode($result);exit;
    }

    /**
     * 删除收货地址
     */
    public function del_address() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $address_id = isset($_POST['address_id']) ? trim($_POST['address_id']) : ''; //收货地址id

        $result = $this->user_service->del_address($token, $address_id);
        echo json_encode($result);exit;
    }

    /**
     * 设置默认收货地址
     */
    public function set_default_address() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $address_id = isset($_POST['address_id']) ? trim($_POST['address_id']) : ''; //收货地址id

        $result = $this->user_service->set_default_address($token, $address_id);
        echo json_encode($result);exit;
    }

    /**
     * 设置用户银行卡信息
     */
    public function set_user_bank() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $open_bank = isset($_POST['open_bank']) ? trim($_POST['open_bank']) : '';//开户行
        $card_num = isset($_POST['card_num']) ? trim($_POST['card_num']) : '';//卡号
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';//姓名

        $result = $this->user_service->set_user_bank($token, $open_bank, $card_num, $name);
        echo json_encode($result);exit;
    }
    
    /**
     * 获取个人资产中心信息
     */
    public function get_person_user_info(){
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';

        $result = $this->user_service->get_person_user_info($token);
        echo json_encode($result);exit;
    }

    /**
     * 获取店铺中心信息
     */
    public function get_shop_user_info(){
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';

        $result = $this->user_service->get_shop_user_info($token);
        echo json_encode($result);exit;
    }

    /**
     * 获取资金流水列表
     */
    public function get_capital_flows() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $page = isset($_POST['page']) ? trim($_POST['page']) : 1;
        $per_page = isset($_POST['per_page']) ? trim($_POST['per_page']) : 20;

        $result = $this->user_service->get_capital_flows($token, $page, $per_page);
        echo json_encode($result);exit;
    }

    /**
     * 获取我的邀请用户列表
     */
    public function get_visitors() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $type = isset($_POST['type']) ? trim($_POST['type']) : 1; //1累计客户 2累计邀请
        $page = isset($_POST['page']) ? trim($_POST['page']) : 1;
        $per_page = isset($_POST['per_page']) ? trim($_POST['per_page']) : 20;

        $result = $this->user_service->get_visitors($token, $type, $page, $per_page);
        echo json_encode($result);exit;
    }

    /**
    * 添加formID
    */
    public function add_formid(){
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $form_id = isset($_POST['form_id']) ? trim($_POST['form_id']) : '';

        $result = $this->user_service->add_formid($token, $form_id);
        echo json_encode($result);exit;
    }

}
