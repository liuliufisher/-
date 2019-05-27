<!--面包屑-->
<div class="page-title">
	<div class="page-breadcrumb">
		<ol class="breadcrumb">
			<li><a href="/admin">管理首页</a></li>
			<li class="active">提现管理</li>
			<li class="active">提现申请列表</li>
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
					<div class="table-responsive">
						<table class="table table-th-block table-hover">
							<thead>
								<tr>
									<th>用户</th>
									<th>姓名</th>
									<th>开户行</th>
									<th>卡号</th>
									<th>提现金额</th>
									<th>提现状态</th>
									<th>提现申请时间</th>
									<th>操作</th>
								</tr>
							</thead>
							<tbody>
								<?php if (!empty($withdraw_list)): ?>
			                        <?php foreach ($withdraw_list as $row): ?>
			                        	<?php
				                            if (empty($row)) {
				                                continue;
				                            }
			                            ?>
			                            <tr>
											<td><?php echo $row->nick_name;?></td>
											<td><?php echo $row->name;?></td>
											<td><?php echo $row->open_bank;?></td>
											<td><?php echo $row->card_num;?></td>
											<td><?php echo $row->price;?></td>
											<td>
												<?php
													if($row->status == 1){
														echo '待提现';
													}elseif($row->status == 2){
														echo '提现成功';
													}elseif($row->status == 3){
														echo '提现失败';
													}
												?>
											</td>
											<td><?php echo $row->create_time;?></td>
											<td>
												<?php if($row->status == 1){?>
													<a href="javascript:;" onclick="agree(<?php echo $row->id;?>);" data-toggle="modal" data-target="#agree"><span>通过</span></a>&nbsp;&nbsp;
													<a href="javascript:;" onclick="refuse(<?php echo $row->id;?>);" data-toggle="modal" data-target="#refuse"><span>拒绝</span></a>&nbsp;&nbsp;
												<?php }?>
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
	<div class="modal fade bs-example-modal-sm" id="agree" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="mySmallModalLabel">通过确认</h4>
                </div>
                <div class="modal-body">
                    确认通过？
                </div>
                <div class="modal-footer" id="modal-footer1">
                	<a href="#" class="btn btn-success"> 确认</a>
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-sm" id="refuse" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="mySmallModalLabel">拒绝确认</h4>
                </div>
                <div class="modal-body">
                    确认拒绝？
                </div>
                <div class="modal-footer" id="modal-footer2">
                	<a href="#" class="btn btn-success"> 确认</a>
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/user_guide/admin/plugins/jquery/jquery-2.1.4.min.js"></script>
<script src="/user_guide/admin/plugins/layer/layer.js" type="text/javascript"></script>
<script src="/user_guide/admin/plugins/validform/Validform_v5.3.2_min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="/user_guide/admin/plugins/datetimepicker/datetimepicker.css">
<script>
	$("#page").change(function(){
		var limit = $(this).children('option:selected').val();
		window.location.href ='/admin/withdraw/withdraw_list?limit='+limit;
	});

	function agree(id){
		if(id != ''){
			str = '<button type="button" onclick="agree_ajax('+id+');" class="btn btn-success">确认</button>';
			str += '<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>';
			$('#modal-footer1').html(str);
		}
	}

	function agree_ajax(id){
		var _data = 'id='+id;
		$.ajax({
			type : "POST",
			url : "/admin/withdraw/agree",
			data : _data,
			dataType : "json", 
			success:function(json){
				if(json.code == 0){
					layer.msg(json.msg,{icon: 1});
					window.location.href = '/admin/withdraw/withdraw_list';
					return true;
				}else{                  
					$('#'+json.filed+'_msg').html(json.msg);  
					$('#'+json.filed+'_msg').addClass("watting-msg");
					layer.msg(json.msg,{shift: 6});                   
					return false;
				}
			}                                       
		});
	}

	function refuse(id){
		if(id != ''){
			str = '<button type="button" onclick="refuse_ajax('+id+');" class="btn btn-success">确认</button>';
			str += '<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>';
			$('#modal-footer2').html(str);
		}
	}

	function refuse_ajax(id){
		var _data = 'id='+id;
		$.ajax({
			type : "POST",
			url : "/admin/withdraw/refuse",
			data : _data,
			dataType : "json", 
			success:function(json){
				if(json.code == 0){
					layer.msg(json.msg,{icon: 1});
					window.location.href = '/admin/withdraw/withdraw_list';
					return true;
				}else{                  
					$('#'+json.filed+'_msg').html(json.msg);  
					$('#'+json.filed+'_msg').addClass("watting-msg");
					layer.msg(json.msg,{shift: 6});                   
					return false;
				}
			}                                       
		});
	}
</script>