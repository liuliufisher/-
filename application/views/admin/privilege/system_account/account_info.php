<!--面包屑-->
<div class="page-title">
	<div class="page-breadcrumb">
		<ol class="breadcrumb">
			<li><a href="/admin">管理首页</a></li>
			<li class="active">个人资料</li>
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
						    <div class="col-md-2 text-right">用户名：</div>
							<div class="col-md-3"><?php echo $login_system_user->user_name;?></div>
						</div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label text-right">姓名：</label>
							<div class="col-sm-3"><?php echo $login_system_user->name;?></div>
						</div>
                        <div class="form-group">
                        	<label class="col-sm-2 control-label text-right">性别：</label>
                        	<div class="col-sm-3">
                        		<?php
                        			if($login_system_user->gender == 0){
                        				echo '保密';
                        			}elseif($login_system_user->gender == 1){
                        				echo '男';
                        			}elseif($login_system_user->gender == 2){
                        				echo '女';
                        			}
                        		?>
                        	</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label text-right">手机：</label>
							<div class="col-sm-3"><?php echo $login_system_user->phone;?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label text-right">邮箱：</label>
							<div class="col-sm-3"><?php echo $login_system_user->email;?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 text-right">是否启用：</label>
							<div class="col-sm-3">
								<?php
                        			if($login_system_user->enable_status == 0){
                        				echo '禁用';
                        			}elseif($login_system_user->enable_status == 1){
                        				echo '启用';
                        			}
                        		?>
							</div>
						</div>
                        <div class="form-group">
							<label class="col-sm-2 control-label">备注：</label>
							<div class="col-md-6"><?php echo $login_system_user->remark;?></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">创建时间：</label>
							<div class="col-md-6"><?php echo $login_system_user->create_time;?></div>
						</div>
					</div>
				</div>			
			</div>
		</div>
	</div>
</div>
