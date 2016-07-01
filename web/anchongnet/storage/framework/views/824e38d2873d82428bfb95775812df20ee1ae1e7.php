<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>分类标签列表</title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.5 -->
	<link rel="stylesheet" href="/admin/bootstrap/css/bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="/admin/dist/dfonts/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="/admin/dist/dfonts/ionicons.min.css">
	<!-- DataTables -->
	<link rel="stylesheet" href="/admin/plugins/datatables/dataTables.bootstrap.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="/admin/dist/css/AdminLTE.min.css">
	<!-- AdminLTE Skins. Choose a skin from the css/skins
			 folder instead of downloading all of them to reduce the load. -->
	<link rel="stylesheet" href="/admin/dist/css/skins/_all-skins.min.css">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	<style>
	th{text-align:center;}
	.f-ib{display:inline-block;}
	#example1{margin-top:10px;}
	</style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
	<?php echo $__env->make('inc.admin.mainHead', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
		<!-- Left side column. contains the logo and sidebar -->
	<aside class="main-sidebar">
		<!-- sidebar: style can be found in sidebar.less -->
		<?php echo $__env->make('inc.admin.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
		<!-- /.sidebar -->
	</aside>

	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>分类标签浏览</h1>
		</section>
		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xs-12">
					<div class="box">
						<div class="box-body">
						    <form action="/catag" method="get" class="form-horizontal form-inline f-ib">
								分类筛选：
								<select class="form-control" name="cat" id="cat">
									<option value="" id="check">请选择</option>
								</select>
								<input type="hidden" id="waitforcat">
						        <button type="submit" class="btn btn-primary btn-sm" id="filter">筛选</button>
						    </form>
		                    <a href="/catag" class="btn btn-default btn-sm f-ib" role="button">取消筛选</a>
							<table id="example1" class="table table-bordered table-striped">
								<tr>
									<th>所属分类</th>
									<th>标签名称</th>
									<th>操作</th>
								</tr>
								<?php foreach($datacol['datas'] as $data): ?>
								<tr>
								    <td align="center">
										<?php echo e($data['cat_name']); ?>

									</td>
									<td align="center">
										<?php echo e($data['tag']); ?>

									</td>
									<td align="center">
										<button type='button' class='edit f-ib btn btn-primary btn-xs' data-id="<?php echo e($data['id']); ?>" data-cid="<?php echo e($data['cat_id']); ?>" data-toggle="modal" data-target="#myModal">编辑</button>
										<button type="button" class="del f-ib btn btn-danger btn-xs" data-id="<?php echo e($data['id']); ?>">删除</button>
									</td>
								</tr>  
								<?php endforeach; ?>
								<tr>
								  <td colspan="3" align="center">
									<?php echo $datacol['datas']->appends($datacol['args'])->render(); ?>
								  </td>
								</tr>
							</table>
						</div>
						<!-- /.box-body -->
					</div>
					<!-- /.box -->
				</div>
				<!-- /.col -->
			</div>
			<!-- /.row -->
		</section>
		<!-- /.content -->
	</div>
	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">分类标签编辑</h4>
				</div>
				<div class="modal-body">
					<form role="form" class="form-horizontal" action="" method="post" id="updateform">
						<input type="hidden" name="_method" value="PUT">
						<div class="form-group">
							<label class="col-sm-2 control-label">所属分类</label>
							<div class="col-sm-10">
								<div class="row">
									<div class="col-xs-4">
										<select class="form-control" id="mainselect" name="mainselect" required>
										</select>
									</div>
									<div class="col-xs-4">
										<select class="form-control" id="midselect" name="midselect" required>
										</select>
										<input type="hidden" name="catname" id="catname">
									</div>
								</div><!--end row-->
							</div><!--end col-sm-10-->
						</div><!--end form-group-->
						<div class="form-group">
							<label class="col-sm-2 control-label" for="name">标签名称</label>
							<div class="col-sm-6">
								<input type="text" name="tag" id="tag" required class="form-control">
							</div>
						</div><!--end form-group-->
						<div class="form-group text-center">
							<label class="col-sm-2 control-label"></label>
							<div class="col-sm-3">
								<button type="submit" class="btn btn-info">保存</button>
							</div>
						</div><!--end form-group text-center-->
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- /.content-wrapper -->
	<input type="hidden" id="activeFlag" value="treetag">
	<?php echo $__env->make('inc.admin.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
<!-- ./wrapper -->
<!-- Bootstrap 3.3.5 -->
<script src="/admin/bootstrap/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="/admin/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="/admin/dist/js/app.min.js"></script>
<script>
	$(function(){
		var cat='<?php echo isset($datacol["args"]["cat"]) ? $datacol["args"]["cat"] : "" ?>';
		$("#waitforcat").val(cat);
	})
</script>
<script src="/admin/js/catag.js"></script>
</body>
</html>
