<?php

class Image {

    private $CI;

    function __construct() {
        $this->CI = & get_instance ();
    }


    /**
     * @desc  上传图片
     * @access public
     * @param $path 图片路径
     * @param $fileName 图片名称
     * @param $img base64格式的图片
     * @return array
     */
    public static function base64imgsave($path, $fileName, $img){
        if(!is_dir($path)){
            @mkdir($path,0777,true);
        }
        $imgUrl = '';
        $types = array('jpg', 'gif', 'png', 'jpeg');
        $img = str_replace(array('_','-'), array('/','+'), $img);
        $b64img = substr($img, 0,100);
        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $b64img, $matches)){
            $type = $matches[2];
            if(!in_array($type, $types)){
                return array('status'=>0,'info'=>'图片格式不正确，只支持 jpg、gif、png、jpeg哦!');
            }
            $img = str_replace($matches[1], '', $img);
            $img = base64_decode($img);
            $imgName = '/'.$fileName.'.'.$type;
            file_put_contents($path.$imgName, $img);

            $ary['status'] = 1;
            $ary['info'] = '保存图片成功';
            $ary['url'] = $imgName;
            return $ary;
        }
        $ary['status'] = 0;
        $ary['info'] = '图片格式不正确，只支持 jpg、gif、png、jpeg哦!';
        return $ary;

    }


}