<?php
/**
 * 提现业务层
 * @author dingxuehuan
 */
class Withdraw_service {

	private $CI;

	function __construct() {
		$this->CI = & get_instance ();
        $this->CI->load->model('withdraw_model');
        $this->CI->load->model('user_model');
	}

    /**
     * 通过
     * @author dingxuehuan
    */
    public function agree($id){
        $data = array();
        if(empty($id)){
            return output(1,'失败');
        }
        $withdraw = $this->CI->withdraw_model->get_by_id($id);
        if(empty($withdraw)){
            return output(1,'该提现申请不存在');
        }
        $user = $this->CI->user_model->get_by_id($withdraw->user_id);
        if(empty($user)){
            return output(1,'该提现申请的用户不存在');
        }

        $this->CI->db->trans_start();

        $withdraw_data = array(
            'status' => 2,
            'update_time' => date("Y-m-d H:i:s")
        );
        $where = array();
        $where['id'] = $id;
        $withdraw_id = $this->CI->withdraw_model->query_update($where, $withdraw_data);
        if($withdraw_id > 0){
            //失败退还金额
            $user_data = array(
                'frozen_price' => $user->frozen_price - $withdraw->price,
                'withdraw_price' => $user->withdraw_price + $withdraw->price,
                'update_time' => date("Y-m-d H:i:s")
            );
            $where = array();
            $where['id'] = $user->id;
            $user_id  = $this->CI->user_model->query_update($where, $user_data);
            if($user_id > 0){
                $this->CI->db->trans_complete();
                //提现成功之后发送小程序提醒
                if($user->wxapp_openid && $withdraw->form_id){
                    $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.WECHAT_APPID.'&secret='.WECHAT_SECRET;
                    $res = json_decode(file_get_contents($url));
                    $access_token = $res->access_token;

                    $msg_url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
                    $msg_data = [
                        'touser' => $user->wxapp_openid,
                        'template_id' => 't18MMRFopNNB-e-dHRvzwJIAWgfZu5z_rpQ2PLFaYkw',
                        'form_id' => $withdraw->form_id,
                        'data' => [
                            'keyword1' => ['value'=>'提现'],
                            'keyword2' => ['value'=>$withdraw->price.'元'],
                            'keyword3' => ['value'=>date('Y-m-d H:i:s')],
                            'keyword4' => ['value'=>'您好，您的'.$withdraw->price.'元提现已到账，可进入个人中心对应的收款账户进行查收。感谢您的支持。如有疑问请咨询我们的在线客服QQ：2518888855'],
                        ]
                    ];
                    curlpost($msg_url, json_encode($msg_data));
                }
                
                return output(0,'成功');
            }else{
                $this->CI->db->trans_rollback();
                return output(1,'失败');
            }
        }else{
            $this->CI->db->trans_rollback();
            return output(1,'失败');
        }

        return output(0,'成功');
    }

    /**
     * 拒绝
     * @author dingxuehuan
    */
    public function refuse($id){
        $data = array();
        if(empty($id)){
            return output(1,'失败');
        }
        $withdraw = $this->CI->withdraw_model->get_by_id($id);
        if(empty($withdraw)){
            return output(1,'该提现申请不存在');
        }
        $user = $this->CI->user_model->get_by_id($withdraw->user_id);
        if(empty($user)){
            return output(1,'该提现申请的用户不存在');
        }

        $this->CI->db->trans_start();

        $withdraw_data = array(
            'status' => 3,
            'update_time' => date("Y-m-d H:i:s")
        );
        $where = array();
        $where['id'] = $id;
        $withdraw_id = $this->CI->withdraw_model->query_update($where, $withdraw_data);
        if($withdraw_id > 0){
            // if($user->frozen_price - $withdraw->price < 0){
            //     $this->CI->db->trans_rollback();
            //     return output(1,'冻结金额不足');
            // }
            //失败退还金额
            $user_data = array(
                'reward_balance' => $user->reward_balance + $withdraw->price,
                'frozen_price' => $user->frozen_price - $withdraw->price,
                'update_time' => date("Y-m-d H:i:s")
            );
            $where = array();
            $where['id'] = $user->id;
            $user_id  = $this->CI->user_model->query_update($where, $user_data);
            if($user_id > 0){
                $this->CI->db->trans_complete();
                return output(0,'成功');
            }else{
                $this->CI->db->trans_rollback();
                return output(1,'失败');
            }
        }else{
            $this->CI->db->trans_rollback();
            return output(1,'失败');
        }
    }

}