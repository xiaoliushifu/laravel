<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>权限分配</title>
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
			<h1>权限分配</h1>
		</section>

		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xs-12">
					<div class="box">
						<div class="box-body">
						    <form action="/permission" method="get" class="form-horizontal form-inline f-ib">
						        <input type="text" name="nm"  placeholder="角色查找" class="form-control input-sm" value="<?php echo e($datacol['args']['nm']); ?>">&nbsp;
						        <button type="submit" class="btn btn-primary btn-sm" id="filter">筛选</button>
						    </form>
							<a href="/permission" class="btn btn-default btn-sm unplay f-ib" role="button">取消筛选</a>
							<table id="example1" class="table table-bordered table-striped">
								<tr>
									<th>ID</th>
									<th>角色标识</th>
									<th>角色描述</th>
									<th>操作</th>
								</tr>
								<?php foreach($datacol['datas'] as $data): ?>
								<tr>
								  <td align="center"><?php echo e($data['id']); ?></td>
								  <td align="center"><?php echo e($data['label']); ?></td>
								  <td align="center"><?php echo e($data['description']); ?></td>
								  <td align="center">
								      <button type="button" class="view btn btn-default btn-xs" rid="<?php echo e($data['id']); ?>" data-toggle="modal" data-target="#myModal">权限分配</button>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">编辑权限</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="myform" action="" method="post">
                        <input type="hidden" id='hidrid' name="rid" value="">
                        <div class="form-group" id="myper">
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <button type="submit" id="ajaxsub" class="btn btn-primary">保存</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
	<input type="hidden" id="activeFlag" value="treeperm">
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
	/**
	*为 ‘权限设置’ 按钮绑定点击事件
	*问题：为什么点击后会是弹框呢？
	*/
	$(".view").click(function(){
		
		$("#myper").empty();                  //清空
	    var rid=parseInt($(this).attr("rid"));
	    $('#hidrid').val(rid);                 //角色id
		$.ajax({
			  type: "GET",
			  url: "/permission/perm/"+rid,
			  cache:true,
			  success:function(data,status){
					var con='';
					//该用户已有的权限
					for(var i=0;i<data[0].length;i++){
						con+='<label ><input type="checkbox" name="perms[]" value="'+data[0][i].id+'" checked="checked" /> '+data[0][i].label+'</label><br />';
					}
					//其他权限
					for(var i=0;i<data[1].length;i++){
						if(data[0])
						con+='<label ><input type="checkbox" name="perms[]" value="'+data[1][i].id+'" /> '+data[1][i].label+'</label> <br />';
					}
					$("#myper").append(con);
				}
		})
	})


	/**
	* 使用ajax提交
	*/
	 $("#ajaxsub").click(function(){
		 //角色id
		 var rid=$('#hidrid').val();
		 //接收checkbox的值为字符串
		 var a=[];
		 alert(a.join());
		 $('input[type="checkbox"]:checked').each(function(k,v){a.push(v.value)});
		 //组织ajax
        $.ajax({
            type: 'post',
            url: '/permission/addperm',
            data:{
                'rid':rid,
                'perms':a.join()
                },
            success: function (data) {
                alert(data);
            },
            error: function(xhr,error){
                alert(error);
            }
        });
        //阻止浏览器默认行为
        return false;
    });
})
</script>
</body>
</html>
