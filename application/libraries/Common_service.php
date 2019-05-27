<?php
/**
 * 公共业务层
 * @author dingxuehuan
 */
class Common_service {

	private $CI;

	function __construct() {
		$this->CI = & get_instance ();
        $this->CI->load->model('user_model');
	}

    /**
     * 生成小程序二维码
     */
    public function get_wxapp_qrcode($scene, $page, $filename) {
        $tokenurl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.WECHAT_APPID.'&secret='.WECHAT_SECRET;
        $tokeninfo = curlget($tokenurl);
        $access_token = $tokeninfo->access_token;

        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$access_token;

        $postparam = array(
            'scene' => $scene,
            'page' => $page
        );

        $qCodePath = httpRequest($url,json_encode($postparam),'POST');
        if(isset($qCodePath['errcode']) && ($qCodePath['errcode'] == 41030 || $qCodePath['errcode'] == 45009)){
            return '';
        }

        $qCodeImg = imagecreatefromstring($qCodePath);

        // imagecopymerge使用注解
        $filename = $filename.'.jpg';
        $path = "./user_guide/upload/qrcode/";
        if (file_exists($path)) {
            imagejpeg($qCodeImg,$path."/".$filename);
        }
        //保存文件
        imagedestroy($qCodeImg);

        return $this->CI->config->item('domain_back')."/user_guide/upload/qrcode/".$filename;
    }

    /**
    * 判断form_id是否过期
    */
    public function form($user_id) {
        $this->CI->load->model('user_formid_model');
        $user_formid = $this->CI->user_formid_model->get_formid($user_id);
        if(empty($user_formid)){
            return false;
        }
        $create_time = strtotime($user_formid->create_time);
        $now_time = time();
        $cha_time = $now_time-($create_time+60*60*24*7);
        if($cha_time>0){
            $formid_data = array(
                'status' => 1,
                'update_time' => date("Y-m-d H:i:s")
            );
            $where['id'] = $$user_formid->id;
            $this->CI->user_formid_model->query_update($where, $formid_data);
            return $this->form($user_id);
        }else{
            return $user_formid->form_id;
        }
    }

    /**
     * 校验token
     */
    public function check_token($token = ''){
        return 1;
        if(empty($token)){
            return output(1001,'参数错误');
        }
        try {
            $check = Authorization::validateToken($token);
            $user_id = $check->user_id;
        } catch (Exception $ex) {
            return output(1004,'token错误');
        }
        $check_user = $this->CI->user_model->get_by_id($user_id);
        if(empty($check_user)){
            return output(1004,'用户不存在');
        }

        return $user_id;
    }

}