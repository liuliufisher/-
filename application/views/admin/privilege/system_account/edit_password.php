<!--面包屑-->
<div class="page-title">
	<div class="page-breadcrumb">
		<ol class="breadcrumb">
			<li><a href="/admin">管理首页</a></li>
			<li class="active">修改密码</li>
		</ol>
	</div>
</div>

<div id="main-wrapper">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-white">
				<div class="panel-body">
					<div class="form-horizontal">
                        <div class="form-group">
							<label class="col-sm-2 control-label">用户名：</label>
							<div class="col-md-2 control-label" style="text-align:left;"><?php echo $login_system_user->user_name;?></div>									
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">姓名：</label>
							<div class="col-md-2 control-label" style="text-align:left;"><?php echo $login_system_user->name;?></div>									
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">原密码：</label>
							<div class="col-md-2"><input placeholder="请输入原密码" class="form-control" id="old_password" name="old_password" type="password" datatype="s6-30" errormsg="原密码只允许6-30位的字符组成" nullmsg="请输入原密码" maxlength="30"/></div>
						</div>	
						<div class="form-group">
							<label class="col-sm-2 control-label">新密码：</label>
							<div class="col-md-2"><input placeholder="请输入新密码" class="form-control" id="new_password" name="new_password" type="password" datatype="newpassword" errormsg="密码只允许6-30位的字符组成" nullmsg="请输入新密码" maxlength="30"/></div>
						</div>	
						<div class="form-group">
							<label class="col-sm-2 control-label">新确认密码：</label>
							<div class="col-md-2"><input placeholder="请输入新确认密码" class="form-control" id="confim_password" name="comfim_password" type="password" datatype="newpassword" recheck="password" errormsg="密码只允许6-30位的字符组成" nullmsg="请输入新确认密码" maxlength="30"/></div>
						</div>											
						<div class="form-group">
							<label class="col-sm-2 control-label">&nbsp;</label>
							<div class="col-md-4">
								<button type="submit" class="btn btn-success">保存</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="/user_guide/admin/plugins/jquery/jquery-2.1.4.min.js"></script>
<script src="/user_guide/admin/plugins/layer/layer.js" type="text/javascript"></script>
<script type="text/javascript">
	$('.btn').click(function(){
        //发送数据
        var old_password = $('#old_password').val();
        var new_password = $('#new_password').val();
        var confim_password = $('#confim_password').val();
        if(old_password == ''){
        	layer.msg('请输入原密码',{shift: 6});return false;
        }
        if(new_password == ''){
        	layer.msg('请输入新密码',{shift: 6});return false;
        }
        if(confim_password == ''){
        	layer.msg('请输入新确认密码',{shift: 6});return false;
        }
        if(new_password != confim_password){
        	layer.msg('新密码不一致',{shift: 6});return false;
        }

        var _data = 'old_password='+old_password;
        	_data += '&new_password='+new_password;
            _data += '&confim_password='+confim_password;
        $.ajax({
            type : "POST",
    	    url : "/privilege/ajax_edit_password",
            data : _data,
            dataType : "json", 
            success:function(json){
                if(json.error_code == 0){
                	layer.msg(json.error_msg,{icon: 1});
                	window.location.href = '/privilege/login_out';
                    return true;
                }else{
                	layer.msg(json.error_msg,{shift: 6});
                    return false;
                }
            }                                       
        });
    });    
</script>