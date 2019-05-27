<!--面包屑-->
<div class="page-title">
	<div class="page-breadcrumb">
		<ol class="breadcrumb">
			<li><a href="/">管理首页</a></li>
			<li class="active">系统设置</li>
			<li><a href="/admin/banner/banner_list">Banner设置</a></li>
			<li class="active">添加Banner</li>
		</ol>
	</div>
</div>

<!--主要内容开始位置-->
<div id="main-wrapper">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-white">
                <form action="/admin/banner/update_banner" method="post" enctype="multipart/form-data" >
                    <input type="hidden" id="id" name="id" value="<?php if(!empty($banner)) echo $banner->id;?>">
    				<div class="panel-body">
    					<div class="form-horizontal">
    						<div class="form-group">
    							<label class="col-sm-2 control-label"><span style="color: red;">*</span>图片：</label>
                                <div class="img-warp file-img"></div>
                                <div class="col-md-3">
                                    <input type="hidden" name="pic" value="<?php if(!empty($banner)) echo $banner->image_url;?>" />
                                    <?php if(!empty($banner)){?>
                                        <img style="height: 100px;width: 80px;" src="<?php echo $banner->image_url;?>" />
                                    <?php }?>
                                    <input type="file" name="userfile" value="选择图片" />
    							</div>
    						</div>
    						<div class="form-group">
    							<label class="col-sm-2 control-label">排序：</label>
    							<div class="col-md-3"><input class="form-control" id="sort" name="sort" type="text" value="<?php if(!empty($banner)) echo $banner->sort;?>"/></div>
    						</div>
    						<div class="form-group">
    							<label class="col-sm-2 control-label">&nbsp;</label>
    							<div class="col-md-4">
    								<button type="submit" class="btn btn-success">保存</button>
    								<a href="/admin/banner/banner_list" class="btn btn-default">返回</a>
    							</div>
    						</div>
    					</div>
    				</div>
                </from>
			</div>
		</div>
	</div>
</div>