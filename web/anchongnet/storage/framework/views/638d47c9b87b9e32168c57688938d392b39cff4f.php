<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>用户认证</title>
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
		.radio-inline{position: relative; top: -4px;}
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
			<h1>用户认证</h1>
		</section>

		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xs-12">
					<div class="box">
						<div class="box-body">
						    <form action="/cert" method="get" class="form-horizontal form-inline f-ib">
						        <input type="number" name="id"  placeholder="用户ID" class="form-control input-sm" value="<?php echo e($datacol['args']['id']); ?>">&nbsp;
								审核状态：
								<label class="radio-inline">
									<input type="radio" name="auth_status" id="status1" class="status" value="1">待审核
								</label>
								<label class="radio-inline">
									<input type="radio" name="auth_status" id="status2" class="status" value="2">未通过
								</label>
								<label class="radio-inline">
									<input type="radio" name="auth_status" id="status3" class="status" value="3">已通过
								</label>
						        <button type="submit" class="btn btn-primary btn-sm" id="filter">筛选</button>

						    </form>
							<a href="/cert" class="btn btn-default btn-sm unplay f-ib" role="button">取消筛选</a>
							<table id="example1" class="table table-bordered table-striped">
								<tr>
									<th>用户ID</th>
									<th>认证名称</th>
									<th>认证状态</th>
									<th>查看资质</th>
									<th>操作</th>
								</tr>
								<?php foreach($datacol['datas'] as $data): ?>
								<tr>
								  <td align="center"><?php echo e($data['users_id']); ?></td>
								  <td align="center"><?php echo e($data['auth_name']); ?></td>
								  <td align="center">
								  <?php
								  switch($data['auth_status']){
									  case 1:
									  echo "待审核";
									  break;
									  case 2:
									  echo "审核未通过";
									  break;
									  case 3:
									  echo "审核已通过";
									  break;
								  }
								  ?>
								  </td>
								  <td align="center">
								      <button type="button" class="view btn btn-default btn-xs" data-auth="<?php echo e($data['auth_name']); ?>" data-id="<?php echo e($data['users_id']); ?>" data-toggle="modal" data-target="#myModal">查看</button>
								  </td>
								  <td align="center">
								  <?php
								  switch($data['auth_status']){
									  case 1:
									  echo "<button type='button' data-id='{$data['id']}' class='check-success btn btn-success btn-xs'>通过</button>&nbsp;&nbsp;<button type='button' data-id='{$data['id']}'  class='check-failed btn btn-danger btn-xs'>不通过</button>";
									  break;
								  }
								  ?>
								  </td>
								</tr>  
								<?php endforeach; ?>
								<tr>
								  <td colspan="5" align="center">
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
	<!-- /.content-wrapper -->
	
	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel">Modal title</h4>
		  </div>
		  <div class="modal-body">
			<table class="table">
			  <tr>
			    <th>资质名称</th>
				<th>简介</th>
				<th>上传证件</th>
			  </tr>
			  <tbody id="qua">
			  </tbody>
				<tr>
					<td colspan="3">
						<ul class="pagination" id="rendor">

						</ul>
					</td>
				</tr>
			</table>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>
	  </div>
	</div>
	<input type="hidden" id="activeFlag" value="treeuser">
	<?php echo $__env->make('inc.admin.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
<!-- ./wrapper -->
<!-- Bootstrap 3.3.5 -->
<script src="/admin/bootstrap/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="/admin/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="/admin/dist/js/app.min.js"></script>
<?php
if(isset($datacol['args']['auth_status'])){
	switch ($datacol['args']['auth_status']){
		case 1:
		echo '<script>$(function(){$("#status1").attr("checked",true)});</script>';
		break;
		case 2:
		echo '<script>$(function(){$("#status2").attr("checked",true)});</script>';
		break;
		case 3:
		echo '<script>$(function(){$("#status3").attr("checked",true)});</script>';
		break;
		default:
		echo '<script>$(function(){$("#status1").attr("checked",false);$("#status2").attr("checked",false);$("#status3").attr("checked",false)});</script>';
	}
}
?>
<script>
$(function(){
	/**
	 * 为'通过'按钮，绑定点击事件，触发ajax
	 * 
	 */
    $("body").on("click",'.check-success',function(){
		if(confirm('确定要通过吗？')){
			var id=parseInt($(this).attr("data-id"));
			$.get("/check",{"id":id,"certified":"yes"},function(data,status){
				alert(data);
				setTimeout(function(){location.reload()},1000);
			});
		}
	})
    /**
    *   为 '不通过'按钮，绑定点击事件  ，触发ajax
    */	
	$("body").on("click",'.check-failed',function(){
		if(confirm('确定审核不通过吗？')){
			var id=parseInt($(this).attr("data-id"));
			$.get("/check",{"id":id,"certified":"no"},function(data,status){
				alert(data);
				setTimeout(function(){location.reload()},1000); 
			});
		}
	})
	/**
	*为 ‘查看’ 按钮绑定点击事件
	*问题：为什么点击后会是弹框呢？
	*/
	$(".view").click(function(){
		//获取 查看 按钮这个button对象，携带的一些属性，该属性隐藏了一些信息，比如
		//data-id就是用户id
		//data-auth就是申请的资质名称
	    var id=parseInt($(this).attr("data-id"));
		var auth=$(this).attr("data-auth");
		$("#myModalLabel").text(auth);
		//这种restful资源访问方式，调用后方控制器的show方法
		$.get("/cert/"+id,function(data,status){

			console.log(data)
			console.log(status)
			$("#qua").empty();
			var con="";
			for(var i=0;i<data.length;i++){
				con+="<tr><td align='center'>"+data[i].qua_name+"</td><td align='center'>"+data[i].explanation+"</td><td align='center'><a href="+data[i].credentials+" target='_blank'><img src="+data[i].credentials+" width='50'></a></td></tr>";
			}
			//添加三列
			$("#qua").append(con);
		});
	})
})
</script>
</body>
</html>
