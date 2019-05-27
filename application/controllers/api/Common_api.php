<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');

class Common_api extends MY_Controller {

	function __construct() {
        parent::__construct();
        $this->load->model('pic_code_model');
    }

    /**
     * 获取短信验证码
     */
    public function get_sms_code() {
        $data = array();
        $this->load->helper('string');
        $this->load->library('sms_service');

        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
        $pic_code = isset($_POST['pic_code']) ? $_POST['pic_code'] : '';
        $time = isset($_POST['time']) ? $_POST['time'] : '';
        if(empty($mobile)){
            echo json_encode(output(1001,'请输入手机号码'));exit;
        }

        $this->load->library('common_service');
        $user_id = $this->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        if(empty($pic_code)){
            echo json_encode(output(1001,'请输入图片验证码'));exit;
        }else{
            $check_code = $this->pic_code_model->get_pic_code($time, $pic_code);
            if(empty($check_code)){
                echo json_encode(output(1001,'图片验证码错误'));exit;
            }
        }
        
        $sms_code = 'SMS_157279028';
        $create_time = date('Y-m-d H:i:s');

        $randomString = random_string('numeric',4);
        $template_param = array('code' => $randomString);

        $result = $this->sms_service->send_sms($mobile, $sms_code, $template_param);
        if(!empty($result) && isset($result->Code)){
            //记录短信验证码发送记录
            $this->load->model('sms_code_log_model');
            $sms_data = array();
            $sms_data['mobile'] = $mobile;
            $sms_data['code'] = $randomString;
            $sms_data['status'] = $result->Code;
            $sms_data['create_time'] = $create_time;
            $this->sms_code_log_model->query_insert($sms_data);

            if($result->Code == 'OK'){
                $this->session->set_userdata('code', $randomString);
                //插入验证码发送日志表
                $this->load->model('verify_code_model');

                $verify_code = $this->verify_code_model->get_verify_code($mobile);
                if(empty($verify_code)){
                    $sms_data = array();
                    $sms_data['mobile'] = $mobile;
                    $sms_data['code'] = $randomString;
                    $sms_data['create_time'] = $create_time;
                    $this->verify_code_model->query_insert($sms_data);
                }else{
                    $sms_data = $where = array();
                    $where['mobile'] = $mobile;
                    $sms_data['code'] = $randomString;
                    $sms_data['create_time'] = $create_time;
                    $this->verify_code_model->query_update($where, $sms_data);
                }
                //删除图片验证码记录
                $pic_code_data = array(
                    'is_delete' => 1
                );
                $pic_code_where = array(
                    'time' => $time,
                    'code' => $pic_code
                );
                $this->pic_code_model->query_update($pic_code_where, $pic_code_data);
                echo json_encode(output(0,'验证码发送成功'));exit;
            }elseif($result->Code == 'isv.BUSINESS_LIMIT_CONTROL'){//业务限流(支持1条/分钟，5条/小时，10条/天)
                echo json_encode(output(2002,'输入频繁,请稍后再试'));exit;
            }else{
                echo json_encode(output(2001,'验证码发送失败，请稍后再试'));exit;
            }
        }else{
            echo json_encode(output(2001,'验证码发送失败，请稍后再试'));exit;
        }
    }

    /**
    * 文件上传
    */
    function upload_file(){
        include_once APPPATH.'libraries/aliyunoss/autoload.php';

        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $this->load->library('common_service');
        $user_id = $this->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        //jpg|jpeg|gif|bmp|png|doc|docx|xls|xlsx|ppt|pptx|pdf|txt|rar|zip|swf|mp4|avi|mov|ram|AVI|webm

        $data = $this->get_file("file");
        if(empty($data)){
            echo json_encode(output(1001,'请选择文件'));
        }
        
        try{
            /**
             * 文件存储到aliyun oss
             */
            // Endpoint以杭州为例，其它Region请按实际情况填写。
            $endpoint = "http://oss-cn-hangzhou.aliyuncs.com";
            // 存储空间名称
            $bucket= "jomo";
            // 文件名称
            $object = $data['file_name'];
            // <yourLocalFile>由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt
            $filePath = $data['full_path'];

            $ossClient = new \OSS\OssClient(AccessKeyId, AccessKeySecret, $endpoint);
            $res = $ossClient->uploadFile($bucket, $object, $filePath);
            $image = $res['info']['url'];

            //删除本地文件
            unlink($filePath);

            echo json_encode(output(0,'上传成功',['file' => str_replace('http://', 'https://', $image)]));
        }catch (\Exception $e){
            echo json_encode(output(1001,'上传失败'));
        }
    }

    /**
     * @desc base64上传图片
     * @access public
     */
    public function upload_base64_img(){
        $token = isset($_SERVER['token']) ? trim($_SERVER['token']) : '';
        $image = isset($_POST['image']) ? trim($_POST['image']) : '';
        if (empty($image)) {
            echo json_encode(output(1001,'请选择一张图片'));exit;
        }
        $path = './user_guide/upload/pic/';
        $fileName = date('YmdHis', time());
        $this->load->library('image');
        $image = $this->image->base64imgsave($path, $fileName, $image);
        if ($image['status'] == 0) {
            echo json_encode(output(1001,$image['info']));exit;
        }
        $image = $this->config->item('domain_www').'/user_guide/upload/pic'.$image['url'];
        echo json_encode(output(0,'上传成功',['image' => $image]));exit;
    }

    /**
    * 获取轮播图
    */
    public function get_banner(){
        $token = isset($_SERVER['token']) ? trim($_SERVER['token']) : '';
        $user_id = 0;
        if(!empty($token)){
            try {
                $check = Authorization::validateToken($token);
                $user_id = $check->user_id;
                $this->load->model('user_model');
                $check_user = $this->user_model->get_by_id($user_id);
                if(empty($check_user)){
                    echo json_encode(output(1004,'用户不存在'));exit;
                }
            } catch (Exception $ex) {
                echo json_encode(output(1004,'token错误'));exit;
            }
        }
        $this->load->model('banner_model');
        $banner = $this->banner_model->get_banners($user_id);
        echo json_encode(output(0,'成功',['banner' => $banner]));
    }

    /**
    * 获取产品
    */
    public function get_products(){
        $this->load->model('product_model');
        $product = $this->product_model->get_products();
        echo json_encode(output(0,'成功',['products' => $product]));
    }

    /**
     * 获取图片验证码
    */
    public function get_pic_code(){
        $time = isset($_GET['t']) ? trim($_GET['t']) : '';
        if(empty($time)){
            echo json_encode(output(1001,'参数错误'));exit;
        }
        $this->load->helper('captha');
        $num = GetVerify(4);
        $this->load->model('pic_code_model');
        $data = array(
            'time' => $time,
            'code' => $num
        );
        $this->pic_code_model->query_insert($data);
        code($num);
    }



    public function collect() {
        ini_set("memory_limit", "1024M");
        $this->load->library('simple_html_dom');
        for($i=210000; $i<211000; $i++){
            // $html = file_get_html('https://www.proginn.com/u/148071');
            $url = 'https://www.proginn.com/u/'.$i;
            $headers = get_headers($url);
            if (strpos($headers[0], '404')){
                continue;
            }else{
                // echo $i.'<br>';
                $html = file_get_contents($url);
                foreach ($html->find('h3') as $tr) {
                    if($tr->plaintext == '工商信息'){
                        echo $i.'<br>';exit;
                        break;
                    }
                }
            }
        }
        echo 'done';
        exit;
    }

}
