<?php
/**
 * 签名类
 */
namespace wxh5pay\lib;
class Sign
{
    /**
     * 生成签名
     * @param array $params
     * @param $key
     * @return string
     */
    public static function makeSign($params,$key)
    {
        //签名步骤一：按字典序排序数组参数
        ksort($params);
        $string = self::toUrlParams($params);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 将参数拼接为url: key=value&key=value
     * @param   $params
     * @return  string
     */
    public static function toUrlParams($params)
    {
        $string = '';
        if (!empty($params)) {
            $array = array();
            foreach ($params as $key => $value) {
                $array[] = $key . '=' . $value;
            }
            $string = implode("&", $array);
        }
        return $string;
    }
}