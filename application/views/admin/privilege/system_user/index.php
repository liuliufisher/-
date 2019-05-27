<!--面包屑-->
<div class="page-title">
	<div class="page-breadcrumb">
		<ol class="breadcrumb">
			<li><a href="/admin">管理首页</a></li>
			<li class="active">后台账户权限</li>
			<li class="active">账户列表</li>
		</ol>
	</div>
</div>

<!--主要内容开始位置-->
<div id="main-wrapper">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-white">
				<div class="panel-body">
					<form method="get" id="frm" class="form-inline" action="/privilege/user_list">
						<label>用户名:&nbsp;
							<input class="form-control" name="user_name" value="<?php echo $user_name;?>" type="text" />
						</label>&nbsp;&nbsp;&nbsp;
						<label>姓名:&nbsp;
							<input class="form-control" name="name" value="<?php echo $name;?>" type="text" />
						</label>
						<button type="submit" class="btn btn-success" style="margin-left:10px">查询</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	<link href="/user_guide/admin/plugins/datatables/css/jquery.datatables.min.css" rel="stylesheet" type="text/css"/>
	<script src="/user_guide/admin/plugins/datatables/js/jquery.datatables.min.js"></script>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-white">
				<div class="panel-body">
					<div>
						<a href="/privilege/user_detail"><button type="button" class="btn btn-success btn-addon m-b-sm"><i class="fa fa-plus"></i> 新增</button></a>
					</div>
					<div class="table-responsive">
						<table class="table table-th-block table-hover">
							<thead>
								<tr>
									<th>用户名</th>
									<th>姓名</th>
									<th>是否启用</th>
									<th>创建时间</th>
									<th>操作</th>
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
											<td><a href="#" class="js-userInfo"><?php echo $row->user_name;?></a></td>
											<td><?php echo $row->name;?></td>
											<td>
												<?php
													if($row->enable_status == 0){
														echo '禁用';
													}elseif($row->enable_status == 1){
														echo '启用';
													}
												?>
											</td>
											<td><?php echo $row->create_time;?></td>
											<td>
												<?php if($row->enable_status == 0){?>
													<a href="/privilege/update_enable_status?id=<?php echo $row->id;?>&enable_status=1"><span><i class="fa fa-play-circle"></i> 启用</span></a>&nbsp;&nbsp;
												<?php }elseif($row->enable_status == 1){?>
													<a href="/privilege/update_enable_status?id=<?php echo $row->id;?>&enable_status=0"><span><i class="fa fa-pause"></i> 停用</span></a>&nbsp;&nbsp;
												<?php }?>
												<a href="/privilege/user_detail?id=<?php echo $row->id;?>"><span><i class="glyphicon glyphicon-pencil"></i> 修改</span></a>&nbsp;&nbsp;
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
									共 <?php echo $total;?> 条记录  每页 <?php echo $per_page;?> 条
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