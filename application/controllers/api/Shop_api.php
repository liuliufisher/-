<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');

class Shop_api extends MY_Controller {

	function __construct() {
        parent::__construct();
        $this->load->library('shop_service');
    }

    /**
     * 一键开店
     */
    public function open_shop() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $shop_name = isset($_POST['shop_name']) ? trim($_POST['shop_name']) : ''; //店铺名称
        $shop_logo = isset($_POST['shop_logo']) ? trim($_POST['shop_logo']) : ''; //店铺logo
        // $shop_domain = isset($_POST['shop_domain']) ? trim($_POST['shop_domain']) : ''; //店铺域名
        $mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : ''; //手机号
        $sms_code = isset($_POST['sms_code']) ? trim($_POST['sms_code']) : '';//短信验证码
        $province_code = isset($_POST['province_code']) ? trim($_POST['province_code']) : ''; //省code
        $city_code = isset($_POST['city_code']) ? trim($_POST['city_code']) : ''; //市code
        $area_code = isset($_POST['area_code']) ? trim($_POST['area_code']) : ''; //区code
        $province_name = isset($_POST['province_name']) ? trim($_POST['province_name']) : ''; //省名称
        $city_name = isset($_POST['city_name']) ? trim($_POST['city_name']) : ''; //市名称
        $area_name = isset($_POST['area_name']) ? trim($_POST['area_name']) : ''; //区名称
        $address = isset($_POST['address']) ? trim($_POST['address']) : ''; //详细地址
        $shop_contacts = isset($_POST['shop_contacts']) ? trim($_POST['shop_contacts']) : ''; //店铺联系人微信二维码

        $result = $this->shop_service->open_shop($token, $shop_name, $shop_logo, $mobile, $sms_code, $province_code, $city_code, $area_code, $province_name, $city_name, $area_name, $address, $shop_contacts);
        echo json_encode($result);exit;
    }

    /**
     * 获取店铺信息
     */
    public function get_shop_info() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';

        $result = $this->shop_service->get_shop_info($token);
        echo json_encode($result);exit;
    }

    /**
     * 修改店铺信息
     */
    public function edit_shop() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $shop_name = isset($_POST['shop_name']) ? trim($_POST['shop_name']) : ''; //店铺名称
        $shop_logo = isset($_POST['shop_logo']) ? trim($_POST['shop_logo']) : ''; //店铺logo
        $province_code = isset($_POST['province_code']) ? trim($_POST['province_code']) : ''; //省code
        $city_code = isset($_POST['city_code']) ? trim($_POST['city_code']) : ''; //市code
        $area_code = isset($_POST['area_code']) ? trim($_POST['area_code']) : ''; //区code
        $province_name = isset($_POST['province_name']) ? trim($_POST['province_name']) : ''; //省名称
        $city_name = isset($_POST['city_name']) ? trim($_POST['city_name']) : ''; //市名称
        $area_name = isset($_POST['area_name']) ? trim($_POST['area_name']) : ''; //区名称
        $address = isset($_POST['address']) ? trim($_POST['address']) : ''; //详细地址
        $shop_contacts = isset($_POST['shop_contacts']) ? trim($_POST['shop_contacts']) : ''; //店铺联系人微信二维码

        $result = $this->shop_service->edit_shop($token, $shop_name, $shop_logo, $province_code, $city_code, $area_code, $province_name, $city_name, $area_name, $address, $shop_contacts);
        echo json_encode($result);exit;
    }

    /**
     * 获取店铺名称
     */
    public function get_shop_name() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $shop_domain = isset($_POST['shop_domain']) ? trim($_POST['shop_domain']) : '';

        $result = $this->shop_service->get_shop_name($token, $shop_domain);
        echo json_encode($result);exit;
    }
    

}
