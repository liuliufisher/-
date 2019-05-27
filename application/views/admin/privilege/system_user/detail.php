<!--面包屑-->
<div class="page-title">
	<div class="page-breadcrumb">
		<ol class="breadcrumb">
			<li><a href="/admin">管理首页</a></li>
			<li><a href="/privilege/user_list">账户列表</a></li>
			<li class="active">添加账户</li>
		</ol>
	</div>
</div>

<!--主要内容开始位置-->

<div id="main-wrapper">
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-danger" role="alert">
                用户名为登录账号，默认密码为123456，登录之后请修改密码。
            </div>
			<div class="panel panel-white">
				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<label class="col-sm-2 control-label"><span style="color: red;">*</span>用户名：</label>
							<div class="col-md-3"><input class="form-control" id="user_name" name="user_name" type="text" value="<?php if(!empty($system_user)) echo $system_user->user_name;?>"/></div>
							
							<label class="col-sm-2 control-label"><span style="color: red;">*</span>姓名：</label>
							<div class="col-md-3"><input class="form-control" id="name" name="name" type="text" value="<?php if(!empty($system_user)) echo $system_user->name;?>"/></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">手机：</label>
							<div class="col-md-3"><input class="form-control" id="phone" name="phone" type="text" value="<?php if(!empty($system_user)) echo $system_user->phone;?>"/></div>

							<label class="col-sm-2 control-label">邮箱：</label>
							<div class="col-md-3"><input class="form-control" id="email" name="email" type="text" value="<?php if(!empty($system_user)) echo $system_user->email;?>"/></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">性别：</label>
							<div class="col-md-3">
								<select id="gender" name="gender" class="form-control">
									<option value="0" <?php if(!empty($system_user) && $system_user->gender == '0') echo 'selected="selected"';?>>保密</option>
									<option value="1" <?php if(!empty($system_user) && $system_user->gender == '1') echo 'selected="selected"';?>>男</option>
									<option value="2" <?php if(!empty($system_user) && $system_user->gender == '2') echo 'selected="selected"';?>>女</option>
								</select>
							</div>
							
							<label class="col-sm-2 control-label">是否启用：</label>
							<div class="col-md-3">
								<select id="enable_status" name="enable_status" class="form-control">
									<option value="0" <?php if(!empty($system_user) && $system_user->enable_status == '0') echo 'selected="selected"';?>>禁用</option>
									<option value="1" <?php if(!empty($system_user) && $system_user->enable_status == '1') echo 'selected="selected"';?>>启用</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">备注：</label>
							<div class="col-md-8"><textarea class="form-control" id="remark" name="remark" rows="3" cols="50" maxlength="250"><?php if(!empty($system_user)) echo $system_user->remark;?></textarea></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">&nbsp;</label>
							<div class="col-md-4">
								<button type="submit" class="btn btn-success" id="user_submit">保存</button>
								<a href="/privilege/user_list" class="btn btn-default">返回</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>				
<input type="hidden" id="id" value="<?php if(!empty($system_user)) echo $system_user->id;?>">
<script src="/user_guide/admin/plugins/jquery/jquery-2.1.4.min.js"></script>
<script src="/user_guide/admin/plugins/layer/layer.js" type="text/javascript"></script>
<script src="/user_guide/admin/plugins/validform/Validform_v5.3.2_min.js" type="text/javascript"></script>

<script type="text/javascript">
	$('#user_submit').click(function(){
        //发送数据
        var id = $('#id').val();          
        var user_name = $('#user_name').val();
        var name = $('#name').val();
        var phone = $('#phone').val();
        var email = $('#email').val();
        var gender  = $('#gender').val();
        var enable_status = $('#enable_status').val();
        var remark = $('#remark').val();

        var _data = 'id='+id;
        	_data += '&user_name='+user_name;
            _data += '&name='+name;
            _data += '&phone='+phone;
            _data += '&email='+email;
            _data += '&gender='+gender;
            _data += '&enable_status='+enable_status;
            _data += '&remark='+remark;
        $.ajax({
            type : "POST",
    	    url : "/privilege/update_system_user",
            data : _data,
            dataType : "json", 
            success:function(json){
                if(json.error_code == 0){
                	layer.msg(json.error_msg,{icon: 1});
                	window.location.href = '/privilege/user_list';
                    return true;
                }else{                	
                	$('#'+json.filed+'_msg').html(json.error_msg);  
                	$('#'+json.filed+'_msg').addClass("watting-msg");
                	layer.msg(json.error_msg,{shift: 6});                	
                    return false;
                }
            }                                       
        });
    });  
</script>