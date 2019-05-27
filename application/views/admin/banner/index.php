<!--面包屑-->
<div class="page-title">
	<div class="page-breadcrumb">
		<ol class="breadcrumb">
			<li><a href="/admin">管理首页</a></li>
			<li class="active">Banner设置</li>
			<li class="active">Banner设置列表</li>
		</ol>
	</div>
</div>

<!--主要内容开始位置-->
<div id="main-wrapper">
	<link href="/user_guide/admin/plugins/datatables/css/jquery.datatables.min.css" rel="stylesheet" type="text/css"/>
	<script src="/user_guide/admin/plugins/datatables/js/jquery.datatables.min.js"></script>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-white">
				<div class="panel-body">
					<div>
						<a href="<?php echo $this->config->item('domain_www')?>/admin/banner/banner_detail"><button type="button" class="btn btn-success btn-addon m-b-sm"><i class="fa fa-plus"></i> 新增</button></a>
					</div>
					<div class="table-responsive">
						<table class="table table-th-block table-hover">
							<thead>
								<tr>
									<th>ID</th>
									<th>图片</th>
									<th>排序</th>
									<th>创建时间</th>
									<th>操作</th>
								</tr>
							</thead>
							<tbody>
								<?php if (!empty($banner_list)): ?>
			                        <?php foreach ($banner_list as $row): ?>
			                        	<?php
				                            if (empty($row)) {
				                                continue;
				                            }
			                            ?>
			                            <tr>
											<td><?php echo $row->id;?></td>
											<td><img style="height: 30px;width: 30px;" src="<?php echo $row->image_url; ?>" /></td>
											<td><?php echo $row->sort;?></td>
											<td><?php echo $row->create_time;?></td>
											<td>
												<a href="<?php echo $this->config->item('domain_www')?>/admin/banner/banner_detail?id=<?php echo $row->id;?>"><span>修改</span></a>&nbsp;&nbsp;
												<a href="javascript:;" onclick="del(<?php echo $row->id;?>);" data-toggle="modal" data-target=".bs-example-modal-sm"><span>删除</span></a>&nbsp;&nbsp;
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
	<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="mySmallModalLabel">删除确认</h4>
                </div>
                <div class="modal-body">
                    确认删除？
                </div>
                <div class="modal-footer">
                	<a href="#" class="btn btn-success"> 确认</a>
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/user_guide/admin/plugins/jquery/jquery-2.1.4.min.js"></script>
<script>
	function del(id){
		if(id != ''){
			str = '<a href="<?php echo $this->config->item('domain_www')?>/admin/banner/delete_banner?id='+id+'" class="btn btn-success"> 确认</a>';
			str += '<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>';
			$('.modal-footer').html(str);
		}
	}
</script>