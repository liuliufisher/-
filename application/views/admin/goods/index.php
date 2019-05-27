<!--面包屑-->
<div class="page-title">
	<div class="page-breadcrumb">
		<ol class="breadcrumb">
			<li><a href="/admin">管理首页</a></li>
			<li class="active">商品管理</li>
			<li class="active">商品列表</li>
		</ol>
	</div>
</div>

<!--主要内容开始位置-->
<div id="main-wrapper">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-white">
				<div class="panel-body">
					<form method="get" id="frm" class="form-inline" action="/admin/goods/goods_list">
						<label>
							<input class="form-control" size="50" name="keyword" value="<?php echo $keyword;?>" type="text" placeholder="输入商品名称"/>
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
									<th>商品主图</th>
									<th>商品名称</th>
									<th>总库存</th>
									<th>销量</th>
									<th>用户头像</th>
									<th>用户名</th>
									<th>商品状态</th>
									<th>创建时间</th>
								</tr>
							</thead>
							<tbody>
								<?php if (!empty($goods_list)): ?>
			                        <?php foreach ($goods_list as $row): ?>
			                        	<?php
				                            if (empty($row)) {
				                                continue;
				                            }
			                            ?>
			                            <tr>
			                            	<td><?php echo $row->id;?></td>
			                            	<td><img style="height: 30px;width: 30px;" src="<?php echo $row->goods_image; ?>" /></td>
											<td><?php echo $row->goods_name;?></td>
											<td><?php echo $row->stock_num;?></td>
											<td><?php echo $row->sale_num;?></td>
			                            	<td><img style="height: 30px;width: 30px;" src="<?php echo $row->avatar_url; ?>" /></td>
											<td><?php echo $row->nick_name;?></td>
											<td>
												<?php
													if($row->status == 1){
														echo '出售中';
													}elseif($row->status == 2){
														echo '出售中但不能下单';
													}elseif($row->status == 3){
														echo '下架';
													}
												?>
											</td>
											<td><?php echo $row->create_time;?></td>
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
		window.location.href ='/admin/goods/goods_list?limit='+limit;
	});

</script>