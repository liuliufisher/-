<?php
/**
 * 后台账户业务层
 * @author dingxuehuan
 */
class System_user_service {

	private $CI;

	function __construct() {
		$this->CI = & get_instance ();
        $this->CI->load->library('session');
        $this->CI->load->model('system_user_model');
	}

	/**
     * 登录
     * @author dingxuehuan
     */
    public function login($user_name, $password) {
        $data = array();
        if(empty($user_name)){
            $data = array(
                'error_code' => 1,
                'error_msg' => '请输入用户名'
            );
            return $data;
        }

        if(empty($password)){
            $data = array(
                'error_code' => 1,
                'error_msg' => '请输入密码'
            );
            return $data;
        }

        $system_user = $this->CI->system_user_model->get_system_user($user_name, md5($password));
        if(empty($system_user)){
            $data = array(
                'error_code' => 1,
                'error_msg' => '账号不存在或密码错误，请重新输入'
            );
            return $data;
        }
        if($system_user->enable_status == 0){
            $data = array(
                'error_code' => 1,
                'error_msg' => '该账号已禁用，请联系管理员'
            );
            return $data;
        }
        $session_result = array(
            'user_id' => $system_user->id,
            'user_name' => $system_user->user_name,
            'logged_in' => TRUE
        );
        $this->CI->session->set_userdata('user', $session_result);

        $data = array(
            'error_code' => 0,
            'error_msg' => '登录成功'
        );

        return $data;
    }

    /**
     * 获取个人资料
     * @author dingxuehuan
    */
    public function get_account_info($user_id){
        $system_user = $this->CI->system_user_model->get_by_id($user_id);
        return $system_user;
    }

    /**
     * 修改密码
     * @author dingxuehuan
    */
    public function edit_password($user_id, $old_password, $new_password, $confim_password){
        $data = $user = array();
        if (!empty($user_id)) {
            $user = $this->CI->system_user_model->get_by_id($user_id);
        }

        if(empty($old_password)){
            $data = array(
                'error_code' => 1,
                'error_msg' => '请输入原密码'
            );
            return $data;
        }
        if(!empty($user) && $user->password != md5($old_password)){
            $data = array(
                'error_code' => 1,
                'error_msg' => '原密码错误'
            );
            return $data;
        }

        if(empty($new_password)){
            $data = array(
                'error_code' => 1,
                'error_msg' => '请输入新密码'
            );
            return $data;
        }

        if(empty($confim_password)){
            $data = array(
                'error_code' => 1,
                'error_msg' => '请输入新确认密码'
            );
            return $data;
        }

        if($new_password != $confim_password){
            $data = array(
                'error_code' => 1,
                'error_msg' => '新密码不一致'
            );
            return $data;
        }

        $user_data = $where = array();
        $where['id'] = $user_id;
        $user_data['password'] = md5($new_password);
        $user_data['update_time'] = date("Y-m-d H:i:s");
        $this->CI->system_user_model->query_update($where, $user_data);

        $data = array(
            'error_code' => 0,
            'error_msg' => '成功'
        );
        return $data;
    }


    /**
     * 账户管理-添加更新
     * @author dingxuehuan
    */
    public function update_system_user($id, $user_name, $name, $phone, $email, $gender, $enable_status, $remark){
        $data = $user = array();
        if (!empty($id)) {
            $user = $this->CI->system_user_model->get_by_id($id);
        }
        if(empty($user_name)){
            $data = array(
                'error_code' => 1,
                'filed' => 'user_name',
                'error_msg' => '请输入用户名'
            );
            return $data;
        }
        $system_user = $this->CI->system_user_model->get_system_user_by_param($user_name);
        if((!empty($user))){
            if(!empty($system_user) && $user->user_name != $user_name){
                $data = array(
                    'error_code' => 1,
                    'error_msg' => '用户名已存在'
                );
                return $data;
            }
        }else{
            if(!empty($system_user)){
                $data = array(
                    'error_code' => 1,
                    'error_msg' => '用户名已存在'
                );
                return $data;
            }
        }

        if(empty($name)){
            $data = array(
                'error_code' => 1,
                'error_msg' => '请输入姓名'
            );
            return $data;
        }
        $system_user = $this->CI->system_user_model->get_system_user_by_param('', $name);
        if((!empty($user))){
            if(!empty($system_user) && $user->name != $name){
                $data = array(
                    'error_code' => 1,
                    'error_msg' => '姓名已存在'
                );
                return $data;
            }
        }else{
            if(!empty($system_user)){
                $data = array(
                    'error_code' => 1,
                    'error_msg' => '姓名已存在'
                );
                return $data;
            }
        }

        $user_data = array(
            'id' => $id,
            'user_name' => $user_name,
            'password' => md5('123456'),
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'gender' => $gender,
            'enable_status' => $enable_status,
            'remark' => $remark
        );

        if (empty($id)) {
            $user_data['create_time'] = date("Y-m-d H:i:s");
            $this->CI->system_user_model->query_insert($user_data);
        } else {
            $where = array();
            $where['id'] = $id;
            $user_data['update_time'] = date("Y-m-d H:i:s");
            $this->CI->system_user_model->query_update($where, $user_data);
        }

        $data = array(
            'error_code' => 0,
            'error_msg' => '成功'
        );
        return $data;
    }

    /**
    * 账户管理-禁用启用操作
    */
    public function update_enable_status($id, $enable_status) {
        $data = array();
        if (empty($id)) {
            $data = array(
                'error_code' => 1,
                'error_msg' => '请先登录'
            );
        }
        $user = $this->CI->system_user_model->get_by_id($id);
        if(empty($user)){
            $data = array(
                'error_code' => 1,
                'error_msg' => '该用户不存在'
            );
        }

        $user_data = $where = array();
        $where['id'] = $id;
        $user_data['enable_status'] = $enable_status;
        $user_data['update_time'] = date("Y-m-d H:i:s");
        $this->CI->system_user_model->query_update($where, $user_data);

        $data = array(
            'error_code' => 0,
            'error_msg' => '成功'
        );
        return $data;
    }

}