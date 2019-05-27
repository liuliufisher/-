<?php
/**
 * 店铺管理业务层
 * @author dingxuehuan
 */
class Shop_service {

	private $CI;

	function __construct() {
		$this->CI = & get_instance ();
        $this->CI->load->model('shop_model');
        $this->CI->load->model('user_model');
	}

	/**
     * 一键开店
     */
    public function open_shop($token, $shop_name, $shop_logo, $mobile, $sms_code, $province_code, $city_code, $area_code, $province_name, $city_name, $area_name, $address, $shop_contacts) {

        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        $check_shop = $this->CI->shop_model->get_by_uid($user_id);
        if(!empty($shop)){
            return output(1004,'该用户已存在店铺');
        }

        if(empty($shop_name)){
            return output(1001,'请填写店铺名称');
        }
        if(empty($shop_logo)){
            return output(1001,'请上传店铺logo');
        }
        if(empty($mobile)){
            return output(1001,'请填写手机号码');
        }
        if(empty($sms_code)){
            return output(1001,'请填写短信验证码');
        }
        if(empty($shop_contacts)){
            return output(1001,'请上传店铺联系人微信二维码');
        }

        $shop_domain = $mobile;
        $shop = $this->CI->shop_model->get_by_domain($shop_domain);
        if(!empty($shop)){
            return output(1001,'该店铺域名已存在');
        }

        $check_mobile = $this->CI->shop_model->get_by_mobile($mobile);
        if(!empty($check_mobile)){
            return output(1001,'该手机号已存在');
        }

        //判断输入的验证码是否正确
        $this->CI->load->model('verify_code_model');
        $verify_code = $this->CI->verify_code_model->get_verify_code($mobile, $sms_code);
        if( empty($verify_code) || (!empty($verify_code) && floor((time()-strtotime($verify_code->create_time))%86400/60) >10 )){
            return output(1001,'验证码错误');
        }

        //店铺二维码
        $qrcode = $this->CI->common_service->get_wxapp_qrcode('shop_domain='.$shop_domain, 'pages/index/main', $shop_domain);

        $shop_data = array(
            'user_id' => $user_id,
            'shop_name' => $shop_name,
            'shop_logo' => $shop_logo,
            'shop_domain' => $shop_domain,
            'mobile' => $mobile,
            'qrcode' => $qrcode,
            'expire_time' => date('Y-m-d',strtotime('+15 day')),
            'province_code' => $province_code,
            'province_name' => $province_name,
            'city_code' => $city_code,
            'city_name' => $city_name,
            'area_code' => $area_code,
            'area_name' => $area_name,
            'address' => $address,
            'shop_contacts' => $shop_contacts,
            'create_time' => date('Y-m-d H:i:s')
        );
        $this->CI->shop_model->query_insert($shop_data);

        //开店之后覆盖个人二维码
        $user_where = $user_data = array();
        $user_where['id'] = $user_id;
        $user_data['user_qrcode'] = $qrcode;
        $user_data['type'] = 2;
        $user_data['update_time'] = date("Y-m-d H:i:s");
        $this->CI->user_model->query_update($user_where, $user_data);

        return output(0,'成功',['shop_domain'=>$shop_domain]);
    }

    /**
     * 获取店铺信息
     */
    public function get_shop_info($token) {

        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        $shop = $this->CI->shop_model->get_by_uid($user_id);
        if(empty($shop)){
            return output(1004,'店铺不存在');
        }

        return output(0,'成功',[
            'shop_name' => $shop->shop_name,
            'shop_logo' => $shop->shop_logo,
            'shop_domain' => $shop->shop_domain,
            'mobile' => $shop->mobile,
            'qrcode' => $shop->qrcode,
            'expire_time' => $shop->expire_time,
            'is_expire' => $shop->expire_time > date('Y-m-d') ? 0 : 1,
            'province_code' => $shop->province_code,
            'province_name' => $shop->province_name,
            'city_code' => $shop->city_code,
            'city_name' => $shop->city_name,
            'area_code' => $shop->area_code,
            'area_name' => $shop->area_name,
            'address' => $shop->address,
            'shop_contacts' => $shop->shop_contacts
        ]);
    }

    /**
     * 修改店铺信息
     */
    public function edit_shop($token, $shop_name, $shop_logo, $province_code, $city_code, $area_code, $province_name, $city_name, $area_name, $address, $shop_contacts) {

        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        $shop = $this->CI->shop_model->get_by_uid($user_id);
        if(empty($shop)){
            return output(1004,'店铺不存在');
        }

        $where = $shop_data = array();
        $where['id'] = $shop->id;
        if(!empty($shop_name)){
            $shop_data['shop_name'] = $shop_name;
        }
        if(!empty($shop_logo)){
            $shop_data['shop_logo'] = $shop_logo;
        }
        if(!empty($province_code)){
            $shop_data['province_code'] = $province_code;
            $shop_data['province_name'] = $province_name;
        }
        if(!empty($city_code)){
            $shop_data['city_code'] = $city_code;
            $shop_data['city_name'] = $city_name;
        }
        if(!empty($area_code)){
            $shop_data['area_code'] = $area_code;
            $shop_data['area_name'] = $area_name;
        }
        if(!empty($address)){
            $shop_data['address'] = $address;
        }
        if(!empty($shop_contacts)){
            $shop_data['shop_contacts'] = $shop_contacts;
        }

        $shop_data['update_time'] = date("Y-m-d H:i:s");
        $this->CI->shop_model->query_update($where, $shop_data);

        return output(0,'成功');
    }

    /**
     * 获取店铺名称
     */
    public function get_shop_name($token, $shop_domain) {

        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($shop_domain)){
            return output(1004,'参数错误');
        }

        $shop = $this->CI->shop_model->get_by_domain($shop_domain);
        if(empty($shop)){
            return output(1004,'店铺不存在');
        }

        return output(0,'成功',[
            'shop_name' => $shop->shop_name
        ]);
    }

}