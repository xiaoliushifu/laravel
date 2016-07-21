<html>
	<head>
		<style>
		.status{
				color:#aaa;
			}
		.status1{
				color:blue;
			}
		.status2{
				color:red;
			}
		.status3{
				color:green;
			}
		</style>
	</head>
	<body>

		<div align="center">
		<h1>注册信息(必填)</h1>


			<form action="/test/fileadd" method="post" enctype="multipart/form-data">
			<table>
            {{ csrf_field() }}
			<tr><th>用户名:</th><td><input type="text" name="username" value="">
			<span class="status">请输入用户名</span><br></tr>
			<tr><th>密码:</th><td><input type="password" name="password" value="">
			<span class="status">请输入密码</span><br></tr>
			<tr><th>确认密码:</th><td><input type="password" name="retype"value="">
			<span class="status">请确认密码</span><br></tr>
			<tr><th>邮箱:</th><td><input type="text" name="email" value="">
			<span class="status">请输入邮箱</span><br></tr>
			<tr><th>文件:</th><td><input type="file" name="uile"></td></tr>
    		<td><input type="submit" name="submit"  value="提交"></td>
    		<td><input type="reset" name="重置" value="重置"></td>
		</table>
		</form>

	</div>
	</body>
</html>
