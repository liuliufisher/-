<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');

class Kd_api extends MY_Controller {

	function __construct() {
        parent::__construct();
    }

    /**
     * 查询物流
     */
    function get_order_traces_sub(){
        $logistics_no = isset($_POST['logistics_no']) ? trim($_POST['logistics_no']) : '';
        $express_code = isset($_POST['express_code']) ? trim($_POST['express_code']) : '';
        if(empty($logistics_no)){
            echo json_encode(output(1001,'参数错误'));exit;
        }
        $Shippers = [];
        $express_name = '';
        if(empty($express_code)){
            $requestData = json_encode(['LogisticCode'=>$logistics_no]);
            $datas = array(
                'EBusinessID' => EBusinessID,
                'RequestType' => '2002',
                'RequestData' => urlencode($requestData) ,
                'DataType' => '2',
            );
            $datas['DataSign'] = $this->encrypt($requestData, AppKey);
            $result = json_decode($this->sendPost(Ebusiness_ReqURL, $datas));
            if($result->Success != 1){
                echo json_encode(output(1001,'查询失败'));exit;
            }
            $Shippers = $result->Shippers;
            // $express_code = $result->Shippers[0]->ShipperCode;
            // $express_name = $result->Shippers[0]->ShipperName;
        }else{
            $requestData = json_encode(['ShipperCode'=>$express_code,'LogisticCode'=>$logistics_no]);
            $datas = array(
                'EBusinessID' => EBusinessID,
                'RequestType' => '8001',
                'RequestData' => urlencode($requestData) ,
                'DataType' => '2',
            );
            $datas['DataSign'] = $this->encrypt($requestData, AppKey);
            $traces_result = json_decode($this->sendPost(Dist_ReqURL, $datas));
            if($traces_result->Success != 1){
                echo json_encode(output(1001,'查询失败'));exit;
            }
            echo json_encode(output(0,'成功',[
                'express_name' => '',
                'express_code' => $express_code,
                'traces' => $traces_result->Traces
            ]));exit;
        }

        if(!empty($Shippers)){
            foreach($Shippers as $Shipper){
                $express_code = $Shipper->ShipperCode;
                $express_name = $Shipper->ShipperName;

                $return = [
                    'express_name' => $express_name,
                    'express_code' => $express_code,
                    'traces' => []
                ];

                $requestData = json_encode(['ShipperCode'=>$express_code,'LogisticCode'=>$logistics_no]);
                $datas = array(
                    'EBusinessID' => EBusinessID,
                    'RequestType' => '1002',
                    'RequestData' => urlencode($requestData) ,
                    'DataType' => '2',
                );
                $datas['DataSign'] = $this->encrypt($requestData, AppKey);
                $traces_result = json_decode($this->sendPost(Dist_ReqURL, $datas));
                if($traces_result->Success != 1){
                    echo json_encode(output(1001,'查询失败'));exit;
                }
                if(!empty($traces_result->Traces)){
                    $return = [
                        'express_name' => $express_name,
                        'express_code' => $express_code,
                        'traces' => $traces_result->Traces
                    ];
                }
            }
            echo json_encode(output(0,'成功',$return));exit;
        }else{
            echo json_encode(output(1001,'查询失败'));exit;
        }
    }

    /**
     *  post提交数据 
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据 
     * @return url响应返回的html
     */
    public function sendPost($url, $datas) {
        $temps = array();   
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);      
        }   
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if(empty($url_info['port']))
        {
            $url_info['port']=80;   
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);  
        
        return $gets;
    }

    /**
     * 电商Sign签名生成
     * @param data 内容   
     * @param appkey Appkey
     * @return DataSign签名
     */
    public function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }

}
