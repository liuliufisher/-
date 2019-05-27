<?php
/**
 * 下单类
 */
namespace wxh5pay;
use wxh5pay\lib\Core;
use wxh5pay\lib\Sign;

class Order
{
    /**
     * 参数
     * @var array
     */
    private $params = [];

    /**
     * 错误提示
     * @var string
     */
    public $errMsg;

    public function __construct()
    {
        $this->setParams([
            'mch_id' => WECHAT_MCHID,
            'sign_type' => 'MD5'
        ]);
    }

    /**
     * 设置参数
     * @param string|array $name
     * @param string $value
     */
    public function setParams($name, $value = '')
    {
        if(is_array($name)){
            $this->params = array_merge($this->params,$name);
        }else{
            $this->params[$name] = $value;
        }
    }

    /**
     * 获取参数
     * @param string $name
     * @return array|mixed
     */
    public function getParams($name = '')
    {
        if (empty($name)) {
            return $this->params;
        } else {
            return $this->params[$name];
        }
    }

    /**
     * 微信H5支付统一下单(返回支付地址,该地址会调用起微信客户端支付)
     * @return bool|string
     */
    public function unifiedorder()
    {
        //生成随机串
        $this->setParams('nonce_str', Core::genRandomString());

        //生成签名参数到数组
        $this->setParams('sign', Sign::makeSign($this->params, PAY_APIKEY));

        //发起下单请求
        $res = Core::postXmlCurl(Core::arrayToXml($this->params), UNIFIEDORDER_URL);

        //Xml转数组
        $resArray = Core::xmlToArray($res);

        //成功返回下单地址
        if ($resArray['return_code'] == 'SUCCESS' and $resArray['result_code'] == 'SUCCESS') {
            return $resArray;
        } elseif ($resArray['return_code'] == 'SUCCESS' and $resArray['result_code'] == 'FAIL') {
            $this->errMsg = Core::error_code($resArray['err_code_des']);
            return false;
        }else{
            $this->errMsg = $resArray['return_msg'];
            return false;
        }
    }

    /**
     * 订单查询(返回查询的订单数组)
     * @return bool|mixed
     */
    public function orderquery()
    {
        //生成随机串
        $this->setParams('nonce_str', Core::genRandomString());

        //生成签名参数到数组
        $this->setParams('sign', Sign::makeSign($this->params, PAY_APIKEY));

        //发起查询请求
        $res = Core::postXmlCurl(Core::arrayToXml($this->params), ORDERQUERY_URL);

        //Xml转数组
        $resArray = Core::xmlToArray($res);

        //成功返回下单地址
        if ($resArray['return_code'] == 'SUCCESS' and $resArray['result_code'] == 'SUCCESS' and $resArray['trade_state'] == 'SUCCESS') {
            return $resArray;
        } elseif ($resArray['return_code'] == 'SUCCESS' and $resArray['result_code'] == 'FAIL') {
            $this->errMsg = Core::error_code($resArray['err_code_des']);
            return false;
        } else {
            $this->errMsg = $resArray['return_msg'];
            return false;
        }
    }

    /**
     * 关闭订单(返回是否成功)
     * @return bool
     */
    public function closeorder()
    {
        //生成随机串
        $this->setParams('nonce_str', Core::genRandomString());

        //生成签名参数到数组
        $this->setParams('sign', Sign::makeSign($this->params, PAY_APIKEY));

        //发起查询请求
        $res = Core::postXmlCurl(Core::arrayToXml($this->params), CLOSEORDER_URL);

        //Xml转数组
        $resArray = Core::xmlToArray($res);

        //成功返回下单地址
        if ($resArray['return_code'] == 'SUCCESS' and $resArray['result_code'] == 'SUCCESS') {
            return true;
        } elseif ($resArray['return_code'] == 'SUCCESS' and $resArray['result_code'] == 'FAIL') {
            $this->errMsg = Core::error_code($resArray['err_code_des']);
            return false;
        } else {
            $this->errMsg = $resArray['return_msg'];
            return false;
        }
    }

    /**
     * 企业付款到零钱
     * @return bool|string
     */
    public function transfers()
    {
        //生成随机串
        $this->setParams('nonce_str', Core::genRandomString());

        //生成签名参数到数组
        $this->setParams('sign', Sign::makeSign($this->params, PAY_APIKEY));

        //发起下单请求
        $res = Core::postXmlCurl(Core::arrayToXml($this->params), TRANSFERS_URL);

        //Xml转数组
        $resArray = Core::xmlToArray($res);

        //成功返回下单地址
        if ($resArray['return_code'] == 'SUCCESS' and $resArray['result_code'] == 'SUCCESS') {
            return $resArray;
        } elseif ($resArray['return_code'] == 'SUCCESS' and $resArray['result_code'] == 'FAIL') {
            $this->errMsg = Core::error_code($resArray['err_code_des']);
            return false;
        }else{
            $this->errMsg = $resArray['return_msg'];
            return false;
        }
    }
}