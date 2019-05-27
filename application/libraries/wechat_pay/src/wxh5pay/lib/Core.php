<?php
/**
 * 核心辅助类
 */
namespace wxh5pay\lib;
class Core
{
    /**
     * 把XML转化为数组
     * @param $xml
     * @return bool|mixed
     */
    public static function xmlToArray($xml)
    {
        if (!$xml) return false;
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data;
    }

    /**
     * 把数组转化为XML
     * @param $array
     * @return bool|string
     */
    public static function arrayToXml($array)
    {
        if (!is_array($array) || count($array) <= 0) return false;
        $xml = "<xml>";
        foreach ($array as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 产生一个指定长度的随机字符串
     * @param int $len 产生字符串的长度
     * @return string 随机字符串
     */
    public static function genRandomString($len = 32)
    {
        $chars = [
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        ];
        $charsLen = count($chars) - 1;
        // 将数组打乱
        shuffle($chars);
        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }

    /**
     * 以post方式提交xml到对应的接口url
     * @param string $xml 需要post的xml数据
     * @param $url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second url执行超时时间，默认30s
     * @return bool|mixed
     */
    public static function postXmlCurl($xml, $url, $useCert = false, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if ($useCert == true) {
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, '证书地址');
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, '证书地址');
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return false;
        }
    }

    /**
     * 错误代码
     * @param int $code 服务器输出的错误代码
     * @return bool|string
     */
    public static function error_code($code)
    {
        $errList = [
            'NOAUTH' => '商户未开通此接口权限',
            'NOTENOUGH' => '用户帐号余额不足',
            'ORDERNOTEXIST' => '订单号不存在',
            'ORDERPAID' => '商户订单已支付，无需重复操作',
            'ORDERCLOSED' => '当前订单已关闭，无法支付',
            'SYSTEMERROR' => '系统错误!系统超时',
            'APPID_NOT_EXIST' => '参数中缺少APPID',
            'MCHID_NOT_EXIST' => '参数中缺少MCHID',
            'APPID_MCHID_NOT_MATCH' => 'appid和mch_id不匹配',
            'LACK_PARAMS' => '缺少必要的请求参数',
            'OUT_TRADE_NO_USED' => '同一笔交易不能多次提交',
            'SIGNERROR' => '参数签名结果不正确',
            'XML_FORMAT_ERROR' => 'XML格式错误',
            'REQUIRE_POST_METHOD' => '未使用post传递参数 ',
            'POST_DATA_EMPTY' => 'post数据不能为空',
            'NOT_UTF8' => '未使用指定编码格式',
            'NO_AUTH' => '没有该接口权限',
            'AMOUNT_LIMIT' => '金额超限',
            'PARAM_ERROR' => '参数错误',
            'OPENID_ERROR' => 'Openid错误',
            'SEND_FAILED' => '付款错误',
            'NOTENOUGH' => '余额不足',
            'SYSTEMERROR' => '系统繁忙，请稍后再试。',
            'NAME_MISMATCH' => '姓名校验出错',
            'SIGN_ERROR' => '签名错误',
            'XML_ERROR' => 'Post内容出错',
            'FATAL_ERROR' => '两次请求参数不一致',
            'FREQ_LIMIT' => '超过频率限制，请稍后再试。',
            'MONEY_LIMIT' => '已经达到今日付款总额上限/已达到付款给此用户额度上限',
            'CA_ERROR' => '商户API证书校验出错',
            'V2_ACCOUNT_SIMPLE_BAN' => '无法给非实名用户付款',
            'PARAM_IS_NOT_UTF8' => '请求参数中包含非utf8编码字符',
            'SENDNUM_LIMIT' => '该用户今日付款次数超过限制,如有需要请登录微信支付商户平台更改API安全配置',
            'RECV_ACCOUNT_NOT_ALLOWED' => '收款账户不在收款账户列表',
            'PAY_CHANNEL_NOT_ALLOWED' => '本商户号未配置API发起能力'
        ];
        if (array_key_exists($code, $errList)) {
            return $errList[$code];
        } else {
            return false;
        }
    }
}