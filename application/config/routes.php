<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$domain_url = ($_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'];
switch ($domain_url) {
	case $this->config->item('domain_api'):
	$config['base_url'] = $this->config->item('domain_api');

	#api接口
	$route['api/wxlogin'] = "api/user_api/wxlogin";//小程序登录
	$route['api/get_user_info'] = "api/user_api/get_user_info";//获取用户信息

	$route['api/save_address'] = "api/user_api/save_address";//保存收货地址
	$route['api/get_addresses'] = "api/user_api/get_addresses";//获取收货地址
	$route['api/get_address_detail'] = "api/user_api/get_address_detail";//获取收货地址详情
	$route['api/del_address'] = "api/user_api/del_address";//删除收货地址
	$route['api/set_default_address'] = "api/user_api/set_default_address";//设置默认收货地址
	$route['api/set_user_bank'] = "api/user_api/set_user_bank";//设置用户银行卡信息
	$route['api/get_person_user_info'] = "api/user_api/get_person_user_info";//获取个人资产中心信息
	$route['api/get_shop_user_info'] = "api/user_api/get_shop_user_info";//获取店铺中心信息
	$route['api/get_capital_flows'] = "api/user_api/get_capital_flows";//获取资金流水列表
	$route['api/get_visitors'] = "api/user_api/get_visitors";//获取我的邀请用户列表
	$route['api/add_formid'] = "api/user_api/add_formid";//添加formID

	$route['api/get_sms_code'] = "api/common_api/get_sms_code";//获取短信验证码
	$route['api/upload_file'] = "api/common_api/upload_file";//文件上传
	$route['api/upload_base64_img'] = "api/common_api/upload_base64_img";//base64上传图片
	$route['api/get_banner'] = "api/common_api/get_banner";//获取轮播图
	$route['api/get_products'] = "api/common_api/get_products";//获取产品
	$route['api/get_pic_code'] = "api/common_api/get_pic_code";//获取图片验证码
	$route['api/get_area'] = "api/common_api/get_area";//获取全国省市区

	$route['api/unifiedorder_user'] = "api/user_order_api/unifiedorder_user";//小程序统一下单
	$route['api/pay'] = "api/user_order_api/pay";//微信支付回调

	$route['api/open_shop'] = "api/shop_api/open_shop";//一键开店
	$route['api/edit_shop'] = "api/shop_api/edit_shop";//修改店铺信息
	$route['api/get_shop_info'] = "api/shop_api/get_shop_info";//获取店铺信息
	$route['api/get_shop_name'] = "api/shop_api/get_shop_name";//获取店铺名称

	$route['api/save_goods'] = "api/shop_goods_api/save_goods";//保存商品
	$route['api/shop_goods_list'] = "api/shop_goods_api/shop_goods_list";//商家商品列表
	$route['api/del_goods'] = "api/shop_goods_api/del_goods";//删除商品
	$route['api/shelves_goods'] = "api/shop_goods_api/shelves_goods";//商品上下架
	$route['api/save_goods_cate'] = "api/shop_goods_api/save_goods_cate";//保存商品分类
	$route['api/get_goods_cates'] = "api/shop_goods_api/get_goods_cates";//获取商品分类
	$route['api/get_goods_one_cates'] = "api/shop_goods_api/get_goods_one_cates";//获取一级商品分类
	$route['api/del_goods_cate'] = "api/shop_goods_api/del_goods_cate";//删除商品分类
	$route['api/get_index_goods'] = "api/shop_goods_api/get_index_goods";//获取店铺首页商品列表
	$route['api/get_cate_goods'] = "api/shop_goods_api/get_cate_goods";//获取分类商品列表
	$route['api/search_goods'] = "api/shop_goods_api/search_goods";//搜索商品
	$route['api/get_goods_info'] = "api/shop_goods_api/get_goods_info";//获取商品信息

	$route['api/save_cart'] = "api/cart_api/save_cart";//保存购物车
	$route['api/add_cart_num'] = "api/cart_api/add_cart_num";//购物车增加数量
	$route['api/get_carts'] = "api/cart_api/get_carts";//获取购物车列表
	$route['api/del_cart'] = "api/cart_api/del_cart";//删除购物车
	$route['api/get_cart_num'] = "api/cart_api/get_cart_num";//获取购物车数量

	$route['api/settle'] = "api/order_api/settle";//购物车进入的结算页面
	$route['api/direct_settle'] = "api/order_api/direct_settle";//直接进入的结算页面
	$route['api/submit_order'] = "api/order_api/submit_order";//购物车进入的提交订单
	$route['api/direct_submit_order'] = "api/order_api/direct_submit_order";//直接进入的提交订单
	$route['api/my_orders'] = "api/order_api/my_orders";//我的订单列表
	$route['api/order_detail'] = "api/order_api/order_detail";//订单详情
	$route['api/edit_order_price'] = "api/order_api/edit_order_price";//订单修改（修改金额、备注）
	$route['api/add_logistics_no'] = "api/order_api/add_logistics_no";//添加订单物流单号
	$route['api/unifiedorder'] = "api/order_api/unifiedorder";//小程序商品统一下单
	$route['api/shop_orders'] = "api/order_api/shop_orders";//店铺订单列表
	$route['api/apply_withdraw'] = "api/order_api/apply_withdraw";//申请提现
	$route['api/confirm_order'] = "api/order_api/confirm_order";//确认收货

	$route['api/get_order_traces_sub'] = "api/kd_api/get_order_traces_sub";//查询物流

	$route['api/unpay_remind'] = "api/cron_api/unpay_remind";//待付款提醒
	$route['api/cancel_order'] = "api/cron_api/cancel_order";//30分钟内没付款，自动取消订单
	$route['api/take_over_order'] = "api/cron_api/take_over_order";//7天自动收货
	$route['api/shop_remind'] = "api/cron_api/shop_remind";//店铺会员到期提醒
	
	break;

	default:
	$route['default_controller'] = "welcome";
	$config['base_url'] = $this->config->item('domain_www');

	//后台
	$route['admin'] = "admin/login"; //登录
	$route['privilege/login_out'] = "admin/login/login_out"; //退出
	//后台 - 权限
	$route['privilege/account_info'] = "admin/privilege/system_account/account_info"; //个人资料
	$route['privilege/edit_password'] = "admin/privilege/system_account/edit_password"; //修改密码页面
	$route['privilege/ajax_edit_password'] = "admin/privilege/system_account/ajax_edit_password"; //修改密码
	//后台 - 账户管理
	$route['privilege/user_list'] = "admin/privilege/system_account/user_list"; //用户管理-列表
	$route['privilege/user_detail'] = "admin/privilege/system_account/user_detail"; //用户管理-详细页
	$route['privilege/update_system_user'] = "admin/privilege/system_account/update_system_user"; //用户管理-添加更新
	$route['privilege/update_enable_status'] = "admin/privilege/system_account/update_enable_status"; //用户管理-禁用启用操作
	break;
}

