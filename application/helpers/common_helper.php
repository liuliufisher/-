<?php

function str_cut($str, $sublen, $etc = '...') {
    if (strlen($str) <= $sublen) {
        $rStr = $str;
    } else {
        $I = 0;
        while ($I < $sublen) {
            $StringTMP = substr($str, $I, 1);

            if (ord($StringTMP) >= 224) {
                $StringTMP = substr($str, $I, 3);
                $I = $I + 3;
            } elseif (ord($StringTMP) >= 192) {
                $StringTMP = substr($str, $I, 2);
                $I = $I + 2;
            } else {
                $I = $I + 1;
            }
            $StringLast[] = $StringTMP;
        }

        $rStr = implode('', $StringLast) . $etc;
    }

    return $rStr;
}

function object2array($object) {
    if (is_object($object)) {
        foreach ($object as $key => $value) {
            $array[$key] = $value;
        }
    } else {
        $array = $object;
    }
    return $array;
}

function array2object($array) {
    if (is_array($array)) {
        $obj = new StdClass();
        foreach ($array as $key => $val) {
            $obj->$key = $val;
        }
    } else {
        $obj = $array;
    }
    return $obj;
}

function readExcel($path){
    //引用PHPexcel 类
    include_once(APPPATH.'libraries/tool/PHPExcel.php');
    include_once(APPPATH.'libraries/tool/PHPExcel/IOFactory.php');//静态类
    $type = 'Excel2007';//设置为Excel5代表支持2003或以下版本，Excel2007代表2007版
    $xlsReader = PHPExcel_IOFactory::createReader($type);
    $xlsReader->setReadDataOnly(true);
    $xlsReader->setLoadSheetsOnly(true);
    $Sheets = $xlsReader->load($path);
    //开始读取上传到服务器中的Excel文件，返回一个二维数组
    $dataArray = $Sheets->getSheet(0)->toArray();
    
    return $dataArray;
}

//生成随机字符串
function getRandChar($length){
     $str = null;
     $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";//大小写字母以及数字
     $max = strlen($strPol)-1;
     
     for($i=0;$i<$length;$i++){
        $str.=$strPol[rand(0,$max)];
     }
     return $str;
  }

/**
 * json返回格式
 * code 状态码
 * msg 提示信息
 * data 返回数据
 */
function output($code=0, $msg='', $data=[]) {
    $res = array(
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
    );
    return $res;
}

/**
 * get请求
 * @param $url
 * @return mixed
 */
function curlget($url){
    //初始化
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //执行命令
    $data = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);
    //显示获得的数据
    $rest = json_decode($data);
    return $rest;
}

/**
 * post请求
 * @param $url
 * @return mixed
 */
function curlpost($url, $postdata){
    //初始化
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //设置post方式提交
    curl_setopt($curl, CURLOPT_POST, 1);
    //设置post数据
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
    //执行命令
    $data = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);
    //显示获得的数据
    $rest = json_decode($data,true);
    return $rest;
}

function httpRequest($url, $data='', $method='GET'){
    $curl = curl_init();  
    curl_setopt($curl, CURLOPT_URL, $url);  
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);  
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);  
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);  
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);  
    if($method=='POST'){
        curl_setopt($curl, CURLOPT_POST, 1); 
        if ($data != ''){
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);  
        }
    }
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);  
    curl_setopt($curl, CURLOPT_HEADER, 0);  
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
    $result = curl_exec($curl);  
    curl_close($curl);  
    return $result;
} 

/**
* 生成订单号
*/
function create_order_code($type = ''){
    list ( $usec, $sec ) = explode ( " ", microtime () );
    $usec = substr ( str_replace ( '0.', '', $usec ), 0, 4 );
    $str = rand ( 10, 99 );
    return $type.date ( "YmdHis" ) . $usec . $str;
}

?>