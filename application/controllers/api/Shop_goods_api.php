<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin:*');

class Shop_goods_api extends MY_Controller {

	function __construct() {
        parent::__construct();
        $this->load->library('shop_goods_service');
    }

    /**
     * 保存商品
     */
    public function save_goods() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $goods_id = isset($_POST['goods_id']) ? trim($_POST['goods_id']) : ''; //商品ID
        $goods_name = isset($_POST['goods_name']) ? trim($_POST['goods_name']) : ''; //商品名称
        $main_image = isset($_POST['main_image']) ? trim($_POST['main_image']) : ''; //商品主图
        $goods_images = isset($_POST['goods_images']) ? trim($_POST['goods_images']) : ''; //商品细节图[{"image": "1"},{"image": "2"}]
        $goods_des_images = isset($_POST['goods_des_images']) ? trim($_POST['goods_des_images']) : ''; //商品详情图片[{"image": "1"},{"image": "2"}]
        $spec_json = isset($_POST['spec_json']) ? trim($_POST['spec_json']) : ''; //商品规格[{"spec_id": "","spec": "白色","goods_price":10000,"discount_price":9000,"stock_num":15},{"spec_id": "","spec": "黑色","goods_price":9000,"discount_price":9000,"stock_num":15}]
        $cate_id = isset($_POST['cate_id']) ? trim($_POST['cate_id']) : ''; //分类ID
        $video_url = isset($_POST['video_url']) ? trim($_POST['video_url']) : ''; //视频链接
        $content = isset($_POST['content']) ? trim($_POST['content']) : ''; //商品详情页文字部分
        $freight = isset($_POST['freight']) ? trim($_POST['freight']) : '';//运费
        $is_hot = isset($_POST['is_hot']) ? trim($_POST['is_hot']) : ''; //是否推荐到首页  1是 2否
        $status = isset($_POST['status']) ? trim($_POST['status']) : ''; //商品状态 1出售中 2出售中但不能下单 3下架

        $result = $this->shop_goods_service->save_goods($token, $goods_id, $goods_name, $main_image, $goods_images, $goods_des_images, $spec_json, $cate_id, $video_url, $content, $freight, $is_hot, $status);
        echo json_encode($result);exit;
    }

    /**
     * 商家商品列表
     */
    public function shop_goods_list() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $status = isset($_POST['status']) ? trim($_POST['status']) : 1;
        $keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';
        $page = isset($_POST['page']) ? trim($_POST['page']) : 1;
        $per_page = isset($_POST['per_page']) ? trim($_POST['per_page']) : 20;

        $result = $this->shop_goods_service->shop_goods_list($token, $status, $keyword, $page, $per_page);
        echo json_encode($result);exit;
    }

    /**
     * 删除商品
     */
    public function del_goods() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $goods_id = isset($_POST['goods_id']) ? trim($_POST['goods_id']) : '';

        $result = $this->shop_goods_service->del_goods($token, $goods_id);
        echo json_encode($result);exit;
    }

    /**
     * 商品上下架
     */
    public function shelves_goods() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $goods_id = isset($_POST['goods_id']) ? trim($_POST['goods_id']) : ''; //商品ID
        $status = isset($_POST['status']) ? trim($_POST['status']) : '';//商品状态 1出售中 2出售中但不能下单 3下架

        $result = $this->shop_goods_service->shelves_goods($token, $goods_id, $status);
        echo json_encode($result);exit;
    }

    /**
     * 保存商品分类
     */
    public function save_goods_cate() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $cate_id = isset($_POST['cate_id']) ? trim($_POST['cate_id']) : '';//商品分类id
        $parent_id = isset($_POST['parent_id']) ? trim($_POST['parent_id']) : 0;//上级分类id
        $cate_name = isset($_POST['cate_name']) ? trim($_POST['cate_name']) : ''; //商品分类名称
        $image_url = isset($_POST['image_url']) ? trim($_POST['image_url']) : ''; //商品分类图片
        $sort = isset($_POST['sort']) ? trim($_POST['sort']) : ''; //商品分类图片

        $result = $this->shop_goods_service->save_goods_cate($token, $cate_id, $parent_id, $cate_name, $image_url, $sort);
        echo json_encode($result);exit;
    }

    /**
     * 获取商品分类
     */
    public function get_goods_cates() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $shop_domain = isset($_POST['shop_domain']) ? trim($_POST['shop_domain']) : '';//店铺域名

        $result = $this->shop_goods_service->get_goods_cates($token, $shop_domain);
        echo json_encode($result);exit;
    }

    /**
     * 获取商品一级分类
     */
    public function get_goods_one_cates() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';

        $result = $this->shop_goods_service->get_goods_one_cates($token);
        echo json_encode($result);exit;
    }

    /**
     * 删除商品分类
     */
    public function del_goods_cate() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $cate_id = isset($_POST['cate_id']) ? trim($_POST['cate_id']) : ''; //商品分类id

        $result = $this->shop_goods_service->del_goods_cate($token, $cate_id);
        echo json_encode($result);exit;
    }

    /**
     * 获取店铺首页商品列表
     */
    public function get_index_goods() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $shop_domain = isset($_POST['shop_domain']) ? trim($_POST['shop_domain']) : '';//店铺域名

        $result = $this->shop_goods_service->get_index_goods($token, $shop_domain);
        echo json_encode($result);exit;
    }

    /**
     * 获取分类商品列表
     */
    public function get_cate_goods() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $shop_domain = isset($_POST['shop_domain']) ? trim($_POST['shop_domain']) : '';//店铺域名
        $cate_id = isset($_POST['cate_id']) ? trim($_POST['cate_id']) : '';//分类ID
        $page = isset($_POST['page']) ? trim($_POST['page']) : 1;
        $per_page = isset($_POST['per_page']) ? trim($_POST['per_page']) : 20;

        $result = $this->shop_goods_service->get_cate_goods($token, $shop_domain, $cate_id, $page, $per_page);
        echo json_encode($result);exit;
    }

    /**
     * 搜索商品
     */
    public function search_goods() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $shop_domain = isset($_POST['shop_domain']) ? trim($_POST['shop_domain']) : '';//店铺域名
        $keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';//分类ID
        $page = isset($_POST['page']) ? trim($_POST['page']) : 1;
        $per_page = isset($_POST['per_page']) ? trim($_POST['per_page']) : 20;

        $result = $this->shop_goods_service->search_goods($token, $shop_domain, $keyword, $page, $per_page);
        echo json_encode($result);exit;
    }

    /**
     * 获取商品信息
     */
    public function get_goods_info() {
        $token = isset($_SERVER['HTTP_TOKEN']) ? trim($_SERVER['HTTP_TOKEN']) : '';
        $goods_id = isset($_POST['goods_id']) ? trim($_POST['goods_id']) : '';//商品ID

        $result = $this->shop_goods_service->get_goods_info($token, $goods_id);
        echo json_encode($result);exit;
    }
    

}
