<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/**
 * 后台模块
 */
define('ACCOUNT','ACCOUNT');//后台账户管理
define('SETTING','SETTING');//系统管理
define('BANNER','BANNER');//banner列表
define('USER','USER');//用户管理
define('SHOP','SHOP');//店铺管理
define('ORDER','ORDER');//订单管理
define('USER_ORDER','USER_ORDER');//会员订单管理
define('SHOP_ORDER','SHOP_ORDER');//商品订单管理
define('GOODS','GOODS');//商品管理
define('WITHDRAW','WITHDRAW');//提现管理

define('WEB_SITE', '金芒杂货铺');

/*
 * 小程序
 */
define('WECHAT_APPID', '');//小程序appid
define('WECHAT_SECRET', '');//小程序secret
define('WECHAT_MCHID', '');//商户ID
define('PAY_APIKEY', '');//支付密钥
define('SSLKEY_PATH', '');//私钥证书地址(通信使用证书才需要配置)
define('SSLCERT_PATH', '');//公钥证书地址(通信使用证书才需要配置)
define('DEVICE_INFO', 'WEB');//设备ID
define('NOTIFY_URL', '');//回调地址
define('UNIFIEDORDER_URL', 'https://api.mch.weixin.qq.com/pay/unifiedorder');//下单接口
define('ORDERQUERY_URL', 'https://api.mch.weixin.qq.com/pay/orderquery');//订单查询接口
define('CLOSEORDER_URL', 'https://api.mch.weixin.qq.com/pay/closeorder');//订单关闭接口
define('TRANSFERS_URL', 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers');//企业付款到零钱


//阿里云短信
define('SIGN_NAME', '');
define('AccessKeyId', '');
define('AccessKeySecret', '');

//JWT
define('JWT_KEY', '');
define('JWT_ALGORITHM', '');

//快递鸟
define('EBusinessID', '');//电商ID
define('AppKey', '');//电商加密私钥，快递鸟提供，注意保管，不要泄漏
define('Ebusiness_ReqURL', 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx');//正式地址
define('Dist_ReqURL', 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx');//正式请求url



