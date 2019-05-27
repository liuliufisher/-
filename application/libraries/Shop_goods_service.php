<?php
/**
 * 店铺商品管理业务层
 * @author dingxuehuan
 */
class Shop_goods_service {

	private $CI;

	function __construct() {
		$this->CI = & get_instance ();
        $this->CI->load->model('shop_model');
        $this->CI->load->model('shop_goods_model');
        $this->CI->load->model('shop_goods_cate_model');
        $this->CI->load->model('shop_goods_spec_model');
        $this->CI->load->model('shop_goods_image_model');
	}

    /**
     * 保存商品
     */
    public function save_goods($token, $goods_id, $goods_name, $main_image, $goods_images, $goods_des_images, $spec_json, $cate_id, $video_url, $content, $freight, $is_hot, $status) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($status)){
            return output(1001,'参数错误');
        }
        if(empty($goods_name)){
            return output(1001,'请填写商品名称');
        }
        if(empty($main_image)){
            return output(1001,'请填写商品主图');
        }
        if(empty($cate_id)){
            return output(1001,'请选择商品分类');
        }

        if($status == 1){
            $check_spec_json = json_decode($spec_json);
            if(!empty($check_spec_json)){
                foreach($check_spec_json as $val){
                    if(empty($val->goods_price)){
                        return output(1001,'请填写商品价格');
                    }
                    if(empty($val->stock_num)){
                        return output(1001,'请填写商品库存');
                    }
                }
            }else{
                return output(1001,'请填写商品规格');
            }
        }
        

        $shop = $this->CI->shop_model->get_by_uid($user_id);
        if(empty($shop)){
            return output(1001,'该店铺不存在');
        }

        $shop_data = array(
            'id' => $goods_id,
            'user_id' => $user_id,
            'goods_name' => $goods_name,
            'goods_image' => $main_image,
            'cate_id' => $cate_id,
            'video_url' => $video_url,
            'content' => $content,
            'freight' => $freight,
            'is_hot' => $is_hot,
            'status' => $status
        );
        if (empty($goods_id)) {
            $shop_data['create_time'] = date("Y-m-d H:i:s");
            $goods_id = $this->CI->shop_goods_model->query_insert($shop_data);
            if($goods_id > 0){
                $update_where = array();
                $update_where['id'] = $goods_id;
                //商品二维码
                $goods_qrcode = $this->CI->common_service->get_wxapp_qrcode('domain='.$shop->shop_domain.'&id='.$goods_id, 'pages/goodsInfo/main', $shop->shop_domain.'_goodsid.'.$goods_id);
                $update_shop_data['goods_qrcode'] = $goods_qrcode;
                $update_shop_data['update_time'] = date("Y-m-d H:i:s");
                $this->CI->shop_goods_model->query_update($update_where, $update_shop_data);
            }
        } else {
            $where = array();
            $where['id'] = $goods_id;
            $shop_data['update_time'] = date("Y-m-d H:i:s");
            $this->CI->shop_goods_model->query_update($where, $shop_data);
        }

        $all_stock_num = 0;
        if($goods_id > 0 ){
            //先删除商品下的图片和规格
            $del_where = ['goods_id'=>$goods_id];
            $this->CI->shop_goods_image_model->query_delete($del_where);

            //保存主图片
            $goods_images = json_decode($goods_images);
            foreach ($goods_images as $goods_image) {
                $image_data = [
                    'goods_id' => $goods_id,
                    'image_url' => $goods_image->image,
                    'type' => 1,
                    'create_time' => date("Y-m-d H:i:s")
                ];
                $this->CI->shop_goods_image_model->query_insert($image_data);
            }
            //保存详情图片
            $goods_des_images = json_decode($goods_des_images);
            if(!empty($goods_des_images)){
                foreach ($goods_des_images as $goods_des_image) {
                    $des_image_data = [
                        'goods_id' => $goods_id,
                        'image_url' => $goods_des_image->image,
                        'type' => 2,
                        'create_time' => date("Y-m-d H:i:s")
                    ];
                    $this->CI->shop_goods_image_model->query_insert($des_image_data);
                }
            }

            //保存规格
            $old_specs = $this->CI->shop_goods_spec_model->get_all_spec($goods_id);
            $old_spec_id = [];
            if(!empty($old_specs)){
                foreach($old_specs as $old_spec){
                    $old_spec_id[] = $old_spec->id;
                }
            }

            $spec_json = json_decode($spec_json);
            $new_spec_id = [];
            $all_stock_num = 0;
            if(!empty($spec_json)){
                foreach($spec_json as $val){
                    $spec_data = [
                        'goods_id' => $goods_id,
                        'spec' => isset($val->spec) ? $val->spec : '',
                        'goods_price' => $val->goods_price,
                        'discount_price' => isset($val->discount_price) ? $val->discount_price : '',
                        'stock_num' => $val->stock_num
                    ];
                    $all_stock_num += $val->stock_num;
                    if(empty($val->spec_id)){
                        $spec_data['create_time'] = date("Y-m-d H:i:s");
                        $this->CI->shop_goods_spec_model->query_insert($spec_data);
                    } else {
                        $new_spec_id[] = $val->spec_id;
                        $where = array();
                        $where['id'] = $val->spec_id;
                        $spec_data['update_time'] = date("Y-m-d H:i:s");
                        $this->CI->shop_goods_spec_model->query_update($where, $spec_data);
                    }
                }

                $diff_spec_ids = array_diff($old_spec_id, $new_spec_id);
                if(!empty($diff_spec_ids)){
                    foreach ($diff_spec_ids as $diff_spec_id) {
                        $del_where = ['id'=>$diff_spec_id];
                        $this->CI->shop_goods_spec_model->query_delete($del_where);
                    }
                }
            }

        }

        //更新库存
        $where = $hop_goods_data = array();
        $where['id'] = $goods_id;
        $hop_goods_data['stock_num'] = $all_stock_num;
        $hop_goods_data['update_time'] = date("Y-m-d H:i:s");
        $this->CI->shop_goods_model->query_update($where, $hop_goods_data);

        return output(0,'成功');
    }


    /**
     * 商家商品列表
     */
    public function shop_goods_list($token, $status, $keyword, $page, $per_page) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        $field = '*';
        $goods_list = $this->CI->shop_goods_model->get_shop_goods_list($user_id, $status, $keyword, $page, $per_page, $field);
        $result = [];
        if(!empty($goods_list)){
            foreach($goods_list as $goods){
                $g['good_id'] = $goods->id;
                $g['good_name'] = $goods->goods_name;
                $g['stock_num'] = $goods->stock_num;
                $g['sale_num'] = $goods->sale_num;
                $g['goods_image'] = $goods->goods_image;
                $spec_price = $this->CI->shop_goods_spec_model->get_price($goods->id);
                $g['good_price'] = !empty($spec_price)&&!empty($spec_price->goods_price) ? $spec_price->goods_price : 0;

                $result[] = $g;
            }
        }

        $goods_count = $this->CI->shop_goods_model->get_shop_goods_count($user_id, $status, $keyword);

        return output(0,'成功',['goods'=>$result,'total'=>$goods_count->count]);
    }

    /**
     * 删除商品
     */
    public function del_goods($token, $goods_id) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($goods_id)){
            return output(1001,'参数错误');
        }

        $goods = $this->CI->shop_goods_model->get_by_id($goods_id);
        if(empty($goods)){
            return output(1001,'该商品不存在');
        }
        
        //删除商品
        $where = array();
        $where['id'] = $goods_id;
        $where['user_id'] = $user_id;
        $goods_data['is_delete'] = 1;
        $goods_data['update_time'] = date("Y-m-d H:i:s");
        $goods_data['delete_time'] = date("Y-m-d H:i:s");
        $this->CI->shop_goods_model->query_update($where, $goods_data);
        //删除商品图片
        $image_where = array();
        $image_where['goods_id'] = $goods_id;
        $goods_image_data['is_delete'] = 1;
        $goods_image_data['update_time'] = date("Y-m-d H:i:s");
        $goods_image_data['delete_time'] = date("Y-m-d H:i:s");
        $this->CI->shop_goods_image_model->query_update($image_where, $goods_image_data);
        //删除商品规格
        $spec_where = array();
        $spec_where['goods_id'] = $goods_id;
        $goods_spec_data['is_delete'] = 1;
        $goods_spec_data['update_time'] = date("Y-m-d H:i:s");
        $goods_spec_data['delete_time'] = date("Y-m-d H:i:s");
        $this->CI->shop_goods_spec_model->query_update($spec_where, $goods_spec_data);

        return output(0,'成功');
    }

    /**
     * 商品上下架
     */
    public function shelves_goods($token, $goods_id, $status) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($goods_id) || empty($status)){
            return output(1001,'参数错误');
        }

        $goods = $this->CI->shop_goods_model->get_by_id($goods_id);
        if(empty($goods)){
            return output(1001,'该商品不存在');
        }
        
        $where = array();
        $where['id'] = $goods_id;
        $where['user_id'] = $user_id;
        $goods_data['status'] = $status;
        $goods_data['update_time'] = date("Y-m-d H:i:s");
        $this->CI->shop_goods_model->query_update($where, $goods_data);

        return output(0,'成功');
    }

	/**
     * 保存商品分类
     */
    public function save_goods_cate($token, $cate_id, $parent_id, $cate_name, $image_url, $sort) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        if(empty($cate_name)){
            return output(1001,'参数错误');
        }
        $check_name = $this->CI->shop_goods_cate_model->get_by_name($cate_name, $user_id);
        if(empty($cate_id)){
            if(!empty($check_name)){
                return output(1001,'该分类名称已存在');
            }
        }else{
            if(!empty($check_name) && $check_name->id != $cate_id){
                return output(1001,'该分类名称已存在');
            }
        }
        if($parent_id > 0 && empty($image_url)){
            return output(1001,'请上传图片');
        }

        $cate_data = array(
            'id' => $cate_id,
            'parent_id' => $parent_id,
            'user_id' => $user_id,
            'cate_name' => $cate_name,
            'image_url' => $image_url,
            'sort' => $sort,
            'create_time' => date('Y-m-d H:i:s')
        );

        if (empty($cate_id)) {
            $cate_data['create_time'] = date("Y-m-d H:i:s");
            $this->CI->shop_goods_cate_model->query_insert($cate_data);
        } else {
            $where = array();
            $where['id'] = $cate_id;
            $cate_data['update_time'] = date("Y-m-d H:i:s");
            $this->CI->shop_goods_cate_model->query_update($where, $cate_data);
        }

        return output(0,'成功');
    }

    /**
     * 获取商品分类
     */
    public function get_goods_cates($token, $shop_domain) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($shop_domain)){
            return output(1001,'参数错误');
        }
        $shop = $this->CI->shop_model->get_by_domain($shop_domain);
        if(empty($shop)){
            return output(1001,'该店铺不存在');
        }

        $goods_cates = $this->CI->shop_goods_cate_model->get_goods_cate($shop->user_id);

        $result = $this->get_attr($goods_cates, 0, []);

        return output(0,'成功',['goods_cates'=>$result]);
    }

    /**
     * 获取商品一级分类
     */
    public function get_goods_one_cates($token) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }

        $goods_cates = $this->CI->shop_goods_cate_model->get_goods_one_cate($user_id);

        return output(0,'成功',['goods_cates'=>$goods_cates]);
    }

    /**
     * 获取树结构
     */
    public function get_attr($data, $pid, $resource_id = array()){
        $tree = array();//每次都声明一个新数组用来放子元素
        foreach($data as $v){
            $v = object2array($v);
            if($v['parent_id'] == $pid){//匹配子记录
                $v['children'] = $this->get_attr($data, $v['id'], $resource_id); //递归获取子记录
                if($v['children'] == null){
                    unset($v['children']);//如果子元素为空则unset()进行删除，说明已经到该分支的最后一个元素了（可选）
                }
                $tree[] = $v;//将记录存入新数组
            }
        }
        return $tree;//返回新数组
    }

    /**
     * 删除商品分类
     */
    public function del_goods_cate($token, $cate_id) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($cate_id)){
            return output(1001,'参数错误');
        }

        $cate = $this->CI->shop_goods_cate_model->get_by_id($cate_id);
        if(empty($cate)){
            return output(1001,'该商品分类不存在');
        }
        $p_cate = $this->CI->shop_goods_cate_model->get_by_pid($cate_id);
        if(!empty($p_cate)){
            return output(1001,'该商品分类存在下级分类，无法删除');
        }

        $cata_goods = $this->CI->shop_goods_model->get_cate_goods($user_id, $cate_id);
        if(!empty($cata_goods)){
            return output(1001,'该商品分类存在商品，无法删除');
        }
        
        $where = array();
        $where['id'] = $cate_id;
        $where['user_id'] = $user_id;
        $cate_data['is_delete'] = 1;
        $cate_data['update_time'] = date("Y-m-d H:i:s");
        $cate_data['delete_time'] = date("Y-m-d H:i:s");
        $this->CI->shop_goods_cate_model->query_update($where, $cate_data);

        return output(0,'成功');
    }

    /**
     * 获取店铺首页商品列表
     */
    public function get_index_goods($token, $shop_domain) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($shop_domain)){
            return output(1001,'参数错误');
        }

        $shop = $this->CI->shop_model->get_by_domain($shop_domain);
        if(empty($shop)){
            return output(1001,'该店铺不存在');
        }

        $goods = $this->CI->shop_goods_model->get_index_goods($shop->user_id);
        $result = [];
        if(!empty($goods)){
            foreach($goods as $row){

                $g['goods_id'] = $row->goods_id;
                $g['goods_name'] = $row->goods_name;
                $g['goods_image'] = $row->goods_image;

                //价格
                $spec_price = $this->CI->shop_goods_spec_model->get_price($row->goods_id);
                $g['good_price'] = !empty($spec_price)&&!empty($spec_price->goods_price) ? $spec_price->goods_price : 0;
                $g['discount_price'] = !empty($spec)&&!empty($spec->discount_price) ? $spec->discount_price : 0;

                $result[$row->cate_name][] = $g;
            }
        }

        return output(0,'成功',$result);
    }

    /**
     * 获取分类商品列表
     */
    public function get_cate_goods($token, $shop_domain, $cate_id, $page, $per_page) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($shop_domain) || empty($cate_id)){
            return output(1001,'参数错误');
        }
        $shop = $this->CI->shop_model->get_by_domain($shop_domain);
        if(empty($shop)){
            return output(1001,'该店铺不存在');
        }

        $field = 'sg.id as goods_id, sg.goods_name,sg.goods_image,sg.cate_id,sgc.cate_name';
        $goods = $this->CI->shop_goods_model->get_cate_goods($shop->user_id, $cate_id, '', $page, $per_page, $field);
        $result = [];
        if(!empty($goods)){
            foreach($goods as $row){
                $g['goods_id'] = $row->goods_id;
                $g['goods_name'] = $row->goods_name;
                $g['goods_image'] = $row->goods_image;

                //价格
                $spec_price = $this->CI->shop_goods_spec_model->get_price($row->goods_id);
                $g['good_price'] = !empty($spec_price)&&!empty($spec_price->goods_price) ? $spec_price->goods_price : 0;
                $g['discount_price'] = !empty($spec)&&!empty($spec->discount_price) ? $spec->discount_price : 0;

                $result[] = $g;
            }
        }

        $count = $this->CI->shop_goods_model->get_cate_goods_count($shop->user_id, $cate_id);

        return output(0,'成功',[
            'goods' => $result,
            'total' => $count->count
        ]);
    }

    /**
     * 搜索商品
     */
    public function search_goods($token, $shop_domain, $keyword, $page, $per_page) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($shop_domain) || empty($keyword)){
            return output(1001,'参数错误');
        }
        $shop = $this->CI->shop_model->get_by_domain($shop_domain);
        if(empty($shop)){
            return output(1001,'该店铺不存在');
        }

        $field = 'sg.id as goods_id, sg.goods_name, sg.goods_image,sg.cate_id,sgc.cate_name';
        $goods = $this->CI->shop_goods_model->get_cate_goods($shop->user_id, '', $keyword, $page, $per_page, $field);
        $result = [];
        if(!empty($goods)){
            foreach($goods as $row){
                $g['goods_id'] = $row->goods_id;
                $g['goods_name'] = $row->goods_name;
                $g['goods_image'] = $row->goods_image;

                //价格
                $spec_price = $this->CI->shop_goods_spec_model->get_price($row->goods_id);
                $g['good_price'] = !empty($spec_price)&&!empty($spec_price->goods_price) ? $spec_price->goods_price : 0;
                $g['discount_price'] = !empty($spec)&&!empty($spec->discount_price) ? $spec->discount_price : 0;

                $result[] = $g;
            }
        }

        $count = $this->CI->shop_goods_model->get_cate_goods_count($shop->user_id, '', $keyword);

        return output(0,'成功',[
            'goods' => $result,
            'total' => $count->count
        ]);
    }

    /**
     * 获取商品信息
     */
    public function get_goods_info($token, $goods_id) {
        $this->CI->load->library('common_service');
        $user_id = $this->CI->common_service->check_token($token);
        if(isset($user_id['code']) && $user_id['code'] > 0){
            echo json_encode(output($user_id['code'],$user_id['msg']));exit;
        }
        if(empty($goods_id)){
            return output(1001,'参数错误');
        }
        $goods = $this->CI->shop_goods_model->get_by_id($goods_id);
        if(empty($goods)){
            return output(1001,'该商品不存在');
        }

        $shop = $this->CI->shop_model->get_by_uid($goods->user_id);
        if(empty($shop)){
            return output(1001,'该店铺不存在');
        }
        if($shop->expire_time < date('Y-m-d') && $user_id != $goods->user_id){
            return output(1001,'该店铺已过期');
        }

        $result = [];
        //主图
        $images = $this->CI->shop_goods_image_model->get_all_image($goods->id);
        $img = [];
        if(!empty($images)){
            foreach($images as $image){
                $img[] = $image->image_url;
            }
        }
        $result['image'] = $img;

        //详细图
        $des_images = $this->CI->shop_goods_image_model->get_all_image($goods->id, 2);
        $des_img = [];
        if(!empty($des_images)){
            foreach($des_images as $des_image){
                $des_img[] = $des_image->image_url;
            }
        }
        $result['des_image'] = $des_img;
        //价格
        $spec_price = $this->CI->shop_goods_spec_model->get_price($goods->id);
        $result['goods'] = [
            'goods_name' => $goods->goods_name,
            'goods_image' => $goods->goods_image,
            'cate_id' => $goods->cate_id,
            'goods_price' => !empty($spec_price)&&!empty($spec_price->goods_price) ? $spec_price->goods_price : 0,
            'discount_price' => !empty($spec_price)&&!empty($spec_price->discount_price) ? $spec_price->discount_price : 0,
            'video_url' => $goods->video_url,
            'content' => $goods->content,
            'freight' => $goods->freight,
            'is_hot' => $goods->is_hot,
            'status' => $goods->status,
            'stock_num' => $goods->stock_num,
            'sale_num' => $goods->sale_num,
            'goods_qrcode' => $goods->goods_qrcode
        ];
        //规格
        $specs = $this->CI->shop_goods_spec_model->get_all_spec($goods->id);
        $spec_result = [];
        if(!empty($specs)){
            foreach($specs as $val){
                $s['spec_id'] = $val->id;
                $s['spec'] = $val->spec ? $val->spec : '默认';
                $s['goods_price'] = $val->goods_price ? $val->goods_price : 0;
                $s['discount_price'] = $val->discount_price ? $val->discount_price : 0;
                $s['stock_num'] = $val->stock_num;

                $spec_result[] = $s;
            }
        }
        $result['spec'] = $spec_result;

        //店铺
        $result['shop'] = [
            'shop_name' => $shop->shop_name,
            'shop_logo' => $shop->shop_logo,
            'shop_domain' => $shop->shop_domain,
            'shop_contacts' => $shop->shop_contacts,
            'is_expire' => $shop->expire_time >= date('Y-m-d') ? 0 : 1,
        ];

        return output(0, '成功', $result);
    }


}