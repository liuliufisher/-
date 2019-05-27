<!--面包屑-->
<div class="page-title">
	<div class="page-breadcrumb">
		<ol class="breadcrumb">
			<li><a href="/admin">管理首页</a></li>
			<li class="active">用户管理</li>
			<li class="active">用户列表</li>
		</ol>
	</div>
</div>

<!--主要内容开始位置-->
<div id="main-wrapper">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-white">
				<div class="panel-body">
					<form method="get" id="frm" class="form-inline" action="/admin/user/user_list">
						<label>
							<input class="form-control" size="50" name="keyword" value="<?php echo $keyword;?>" type="text" placeholder="输入用户名"/>
						</label>&nbsp;&nbsp;&nbsp;
						<button type="submit" class="btn btn-success" style="margin-left:10px">查询</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	<link href="/user_guide/admin/plugins/datatables/css/jquery.datatables.min.css" rel="stylesheet" type="text/css"/>
	<script src="/user_guide/admin/plugins/datatables/js/jquery.datatables.min.js"></script>
	<link href="/user_guide/admin/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css"/>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-white">
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-th-block table-hover">
							<thead>
								<tr>
									<th>ID</th>
									<th>用户头像</th>
									<th>用户名</th>
									<th>二维码</th>
									<th>推荐人</th>
									<th>返比</th>
									<th>返佣<br>总金额</th>
									<th>返佣<br>可用余额</th>
									<th>店铺<br>总金额</th>
									<th>店铺<br>可用余额</th>
									<th>注册时间</th>
									<th>最后登录时间</th>
									<th>是否开店</th>
								</tr>
							</thead>
							<tbody>
								<?php if (!empty($user_list)): ?>
			                        <?php foreach ($user_list as $row): ?>
			                        	<?php
				                            if (empty($row)) {
				                                continue;
				                            }
			                            ?>
			                            <tr>
			                            	<td><?php echo $row->id;?></td>
			                            	<td><img style="height: 30px;width: 30px;" src="<?php echo $row->avatar_url; ?>" /></td>
											<td><?php echo $row->nick_name;?></td>
											<td><img style="height: 30px;width: 30px;" src="<?php echo $row->user_qrcode; ?>" /></td>
											<td>
												<?php
													if(empty($row->superior_user_id)){
														echo '';
													}else{
														$superior_user = $this->user_model->get_by_id($row->superior_user_id);
														echo $superior_user->nick_name;
													}
												?>	
											</td>
											<td><?php echo $row->remaid_ratio;?>%</td>
											<td><?php echo $row->all_reward_price;?></td>
											<td><?php echo $row->withdraw_price;?></td>
											<td><?php echo $row->shop_all_price;?></td>
											<td><?php echo $row->available_balance;?></td>
											<td><?php echo $row->create_time;?></td>
											<td><?php echo $row->last_login_time;?></td>
											<td>
												<?php
													if($row->type == 1){
														echo '普通用户';
													}elseif($row->type == 2){
														echo '试用店铺';
													}elseif($row->type == 3){
														echo '付费店铺';
													}
												?>
											</td>
										</tr>
			                        <?php endforeach; ?>
			                    <?php endif; ?>
							</tbody>
						</table>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="the-box no-border">
								<ul class="pagination danger">
									共 <?php echo $total;?> 条记录  每页 
										<select id="page">
											<option value="20" <?php if($per_page == '20') echo 'selected="selected"';?>>20</option>
											<option value="30" <?php if($per_page == '30') echo 'selected="selected"';?>>30</option>
											<option value="50" <?php if($per_page == '50') echo 'selected="selected"';?>>50</option>
											<option value="100" <?php if($per_page == '100') echo 'selected="selected"';?>>100</option>
										</select>
									条
								</ul>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="the-box no-border" style="float:right;">
								<ul class="pagination danger">
									<?php echo $pagination;?>
								</ul>
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
<script src="/user_guide/admin/plugins/validform/Validform_v5.3.2_min.js" type="text/javascript"></script>
<script>
	$(document).ready(function() {
	    $('.date-picker').datepicker({
	    	language: 'zh',
		    autoclose: true,
		    todayHighlight: true,
		    format: 'yyyy-mm-dd',
		    formatDate: 'yyyy-mm-dd',
		});
    })

	$("#page").change(function(){
		var limit = $(this).children('option:selected').val();
		window.location.href ='/admin/user/user_list?limit='+limit;
	});

</script>